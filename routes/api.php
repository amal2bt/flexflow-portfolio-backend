<?php

/*
|--------------------------------------------------------------------------
| FlexFlow API routes
|--------------------------------------------------------------------------
| These routes are loaded by bootstrap/app.php and are automatically
| prefixed with /api. Public viewing endpoints (GET /site) and the contact
| form stay open so the marketing site keeps working; every admin/CRUD
| route requires a valid Bearer token (auth.apikey). Sensitive endpoints
| (login, contact) are rate limited; login uses an explicit fixed-window
| lockout in AuthController so Retry-After is always the full 10 minutes.
*/

use App\Http\Controllers\Api\AboutSectionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\ContactItemController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InvestFeatureController;
use App\Http\Controllers\Api\InvestSectionController;
use App\Http\Controllers\Api\InvestStatController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProcessSectionController;
use App\Http\Controllers\Api\ProcessStepController;
use App\Http\Controllers\Api\ServiceCardController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\TopSectionController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VisionPointController;
use App\Http\Controllers\Api\VisionSectionController;
use Illuminate\Support\Facades\Route;

// ---- Public site (viewing stays open for the portfolio frontend) ----
Route::get('/site', [SiteController::class, 'index']);
Route::post('/contact', [MessageController::class, 'store'])->middleware('throttle:contact');

// ---- Auth ----
Route::post('/login', [AuthController::class, 'login']);

// ---- Admin + CRUD (requires a valid Bearer token) ----
Route::middleware('auth.apikey')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/upload', [UploadController::class, 'store']);

    // ---- Collections (full CRUD) ----
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('services.cards', ServiceCardController::class)->shallow();
    Route::apiResource('challenges', ChallengeController::class);
    Route::apiResource('process-steps', ProcessStepController::class);
    Route::apiResource('invest-stats', InvestStatController::class);
    Route::apiResource('invest-features', InvestFeatureController::class);
    Route::apiResource('contact-items', ContactItemController::class);
    Route::apiResource('partners', PartnerController::class);
    Route::apiResource('messages', MessageController::class)->except(['store']);
    Route::apiResource('testimonials', TestimonialController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('vision-points', VisionPointController::class);

    // ---- Singleton content sections ----
    Route::get('/top-section', [TopSectionController::class, 'show']);
    Route::match(['put', 'post'], '/top-section', [TopSectionController::class, 'update']);

    Route::get('/about-section', [AboutSectionController::class, 'show']);
    Route::match(['put', 'post'], '/about-section', [AboutSectionController::class, 'update']);

    Route::get('/vision-section', [VisionSectionController::class, 'show']);
    Route::match(['put', 'post'], '/vision-section', [VisionSectionController::class, 'update']);

    Route::get('/process-section', [ProcessSectionController::class, 'show']);
    Route::match(['put', 'post'], '/process-section', [ProcessSectionController::class, 'update']);

    Route::get('/invest-section', [InvestSectionController::class, 'show']);
    Route::match(['put', 'post'], '/invest-section', [InvestSectionController::class, 'update']);
});
