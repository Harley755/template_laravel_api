<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                "firstname" => "John",
                "lastname" => "Doe",
                "email" => "admin@app.com",
                "phone_number" => "123456789",
                "is_active" => true,
                "password" => bcrypt("password"),
                "can_login" => true,
                "email_verified_at" => now()
            ]
        ];
        $this->createAdmins($admins);

        $clients = [
            [
                "firstname" => "Jack",
                "lastname" => "Doe",
                "email" => "customer@app.com",
                "phone_number" => "323456789",
                "is_active" => true,
                "password" => bcrypt("password"),
                "can_login" => true,
                "email_verified_at" => now()
            ]
        ];
        $this->createClients($clients);
    }

    public function createAdmins(array $admins): void
    {
        foreach ($admins as $admin) {
            $user = User::create($admin);
            $user->roles()->attach(Role::where('alias', Role::ADMIN_ROLE_ALIAS)->first()->id);
        }
    }

    public function createCollaborateurs(array $collaborateurs): void
    {
        foreach ($collaborateurs as $collaborateur) {
            $user = User::create($collaborateur);
            $user->roles()->attach(Role::where('alias', Role::COLLABORATEUR_ROLE_ALIAS)->first()->id);
        }
    }

    public function createClients(array $clients): void
    {
        foreach ($clients as $client) {
            $user = User::create($client);
            $user->roles()->attach(Role::where('alias', Role::CLIENT_ROLE_ALIAS)->first()->id);
        }
    }
}
