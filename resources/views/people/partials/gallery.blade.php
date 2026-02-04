@php
    $photos = $person->photos
        ->sortByDesc(fn ($p) => $p->taken_year ?? 0);
@endphp

<div class="card mb-5">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold">📸 Галерея жизни</span>

        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary" onclick="sortGallery('desc')">
                Сначала новые
            </button>
            <button class="btn btn-outline-secondary" onclick="sortGallery('asc')">
                Сначала старые
            </button>
        </div>
    </div>

    <div class="card-body">
        @if($photos->isEmpty())
            <div class="text-muted">Фотографии пока не добавлены</div>
        @else
            <div id="life-gallery" class="row g-3">
                @foreach($photos as $photo)
                    <div class="col-6 col-md-4 col-lg-3 gallery-item"
                         data-year="{{ $photo->taken_year ?? 0 }}">

                        <div class="life-photo-wrapper position-relative">

                            {{-- ❌ УДАЛЕНИЕ --}}
                            <form method="POST"
                                  action="{{ route('people.gallery.photos.destroy', [$person, $photo]) }}"
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

                            {{-- 📸 КЛИКАБЕЛЬНОЕ ФОТО (ТОЛЬКО ОНО) --}}
                            <a href="{{ asset('storage/'.$photo->image_path) }}"
                               class="glightbox life-photo-link"
                               data-gallery="life"
                               data-title="{{ $photo->title }}"
                               data-description="{{ $photo->description }}">

                                <div class="card card-sm h-100">
                                    <div class="ratio ratio-1x1">
                                        <img src="{{ asset('storage/'.$photo->image_path) }}"
                                             class="card-img-top object-fit-cover"
                                             alt="{{ $photo->title }}">
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

<script>
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
