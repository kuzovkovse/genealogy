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
use App\Http\Controllers\PersonDocumentController;
use App\Http\Controllers\PersonMilitaryServiceController;
use App\Http\Controllers\PersonMilitaryDocumentController;

/*
|--------------------------------------------------------------------------
| 🌍 Публичные маршруты
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/p/{uuid}', [PublicPersonController::class, 'show'])
    ->name('people.public');

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
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | 👤 Профиль
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | 👨‍👩‍👧 Люди
    |--------------------------------------------------------------------------
    */
    Route::resource('people', PersonController::class);

    Route::patch('/people/{person}/biography', [PersonController::class, 'updateBiography'])
        ->name('people.biography.update');

    Route::post('/people/{person}/photo', [PersonController::class, 'updatePhoto'])
        ->name('people.photo.update');

    /*
    |--------------------------------------------------------------------------
    | 🪖 Участие в войнах
    |--------------------------------------------------------------------------
    */
    Route::post('/people/{person}/military', [PersonMilitaryServiceController::class, 'store'])
        ->name('military.store');

    Route::patch('/people/military/{service}', [PersonMilitaryServiceController::class, 'update'])
        ->name('military.update');

    Route::delete('/people/military/{service}', [PersonMilitaryServiceController::class, 'destroy'])
        ->name('military.destroy');

    /*
    |--------------------------------------------------------------------------
    | 💍 Браки и дети
    |--------------------------------------------------------------------------
    */
    Route::post('/person/{person}/couples', [CoupleController::class, 'store'])->name('couples.store');
    Route::patch('/couples/{couple}', [CoupleController::class, 'update'])->name('couples.update');

    Route::post('/couples/{couple}/children', [CoupleChildController::class, 'store'])
        ->name('couples.children.store');

    Route::post('/couples/{couple}/children/attach', [CoupleChildController::class, 'attach'])
        ->name('couples.children.attach');

    Route::delete('/couples/{couple}/children/{child}', [CoupleChildController::class, 'detach'])
        ->name('couples.children.detach');

    /*
    |--------------------------------------------------------------------------
    | ⏳ События
    |--------------------------------------------------------------------------
    */
    Route::post('/people/{person}/events', [PersonEventController::class, 'store'])->name('events.store');
    Route::patch('/people/{person}/events/{event}', [PersonEventController::class, 'update'])->name('events.update');
    Route::delete('/people/{person}/events/{event}', [PersonEventController::class, 'destroy'])->name('events.destroy');

    /*
    |--------------------------------------------------------------------------
    | 📸 Фотогалерея
    |--------------------------------------------------------------------------
    */
    Route::post('/people/{person}/photos', [PersonPhotoController::class, 'store'])
        ->name('people.photos.store');

    Route::delete('/people/photos/{photo}', [PersonPhotoController::class, 'destroy'])
        ->name('people.photos.destroy');

    Route::delete('/people/{person}/gallery/{photo}', [PersonController::class, 'destroyGalleryPhoto'])
        ->name('people.gallery.photos.destroy');

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
    Route::get('/tree-view/{person}', fn (Person $person) => view('tree.show', compact('person')))
        ->name('tree.view');

    Route::get('/tree-json/{person}', [TreeController::class, 'show'])
        ->name('tree.json');

    /*
    |--------------------------------------------------------------------------
    | 🕯 Место памяти
    |--------------------------------------------------------------------------
    */
    Route::patch('/people/{person}/memorial', [PersonController::class, 'updateMemorial'])
        ->name('people.memorial.update');

    Route::post('/people/{person}/memorial/candle', [PersonController::class, 'lightCandle'])
        ->middleware('throttle:3,1')
        ->name('people.memorial.candle');

    Route::post('/people/{person}/memorial/photos', [PersonController::class, 'storeMemorialPhoto'])
        ->name('people.memorial.photos.store');
});

/*
   |--------------------------------------------------------------------------
   | ДОКУМЕНТЫ ВОЙНЫ
   |--------------------------------------------------------------------------
   */
Route::post(
    '/military/{service}/documents',
    [PersonMilitaryDocumentController::class, 'store']
)->name('military.documents.store');

Route::delete(
    '/military/documents/{document}',
    [PersonMilitaryDocumentController::class, 'destroy']
)->name('military.documents.destroy');


/*
|--------------------------------------------------------------------------
| 🌱 Онбординг
|--------------------------------------------------------------------------
*/
Route::get('/welcome', fn () => view('welcome.first'))
    ->middleware('auth')
    ->name('welcome.first');

require __DIR__ . '/auth.php';
