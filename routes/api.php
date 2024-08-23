<?php

use App\Http\Controllers\API\JWTAuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ExtController;
use App\Http\Controllers\GirlController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\TaskResultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/get-week-range', function (Request $request) {
    $week = $request->input('week');
    $year = $request->input('year');

    $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
    $endDate = $startDate->copy()->endOfWeek();

    return response()->json([
        'start_date' => $startDate->format('d/m/Y'),
        'end_date' => $endDate->format('d/m/Y'),
    ]);
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [JWTAuthController::class, 'login']);
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::get('me', [JWTAuthController::class, 'me']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/group', [ExtController::class, 'loadGroup']);
    Route::post('/task-results', [TaskResultController::class, 'store']);
    Route::get('/girls', [ExtController::class, 'loadGirls']);
    Route::get('/platforms', [ExtController::class, 'loadPlatforms']);
});
