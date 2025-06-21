<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExcelUploadController;
use App\Http\Controllers\ReportHelperController;

Route::get('/leads', [LeadController::class, 'index']);
Route::post('/report', [ReportController::class, 'store']);
Route::get('/report', [ReportController::class, 'index']);
Route::delete('/report/{id}', [ReportController::class, 'destroy']);
Route::post('/upload-excel', [ExcelUploadController::class, 'upload']);

Route::get('/report-fields', [ReportHelperController::class, 'getAvailableFields']);
