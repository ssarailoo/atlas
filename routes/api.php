<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('employees')->name('employees.')->controller(EmployeeController::class)->group(function () {
        Route::get("", 'index')->name('index');
    });
});
