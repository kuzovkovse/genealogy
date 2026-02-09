@extends('layouts.app')

@section('content')
    <div class="container">

        <h2 class="mb-3">Передача владения семьёй</h2>

        <div class="alert alert-warning">
            <strong>Внимание:</strong><br>
            После передачи владения вы перестанете быть владельцем семьи
            и станете обычным участником.
        </div>

        @if ($candidates->isEmpty())
            <div class="alert alert-info">
                В семье нет участников, которым можно передать владение.
            </div>
        @else
            <form method="POST" action="{{ route('family.ownership.transfer') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Новый владелец</label>
                    <select
                        name="new_owner_user_id"
                        class="form-select"
                        required
                    >
                        <option value="">— выберите участника —</option>

                        @foreach ($candidates as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->email }}
                                ({{ $user->pivot->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button
                    type="submit"
                    class="btn btn-danger"
                    onclick="return confirm('Вы уверены? Это действие нельзя отменить.')"
                >
                    Передать владение
                </button>
            </form>
        @endif

    </div>
@endsection
