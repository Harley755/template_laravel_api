<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthGate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() != null) {
            $user = User::find(Auth::user()->id);

            $permissions = Permission::whereHas('roles', function ($role) use ($user) {
                $role->whereIn('id', $user->roles->pluck('id'))->where('is_active', true);
            })->get();

            foreach ($permissions as $permission) {
                Gate::define($permission->title, function () use (&$permission) {
                    return $permission != null;
                });
            }
        }
        return $next($request);
    }
}
