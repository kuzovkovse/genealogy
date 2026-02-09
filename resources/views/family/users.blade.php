@extends('layouts.app')

@section('title', 'Участники семьи')

@section('content')

    <div class="container" style="max-width: 900px">

        {{-- Заголовок + действие --}}
        <h1 class="mb-4 d-flex justify-content-between align-items-center">
            👨‍👩‍👧 Участники семьи

            @if(auth()->user()->isOwnerOfFamily($family))
                <a
                    href="{{ route('family.ownership') }}"
                    class="btn btn-outline-danger btn-sm"
                >
                    Передать владение
                </a>
            @endif
        </h1>

        {{-- ===== ТЕКУЩИЕ УЧАСТНИКИ ===== --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                Текущие участники
            </div>

            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Пользователь</th>
                        <th style="width: 180px">Роль</th>
                        <th style="width: 200px"></th>
                    </tr>
                    </thead>

                    <tbody>
                    @php
                        $roleLabels = [
                            'owner'  => '👑 Владелец',
                            'editor' => '✏️ Редактор',
                            'viewer' => '👁 Наблюдатель',
                        ];
                    @endphp

                    @foreach($family->users as $user)
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $user->name ?? $user->email }}
                                </div>
                                <div class="text-muted small">
                                    {{ $user->email }}
                                </div>
                            </td>

                            <td>
                            <span class="badge bg-secondary">
                                {{ $roleLabels[$user->pivot->role] ?? $user->pivot->role }}
                            </span>
                            </td>

                            <td class="text-end">
                                @if(
                                    auth()->user()->isOwnerOfFamily($family)
                                    && auth()->id() !== $user->id
                                )
                                    <div class="d-flex gap-2 justify-content-end">
                                        {{-- Заглушки под будущий функционал --}}
                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            disabled
                                            title="Скоро будет доступно"
                                        >
                                            Сменить роль
                                        </button>

                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            disabled
                                            title="Удаление владельца запрещено"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== ПРИГЛАШЕНИЕ ===== --}}
        @if(auth()->user()->isOwnerOfFamily($family))
            <div class="card">
                <div class="card-header fw-semibold">
                    Пригласить нового участника
                </div>

                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ route('families.invite', $family) }}"
                        class="row g-3"
                    >
                        @csrf

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required
                                placeholder="email@example.com"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Роль</label>
                            <select name="role" class="form-select" required>
                                <option value="viewer">👁 Наблюдатель</option>
                                <option value="editor">✏️ Редактор</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100">
                                ➕ Пригласить
                            </button>
                        </div>
                    </form>

                    @if(session('invite_link'))
                        <div class="alert alert-success mt-3">
                            <div class="fw-semibold mb-1">
                                Ссылка приглашения (dev):
                            </div>
                            <code>{{ session('invite_link') }}</code>
                        </div>
                    @endif

                    <div class="text-muted small mt-3">
                        Приглашённый пользователь получит письмо и доступ к этой семье.
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection
