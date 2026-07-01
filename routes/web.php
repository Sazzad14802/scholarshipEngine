<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\ApplicationController as StudentAppController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ScholarshipController as StudentScholarshipController;
use Illuminate\Support\Facades\Route;

// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Auth routes ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ── Admin routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Student directory
    Route::get('students', [StudentManagementController::class, 'index'])->name('students.index');

    // Scholarships
    Route::get('scholarships',              [ScholarshipController::class, 'index'])  ->name('scholarships.index');
    Route::get('scholarships/create',       [ScholarshipController::class, 'create']) ->name('scholarships.create');
    Route::post('scholarships',             [ScholarshipController::class, 'store'])  ->name('scholarships.store');
    Route::get('scholarships/{id}',         [ScholarshipController::class, 'show'])   ->name('scholarships.show');
    Route::get('scholarships/{id}/edit',    [ScholarshipController::class, 'edit'])   ->name('scholarships.edit');
    Route::put('scholarships/{id}',         [ScholarshipController::class, 'update']) ->name('scholarships.update');
    Route::delete('scholarships/{id}',      [ScholarshipController::class, 'destroy'])->name('scholarships.destroy');
});

// ── Student routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {

    // Dashboard
    Route::get('dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile',       [ProfileController::class, 'show'])  ->name('profile.show');

    // Browse scholarships & apply
    Route::get('scholarships',                   [StudentScholarshipController::class, 'index'])->name('scholarships.index');
    Route::post('scholarships/{id}/apply',       [StudentScholarshipController::class, 'apply'])->name('scholarships.apply');
});
