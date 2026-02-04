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
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1️⃣ Аутентификация
        $request->authenticate();

        // 2️⃣ Регенерация сессии (важно для безопасности)
        $request->session()->regenerate();

        // 3️⃣ 🔥 ВСЕГДА выставляем активную семью
        if (auth()->check()) {
            $family = auth()->user()
                ->families()
                ->orderBy('family_users.created_at')
                ->first();

            if ($family) {
                session(['active_family_id' => $family->id]);
            }
        }

        // 4️⃣ Редирект
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
