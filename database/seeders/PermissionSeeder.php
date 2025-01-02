<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $everybody = Role::ROLE_ALIASES;
        $admins = Role::ADMINS_ROLE_ALIASES;

        $admin = Role::ADMIN_ROLE_ALIAS;
        $collaborateur = Role::COLLABORATEUR_ROLE_ALIAS;
        $client = Role::CLIENT_ROLE_ALIAS;

        $permissions = [

            ...$this->createPermissions('otp', ['can_use' => "Pouvoir utiliser la connexion OTP"], $everybody),
            ...$this->createPermissions('password', ['can_change' => "Pouvoir changer son mot de passe"], $everybody),

            ...$this->createPermissions('app_configuration', ['create', 'edit', 'delete', 'access'], $admins),
            ...$this->createPermissions('app_configuration', ['show', 'search', 'list'], $everybody),

            ...$this->createPermissions('permission', ['create', 'edit', 'delete', 'manage'], $admins),
            ...$this->createPermissions('permission', ['show', 'access', 'search', 'list'], $everybody),

            ...$this->createPermissions('role', ['create', 'edit', 'delete', 'manage'], $admins),
            ...$this->createPermissions('role', ['show', 'access', 'search', 'list'], $everybody),

            ...$this->createPermissions('user', ['access', 'create', 'edit', 'delete', 'history_access'], $admin),
            ...$this->createPermissions('user', ['show'], $everybody),

            ...$this->createPermissions('faq', ['access', 'show', 'search'], $everybody),
            ...$this->createPermissions('faq', ['create', 'edit', 'delete'], $admin),

            ...$this->createPermissions('faq_section', ['access', 'show', 'search'], $everybody),
            ...$this->createPermissions('faq_section', ['create', 'edit', 'delete'], $admins),

            ...$this->createPermissions('movie_category', ['access', 'show', 'search'], $everybody),
            ...$this->createPermissions('movie_category', ['create', 'edit', 'delete'], $admins),

            ...$this->createPermissions('movie', ['access', 'show', 'search'], $everybody),
            ...$this->createPermissions('movie', ['create', 'edit', 'delete'], $admins),

            ...$this->createPermissions('seance', ['access', 'show', 'search'], $everybody),
            ...$this->createPermissions('seance', ['create', 'edit', 'delete'], $admins),

            ...$this->createPermissions('product', ['access', 'show', 'search'], $everybody),
            ...$this->createPermissions('product', ['create', 'edit', 'delete'], $admins),

            ...$this->createPermissions('reservation', ['access', 'show', 'search'], $everybody),
            ...$this->createPermissions('reservation', ['create', 'edit', 'delete'], $admins),

            ...$this->createPermissions('mail', ['access', 'show', 'search'], $everybody),
            ...$this->createPermissions('mail', ['create', 'edit', 'delete'], $admins),


        ];

        Permission::insert($permissions);
    }

    public function createPermissions($resource, $permissions, $default_roles = [], $module = null)
    {
        $result = array_map(function ($permission, $description) use (&$resource, &$default_roles, &$module) {
            if (gettype($permission) == "integer") {
                $permission = $description;
                $description = null;
            }
            $item = [
                "title" => $resource . '_' . $permission,
                "resource" => $resource,
                "module" => $module,
                "description" => $description,
                "action" => $permission,
                "default_roles" => json_encode($default_roles)
            ];
            return $item;
        }, array_keys($permissions),   $permissions);

        return $result;
    }
}
