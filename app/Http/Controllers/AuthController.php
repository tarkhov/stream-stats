<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller
{
    public function socialRedirect()
    {
        return Socialite::driver('twitch')->redirect();
    }

    public function socialCallback()
    {
        $twitchUser = Socialite::driver('twitch')->user();

        $user = User::where('email', $twitchUser->email)->first();
        if (!$user) {
            $user = User::create([
                'name'  => $twitchUser->name,
                'email' => $twitchUser->email
            ]);
        }

        $token = $user->createToken('authToken')->plainTextToken;
        $frontendUrl = config('api.frontend_url');
        return redirect()->away("$frontendUrl/login?token=$token");
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function refresh()
    {
    }
}
