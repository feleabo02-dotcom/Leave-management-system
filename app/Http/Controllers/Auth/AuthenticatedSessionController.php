<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Log login history
        try {
            \App\Models\LoginHistory::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'success'     => true,
                'logged_in_at'=> now(),
            ]);
            Auth::user()->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
        } catch (\Throwable) {}

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
