<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\SaveController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\ApplicationController;

Route::get('/test', [TestController::class, 'test']);

// admin routes
Route::prefix('admin')->group(function(){
    Route::post('/register', [AdminController::class, 'register']);
    Route::post('/login', [AdminController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout']);
    });

    Route::get('/getDashboardData', [AdminController::class, 'getDashboardData']);

    Route::prefix('company')->group(function(){
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/create', [CompanyController::class, 'create']);
        Route::get('/getCompany/{id}', [CompanyController::class, 'getCompany']);
        Route::put('/updateCompany/{id}', [CompanyController::class, 'update']);
        Route::delete('/deleteCompany/{company}', [CompanyController::class, 'delete']);
        Route::get('/search', [CompanyController::class, 'search']);
    });

    Route::prefix('job')->group(function() {
        Route::get('/', [JobController::class, 'index']);
        Route::get('/search', [JobController::class, 'search']);
        Route::get('/getCompanyName', [JobController::class, 'getCompanyName']);
        Route::post('/create', [JobController::class, 'create']);
        Route::put('/updateJob/{id}', [JobController::class, 'update']);
        Route::delete('/deleteJob/{job}', [JobController::class, 'delete']);
        Route::get('/getJob/{id}', [JobController::class, 'getJob']);
    });

    Route::prefix('application')->group(function(){
        Route::get('/', [ApplicationController::class, 'index']);
        Route::post('/filter', [ApplicationController::class, 'filter']);
        Route::post('/{id}/updateStatus', [ApplicationController::class, 'updateStatus']);
        Route::post('/{id}/updateSchedule', [ApplicationController::class, 'updateSchedule']);
        Route::post('/{id}/confirmSchedule', [ApplicationController::class, 'confirmSchedule']);
        Route::post('/{id}/reject', [ApplicationController::class, 'reject']);
        Route::get('/getConfirmedInterview', [ApplicationController::class, 'getConfirmedInterview']);
    });
});

//user routes
Route::prefix('user')->group(function() {
    Route::post('/register', [ApplicantController::class, 'register']);
    Route::post('/login', [ApplicantController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ApplicantController::class, 'logout']);

        Route::get('/getProfile', [ApplicantController::class, 'getProfile']);
        Route::post('/updateProfile', [ApplicantController::class, 'updateProfile']);
        
        Route::post('/applyJob', [ApplyController::class, 'applyJob']);
        Route::get('/getAppliedJob', [ApplyController::class, 'getAppliedJob']);

        Route::post('/{id}/saveJob', [SaveController::class, 'saveJob']);
        Route::post('/{id}/unsaveJob', [SaveController::class, 'unsaveJob']);

        Route::post('/application/{id}/selectSlot', [ApplicantController::class, 'selectSlot']);
        Route::post('/application/{id}/suggestSlot', [ApplicantController::class, 'suggestSlot']); 
    });

    Route::get('/getAvailableJobs', [ApplicantController::class, 'getAvailableJobs']);
    
    Route::get('/searchJob', [ApplicantController::class, 'searchJob']);
    
    Route::get('/getJob/{id}', [ApplicantController::class, 'getJob']);

    Route::get('/getSavedJob', [SaveController::class, 'getSavedJob']);
    
    Route::get('/getSpecialization', [ApplicantController::class, 'getSpecialization']);
});



// Route::get('/test', [AdminController::class, 'test']);
