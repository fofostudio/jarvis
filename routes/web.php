<?php

use App\Http\Controllers\Admin\AdminOperativeReportController;
use App\Http\Controllers\Admin\SessionLogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AutomatedTaskController;
use App\Http\Controllers\BreakController;
use App\Http\Controllers\CategoryLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\FoodAdminController;
use App\Http\Controllers\GestionBreaksController;
use App\Http\Controllers\GirlController;
use App\Http\Controllers\GroupCategoryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupOperatorController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\OperativeReportController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ScheduleCalendarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkPlanController;


Route::redirect('/', '/login');

require __DIR__ . '/auth.php';


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/admin/get-session-log-time/{userId}/{date}', [SessionLogController::class, 'getSessionLogTime'])->name('admin.get-session-log-time');
    // Nueva ruta para obtener datos de un registro de sesión específico
    Route::get('/admin/get-session-log-data/{userId}/{date}', [SessionLogController::class, 'getSessionLogData'])
        ->name('admin.get-session-log-data');

    // Nueva ruta para actualizar el estado de asistencia
    Route::post('/admin/update-attendance-status', [SessionLogController::class, 'updateAttendanceStatus'])
        ->name('admin.update-attendance-status');

    Route::post('/admin/close-operator-session', [SessionLogController::class, 'closeOperatorSession'])->name('admin.close-operator-session');
    Route::get('/gestion-breaks', [GestionBreaksController::class, 'index'])
        ->name('admin.gestion-breaks');

    // Ruta para obtener los datos de la tabla
    Route::get('/gestion-breaks/datos-tabla', [GestionBreaksController::class, 'getDatosTabla'])
        ->name('admin.gestion-breaks.datos-tabla');

    // Nueva ruta para obtener los datos del dashboard
    Route::get('/gestion-breaks/datos-dashboard', [GestionBreaksController::class, 'getDatosDashboard'])
        ->name('admin.gestion-breaks.datos-dashboard');

    // Rutas para iniciar y finalizar breaks
    Route::post('/gestion-breaks/iniciar-break/{userId}', [GestionBreaksController::class, 'iniciarBreak'])
        ->name('admin.gestion-breaks.iniciar-break');
    Route::post('/gestion-breaks/finalizar-break/{userId}', [GestionBreaksController::class, 'finalizarBreak'])
        ->name('admin.gestion-breaks.finalizar-break');

    // Rutas para obtener estadísticas y datos adicionales
    Route::get('/gestion-breaks/estadisticas', [GestionBreaksController::class, 'obtenerEstadisticasBreaks'])
        ->name('admin.gestion-breaks.estadisticas');
    Route::get('/gestion-breaks/breaks-por-dia', [GestionBreaksController::class, 'obtenerBreaksPorDia'])
        ->name('admin.gestion-breaks.breaks-por-dia');
    Route::get('/gestion-breaks/tiempo-extra-por-operador', [GestionBreaksController::class, 'obtenerTiempoExtraPorOperador'])
        ->name('admin.gestion-breaks.tiempo-extra-por-operador');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/admin/update-attendance-status', [SessionLogController::class, 'updateAttendanceStatus'])->name('admin.update-attendance-status');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/points-dashboard', [PointController::class, 'dashboard'])->name('points.dashboard');
    Route::get('/automated-task/platform/{platform}', [AutomatedTaskController::class, 'platformTasks'])->name('automated_task.platform');
    Route::get('/my-points', [OperatorController::class, 'myPoints'])->name('my_points');
    Route::get('/my-logins', [SessionLogController::class, 'myLogins'])->name('my_logins');
    Route::post('/update-break', [BreakController::class, 'updateBreak']);
    Route::post('/start-break', [BreakController::class, 'startBreak']);
    Route::get('/schedule-calendar', [ScheduleCalendarController::class, 'index'])->name('schedule-calendar.index');
    Route::post('/schedule-calendar/update-day', [ScheduleCalendarController::class, 'updateDay'])->name('schedule-calendar.update-day');
    Route::post('/end-break', [BreakController::class, 'endBreak']);
    Route::get('/break-status', [BreakController::class, 'getBreakStatus']);
    Route::get('/group-points', [OperatorController::class, 'groupPoints']);
    Route::get('/my-config', [OperatorController::class, 'mySetings'])->name('my-settings');
    Route::get('/my-operative-reports', [OperativeReportController::class, 'myReports'])->name('my-operative-reports');
    Route::prefix('foodAdmin')->group(function () {
        Route::get('/', [FoodAdminController::class, 'index'])->name('foodAdmin.index');
        Route::get('/create', [FoodAdminController::class, 'create'])->name('foodAdmin.create');
        Route::post('/', [FoodAdminController::class, 'store'])->name('foodAdmin.store');
        Route::get('/{product}/edit', [FoodAdminController::class, 'edit'])->name('foodAdmin.edit');
        Route::put('/{product}', [FoodAdminController::class, 'update'])->name('foodAdmin.update');
        Route::delete('/{product}', [FoodAdminController::class, 'destroy'])->name('foodAdmin.destroy');

        Route::get('/sales', [FoodAdminController::class, 'showSales'])->name('foodAdmin.sales');
        Route::get('/sales/create', [FoodAdminController::class, 'createSale'])->name('foodAdmin.createSale');
        Route::post('/sales', [FoodAdminController::class, 'storeSale'])->name('foodAdmin.storeSale');

        Route::get('/payments', [FoodAdminController::class, 'showPayments'])->name('foodAdmin.payments');
        Route::get('/payments/create', [FoodAdminController::class, 'createPayment'])->name('foodAdmin.createPayment');
        Route::post('/payments', [FoodAdminController::class, 'storePayment'])->name('foodAdmin.storePayment');

        Route::get('/sales-report', [FoodAdminController::class, 'showSalesReport'])->name('foodAdmin.salesReport');
    });
    Route::get('/operator/my-shopitems', [FoodAdminController::class, 'myShopItems'])->name('operator.myShopItems');
    Route::get('/foodAdmin/categories', [FoodAdminController::class, 'categoryIndex'])->name('foodAdmin.categories.index');
    Route::get('/foodAdmin/categories/create', [FoodAdminController::class, 'categoryCreate'])->name('foodAdmin.categories.create');
    Route::post('/foodAdmin/categories', [FoodAdminController::class, 'categoryStore'])->name('foodAdmin.categories.store');
    Route::get('/foodAdmin/categories/{category}/edit', [FoodAdminController::class, 'categoryEdit'])->name('foodAdmin.categories.edit');
    Route::put('/foodAdmin/categories/{category}', [FoodAdminController::class, 'categoryUpdate'])->name('foodAdmin.categories.update');
    Route::delete('/foodAdmin/categories/{category}', [FoodAdminController::class, 'categoryDestroy'])->name('foodAdmin.categories.destroy');
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
    });
    Route::post('/operative-reports/{operativeReport}/review', [OperativeReportController::class, 'reviewReport'])->name('operative-reports.review');
    Route::get('/operative-reports/{operativeReport}/download', [OperativeReportController::class, 'downloadFile'])->name('operative-reports.download');
    Route::get('/my-work-plan', [WorkPlanController::class, 'myPlan'])->name('my_work_plan');
    Route::get('/automated-task/platform/{platform}', [AutomatedTaskController::class, 'showPlatformTasks'])
        ->name('automated_task.platform');
    Route::resource('products', ProductController::class);
    Route::resource('foodAdmin', FoodAdminController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('category_links', CategoryLinkController::class);
    Route::resource('links', LinkController::class);
    Route::get('/dictionary', [DictionaryController::class, 'index'])->name('dictionary.index');
});

Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::resource('platforms', PlatformController::class);
    Route::resource('girls', GirlController::class);
    Route::get('/girls/search', [GirlController::class, 'search'])->name('girls.search');
    Route::resource('groups', GroupController::class);
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('users-admin', [UserController::class, 'indexadmin'])->name('admin.users');
    Route::get('users-admin/create', [UserController::class, 'createadmin'])->name('admin.users.create');
    Route::post('users-admin', [UserController::class, 'storeadmin'])->name('admin.users.store');
    Route::get('users-admin/{user}', [UserController::class, 'showadmin'])->name('admin.users.show');
    Route::get('users-admin/{user}/edit', [UserController::class, 'editadmin'])->name('admin.users.edit');
    Route::put('users-admin/{user}', [UserController::class, 'updateadmin'])->name('admin.users.update');
    Route::delete('users-admin/{user}', [UserController::class, 'destroyadmin'])->name('admin.users.destroy');
    Route::resource('group-categories', GroupCategoryController::class);
    Route::get('group-categories/{groupCategory}/points', [GroupCategoryController::class, 'showPoints'])->name('group-categories.points');
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
    Route::post('/toggle-break/{userId}', [DashboardController::class, 'toggleBreak'])->name('toggle.break');
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
    Route::get('/safoodsales', [PointController::class, 'index'])->name('SAfoodSales.index');

    Route::get('/assign-operators', [GroupController::class, 'assignOperatorsForm'])->name('groups.assign-operators-form');
    Route::post('/assign-operators', [GroupController::class, 'assignOperators'])->name('groups.assign-operators');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
