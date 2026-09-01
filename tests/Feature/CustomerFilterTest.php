<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_dashboard_renders_and_lists_providers(): void
    {
        $category = ServiceCategory::create(['name' => 'Elektrik Tesisatı']);
        
        $providerUser = User::factory()->create([
            'name'    => 'Ali Usta',
            'role_id' => Role::PROVIDER,
        ]);
        
        ServiceProvider::create([
            'user_id'       => $providerUser->id,
            'category_id'   => $category->id,
            'company_name'  => 'Ali Usta Elektrik Servisi',
            'bio'           => 'Profesyonel elektrik arıza tespiti.',
            'working_hours' => '09:00 - 18:00',
        ]);

        $customer = User::factory()->create([
            'role_id' => Role::CUSTOMER,
        ]);

        $response = $this->actingAs($customer)->get('/musteri');

        $response->assertOk();
        $response->assertSee('Ali Usta Elektrik Servisi');
        $response->assertSee('Elektrik Tesisatı');
        $response->assertSee('09:00 - 18:00');
    }

    public function test_customer_can_filter_providers_by_category(): void
    {
        $electricCat = ServiceCategory::create(['name' => 'Elektrik']);
        $plumbCat = ServiceCategory::create(['name' => 'Tesisat']);

        $electricUser = User::factory()->create(['name' => 'Hasan Elektrikçi', 'role_id' => Role::PROVIDER]);
        ServiceProvider::create([
            'user_id'      => $electricUser->id,
            'category_id'  => $electricCat->id,
            'company_name' => 'Hasan Elektrik A.Ş.',
            'bio'          => 'Tüm elektrik işleri.',
        ]);

        $plumbUser = User::factory()->create(['name' => 'Mehmet Tesisatçı', 'role_id' => Role::PROVIDER]);
        ServiceProvider::create([
            'user_id'      => $plumbUser->id,
            'category_id'  => $plumbCat->id,
            'company_name' => 'Mehmet Su Tesisatı',
            'bio'          => 'Su kaçağı tespiti.',
        ]);

        $customer = User::factory()->create(['role_id' => Role::CUSTOMER]);

        // Filter by electric category ID
        $response = $this->actingAs($customer)->get('/musteri?category=' . $electricCat->id);
        $response->assertOk();
        $response->assertSee('Hasan Elektrik A.Ş.');
        $response->assertDontSee('Mehmet Su Tesisatı');

        // Filter by plumbing category ID
        $responsePlumb = $this->actingAs($customer)->get('/musteri?category=' . $plumbCat->id);
        $responsePlumb->assertOk();
        $responsePlumb->assertSee('Mehmet Su Tesisatı');
        $responsePlumb->assertDontSee('Hasan Elektrik A.Ş.');
    }

    public function test_customer_can_search_providers_by_keyword(): void
    {
        $category = ServiceCategory::create(['name' => 'Genel']);

        $user1 = User::factory()->create(['name' => 'Kemal Usta', 'role_id' => Role::PROVIDER]);
        ServiceProvider::create([
            'user_id'      => $user1->id,
            'category_id'  => $category->id,
            'company_name' => 'Kemal Boya & Badana',
            'bio'          => 'İç cephe ve tavan boyama ustası.',
        ]);

        $user2 = User::factory()->create(['name' => 'Ayşe Temizlik', 'role_id' => Role::PROVIDER]);
        ServiceProvider::create([
            'user_id'      => $user2->id,
            'category_id'  => $category->id,
            'company_name' => 'Ayşe Ev Temizliği',
            'bio'          => 'Detaylı ofis ve ev hijyeni.',
        ]);

        $customer = User::factory()->create(['role_id' => Role::CUSTOMER]);

        // Search for "Boya"
        $response = $this->actingAs($customer)->get('/musteri?search=Boya');
        $response->assertOk();
        $response->assertSee('Kemal Boya & Badana');
        $response->assertDontSee('Ayşe Ev Temizliği');

        // Search for "Temizlik"
        $response = $this->actingAs($customer)->get('/musteri?search=Temizlik');
        $response->assertOk();
        $response->assertSee('Ayşe Ev Temizliği');
        $response->assertDontSee('Kemal Boya & Badana');
    }

    public function test_login_and_register_pages_render_successfully(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertOk();
        $loginResponse->assertSee('ServiGo');
        $loginResponse->assertSee('Tekrar Hoş Geldiniz');

        $registerResponse = $this->get('/register');
        $registerResponse->assertOk();
        $registerResponse->assertSee('ServiGo');
        $registerResponse->assertSee('Hesap Türünüzü Seçin');
        $registerResponse->assertSee('Müşteriyim');
        $registerResponse->assertSee('Ustayı / Hizmet Sağlayıcıyım');
    }
}

