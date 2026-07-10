<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AuthController,
    CategoryController,
    DataTableController,
    DashboardController,
    TemplatesController,
    SmtpController,
    SettingsController,
    SubscribersController,
    ScheduleController,
    PagesController,
    LogController,
    RedirectController,
    MacrosController,
    OrganizationController,
    ProjectController,
    UsersController,
};
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\AjaxController;

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

Route::group(['middleware' => ['install']], function () {
    Route::get('pic/{subscriber}_{template}', [FrontendController::class, 'pic'])->name('frontend.pic')->where('subscriber', '[0-9]+')->where('template', '[0-9]+');
    Route::get('referral/{ref}/{subscriber}', [FrontendController::class, 'redirectLog'])->name('frontend.referral')->where('subscriber', '[0-9]+');
    Route::get('unsubscribe/{subscriber}/{token}', [FrontendController::class, 'unsubscribe'])->name('frontend.unsubscribe')->where('subscriber', '[0-9]+')->where('token', '[a-z0-9]+');
    Route::get('subscribe/{subscriber}/{token}', [FrontendController::class, 'subscribe'])->name('frontend.subscribe')->where('subscriber', '[0-9]+')->where('token', '[a-z0-9]+');
    Route::any('form', [FrontendController::class, 'form'])->name('frontend.form');
    Route::any('categories', [FrontendController::class, 'getCategories'])->name('frontend.categories');
    Route::post('add-sub', [FrontendController::class, 'addSub'])->name('frontend.addsub');
    Route::any('ajax', [AjaxController::class, 'action'])->name('admin.ajax.action');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard.index');
    Route::get('templates', [TemplatesController::class, 'index'])->name('admin.templates.index');

    Route::group(['prefix' => 'template'], function () {
        Route::get('create', [TemplatesController::class, 'create'])->name('admin.templates.create');
        Route::post('store', [TemplatesController::class, 'store'])->name('admin.templates.store');
        Route::get('edit/{id}', [TemplatesController::class, 'edit'])->name('admin.templates.edit')->where('id', '[0-9]+');
        Route::put('update', [TemplatesController::class, 'update'])->name('admin.templates.update');
        Route::delete('destroy/{id}', [TemplatesController::class, 'destroy'])->name('admin.templates.destroy')->where('id', '[0-9]+');
        Route::post('status', [TemplatesController::class, 'delete'])->name('admin.templates.status');
    });

    Route::middleware(['permission:admin|moderator'])->group(function () {
        Route::group(['prefix' => 'category'], function () {
            Route::get('', [CategoryController::class, 'index'])->name('admin.category.index');
            Route::get('create', [CategoryController::class, 'create'])->name('admin.category.create');
            Route::post('store', [CategoryController::class, 'store'])->name('admin.category.store');
            Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('admin.category.edit')->where('id', '[0-9]+');
            Route::put('update', [CategoryController::class, 'update'])->name('admin.category.update');
            Route::delete('destroy/{id}', [CategoryController::class, 'destroy'])->name('admin.category.destroy')->where('id', '[0-9]+');
        });

    });

    Route::middleware(['permission:admin|organization_admin|project_admin|moderator'])->group(function () {
        Route::group(['prefix' => 'subscribers'], function () {
            Route::get('', [SubscribersController::class, 'index'])->name('admin.subscribers.index');
            Route::get('create', [SubscribersController::class, 'create'])->name('admin.subscribers.create');
            Route::post('store', [SubscribersController::class, 'store'])->name('admin.subscribers.store');
            Route::get('edit/{id}', [SubscribersController::class, 'edit'])->name('admin.subscribers.edit')->where('id', '[0-9]+');
            Route::put('update', [SubscribersController::class, 'update'])->name('admin.subscribers.update');
            Route::delete('destroy/{id}', [SubscribersController::class, 'destroy'])->name('admin.subscribers.destroy')->where('id', '[0-9]+');
            Route::get('import', [SubscribersController::class, 'import'])->name('admin.subscribers.import');
            Route::post('import-subscribers', [SubscribersController::class, 'importSubscribers'])->name('admin.subscribers.import_subscribers');
            Route::get('export', [SubscribersController::class, 'export'])->name('admin.subscribers.export');
            Route::post('export-subscribers', [SubscribersController::class, 'exportSubscribers'])->name('admin.subscribers.export_subscribers');
            Route::get('remove-all', [SubscribersController::class, 'removeAll'])->name('admin.subscribers.remove_all')->middleware(['permission:admin']);
            Route::post('status', [SubscribersController::class, 'status'])->name('admin.subscribers.status');
        });

    });

    Route::group(['prefix' => 'schedule'], function () {
        Route::get('', [ScheduleController::class, 'index'])->name('admin.schedule.index');
        Route::post('calendar-crud-ajax', [ScheduleController::class, 'calendarEvents'])->name('admin.schedule.calendarEvents');
        Route::get('calendar-event', [ScheduleController::class, 'list'])->name('admin.schedule.list');
        Route::get('create', [ScheduleController::class, 'create'])->name('admin.schedule.create');
        Route::post('store', [ScheduleController::class, 'store'])->name('admin.schedule.store');
        Route::get('edit/{id}', [ScheduleController::class, 'edit'])->name('admin.schedule.edit')->where('id', '[0-9]+');
        Route::put('update', [ScheduleController::class, 'update'])->name('admin.schedule.update');
        Route::delete('destroy/{id}', [ScheduleController::class, 'destroy'])->name('admin.schedule.destroy')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'log'], function () {
        Route::get('', [LogController::class, 'index'])->name('admin.log.index');
        Route::get('clear', [LogController::class, 'clear'])->name('admin.log.clear');
        Route::get('download/{id}', [LogController::class, 'download'])->name('admin.log.report')->where('id', '[0-9]+');
        Route::get('info/{id}', [LogController::class, 'info'])->name('admin.log.info')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'redirect'], function () {
        Route::get('', [RedirectController::class, 'index'])->name('admin.redirect.index');
        Route::get('clear', [RedirectController::class, 'clear'])->name('admin.redirect.clear');
        Route::get('download/{url}', [RedirectController::class, 'download'])->name('admin.redirect.report');
        Route::get('info/{url}', [RedirectController::class, 'info'])->name('admin.redirect.info');
    });

    Route::middleware(['permission:admin'])->group(function () {
        Route::group(['prefix' => 'macros'], function () {
            Route::get('', [MacrosController::class, 'index'])->name('admin.macros.index');
            Route::get('create', [MacrosController::class, 'create'])->name('admin.macros.create');
            Route::post('store', [MacrosController::class, 'store'])->name('admin.macros.store');
            Route::get('edit/{id}', [MacrosController::class, 'edit'])->name('admin.macros.edit')->where('id', '[0-9]+');
            Route::put('update', [MacrosController::class, 'update'])->name('admin.macros.update');
            Route::delete('destroy/{id}', [MacrosController::class, 'destroy'])->name('admin.macros.destroy')->where('id', '[0-9]+');
        });

        Route::group(['prefix' => 'smtp'], function () {
            Route::get('', [SmtpController::class, 'index'])->name('admin.smtp.index');
            Route::get('create', [SmtpController::class, 'create'])->name('admin.smtp.create');
            Route::post('store', [SmtpController::class, 'store'])->name('admin.smtp.store');
            Route::get('edit/{id}', [SmtpController::class, 'edit'])->name('admin.smtp.edit')->where('id', '[0-9]+');
            Route::put('update', [SmtpController::class, 'update'])->name('admin.smtp.update');
            Route::delete('destroy/{id}', [SmtpController::class, 'destroy'])->name('admin.smtp.destroy')->where('id', '[0-9]+');
            Route::post('status', [SmtpController::class, 'status'])->name('admin.smtp.status');
        });

        Route::group(['prefix' => 'settings'], function () {
            Route::get('', [SettingsController::class, 'index'])->name('admin.settings.index');
            Route::put('update', [SettingsController::class, 'update'])->name('admin.settings.update');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('', [UsersController::class, 'index'])->name('admin.users.index');
            Route::get('create', [UsersController::class, 'create'])->name('admin.users.create');
            Route::post('store', [UsersController::class, 'store'])->name('admin.users.store');
            Route::get('edit/{id}', [UsersController::class, 'edit'])->name('admin.users.edit');
            Route::put('update', [UsersController::class, 'update'])->name('admin.users.update');
            Route::delete('destroy/{id}', [UsersController::class, 'destroy'])->name('admin.users.destroy')->where('id', '[0-9]+');
        });
    });

    Route::middleware(['permission:admin|organization_admin|project_admin|moderator'])->group(function () {
        Route::group(['prefix' => 'organizations'], function () {
            Route::get('', [OrganizationController::class, 'index'])->name('admin.organizations.index');
            Route::get('create', [OrganizationController::class, 'create'])->name('admin.organizations.create');
            Route::post('store', [OrganizationController::class, 'store'])->name('admin.organizations.store');
            Route::get('{organization}', [OrganizationController::class, 'show'])->name('admin.organizations.show')->where('organization', '[0-9]+');
            Route::get('{organization}/edit', [OrganizationController::class, 'edit'])->name('admin.organizations.edit')->where('organization', '[0-9]+');
            Route::put('{organization}', [OrganizationController::class, 'update'])->name('admin.organizations.update')->where('organization', '[0-9]+');
            Route::delete('{organization}', [OrganizationController::class, 'destroy'])->name('admin.organizations.destroy')->where('organization', '[0-9]+');
            Route::post('{organization}/administrators', [OrganizationController::class, 'storeAdministrator'])->name('admin.organizations.administrators.store')->where('organization', '[0-9]+');
            Route::delete('{organization}/administrators/{user}', [OrganizationController::class, 'destroyAdministrator'])->name('admin.organizations.administrators.destroy')->where(['organization' => '[0-9]+', 'user' => '[0-9]+']);

            Route::get('{organization}/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create')->where('organization', '[0-9]+');
            Route::post('{organization}/projects', [ProjectController::class, 'store'])->name('admin.projects.store')->where('organization', '[0-9]+');
            Route::get('{organization}/projects/{project}', [ProjectController::class, 'show'])->name('admin.projects.show')->where(['organization' => '[0-9]+', 'project' => '[0-9]+']);
            Route::get('{organization}/projects/{project}/template/create', [TemplatesController::class, 'createForProject'])->name('admin.projects.templates.create')->where(['organization' => '[0-9]+', 'project' => '[0-9]+']);
            Route::get('{organization}/projects/{project}/edit', [ProjectController::class, 'edit'])->name('admin.projects.edit')->where(['organization' => '[0-9]+', 'project' => '[0-9]+']);
            Route::put('{organization}/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update')->where(['organization' => '[0-9]+', 'project' => '[0-9]+']);
            Route::delete('{organization}/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy')->where(['organization' => '[0-9]+', 'project' => '[0-9]+']);
            Route::post('{organization}/projects/{project}/administrators', [ProjectController::class, 'storeAdministrator'])->name('admin.projects.administrators.store')->where(['organization' => '[0-9]+', 'project' => '[0-9]+']);
            Route::delete('{organization}/projects/{project}/administrators/{user}', [ProjectController::class, 'destroyAdministrator'])->name('admin.projects.administrators.destroy')->where(['organization' => '[0-9]+', 'project' => '[0-9]+', 'user' => '[0-9]+']);
            Route::post('{organization}/projects/{project}/moderators', [ProjectController::class, 'storeModerator'])->name('admin.projects.moderators.store')->where(['organization' => '[0-9]+', 'project' => '[0-9]+']);
            Route::delete('{organization}/projects/{project}/moderators/{user}', [ProjectController::class, 'destroyModerator'])->name('admin.projects.moderators.destroy')->where(['organization' => '[0-9]+', 'project' => '[0-9]+', 'user' => '[0-9]+']);
        });
    });

    Route::get('faq', [PagesController::class, 'faq'])->name('admin.faq');

    Route::group(['prefix' => 'pages'], function () {

        Route::get('cron-job-list', [PagesController::class, 'cronJobList'])->name('admin.pages.cron_job_list');
        Route::get('phpinfo', [PagesController::class, 'phpinfo'])->name('admin.pages.phpinfo');
        Route::get('subscription-form', [PagesController::class, 'subscriptionForm'])->name('admin.pages.subscription_form');
    });

    Route::group(['prefix' => 'datatable'], function () {
        Route::any('templates', [DataTableController::class, 'getTemplates'])->name('admin.datatable.templates');
        Route::any('category', [DataTableController::class, 'getCategory'])->name('admin.datatable.category')->middleware(['permission:admin|moderator']);
        Route::any('smtp', [DataTableController::class, 'getSmtp'])->name('admin.datatable.smtp')->middleware(['permission:admin']);
        Route::any('subscribers', [DataTableController::class, 'getSubscribers'])->name('admin.datatable.subscribers')->middleware(['permission:admin|organization_admin|project_admin|moderator']);
        Route::any('users', [DataTableController::class, 'getUsers'])->name('admin.datatable.users')->middleware(['permission:admin']);
        Route::any('logs', [DataTableController::class, 'getLogs'])->name('admin.datatable.logs');
        Route::any('info-log/{id?}', [DataTableController::class, 'getInfoLog'])->name('admin.datatable.info_log')->where('id', '[0-9]+');
        Route::any('redirect-log', [DataTableController::class, 'getRedirectLogs'])->name('admin.datatable.redirect');
        Route::any('info-redirect-log/{url}', [DataTableController::class, 'getInfoRedirectLog'])->name('admin.datatable.info_redirect');
        Route::any('macros', [DataTableController::class, 'getMacros'])->name('admin.datatable.macros')->middleware(['permission:admin']);
    });
});

Route::group(['prefix' => 'install'], function () {
    Route::get('/', [InstallController::class, 'index'])->name('install.start');
    Route::get('requirements', [InstallController::class, 'requirements'])->name('install.requirements');
    Route::get('permissions', [InstallController::class, 'permissions'])->name('install.permissions');
    Route::get('database', [InstallController::class, 'database'])->name('install.database');
    Route::get('admin', [InstallController::class, 'admin'])->name('install.admin');
    Route::post('installation', [InstallController::class, 'installation'])->name('install.installation');
    Route::post('install-app', [InstallController::class, 'install'])->name('install.install');
    Route::get('complete', [InstallController::class, 'complete'])->name('install.complete');
    Route::get('error', [InstallController::class, 'error'])->name('install.error');
    Route::any('ajax', [InstallController::class, 'ajax'])->name('install.ajax.action');
});
