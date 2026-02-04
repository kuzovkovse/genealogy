<div class="card mb-4">
    <div class="card-header fw-bold">
        ➕ Добавить фото жизни
    </div>

    <div class="card-body">
        <form action="{{ route('people.photos.store', $person) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="mb-3">
                <label class="form-label">Фото</label>
                <input type="file"
                       name="photo"
                       class="form-control"
                       required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Год</label>
                    <input type="number"
                           name="year"
                           class="form-control"
                           placeholder="Например, 2012">
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label">Заголовок</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           placeholder="Свадьба, армия, выпускной…">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea name="description"
                          class="form-control"
                          rows="3"
                          placeholder="Короткая история или подпись"></textarea>
            </div>

            <button class="btn btn-primary">
                📸 Сохранить фото
            </button>
        </form>
    </div>
</div>
