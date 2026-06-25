<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// ── Root redirect ─────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Auth routes ───────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// ── Admin routes ──────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('students', [StudentManagementController::class, 'index'])->name('students.index');

    Route::get('scholarships/create', [ScholarshipController::class, 'create'])
        ->name('scholarships.create');
    Route::post('scholarships', [ScholarshipController::class, 'store'])
        ->name('scholarships.store');

    // Placeholder stubs (Week 2+)
    Route::get('scholarships',  fn () => view('admin.placeholder', ['page' => 'Scholarships']))->name('scholarships.index');
    Route::get('applications',  fn () => view('admin.placeholder', ['page' => 'Applications']))->name('applications.index');
    Route::get('allocations',   fn () => view('admin.placeholder', ['page' => 'Allocations']))->name('allocations.index');
    Route::get('reports',       fn () => view('admin.placeholder', ['page' => 'Reports']))->name('reports.index');
});

// ── Student routes ────────────────────────────────────────────
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

    // Placeholder stubs (Week 2+)
    Route::get('scholarships', fn () => view('student.placeholder', ['page' => 'Browse Scholarships']))->name('scholarships.index');
    Route::get('applications', fn () => view('student.placeholder', ['page' => 'My Applications']))->name('applications.index');
    Route::get('allocations',  fn () => view('student.placeholder', ['page' => 'My Allocations']))->name('allocations.index');
});
