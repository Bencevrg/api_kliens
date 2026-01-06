<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\WriterController;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('writers.index');
});

// LOGIN (Bejelentkezés)
// A 'guest' middleware biztosítja, hogy ha már be vagyunk lépve, ne lássuk a login oldalt.
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// LOGOUT (Kijelentkezés)
// FONTOS: Kivettük az 'auth' middleware alól, mert session-alapú a rendszerünk!
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


/**
 * REST API Kliens Route-ok
 */

// 1. Publikus listázások és Export
Route::get('/writers', [WriterController::class, 'index'])->name('writers.index');
Route::get('/writers/export/{type}', [WriterController::class, 'export'])->name('writers.export');

// 2. Könyvek listázása (Publikus)
Route::get('/writers/{writer}/books', [BookController::class, 'index'])->name('writers.books.index');




Route::group([], function () {
    
    // Írók CRUD
    Route::get('/writers/create', [WriterController::class, 'create'])->name('writers.create');
    Route::post('/writers', [WriterController::class, 'store'])->name('writers.store');
    Route::get('/writers/{writer}/edit', [WriterController::class, 'edit'])->name('writers.edit');
    Route::patch('/writers/{writer}', [WriterController::class, 'update'])->name('writers.update');
    Route::delete('/writers/{writer}', [WriterController::class, 'destroy'])->name('writers.destroy');

    // Könyvek CRUD (Nested Resource)
    // Create
    Route::get('/writers/{writer}/books/create', [BookController::class, 'create'])->name('writers.books.create');
    Route::post('/writers/{writer}/books', [BookController::class, 'store'])->name('writers.books.store');
    
    // Edit/Update/Delete
    Route::get('/writers/{writer}/books/{book}/edit', [BookController::class, 'edit'])->name('writers.books.edit');
    Route::patch('/writers/{writer}/books/{book}', [BookController::class, 'update'])->name('writers.books.update');
    Route::delete('/writers/{writer}/books/{book}', [BookController::class, 'destroy'])->name('writers.books.destroy');
});

