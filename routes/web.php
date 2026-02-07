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
use App\Http\Controllers\FamilyInviteController;

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
| 🔐 Авторизованные маршруты
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

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

    /*
    |--------------------------------------------------------------------------
    | ✏️ ДЕЙСТВИЯ (с ролями)
    |--------------------------------------------------------------------------
    */
    Route::middleware('family.role:owner,editor')->group(function () {

        Route::patch('/people/{person}/biography', [PersonController::class, 'updateBiography'])
            ->name('people.biography.update');

        Route::post('/people/{person}/photo', [PersonController::class, 'updatePhoto'])
            ->name('people.photo.update');

        Route::post('/people/{person}/military', [PersonMilitaryServiceController::class, 'store'])
            ->name('military.store');

        Route::patch('/people/military/{service}', [PersonMilitaryServiceController::class, 'update'])
            ->name('military.update');

        Route::delete('/people/military/{service}', [PersonMilitaryServiceController::class, 'destroy'])
            ->name('military.destroy');

        Route::post('/person/{person}/couples', [CoupleController::class, 'store'])
            ->name('couples.store');

        Route::post('/people/{person}/photos', [PersonPhotoController::class, 'store'])
            ->name('people.photos.store');

        Route::post('/people/{person}/documents', [PersonDocumentController::class, 'store'])
            ->name('people.documents.store');
    });

    /*
    |--------------------------------------------------------------------------
    | 🔥 ТОЛЬКО OWNER
    |--------------------------------------------------------------------------
    */
    Route::middleware('family.role:owner')->group(function () {

        Route::delete('/people/photos/{photo}', [PersonPhotoController::class, 'destroy'])
            ->name('people.photos.destroy');

        Route::delete('/documents/{document}', [PersonDocumentController::class, 'destroy'])
            ->name('documents.destroy');

        Route::post('/families/{family}/invite', [FamilyInviteController::class, 'store'])
            ->name('families.invite');
    });

    /*
    |--------------------------------------------------------------------------
    | 🌳 Дерево
    |--------------------------------------------------------------------------
    */
    Route::get('/tree-view/{person}', fn (Person $person) => view('tree.show', compact('person')))
        ->name('tree.view');

    Route::get('/tree-json/{person}', [TreeController::class, 'show'])
        ->name('tree.json');
});

/*
|--------------------------------------------------------------------------
| 🌱 Онбординг
|--------------------------------------------------------------------------
*/
Route::get('/welcome', fn () => view('welcome.first'))
    ->middleware('auth')
    ->name('welcome.first');

require __DIR__ . '/auth.php';
