<form method="POST"
      action="{{ route('people.memorial.photos.store', $person) }}"
      enctype="multipart/form-data"
      class="memorial-card mt-3">

@csrf

<div class="mb-3">
    <input type="file" name="photo" class="form-control" required>
</div>

<div class="mb-2">
    <input type="text"
           name="title"
           class="form-control"
           placeholder="Например: Памятник, 1980-е">
</div>

<div class="mb-2">
    <input type="number"
           name="taken_year"
           class="form-control"
           placeholder="Год (необязательно)">
</div>

<div class="mb-3">
        <textarea name="description"
                  class="form-control"
                  rows="2"
                  placeholder="Короткое описание"></textarea>
</div>

<button class="btn btn-outline-primary">
    📷 Добавить фото
</button>
</form>
