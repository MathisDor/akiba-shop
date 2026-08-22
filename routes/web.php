<?php

use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\FavoriteController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeliveryZoneController as AdminDeliveryZoneController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderConfirmationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Cart;
use App\Livewire\Checkout\Informations;
use App\Livewire\Checkout\Livraison;
use App\Livewire\Checkout\Paiement;
use App\Livewire\Checkout\PaymentPending;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public storefront
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/recherche', SearchController::class)->name('search');
Route::get('/categories', CategoriesController::class)->name('categories.index');
Route::get('/catalogue', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/{gender}', [CatalogController::class, 'gender'])
    ->whereIn('gender', ['femme', 'homme'])
    ->name('catalog.gender');
Route::get('/{gender}/{category}', [CatalogController::class, 'category'])
    ->whereIn('gender', ['femme', 'homme'])
    ->name('catalog.category');
Route::get('/produit/{product}', ProductController::class)->name('products.show');

/*
|--------------------------------------------------------------------------
| Cart & Checkout
|--------------------------------------------------------------------------
*/

Route::get('/panier', Cart::class)->name('cart.index');

Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/informations', Informations::class)->name('informations');
    Route::get('/livraison', Livraison::class)->name('livraison');
    Route::get('/paiement', Paiement::class)->name('paiement');
});

Route::middleware('auth')->get('/commande/confirmation/{order:order_number}', OrderConfirmationController::class)
    ->name('checkout.confirmation');

Route::middleware('auth')->get('/commande/{order:order_number}/en-attente', PaymentPending::class)
    ->name('checkout.pending');

/*
|--------------------------------------------------------------------------
| Guest / Client authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/inscription', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/inscription', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Customer account area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('compte')->name('account.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('profile');
    Route::put('/', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/adresses', [AddressController::class, 'index'])->name('addresses');
    Route::post('/adresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/adresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/adresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/adresses/{address}/defaut', [AddressController::class, 'makeDefault'])->name('addresses.default');

    Route::get('/commandes', [AccountOrderController::class, 'index'])->name('orders');
    Route::get('/commandes/{order:order_number}', [AccountOrderController::class, 'show'])->name('orders.show');

    Route::get('/favoris', [FavoriteController::class, 'index'])->name('favorites');
    Route::post('/favoris/{product}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});

/*
|--------------------------------------------------------------------------
| Admin dashboard
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

        Route::middleware('permission:products.manage')->group(function () {
            Route::get('/produits', [AdminProductController::class, 'index'])->name('products.index');
            Route::get('/produits/nouveau', [AdminProductController::class, 'create'])->name('products.create');
            Route::get('/produits/{product}/modifier', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::delete('/produits/en-masse', [AdminProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
            Route::delete('/produits/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        });

        Route::middleware('permission:categories.manage')->group(function () {
            Route::resource('categories', AdminCategoryController::class)->except(['show'])->names('categories');
        });

        Route::middleware('permission:collections.manage')->group(function () {
            Route::resource('collections', AdminCollectionController::class)->except(['show'])->names('collections');
        });

        Route::middleware('permission:promotions.manage')->group(function () {
            Route::resource('promotions', AdminPromotionController::class)->except(['show'])->names('promotions');
        });

        Route::middleware('permission:coupons.manage')->group(function () {
            Route::resource('coupons', AdminCouponController::class)->except(['show'])->names('coupons');
        });

        Route::middleware('permission:delivery.manage')->group(function () {
            Route::resource('zones-livraison', AdminDeliveryZoneController::class)->except(['show'])->parameters(['zones-livraison' => 'deliveryZone'])->names('delivery-zones');
        });

        Route::middleware('permission:orders.manage')->group(function () {
            Route::get('/commandes', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/commandes/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::patch('/commandes/{order}/statut', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        });

        Route::middleware('permission:customers.view')->group(function () {
            Route::get('/clients', [AdminCustomerController::class, 'index'])->name('customers.index');
            Route::get('/clients/{user}', [AdminCustomerController::class, 'show'])->name('customers.show');
        });

        Route::middleware('permission:reviews.moderate')->group(function () {
            Route::get('/avis', [AdminReviewController::class, 'index'])->name('reviews.index');
            Route::patch('/avis/{review}/moderer', [AdminReviewController::class, 'moderate'])->name('reviews.moderate');
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
