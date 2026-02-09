@extends('layouts.app')

@section('title', 'Доступ ограничен')

@section('content')
    <div class="container" style="max-width: 620px">
        <div class="text-center mt-5">

            <div class="display-1 text-danger fw-bold">403</div>

            <h2 class="mt-3">Доступ ограничен</h2>

            <p class="text-muted mt-3 fs-5">
                Это действие доступно только владельцу семьи.
            </p>

            <p class="text-muted">
                Если вам нужно изменить доступ или передать права —
                попросите владельца семьи сделать это.
            </p>

            <div class="mt-4 d-flex justify-content-center gap-3">
                <a href="{{ url()->previous() }}"
                   class="btn btn-outline-secondary">
                    ← Назад
                </a>

                <a href="/people"
                   class="btn btn-primary">
                    👥 Вернуться к людям
                </a>
            </div>

        </div>
    </div>
@endsection
