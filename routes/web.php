<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EventController::class, 'index'])->name('home');

Route::get('/acara/{slug}', [EventController::class, 'show'])
    ->name('events.show');

Route::post('/acara/{slug}/daftar', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('events.register');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });
    Route::post('/logout', [AdminAuthController::class, 'destroy'])->middleware(['auth', 'admin'])->name('logout');
    Route::get('/', DashboardController::class)->middleware(['auth', 'admin'])->name('dashboard');
});

// Manajemen acara — hanya untuk administrator yang sudah login.
Route::middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])
    ->prefix('admin/acara')
    ->name('admin.events.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EventController::class, 'index'])
            ->name('index');
        Route::get('/tambah', [\App\Http\Controllers\Admin\EventController::class, 'create'])
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EventController::class, 'store'])
            ->name('store');
        Route::get('/{event}/edit', [\App\Http\Controllers\Admin\EventController::class, 'edit'])
            ->name('edit');
        Route::put('/{event}', [\App\Http\Controllers\Admin\EventController::class, 'update'])
            ->name('update');
        Route::delete('/{event}', [\App\Http\Controllers\Admin\EventController::class, 'destroy'])
            ->name('destroy');
    });

// Manajemen jadwal perjalanan untuk setiap acara.
Route::middleware(["auth", \App\Http\Middleware\EnsureAdmin::class])
    ->prefix("admin/acara/{event}/jadwal")
    ->name("admin.events.schedules.")
    ->group(function () {
        Route::get("/", [
            \App\Http\Controllers\Admin\ScheduleController::class,
            "index",
        ])->name("index");

        Route::get("/tambah", [
            \App\Http\Controllers\Admin\ScheduleController::class,
            "create",
        ])->name("create");

        Route::post("/", [
            \App\Http\Controllers\Admin\ScheduleController::class,
            "store",
        ])->name("store");

        Route::get("/{schedule}/edit", [
            \App\Http\Controllers\Admin\ScheduleController::class,
            "edit",
        ])->name("edit");

        Route::put("/{schedule}", [
            \App\Http\Controllers\Admin\ScheduleController::class,
            "update",
        ])->name("update");

        Route::delete("/{schedule}", [
            \App\Http\Controllers\Admin\ScheduleController::class,
            "destroy",
        ])->name("destroy");
    });

// Manajemen pengumuman per acara — khusus administrator.
Route::middleware(["auth", \App\Http\Middleware\EnsureAdmin::class])
    ->prefix("admin/acara/{event}/pengumuman")
    ->name("admin.events.announcements.")
    ->group(function () {
        Route::get("/", [
            \App\Http\Controllers\Admin\AnnouncementController::class,
            "index",
        ])->name("index");

        Route::get("/tambah", [
            \App\Http\Controllers\Admin\AnnouncementController::class,
            "create",
        ])->name("create");

        Route::post("/", [
            \App\Http\Controllers\Admin\AnnouncementController::class,
            "store",
        ])->name("store");

        Route::get("/{announcement}/edit", [
            \App\Http\Controllers\Admin\AnnouncementController::class,
            "edit",
        ])->name("edit");

        Route::put("/{announcement}", [
            \App\Http\Controllers\Admin\AnnouncementController::class,
            "update",
        ])->name("update");

        Route::delete("/{announcement}", [
            \App\Http\Controllers\Admin\AnnouncementController::class,
            "destroy",
        ])->name("destroy");
    });

// Daftar peserta dan ekspor CSV per acara.
Route::middleware(["auth", \App\Http\Middleware\EnsureAdmin::class])
    ->prefix("admin/acara/{event}/peserta")
    ->name("admin.events.participants.")
    ->group(function () {
        Route::get("/", [
            \App\Http\Controllers\Admin\ParticipantController::class,
            "index",
        ])->name("index");

        Route::get("/ekspor", [
            \App\Http\Controllers\Admin\ParticipantController::class,
            "export",
        ])->name("export");
    });
