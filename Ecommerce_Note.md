# Laravel 13 E-Commerce Project — Multiple Authentication (Breeze)

> **Goal:** Build a Laravel 13 online shop with **two kinds of login**: a normal **User** and an **Admin**. We use **Laravel Breeze** for login/register, a ready-made **GitHub template** for the shop layout, and **MySQL** for the database.

## Contents

| Part | What you do                            |
| ---- | -------------------------------------- |
| 1    | Create the project and install Breeze  |
| 2    | Layout setup (shop design from GitHub) |
| 3    | MySQL database configuration           |
| 4    | Admin and User authentication          |
| End  | Quick checklist and small fixes        |

**Legend:** 📁 file or folder · 💻 command to run · 🔁 replace old code with new code

---

# Part 1 — Create project and install Breeze

> **Note:** You must already have a Laravel project. If you do not, create one first:

```bash
composer create-project laravel/laravel Project_Folder_Name
```

### Step 1 — Go into the project folder

```bash
cd Project_Folder_Name
```

### Step 2 — Install Laravel Breeze with Composer

```bash
composer require laravel/breeze --dev
```

### Step 3 — Create the Blade login/register files

```bash
php artisan breeze:install blade
```

### Step 4 — Run migrations and build assets

```bash
php artisan migrate
npm install
npm run build


```

```if no see the key = base64.. in .env file show use this command
    cmd : php artisan key:generate

### Step 5 — Run the project

```bash
php artisan serve
```

---

# Part 2 — Layout setup

We take the shop design from a GitHub template and put it into our Laravel project.

### Step 1 — Download the template

- Open: https://github.com/surfsidemedia/Laravel-13-E-Commerce-Project-Template/tree/main
- Download the code and **extract** (unzip) all files.

### Step 2 — Copy assets and uploads

📁 Copy the folders **`assets`** and **`uploads`** from the extracted template into the **`public`** folder of our project.

### Step 3 — Replace the body in `app.blade.php`

📁 Open: `resources/views/layouts/app.blade.php`

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

</body>

🔁 Copy the `<body>` from GitHub `blank.php` and use it to replace the `<body>` in `app.blade.php`:

```blade
<body class="font-sans text-gray-600 antialiased">

    <?php include 'includes/header.php'; ?>

    <!-- Main Content Start -->

           {{ $slot }}

    <!-- Main Content End -->

    <?php include 'includes/footer.php'; ?>

    <?php include 'includes/script.php'; ?>

</body>
```

These three `include` lines are placeholders. In Step 4 we replace them with real code. `{{ $slot }}` is where each page content goes.

### Step 4 — Fill in the head, header, footer and scripts

📁 File: `resources/views/layouts/app.blade.php`

**4.1 — Replace the `<head>`** with the code from GitHub `includes/head.php`:

```blade
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{asset('favicon.ico')}}" rel="shortcut icon" type="image/x-icon" />
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0369a1', // Adjust this to match your brand blue
                        secondary: '#075985',
                    },
                    container: {
                        center: true,
                        padding: '1rem',
                        screens: {
                            xl: '1200px',
                        },
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{asset('assets/css/plugins/pe-icon-7-stroke.css')}}" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

**4.2 — Replace the header line** (`<?php include 'includes/header.php'; ?>`) with the code from GitHub `includes/header.php`. It has a **desktop header**, a **mobile header** and a **mobile sidebar menu**:

> Your original note wrote `headwrap.php` here. The body code in Step 3 uses `header.php`, so use that line.

```blade
<header class="hidden lg:block bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto py-4">
        <div class="flex justify-between items-center">
            <div class="w-1/4">
                <a href="index.php">
                    <img src="{{asset('assets/images/logo.png')}}" alt="Logo" class="max-w-[200px]" />
                </a>
            </div>

            <div class="w-1/2 flex justify-center">
                <nav>
                    <ul class="flex space-x-8 font-medium text-gray-700">
                        <li><a href="index.php" class="hover:text-primary transition">Home</a></li>
                        <li><a href="shop.php" class="hover:text-primary transition">Shop</a></li>
                        <li><a href="cart.php" class="hover:text-primary transition">Cart</a></li>
                        <li><a href="wishlist.php" class="hover:text-primary transition">Wishlist</a></li>
                        <li><a href="contact.php" class="hover:text-primary transition">Contact</a></li>
                    </ul>
                </nav>
            </div>

            <div class="w-1/4 flex justify-end items-center space-x-6">
                <div class="relative group">
                    <button class="text-2xl hover:text-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <div class="absolute right-0 mt-2 w-64 bg-white border shadow-lg p-3 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <form class="flex">
                            <input type="text" placeholder="Search..." class="w-full border p-2 text-sm outline-none focus:border-primary" />
                            <button class="p-2 text-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>
                </div>

                <a href="wishlist.php" class="text-2xl hover:text-primary"><i class="fa-regular fa-heart"></i></a>

                <a href="cart.php" class="text-2xl hover:text-primary relative">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                </a>

                <div class="relative group">
                    <button class="text-2xl hover:text-primary">
                        <i class="fa-regular fa-user"></i>
                        <!-- <span class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-bold uppercase text-lg shadow-sm">
                            <i class="fa-regular fa-user"></i>
                        </span> -->
                    </button>
                    <ul class="absolute right-0 mt-2 w-40 bg-white border shadow-lg py-2 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <li><a href='login.php' class="block px-4 py-2 hover:bg-gray-100 text-sm">Sign In</a></li>
                        <li><a href='My-Account.php' class="block px-4 py-2 hover:bg-gray-100 text-sm">My Account</a></li>
                        <li><a href="login.php" class="block px-4 py-2 hover:bg-gray-100 text-sm">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<header class="lg:hidden bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center mb-4">
            <button id="mobile-menu-btn" class="text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="index.php">
                <img src="{{asset('assets/images/logo.png')}}" alt="Logo" class="max-w-[120px]" />
            </a>

            <div class="flex items-center space-x-5">
                <a href="wishlist.php" class="text-xl hover:text-primary">
                    <i class="fa-regular fa-heart"></i>
                </a>

                <a href="cart.php" class="text-xl hover:text-primary relative">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="absolute -top-2 -right-2 bg-primary text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">3</span>
                </a>

                <div class="relative">
                    <button id="mobile-avatar-button" class="text-xl hover:text-primary focus:outline-none">
                        <i class="fa-regular fa-user"></i>
                    </button>

                    <ul id="avatar-submenu-mobile" class="absolute right-0 mt-3 w-44 bg-white border border-gray-100 shadow-xl py-2 rounded-lg hidden z-[100]">
                        <li><a href="my-account.php" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">My Account</a></li>
                        <li><a href="checkout.php" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">Checkout</a></li>
                        <li class="border-t border-gray-50 mt-1">
                            <a href="login.php" class="block px-4 py-2 hover:bg-gray-100 text-sm font-bold text-primary">Sign In</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="pb-2">
            <form action="shop.php" method="GET" class="relative">
                <input type="text" name="q" placeholder="Search for products..."
                    class="w-full bg-gray-100 border-none px-4 py-2.5 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 z-[60]">
    <div class="p-4 flex justify-between items-center border-b">
        <span class="font-bold text-lg">Menu</span>
        <button id="close-menu-btn" class="text-xl"><i class="pe-7s-close"></i></button>
    </div>
    <nav class="p-4">
        <ul class="space-y-4">
            <li><a href="index.php" class="block hover:text-primary">Home</a></li>
            <li><a href="shop.php" class="block hover:text-primary">Shop</a></li>
            <li><a href="cart.php" class="block hover:text-primary">Cart</a></li>
            <li><a href="wishlist.php" class="block hover:text-primary">Wishlist</a></li>
            <li><a href="contact.php" class="block hover:text-primary">Contact</a></li>
        </ul>
    </nav>
</div>
<div id="menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[55]"></div>
```

**4.3 — Replace the footer and script lines** (`includes/footer.php` and `includes/script.php`) with the code from GitHub `includes/footer.php`. This block has the **footer**, the **back-to-top button** and the **JavaScript** (menus, sliders, tabs, countdown):

```blade
<footer class="bg-sky-800 text-gray-100 py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <img src="{{ asset('assets/images/logo-w.png') }}" alt="Logo" class="mb-6 max-w-[200px]" />
                <ul class="space-y-2 text-sm">
                    <li>ABC, Address Here, Country</li>
                    <li>Call Us: <a href="#" class="hover:text-primary transition">+000 000 0000</a></li>
                    <li>Email: <a href="#" class="hover:text-primary transition">info@surfsidemedia.in</a></li>
                </ul>
                <div class="flex space-x-4 mt-6">
                    <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div>
                <h4 class="text-white font-bold text-lg mb-6">Our Categories</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-primary transition">Category 1</a></li>
                    <li><a href="#" class="hover:text-primary transition">Category 2</a></li>
                    <li><a href="#" class="hover:text-primary transition">Furniture</a></li>
                    <li><a href="#" class="hover:text-primary transition">Lighting</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-lg mb-6">Information</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="about.php" class="hover:text-primary transition">About Us</a></li>
                    <li><a href="#" class="hover:text-primary transition">How to Shop</a></li>
                    <li><a href="#" class="hover:text-primary transition">FAQ</a></li>
                    <li><a href="contact.php" class="hover:text-primary transition">Contact Us</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-lg mb-6">My Account</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="login.php" class="hover:text-primary transition">Sign In</a></li>
                    <li><a href="cart.php" class="hover:text-primary transition">View Cart</a></li>
                    <li><a href="wishlist.php" class="hover:text-primary transition">My Wishlist</a></li>
                    <li><a href="#" class="hover:text-primary transition">Track My Order</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-400 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-sm">&copy; 2026 Surfside Media All rights reserved.</p>
            <img src="{{asset('assets/images/payment.png')}}" alt="Payment" class="mt-4 md:mt-0" />
        </div>
    </div>
</footer>

<button id="back-to-top" class="fixed bottom-8 right-8 bg-primary text-white w-10 h-10 rounded shadow-lg hover:bg-blue-600 transition hidden items-center justify-center z-50">
    <i class="fa-solid fa-chevron-up text-xl"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- Helper: Select Element ---
        const $ = (id) => document.getElementById(id);
        const $$ = (selector) => document.querySelectorAll(selector);

        // --- 1. UI Toggles (Mobile Menu & Avatar) ---
        const toggleUI = (btnId, menuId, overlayId = null) => {
            const btn = $(btnId);
            const menu = $(menuId);
            const overlay = overlayId ? $(overlayId) : null;

            if (!btn || !menu) return;

            const toggle = () => {
                menu.classList.toggle('-translate-x-full');
                menu.classList.toggle('hidden'); // For avatar type menus
                if (overlay) overlay.classList.toggle('hidden');
            };

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggle();
            });

            // Specific for mobile sidebar close button
            if ($(btnId === 'mobile-menu-btn' ? 'close-menu-btn' : null)) {
                $('close-menu-btn').addEventListener('click', toggle);
            }

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.add('-translate-x-full');
                    menu.classList.add('hidden');
                    if (overlay) overlay.classList.add('hidden');
                }
            });
        };

        toggleUI('mobile-menu-btn', 'mobile-sidebar', 'menu-overlay');
        toggleUI('mobile-avatar-button', 'avatar-submenu-mobile');

        // --- 2. Back to Top Button ---
        const backToTopBtn = $('back-to-top');
        if (backToTopBtn) {
            window.addEventListener('scroll', () => {
                const isVisible = window.scrollY > 300;
                backToTopBtn.classList.toggle('hidden', !isVisible);
                backToTopBtn.classList.toggle('flex', isVisible);
            });

            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        // --- 3. Swiper Initializations (Unified Logic) ---
        const initSwiper = (selector, options) => {
            if (document.querySelector(selector)) return new Swiper(selector, options);
        };

        // Hero
        initSwiper('.main-slider', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            autoplay: {
                delay: 5000
            },
        });

        // Product/Category Sliders (Common Breakpoints)
        const productBreakpoints = {
            640: {
                slidesPerView: 2
            },
            768: {
                slidesPerView: 3
            },
            1024: {
                slidesPerView: 4
            }
        };

        initSwiper('.category-slider', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            breakpoints: {
                ...productBreakpoints,
                1024: {
                    slidesPerView: 6
                }
            }
        });

        initSwiper('.brand-slider', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            breakpoints: {
                ...productBreakpoints,
                1024: {
                    slidesPerView: 5
                }
            }
        });

        initSwiper('.featured-slider', {
            slidesPerView: 1,
            spaceBetween: 20,
            navigation: {
                nextEl: '.feat-next',
                prevEl: '.feat-prev'
            },
            breakpoints: productBreakpoints
        });

        initSwiper('.related-slider', {
            slidesPerView: 1,
            spaceBetween: 20,
            navigation: {
                nextEl: '.related-next',
                prevEl: '.related-prev'
            },
            breakpoints: productBreakpoints
        });

        // Gallery Thumbs logic
        const galleryThumbs = initSwiper('.gallery-thumbs', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });

        initSwiper('.gallery-top', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            },
            thumbs: {
                swiper: galleryThumbs
            }
        });

        // --- 4. Tabs Logic ---
        $$('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                $$('.tab-btn').forEach(b => b.classList.remove('active'));
                $$('.tab-content').forEach(c => c.classList.add('hidden'));

                btn.classList.add('active');
                const target = $(btn.getAttribute('data-target'));
                if (target) target.classList.remove('hidden');
            });
        });

        // --- 5. Countdown Timer ---
        const countdownContainer = $('countdown-timer');
        if (countdownContainer) {
            const targetDate = new Date("2025-12-31T00:00:00").getTime();
            const updateTimer = () => {
                const distance = targetDate - new Date().getTime();
                if (distance < 0) {
                    countdownContainer.innerHTML = "EXPIRED";
                    return clearInterval(timerInterval);
                }
                const timeMap = {
                    days: Math.floor(distance / 864e5),
                    hours: Math.floor((distance % 864e5) / 36e5),
                    minutes: Math.floor((distance % 36e5) / 6e4),
                    seconds: Math.floor((distance % 6e4) / 1000)
                };
                Object.keys(timeMap).forEach(unit => {
                    const el = $(unit);
                    if (el) el.innerText = String(timeMap[unit]).padStart(2, '0');
                });
            };
            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer();
        }
    });

    // --- 6. Global Functions ---
    function updateQty(amount) {
        const input = document.getElementById('qty-input');
        if (!input) return;
        let val = parseInt(input.value) + amount;
        input.value = val < 1 ? 1 : val;
    }
</script>
```

**Final shape of `app.blade.php` (map):**

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <!-- 4.1  <head> ... </head> -->
    <body class="font-sans text-gray-600 antialiased">
        <!-- 4.2  header (desktop + mobile + sidebar) -->

        {{ $slot }}   <!-- page content -->

        <!-- 4.3  footer + back-to-top + scripts -->
    </body>
</html>
```

### Step 5 — Create the home page `index.blade.php`

📁 File: `resources/views/index.blade.php`

1. 💻 Create the file:

```bash
php artisan make:view index
```

2. Wrap everything inside the layout component:

```blade
<x-app-layout>
    // code here..
</x-app-layout>
```

3. For `code here..`, copy from GitHub `includes/slider.php` and `index.php`. The final file looks like this:

```blade
<x-app-layout>
    <section class="relative bg-gray-50 overflow-hidden">
        <div class="swiper-container main-slider h-[500px] lg:h-[600px]">
            <div class="swiper-wrapper">
                <div class="swiper-slide flex items-center justify-center">
                    <div class="container mx-auto px-4 grid lg:grid-cols-2 gap-8 items-center h-full">
                        <div class="text-center lg:text-left space-y-4 animate-fadeIn">
                            <h2 class="text-4xl lg:text-6xl font-bold text-gray-800 leading-tight">New Stylish
Decor Furniture</h2>
                            <p class="text-lg text-gray-600">Unique Furniture Style Design for Your Family</p>
                            <a href="shop.php" class="inline-block bg-primary text-white px-8 py-3 rounded hover:bg-blue-600 transition shadow-lg mt-4">Purchase Now</a>
                        </div>
                        <div class="hidden lg:block">
                            <img src="assets/images/slider/slider-item-1.png" alt="Furniture" class="mx-auto" />
                        </div>
                    </div>
                </div>
                <div class="swiper-slide flex items-center justify-center">
                    <div class="container mx-auto px-4 grid lg:grid-cols-2 gap-8 items-center h-full">
                        <div class="text-center lg:text-left space-y-4">
                            <h2 class="text-4xl lg:text-6xl font-bold text-gray-800 leading-tight">Modern Living
Room Sets</h2>
                            <p class="text-lg text-gray-600">Comfort and Style combined.</p>
                            <a href="shop.php" class="inline-block bg-primary text-white px-8 py-3 rounded hover:bg-blue-600 transition shadow-lg mt-4">Shop Now</a>
                        </div>
                        <div class="hidden lg:block">
                            <img src="assets/images/slider/slider-item-2.png" alt="Furniture" class="mx-auto" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-10 text-center uppercase tracking-widest text-gray-800">Popular Categories</h2>

            <div class="swiper-container category-slider overflow-hidden">
                <div class="swiper-wrapper pb-10">

                    <div class="swiper-slide text-center group">
                        <a href="#" class="block bg-white rounded-2xl p-6 transition-all duration-300 border border-transparent hover:border-gray-100 hover:shadow-2xl hover:-translate-y-2">
                            <img src="uploads/categories/1.jpg"
                                class="mx-auto w-24 h-24 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                                alt="Living Room">
                        </a>
                        <h4 class="mt-5 font-semibold text-gray-600 group-hover:text-primary transition-colors uppercase text-xs tracking-widest">
                            Living Room
                        </h4>
                    </div>

                    <div class="swiper-slide text-center group">
                        <a href="#" class="block bg-white rounded-2xl p-6 transition-all duration-300 border border-transparent hover:border-gray-100 hover:shadow-2xl hover:-translate-y-2">
                            <img src="uploads/categories/2.jpg"
                                class="mx-auto w-24 h-24 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                                alt="Bedroom">
                        </a>
                        <h4 class="mt-5 font-semibold text-gray-600 group-hover:text-primary transition-colors uppercase text-xs tracking-widest">
                            Bedroom
                        </h4>
                    </div>

                    <div class="swiper-slide text-center group">
                        <a href="#" class="block bg-white rounded-2xl p-6 transition-all duration-300 border border-transparent hover:border-gray-100 hover:shadow-2xl hover:-translate-y-2">
                            <img src="uploads/categories/3.jpg"
                                class="mx-auto w-24 h-24 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                                alt="Dining">
                        </a>
                        <h4 class="mt-5 font-semibold text-gray-600 group-hover:text-primary transition-colors uppercase text-xs tracking-widest">
                            Dining Room
                        </h4>
                    </div>

                    <div class="swiper-slide text-center group">
                        <a href="#" class="block bg-white rounded-2xl p-6 transition-all duration-300 border border-transparent hover:border-gray-100 hover:shadow-2xl hover:-translate-y-2">
                            <img src="uploads/categories/4.jpg"
                                class="mx-auto w-24 h-24 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                                alt="Office">
                        </a>
                        <h4 class="mt-5 font-semibold text-gray-600 group-hover:text-primary transition-colors uppercase text-xs tracking-widest">
                            Office
                        </h4>
                    </div>

                    <div class="swiper-slide text-center group">
                        <a href="#" class="block bg-white rounded-2xl p-6 transition-all duration-300 border border-transparent hover:border-gray-100 hover:shadow-2xl hover:-translate-y-2">
                            <img src="uploads/categories/5.jpg"
                                class="mx-auto w-24 h-24 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                                alt="Storage">
                        </a>
                        <h4 class="mt-5 font-semibold text-gray-600 group-hover:text-primary transition-colors uppercase text-xs tracking-widest">
                            Storage
                        </h4>
                    </div>

                    <div class="swiper-slide text-center group">
                        <a href="#" class="block bg-white rounded-2xl p-6 transition-all duration-300 border border-transparent hover:border-gray-100 hover:shadow-2xl hover:-translate-y-2">
                            <img src="uploads/categories/6.jpg"
                                class="mx-auto w-24 h-24 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                                alt="Outdoor">
                        </a>
                        <h4 class="mt-5 font-semibold text-gray-600 group-hover:text-primary transition-colors uppercase text-xs tracking-widest">
                            Outdoor
                        </h4>
                    </div>

                    <div class="swiper-slide text-center group">
                        <a href="#" class="block bg-white rounded-2xl p-6 transition-all duration-300 border border-transparent hover:border-gray-100 hover:shadow-2xl hover:-translate-y-2">
                            <img src="uploads/categories/9.jpg"
                                class="mx-auto w-24 h-24 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                                alt="Lighting">
                        </a>
                        <h4 class="mt-5 font-semibold text-gray-600 group-hover:text-primary transition-colors uppercase text-xs tracking-widest">
                            Kitchen & Bar
                        </h4>
                    </div>

                    <div class="swiper-slide text-center group">
                        <a href="#" class="block bg-white rounded-2xl p-6 transition-all duration-300 border border-transparent hover:border-gray-100 hover:shadow-2xl hover:-translate-y-2">
                            <img src="uploads/categories/10.jpg"
                                class="mx-auto w-24 h-24 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                                alt="Decor">
                        </a>
                        <h4 class="mt-5 font-semibold text-gray-600 group-hover:text-primary transition-colors uppercase text-xs tracking-widest">
                            Kids' Room
                        </h4>
                    </div>

                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 border rounded-lg hover:shadow-md transition">
                    <img src="assets/images/icon/icon-1.png" alt="Free Shipping" class="mx-auto mb-4 h-16" />
                    <h3 class="text-xl font-bold mb-2">Free Shipping</h3>
                    <p class="text-sm text-gray-500">Get 10% cash back, free shipping, free returns, and more at 1000+ top retailers!</p>
                </div>
                <div class="text-center p-6 border rounded-lg hover:shadow-md transition">
                    <img src="assets/images/icon/icon-2.png" alt="Safe Payment" class="mx-auto mb-4 h-16" />
                    <h3 class="text-xl font-bold mb-2">Safe Payment</h3>
                    <p class="text-sm text-gray-500">Get 10% cash back, free shipping, free returns, and more at 1000+ top retailers!</p>
                </div>
                <div class="text-center p-6 border rounded-lg hover:shadow-md transition">
                    <img src="assets/images/icon/icon-3.png" alt="Online Support" class="mx-auto mb-4 h-16" />
                    <h3 class="text-xl font-bold mb-2">Online Support</h3>
                    <p class="text-sm text-gray-500">Get 10% cash back, free shipping, free returns, and more at 1000+ top retailers!</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-10 uppercase tracking-widest">Our Partner Brands</h2>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 items-center">

                <a href="/brands/the-oak-studio" class="flex flex-col items-center group">
                    <div class="w-32 h-32 flex items-center justify-center p-4 rounded-xl grayscale group-hover:grayscale-0 group-hover:shadow-xl group-hover:bg-white transition-all duration-300 border border-transparent group-hover:border-gray-100">
                        <img src="uploads/brands/1.jpg" alt="The Oak Studio" class="max-h-full max-w-full object-contain">
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-500 group-hover:text-primary transition-colors uppercase tracking-wider text-center">
                        The Oak Studio
                    </p>
                </a>

                <a href="/brands/luxe-edge" class="flex flex-col items-center group">
                    <div class="w-32 h-32 flex items-center justify-center p-4 rounded-xl grayscale group-hover:grayscale-0 group-hover:shadow-xl group-hover:bg-white transition-all duration-300 border border-transparent group-hover:border-gray-100">
                        <img src="uploads/brands/2.jpg" alt="Luxe Edge" class="max-h-full max-w-full object-contain">
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-500 group-hover:text-primary transition-colors uppercase tracking-wider text-center">
                        Luxe Edge
                    </p>
                </a>

                <a href="/brands/urban-nest" class="flex flex-col items-center group">
                    <div class="w-32 h-32 flex items-center justify-center p-4 rounded-xl grayscale group-hover:grayscale-0 group-hover:shadow-xl group-hover:bg-white transition-all duration-300 border border-transparent group-hover:border-gray-100">
                        <img src="uploads/brands/3.jpg" alt="Urban Nest" class="max-h-full max-w-full object-contain">
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-500 group-hover:text-primary transition-colors uppercase tracking-wider text-center">
                        Urban Nest
                    </p>
                </a>

                <a href="/brands/heritage-wood" class="flex flex-col items-center group">
                    <div class="w-32 h-32 flex items-center justify-center p-4 rounded-xl grayscale group-hover:grayscale-0 group-hover:shadow-xl group-hover:bg-white transition-all duration-300 border border-transparent group-hover:border-gray-100">
                        <img src="uploads/brands/4.jpg" alt="Heritage Wood" class="max-h-full max-w-full object-contain">
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-500 group-hover:text-primary transition-colors uppercase tracking-wider text-center">
                        Heritage Wood
                    </p>
                </a>

                <a href="/brands/pure-pine" class="flex flex-col items-center group">
                    <div class="w-32 h-32 flex items-center justify-center p-4 rounded-xl grayscale group-hover:grayscale-0 group-hover:shadow-xl group-hover:bg-white transition-all duration-300 border border-transparent group-hover:border-gray-100">
                        <img src="uploads/brands/5.jpg" alt="Pure Pine" class="max-h-full max-w-full object-contain">
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-500 group-hover:text-primary transition-colors uppercase tracking-wider text-center">
                        Pure Pine
                    </p>
                </a>

            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-10">New Products</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="group relative">
                    <div class="relative overflow-hidden bg-gray-100 rounded-lg">
                        <a href="details.php">
                            <img src="assets/images/product/product-01.jpg" alt="Product" class="w-full h-[300px] object-cover transition duration-500 group-hover:scale-105" />
                        </a>
                        <div class="absolute top-4 right-4 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition duration-300 transform translate-x-4 group-hover:translate-x-0">
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-search"></i></button>
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-shopping-bag"></i></button>
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-heart"></i></button>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h4 class="text-lg font-medium hover:text-primary transition"><a href="details.php">Elona bedside grey table</a></h4>
                        <p class="text-primary font-bold mt-1">$40.00</p>
                    </div>
                </div>

                <div class="group relative">
                    <div class="relative overflow-hidden bg-gray-100 rounded-lg">
                        <a href="details.php">
                            <img src="assets/images/product/product-02.jpg" alt="Product" class="w-full h-[300px] object-cover transition duration-500 group-hover:scale-105" />
                        </a>
                        <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">-50%</span>
                        <div class="absolute top-4 right-4 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition duration-300 transform translate-x-4 group-hover:translate-x-0">
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-search"></i></button>
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-shopping-bag"></i></button>
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-heart"></i></button>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h4 class="text-lg font-medium hover:text-primary transition"><a href="details.php">Simple minimal Chair</a></h4>
                        <div class="flex justify-center items-center space-x-2 mt-1">
                            <span class="text-primary font-bold">$20.00</span>
                            <span class="text-gray-400 line-through text-sm">$40.00</span>
                        </div>
                    </div>
                </div>

                <div class="group relative">
                    <div class="relative overflow-hidden bg-gray-100 rounded-lg">
                        <a href="details.php">
                            <img src="assets/images/product/product-03.jpg" alt="Product" class="w-full h-[300px] object-cover transition duration-500 group-hover:scale-105" />
                        </a>
                        <div class="absolute top-4 right-4 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition duration-300 transform translate-x-4 group-hover:translate-x-0">
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-search"></i></button>
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-shopping-bag"></i></button>
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-heart"></i></button>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h4 class="text-lg font-medium hover:text-primary transition"><a href="details.php">Pendant Chandelier Light</a></h4>
                        <p class="text-primary font-bold mt-1">$40.00</p>
                    </div>
                </div>

                <div class="group relative">
                    <div class="relative overflow-hidden bg-gray-100 rounded-lg">
                        <a href="details.php">
                            <img src="assets/images/product/product-04.jpg" alt="Product" class="w-full h-[300px] object-cover transition duration-500 group-hover:scale-105" />
                        </a>
                        <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">-50%</span>
                        <div class="absolute top-4 right-4 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition duration-300 transform translate-x-4 group-hover:translate-x-0">
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-search"></i></button>
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-shopping-bag"></i></button>
                            <button class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition"><i class="fas fa-heart"></i></button>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h4 class="text-lg font-medium hover:text-primary transition"><a href="details.php">High quality vase bottle</a></h4>
                        <div class="flex justify-center items-center space-x-2 mt-1">
                            <span class="text-primary font-bold">$20.00</span>
                            <span class="text-gray-400 line-through text-sm">$40.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="relative group overflow-hidden rounded-lg">
                    <img src="assets/images/banner/banner-05.jpg" alt="Banner" class="w-full transition duration-500 group-hover:scale-105" />
                    <div class="absolute inset-0 flex flex-col justify-center items-start p-8">
                        <h6 class="text-sm font-bold uppercase text-gray-500 mb-2">High-Quality</h6>
                        <h3 class="text-2xl font-bold mb-4">New Kitchen <br> Furniture</h3>
                        <a href="shop.php" class="bg-primary text-white px-6 py-2 rounded hover:bg-blue-700 transition">Shop Now</a>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-lg">
                    <img src="assets/images/banner/banner-06.jpg" alt="Banner" class="w-full transition duration-500 group-hover:scale-105" />
                    <div class="absolute inset-0 flex flex-col justify-center items-start p-8">
                        <h6 class="text-sm font-bold uppercase text-gray-500 mb-2">Best-Quality</h6>
                        <h3 class="text-2xl font-bold mb-4">Bed Room <br> Furniture</h3>
                        <a href="shop.php" class="bg-primary text-white px-6 py-2 rounded hover:bg-blue-700 transition">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-100 overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-4xl font-bold">Chair Collection <span class="text-primary">50%</span> Off</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipisicing sed do eiusmol tempor incididunt ut labore et dolore magna aliqua. Ut enim ad mini veniam.
                    </p>

                    <div class="flex space-x-4 text-center" id="countdown-timer">
                        <div class="bg-white p-4 rounded shadow w-20">
                            <span class="block text-2xl font-bold text-primary" id="days">00</span>
                            <span class="text-xs text-gray-500 uppercase">Days</span>
                        </div>
                        <div class="bg-white p-4 rounded shadow w-20">
                            <span class="block text-2xl font-bold text-primary" id="hours">00</span>
                            <span class="text-xs text-gray-500 uppercase">Hrs</span>
                        </div>
                        <div class="bg-white p-4 rounded shadow w-20">
                            <span class="block text-2xl font-bold text-primary" id="minutes">00</span>
                            <span class="text-xs text-gray-500 uppercase">Mins</span>
                        </div>
                        <div class="bg-white p-4 rounded shadow w-20">
                            <span class="block text-2xl font-bold text-primary" id="seconds">00</span>
                            <span class="text-xs text-gray-500 uppercase">Secs</span>
                        </div>
                    </div>

                    <a href="shop.php" class="inline-block bg-primary text-white px-8 py-3 rounded hover:bg-blue-600 transition shadow-lg">Shop Now</a>
                </div>
                <div class="relative">
                    <img src="assets/images/countdown.png" alt="Chair" class="relative z-10 w-full max-w-md mx-auto" />
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 mb-8">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-end mb-8">
                <h2 class="text-2xl font-bold">Featured Products</h2>
                <div class="flex space-x-2">
                    <button class="feat-prev w-10 h-10 rounded-full border border-gray-300 hover:bg-primary hover:border-primary hover:text-white transition flex items-center justify-center"><i class="fa-solid fa-angle-left text-xl"></i></button>
                    <button class="feat-next w-10 h-10 rounded-full border border-gray-300 hover:bg-primary hover:border-primary hover:text-white transition flex items-center justify-center"><i class="fa-solid fa-angle-right text-xl"></i></button>
                </div>
            </div>

            <div class="swiper-container featured-slider overflow-hidden">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="group text-center">
                            <div class="overflow-hidden rounded bg-gray-100 mb-4 relative">
                                <a href="details.php"><img src="assets/images/product/product-12.jpg" class="w-full object-cover group-hover:scale-105 transition duration-500" alt="Prod"></a>
                            </div>
                            <h4 class="font-medium"><a href="details.php">Modern Accent Chair</a></h4>
                            <p class="text-primary font-bold">$40.00</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="group text-center">
                            <div class="overflow-hidden rounded bg-gray-100 mb-4 relative">
                                <a href="details.php"><img src="assets/images/product/product-08.jpg" class="w-full object-cover group-hover:scale-105 transition duration-500" alt="Prod"></a>
                            </div>
                            <h4 class="font-medium"><a href="details.php">Herman Seater Sofa</a></h4>
                            <p class="text-primary font-bold">$40.00</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="group text-center">
                            <div class="overflow-hidden rounded bg-gray-100 mb-4 relative">
                                <a href="details.php"><img src="assets/images/product/product-09.jpg" class="w-full object-cover group-hover:scale-105 transition duration-500" alt="Prod"></a>
                            </div>
                            <h4 class="font-medium"><a href="details.php">Reece Seater Sofa</a></h4>
                            <p class="text-primary font-bold">$40.00</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="group text-center">
                            <div class="overflow-hidden rounded bg-gray-100 mb-4 relative">
                                <a href="details.php"><img src="assets/images/product/product-10.jpg" class="w-full object-cover group-hover:scale-105 transition duration-500" alt="Prod"></a>
                            </div>
                            <h4 class="font-medium"><a href="details.php">Round Swivel Chair</a></h4>
                            <p class="text-primary font-bold">$40.00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
```

### Step 6 — Create `HomeController`

📁 File: `app/Http/Controllers/HomeController.php`

1. 💻 Create the file:

```bash
php artisan make:controller HomeController
```

2. Write this method inside the class:

```php
public function index(){
    return view('index');
}
```

### Step 7 — Add the home route

📁 File: `routes/web.php`

```php
<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home.index');   // <-- new code

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

---

# Part 3 — MySQL database configuration

### Step 1 — Set the database in `.env`

📁 File: `.env`

> Tip: create an empty database named `db_ecommerse` in MySQL (for example in phpMyAdmin) first.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_ecommerse
DB_USERNAME=root
DB_PASSWORD=
```

### Step 2 — Create the tables with migration

```bash
php artisan migrate
```

---

# Part 4 — Admin and User authentication

**How it works:**

- Each user has a **`utype`** column: `USER` (default) or `ADM` (administrator).
- A middleware called **`AuthAdmin`** only lets `ADM` users enter admin pages.
- After login, the **My Account** link goes to the **admin page** if the user is `ADM`, or to the **user page** if not.
- Steps 9–12 work together, so test after Step 12.

> Tip: to make someone an admin, change `utype` to `ADM` for that user in the `users` table.

### Step 1 — Add `mobile` and `utype` columns to the users table

📁 File: `database/migrations/…_create_users_table.php` — add these two lines inside the `users` table:

```php
$table->string('mobile')->unique();
$table->string('utype')->default('USER')->comment('USER for regular users, ADM for administrators');
```

### Step 2 — Allow `mobile` in the User model

📁 File: `app/Models/User.php`

```php
#[Fillable(['name', 'email', 'password', 'mobile'])]
```

### Step 3 — Save `mobile` when registering

📁 File: `app/Http/Controllers/Auth/RegisteredUserController.php` — update the `store` method (new lines are marked):

```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'mobile' => ['required', 'string', 'digits:10', 'unique:'.User::class],   // <-- new
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'mobile' => $request->mobile,   // <-- new
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect(route('dashboard', absolute: false));
}
```

### Step 4 — Add the Mobile field to the register form

📁 File: `resources/views/auth/register.blade.php` — add the Mobile block under the Email block:

```blade
<!-- Email Address -->
<div class="mt-4">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<!-- Mobile  (new) -->
<div class="mt-4">
    <x-input-label for="mobile" :value="__('Mobile')" />
    <x-text-input id="mobile" class="block mt-1 w-full" type="text" name="mobile" :value="old('mobile')" required autocomplete="mobile" />
    <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
</div>
```

### Step 5 — Migrate again (refresh)

```bash
php artisan migrate:refresh
```

> ⚠️ `migrate:refresh` deletes all data in your tables and builds them again.

### Step 6 — Create the `AuthAdmin` middleware

📁 File: `app/Http/Middleware/AuthAdmin.php`

1. 💻 Create the file:

```bash
php artisan make:middleware AuthAdmin
```

2. Code:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;   // <-- new

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->utype == 'ADM') {   // <-- new
            return $next($request);
        } else {
            return redirect()->route('login')->with('error', 'You must be an administrator to access this page.');   // <-- new
        }
    }
}
```

### Step 7 — Create the admin page

📁 File: `resources/views/admin/index.blade.php` (create the `admin` folder first)

```blade
<x-app-layout>
    <h1>Admin Dashboard</h1>
</x-app-layout>
```

### Step 8 — Create the user page

📁 File: `resources/views/users/index.blade.php` (create the `users` folder first)

```blade
<x-app-layout>
    <h1>Users Dashboard</h1>
</x-app-layout>
```

### Step 9 — Update the routes

📁 File: `routes/web.php` (lines marked `// new` are what we add now):

```php
<?php

use App\Http\Controllers\AdminController;     // new
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;      // new
use App\Http\Middleware\AuthAdmin;             // new
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home.index');   // from Part 2 (Layout setup)

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');   // new
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware([AuthAdmin::class])->group(function () {   // new
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});

require __DIR__.'/auth.php';
```

### Step 10 — Create `AdminController`

📁 File: `app/Http/Controllers/AdminController.php`

1. 💻 Create the file:

```bash
php artisan make:controller AdminController
```

2. Code:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
}
```

### Step 11 — Create `UserController`

📁 File: `app/Http/Controllers/UserController.php`

1. 💻 Create the file:

```bash
php artisan make:controller UserController
```

2. Code:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }
}
```

### Step 12 — Update the account menu in `app.blade.php`

📁 File: `resources/views/layouts/app.blade.php`

**12.1 — Find this code** (the old user icon and menu):

```blade
<div class="relative group">
    <button class="text-2xl hover:text-primary">
        <i class="fa-regular fa-user"></i>
        <!-- <span class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-bold uppercase text-lg shadow-sm">
            <i class="fa-regular fa-user"></i>
        </span> -->
    </button>
    <ul class="absolute right-0 mt-2 w-40 bg-white border shadow-lg py-2 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
        <li><a href='login.php' class="block px-4 py-2 hover:bg-gray-100 text-sm">Sign In</a></li>
        <li><a href='My-Account.php' class="block px-4 py-2 hover:bg-gray-100 text-sm">My Account</a></li>
        <li><a href="login.php" class="block px-4 py-2 hover:bg-gray-100 text-sm">Sign Out</a></li>
    </ul>
</div>
```

**12.2 — 🔁 Replace it with this code:**

```blade
<div class="relative group">
    <button class="text-2xl hover:text-primary">
        @auth
            <span class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-bold uppercase text-lg shadow-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
        @else
            <span class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-bold uppercase text-lg shadow-sm">
                <i class="fa-regular fa-user"></i>
            </span>
        @endauth
    </button>
    <ul class="absolute right-0 mt-2 w-40 bg-white border shadow-lg py-2 rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
        @guest
            <li><a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-gray-100 text-sm">Sign In</a></li>
            <li><a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-gray-100 text-sm">Sign Up</a></li>
        @else
            <li>
                <a href="{{ Auth::user()->utype === 'ADM' ? route('admin.index') : route('users.index') }}" class="block px-4 py-2 hover:bg-gray-100 text-sm">My Account</a>
            </li>
            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 hover:bg-gray-100 text-sm">Sign Out</a>
            </li>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        @endguest
    </ul>
</div>
```

What the new code does:

- **Logged in (`@auth`):** shows the first letter of the user name in a blue circle.
- **Not logged in (`@guest`):** shows the user icon, with **Sign In** and **Sign Up**.
- **My Account:** goes to the admin page for `ADM`, otherwise to the user page.
- **Sign Out:** sends a hidden POST form with `@csrf`.

---

# Quick checklist

- [ ] Part 1: project created, Breeze installed, `php artisan serve` works
- [ ] Part 2: `assets` + `uploads` in `public`, `app.blade.php` updated, `index.blade.php`, `HomeController` and home route done
- [ ] Part 3: `.env` set, database created, `php artisan migrate` done
- [ ] Part 4: `mobile` + `utype` columns, register form, `AuthAdmin`, admin/user pages, routes, controllers, account menu
- [ ] Test: register a user, log in, open My Account; set `utype` to `ADM` and check the admin page

# Part 5 — Changing the login and Registration page Layout

### step1

**1 — 🔁 Replace from this code in auth/login.blade.php this code:**
Session Status
<x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

**2 — 🔁 result code in auth/login.blade.php code in below code by from github file (register.php):**

<x-app-layout>

    <!-- Main Content Start -->

    <div class="relative bg-sky-700 text-white h-64 flex items-center justify-center bg-cover bg-center" style="background-image: url('assets/images/page-banner.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-4xl font-bold mb-2">Login</h2>
            <ul class="flex justify-center space-x-2 text-sm">
                <li><a href='{{route("home.index")}}' class="hover:text-primary">Home</a></li>
                <li>/</li>
                <li class="text-primary">Login</li>
            </ul>
        </div>
    </div>

    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="flex justify-center">
                <div class="w-full lg:w-1/2">
                    <div class="bg-gray-50 border rounded-lg p-8 shadow-sm">
                        <h4 class="text-2xl font-bold text-center mb-6">Login to Your Account</h4>

                        <form  method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf
                            <div>
                                <input name="email" id="email" type="email" placeholder="Email *" class="w-full border p-3 rounded focus:outline-none focus:border-primary transition" :value="old('email')" required autofocus autocomplete="username" />
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                                @enderror
                            </div>

                            <div>
                                <input id="password" name="password" type="password" placeholder="Password" class="w-full border p-3 rounded focus:outline-none focus:border-primary transition" required autocomplete="current-password"  />
                                @error('')
                                    <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                                @enderror
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="remember_me" name="remember_me" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer" />
                                <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">Remember me</label>
                            </div>

                            <div>
                                <button type="submit" class="w-full bg-primary text-white font-medium py-3 rounded hover:bg-blue-600 transition shadow-lg">
                                    Login
                                </button>
                            </div>
                        </form>

                        <div class="mt-6 text-center text-sm space-y-2">
                            <p><a href='{{route("password.request")}}' class="text-gray-500 hover:text-primary transition">Lost your password?</a></p>
                            <p class="text-gray-600">
                                No account?
                                <a href='{{route("register")}}' class="text-primary font-bold hover:underline">Create one here.</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-app-layout>

### step2

**1 — 🔁 Replace from this code in auth/register.blade.php this code by from github file (register.php):**

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <!-- Mobile ================= -->
        <div class="mt-4">
            <x-input-label for="mobile" :value="__('Mobile')" />
            <x-text-input id="mobile" class="block mt-1 w-full" type="text" name="mobile" :value="old('mobile')" required autocomplete="mobile" />
            <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

**2 — 🔁 result code in auth/register.blade.php code:**

<x-app-layout>
     <!-- Main Content Start -->

    <div class="relative bg-sky-700 text-white h-64 flex items-center justify-center bg-cover bg-center" style="background-image: url('assets/images/page-banner.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-4xl font-bold mb-2">Register</h2>
            <ul class="flex justify-center space-x-2 text-sm">
                <li><a href='{{route("home.index")}}' class="hover:text-primary">Home</a></li>
                <li>/</li>
                <li class="text-primary">Register</li>
            </ul>
        </div>
    </div>

    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="flex justify-center">
                <div class="w-full lg:w-1/2">
                    <div class="bg-gray-50 border rounded-lg p-8 shadow-sm">
                        <h4 class="text-2xl font-bold text-center mb-2">Create New Account</h4>
                        <p class="text-center text-sm mb-8 text-gray-500">
                            Already have an account?
                            <a href='{{route("login")}}' class="text-primary hover:underline">Log in instead!</a>
                        </p>

                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf
                            <div >

                                <input type="text" id="name" name="name" placeholder="Name" class="w-full border p-3 rounded focus:outline-none focus:border-primary transition" :value="old('name')" required autofocus autocomplete="name" />
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="email" id="email" name="email" placeholder="Email Address *" class="w-full border p-3 rounded focus:outline-none focus:border-primary transition" :value="old('email')" required autocomplete="username"  />
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="text" name="mobile" id="mobile"  placeholder="Mobile *" class="w-full border p-3 rounded focus:outline-none focus:border-primary transition" :value="old('mobile')" required autocomplete="mobile"  />
                                @error('mobie')
                                    <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="password" id="password" name="password" placeholder="Password" class="w-full border p-3 rounded focus:outline-none focus:border-primary transition" required autocomplete="new-password" />
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                                @enderror
                            </div>
                            <div>
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" class="w-full border p-3 rounded focus:outline-none focus:border-primary transition" required autocomplete="new-password" />
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-primary text-white font-medium py-3 rounded hover:bg-blue-600 transition shadow-lg">
                                    Register
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <!-- Main Content End -->

</x-app-layout>

# Part 6 — Changing the Admin and User Dashboard Layout

//////////////////=================

# Part 7 -- Admin Brand Page

**1. Create Model (Brand file with migration Brand)** ->app/Models/Brand

cmd : php artisan make:model Brand -m

# Part 8 -- Admin Create New Brand

-> install image-larave package in packagist webist by shearch : image-laravel
and copy past to run cmd ...
-> check gd in php  
 cmd php -m
if no gd -> go to file php.ini in xammpp/php folder and search: ;g extension=gd -> remove: ; in front of g extension=gd

# Part 9 -- Admin Update Brand

# Part 10 -- Admin Delete Brand

# Part 11 -- Search and Filter Brand

//////////////////=================

# Part 12 -- Admin Category , Shearch and Filter Category

# Part 13 -- Admin Create new Category

# Part 14 -- Admin Update Category

# Part 15 -- Admin Delete Category

//////////////////=================

# Part 16 -- Admin Create Subcategories

**1.create migration add parent_id into table categories**
cmd : php artisan make:migration add_parent_id_to_categories_table --table=categories
===============> **1.1 Note** <=================

# Summary: `Schema::table(...)` Body (Self-Referencing `parent_id`)

Based on the `Schema::table('categories', ...)` body from your migration, here is a concise Markdown summary:

## What the `Schema` Body Does

- **`up()` (`Schema::table('categories', ...)`):** Alters the existing `categories` table to add a self-referencing `parent_id` foreign key for nested/parent-child categories.
    - Adds an `unsignedBigInteger('parent_id')` column (`nullable()`, placed `after('id')`) so root categories can have no parent (`NULL`).
    - Links `parent_id` to `categories.id` with `onDelete('cascade')` so deleting a parent category automatically deletes its child categories.
- **`down()` (`Schema::table('categories', ...)`):** Reverts the change cleanly by dropping the `parent_id` foreign key constraint first, then dropping the `parent_id` column.

## Quick Reference

```php
// Add parent_id referencing categories.id
$table->unsignedBigInteger('parent_id')->nullable()->after('id');$table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');

// Rollback
$table->dropForeign(['parent_id']);$table->dropColumn('parent_id');
```

# Part 17 -- Admin Product page

**1.create model and migration Product**
cmd : php artisan make:model Product -m
**2.create controller ProductController**
cmd : php artisan make:controller ProductController

# Part 18 -- Create new Product

# Part 19 -- Edit Product

# Part 20 -- Delete Product

# Part 21 -- Delete multiple product

# Part 22 -- Shearch and filter Product

# Part 23 -- Print Product

# Part 24 -- Exporting product into excel

**1.Install package from packagist**

- check by cmd : php -m
    - if no zip
      =>into file php.ini in xampp to search " ;extension zip " and remove ";"
      =>into file php.ini if no see " ;extension zip " add it by by self "extension zip"
- search maatwebsite/excel ->website Packagist
  run this cmd : composer require maatwebsite/excel
- make export file in Folder App in project :
  cmd : php artisan make:export ProductExport --model=Product

# Part 25 -- Product sorting feature

# Part 26 -- Creaete Shop product page

**1.Create a file controller 'ShowController'**

- cmd : php artisan make:controller ShopController
  **2.create a shop/index.blade.php page in view folder**

# Part 27 -- Product Details Page

# Part 28 -- Product Sorting and Products Per Page

# Part 29 -- Product Filter By Brand in Shop page

# Part 30 -- Product Filter By Category in Shop page

# Part 31 -- Product Filter By Price in Shop page

# Part 32 -- Product Search in Shop page

# Part 33 -- Create Shoping Cart

    create a class name Services/CartService in APP
    -> cmd: php artisan make:class Services/CartService
    create a class name Facades/Cart
    -> cmd: php artisan make:class Facades/Cart in APP

# Part 34 -- Add to Cart

    create controller name CartController
    cmd: php artisan make:controller CartController

# Part 35 -- View Cart ✔️

# Part 36 -- Update cart Qunatity ✔️

# Part 37: Delete and Clear Cart Item

