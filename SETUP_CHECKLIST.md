# SETUP CHECKLIST - Sistem Authentication Multi-Role

> Status: **WAJIB DIKERJAKAN SEBELUM PRODUCTION**

---

## ✅ Step-by-Step Setup

### 1️⃣ Database & Migration

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

```bash
# Pastikan .env konfigurasi database sudah benar
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartin_app
DB_USERNAME=root
DB_PASSWORD=

# Jalankan migration
php artisan migrate

# Jika pakai session driver: database
php artisan session:table
php artisan migrate
```

**Verifikasi:**
```bash
# Check database connection
php artisan tinker
> DB::connection()->getPDO();
# Jika tidak error, connection OK
```

---

### 2️⃣ Environment & Configuration

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

**File: `.env`**

```env
# ========== APP SETUP ==========
APP_NAME="SmartiN"
APP_ENV=production
APP_DEBUG=false  # CRITICAL: Set false di production!
APP_URL=http://smartin.local

# ========== DATABASE ==========
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartin_app
DB_USERNAME=root
DB_PASSWORD=

# ========== SESSION ==========
SESSION_DRIVER=database  # Atau 'cookie' untuk development
SESSION_LIFETIME=120     # 2 jam
REMEMBER_ME_LIFETIME=525600  # 1 tahun

# ========== LOGGING ==========
LOG_CHANNEL=single
LOG_LEVEL=debug  # Change to 'error' atau 'warning' di production
LOG_DAILY_DAYS=14  # Simpan log 14 hari

# ========== SECURITY ==========
APP_KEY=base64:xxxxxxxxxxxxx  # Generate dengan php artisan key:generate
HASH_DRIVER=bcrypt  # Jangan ubah!
```

**Generate APP_KEY:**
```bash
php artisan key:generate
# Copy output ke .env
```

---

### 3️⃣ Middleware Registration

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

#### Jika Laravel 11 (bootstrap/app.php):

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        // Alias untuk middleware custom
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withRouting(...)
    ->withExceptions(...)
    ->create();
```

#### Jika Laravel 10 (app/Http/Kernel.php):

```php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'role' => \App\Http\Middleware\CheckRole::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
];
```

**Verifikasi:**
```bash
php artisan route:list
# Check apakah middleware 'role' muncul di routes
```

---

### 4️⃣ Seeder Admin User

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

**File: `database/seeders/UserSeeder.php`**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@smartin.app',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Owner User
        User::create([
            'full_name' => 'Owner User',
            'email' => 'owner@smartin.app',
            'username' => 'owner',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        // Kasir User
        User::create([
            'full_name' => 'Kasir User',
            'email' => 'kasir@smartin.app',
            'username' => 'kasir',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);
    }
}
```

**Jalankan Seeder:**
```bash
# Seed ke database
php artisan db:seed --class=UserSeeder

# Atau seeding semua
php artisan db:seed

# Verifikasi user sudah ada
php artisan tinker
> App\Models\User::all();
# Seharusnya ada 3 users
```

---

### 5️⃣ File Permissions (Critical!)

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

```bash
# Linux/Mac
chmod -R 755 app/
chmod -R 755 bootstrap/
chmod -R 755 config/
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/

# Windows (jika dev)
# Pastikan folder storage writable oleh web server

# Verify
ls -la storage/
# Seharusnya punya read/write permission
```

---

### 6️⃣ Routes Verification

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

```bash
# Check routes sudah registered dengan benar
php artisan route:list

# Expected routes:
# GET  /login                    (login view)
# POST /login                    (login submit)
# POST /logout                   (logout)
# GET  /dashboard                (protected)
# GET  /admin                    (admin only)
# GET  /owner                    (owner only)
# GET  /kasir                    (kasir only)
```

---

### 7️⃣ Test Manual

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

#### Test 1: Login Sukses
```
1. Buka http://localhost:8000/login
2. Masukkan: admin@smartin.app / password
3. Klik Login
4. ✓ Harus redirect ke /admin dengan pesan "Selamat datang"
```

#### Test 2: Wrong Password
```
1. Masukkan: admin@smartin.app / wrongpass
2. Klik Login
3. ✓ Harus show error "Email/username atau password salah"
4. ✓ Check storage/logs/laravel.log ada entry error
```

#### Test 3: Rate Limiting
```
1. Login fail 5x berturut-turut
2. Attempt ke-6 langsung
3. ✓ Harus return "Too Many Requests" (HTTP 429)
4. Tunggu 1 menit, coba lagi
```

#### Test 4: Authorization
```
1. Login sebagai kasir
2. Coba buka /admin
3. ✓ Harus error 403 "Anda tidak memiliki akses"
4. ✓ Check laravel.log ada warning unauthorized
```

#### Test 5: Logout
```
1. Login sebagai admin
2. Klik logout
3. ✓ Redirect ke /login
4. ✓ Session cleared
5. Coba back button browser
6. ✓ Jangan bisa masuk kembali
```

---

### 8️⃣ Logging Configuration

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

**File: `config/logging.php`** (sudah default OK, tapi bisa customize)

```php
'single' => [
    'driver' => 'single',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
],

'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => 14,
],
```

**Monitor Log Real-Time:**
```bash
# Watch log file
tail -f storage/logs/laravel.log

# Filter login activity
grep "login\|logout\|role" storage/logs/laravel.log

# Watch dengan timestamps
tail -f storage/logs/laravel.log | grep -E "login|logout|unauthorized"
```

---

### 9️⃣ Production Deployment Checklist

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

```bash
# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compile assets (jika pakai webpack/vite)
npm run build

# Clear cache sebelum deploy
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Verify
echo $APP_ENV
# Harus output: production

echo $APP_DEBUG
# Harus output: false
```

---

### 🔟 Security Hardening

**Status:** [ ] Belum Dikerjakan | [ ] Sedang Dikerjakan | [✓] Selesai

#### .env Security
```env
# JANGAN COMMIT .env ke git
# Tambah ke .gitignore:
echo "/.env" >> .gitignore
echo "/.env.*.php" >> .gitignore

# JANGAN kirim .env via email
# JANGAN simpan password di code
```

#### HTTPS Only (Production)
```php
// config/app.php atau bootstrap/app.php
'url' => env('APP_URL', 'https://smartin.app'),

// Middleware untuk force HTTPS
// app/Http/Middleware/ForceHttps.php
public function handle($request, Closure $next)
{
    if (!$request->secure() && env('APP_ENV') === 'production') {
        return redirect()->secure($request->getRequestUri());
    }
    return $next($request);
}
```

#### Rate Limiting Customize
```php
// config/cache.php
'throttle' => [
    'default' => '60,1',
    'login' => '5,1',  // Login: 5 attempt per minute
    'api' => '60,1',   // API: 60 request per minute
],
```

---

## 🎯 Verification Commands

Jalankan commands ini untuk verify setup:

```bash
# 1. Check connectivity
php artisan tinker
> DB::connection()->getPDO()
> Auth::check()

# 2. Check middleware
php artisan route:list | grep -E "(login|logout|dashboard|admin)"

# 3. Check user
> App\Models\User::count()
> App\Models\User::first()

# 4. Check cache/session
php artisan cache:clear
php artisan session:table
php artisan migrate

# 5. Check log
tail storage/logs/laravel.log

# 6. Check app key
echo $APP_KEY
# Harus output: base64:xxxxx
```

---

## 📊 Status Summary

| Komponen | Status | Verifikasi |
|----------|--------|-----------|
| Database & Migration | [ ] ✓ | Command: `php artisan migrate --status` |
| Environment Variables | [ ] ✓ | File: `.env` |
| Middleware Registration | [ ] ✓ | Command: `php artisan route:list` |
| Sample Users Seeded | [ ] ✓ | Command: `php artisan tinker` → `App\Models\User::all()` |
| File Permissions | [ ] ✓ | Folder: `storage/` dan `bootstrap/cache/` writeable |
| Routes Verified | [ ] ✓ | Command: `php artisan route:list` |
| Manual Testing | [ ] ✓ | Selenium/Browser testing |
| Logging Configuration | [ ] ✓ | File: `storage/logs/laravel.log` ada entries |
| Production Optimization | [ ] ✓ | Command: `php artisan config:cache` |
| Security Hardening | [ ] ✓ | .env di .gitignore, HTTPS configured |

---

## 🆘 Emergency Troubleshooting

### Jika Login Tidak Berfungsi

```bash
# 1. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 2. Check database connection
php artisan tinker
> DB::connection()->getPDO();

# 3. Check user exists
> App\Models\User::where('email', 'admin@smartin.app')->first();

# 4. Verify password hash
$user = App\Models\User::first();
> Hash::check('password', $user->password);
# Harus return true
```

### Jika Rate Limiting Tidak Berfungsi

```bash
# Check cache driver di .env
echo $CACHE_DRIVER
# Jika 'array' (development default), rate limiting tidak jalan
# Ubah ke: CACHE_DRIVER=database

# Test rate limiting
php artisan tinker
> Cache::store('database')->put('test', 'value')
```

### Jika Session Tidak Tersimpan

```bash
# Check session driver
echo $SESSION_DRIVER

# Jika 'database', pastikan table sudah ada
php artisan tinker
> Schema::hasTable('sessions');
# Harus return true

# Jika tidak ada, buat:
php artisan session:table
php artisan migrate
```

---

**Document Generated:** 18 Februari 2026  
**For:** SmartiN Multi-Role Authentication System  
**Requirement:** Setup HARUS selesai sebelum production deployment
