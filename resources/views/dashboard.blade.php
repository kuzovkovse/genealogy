<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-6">

        {{-- 🧠 уведомления теперь ТОЛЬКО в navbar --}}
        {{-- <x-family-notifications /> ❌ УДАЛЕНО --}}

        <div class="bg-white shadow-sm rounded-lg p-6">
            Вы вошли в систему
        </div>

    </div>
</x-app-layout>
