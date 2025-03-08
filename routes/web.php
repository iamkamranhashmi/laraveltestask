<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Home Page - List of uploaded files
Route::get('/', [FileUploadController::class, 'index'])->name('files.index');
Route::get('/delete-expired-files', [FileUploadController::class, 'deleteExpiredFiles']);

// File Upload
Route::post('/upload', [FileUploadController::class, 'upload'])->name('files.upload');

// File Delete
Route::delete('/delete/{file}', [FileUploadController::class, 'delete'])->name('files.delete');