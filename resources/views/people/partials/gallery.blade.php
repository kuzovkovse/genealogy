@php
    $photos = $person->photos
        ->sortByDesc(fn ($p) => $p->taken_year ?? 0);
@endphp

<div class="card mb-5">
    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold">📸 Галерея жизни</span>

        <div class="d-flex gap-2">
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary"
                        type="button"
                        onclick="sortGallery('desc')">
                    Сначала новые
                </button>
                <button class="btn btn-outline-secondary"
                        type="button"
                        onclick="sortGallery('asc')">
                    Сначала старые
                </button>
            </div>

            <button class="btn btn-sm btn-outline-primary"
                    type="button"
                    onclick="toggleAddLifePhoto()">
                ➕ Добавить фото
            </button>
        </div>
    </div>

    <div class="card-body">

        {{-- ГАЛЕРЕЯ --}}
        @if($photos->isEmpty())
            <div class="text-muted fst-italic mb-2">
                Здесь могут появиться фотографии из разных периодов жизни —
                семейные снимки, важные события, редкие кадры.
            </div>

            @include('people.partials.next-step', [
                'step' => $nextSteps['gallery'] ?? null
            ])
        @else
            <div id="life-gallery" class="row g-3">
                @foreach($photos as $photo)
                    <div class="col-6 col-md-4 col-lg-3 gallery-item"
                         data-year="{{ $photo->taken_year ?? 0 }}">

                        <div class="life-photo-wrapper position-relative">

                            {{-- ❌ УДАЛЕНИЕ --}}
                            <form method="POST"
                                  action="{{ route('people.photos.destroy', $photo) }}"
                                  onsubmit="return confirm('Удалить это фото?')"
                                  class="life-photo-delete">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        title="Удалить фото">
                                    ✕
                                </button>
                            </form>

                            {{-- 📸 ФОТО --}}
                            <a href="{{ asset('storage/'.$photo->image_path) }}"
                               class="glightbox"
                               data-gallery="life"
                               data-title="{{ $photo->title }}"
                               data-description="{{ $photo->description }}">

                                <div class="card h-100">
                                    <div class="ratio ratio-1x1">
                                        <img src="{{ asset('storage/'.$photo->image_path) }}"
                                             class="card-img-top object-fit-cover">
                                    </div>

                                    <div class="card-body p-2">
                                        @if($photo->taken_year)
                                            <div class="text-muted small">
                                                {{ $photo->taken_year }}
                                            </div>
                                        @endif
                                        @if($photo->title)
                                            <div class="fw-semibold small">
                                                {{ $photo->title }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- =======================
         | ДОБАВЛЕНИЕ ФОТО (СКРЫТО)
         ======================= --}}
        <div id="add-life-photo"
             class="border rounded p-3 mt-4 bg-light"
             style="display:none">

            <h6 class="mb-3">➕ Добавить фото жизни</h6>

            <form method="POST"
                  action="{{ route('people.photos.store', $person) }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Фото *</label>
                        <input type="file"
                               name="photo"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Год</label>
                        <input type="number"
                               name="taken_year"
                               class="form-control"
                               placeholder="Напр. 1943">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Заголовок</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               placeholder="Свадьба, армия, выпускной…">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Описание</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="2"></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary btn-sm">
                        💾 Сохранить фото
                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            onclick="toggleAddLifePhoto()">
                        Отмена
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- ===== СТИЛИ ===== --}}
<style>
    .life-photo-wrapper {
        position: relative;
    }

    .life-photo-delete {
        position: absolute;
        top: 6px;
        right: 6px;
        z-index: 20;
        opacity: 0;
        transition: opacity .2s ease;
    }

    .life-photo-wrapper:hover .life-photo-delete {
        opacity: 1;
    }

    .life-photo-delete button {
        padding: 2px 6px;
        line-height: 1;
        border-radius: 999px;
    }
</style>

{{-- ===== JS ===== --}}
<script>
    function toggleAddLifePhoto() {
        const el = document.getElementById('add-life-photo');
        if (!el) return;
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function sortGallery(direction) {
        const container = document.getElementById('life-gallery');
        if (!container) return;

        const items = Array.from(container.querySelectorAll('.gallery-item'));

        items.sort((a, b) => {
            const ay = parseInt(a.dataset.year || 0);
            const by = parseInt(b.dataset.year || 0);
            return direction === 'asc' ? ay - by : by - ay;
        });

        items.forEach(i => container.appendChild(i));
    }
</script>
