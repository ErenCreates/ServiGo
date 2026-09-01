<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_view_profile_edit_page(): void
    {
        $category = ServiceCategory::create(['name' => 'Elektrik']);

        $user = User::factory()->create([
            'role_id' => Role::PROVIDER,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/usta/profil');

        $response->assertOk();
        $response->assertSee('İş Profilini Düzenle');
        $response->assertSee('Elektrik');
        $response->assertSee('Çalışma Saatleri');
    }

    public function test_provider_can_update_profile(): void
    {
        $category = ServiceCategory::create(['name' => 'Tesisat']);

        $user = User::factory()->create([
            'role_id' => Role::PROVIDER,
        ]);

        $response = $this
            ->actingAs($user)
            ->put('/usta/profil', [
                'company_name'  => 'Usta Ahmet Tesisat',
                'category_id'   => $category->id,
                'bio'           => '15 yıllık sıhhi tesisat tecrübesi.',
                'working_hours' => 'Hafta içi 09:00 - 18:00',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/usta');

        $this->assertDatabaseHas('service_providers', [
            'user_id'       => $user->id,
            'category_id'   => $category->id,
            'company_name'  => 'Usta Ahmet Tesisat',
            'bio'           => '15 yıllık sıhhi tesisat tecrübesi.',
            'working_hours' => 'Hafta içi 09:00 - 18:00',
        ]);
    }

    public function test_provider_dashboard_contains_edit_profile_button(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::PROVIDER,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/usta');

        $response->assertOk();
        $response->assertSee('İş Bilgilerimi Düzenle');
        $response->assertSee(route('provider.profile.edit'));
    }
}

