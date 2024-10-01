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
use App\Http\Controllers\ChatController;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\TeamLiderController;
use App\Http\Controllers\Api\TaskController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/team-lider/tasks', [TeamLiderController::class, 'getTasks']);
    Route::post('/team-lider/tasks', [TeamLiderController::class, 'store']);
    Route::put('/team-lider/tasks/{task}', [TeamLiderController::class, 'update']);
    Route::delete('/team-lider/tasks/{task}', [TeamLiderController::class, 'destroy']);
});
// Rutas públicas
Route::get('/user-info', [JWTAuthController::class, 'getUserInfo']);
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

Route::post('/refresh-token', function (Request $request) {
    try {
        // Refrescar el token actual
        $newToken = JWTAuth::parseToken()->refresh();

        return response()->json([
            'success' => true,
            'token' => $newToken,
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'No se pudo refrescar el token'
        ], 500);
    }
});
// Rutas de autenticación (ahora públicas)
Route::post('auth/login', [AuthenticatedSessionController::class, 'apiLogin']);
Route::post('auth/logout', [AuthenticatedSessionController::class, 'apiLogout']);
Route::post('auth/refresh', [AuthenticatedSessionController::class, 'refresh']);
Route::get('auth/me', [AuthenticatedSessionController::class, 'me']);
Route::get('auth/validate-token', [AuthenticatedSessionController::class, 'validateToken']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [ChatController::class, 'getUsers']);
    Route::post('/conversations', [ChatController::class, 'getOrCreateConversation']);
    Route::get('/conversations/{conversationId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/conversations/{conversationId}/messages', [ChatController::class, 'sendMessage']);
});
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/group', [ExtController::class, 'loadGroup']);
    Route::get('/girls', [ExtController::class, 'loadGirls']);
    Route::get('/platforms', [ExtController::class, 'loadPlatforms']);
    Route::post('/execute-task', [TaskResultController::class, 'executeTask']);
    Route::post('/task-results', [TaskResultController::class, 'store']);
});

// Ruta para invalidar token
Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->middleware('auth:api');
