<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\TestCustomerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TestCustomerSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_test_customer_seeder_creates_10_customers_with_correct_credentials(): void
    {
        $this->seed(TestCustomerSeeder::class);

        for ($i = 1; $i <= 10; $i++) {
            $email = "musteri{$i}@servigo.com";
            $user = User::where('email', $email)->first();

            $this->assertNotNull($user, "Müşteri {$email} veritabanında bulunamadı.");
            $this->assertEquals((int) Role::CUSTOMER, (int) $user->role_id);
            $this->assertTrue(Hash::check('12345678', $user->password), "{$email} için şifre '12345678' doğrulanamadı.");
        }

        // Giriş denemesi testi
        $loginResponse = $this->post('/login', [
            'email'    => 'musteri1@servigo.com',
            'password' => '12345678',
        ]);

        $loginResponse->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
    }
}

