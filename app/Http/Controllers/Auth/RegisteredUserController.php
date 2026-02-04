<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Family;
use App\Models\FamilyUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Форма регистрации
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Обработка регистрации
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 👤 Создаём пользователя
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 🌳 Создаём НОВУЮ семью (всегда!)
        $family = Family::create([
            'name' => 'Семейное древо ' . $user->name,
            'owner_user_id' => $user->id,
        ]);

        // 🔗 Привязываем пользователя как владельца семьи
        FamilyUser::create([
            'family_id' => $family->id,
            'user_id'   => $user->id,
            'role'      => 'owner',
            'joined_at' => now(),
        ]);

        // 🔐 Авторизация
        event(new Registered($user));
        Auth::login($user);

        // ⭐️ КЛЮЧЕВОЕ: активная семья — именно эта
        session(['active_family_id' => $family->id]);

        // 🚀 Первый шаг — создание ПЕРВОГО человека
        return redirect()->route('people.create');
    }
}
