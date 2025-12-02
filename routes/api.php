<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('employees')->name('employees.')->controller(EmployeeController::class)->group(function () {
        Route::get("", 'index')->name('index');
    });

    Route::prefix('leave-requests')->name('leave-requests.')->controller(LeaveRequestController::class)->group(function (){
       Route::post('/','store')->name('store') ;
       Route::post('/{leave}/approve','approve')->name('approve') ;
       Route::post('/{leave}/reject','reject')->name('reject') ;
    });
});
