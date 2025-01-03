<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AppConfiguration;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\LoginOTPRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use App\Http\Resources\UserShortResource;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        $user->roles()->attach(Role::where('alias', Role::CLIENT_ROLE_ALIAS)->first()->id);

        $avatar = $request->avatar;

        if ($avatar) {
            $user->addMedia($avatar)->toMediaCollection(User::AVATAR_COLLECTION_NAME);
        }

        event(new Registered($user));

        return $this->handleResponse($user, trans('auth.registered'), Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // IDENTIFIANT CORRECTE ?
        if (Auth::attempt($credentials)) {
            if (AppConfiguration::getByCode(User::CAN_USE_OTP_CONF)->value) {

                // RECUPERER L'EMAIL DE L'UTILISATEUR
                $user = User::where('email', $request->email)->first();

                $otp = Str::random(8);

                $user->update([
                    'otp' => Hash::make($otp),
                    'otp_created_at' => now()
                ]);

                // ENVOYER UN MAIL AVEC LE CODE OTP
                Mail::to($user->email)->send(new OtpMail([
                    'otp' => $otp,
                    'name' => $user->name,
                    'email' => $user->email
                ]));

                return response()->json(['message' => trans('auth.otp_sent'), 'is_otp_active' => true]);
            }

            $user = User::find(Auth::user()->id);

            $data['token'] = $user->createToken('APP API DEV')->plainTextToken;

            $data['user'] = new UserShortResource($user->load('roles'));

            return $this->handleResponse($data, trans('auth.login'));
        } else {

            return $this->handleResponse([], trans('auth.failed'), Response::HTTP_UNAUTHORIZED, false);
        }
    }

    public function login_otp(LoginOTPRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // VERIFIER SI LE CODE OTP A EXPIRÉ
        if ($user->otp_created_at < now()->subMinutes(5)) {
            return $this->handleResponse([], trans('auth.token_expired'), Response::HTTP_UNPROCESSABLE_ENTITY, false);
        }

        // MATCHER LES OTPS
        if (Hash::check($request->otp, $user->otp)) {
            $user->update(['otp' => null]);

            Log::info('LOGIN OTP SUCCESS');
            Auth::login($user);

            $data["token"] = $user->createToken("LaravelSanctumAuth")->plainTextToken;

            $data['user'] = new UserShortResource($user->load('roles.permissions'));

            return $this->handleResponse($data, trans('auth.login'));
        }
        return $this->handleResponse([], trans('auth.failed'), Response::HTTP_UNAUTHORIZED, false);
    }


    public function change_password(ChangePasswordRequest $request)
    {
        $user = User::find(Auth::user()->id);

        if (Hash::check($request->old_password, $user->password)) {
            if (Hash::check($request->password, $user->password)) {
                return $this->handleResponse([], trans('passwords.must_not_match'), 422, false);
            }
            $user->update(['password' => $request->password]);

            return $this->handleResponse([], trans('passwords.changed'));
        }
        return $this->handleResponse([], trans('auth.failed'), Response::HTTP_UNPROCESSABLE_ENTITY, false);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        return $this->handleResponse(null, trans('auth.logout'));
    }
}
