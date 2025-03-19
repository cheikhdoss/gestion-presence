<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\EmargementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Routes d'authentification
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestion des ressources
    Route::middleware(['role:admin'])->group(function () {
        Route::resources([
            'users' => UserController::class,
            'salles' => SalleController::class,
            'cours' => CoursController::class,
        ]);
    });

    // Routes des émargements
    Route::prefix('emargements')->name('emargements.')->group(function () {
        Route::get('/', [EmargementController::class, 'index'])->name('index');
        Route::get('/create', [EmargementController::class, 'create'])->name('create');
        Route::post('/', [EmargementController::class, 'store'])->name('store');
        Route::get('/{emargement}/edit', [EmargementController::class, 'edit'])->name('edit');
        Route::put('/{emargement}', [EmargementController::class, 'update'])->name('update');
        Route::delete('/{emargement}', [EmargementController::class, 'destroy'])->name('destroy');
        
        // Exports
        Route::get('/export-pdf', [EmargementController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [EmargementController::class, 'exportExcel'])->name('export-excel');
        
        // Statistiques
        Route::get('/statistiques', [EmargementController::class, 'statistiques'])->name('statistiques');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/preferences', [NotificationController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [NotificationController::class, 'updatePreferences'])->name('update-preferences');
    });

    // Export des cours
    Route::get('/cours/{cours}/export/{format}', [CoursController::class, 'exportEmargements'])
        ->name('cours.export')
        ->where('format', 'excel|pdf');
});
