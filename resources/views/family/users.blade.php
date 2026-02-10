@extends('layouts.app')

@section('title', 'Участники семьи')

@section('content')

    <div class="container" style="max-width: 900px">

        {{-- ===== TOAST ===== --}}
        @if(session('role_updated'))
            <div class="toast-lite">
                ✅ Роль изменена
            </div>
        @endif

        {{-- ===== HEADER ===== --}}
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
                        <th style="width: 200px">Роль</th>
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
                        <tr class="user-row">
                            {{-- Пользователь --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $user->name ?? $user->email }}
                                </div>
                                <div class="text-muted small">
                                    {{ $user->email }}
                                </div>
                            </td>

                            {{-- Роль --}}
                            <td>
                            <span class="badge rounded-pill text-bg-light border">
                                {{ $roleLabels[$user->pivot->role] ?? $user->pivot->role }}
                            </span>
                            </td>

                            {{-- Действия --}}
                            <td class="text-end">
                                @if(
                                    auth()->user()->isOwnerOfFamily($family)
                                    && auth()->id() !== $user->id
                                )
                                    <form
                                        method="POST"
                                        action="{{ route('family.users.role.update', $user) }}"
                                        class="d-flex justify-content-end align-items-center gap-2 user-actions role-form"
                                        data-role-form
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <select
                                            name="role"
                                            class="form-select form-select-sm role-select"
                                            style="width: 140px"
                                            data-original="{{ $user->pivot->role }}"
                                        >
                                            <option value="viewer"
                                                {{ $user->pivot->role === 'viewer' ? 'selected' : '' }}>
                                                👁 Наблюдатель
                                            </option>
                                            <option value="editor"
                                                {{ $user->pivot->role === 'editor' ? 'selected' : '' }}>
                                                ✏️ Редактор
                                            </option>
                                        </select>

                                        <div class="btn-group btn-group-sm" role="group">
                                            <button
                                                type="submit"
                                                class="btn btn-outline-primary save-btn"
                                                disabled
                                                title="Сохранить роль"
                                            >
                                                💾
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-outline-danger"
                                                disabled
                                                title="Удаление владельца запрещено"
                                            >
                                                🗑
                                            </button>
                                        </div>
                                    </form>
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
                        class="row g-3 align-items-end"
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

                        <div class="col-md-2">
                            <button
                                class="btn btn-outline-primary btn-sm px-3 d-inline-flex align-items-center gap-1"
                            >
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

    {{-- ===== CSS ===== --}}
    <style>
        tr.user-row .user-actions {
            opacity: 0;
            pointer-events: none;
            transition: opacity .15s ease;
        }

        tr.user-row:hover .user-actions {
            opacity: 1;
            pointer-events: auto;
        }

        .toast-lite {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #111827;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,.25);
            z-index: 9999;
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0%   { opacity: 0; transform: translateY(-5px); }
            10%  { opacity: 1; transform: translateY(0); }
            80%  { opacity: 1; }
            100% { opacity: 0; transform: translateY(-5px); }
        }
    </style>

    {{-- ===== JS ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.role-form').forEach(form => {
                const select = form.querySelector('.role-select');
                const saveBtn = form.querySelector('.save-btn');
                const original = select.dataset.original;

                function sync() {
                    saveBtn.disabled = (select.value === original);
                }

                select.addEventListener('change', sync);
                sync();
            });
        });
    </script>

@endsection
