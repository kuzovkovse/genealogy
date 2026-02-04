@php
    $documents = $person->documents->sortByDesc('year');
@endphp

<div class="card mb-5">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold">📄 Документы</span>

        <button class="btn btn-sm btn-outline-primary"
                onclick="toggleBlock('add-document')">
            ➕ Добавить документ
        </button>
    </div>

    <div class="card-body">

        {{-- Форма добавления --}}
        <div id="add-document" class="mb-4" style="display:none;">
            <form method="POST"
                  action="{{ route('people.documents.store', $person) }}"
                  enctype="multipart/form-data"
                  class="card card-body">
                @csrf

                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input class="form-control" name="title" placeholder="Название">
                    </div>
                    <div class="col-md-3">
                        <input class="form-control" name="type" placeholder="Тип (паспорт, архив)">
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="year" placeholder="Год">
                    </div>
                </div>

                <textarea class="form-control mb-2"
                          name="description"
                          placeholder="Описание"></textarea>

                <input type="file" name="file" class="form-control mb-2" required>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm">Сохранить</button>
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            onclick="toggleBlock('add-document')">
                        Отмена
                    </button>
                </div>
            </form>
        </div>

        {{-- Список документов --}}
        @if($documents->isEmpty())
            <div class="text-muted">Документы не добавлены</div>
        @else
            <div class="list-group list-group-flush">
                @foreach($documents as $doc)
                    <div class="list-group-item d-flex justify-content-between align-items-start">

                        <div>
                            <div class="fw-semibold">
                                {{ $doc->title ?? 'Документ' }}
                                @if($doc->year)
                                    <span class="text-muted">({{ $doc->year }})</span>
                                @endif
                            </div>

                            <div class="text-muted small">
                                {{ $doc->type }}
                            </div>

                            @if($doc->description)
                                <div class="small mt-1">{{ $doc->description }}</div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            @if($doc->isPdf())
                                <a href="{{ asset('storage/'.$doc->file_path) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-secondary">
                                    📄
                                </a>
                            @else
                                <a href="{{ asset('storage/'.$doc->file_path) }}"
                                   class="btn btn-sm btn-outline-secondary glightbox"
                                   data-gallery="docs">
                                    👁️
                                </a>
                            @endif

                            <form method="POST"
                                  action="{{ route('documents.destroy', $doc) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">✖</button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
