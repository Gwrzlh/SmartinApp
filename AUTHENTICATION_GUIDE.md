# Panduan Sistem Authentication Multi-Role yang Aman

> 📌 **Tanggal:** 18 Februari 2026  
> 📌 **Project:** SmartiN (Smart Integration System)  
> 📌 **Focus:** Sistem Login yang Kokoh dan Aman

---

## 📋 Daftar Isi
1. [Alur Login](#alur-login)
2. [Penjelasan Detail Setiap Komponen](#penjelasan-detail-setiap-komponen)
3. [Security Features](#security-features)
4. [Konfigurasi yang Diperlukan](#konfigurasi-yang-diperlukan)
5. [Testing & Monitoring](#testing--monitoring)
6. [Troubleshooting](#troubleshooting)

---

## 🔐 Alur Login

```
┌─────────────────────────────────────────────────────────────┐
│                    USER SUBMIT LOGIN FORM                   │
│                  (Email/Username + Password)                │
└────────────────────┬────────────────────────────────────────┘
                     │
     ┌───────────────▼──────────────────┐
     │  MIDDLEWARE: throttle:5,1        │
     │  (Max 5 attempts per minute)     │
     └───────────────┬──────────────────┘
                     │
     ┌───────────────▼──────────────────┐
     │  AUTHCONTROLLER::DOLOGIN()       │
     │                                  │
     │  1. Validasi Input               │
     │  2. Cari User (email/username)   │
     │  3. Verifikasi Password Hash     │
     │  4. Create Session               │
     │  5. Log Activity                 │
     │  6. Redirect by Role             │
     └───────────────┬──────────────────┘
                     │
     ┌───────────────▼──────────────────┐
     │  MIDDLEWARE: 'auth'              │
     │  (Cek session ada/valid)         │
     └───────────────┬──────────────────┘
                     │
     ┌───────────────▼──────────────────┐
     │  MIDDLEWARE: 'role:admin'        │
     │  (Cek role user sesuai)          │
     └───────────────┬──────────────────┘
                     │
     ┌───────────────▼──────────────────┐
     │  CONTROLLER ACTION               │
     │  (Eksekusi logic controller)     │
     └───────────────┬──────────────────┘
                     │
     ┌───────────────▼──────────────────┐
     │  RETURN VIEW                     │
     │  (Tampil halaman sesuai role)    │
     └───────────────────────────────────┘
```

---

## 🔍 Penjelasan Detail Setiap Komponen

### 1. INPUT VALIDATION (Validasi Input)

**File:** `app/Http/Controllers/authController.php` - Method `doLogin()`

```php
$credentials = $request->validate([
    'email_or_username' => 'required|string|min:3|max:255',
    'password' => 'required|string|min:6',
]);
```

**Penjelasan:**
- `required` = Field tidak boleh kosong
- `string` = Harus tipe string, bukan angka/array
- `min:3` = Minimal 3 karakter (prevent single char)
- `max:255` = Maksimal 255 karakter (sesuai DB)
- `min:6` pada password = Minimal 6 karakter (standard security)

**Keamanan:**
- ✅ Mencegah SQL injection (Laravel escapes otomatis)
- ✅ Mencegah XSS attack
- ✅ Reject data yang tidak sesuai format

---

### 2. USER LOOKUP (Mencari User)

```php
$user = User::where('email', $credentials['email_or_username'])
    ->orWhere('username', $credentials['email_or_username'])
    ->first();
```

**Penjelasan:**
- User bisa login dengan **email ATAU username**, lebih fleksibel
- `first()` = Ambil 1 row pertama saja (efficient)
- `orWhere` = Gunakan OR query

**Security Point:**
- Tidak langsung memberikan error "user tidak ditemukan"
- Nanti error message untuk email/password sama (prevent user enumeration)

---

### 3. PASSWORD VERIFICATION (Verifikasi Password)

```php
if (!Hash::check($credentials['password'], $user->password)) {
    Log::warning('Login attempt dengan password salah', [...]); 
    throw ValidationException::withMessages([...]);
}
```

**Penjelasan:**
- `Hash::check()` = Verifikasi password dengan hash di database
- Password **tidak disimpan plain text**, tapi hash menggunakan bcrypt
- Bcrypt = One-way hashing (tidak bisa decrypt)

**Keamanan:**
- ✅ Password tidak bisa dibaca meski DB bocor
- ✅ Brute force attack akan sangat lambat (bcrypt intentionally slow)
- ✅ Setiap password berbeda hash (salt included)

**Contoh:**
```
Password input: "password123"
Password di DB: "$2y$12$abcdefghijk...xyz" (hash)

Hash::check("password123", "$2y$12$abc...") 
// TRUE jika cocok, FALSE jika tidak
```

---

### 4. SESSION CREATION & REGENERATION

```php
Auth::login($user, remember: $request->boolean('remember'));
$request->session()->regenerate();
```

**Penjelasan:**
- `Auth::login()` = Buat session untuk user
- `remember: true` = Jika user check "Remember Me", simpan cookie jangka panjang
- `session()->regenerate()` = **PENTING** - ubah session ID

**Keamanan:**
- ✅ Prevent session fixation attack
- ✅ Session ID lama tidak valid lagi
- ✅ Attacker tidak bisa masuk dengan session ID sebelumnya

**Session Fixation Attack (tanpa regenerate):**
```
1. Attacker buat session dgn ID "abc123"
2. Attacker kirim link ke victim: example.com?PHPSESSID=abc123
3. Victim klik, system login dgn session ID "abc123" yg sudah attacker tahu
4. Attacker bisa pake session ID "abc123" untuk akses account victim
```

---

### 5. AUDIT LOGGING (Pencatatan Aktivitas)

```php
Log::info('User berhasil login', [
    'user_id' => $user->id,
    'email' => $user->email,
    'role' => $user->role,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now(),
]);
```

**Penjelasan:**
- Setiap activity login dicatat di file log
- Info yang dicatat: user ID, email, role, IP address, device info, waktu

**Keamanan:**
- ✅ Bisa detect jika ada suspicious login attempts
- ✅ Compliance dengan SOP audit trail
- ✅ Historical record jika ada security issue

**Di mana log tersimpan?**
```
storage/logs/laravel.log
```

**Contoh isi log (successful login):**
```
[2026-02-18 10:30:45] production.INFO: User berhasil login {
    "user_id": 1,
    "email": "admin@smartin.app",
    "role": "admin",
    "ip": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "timestamp": "2026-02-18 10:30:45"
}
```

**Contoh isi log (failed login):**
```
[2026-02-18 10:30:20] production.WARNING: Login attempt dengan password salah {
    "user_id": 5,
    "email": "student@smartin.app",
    "ip": "192.168.1.100",
    "timestamp": "2026-02-18 10:30:20"
}

[2026-02-18 10:30:10] production.WARNING: Login attempt dengan user tidak ditemukan {
    "identifier": "nonexistent@smartin.app",
    "ip": "203.0.113.50",
    "timestamp": "2026-02-18 10:30:10"
}
```

---

### 6. ROLE-BASED REDIRECT

```php
private function redirectByRole($user)
{
    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard')
            ->with('success', 'Selamat datang, Admin!'),
        'owner' => redirect()->route('owner.dashboard')
            ->with('success', 'Selamat datang, Owner!'),
        'kasir' => redirect()->route('kasir.dashboard')
            ->with('success', 'Selamat datang, Kasir!'),
    };
}
```

**Penjelasan:**
- Setelah login sukses, redirect ke dashboard masing-masing role
- Admin → `/admin`, Owner → `/owner`, Kasir → `/kasir`
- Dengan pesan success "Selamat datang..."

**UX Benefit:**
- User langsung diarahkan ke halaman yang relevan dengan role-nya
- Tidak perlu manual buka halaman lain

---

### 7. MIDDLEWARE THROTTLING (Rate Limiting)

**Di routes/web.php:**
```php
Route::post('login', [authController::class, 'doLogin'])
    ->middleware(['guest', 'throttle:5,1']);
    // Max 5 login attempts per 1 minute
```

**Penjelasan:**
- `throttle:5,1` = Max 5 requests per 1 minute
- Jika lebih dari 5 attempts, return HTTP 429 (Too Many Requests)

**Keamanan:**
- ✅ Prevent brute force attack
- ✅ Limit automated attack attempts
- ✅ Attacker tidak bisa login dgn cepat

**Tabel Brute Force dengan/tanpa Rate Limiting:**

#### Tanpa Rate Limiting:
```
Attacker bisa coba 1000 password dalam 10 menit
Waktu total: 1000 attempt / 1000 per menit = 1 menit (jika automated)
Kesimpulan: SANGAT RAWAN
```

#### Dengan throttle:5,1:
```
Attacker hanya bisa coba 5 password per menit
5 password/menit × 60 menit = 300 password per jam
1000 password perlukan: 1000 / 5 = 200 menit (~3 jam)
Tapi setiap failed attempt harus tunggu 1 menit lagi
Kesimpulan: LEBIH AMAN
```

---

### 8. MIDDLEWARE GUEST

```php
Route::get('login', [...])
    ->middleware('guest');
```

**Penjelasan:**
- `guest` middleware = Hanya untuk user yang BELUM login
- Jika sudah login, redirect ke `/dashboard`

**Use Case:**
```
User A sudah login, coba buka /login langsung
→ Middleware 'guest' detect user sudah login
→ Redirect ke /dashboard
→ User tidak perlu masukin password lagi
```

---

### 9. MIDDLEWARE AUTH (Session Check)

```php
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [...]);
    Route::post('logout', [...]);
});
```

**Penjelasan:**
- `auth` middleware = Check apakah user sudah login
- Jika belum login, redirect ke `/login`

**Alur:**
```
User tidak login → akses /dashboard
         ↓
Middleware 'auth' check session
         ↓
Session tidak ada → redirect /login
         ↓
User login → session ada
         ↓
Middleware pass → akses dashboard
```

---

### 10. MIDDLEWARE ROLE (Authorization Check)

```php
Route::middleware('role:admin')->group(function () {
    Route::get('admin', [...]);
});
```

**Penjelasan:**
- `role:admin` middleware = Check apakah role user adalah admin
- Jika bukan admin, return error 403

**Alur:**
```
User (role: student) akses /admin
         ↓
Middleware 'auth' pass (user sudah login)
         ↓
Middleware 'role:admin' check role
         ↓
Role "student" ≠ "admin"
         ↓
Abort 403 (Forbidden)
```

---

### 11. LOGOUT (Keluar Sistem)

```php
public function logout(Request $request)
{
    $user = Auth::user();
    
    Log::info('User logout', [
        'user_id' => $user->id,
        'email' => $user->email,
        'ip' => $request->ip(),
        'timestamp' => now(),
    ]);

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->with('success', 'Anda telah logout. Sampai jumpa!');
}
```

**Penjelasan:**
- `Auth::logout()` = Hapus session user
- `session()->invalidate()` = Destroy semua session data
- `regenerateToken()` = Generate CSRF token baru

**Keamanan:**
- ✅ Session user sudah tidak valid
- ✅ Cookie session dihapus
- ✅ User tidak bisa kembali dgn browser back button
- ✅ Attacker tidak bisa gunakan session yang sudah logout

---

### 12. USER MODEL VALIDATION (Validasi Role)

```php
protected static function booted()
{
    static::saving(function ($model) {
        $validRoles = ['admin', 'owner', 'kasir'];

        if (!in_array($model->role, $validRoles)) {
            throw new \Exception('Role tidak valid: ' . $model->role);
        }
    });
}
```

**Penjelasan:**
- Setiap kali user di-save ke database, validasi role
- Jika role bukan admin/owner/kasir → throw exception

**Keamanan:**
- ✅ Prevent role injection attack
- ✅ Role hanya bisa diset value yang valid
- ✅ Jika attacker coba set role ke "superadmin", akan error

---

## 🔒 Security Features yang Diterapkan

| Feature | Apa | Keamanan |
|---------|-----|----------|
| **Input Validation** | Validasi email/username/password | Prevent SQL injection, XSS |
| **Password Hashing (Bcrypt)** | Hash password dengan bcrypt | Password aman meski DB bocor |
| **Session Regeneration** | Ganti session ID setelah login | Prevent session fixation |
| **Rate Limiting** | Max 5 login attempts/minute | Prevent brute force |
| **Audit Logging** | Catat setiap login/logout/error | Deteksi suspicious activity |
| **Role-based Access Control** | Restrict route by role | Role tidak bisa akses route other roles |
| **CSRF Token** | Auto di setiap form request | Prevent CSRF attack |
| **Session Invalidation** | Logout destroy session completely | Prevent session hijacking |
| **Error Message Vague** | Tidak expose "user tidak ditemukan" | Prevent user enumeration |
| **IP Address Logging** | Catat IP setiap request | Trace suspicious login source |

---

## ⚙️ Konfigurasi yang Diperlukan

### 1. Register Middleware di Kernel

**File:** `app/Http/Kernel.php` atau `bootstrap/app.php`

#### Untuk Laravel 11 (bootstrap/app.php):
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
    
    // Rate limiting sudah built-in
})
```

#### Untuk Laravel 10 (app/Http/Kernel.php):
```php
protected $routeMiddleware = [
    'role' => \App\Http\Middleware\CheckRole::class,
];

protected $middleware = [
    // ... middleware lain
];
```

### 2. Konfigurasi Rate Limiting (config/cache.php)

Sudah built-in di Laravel, tapi bisa customize:

```php
'throttle' => env('THROTTLE_RATE', '60,1'),
```

### 3. Environment Variable (.env)

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartin_app
DB_USERNAME=root
DB_PASSWORD=

# Encryption Key (untuk session/cookie encryption)
APP_KEY=base64:xxxxxxxxxxxx

# Session
SESSION_DRIVER=database  # atau 'cookie'
SESSION_LIFETIME=120     # minutes
REMEMBER_ME_LIFETIME=525600  # 1 year

# Logging
LOG_CHANNEL=single
LOG_LEVEL=debug
```

### 4. Create Sessions Table (jika pakai session driver database)

```bash
php artisan session:table
php artisan migrate
```

---

## 🧪 Testing & Monitoring

### 1. Testing Manual

#### Test Case 1: Successful Login
```
1. Buka halaman /login
2. Masukkan email: admin@smartin.app, password: password
3. Klik Login
4. Expected: Redirect ke /admin dengan pesan "Selamat datang, Admin!"
5. Check: storage/logs/laravel.log ada entry login sukses
```

#### Test Case 2: Wrong Password
```
1. Masukkan email: admin@smartin.app, password: wrongpassword
2. Klik Login
3. Expected: Error validation "Email/username atau password salah"
4. Check: storage/logs/laravel.log ada entry failed login
```

#### Test Case 3: Nonexistent User
```
1. Masukkan email: notexist@smartin.app, password: password
2. Klik Login
3. Expected: Error validation "Email/username atau password salah" (sama dengan wrong password)
4. Check: Tidak ada di logs (hanya warning)
```

#### Test Case 4: Rate Limiting
```
1. Login attempt 5x dengan password salah (cepat)
2. Attempt ke-6 langsung
3. Expected: HTTP 429 "Too Many Requests" atau "Please retry after X seconds"
4. Tunggu 1 menit, baru bisa login lagi
```

#### Test Case 5: Role-based Access
```
1. Login sebagai 'student' (role: student)
2. Coba akses /admin
3. Expected: Error 403 "Anda tidak memiliki akses ke halaman ini"
4. Check: storage/logs/laravel.log ada entry unauthorized access
```

#### Test Case 6: Logout
```
1. Login sebagai admin
2. Klik logout
3. Expected: Redirect ke /login dengan pesan "Anda telah logout"
4. Coba akses /admin tanpa login lagi
5. Expected: Redirect ke /login (session sudah invalid)
```

### 2. Check Log Files

```bash
# Lihat semua log
tail -f storage/logs/laravel.log

# Filter hanya login activity
grep "login\|logout\|unauthorized" storage/logs/laravel.log

# Realtime monitoring
watch 'tail -30 storage/logs/laravel.log'
```

### 3. Monitor Database Sessions

```bash
# Jika pakai session driver: database
SELECT * FROM sessions;

# Check session expiry
SELECT session_id, user_id, last_activity FROM sessions;
```

---

## 🚨 Troubleshooting

### Problem 1: "SQLSTATE[HY000]: General error"

**Penyebab:** Middleware 'role' tidak registered

**Solusi:**
1. Cek `app/Http/Kernel.php` atau `bootstrap/app.php`
2. Pastikan `CheckRole::class` sudah di-alias sebagai `'role'`

### Problem 2: "Middleware [guest] not found"

**Penyebab:** Guest middleware belum di-register

**Solusi:**
```php
// Di bootstrap/app.php atau Kernel.php
// Guest middleware sudah built-in Laravel, tapi pastikan di-alias
$middleware->alias([
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
]);
```

### Problem 3: Rate Limiting Tidak Berfungsi

**Penyebab:** Cache driver belum dikonfigurasi dengan baik

**Solusi:**
```php
// .env
CACHE_DRIVER=redis  # atau 'database', 'memcached'

// Atau update route
Route::post('login', [...])
    ->middleware(['guest', 'throttle:login']);
    
// Di config/cache.php, define rate limit
'throttle' => [
    'default' => '60,1',
    'login' => '5,1',
]
```

### Problem 4: Password Always Wrong

**Penyebab:** Password tidak di-hash saat di-seed ke database

**Solusi:** Gunakan Hash saat create user
```php
User::create([
    'email' => 'admin@smartin.app',
    'password' => Hash::make('password'), // HARUS di-hash
    'role' => 'admin',
]);
```

### Problem 5: Log File Tidak Tersimpan

**Penyebab:** Permission storage folder tidak writable

**Solusi:**
```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

---

## 📊 Summary Security Stack

```
┌──────────────────────────────────────────────────────┐
│           SECURITY LAYERS AUTHENTICATION             │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Layer 1: INPUT VALIDATION                          │
│  └─ Reject invalid input format                     │
│                                                      │
│  Layer 2: USER LOOKUP & AUTHENTICATION              │
│  └─ Email/username + password verification         │
│  └─ Hash password dengan bcrypt                     │
│                                                      │
│  Layer 3: SESSION MANAGEMENT                        │
│  └─ Create session + regenerate ID                  │
│  └─ Session stored di database atau cookie (signed) │
│                                                      │
│  Layer 4: RATE LIMITING & BRUTE FORCE PROTECTION    │
│  └─ Max 5 login attempts per minute                 │
│  └─ Cooldown period setelah exceed limit            │
│                                                      │
│  Layer 5: AUTHORIZATION (ROLE-BASED)                │
│  └─ Check user role per route                       │
│  └─ Restrict unauthorized access                    │
│                                                      │
│  Layer 6: AUDIT LOGGING & MONITORING                │
│  └─ Log setiap activity detail (IP, timestamp, dll) │
│  └─ Historical record untuk compliance & debugging  │
│                                                      │
│  Layer 7: LOGOUT & SESSION INVALIDATION             │
│  └─ Destroy session completely                      │
│  └─ Prevent session reuse                           │
│                                                      │
└──────────────────────────────────────────────────────┘
```

---

## ✅ Checklist Sebelum Production

- [ ] Middleware 'role' sudah registered di Kernel
- [ ] Environment APP_KEY sudah di-generate (`php artisan key:generate`)
- [ ] Database migration sudah jalan (`php artisan migrate`)
- [ ] Session table sudah di-create (jika pakai driver: database)
- [ ] Log folder permission sudah writable (chmod 775)
- [ ] .env configuration sudah benar (DB, MAIL, CACHE, LOG, etc)
- [ ] Test semua test case di atas
- [ ] Monitor log file untuk suspicious activity
- [ ] Set up backup strategy untuk audit log
- [ ] Document password policy untuk user (min 6 char, standard)

---

## 📞 Referensi & Dokumentasi

- [Laravel Authentication Official Docs](https://laravel.com/docs/guards)
- [Laravel Authorization & Policies](https://laravel.com/docs/authorization)
- [Laravel Logging](https://laravel.com/docs/logging)
- [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)
- [Bcrypt Password Security](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)

---

**Document Version:** 1.0  
**Last Updated:** 18 Februari 2026  
**Author:** GitHub Copilot / SmartiN Development Team  
**Status:** ✅ Recommended untuk Production (dengan checklist terpenuhi)
