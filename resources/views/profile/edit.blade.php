@extends('layouts.app')

@section('title', 'Профиль')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- ОБНОВЛЕНИЕ ПРОФИЛЯ --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="h4 mb-3">Профиль</h3>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- СМЕНА ПАРОЛЯ --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="h4 mb-3">Смена пароля</h3>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- УДАЛЕНИЕ АККАУНТА --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="h4 mb-3 text-danger">Удаление аккаунта</h3>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>

@endsection
