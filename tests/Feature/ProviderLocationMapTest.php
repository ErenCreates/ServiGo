<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\User;
use Database\Seeders\TestProviderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProviderLocationMapTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $providerUser;
    protected ServiceProvider $serviceProvider;
    protected ServiceCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'name'    => 'Ali Müşteri',
            'role_id' => Role::CUSTOMER,
        ]);

        $this->providerUser = User::factory()->create([
            'name'    => 'Hasan Usta',
            'email'   => 'hasanusta@servigo.com',
            'role_id' => Role::PROVIDER,
        ]);

        $this->category = ServiceCategory::create([
            'name'        => 'Su Tesisatı',
            'description' => 'Tesisat işleri',
            'is_active'   => true,
        ]);

        $this->serviceProvider = ServiceProvider::create([
            'user_id'       => $this->providerUser->id,
            'category_id'   => $this->category->id,
            'company_name'  => 'Hasan Tesisat',
            'bio'           => '10 yıllık deneyimli tesisat ustası.',
            'working_hours' => '09:00 - 18:00',
            'latitude'      => 40.9901000,
            'longitude'     => 29.0284000,
        ]);
    }

    public function test_customer_can_view_provider_detail_with_leaflet_map(): void
    {
        $response = $this->actingAs($this->customer)
            ->get("/usta-profili/{$this->serviceProvider->id}");

        $response->assertOk();
        $response->assertSee('leaflet@1.9.4/dist/leaflet.css', false);
        $response->assertSee('leaflet@1.9.4/dist/leaflet.js', false);
        $response->assertSee('id="provider-map"', false);
        $response->assertSee('40.9901', false);
        $response->assertSee('29.0284', false);
        $response->assertSee('Hasan Tesisat');
    }

    public function test_provider_profile_edit_page_contains_interactive_leaflet_map_picker(): void
    {
        $response = $this->actingAs($this->providerUser)
            ->get(route('provider.profile.edit'));

        $response->assertOk();
        $response->assertSee('leaflet@1.9.4/dist/leaflet.css', false);
        $response->assertSee('leaflet@1.9.4/dist/leaflet.js', false);
        $response->assertSee('id="map-picker"', false);
        $response->assertSee('name="latitude"', false);
        $response->assertSee('name="longitude"', false);
        $response->assertSee('coords-display', false);
    }

    public function test_provider_can_update_location_coordinates(): void
    {
        $response = $this->actingAs($this->providerUser)
            ->put(route('provider.profile.update'), [
                'category_id'   => $this->category->id,
                'bio'           => 'Güncel biyografi ve konum bilgisi.',
                'company_name'  => 'Hasan Tesisat Kadıköy',
                'working_hours' => '08:00 - 19:00',
                'latitude'      => 41.0428,
                'longitude'     => 29.0077,
            ]);

        $response->assertRedirect(route('provider.dashboard'));

        $this->assertDatabaseHas('service_providers', [
            'id'           => $this->serviceProvider->id,
            'company_name' => 'Hasan Tesisat Kadıköy',
            'latitude'     => 41.0428,
            'longitude'    => 29.0077,
        ]);
    }

    public function test_test_provider_seeder_creates_10_craftsmen_with_correct_credentials_and_locations(): void
    {
        $this->seed(TestProviderSeeder::class);

        // 10 test ustasının e-postası ve şifresi
        for ($i = 1; $i <= 10; $i++) {
            $email = "usta{$i}@servigo.com";
            $user = User::where('email', $email)->first();

            $this->assertNotNull($user, "Usta kullanıcısı {$email} bulunamadı.");
            $this->assertEquals((int) Role::PROVIDER, (int) $user->role_id);
            $this->assertTrue(Hash::check('12345678', $user->password), "{$email} için şifre '12345678' doğrulanmadı.");

            $this->assertNotNull($user->serviceProvider, "{$email} için service_provider kaydı bulunamadı.");
            $this->assertNotNull($user->serviceProvider->latitude);
            $this->assertNotNull($user->serviceProvider->longitude);
        }
    }
}
