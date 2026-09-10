<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates 10 test customers with emails musteri1@servigo.com to musteri10@servigo.com
     * with standard password '12345678'.
     */
    public function run(): void
    {
        $customers = [
            ['name' => 'Ahmet Yılmaz', 'email' => 'musteri1@servigo.com'],
            ['name' => 'Ayşe Kaya',    'email' => 'musteri2@servigo.com'],
            ['name' => 'Mehmet Demir', 'email' => 'musteri3@servigo.com'],
            ['name' => 'Fatma Çelik',  'email' => 'musteri4@servigo.com'],
            ['name' => 'Emre Şahin',   'email' => 'musteri5@servigo.com'],
            ['name' => 'Zeynep Öztürk','email' => 'musteri6@servigo.com'],
            ['name' => 'Caner Aydın',  'email' => 'musteri7@servigo.com'],
            ['name' => 'Elif Yıldız',  'email' => 'musteri8@servigo.com'],
            ['name' => 'Burak Arslan', 'email' => 'musteri9@servigo.com'],
            ['name' => 'Gamze Koç',    'email' => 'musteri10@servigo.com'],
        ];

        foreach ($customers as $customer) {
            User::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name'              => $customer['name'],
                    'password'          => Hash::make('12345678'),
                    'role_id'           => Role::CUSTOMER,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

