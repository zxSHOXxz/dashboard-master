<?php
# Backend Controllers

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\BackendAdminController;
use App\Http\Controllers\Backend\BackendNotificationsController;
use App\Http\Controllers\Backend\BackendHelperController;
use App\Http\Controllers\Backend\BackendProfileController;
use App\Http\Controllers\Backend\BackendArticleController;
use App\Http\Controllers\Backend\BackendSiteMapController;
use App\Http\Controllers\Backend\BackendSettingController;
use App\Http\Controllers\Backend\BackendContactController;
use App\Http\Controllers\Backend\BackendRedirectionController;
use App\Http\Controllers\Backend\BackendUserController;
use App\Http\Controllers\Backend\BackendTrafficsController;
use App\Http\Controllers\Backend\BackendPageController;
use App\Http\Controllers\Backend\BackendMenuController;
use App\Http\Controllers\Backend\BackendMenuLinkController;
use App\Http\Controllers\Backend\BackendFileController;
use App\Http\Controllers\Backend\BackendUserRoleController;
use App\Http\Controllers\Backend\BackendRoleController;
use App\Http\Controllers\Backend\BackendPluginController;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\DonorController;
# Frontend Controllers
use App\Http\Controllers\FrontController;
use App\Http\Controllers\FrontendProfileController;
use App\Http\Controllers\ProgramController;

// Auth::routes();





Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/index2', function () {
    return view('front.index2');
})->name('index2');



Route::prefix('dashboard')->middleware(['auth:web,client', 'ActiveAccount', 'verified'])->name('user.')->group(function () {
    Route::get('/', [FrontendProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('/support', [FrontendProfileController::class, 'support'])->name('support');
    Route::get('/support/create-ticket', [FrontendProfileController::class, 'create_ticket'])->name('create-ticket');
    Route::post('/support/create-ticket', [FrontendProfileController::class, 'store_ticket'])->name('store-ticket');
    Route::get('/support/{ticket}', [FrontendProfileController::class, 'ticket'])->name('ticket');
    Route::post('/support/{ticket}/reply', [FrontendProfileController::class, 'reply_ticket'])->name('reply-ticket');
    Route::get('/notifications', [FrontendProfileController::class, 'notifications'])->name('notifications');
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/settings', [FrontendProfileController::class, 'profile_edit'])->name('edit');
        Route::put('/update', [FrontendProfileController::class, 'profile_update'])->name('update');
        Route::put('/update-password', [FrontendProfileController::class, 'profile_update_password'])->name('update-password');
        Route::put('/update-email', [FrontendProfileController::class, 'profile_update_email'])->name('update-email');
    });
});



#Route::get('/test',[BackendTestController::class,'test']);

Route::prefix('admin')->middleware(['auth:admin,donor', 'ActiveAccount'])->name('admin.')->group(function () {

    Route::get('/', [BackendAdminController::class, 'index'])->name('index');
    Route::middleware('auth')->group(function () {
        Route::resource('files', BackendFileController::class);
        Route::post('contacts/resolve', [BackendContactController::class, 'resolve']);
        Route::resource('contacts', BackendContactController::class);
        Route::resource('menus', BackendMenuController::class);
        Route::get('users/{user}/access', [BackendUserController::class, 'access'])->name('users.access');
        Route::resource('users', BackendUserController::class);
        Route::resource('roles', BackendRoleController::class);
        Route::get('user-roles/{user}', [BackendUserRoleController::class, 'index'])->name('users.roles.index');
        Route::put('user-roles/{user}', [BackendUserRoleController::class, 'update'])->name('users.roles.update');
        Route::get('client-roles/{client}', [BackendUserRoleController::class, 'indexClient'])->name('clients.roles.index');
        Route::put('client-roles/{client}', [BackendUserRoleController::class, 'updateClient'])->name('clients.roles.update');
        Route::resource('articles', BackendArticleController::class);
        Route::resource('pages', BackendPageController::class);
        Route::post('menu-links/get-type', [BackendMenuLinkController::class, 'getType'])->name('menu-links.get-type');
        Route::post('menu-links/order', [BackendMenuLinkController::class, 'order'])->name('menu-links.order');
        Route::resource('menu-links', BackendMenuLinkController::class);
        Route::resource('redirections', BackendRedirectionController::class);
        Route::resource('admins', AdminController::class);
        Route::resource('donors', DonorController::class);
        Route::resource('donates', DonateController::class);
        Route::resource('programms', ProgramController::class);
        Route::get('traffics', [BackendTrafficsController::class, 'index'])->name('traffics.index');
        Route::get('traffics/logs', [BackendTrafficsController::class, 'logs'])->name('traffics.logs');
        Route::get('error-reports', [BackendTrafficsController::class, 'error_reports'])->name('traffics.error-reports');
        Route::get('error-reports/{report}', [BackendTrafficsController::class, 'error_report'])->name('traffics.error-report');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [BackendSettingController::class, 'index'])->name('index');
            Route::put('/update', [BackendSettingController::class, 'update'])->name('update');
        });
    });

    Route::prefix('upload')->name('upload.')->group(function () {
        Route::post('/image', [BackendHelperController::class, 'upload_image'])->name('image');
        Route::post('/file', [BackendHelperController::class, 'upload_file'])->name('file');
        Route::post('/remove-file', [BackendHelperController::class, 'remove_files'])->name('remove-file');
    });

    Route::prefix('plugins')->name('plugins.')->group(function () {
        Route::get('/', [BackendPluginController::class, 'index'])->name('index');
        Route::get('/create', [BackendPluginController::class, 'create'])->name('create');
        Route::post('/create', [BackendPluginController::class, 'store'])->name('store');
        Route::post('/{plugin}/activate', [BackendPluginController::class, 'activate'])->name('activate');
        Route::post('/{plugin}/deactivate', [BackendPluginController::class, 'deactivate'])->name('deactivate');
        Route::post('/{plugin}/delete', [BackendPluginController::class, 'delete'])->name('delete');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [BackendProfileController::class, 'index'])->name('index');
        Route::get('/edit', [BackendProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [BackendProfileController::class, 'update'])->name('update');
        Route::put('/update-password', [BackendProfileController::class, 'update_password'])->name('update-password');
        Route::put('/update-email', [BackendProfileController::class, 'update_email'])->name('update-email');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [BackendNotificationsController::class, 'index'])->name('index');
        Route::get('/ajax', [BackendNotificationsController::class, 'ajax'])->name('ajax');
        Route::post('/see', [BackendNotificationsController::class, 'see'])->name('see');
        Route::get('/create', [BackendNotificationsController::class, 'create'])->name('create');
        Route::post('/create', [BackendNotificationsController::class, 'store'])->name('store');
    });
});

Route::get('/login/google/redirect', [LoginController::class, 'redirect_google']);
Route::get('/login/google/callback', [LoginController::class, 'callback_google']);
Route::get('/login/facebook/redirect', [LoginController::class, 'redirect_facebook']);
Route::get('/login/facebook/callback', [LoginController::class, 'callback_facebook']);


Route::get('blocked', [BackendHelperController::class, 'blocked_user'])->name('blocked');
Route::get('robots.txt', [BackendHelperController::class, 'robots']);
Route::get('manifest.json', [BackendHelperController::class, 'manifest'])->name('manifest');
Route::get('sitemap.xml', [BackendSiteMapController::class, 'sitemap']);
Route::get('sitemaps/links', [BackendSiteMapController::class, 'custom_links']);
Route::get('sitemaps/{name}/{page}/sitemap.xml', [BackendSiteMapController::class, 'viewer']);


Route::view('contact', 'front.pages.contact')->name('contact');
Route::get('page/{page}', [FrontController::class, 'page'])->name('page.show');
Route::get('tag/{tag}', [FrontController::class, 'tag'])->name('tag.show');
Route::get('category/{category}', [FrontController::class, 'category'])->name('category.show');
Route::get('article/{article}', [FrontController::class, 'article'])->name('article.show');
Route::get('blog', [FrontController::class, 'blog'])->name('blog');
Route::post('contact', [FrontController::class, 'contact_post'])->name('contact-post');
Route::post('comment', [FrontController::class, 'comment_post'])->name('comment-post');
