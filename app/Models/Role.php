<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'title',
        'description',
        'alias',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const ADMIN_ROLE_ALIAS = "A";
    public const COLLABORATEUR_ROLE_ALIAS = "COL";
    public const CLIENT_ROLE_ALIAS = "CLIENT";

    public const ROLE_ALIASES = [
        self::ADMIN_ROLE_ALIAS,
        self::COLLABORATEUR_ROLE_ALIAS,
        self::CLIENT_ROLE_ALIAS,
    ];

    public const ADMINS_ROLE_ALIASES = [
        self::ADMIN_ROLE_ALIAS,
        self::COLLABORATEUR_ROLE_ALIAS
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, "permission_role")->withPivot(['is_active']);
    }
}
