# Penggunaan Multi-Role System

## Di Routes (routes/web.php)

```php
// Admin only
Route::get('/admin', [RoleController::class, 'dashboard'])
    ->middleware(['auth', 'role:admin']);

// Admin atau Mentor
Route::get('/class', [RoleController::class, 'dashboard'])
    ->middleware(['auth', 'role:admin,mentor']);

// Semua role yang sudah login
Route::get('/dashboard', [RoleController::class, 'dashboard'])
    ->middleware('auth');

// Update role (admin only)
Route::post('/users/{id}/role', [RoleController::class, 'updateRole'])
    ->middleware(['auth', 'role:admin']);
```

## Di Controller

```php
$user = Auth::user();

if ($user->role === 'admin') {
    // Admin
}

if (in_array($user->role, ['admin', 'mentor'])) {
    // Admin atau Mentor
}
```

## Di Blade Template

```blade
@if(auth()->user()->role === 'admin')
    <p>Admin Panel</p>
@endif

@if(in_array(auth()->user()->role, ['admin', 'mentor']))
    <p>Admin atau Mentor</p>
@endif
```

## Pastikan Middleware Terdaftar

Di `app/Http/Kernel.php` atau `bootstrap/app.php`:

```php
'role' => \App\Http\Middleware\CheckRole::class,
```
