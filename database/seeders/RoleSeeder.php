<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            Role::ADMIN => 'admin',
            Role::CUSTOMER => 'musteri',
            Role::PROVIDER => 'usta',
        ];

        foreach ($roles as $id => $name) {
            Role::query()->updateOrCreate(
                ['id' => $id],
                ['name' => $name],
            );
        }
    }
}
