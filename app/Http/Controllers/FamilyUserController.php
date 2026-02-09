<?php

namespace App\Http\Controllers;

use App\Services\FamilyContext;

class FamilyUserController extends Controller
{
    /**
     * 👥 Экран участников семьи
     */
    public function index()
    {
        // Активная семья (через уже существующий контекст)
        $family = FamilyContext::require();

        // Подгружаем пользователей + роли
        $family->load('users');

        return view('family.users', compact('family'));
    }
}
