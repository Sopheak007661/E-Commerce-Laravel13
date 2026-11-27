# Laravel 13 E-Commerce Project: Step-by-Step Guide

A simple roadmap for building an online shop with User and Admin login, based on your 36-part notes.

## 1. The Big Picture

**What you are building:** an online shop with two kinds of people.

- **User**: browses products, adds to cart, checks out.
- **Admin**: manages brands, categories, products (create, edit, delete, search, export).

**Tools used**

- Laravel 13 (PHP framework)
- Laravel Breeze (ready-made login/register)
- MySQL (database), XAMPP + phpMyAdmin
- Tailwind CSS + Swiper + Font Awesome (shop design from the GitHub template)
- Packages: intervention/image-laravel (resize images), maatwebsite/excel (export)

**The 4 building blocks you repeat in every part**

| Block | What it does | Example |
| --- | --- | --- |
| Route | URL that the browser opens | /admin/brands |
| Controller | Code that decides what to do | BrandController |
| Model | Talks to one database table | Brand |
| View (Blade) | The HTML page the person sees | admin/brands/index.blade.php |

Flow: **Browser -> Route -> Controller -> Model (database) -> View -> Browser**.

## 2. How to Use This Guide

1. Work in order. Each part uses files from the part before.
2. After every part, run the site and test it before moving on.
3. Commit to Git after each part so you can go back if something breaks.
4. For new projects, copy the same order: setup, layout, database, login, then admin CRUD, then the public shop, then cart.

## 3. Roadmap at a Glance

| Stage | Parts | Goal |
| --- | --- | --- |
| A. Setup | 1-3 | Project, Breeze, layout, MySQL |
| B. Login system | 4-6 | User and Admin login, nice login pages, dashboards |
| C. Admin: Brands | 7-11 | Brand CRUD + search |
| D. Admin: Categories | 12-16 | Category CRUD + subcategories |
| E. Admin: Products | 17-25 | Product CRUD, bulk delete, print, Excel export, sort |
| F. Public Shop | 26-32 | Shop page, details, sort, filters, search |
| G. Cart | 33-36 | Add to cart, view cart, change quantity |

---

## Stage A: Setup (Parts 1-3)

### Part 1: Create project and install Breeze

**Goal:** a working Laravel site with login and register pages.

```bash
composer create-project laravel/laravel Project_Folder_Name
cd Project_Folder_Name
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install
npm run build
php artisan serve
```

**Check:** open http://127.0.0.1:8000. You should see the welcome page and /login, /register.

**If you see 'No application encryption key':** run `php artisan key:generate`, then `php artisan config:clear`.

### Part 2: Layout setup (shop design)

**Goal:** every page uses one shared shop design (header, footer, scripts).

**Idea:** `resources/views/layouts/app.blade.php` is the master layout. Each page is wrapped in `<x-app-layout> ... </x-app-layout>` and its content is injected where `{{ $slot }}` is.

**Steps**

1. Download the GitHub template. Copy `assets` and `uploads` into `public/`.
2. In `app.blade.php`: replace `<head>` (Tailwind CDN, Font Awesome, Swiper, custom.css, @vite).
3. Replace `<body>` with: header, `{{ $slot }}`, footer + scripts.
4. Create the home page: `php artisan make:view index`, wrap in `<x-app-layout>`.
5. Create `HomeController` with `index()` that returns `view('index')`.
6. Add route: `Route::get('/', [HomeController::class, 'index'])->name('home.index');`

**Tips that save time**

- Change links like `href='shop.php'` into route names, for example `{{ route('shop.index') }}`.
- Use `{{ asset('assets/images/...') }}` for every image, not plain `assets/images/...`. Plain paths break on pages like /shop/product/5.
- Keep Tailwind CDN while learning. Move to Vite build for production.

### Part 3: MySQL configuration

1. Create an empty database `db_ecommerse` in phpMyAdmin.
2. Edit `.env`: `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=db_ecommerse`, `DB_USERNAME=root`, `DB_PASSWORD=` (empty on XAMPP).
3. Run `php artisan migrate`.

**Check:** phpMyAdmin shows tables users, sessions, cache, jobs.

---

## Stage B: Login System (Parts 4-6)

### Part 4: Admin and User authentication

**Idea:** one `users` table, one extra column `utype`. Value `USER` = normal user, `ADM` = admin.

**Steps**

1. In the users migration add: `mobile` (unique) and `utype` (default USER).
2. Add `mobile` to the model's fillable list.
3. In `RegisteredUserController@store`: validate `mobile` and save it.
4. Add a Mobile input to `auth/register.blade.php`.
5. Run `php artisan migrate:refresh` (this erases all data).
6. Create middleware: `php artisan make:middleware AuthAdmin`. In `handle()`: if logged in and `utype == 'ADM'` then continue, else redirect to login.
7. Create pages: `admin/index.blade.php` and `users/index.blade.php`.
8. Create `AdminController` and `UserController`, each with `index()`.
9. Routes: `/users` inside `auth` group, `/admin` inside the `AuthAdmin` group.
10. Header menu: `@auth` shows first letter of name; My Account goes to admin or user page depending on `utype`; Sign Out is a hidden POST form with `@csrf`.

**Make yourself admin:** in phpMyAdmin, open table users, set `utype` to `ADM` for your row.

**Test:** register -> log in -> My Account opens Users Dashboard. Change to ADM -> My Account opens Admin Dashboard. A normal user opening /admin must be sent away.

### Part 5: Login and Register page design

**Idea:** replace Breeze's default forms with the template's styled forms, but keep the same form fields and `route('login')` / `route('register')` actions.

**Keep these rules or login will break**

- Field names must stay `email`, `password`, `name`, `mobile`, `password_confirmation`.
- Remember me checkbox must be `name='remember'` (not remember\_me).
- Errors: use `@error('email') ... @enderror` with the exact field name. Check typos (for example 'mobie' should be 'mobile'; an empty `@error('')` should be `@error('password')`).
- On plain `<input>` use `value='{{ old('email') }}'`. The `:value=` form only works on Blade components like `<x-text-input>`.

### Part 6: Admin and User dashboard layout

**Idea:** make a separate admin layout (sidebar + top bar) so admin pages look different from the shop.

**Steps**

1. Create `resources/views/layouts/admin.blade.php` (sidebar with links: Dashboard, Brands, Categories, Products, Orders).
2. Use `<x-admin-layout>` or `@extends('layouts.admin')` in all admin pages.
3. Create a similar simple layout for the user account area (My Orders, Profile).

**Tip:** use `request()->routeIs('admin.brands*')` to highlight the active menu item.

---

## The CRUD Pattern (use it for Brands, Categories, Products)

Parts 7-25 all follow the same 7 steps. Learn it once, repeat it three times.

1. **Model + migration:** `php artisan make:model Brand -m`
2. **Migration columns,** then `php artisan migrate`.
3. **Model:** set fillable fields.
4. **Controller:** `php artisan make:controller BrandController` with methods index, create, store, edit, update, destroy.
5. **Routes** inside the AuthAdmin group.
6. **Views:** index (table), create (form), edit (form).
7. **Validate** every form with `$request->validate([...])`.

**Route example**

```php
Route::middleware([AuthAdmin::class])->group(function () {
    Route::get('/admin/brands', [BrandController::class, 'index'])->name('admin.brands');
    Route::get('/admin/brand/add', [BrandController::class, 'create'])->name('admin.brand.add');
    Route::post('/admin/brand/store', [BrandController::class, 'store'])->name('admin.brand.store');
    Route::get('/admin/brand/{id}/edit', [BrandController::class, 'edit'])->name('admin.brand.edit');
    Route::put('/admin/brand/update', [BrandController::class, 'update'])->name('admin.brand.update');
    Route::delete('/admin/brand/{id}/delete', [BrandController::class, 'destroy'])->name('admin.brand.delete');
});
```

**Rules for forms:** every form needs `@csrf`. Edit forms need `@method('PUT')`. Delete buttons need `@method('DELETE')`, and ask the person to confirm first.

---

## Stage C: Admin Brands (Parts 7-11)

### Part 7: Brand page

- `php artisan make:model Brand -m`
- Columns: `name`, `slug` (unique), `image` (nullable).
- Show a table with paginate: `Brand::orderBy('id','DESC')->paginate(10)`; show links with `{{ $brands->links() }}`.

### Part 8: Create new brand (with image upload)

1. Install the image package from Packagist: `composer require intervention/image-laravel`.
2. Check GD extension: `php -m`. If gd is missing, open `xampp/php/php.ini`, find `;extension=gd`, remove the `;`, restart Apache.
3. In store(): validate name, slug (unique), image (mimes png,jpg,jpeg; max 2048).
4. Make slug with `Str::slug($request->name)`.
5. Save image: give it a unique file name (`Carbon::now()->timestamp.'.'.$ext`), resize/crop, and save into `public/uploads/brands`.
6. Redirect back with a success message: `->with('status','Brand added successfully')`.

**Form must have:** `enctype='multipart/form-data'`.

### Part 9: Update brand

- Fill the edit form with current values.
- If a new image is uploaded: delete the old file (`File::delete(...)`), then save the new one. If not, keep the old image.
- Slug must be unique but ignore the current row: `unique:brands,slug,`.$request->id.

### Part 10: Delete brand

- Find the row, delete its image file, then `$brand->delete()`.
- Use a confirm popup (JavaScript confirm or SweetAlert) before submitting.

### Part 11: Search and filter brand

- Add a search box that sends `?q=word` (GET).
- In the controller: `Brand::when($request->q, fn($q,$s) => $q->where('name','like',"%$s%"))`.
- Keep pagination working with `->paginate(10)->withQueryString()`.

---

## Stage D: Admin Categories (Parts 12-16)

### Parts 12-15: Category list, create, update, delete

Same CRUD pattern as Brands: columns `name`, `slug`, `image`. Add search and filter on the list page.

### Part 16: Subcategories

**Idea:** a category can have a parent. Root categories have `parent_id = NULL`.

```bash
php artisan make:migration add_parent_id_to_categories_table --table=categories
```

```php
// up()
$table->unsignedBigInteger('parent_id')->nullable()->after('id');
$table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');

// down()
$table->dropForeign(['parent_id']);
$table->dropColumn('parent_id');
```

- `onDelete('cascade')` means deleting a parent also deletes its children.
- In the Category model add two relations: `parent()` (belongsTo) and `children()` (hasMany).
- In the create form add a select box listing root categories (Parent Category, optional).

---

## Stage E: Admin Products (Parts 17-25)

### Part 17: Product page, model, controller

```bash
php artisan make:model Product -m
php artisan make:controller ProductController
```

Suggested columns: `name`, `slug`, `short_description`, `description`, `regular_price`, `sale_price`, `SKU`, `stock_status` (instock/outofstock), `featured` (boolean), `quantity`, `image`, `images` (text, comma list for gallery), `category_id`, `brand_id` (foreign keys).

In the Product model add `category()` and `brand()` relations (belongsTo).

### Part 18: Create product

- Form with main image, gallery images (multiple), category select, brand select.
- Validate prices as numeric; sale\_price should be less than regular\_price.
- Save gallery files in a loop and store the names joined by comma.

### Part 19: Edit product

Same as Part 9, plus handle gallery images (keep old ones if none uploaded).

### Part 20: Delete product

Delete main image and gallery files from disk, then delete the row.

### Part 21: Delete multiple products

- Add a checkbox in each row and a Select All checkbox.
- Submit selected ids as an array: `Product::whereIn('id', $request->ids)->delete()`.

### Part 22: Search and filter products

Search by name or SKU; filter by category, brand, stock status. Combine with `when()` and keep `withQueryString()`.

### Part 23: Print products

- Easiest way: a Print button calling `window.print()` with CSS `@media print` to hide the sidebar and buttons.

### Part 24: Export products to Excel

1. Check zip extension: `php -m`. If missing, enable `extension=zip` in `php.ini` (remove `;`, or add the line), restart Apache.
2. Install: `composer require maatwebsite/excel`
3. Create export class: `php artisan make:export ProductExport --model=Product`
4. Add a route + controller method: `return Excel::download(new ProductExport, 'products.xlsx');`

### Part 25: Product sorting (admin)

- Clickable column titles send `?sort=name&dir=asc`.
- Allow only safe columns: check the value against an array before `orderBy`.

---

## Stage F: Public Shop (Parts 26-32)

### Part 26: Shop product page

```bash
php artisan make:controller ShopController
```

- Create `resources/views/shop/index.blade.php` (use the template's shop grid).
- Route: `Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');`
- Load products: `Product::paginate(12)`.

### Part 27: Product details page

- Route: `/shop/{slug}` and method `details($slug)` using `firstOrFail()`.
- Show gallery, price (show sale price and crossed regular price), stock status, related products (same category, exclude current).

### Part 28: Sorting and products per page

- Dropdown sends `?order=1` (newest), `2` (oldest), `3` (price low-high), `4` (price high-low).
- Another dropdown sends `?size=12|24|48`.
- Always use `->paginate($size)->withQueryString()`.

### Part 29: Filter by brand

- Show brands with checkboxes and the product count.
- Use `whereIn('brand_id', $selectedBrands)`.

### Part 30: Filter by category

- Same idea with `category_id`. Also include subcategory ids when a parent category is chosen.

### Part 31: Filter by price

- Two inputs min and max (or a slider): `whereBetween('regular_price', [$min, $max])`.

### Part 32: Search in shop

- Search box in header sends `?q=chair` to the shop route.
- `where('name','like',"%$q%")`.

**Tip for Parts 28-32:** put all filters into one controller method. Each filter is a `when()` condition so they work together.

---

## Stage G: Shopping Cart (Parts 33-36)

### Part 33: Create the cart service

**Idea:** one class that holds all cart logic, plus a Facade so you can write `Cart::add(...)` anywhere.

```bash
php artisan make:class Services/CartService
php artisan make:class Facades/Cart
```

- CartService keeps items in the session under one key. Each item has product id, name, price, quantity, image.
- Methods to build: `add()`, `update()`, `remove()`, `clear()`, `items()`, `count()`, `subtotal()`.
- Facade class extends `Illuminate\Support\Facades\Facade` and returns the service name from `getFacadeAccessor()`.
- Register the service in `AppServiceProvider` as a singleton.

### Part 34: Add to cart

```bash
php artisan make:controller CartController
```

- Route: `POST /cart/add` with product id and quantity.
- Validate the product exists and is in stock. If the item is already in the cart, increase its quantity.
- Update the header cart badge using `Cart::count()` (replace the fixed number 3 in the header).

### Part 35: View cart

- Route `GET /cart` shows the table: image, name, price, quantity, line total, remove button; and a totals box.
- Show a friendly message if the cart is empty.

### Part 36: Update cart quantity

- Plus/minus buttons submit a small form (or use fetch) with new quantity.
- Quantity less than 1 should become 1 (or remove the item).
- Never trust prices from the browser. Read prices from the database each time.

---

## Common Mistakes Found in the Notes (Fix These)

| Problem | Fix |
| --- | --- |
| Header links use .php (index.php, shop.php) | Use route names: `route('home.index')`, `route('shop.index')` |
| Slider/category images use plain paths (assets/...) | Use `asset('assets/...')` |
| Login 'remember' checkbox named remember\_me | Use `name='remember'` |
| `@error('')` empty in login password | `@error('password')` |
| `@error('mobie')` typo in register | `@error('mobile')` |
| `:value='old(...)'` on plain input | `value='{{ old('email') }}'` |
| AuthAdmin sends error message but no page shows it | Show `session('error')` on the login page |
| Admin registered users: no one is ADM | Set `utype` to ADM manually in the database (or a seeder) |
| migrate:refresh deletes all data | Use only in development |
| Countdown timer date is 2025-12-31 (already past) | Change it to a future date |
| Footer says Surfside Media, fake address and email | Replace with your shop info |

## Checklist Before Starting a New Project

- [ ] Install Laravel and Breeze, set `.env` database
- [ ] Copy layout, assets, home page
- [ ] Add `utype`, AuthAdmin middleware, admin and user pages
- [ ] Build admin CRUD in order: Brand, Category, Product
- [ ] Build public shop: list, details, sort, filters, search
- [ ] Build cart, then (next steps) checkout, orders, payment, order status

## Ideas for Next Parts

- Wishlist (heart button)
- Checkout and Orders table (orders, order\_items)
- Payment (cash on delivery first, then a payment gateway)
- Order status for admin (ordered, delivered, canceled)
- Coupons, product reviews, contact page, slider management in admin
- Deploy to a server (set APP\_ENV=production, APP\_DEBUG=false, run `php artisan config:cache`)


### Part 37: Delete and Clear Cart Item

- **Files touched:**
  - `app/Http/Controllers/CartController.php` (`remove_from_cart`, `clear_cart`)
  - `routes/web.php` (`cart.remove`, `cart.clear`)
  - `resources/views/cart/index.blade.php` (row delete form and "Clear Cart" form)
- **Remove single item:** `Cart::instance('cart')->remove($rowId)` deletes one row by `$rowId` and redirects back with a `success` flash message.
- **Clear full cart:** `Cart::instance('cart')->destroy()` wipes all items from the `cart` instance and redirects back with a `success` flash message.
- **HTTP method spoofing:** HTML forms only send `GET` or `POST`, so use `@csrf` and `@method('DELETE')` to match `Route::delete(...)`.
- **Keep `cart.clear` before `cart.remove` (or constrain `{rowId}`):** Placing `/cart/remove/{rowId}` first with a loose wildcard can risk route matching issues if paths overlap; order `/cart/clear` cleanly and ensure `$item->rowId` is always present.
- **UI & real-store watchouts:**
  - Add a quick confirmation (`onclick="return confirm(...)"`) on **Clear Cart** so users don't wipe a full cart by accident.
  - Show an empty-cart state/message when all items are removed or cleared so the table doesn't look broken.
  - Avoid hardcoding static labels like `Size: S` / `Color: White` or showing strikethrough `$item->price` unless actual variant/sale data supports it.

  ### Part 38: Add Product to Wishlist

- **Files touched:**
  - `app/Http/Controllers/WishlistController.php` (`add_to_wishlist`)
  - `routes/web.php` (`wishlist.add`)
  - `resources/views/shop/index.blade.php` (heart button form)
  - `resources/views/layouts/app.blade.php` (header wishlist counter)
- **Create controller:** `php artisan make:controller WishlistController`
- **Separate cart instance:** `Cart::instance('wishlist')` keeps wishlist items apart from `Cart::instance('cart')`.
- **Add to wishlist:** validate `product_id`, `name`, `quantity`, `price`, skip if the product is already in the wishlist, then `add(...)->associate('App\Models\Product')` and redirect back with a `success` flash message.
- **Route:** `Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');`
- **Shop view:** a POST form with `@csrf` and hidden fields (`product_id`, `name`, `quantity`, `price`) on the heart button. Show `fa-solid fa-heart` if the product is already in the wishlist, otherwise `fa-regular fa-heart`.
- **Header counter:** `{{ Cart::instance('wishlist')->count() }}` in the heart badge.
- **Bugs to watch out for:**
  - `associate('App\Models\{product')` is a typo. Use `'App\Models\Product'`.
  - `regualer_price` is misspelled. Use `regular_price`.
  - Use `?:` instead of `??` for the price fallback, because `??` only handles `null`.
  - Check for duplicates before `add()`, otherwise the package increases the quantity.
- **Next steps:** link the header heart (`href="#"`) to a wishlist page and show the `success` flash message on the shop page.

### Part 39: Show All Wishlist Products

- **Files touched:**
  - `resources/views/wishlist/index.blade.php` (new folder `wishlist` and new file)
  - `app/Http/Controllers/WishlistController.php` (`index`)
  - `routes/web.php` (`wishlist.index`)
  - `resources/views/layouts/app.blade.php` (header heart link)
- **Create the view:** make the folder `resources/views/wishlist/` and the file `index.blade.php` inside it.
- **Route:** `Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');`
- **Controller:** `index()` returns `view('wishlist.index')`.
- **Header link:** change the heart `href="#"` to `href="{{ route('wishlist.index') }}"`.
- **Empty state:** `@if(Cart::instance('wishlist')->content()->count() == 0)` shows the "no items" block, and `@else` shows the table.
- **Loop the items:** `@foreach (Cart::instance('wishlist')->content() as $item)`, then get the product with `Cart::instance('wishlist')->model($item->rowId)`.
- **Skip missing products:** wrap each row in `@if($product)`, so deleted products don't break the table.
- **Row data:**
  - Image: `asset('uploads/products/thumbnails/' . $product->image)`
  - Name and link: `$item->name` with `route('shop.product.details', $product->slug)`
  - Price: `number_format($item->price, 2)`
  - Quantity: `{{ $item->qty }}`
- **Next steps:** the **Add to Cart** and trash buttons are not wired yet. Add them in the next part (remove from wishlist, clear wishlist, move to cart).


### Part 40: Remove Product from Wishlist

- **Files touched:**
  - `app/Http/Controllers/WishlistController.php` (`remove_from_wishlist`, `clear_wishlist`)
  - `routes/web.php` (`wishlist.remove`, `wishlist.clear`)
  - `resources/views/wishlist/index.blade.php` (trash button form in the Action column)
- **Remove single item:** `Cart::instance('wishlist')->remove($rowId)` deletes one row by `$rowId` and redirects back with a `success` flash message.
- **Clear full wishlist:** `Cart::instance('wishlist')->destroy()` wipes all items from the `wishlist` instance and redirects back with a `success` flash message.
- **Routes:**

```php
Route::delete('/wishlist/remove/{rowId}', [WishlistController::class, 'remove_from_wishlist'])->name('wishlist.remove');
Route::delete('/wishlist/remove', [WishlistController::class, 'clear_wishlist'])->name('wishlist.clear');
```

- **View (trash button):** wrap the button in a form that posts to `route('wishlist.remove', $item->rowId)`.

```php
<form action="{{ route('wishlist.remove', $item->rowId) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this item from your wishlist?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-trash-can text-xl"></i></button>
</form>
```

- **HTTP method spoofing:** HTML forms only send `GET` or `POST`, so use `@csrf` and `@method('DELETE')` to match `Route::delete(...)`.
- **Confirm before delete:** `onsubmit="return confirm(...)"` stops accidental removals.
- **Next steps:** add a "Clear Wishlist" form that posts to `route('wishlist.clear')` with `@csrf` and `@method('DELETE')`, then wire the **Add to Cart** button (move to cart).


### Part 41: Move Products from Wishlist to Cart

- **Files touched:**
  - `app/Http/Controllers/WishlistController.php` (`move_to_cart`)
  - `app/Services/CartService.php` (`get`)
  - `routes/web.php` (`wishlist.move.to.cart`)
  - `resources/views/wishlist/index.blade.php` (Add to Cart form in the Add to Cart column)
- **Move to cart flow:** get the item from the `wishlist` instance by `$rowId`, remove it from the wishlist, add it to the `cart` instance with the same id, name, qty and price, then redirect back with a `success` flash message.
- **Controller:**

```php
public function move_to_cart(string $rowId)
{
    $item = Cart::instance('wishlist')->get($rowId);
    Cart::instance('wishlist')->remove($rowId);

    Cart::instance('cart')->add($item->id, $item->name, $item->qty, $item->price)
        ->associate('App\Models\Product');

    return back()->with('success', 'Product moved to cart successfully!');
}
```

- **Service (`CartService`):** added a `get()` helper that returns one cart item by `$rowId`, or `null` if it is not found.

```php
public function get(string $rowId): ?object
{
    return $this->getCart()->get($rowId);
}
```

- **Route:**

```php
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');
```

- **View (Add to Cart button):** wrap the button in a form that posts to `route('wishlist.move.to.cart', $item->rowId)`.

```php
<td class="py-4 px-4" data-label="Add to Cart">
    <form action="{{ route('wishlist.move.to.cart', $item->rowId) }}" method="POST">
        @csrf
        <button type="submit" class="bg-sky-800 text-white px-4 py-2 rounded hover:bg-primary transition text-sm">Add to Cart</button>
    </form>
</td>
```

- **Why `POST`:** moving to cart changes data (adds to cart and removes from wishlist), so it uses `POST` with `@csrf`. No `@method` spoofing is needed.
- **Order matters:** call `get($rowId)` **before** `remove($rowId)`. Once removed, the item is gone and its data can't be read.
- **`associate('App\Models\Product')`:** links the cart row to the `Product` model, so `$item->model` works in the cart view (image, slug, etc.), the same way it does in the wishlist.
- **Same product already in cart:** `add()` with the same product id merges into the existing row and increases its quantity instead of creating a duplicate row.
- **Two instances:** `wishlist` and `cart` are separate instances of the same package, so each one has its own rows and its own `rowId` values.
- **Next steps:** show the cart item count in the header after a move, and add a message or style for the `success` flash on the wishlist page.




 ### Part 42: Admin Create Coupons Page

- **Files touched:**
  - `app/Models/Coupon.php` (new, `php artisan make:model Coupon -m`)
  - `database/migrations/2026_11_27_050351_create_coupons_table.php` (new, created with the model)
  - `app/Http/Controllers/CouponController.php` (new, `php artisan make:controller CouponController`)
  - `routes/web.php` (`admin.coupons`)
  - `resources/views/admin/coupons.blade.php` (new, coupons list page)
  - `resources/views/layouts/admin.blade.php` (Coupons link in the sidebar)
- **Commands:**

```bash
php artisan make:model Coupon -m
php artisan make:controller CouponController
php artisan migrate
```

- **`-m` flag:** creates the model and its migration together, so `Coupon` and the `coupons` table are made in one command.
- **Migration:**

```php
Schema::create('coupons', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->enum('type', ['fixed', 'percent']);
    $table->decimal('value');
    $table->decimal('cart_value');
    $table->date('expiry_date')->default(DB::raw("(DATE(CURRENT_TIMESTAMP))"));
    $table->timestamps();
});
```

- **Columns:**
  - `code`: the coupon code, `unique()` so no two coupons share one.
  - `type`: `fixed` (amount off) or `percent` (percentage off).
  - `value`: the discount amount or percentage.
  - `cart_value`: the minimum cart total needed to use the coupon.
  - `expiry_date`: defaults to today's date.
- **DB import:** `DB::raw(...)` needs `use Illuminate\Support\Facades\DB;` at the top of the migration.
- **Controller:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }
}
```

- **Controller logic:** gets all coupons ordered by `expiry_date` (newest first), splits them into pages of 12 with `paginate(12)`, and sends them to the `admin.coupons` view.
- **Column name must match:** `orderBy('expiry_date', ...)` must use the exact column name from the migration, with an underscore. A wrong name (for example `expiry-data`) throws an SQL error.
- **Route (inside the admin auth group):**

```php
Route::get('/admin/coupons', [CouponController::class, 'coupons'])->name('admin.coupons');
```

- **View:** `coupons.blade.php` is in `resources/views/admin/`, so it is loaded as `admin.coupons`. It uses `@forelse ... @empty ... @endforelse` to show the table, or a "No coupons available" row when there are none.
- **Pagination:** `{{ $coupons->links() }}` shows the page links under the table.
- **Sidebar link (`layouts/admin.blade.php`):**

```php
<a href="{{ route('admin.coupons') }}" class="nav-link flex items-center gap-3 px-4 py-2.5 text-gray-100 hover:text-white hover:bg-gray-800 rounded-lg transition">
    <i class="fa-solid fa-ticket w-5 text-center"></i>
    <span>Coupons</span>
</a>
```

- **View columns:** use `{{ $coupon->code }}` for the code, and `@if($coupon->type == 'fixed')` to show the Fixed or Percent badge. A bare `@if($coupon->type)` is always true for any non-empty string.
- **Next steps:** add the "Add New Coupon" page (form, store method, and validation) and the edit and delete actions.



### Part 43: Admin Add New Coupon

- **Files touched:**
  - `app/Http/Controllers/CouponController.php` (`coupon_add`, `coupon_store`)
  - `routes/web.php` (`admin.coupon.add`, `admin.coupon.store`)
  - `resources/views/admin/coupon-add.blade.php` (new, the add coupon form)
  - `resources/views/admin/coupons.blade.php` (success message and Add New Coupon links)
- **Flow to create a coupon:**
  1. Admin clicks **Add New Coupon** on the coupons page.
  2. `GET /admin/coupon-add` runs `coupon_add()` and shows the form.
  3. Admin fills the form and clicks **Save Coupon**.
  4. `POST /admin/coupon-store` runs `coupon_store()`.
  5. The controller validates the data, saves the coupon, and redirects back to the coupons list with a `success` message.
  6. If validation fails, Laravel goes back to the form, shows the errors, and keeps the old input.
- **Routes (inside the admin auth group):**

```php
Route::get('/admin/coupon-add', [CouponController::class, 'coupon_add'])->name('admin.coupon.add');
Route::post('/admin/coupon-store', [CouponController::class, 'coupon_store'])->name('admin.coupon.store');
```

- **Controller:**

```php
public function coupon_add()
{
    return view('admin.coupon-add');
}

public function coupon_store(Request $request)
{
    $request->validate([
        'code'        => 'required|string|unique:coupons,code',
        'type'        => 'required|in:fixed,percent',
        'value'       => 'required|numeric|min:0',
        'cart_value'  => 'required|numeric|min:0',
        'expiry_date' => 'required|date',
    ]);

    $coupon = new Coupon();
    $coupon->code = $request->code;
    $coupon->type = $request->type;
    $coupon->value = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date = $request->expiry_date;
    $coupon->save();

    return redirect()->route('admin.coupons')->with('success', 'Coupon created successfully');
}
```

- **Validation rules:**
  - `code`: required, text, and `unique:coupons,code` so no two coupons share a code.
  - `type`: must be `fixed` or `percent`.
  - `value` and `cart_value`: required numbers, not below 0.
  - `expiry_date`: required, must be a valid date.
- **View (`coupon-add.blade.php`):** a form with `@csrf` that posts to `route('admin.coupon.store')`. Each field uses `old('field')` to keep the typed value and `@error('field')` to show its error message.
- **Coupons page (`coupons.blade.php`):** the **Add New Coupon** button and the "Create your first coupon" link both use `route('admin.coupon.add')`. A success box shows the flash message:

```php
@if(Session::has('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-6">
        {{ Session::get('success') }}
    </div>
@endif
```

- **Mass assignment:** the controller sets each field one by one, so `$fillable` is not needed in the `Coupon` model.
- **Next steps:** add the edit and delete actions for coupons.



### Part 44: Admin Edit Coupon

- **Files touched:**
  - `app/Http/Controllers/CouponController.php` (`coupon_edit`, `coupon_update`)
  - `routes/web.php` (`admin.coupon.edit`, `admin.coupon.update`)
  - `resources/views/admin/coupon-edit.blade.php` (new, the edit coupon form)
  - `resources/views/admin/coupons.blade.php` (Edit button link)
- **Flow to edit a coupon:**
  1. Admin clicks the **Edit** (pen) icon on a coupon row.
  2. `GET /admin/coupon-edit/{coupon_id}` runs `coupon_edit()` and shows the form filled with the coupon data.
  3. Admin changes the data and clicks **Save Coupon**.
  4. `PUT /admin/coupon-update/{coupon_id}` runs `coupon_update()`.
  5. The controller validates, updates the coupon, and redirects to the coupons list with a `success` message.
  6. If validation fails, Laravel goes back to the form with errors and old input.
- **Routes (inside the admin auth group):**

```php
Route::get('/admin/coupon-edit/{coupon_id}', [CouponController::class, 'coupon_edit'])->name('admin.coupon.edit');
Route::put('/admin/coupon-update/{coupon_id}', [CouponController::class, 'coupon_update'])->name('admin.coupon.update');
```

- **Controller:**

```php
public function coupon_edit(int $coupon_id)
{
    $coupon = Coupon::findOrFail($coupon_id);
    return view('admin.coupon-edit', compact('coupon'));
}

public function coupon_update(Request $request, int $coupon_id)
{
    $request->validate([
        'code'        => 'required|string|unique:coupons,code,' . $coupon_id,
        'type'        => 'required|in:fixed,percent',
        'value'       => 'required|numeric|min:0',
        'cart_value'  => 'required|numeric|min:0',
        'expiry_date' => 'required|date',
    ]);

    $coupon = Coupon::findOrFail($coupon_id);
    $coupon->code = $request->code;
    $coupon->type = $request->type;
    $coupon->value = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date = $request->expiry_date;
    $coupon->save();

    return redirect()->route('admin.coupons')->with('success', 'Coupon updated successfully!');
}
```

- **Key points:**
  - `findOrFail()` returns a 404 page if the coupon id does not exist.
  - `unique:coupons,code,{id}` ignores the current coupon, so saving without changing the code does not fail.
  - The form needs `@csrf` and `@method('PUT')`, because HTML forms only support GET and POST.
- **View (`coupon-edit.blade.php`):** same form as the add page, posting to `route('admin.coupon.update', ['coupon_id' => $coupon->id])`. Each field uses `old('field', $coupon->field)`: it shows the old input after a failed validation, otherwise the saved value. The `type` select marks the saved option with `selected`.
- **Coupons page (`coupons.blade.php`):** the Edit button links to:

```php
<a href="{{ route('admin.coupon.edit', ['coupon_id' => $coupon->id]) }}">
    <i class="fa-solid fa-pen-to-square"></i>
</a>
```

- **Next steps:** add the delete action for coupons (the delete button is still hardcoded with `'SUMMER20', 1`).



### Part 45: Admin Delete Coupon

- **Files touched:**
  - `app/Http/Controllers/CouponController.php` (`coupon_delete`)
  - `routes/web.php` (`admin.coupon.delete`)
  - `resources/views/admin/coupons.blade.php` (delete form, confirm modal, JavaScript)
- **Flow to delete a coupon:**
  1. Admin clicks the **Delete** (trash) icon on a coupon row.
  2. JavaScript `deleteCoupon()` opens a confirm modal with the coupon code.
  3. Admin clicks **Cancel** to close the modal, or **Delete** to confirm.
  4. On confirm, JavaScript submits the hidden form `delete-form-{id}`.
  5. `DELETE /admin/coupon-delete/{coupon_id}` runs `coupon_delete()`.
  6. The controller deletes the coupon and redirects to the coupons list with a `success` message.
- **Route (inside the admin auth group):**

```php
Route::delete('/admin/coupon-delete/{coupon_id}', [CouponController::class, 'coupon_delete'])->name('admin.coupon.delete');
```

- **Controller:**

```php
public function coupon_delete($coupon_id)
{
    $coupon = Coupon::findOrFail($coupon_id);
    $coupon->delete();
    return redirect()->route('admin.coupons')->with('success', 'Coupon deleted successfully.');
}
```

- **Key points:**
  - `findOrFail()` returns a 404 page if the coupon id does not exist.
  - The form needs `@csrf` and `@method('DELETE')`, because HTML forms only support GET and POST.
  - The trash button is `type="button"`, so it does not submit the form directly. It only opens the modal.
  - Each row has its own form with a unique id (`delete-form-{{ $coupon->id }}`), so JavaScript can find the right form.
- **View (`coupons.blade.php`):**
  - Delete form in each row:

```php
<form id="delete-form-{{ $coupon->id }}" action="{{ route('admin.coupon.delete', ['coupon_id' => $coupon->id]) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="button" onclick="deleteCoupon(this, '{{ $coupon->code }}', {{ $coupon->id }})" title="Delete">
        <i class="fa-solid fa-trash"></i>
    </button>
</form>
```

  - Confirm modal (`#deleteModal`): hidden by default, shows the coupon code in `#delete-coupon-code`, with **Cancel** and **Delete** buttons.
- **JavaScript:**
  - `deleteCoupon(button, code, id)`: saves the id, puts the code in the modal text, and shows the modal.
  - `closeModal()`: hides the modal and clears the saved id.
  - **Cancel** button: calls `closeModal()`.
  - **Delete** button: finds `delete-form-{id}` and calls `form.submit()`.
- **Result:** the coupon CRUD is complete: list, add, edit, and delete.
- **Next steps:** apply coupons in the cart and checkout.