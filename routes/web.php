<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/',[HomeController::class,'index'])->name('home.index'); //==========

Route::get('/shop',[ ShopController::class,'index']) -> name ('shop.index');
Route::get('/shop/{slug}',[ ShopController::class,'productDetails']) -> name ('shop.product.details');
Route::get('/cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class,'add_to_cart'])->name('cart.add');
Route::put('/cart/update',[CartController::class,'update_cart'])->name('cart.update');
Route::delete('/cart/remove/{rowId}',[CartController::class,'remove_from_cart'])->name('cart.remove');
Route::delete('/cart/clear',[CartController::class,'clear_cart'])->name('cart.clear');


Route::get('/wishlist',[WishlistController::class,'index'])->name('wishlist.index');
Route::post('/wishlist/add',[WishlistController::class,'add_to_wishlist'])->name('wishlist.add');
Route::delete('/wishlist/remove/{rowId}',[WishlistController::class,'remove_from_wishlist'])->name('wishlist.remove');
Route::delete('/wishlist/remove',[WishlistController::class,'clear_wishlist'])->name('wishlist.clear');
Route::post('/wishlist/move-to-cart/{rowId}',[WishlistController::class,'move_to_cart'])->name('wishlist.move.to.cart');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/users',[UserController::class,'index'])->name('users.index');//==================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware([AuthAdmin::class])->group(function(){ //==========
    Route::get('/admin',[AdminController::class,'index'])->name('admin.index');

 //1============================
    //View brand data
    Route::get('/admin/brands',[AdminController::class,'brands'])->name('admin.brands');
    // View add brand layout 
    Route::get('/admin/brand-add',[AdminController::class,'brandAdd'])->name('admin.brand.add');
    // Add this POST route to handle saving the brand:
    Route::post('/admin/brand-store',[AdminController::class,'brandStore'])->name('admin.brand.store');
    // View  edit brand layout
    Route::get('/admin/brand-edit/{id}',[AdminController::class,'brandEdit'])->name('admin.brand.edit');
    //Update brand data
    Route::put('/admin/brand-update/{id}',[AdminController::class, 'brandUpdate']) -> name('admin.brand.update');
    //Delete brand data
    Route::delete('/admin/brand-delete/{id}',[AdminController::class,'brandDelete']) -> name('admin.brand.delete');
 
 //2============================
    //View Category data
    Route::get('/admin/categories',[AdminController::class,'categories']) -> name('admin.categories');
    //View  Categories add layout 
    Route::get('/admin/categories-add',[AdminController::class,'categoryAdd']) -> name('admin.category.add');
    // Add this POST route to handle saving the category:
    Route::post('/admin/categories-store',[AdminController::class,'categoryStore']) -> name('admin.category.store');
    //View edit category layout
    Route::get('/admin/categories-edit/{id}',[AdminController::class,'categoryEdit']) -> name('admin.category.edit');
    //Update category data
    Route::put('/admin/categories-update/{id}',[AdminController::class,'categoryUpdate']) -> name('admin.category.update');
    //Delete category data
    Route::delete('/admin/categories-delete/{id}',[AdminController::class,'categoryDelete']) -> name('admin.category.delete');
   
//3==============================
    //get all data product
    Route::get('/admin/products',[ProductController::class,'products']) -> name('admin.products');
    //  view add product data layout
    Route::get('/admin/product-add',[ProductController::class,'productAdd']) -> name('admin.product.add');
    // add product data
    Route::post('/admin/product-store',[ProductController::class,'productStore']) -> name('admin.product.store');
    // view  edit product layout
    Route::get('/admin/product-edit/{id}',[ProductController::class,'productEdit']) -> name('admin.product.edit');
    // Update product data
    Route::put('/admin/product-update/{id}',[ProductController::class,'productUpdate']) -> name('admin.product.update');
    // Delete product data
    Route::delete('/admin/product-delete/{id}',[ProductController::class,'productDelete']) -> name('admin.product.delete');
    // Delete  multiple products datas
    Route::delete('/admin/produts-bulk-delete',[ProductController::class,'productBulkDelete']) -> name('admin.product.bulk.delete');
    // Export products into excel
    Route::get('/admin/produts-export',[ProductController::class,'productExport']) -> name('admin.product.export');

//4==============================
    Route::get('/admin/coupons',[CouponController::class,'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon-add',[CouponController::class,'coupon_add'])->name('admin.coupon.add');
    // Add new Coupons data
    Route::get('/admin/coupon-add',[CouponController::class,'coupon_add'])->name('admin.coupon.add');
    Route::post('/admin/coupon-store',[CouponController::class,'coupon_store'])->name('admin.coupon.store');
});

require __DIR__.'/auth.php';
