<?php

use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Clubs\ClubController;
use App\Http\Controllers\Clubs\ClubMembershipController;
use App\Http\Controllers\Clubs\EventController;
use App\Http\Controllers\Exams\ExamController;
use App\Http\Controllers\Exams\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Questions\AnswerController;
use App\Http\Controllers\Questions\QuestionController;
use App\Http\Controllers\Questions\VoteController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome');
})->name('home');

// Room occupancy is intentionally not built yet.
Route::get('/rooms', fn () => Inertia::render('ComingSoon', ['section' => 'nav.rooms']))->name('rooms');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Exams & materials
    Route::resource('exams', ExamController::class);
    Route::post('/exams/{exam}/materials', [MaterialController::class, 'store'])
        ->middleware('throttle:uploads')->name('exams.materials.store');
    Route::get('/materials/{material}/download', [MaterialController::class, 'download'])->name('materials.download');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

    // Clubs & events (teachers: read-only, enforced by ClubPolicy/EventPolicy)
    Route::resource('clubs', ClubController::class);
    Route::post('/clubs/{club}/membership', [ClubMembershipController::class, 'store'])->name('clubs.join');
    Route::delete('/clubs/{club}/membership', [ClubMembershipController::class, 'destroy'])->name('clubs.leave');
    Route::resource('events', EventController::class);
    Route::post('/events/{event}/rsvp', [EventController::class, 'rsvp'])->name('events.rsvp');
    Route::delete('/events/{event}/rsvp', [EventController::class, 'cancelRsvp'])->name('events.rsvp.cancel');

    // Q&A and reports: student spaces, closed to teachers.
    Route::middleware('no-teachers')->group(function () {
        Route::post('/questions', [QuestionController::class, 'store'])->middleware('throttle:questions')->name('questions.store');
        Route::resource('questions', QuestionController::class)->except('store');
        Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])
            ->middleware('throttle:answers')->name('questions.answers.store');
        Route::post('/questions/{question}/vote', [VoteController::class, 'question'])->middleware('throttle:votes')->name('questions.vote');
        Route::patch('/answers/{answer}', [AnswerController::class, 'update'])->name('answers.update');
        Route::delete('/answers/{answer}', [AnswerController::class, 'destroy'])->name('answers.destroy');
        Route::post('/answers/{answer}/accept', [AnswerController::class, 'accept'])->name('answers.accept');
        Route::post('/answers/{answer}/vote', [VoteController::class, 'answer'])->middleware('throttle:votes')->name('answers.vote');
        Route::post('/reports', [ReportController::class, 'store'])->middleware('throttle:reports')->name('reports.store');
    });
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/students/import', [StudentImportController::class, 'create'])->name('students.import');
    Route::post('/students/import', [StudentImportController::class, 'store'])->name('students.import.store');
});

require __DIR__.'/auth.php';
