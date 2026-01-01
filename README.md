# Simple E-commerce Shopping Cart Livewire Starter Kit (Laravel 12 + Livewire + TailwindCSS)

A simple, extendable e-commerce shopping cart system built with **Laravel 12**, **Livewire**, and **Tailwind CSS**.  
Supports **user carts**, **stock-aware checkout**, **low stock notifications**, **daily sales reports**, and **activity logging**.

---

## Table of Contents

-   [Features](#features)
-   [Tech Stack](#tech-stack)
-   [Installation & Setup](#installation--setup)
-   [Database Structure](#database-structure)
-   [Jobs & Scheduled Tasks](#jobs--scheduled-tasks)
-   [Activity Logging](#activity-logging)
-   [Testing](#testing)

## Features

-   Browse products with **stock availability**
-   Add products to **user-specific cart**
-   Increment/decrement product quantities
-   Prevent adding more than available stock
-   Checkout with **stock validation**
-   **Low stock email alerts** to admin
-   **Daily sales report** to admin (scheduled cron)
-   Activity log for all user actions

---

## Tech Stack

-   **Backend:** Laravel 12
-   **Frontend:** Livewire
-   **Styling:** Tailwind CSS
-   **Queue / Jobs:** Laravel Queues (database)
-   **Mail:** Laravel Mailables
-   **Command:** Laravel Artisan
-   **Testing:** PHPUnit
-   **Database:** MySQL / MariaDB

---

## Installation & Setup

1. Clone the repository:

```bash
git clone <repo-url>
cd project
```

2. Install dependencies:

```bash
composer install
npm install
npm run dev
```

3. Copy .env and generate key:

```bash
cp .env.example .env
cp .env.example .env.testing (recommended)
php artisan key:generate
```

4. Configure database in .env:

```bash
// .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopping_cart
DB_USERNAME=root
DB_PASSWORD=

// .env.testing
APP_ENV=testing
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopping_cart_test
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders:

```bash
php artisan migrate --seed
php artisan migrate --env=testing
```

5. Start development server:

```bash
composer run dev
```

6. Optional: Start queue worker for jobs:

```bash
php artisan queue:work
```

## Database Structure

Tables:

-   `users`
-   `products` - name, price, stock_quantity, low_stock_threshold
-   `carts` - user-specific cart
-   `cart_items` - items in carts
-   `orders` - completed orders
-   `order_items` - products in orders
-   `activities` - logs user actions

## Jobs & Scheduled Tasks

-   LowStockJob - triggers when stock reaches threshold
-   SendDailySalesReport - cron scheduled for daily email report
-   Uses queued mailables for async delivery

### Scheduling

```bash
// bootstrap\app.php

->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
    $schedule->command('report:daily-sales')->dailyAt('20:00');
})
```

## Activity Logging

-   Every user action is logged in activities table:
-   Cart actions: `cart.added`, `cart.decremented`, `cart.removed`
-   Order placed: order.placed

Each log includes optional meta JSON

### Testing:

```bash
php artisan test

```
