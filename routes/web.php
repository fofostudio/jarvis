<?php

use App\Http\Controllers\Admin\AdminOperativeReportController;
use App\Http\Controllers\Admin\SessionLogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuditController;
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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkPlanController;


Route::redirect('/', '/login');

require __DIR__ . '/auth.php';


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/points-dashboard', [PointController::class, 'dashboard'])->name('points.dashboard');
    Route::get('/automated-task/platform/{platform}', [AutomatedTaskController::class, 'platformTasks'])->name('automated_task.platform');
    Route::get('/my-points', [OperatorController::class, 'myPoints'])->name('my_points');
    Route::get('/my-logins', [SessionLogController::class, 'myLogins'])->name('my_logins');
    Route::post('/update-break', [BreakController::class, 'updateBreak']);
    Route::post('/start-break', [BreakController::class, 'startBreak']);
    Route::post('/end-break', [BreakController::class, 'endBreak']);
    Route::get('/break-status', [BreakController::class, 'getBreakStatus']);
    Route::get('/group-points', [OperatorController::class, 'groupPoints']);
    Route::get('/my-config', [OperatorController::class, 'mySetings'])->name('my-settings');
    Route::get('/my-operative-reports', [OperativeReportController::class, 'myReports'])->name('my-operative-reports');
    Route::group(['prefix' => 'operative-reports', 'as' => 'operative-reports.'], function () {
        // GET /operative-reports (index)
        Route::post('/', [OperativeReportController::class, 'store'])->name('store');
        // GET /operative-reports/create (create)
        Route::get('/create', [OperativeReportController::class, 'create'])->name('create');


        // GET /operative-reports/{operative_report}/edit (edit)
        Route::get('/{operative_report}/edit', [OperativeReportController::class, 'edit'])->name('edit');

        // PUT/PATCH /operative-reports/{operative_report} (update)
        Route::match(['put', 'patch'], '/{operative_report}', [OperativeReportController::class, 'update'])->name('update');

        // DELETE /operative-reports/{operative_report} (destroy)
        Route::delete('/{operative_report}', [OperativeReportController::class, 'destroy'])->name('destroy');
    });    Route::post('/operative-reports/{operativeReport}/review', [OperativeReportController::class, 'reviewReport'])->name('operative-reports.review');
    Route::get('/operative-reports/{operativeReport}/download', [OperativeReportController::class, 'downloadFile'])->name('operative-reports.download');
    Route::get('/my-work-plan', [WorkPlanController::class, 'myPlan'])->name('my_work_plan');
    Route::get('/automated-task/platform/{platform}', [AutomatedTaskController::class, 'showPlatformTasks'])
        ->name('automated_task.platform');
    Route::resource('products', ProductController::class);
    Route::resource('sales', SaleController::class);
});

Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::resource('platforms', PlatformController::class);
    Route::resource('girls', GirlController::class);
    Route::resource('groups', GroupController::class);
    Route::resource('users', UserController::class);
    Route::get('users-admin', [UserController::class, 'indexadmin'])->name('admin.users');
    Route::resource('group_operator', GroupOperatorController::class);
    Route::resource('points', PointController::class);
    Route::get('/admin-operative-reports', [AdminOperativeReportController::class, 'index'])->name('operative-reports.index');
    Route::get('/operative-reports/{report}', [AdminOperativeReportController::class, 'show'])->name('operative-reports.show');
    Route::patch('/operative-reports/{report}/update-status', [AdminOperativeReportController::class, 'updateStatus'])->name('operative-reports.update-status');
    Route::delete('/operative-reports/{report}', [AdminOperativeReportController::class, 'destroy'])->name('operative-reports.destroy');
    Route::get('/dashboard/monthly-totals', [DashboardController::class, 'getMonthlyTotals'])->name('dashboard.monthly-totals');
    Route::get('/admin/session-logs', [SessionLogController::class, 'index'])->name('admin.session_logs.index');
    Route::post('/points/groups', [PointController::class, 'groups'])->name('points.groups');
    Route::post('/points/preview', [PointController::class, 'preview'])->name('points.preview');

    Route::get('/work-plans', [WorkPlanController::class, 'index'])->name('work_plans.index');
    Route::post('/work-plans/generate', [WorkPlanController::class, 'generate'])->name('work_plans.generate');
    Route::put('/work-plans/{workPlan}', [WorkPlanController::class, 'update'])->name('work_plans.update');
    Route::resource('audits', AuditController::class);
    Route::get('/digital', [PointController::class, 'index'])->name('digital.index');
    Route::get('/footitems', [PointController::class, 'index'])->name('fooditems.index');
    Route::get('/extension', [PointController::class, 'index'])->name('extension_chrome.index');
    Route::get('/task', [PointController::class, 'index'])->name('automatized_task.index');
    Route::get('/permissions', [PointController::class, 'index'])->name('permissions_and_roles.index');
    Route::get('/jarvis-settings', [PointController::class, 'index'])->name('settings_jarvis.index');
    Route::get('/safood', [PointController::class, 'index'])->name('SAfoodProducts.index');

    Route::get('/assign-operators', [GroupController::class, 'assignOperatorsForm'])->name('groups.assign-operators-form');
    Route::post('/assign-operators', [GroupController::class, 'assignOperators'])->name('groups.assign-operators');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
