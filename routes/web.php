<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectShortUrlController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\TeamMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');

    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');

    Route::get('/short-urls', [ShortUrlController::class, 'index'])->name('short-urls.index');
    Route::get('/short-urls/create', [ShortUrlController::class, 'create'])->name('short-urls.create');
    Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');
    Route::get('/short-urls/download', [ShortUrlController::class, 'download'])->name('short-urls.download');
    Route::get('/short-urls/view-all', [ShortUrlController::class, 'viewAll'])->name('short-urls.view-all');

    Route::get('/team-members', [TeamMemberController::class, 'index'])->name('team-members.index');
});

Route::get('/s/{shortCode}', RedirectShortUrlController::class)->name('short-urls.redirect');

Route::get('/invitations/accept/{token}', [InvitationController::class, 'acceptShow'])->name('invitations.accept')->middleware('guest');
Route::post('/invitations/accept', [InvitationController::class, 'acceptStore'])->name('invitations.accept.store')->middleware('guest');

require __DIR__.'/auth.php';
