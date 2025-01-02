<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['title' => 'Administrateur', 'alias' => Role::ADMIN_ROLE_ALIAS],
            ['title' => 'Collaborateur', 'alias' => Role::COLLABORATEUR_ROLE_ALIAS],
            ['title' => 'Client', 'alias' => Role::CLIENT_ROLE_ALIAS],
        ];

        Role::insert($roles);
    }
}
