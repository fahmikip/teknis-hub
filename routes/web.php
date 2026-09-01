<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Route khusus dokumen (sebelum resource agar tidak tergerus {document} wildcard).
    Route::get('documents/recent', [DocumentController::class, 'recent'])->name('documents.recent');
    Route::get('documents/archived', [DocumentController::class, 'archived'])->name('documents.archived');
    Route::get('documents/export', [DocumentController::class, 'export'])->name('documents.export');
    Route::put('documents/{document}/restore', [DocumentController::class, 'restore'])->name('documents.restore');

    Route::resource('documents', DocumentController::class);
});

/*
 * Placeholder routes untuk modul yang akan dikembangkan pada fase berikutnya.
 * Menjaga navigasi sidebar tetap berfungsi pada fase foundation.
 */
$placeholderModules = [
    'favorites' => 'favorites.index',
    'audit-logs' => 'audit-logs.index',
    'users' => 'users.index',
    'roles' => 'roles.index',
    'settings' => 'settings.index',
];

Route::middleware('auth')->group(function () use ($placeholderModules) {
    foreach ($placeholderModules as $uri => $name) {
        [$module] = explode('/', $uri);
        Route::get($uri, fn () => app(PageController::class)->placeholder($module))
            ->name($name);
    }

    Route::resource('categories', CategoryController::class);
    Route::resource('stages', StageController::class);
    Route::resource('document-types', DocumentTypeController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
