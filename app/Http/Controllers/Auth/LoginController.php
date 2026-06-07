<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Generic message — do not distinguish "email not found" from "wrong password" (VULN-04)
            Log::warning('Failed login attempt', [       // VULN-17
                'email' => $request->email,
                'ip'    => $request->ip(),
            ]);
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            Log::warning('Login blocked — inactive account', [  // VULN-17
                'user_id' => $user->id,
                'status'  => $user->status,
                'ip'      => $request->ip(),
            ]);
            return back()->withErrors(['email' => 'Your account is not active. Please contact support.']);
        }

        $user->last_login_at = now();
        $user->save();

        $request->session()->regenerate();

        Log::info('User logged in', ['user_id' => $user->id, 'ip' => $request->ip()]); // VULN-17

        return redirect()->intended($this->redirectTo($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['user_id' => $userId, 'ip' => $request->ip()]); // VULN-17

        return redirect()->route('login');
    }

    private function redirectTo(\App\Models\User $user): string
    {
        return match ($user->role) {
            'admin'  => route('admin.dashboard'),
            'seller' => route('seller.dashboard'),
            default  => route('home'),
        };
    }
}
