<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
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
    // Placeholder statis untuk modul yang dikembangkan pada fase berikutnya.
    // Didefinisikan sebelum resource agar tidak tergerus oleh {document} wildcard.
    Route::get('documents/recent', fn () => app(PageController::class)->placeholder('recent'))->name('documents.recent');
    Route::get('documents/archived', fn () => app(PageController::class)->placeholder('archived'))->name('documents.archived');

    Route::resource('documents', DocumentController::class);
});

/*
 * Placeholder routes untuk modul yang akan dikembangkan pada fase berikutnya.
 * Menjaga navigasi sidebar tetap berfungsi pada fase foundation.
 */
$placeholderModules = [
    'favorites' => 'favorites.index',
    'categories' => 'categories.index',
    'stages' => 'stages.index',
    'document-types' => 'document-types.index',
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
