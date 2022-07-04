<?php

use App\Http\Controllers\Api\v1\Student\ApplicationsController;
use App\Http\Controllers\Api\v1\Student\FavoritesController;
use App\Http\Controllers\Api\v1\FavoritesController as FavController;
use App\Http\Controllers\Api\v1\Student\InternshipPostsController;
use App\Http\Controllers\Api\v1\Student\PasswordResetController;
use App\Http\Controllers\Api\v1\Student\StudentController;
use App\Http\Controllers\Api\v1\Student\MediaPostsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Student\AuthController;
use App\Http\Controllers\Api\v1\ForgotPasswordController;
use App\Http\Controllers\Api\v1\Student\ContactUsController;
use App\Http\Controllers\Api\v1\Student\FeedbacksController;
use App\Http\Controllers\Api\v1\Student\HomeController;

Route::post('/log-in', [StudentController::class, 'login']);
// Route::post('/logout', [StudentController::class, 'logout']);
Route::post('/password-reset', [PasswordResetController::class, 'resetStudentPassword'])->name('studentPasswordReset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('resetPassword');
Route::post('/forget-password', [ForgotPasswordController::class, 'studentForgot']);

Route::post('/signup-request-email', [StudentController::class, 'store']);
Route::post('/signup-request-token-check', [StudentController::class, 'storeTokenCheck']);
Route::post('/signup', [StudentController::class, 'signup']);
Route::get('show-sign-up-student/{id}', [StudentController::class, 'show']);
Route::apiResource('internship-post', InternshipPostsController::class);
Route::get('media-posts/recommendation', [MediaPostsController::class, 'recommendation']);
Route::apiResource('media-posts', MediaPostsController::class)->only(['index', 'show']);
Route::post('/email-request-token-check', [StudentController::class, 'storeTokenEmailChange']);
Route::get('/home', [HomeController::class, 'index']);

Route::group(['middleware' => 'auth:students'], function () {
    Route::post('/logout', [StudentController::class, 'logout']);
    Route::post('/send-email-change-request-token/{id}', [StudentController::class, 'sendTokenEmailChange']);
    Route::apiResource('students', StudentController::class)->except('index', 'store');
    Route::post('internship-favourite/{id}', FavoritesController::class);
    Route::post('student/change_password', [AuthController::class, 'changePassword'])->name('studentChangePassword');
    Route::post('basicInformation', [StudentController::class, 'update'])->name('updateInformation');
    Route::apiResource('favorite', FavController::class);
    Route::apiResource('applications', ApplicationsController::class)->only('store', 'index');
    Route::post('applications/update', [ApplicationsController::class, 'update']);
    Route::get('/feedback', [FeedbacksController::class, 'index']);
    Route::put('/feedback', [FeedbacksController::class, 'massUpdate']);
    Route::put('update-membership/{userId}', [StudentController::class, 'updateMembership']);
});

Route::post('contact', [ContactUsController::class, 'store']);
