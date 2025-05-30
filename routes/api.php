<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LembagaController;
use App\Http\Controllers\ProgramKursusController;
use App\Http\Controllers\ReviewLembagaController;
use App\Http\Controllers\BookmarkLembagaController;

// Autentikasi (Tidak ada perubahan di sini)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute yang memerlukan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // Profil Pengguna
    Route::get('/user', [AuthController::class, 'user']);
    
    // Tambahan: Rute untuk memperbarui profil pengguna
    Route::put('/user', [AuthController::class, 'update']);

    // CRUD Lembaga
    Route::prefix('lembagas')->group(function () {
        Route::get('/', [LembagaController::class, 'getLembagas']);
        // Pindahkan rute /filter ke atas agar lebih spesifik
        Route::get('/filter', [LembagaController::class, 'filterLembagas']);
        Route::post('/filter', [LembagaController::class, 'filterLembagas']);
        Route::get('/recommended', [LembagaController::class, 'getRecommendedLembagas']);
        // Rute wildcard seperti {id} harus di bawah
        Route::get('/{id}', [LembagaController::class, 'getLembagaDetail']);
        Route::post('/', [LembagaController::class, 'store']);
        Route::put('/{id}', [LembagaController::class, 'update']);
        Route::delete('/{id}', [LembagaController::class, 'destroy']);
    });

    // CRUD Program Kursus
    Route::prefix('program-kursuses')->group(function () {
        Route::get('/', [ProgramKursusController::class, 'index']); // Ambil semua data
        Route::get('/{id}', [ProgramKursusController::class, 'show']); // Ambil satu data
        Route::post('/', [ProgramKursusController::class, 'store']); // Buat data baru
        Route::put('/{id}', [ProgramKursusController::class, 'update']); // Perbarui data
        Route::delete('/{id}', [ProgramKursusController::class, 'destroy']); // Hapus data
        Route::get('/lembaga/{lembaga_id}', [ProgramKursusController::class, 'getByLembaga']); // Ambil berdasarkan lembaga
    });

    // CRUD Review Lembaga
    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewLembagaController::class, 'index']); // Ambil semua data
        // Tambahan: Rute untuk mengambil ulasan berdasarkan lembaga_id
        Route::get('/', [ReviewLembagaController::class, 'indexByLembaga']);
        Route::get('/{id}', [ReviewLembagaController::class, 'show']); // Ambil satu data
        Route::post('/', [ReviewLembagaController::class, 'storeReview']); // Buat data baru
        Route::put('/{id}', [ReviewLembagaController::class, 'update']); // Perbarui data
        Route::delete('/{id}', [ReviewLembagaController::class, 'destroy']); // Hapus data
    });

    // CRUD Bookmark Lembaga
    Route::prefix('bookmarks')->group(function () {
        Route::get('/', [BookmarkLembagaController::class, 'getBookmarks']); // Ambil semua data
        Route::get('/{id}', [BookmarkLembagaController::class, 'show']); // Ambil satu data
        Route::post('/', [BookmarkLembagaController::class, 'bookmarkLembaga']); // Buat data baru
        Route::delete('/{id}', [BookmarkLembagaController::class, 'deleteBookmark']); // Hapus data
        // Tambahan: Rute untuk memeriksa status bookmark
        Route::get('/check/{lembagaId}', [BookmarkLembagaController::class, 'check']);
    });

    // Menambahkan route untuk getAllUsers
    Route::get('/users', [AuthController::class, 'getAllUsers']);
    Route::get('/bookmarks', [BookmarkLembagaController::class, 'getBookmarks']);
});