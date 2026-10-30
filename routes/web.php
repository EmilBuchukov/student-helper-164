<?php

use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome');
})->name('home');

// Placeholder sections, built in later iterations.
foreach (['exams', 'questions', 'rooms', 'clubs'] as $section) {
    Route::get("/{$section}", fn () => Inertia::render('ComingSoon', ['section' => "nav.{$section}"]))
        ->name($section);
}

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/students/import', [StudentImportController::class, 'create'])->name('students.import');
    Route::post('/students/import', [StudentImportController::class, 'store'])->name('students.import.store');
});

require __DIR__.'/auth.php';
