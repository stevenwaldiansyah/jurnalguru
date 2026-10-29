<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\JournalSearchController;
use App\Http\Controllers\RecapController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/catatan/cari', [JournalSearchController::class, 'index'])->name('journals.search');
    Route::get('/rekap', [RecapController::class, 'index'])->name('recap.index');
    Route::get('/rekap/pdf', [RecapController::class, 'pdf'])->name('recap.pdf');

    Route::get('/jurnal/{journal}', [JournalController::class, 'show'])->name('journals.show');

    Route::middleware('role:guru')->group(function () {
        Route::get('/jadwal/{schedule}/jurnal/buat', [JournalController::class, 'create'])->name('journals.create');
        Route::post('/jadwal/{schedule}/jurnal', [JournalController::class, 'store'])->name('journals.store');
        Route::get('/jurnal/{journal}/ubah', [JournalController::class, 'edit'])->name('journals.edit');
        Route::put('/jurnal/{journal}', [JournalController::class, 'update'])->name('journals.update');
        Route::delete('/jurnal/{journal}', [JournalController::class, 'destroy'])->name('journals.destroy');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('classes', SchoolClassController::class)->except(['show'])->parameters(['classes' => 'schoolClass']);
        Route::resource('students', StudentController::class)->except(['show']);
        Route::resource('subjects', SubjectController::class)->except(['show']);
        Route::resource('semesters', SemesterController::class)->except(['show']);
        Route::resource('schedules', ScheduleController::class)->except(['show']);
        Route::get('backup', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backup', [BackupController::class, 'store'])->name('backups.store');
        Route::post('backup/restore', [BackupController::class, 'restore'])->name('backups.restore');
    });
});
