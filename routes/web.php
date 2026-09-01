<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Route khusus dokumen (sebelum resource agar tidak tergerus {document} wildcard).
    Route::get('documents/recent', [DocumentController::class, 'recent'])->name('documents.recent');
    Route::get('documents/archived', [DocumentController::class, 'archived'])->name('documents.archived');
    Route::get('documents/export', [DocumentController::class, 'export'])->name('documents.export');
    Route::put('documents/{document}/restore', [DocumentController::class, 'restore'])->name('documents.restore');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::post('documents/{document}/versions', [DocumentController::class, 'storeVersion'])->name('documents.versions.store');
    Route::delete('documents/{document}/versions/{version}', [DocumentController::class, 'destroyVersion'])->name('documents.versions.destroy');
    Route::get('documents/{document}/versions/{version}/download', [DocumentController::class, 'downloadVersion'])->name('documents.versions.download');

    Route::resource('documents', DocumentController::class);

    Route::post('favorites/toggle/{document}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::delete('favorites/{favorite}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('stages', StageController::class);
    Route::resource('document-types', DocumentTypeController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';