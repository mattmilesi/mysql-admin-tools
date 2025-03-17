<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $otp = TOTP::generate();
        return view('auth.login', [
            'random_secret' => $otp->getSecret(),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed, store the user in the session
            /** @noinspection PhpUndefinedFieldInspection */
            $totp = TOTP::createFromSecret(Auth::user()->mfa_secret);
            if ($totp->verify($request->input('mfa_code'))) {
                $request->session()->regenerate();
                return redirect()->intended('/'); // Redirect to intended page
            }
            return back()->withErrors([
                'mfa_code' => 'The provided code is not correct.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
