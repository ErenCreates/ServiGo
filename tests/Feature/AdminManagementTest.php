<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected User $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'    => 'Admin User',
            'role_id' => Role::ADMIN,
        ]);

        $this->customer = User::factory()->create([
            'name'    => 'Customer User',
            'role_id' => Role::CUSTOMER,
        ]);

        $this->provider = User::factory()->create([
            'name'    => 'Provider User',
            'role_id' => Role::PROVIDER,
        ]);
    }

    public function test_admin_can_access_dashboard_and_non_admins_are_forbidden(): void
    {
        // Admin access
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertOk();
        $response->assertSee('Genel Bakış');

        // Customer forbidden
        $customerResponse = $this->actingAs($this->customer)->get('/admin');
        $customerResponse->assertForbidden();

        // Provider forbidden
        $providerResponse = $this->actingAs($this->provider)->get('/admin');
        $providerResponse->assertForbidden();
    }

    public function test_admin_can_manage_categories_crud(): void
    {
        // 1. List categories
        $category = ServiceCategory::create([
            'name'        => 'Klima & Kombi',
            'description' => 'Klima bakım onarım',
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/kategoriler');
        $response->assertOk();
        $response->assertSee('Klima & Kombi');

        // 2. Create category
        $createResponse = $this->actingAs($this->admin)->post('/admin/kategoriler', [
            'name'        => 'Marangoz & Mobilya',
            'description' => 'Ahşap tamir ve montaj',
            'is_active'   => 1,
        ]);
        $createResponse->assertRedirect('/admin/kategoriler');
        $this->assertDatabaseHas('service_categories', ['name' => 'Marangoz & Mobilya']);

        // 3. Update category
        $createdCat = ServiceCategory::where('name', 'Marangoz & Mobilya')->first();
        $updateResponse = $this->actingAs($this->admin)->put('/admin/kategoriler/' . $createdCat->id, [
            'name'        => 'Mobilya & Dekorasyon',
            'description' => 'Güncellenmiş açıklama',
            'is_active'   => 1,
        ]);
        $updateResponse->assertRedirect('/admin/kategoriler');
        $this->assertDatabaseHas('service_categories', ['name' => 'Mobilya & Dekorasyon']);

        // 4. Toggle status (Active to Passive)
        $toggleResponse = $this->actingAs($this->admin)->patch('/admin/kategoriler/' . $createdCat->id . '/durum');
        $toggleResponse->assertRedirect();
        $this->assertDatabaseHas('service_categories', [
            'id'        => $createdCat->id,
            'is_active' => false,
        ]);

        // 5. Delete category
        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/kategoriler/' . $createdCat->id);
        $deleteResponse->assertRedirect('/admin/kategoriler');
        $this->assertDatabaseMissing('service_categories', ['id' => $createdCat->id]);
    }

    public function test_admin_can_manage_users_and_suspend_or_delete(): void
    {
        $targetUser = User::factory()->create([
            'name'      => 'Kural İhlali Yapan Usta',
            'role_id'   => Role::PROVIDER,
            'is_active' => true,
        ]);

        // 1. List users
        $response = $this->actingAs($this->admin)->get('/admin/kullanicilar');
        $response->assertOk();
        $response->assertSee('Kural İhlali Yapan Usta');

        // 2. Suspend (toggle status to inactive)
        $toggleResponse = $this->actingAs($this->admin)->patch('/admin/kullanicilar/' . $targetUser->id . '/durum');
        $toggleResponse->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id'        => $targetUser->id,
            'is_active' => false,
        ]);

        // 3. Admin cannot suspend self
        $selfToggleResponse = $this->actingAs($this->admin)->patch('/admin/kullanicilar/' . $this->admin->id . '/durum');
        $selfToggleResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id'        => $this->admin->id,
            'is_active' => true,
        ]);

        // 4. Delete user
        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/kullanicilar/' . $targetUser->id);
        $deleteResponse->assertRedirect('/admin/kullanicilar');
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_admin_can_view_and_manage_requests_and_appointments(): void
    {
        $category = ServiceCategory::create(['name' => 'Elektrik']);
        $serviceProvider = ServiceProvider::create([
            'user_id'      => $this->provider->id,
            'category_id'  => $category->id,
            'company_name' => 'Usta Elektrik',
        ]);

        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $serviceProvider->id,
            'category_id' => $category->id,
            'description' => 'Sigorta kutusu değişimi randevusu.',
            'status'      => 'pending',
        ]);

        // 1. List requests
        $response = $this->actingAs($this->admin)->get('/admin/talepler');
        $response->assertOk();
        $response->assertSee('Sigorta kutusu değişimi randevusu.');

        // 2. Update status to completed
        $updateResponse = $this->actingAs($this->admin)->patch('/admin/talepler/' . $serviceRequest->id . '/durum', [
            'status' => 'completed',
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('service_requests', [
            'id'     => $serviceRequest->id,
            'status' => 'completed',
        ]);

        // 3. Delete request
        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/talepler/' . $serviceRequest->id);
        $deleteResponse->assertRedirect('/admin/talepler');
        $this->assertDatabaseMissing('service_requests', ['id' => $serviceRequest->id]);
    }

    public function test_admin_can_view_and_moderate_reviews(): void
    {
        $category = ServiceCategory::create(['name' => 'Temizlik']);
        $serviceProvider = ServiceProvider::create([
            'user_id'      => $this->provider->id,
            'category_id'  => $category->id,
            'company_name' => 'Pırıl Temizlik',
        ]);

        $review = Review::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $serviceProvider->id,
            'rating'      => 5,
            'comment'     => 'Harika ve titiz hizmet, teşekkürler!',
            'is_approved' => true,
        ]);

        // 1. List reviews
        $response = $this->actingAs($this->admin)->get('/admin/yorumlar');
        $response->assertOk();
        $response->assertSee('Harika ve titiz hizmet, teşekkürler!');

        // 2. Toggle approval (hide review)
        $toggleResponse = $this->actingAs($this->admin)->patch('/admin/yorumlar/' . $review->id . '/onay');
        $toggleResponse->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id'          => $review->id,
            'is_approved' => false,
        ]);

        // 3. Delete review
        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/yorumlar/' . $review->id);
        $deleteResponse->assertRedirect('/admin/yorumlar');
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}

