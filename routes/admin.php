<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\ApplicationsController;
use App\Http\Controllers\Api\v1\CompanyController;
use App\Http\Controllers\Api\v1\StudentController;
use App\Http\Controllers\Api\v1\DashboardController;
use App\Http\Controllers\Api\v1\FavoritesController;
use App\Http\Controllers\Api\v1\FeedbacksController;
use App\Http\Controllers\Api\v1\MediaTagsController;
use App\Http\Controllers\Api\v1\DataExportController;
use App\Http\Controllers\Api\v1\IndustriesController;
use App\Http\Controllers\Api\v1\MediaPostsController;
use App\Http\Controllers\Api\v1\PasswordResetController;
use App\Http\Controllers\Api\v1\WorkCategoriesController;
use App\Http\Controllers\Api\v1\InternshipPostsController;
use App\Http\Controllers\Api\v1\InternshipFeatureController;
use App\Http\Controllers\Api\v1\EducationFacilitiesController;
use App\Http\Controllers\Api\v1\ForgotPasswordController;
use App\Http\Controllers\MasterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/log-in', [AdminController::class, 'login']);

Route::get('/master', [MasterController::class, 'index']);
Route::apiResource('media-posts', MediaPostsController::class);
Route::post('/password-reset', [PasswordResetController::class, 'resetAdminPassword'])->name('adminPasswordReset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('resetPassword');
Route::apiResource('education-facility', EducationFacilitiesController::class);

Route::group(['middleware' => 'auth:admins'], function() {
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::get('/admin/dashboard/{id}', DashboardController::class);
    Route::post('/admin/create', [AdminController::class, 'store']);
    Route::apiResource('work-category', WorkCategoriesController::class);
    Route::apiResource('internship-feature', InternshipFeatureController::class);
    Route::post('/email-check', [AdminController::class, 'emailCheck']);
    Route::apiResource('admin', AdminController::class);
    Route::apiResource('media-post', MediaPostsController::class);
    Route::apiResource('media-tag', MediaTagsController::class);
    Route::apiResource('internship-post', InternshipPostsController::class);
    /**
     * TODO:
     * Internship-post update(PUT method) not receiving the request kyes.
     * Need to resolve and identify the issue of
     */
    Route::post('internship-post-update/{id}', [InternshipPostsController::class, 'update']);
    Route::post('media-post-update/{id}', [MediaPostsController::class, 'update']);

    Route::apiResource('company', CompanyController::class);
    Route::apiResource('favorite', FavoritesController::class);
    Route::apiResource('student', StudentController::class);
    Route::apiResource('feedback', FeedbacksController::class);
    Route::apiResource('business_industry', IndustriesController::class);
    Route::get('applications/admin-read', [ApplicationsController::class, 'updateAdminRead']);
    Route::apiResource('applications', ApplicationsController::class);
    Route::get('/student/applications/{id}', [ApplicationsController::class, 'studentApplications']);
    Route::get('/export/application', [DataExportController::class, 'exportApplication']);
    Route::get('/export/company', [DataExportController::class, 'exportCompany']);
    Route::get('/export/student', [DataExportController::class, 'exportStudent']);
    Route::get('/student-feedback/{student}', [StudentController::class, 'studentFeedback']);

});


// Forget and reset password for admin user using email
Route::post('/forget-password', [ForgotPasswordController::class, 'adminForgot']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('resetPassword');
