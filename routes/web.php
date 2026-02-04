<?php

use Illuminate\Support\Facades\Route;
use App\Models\Person;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PublicPersonController;
use App\Http\Controllers\CoupleController;
use App\Http\Controllers\CoupleChildController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PersonEventController;
use App\Http\Controllers\PersonPhotoController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\TreeController;
use App\Models\MemorialPhoto;
use App\Http\Controllers\PersonDocumentController;

/*
|--------------------------------------------------------------------------
| 🌍 Публичные маршруты
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
| 🌐 Публичная страница человека (по UUID)
*/
Route::get('/p/{uuid}', [PublicPersonController::class, 'show'])
    ->name('people.public');

/*
| 🖼 SVG-аватар по инициалам
*/
Route::get('/avatar', [AvatarController::class, 'show'])
    ->name('avatar');

/*
|--------------------------------------------------------------------------
| 🔐 Авторизованные маршруты + активная семья
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'active.family'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 🏠 Дашборд
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | 👤 Профиль пользователя (Breeze)
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | 👨‍👩‍👧 Люди (REST)
    |--------------------------------------------------------------------------
    */

    Route::get('/people', [PersonController::class, 'index'])
        ->name('people.index');

    Route::get('/people/create', [PersonController::class, 'create'])
        ->name('people.create');

    Route::post('/people', [PersonController::class, 'store'])
        ->name('people.store');

    Route::get('/people/{person}', [PersonController::class, 'show'])
        ->name('people.show');

    Route::get('/people/{person}/edit', [PersonController::class, 'edit'])
        ->name('people.edit');

    Route::patch('/people/{person}', [PersonController::class, 'update'])
        ->name('people.update');

    Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');

    /*
    |--------------------------------------------------------------------------
    | 📖 Биография и главное фото
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/people/{person}/biography',
        [PersonController::class, 'updateBiography']
    )->name('people.biography.update');

    Route::post(
        '/people/{person}/photo',
        [PersonController::class, 'updatePhoto']
    )->name('people.photo.update');

    /*
    |--------------------------------------------------------------------------
    | 💍 Браки
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/person/{person}/couples',
        [CoupleController::class, 'store']
    )->name('couples.store');

    Route::patch(
        '/couples/{couple}',
        [CoupleController::class, 'update']
    )->name('couples.update');

    /*
    |--------------------------------------------------------------------------
    | 👶 Дети (через брак)
    |--------------------------------------------------------------------------
    */

    // ➕ Новый ребёнок
    Route::post(
        '/couples/{couple}/children',
        [CoupleChildController::class, 'store']
    )->name('couples.children.store');

    // 🔗 Привязать существующего ребёнка
    Route::post(
        '/couples/{couple}/children/attach',
        [CoupleChildController::class, 'attach']
    )->name('couples.children.attach');

    // 🗑 Отвязать ребёнка
    Route::delete(
        '/couples/{couple}/children/{child}',
        [CoupleChildController::class, 'detach']
    )->name('couples.children.detach');

    /*
    |--------------------------------------------------------------------------
    | ⏳ Хронология жизни (события)
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/people/{person}/events',
        [PersonEventController::class, 'store']
    )->name('events.store');

    Route::patch('/people/{person}/events/{event}', [PersonEventController::class, 'update'])
        ->name('events.update');

    Route::delete('/people/{person}/events/{event}', [PersonEventController::class, 'destroy'])
        ->name('events.destroy');

    /*
    |--------------------------------------------------------------------------
    | 📸 Фотогалерея
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/people/{person}/photos',
        [PersonPhotoController::class, 'store']
    )->name('people.photos.store');

    Route::delete(
        '/people/photos/{photo}',
        [PersonPhotoController::class, 'destroy']
    )->name('people.photos.destroy');

    Route::delete(
        '/people/{person}/gallery/{photo}',
        [PersonController::class, 'destroyGalleryPhoto']
    )->name('people.gallery.photos.destroy');
    /*
    |--------------------------------------------------------------------------
    | 📄 Документы
    |--------------------------------------------------------------------------
    */

    Route::post('/people/{person}/documents', [PersonDocumentController::class, 'store'])
        ->name('people.documents.store');

    Route::delete('/documents/{document}', [PersonDocumentController::class, 'destroy'])
        ->name('documents.destroy');

    /*
    |--------------------------------------------------------------------------
    | 🌳 Генеалогическое дерево
    |--------------------------------------------------------------------------
    */

    Route::get('/tree-view/{person}', function (Person $person) {
        return view('tree.show', compact('person'));
    })->name('tree.view');

    Route::get(
        '/tree-json/{person}',
        [TreeController::class, 'show']
    )->name('tree.json');

});

/*
  |--------------------------------------------------------------------------
  | 🌳 Онбдординг
  |--------------------------------------------------------------------------
  */
Route::get('/welcome', function () {
    return view('welcome.first');
})->middleware(['auth'])->name('welcome.first');
/*
|--------------------------------------------------------------------------
| 🔐 Маршруты аутентификации (Breeze)
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Место памяти
|--------------------------------------------------------------------------
*/
Route::patch(
    '/people/{person}/memorial',
    [PersonController::class, 'updateMemorial']
)->name('people.memorial.update');

/*
|--------------------------------------------------------------------------
| Место памяти свечи
|--------------------------------------------------------------------------
*/
Route::post('/people/{person}/memorial/candle', [PersonController::class, 'lightCandle'])
    ->name('people.memorial.candle');
/*
|--------------------------------------------------------------------------
| Фото место памяти
|--------------------------------------------------------------------------
*/
Route::post('/people/{person}/memorial/candle',
    [PersonController::class, 'lightCandle']
)->middleware(['auth', 'throttle:3,1'])
    ->name('people.memorial.candle');


Route::middleware(['auth'])->group(function () {

    Route::post(
        '/people/{person}/memorial/photos',
        [PersonController::class, 'storeMemorialPhoto']
    )->name('people.memorial.photos.store');
});

require __DIR__ . '/auth.php';
