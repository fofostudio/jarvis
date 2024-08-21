<?php

use App\Http\Controllers\Admin\SessionLogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AutomatedTaskController;
use App\Http\Controllers\BreakController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GirlController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupOperatorController;
use App\Http\Controllers\OperativeReportController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkPlanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/points-dashboard', [PointController::class, 'dashboard'])->name('points.dashboard');
    // Automated Tasks
    Route::get('/automated-task/platform/{platform}', [AutomatedTaskController::class, 'platformTasks'])->name('automated_task.platform');

    // My Points
    Route::get('/my-points', [OperatorController::class, 'myPoints'])->name('my_points');

    // My Logins
    Route::get('/my-logins', [SessionLogController::class, 'myLogins'])->name('my_logins');
    // My Operative Reports
    Route::get('/my-operative-reports', [OperativeReportController::class, 'myReports'])->name('my_operative_reports');
    Route::post('/update-break', [BreakController::class, 'updateBreak']);
    Route::post('/start-break', [BreakController::class, 'startBreak']);
    Route::get('/break-status', [BreakController::class, 'getBreakStatus']);
    // My Work Plan
    Route::get('/group-points', [OperatorController::class, 'groupPoints']);
    Route::get('/my-work-plan', [WorkPlanController::class, 'myPlan'])->name('my_work_plan');
    Route::get('/automated-task/platform/{platform}', [AutomatedTaskController::class, 'showPlatformTasks'])
        ->name('automated_task.platform');

    // Settings

});

Route::middleware(['auth', 'admin.access'])->group(function () {
    // Rutas para Plataformas
    Route::resource('platforms', PlatformController::class);
    // Rutas para Chicas
    Route::resource('girls', GirlController::class);
    // Rutas para Grupos
    Route::resource('groups', GroupController::class);
    // Rutas para Usuarios
    Route::resource('users', UserController::class);
    Route::resource('group_operator', GroupOperatorController::class);
    // Rutas para Puntos
    Route::resource('points', PointController::class);
    Route::get('/admin/session-logs', [SessionLogController::class, 'index'])->name('admin.session_logs.index');
    Route::post('/points/groups', [PointController::class, 'groups'])->name('points.groups');
    Route::post('/points/preview', [PointController::class, 'preview'])->name('points.preview');

    // Ruta para asignar operadores a grupos
    Route::get('/assign-operators', [GroupController::class, 'assignOperatorsForm'])->name('groups.assign-operators-form');
    Route::post('/assign-operators', [GroupController::class, 'assignOperators'])->name('groups.assign-operators');
    // Ruta para generar reportes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});



require __DIR__ . '/auth.php';
