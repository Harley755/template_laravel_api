<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\RoleUser;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;


    public const AVATAR_COLLECTION_NAME = "avatar";
    public const CAN_USE_OTP_CONF = 'login.configuration.enable_otp';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone_number',
        'is_active',
        'can_login',
        'otp',
        'otp_created_at',
        'email_verified_at',
        'account_set_token',
        'account_set_token_created_at',
        'password_reset_token',
        'password_reset_token_created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',

        'otp',
        'otp_created_at',

        'account_set_token',
        'account_set_token_created_at',

        'password_reset_token',
        'password_reset_token_created_at',

    ];


    protected $appends = [
        'fullname',
        'avatar',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarAttribute()
    {
        return $this->getFirstMedia(User::AVATAR_COLLECTION_NAME);
    }


    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_user', 'user_id', 'permission_id')
            ->through(Role::class);
    }
}
