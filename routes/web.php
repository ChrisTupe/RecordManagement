<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;

// Root route - always show login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Login routes (no auth required)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DocumentController::class, 'index'])->name('documents.index');

    // Profile routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile-image/{filename}', [\App\Http\Controllers\ProfileController::class, 'image'])->name('profile.image');

    // Documents list (Incoming documents)
    Route::get('/documents-list', [DocumentController::class, 'listDocuments'])->name('documents.list');

    // Document CRUD routes (excluding index)
    Route::resource('documents', DocumentController::class)->except(['index']);

    // Export document transactions to CSV
    Route::get('documents/{document}/export-transactions', [DocumentController::class, 'exportTransactions'])->name('documents.export');

    // Show document details
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');

    // Transaction routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export', [TransactionController::class, 'exportAll'])->name('transactions.export');
    Route::resource('documents.transactions', TransactionController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

    // Attachment routes (nested under documents)
    Route::post('documents/{document}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('documents/{document}/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::get('documents/{document}/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::get('documents/{document}/attachments/{attachment}/view', [AttachmentController::class, 'view'])->name('attachments.view');

    // Debug route (remove after testing)
    Route::get('/debug-image', function () {
        return view('debug-image');
    })->name('debug-image');

    // Admin routes (require admin role)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\UserManagementController::class, 'dashboard'])->name('dashboard');
        
        // User management routes
        Route::get('/users', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/bulk-update-role', [\App\Http\Controllers\UserManagementController::class, 'bulkUpdateRole'])->name('users.bulk-update-role');
        Route::get('/users/export', [\App\Http\Controllers\UserManagementController::class, 'export'])->name('users.export');

        // Department management routes
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });
});
