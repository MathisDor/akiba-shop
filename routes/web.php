<?php

use App\Http\Controllers\Account\FavoriteController;
use App\Http\Controllers\Account\ListingController as AccountListingController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ListingController as AdminListingController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminMemberController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingReportController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public marketplace
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/annonces', [ListingController::class, 'index'])->name('listings.index');
Route::get('/annonce/{listing}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoriesController::class, 'show'])->name('categories.show');
Route::get('/vendeur/{user}', SellerController::class)->name('sellers.show');

Route::post('/annonce/{listing}/signaler', [ListingReportController::class, 'store'])
    ->middleware('throttle:10,60')
    ->name('listings.report');

Route::view('/a-propos', 'pages.about')->name('pages.about');
Route::view('/contact', 'pages.contact')->name('pages.contact');
Route::view('/cgu', 'pages.terms')->name('pages.terms');
Route::view('/confidentialite', 'pages.privacy')->name('pages.privacy');
Route::view('/conseils-securite', 'pages.safety')->name('pages.safety');

// URLs of the former fashion shop, kept alive for bookmarks and search engines.
Route::permanentRedirect('/recherche', '/annonces');
Route::permanentRedirect('/catalogue', '/annonces');
Route::permanentRedirect('/panier', '/');
Route::get('/{gender}/{category?}', fn () => redirect()->route('categories.show', 'mode-beaute', 301))
    ->whereIn('gender', ['femme', 'homme']);
Route::get('/produit/{any}', fn () => redirect()->route('listings.index', [], 301))->where('any', '.*');

/*
|--------------------------------------------------------------------------
| Guest / member authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/inscription', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/inscription', [RegisteredUserController::class, 'store'])->middleware('throttle:10,1')->name('register.store');
});

Route::middleware('auth')->post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Member area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/publier', [AccountListingController::class, 'create'])->name('listings.create');
    Route::post('/publier', [AccountListingController::class, 'store'])->middleware('throttle:20,60')->name('listings.store');

    Route::post('/annonce/{listing}/favori', [FavoriteController::class, 'toggle'])->name('listings.favorite');

    Route::prefix('compte')->name('account.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/annonces', [AccountListingController::class, 'index'])->name('listings');
        Route::get('/annonces/{listing}/modifier', [AccountListingController::class, 'edit'])->name('listings.edit');
        Route::put('/annonces/{listing}', [AccountListingController::class, 'update'])->name('listings.update');
        Route::delete('/annonces/{listing}', [AccountListingController::class, 'destroy'])->name('listings.destroy');
        Route::patch('/annonces/{listing}/renouveler', [AccountListingController::class, 'renew'])->name('listings.renew');
        Route::patch('/annonces/{listing}/vendue', [AccountListingController::class, 'markSold'])->name('listings.sold');

        Route::get('/favoris', [FavoriteController::class, 'index'])->name('favorites');
    });
});

/*
|--------------------------------------------------------------------------
| Admin back-office
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/connexion', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/connexion', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::post('/deconnexion', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::middleware('permission:listings.moderate')->group(function () {
            Route::get('/annonces', [AdminListingController::class, 'index'])->name('listings.index');
            Route::get('/annonces/{listing:id}', [AdminListingController::class, 'show'])->name('listings.show');
            Route::patch('/annonces/{listing:id}/approuver', [AdminListingController::class, 'approve'])->name('listings.approve');
            Route::patch('/annonces/{listing:id}/refuser', [AdminListingController::class, 'reject'])->name('listings.reject');
            Route::patch('/annonces/{listing:id}/booster', [AdminListingController::class, 'boost'])->name('listings.boost');
            Route::delete('/annonces/{listing:id}', [AdminListingController::class, 'destroy'])->name('listings.destroy');
        });

        Route::middleware('permission:reports.manage')->group(function () {
            Route::get('/signalements', [AdminReportController::class, 'index'])->name('reports.index');
            Route::patch('/signalements/{report}', [AdminReportController::class, 'update'])->name('reports.update');
        });

        Route::middleware('permission:categories.manage')->group(function () {
            Route::resource('categories', AdminCategoryController::class)
                ->except(['show'])
                ->parameters(['categories' => 'category:id'])
                ->names('categories');
        });

        Route::middleware('permission:users.manage')->group(function () {
            Route::get('/utilisateurs', [AdminMemberController::class, 'index'])->name('users.index');
            Route::get('/utilisateurs/{user}', [AdminMemberController::class, 'show'])->name('users.show');
            Route::patch('/utilisateurs/{user}/verification', [AdminMemberController::class, 'toggleVerified'])->name('users.verify');
            Route::patch('/utilisateurs/{user}/statut', [AdminMemberController::class, 'toggleActive'])->name('users.toggle-active');
        });

        Route::middleware('super_admin')->group(function () {
            Route::get('/administrateurs', [AdminUserController::class, 'index'])->name('admins.index');
            Route::get('/administrateurs/nouveau', [AdminUserController::class, 'create'])->name('admins.create');
            Route::post('/administrateurs', [AdminUserController::class, 'store'])->name('admins.store');
            Route::get('/administrateurs/{admin}/modifier', [AdminUserController::class, 'edit'])->name('admins.edit');
            Route::put('/administrateurs/{admin}', [AdminUserController::class, 'update'])->name('admins.update');
            Route::patch('/administrateurs/{admin}/statut', [AdminUserController::class, 'toggleActive'])->name('admins.toggle-active');
            Route::delete('/administrateurs/{admin}', [AdminUserController::class, 'destroy'])->name('admins.destroy');

            Route::get('/journal', [ActivityLogController::class, 'index'])->name('activity-log.index');
        });
    });
});
