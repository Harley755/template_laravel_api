<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $table = 'permissions';

    public $casts = [
        'default_roles' => 'array'
    ];

    protected $fillable = [
        'title',
        'action',
        'resource',
        'is_active',
        'description',
        'module',
        'default_roles',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
