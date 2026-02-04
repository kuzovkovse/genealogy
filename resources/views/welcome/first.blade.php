<?php
<x-guest-layout>

    <div class="login-title">
Добро пожаловать 🌱
</div>

    <div class="login-subtitle">
Давайте создадим первого человека<br>
        в вашем семейном древе
</div>

    <form method="POST" action="{{ route('people.store') }}" enctype="multipart/form-data">
@csrf

{{-- Фото --}}
<div class="mb-4">
    <label class="block text-sm text-gray-600 mb-1">Фотография (необязательно)</label>
    <input type="file"
           name="photo"
           class="w-full border border-gray-300 rounded-lg px-3 py-2">
</div>

{{-- Имя --}}
<div class="mb-4">
    <label class="block text-sm text-gray-600 mb-1">Имя *</label>
    <input
        name="first_name"
        required
        class="w-full border border-gray-300 rounded-lg px-3 py-2"
    >
</div>

{{-- Фамилия --}}
<div class="mb-4">
    <label class="block text-sm text-gray-600 mb-1">Фамилия</label>
    <input
        name="last_name"
        class="w-full border border-gray-300 rounded-lg px-3 py-2"
    >
</div>

{{-- Пол --}}
<div class="mb-4">
    <label class="block text-sm text-gray-600 mb-1">Пол</label>
    <select
        name="gender"
        class="w-full border border-gray-300 rounded-lg px-3 py-2"
    >
        <option value="">—</option>
        <option value="male">Мужской</option>
        <option value="female">Женский</option>
    </select>
</div>

{{-- Дата рождения --}}
<div class="mb-6">
    <label class="block text-sm text-gray-600 mb-1">Дата рождения</label>
    <input
        type="date"
        name="birth_date"
        class="w-full border border-gray-300 rounded-lg px-3 py-2"
    >
</div>

{{-- КНОПКА --}}
<button
    type="submit"
    style="
                background:#1f2937;
                color:#fff;
                width:100%;
                padding:14px;
                border-radius:14px;
                font-weight:600;
                box-shadow:0 10px 25px rgba(0,0,0,.15);
            "
>
    🌳 Создать и перейти к древу
</button>

<div class="text-center mt-4 text-xs text-gray-500">
    Вы сможете добавить родителей, браки и детей позже
</div>

</form>

</x-guest-layout>
