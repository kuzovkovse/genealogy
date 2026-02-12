<?php

use Illuminate\Support\Facades\Route;
use App\Models\Person;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PublicPersonController;
use App\Http\Controllers\CoupleController;
use App\Http\Controllers\CoupleChildController;
use App\Http\Controllers\PersonEventController;
use App\Http\Controllers\PersonPhotoController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\PersonDocumentController;
use App\Http\Controllers\PersonMilitaryServiceController;
use App\Http\Controllers\PersonMilitaryDocumentController;
use App\Http\Controllers\FamilyInviteController;
use App\Http\Controllers\FamilyUserController;
use App\Http\Controllers\FamilyOwnershipController;
use App\Http\Controllers\FamilyHistoryController;
use App\Observers\PersonObserver;


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

    /*
    |--------------------------------------------------------------------------
    | 📩 Приглашения в семью
    |--------------------------------------------------------------------------
    */

    // экран принятия приглашения
    Route::get('/family/invite/{token}', [FamilyInviteController::class, 'accept'])
        ->name('family.invites.accept');

    // подтверждение принятия (ЕДИНСТВЕННЫЙ POST)
    Route::post('/family/invite/{token}', [FamilyInviteController::class, 'acceptPost'])
        ->name('family.invites.accept.post');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware([
        'auth',
        'set.active.family', // 🔑 КЛЮЧЕВО
    ])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | 👤 Профиль пользователя
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | ➕ СОЗДАНИЕ ЧЕЛОВЕКА
    |--------------------------------------------------------------------------
    */
    Route::get('/people/create', [PersonController::class, 'create'])
        ->name('people.create');

    Route::post('/people', [PersonController::class, 'store'])
        ->name('people.store');

    /*
    |--------------------------------------------------------------------------
    | 👨‍👩‍👧 Просмотр людей
    |--------------------------------------------------------------------------
    */
    Route::middleware(['set.active.family','family.role:owner,editor,viewer'])->group(function () {

        Route::get('/people', [PersonController::class, 'index'])
            ->name('people.index');

        Route::get('/people/{person}', [PersonController::class, 'show'])
            ->name('people.show');
    });

    /*
    |--------------------------------------------------------------------------
    | ✏️ Редактирование и управление
    |--------------------------------------------------------------------------
    */
    Route::middleware(['set.active.family', 'family.role:owner,editor'])->group(function () {


        Route::get('/people/{person}/edit', [PersonController::class, 'edit'])
            ->name('people.edit');

        Route::patch('/people/{person}', [PersonController::class, 'update'])
            ->name('people.update');

        Route::patch('/people/{person}/biography', [PersonController::class, 'updateBiography'])
            ->name('people.biography.update');

        Route::post('/people/{person}/photo', [PersonController::class, 'updatePhoto'])
            ->name('people.photo.update');

        Route::post('/people/{person}/photos', [PersonPhotoController::class, 'store'])
            ->name('people.photos.store');

        Route::post('/people/{person}/documents', [PersonDocumentController::class, 'store'])
            ->name('people.documents.store');

        Route::post('/people/{person}/events', [PersonEventController::class, 'store'])
            ->name('events.store');

        Route::patch('/people/{person}/events/{event}', [PersonEventController::class, 'update'])
            ->name('events.update');

        Route::delete('/people/{person}/events/{event}', [PersonEventController::class, 'destroy'])
            ->name('events.destroy');

        Route::post('/people/{person}/military', [PersonMilitaryServiceController::class, 'store'])
            ->name('military.store');

        Route::patch('/people/military/{service}', [PersonMilitaryServiceController::class, 'update'])
            ->name('military.update');

        Route::delete('/people/military/{service}', [PersonMilitaryServiceController::class, 'destroy'])
            ->name('military.destroy');

        Route::post('/people/military/{service}/documents', [PersonMilitaryDocumentController::class, 'store'])
            ->name('military.documents.store');

        Route::delete('/military-documents/{document}', [PersonMilitaryDocumentController::class, 'destroy'])
            ->name('military.documents.destroy');

        Route::post('/person/{person}/couples', [CoupleController::class, 'store'])
            ->name('couples.store');

        Route::post('/couples/{couple}/children', [CoupleChildController::class, 'store'])
            ->name('couples.children.store');

        Route::post('/couples/{couple}/children/attach', [CoupleChildController::class, 'attach'])
            ->name('couples.children.attach');

        Route::patch('/people/{person}/memorial', [PersonController::class, 'updateMemorial'])
            ->name('people.memorial.update');

        Route::post('/people/{person}/memorial/photos', [PersonController::class, 'storeMemorialPhoto'])
            ->name('people.memorial.photos.store');
    });

    /*
    |--------------------------------------------------------------------------
    | 🔥 Только владелец семьи
    |--------------------------------------------------------------------------
    */
    Route::middleware('family.role:owner')->group(function () {

        // ✅ ВОЗВРАЩЁН
        Route::get('/family/users', [FamilyUserController::class, 'index'])
            ->name('family.users.index');

        Route::patch('/family/users/{user}/role', [FamilyUserController::class, 'updateRole'])->name('family.users.role.update');

        Route::post('/families/{family}/invite', [FamilyInviteController::class, 'store'])
            ->name('families.invite');

        Route::delete('/people/photos/{photo}', [PersonPhotoController::class, 'destroy'])
            ->name('people.photos.destroy');

        Route::delete('/documents/{document}', [PersonDocumentController::class, 'destroy'])
            ->name('documents.destroy');

        Route::delete('/couples/{couple}/children/{child}', [CoupleChildController::class, 'detach'])
            ->name('couples.children.detach');


        });


    /*
    |--------------------------------------------------------------------------
    | 🕯 Свеча памяти — НЕ ТРОГАЕМ
    |--------------------------------------------------------------------------
    */
    Route::post('/people/{person}/memorial/candle', [PersonController::class, 'lightCandle'])
        ->name('people.memorial.candle');

    /*
    |--------------------------------------------------------------------------
    | 🌳 Дерево
    |--------------------------------------------------------------------------
    */
    Route::get('/tree-view/{person}', fn (Person $person) =>
    view('tree.show', compact('person'))
    )->name('tree.view');

    Route::get('/tree-json/{person}', [TreeController::class, 'show'])
        ->name('tree.json');
});

/*
   |--------------------------------------------------------------------------
   | Передача прав собственности на семью (только POST, чтобы не было случайных кликов и т.п.)
   |--------------------------------------------------------------------------
   */
Route::middleware([
    'auth',
    'set.active.family',
    'family.role:owner'
])->group(function () {

    Route::get(
        '/family/ownership',
        [FamilyOwnershipController::class, 'index']
    )->name('family.ownership');

    Route::post(
        '/family/ownership/transfer',
        [FamilyOwnershipController::class, 'transfer']
    )->name('family.ownership.transfer');

});

/*
|--------------------------------------------------------------------------
| Логирование истории
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'set.active.family'])->group(function () {

    Route::get('/family/history', [FamilyHistoryController::class, 'index'])
        ->name('family.history');

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
