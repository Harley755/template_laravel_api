<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        $user->roles()->attach(Role::where('alias', Role::ADMIN_ROLE_ALIAS)->first()->id);

        $avatar = $request->avatar;

        if ($avatar) {
            $user->addMedia($avatar)->toMediaCollection(User::AVATAR_COLLECTION_NAME);
        }

        event(new Registered($user));

        return $this->handleResponse($user, trans('auth.registered'), Response::HTTP_CREATED);
    }

    public function login() {}
}
