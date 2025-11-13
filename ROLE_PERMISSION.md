# Sistem Role dan Permission - CSIRT Kalselprov

## Overview

Projek ini menggunakan **Spatie Laravel Permission** untuk manajemen role dan permission. Sistem ini memungkinkan kontrol akses yang fleksibel untuk berbagai pengguna.

## Status Implementasi

✅ **Package Terinstall**: `spatie/laravel-permission` v6.23
✅ **Migration**: Sudah di-publish dan siap dijalankan
✅ **User Model**: Sudah menggunakan trait `HasRoles`
✅ **Seeder**: Sudah dibuat untuk roles dan permissions default
✅ **Middleware**: Sudah digunakan di controller untuk proteksi route

## Roles yang Tersedia

### 1. **Super Admin**

Role dengan akses penuh ke semua fitur sistem.

**Permissions:**

-   `admin.panel` - Akses ke panel admin
-   `tickets.*` - Semua aksi pada tiket
-   `users.*` - Manajemen user
-   `departments.*` - Manajemen departemen

### 2. **Admin**

Role untuk administrator dengan akses terbatas.

**Permissions:**

-   `admin.panel` - Akses ke panel admin
-   `tickets.view`, `tickets.create`, `tickets.update`, `tickets.assign`, `tickets.close`
-   `users.view`, `users.create`, `users.update`
-   `departments.view`, `departments.create`, `departments.update`

### 3. **Agent**

Role untuk agen CSIRT yang menangani laporan insiden.

**Permissions:**

-   `admin.panel` - Akses ke panel agent
-   `tickets.view` - Melihat tiket
-   `tickets.update` - Update tiket
-   `tickets.assign` - Assign tiket ke agent lain

### 4. **Support Agent**

Role untuk agen support dengan akses terbatas.

**Permissions:**

-   `tickets.view` - Melihat tiket
-   `tickets.update` - Update tiket

## Permissions yang Tersedia

### Admin Panel

-   `admin.panel` - Akses ke dashboard admin/agent

### Ticket Permissions

-   `tickets.view` - Melihat daftar dan detail tiket
-   `tickets.create` - Membuat tiket baru
-   `tickets.update` - Update tiket (status, priority, dll)
-   `tickets.delete` - Menghapus tiket
-   `tickets.assign` - Assign tiket ke agent
-   `tickets.close` - Menutup tiket

### User Management

-   `users.view` - Melihat daftar user
-   `users.create` - Membuat user baru
-   `users.update` - Update user
-   `users.delete` - Menghapus user

### Department Management

-   `departments.view` - Melihat daftar departemen
-   `departments.create` - Membuat departemen baru
-   `departments.update` - Update departemen
-   `departments.delete` - Menghapus departemen

## Setup Awal

### 1. Jalankan Migration

```bash
php artisan migrate
```

Ini akan membuat tabel:

-   `roles`
-   `permissions`
-   `model_has_permissions`
-   `model_has_roles`
-   `role_has_permissions`

### 2. Jalankan Seeder

```bash
php artisan db:seed --class=RolePermissionSeeder
```

Atau jalankan semua seeder:

```bash
php artisan db:seed
```

Seeder ini akan:

-   Membuat semua permissions
-   Membuat semua roles
-   Assign permissions ke masing-masing role
-   Assign Super Admin role ke user pertama (jika ada)

### 3. User Default

Setelah menjalankan `DatabaseSeeder`, akan dibuat 2 user default:

1. **Super Admin**

    - Email: `admin@csirt.kalselprov.go.id`
    - Password: `password`
    - Role: Super Admin

2. **Agent**
    - Email: `agent@csirt.kalselprov.go.id`
    - Password: `password`
    - Role: Agent

**⚠️ Penting**: Ganti password default setelah pertama kali login!

## Penggunaan di Code

### Check Role

```php
// Check jika user punya role
if ($user->hasRole('Super Admin')) {
    // ...
}

// Check beberapa role
if ($user->hasAnyRole(['Admin', 'Agent'])) {
    // ...
}
```

### Check Permission

```php
// Check permission
if ($user->can('admin.panel')) {
    // ...
}

// Check multiple permissions
if ($user->hasAllPermissions(['tickets.view', 'tickets.update'])) {
    // ...
}
```

### Assign Role

```php
// Assign role ke user
$user->assignRole('Agent');

// Assign multiple roles
$user->assignRole(['Agent', 'Support Agent']);

// Remove role
$user->removeRole('Agent');

// Sync roles (replace semua roles)
$user->syncRoles(['Admin']);
```

### Assign Permission

```php
// Assign permission langsung ke user
$user->givePermissionTo('tickets.assign');

// Remove permission
$user->revokePermissionTo('tickets.assign');

// Sync permissions
$user->syncPermissions(['tickets.view', 'tickets.update']);
```

### Middleware

```php
// Di route
Route::middleware(['auth', 'permission:admin.panel'])->group(function () {
    // ...
});

// Di controller
public function __construct()
{
    $this->middleware(['auth', 'permission:admin.panel']);
}

// Multiple permissions (OR)
$this->middleware(['auth', 'permission:tickets.view|tickets.update']);

// Multiple permissions (AND)
$this->middleware(['auth', 'permission:tickets.view,tickets.update']);

// Role-based
$this->middleware(['auth', 'role:Admin|Agent']);
```

### Di Blade Template

```blade
{{-- Check role --}}
@role('Super Admin')
    <p>Hanya Super Admin yang bisa lihat ini</p>
@endrole

{{-- Check permission --}}
@can('admin.panel')
    <a href="/admin">Admin Panel</a>
@endcan

{{-- Check multiple --}}
@hasanyrole('Admin|Agent')
    <p>Admin atau Agent</p>
@endhasanyrole
```

## Menambah Role/Permission Baru

### 1. Tambah Permission di Seeder

Edit `database/seeders/RolePermissionSeeder.php`:

```php
$permissions = [
    // ... existing permissions
    'tickets.export', // Permission baru
];
```

### 2. Assign ke Role

```php
'Agent' => [
    // ... existing permissions
    'tickets.export',
],
```

### 3. Jalankan Seeder Lagi

```bash
php artisan db:seed --class=RolePermissionSeeder
```

**Note**: Seeder menggunakan `firstOrCreate`, jadi tidak akan duplikat jika sudah ada.

## Menambah Role Baru

Edit `database/seeders/RolePermissionSeeder.php`:

```php
$roles = [
    // ... existing roles
    'Manager' => [
        'admin.panel',
        'tickets.view',
        'tickets.close',
        'users.view',
    ],
];
```

Jalankan seeder lagi.

## Command Line

```bash
# Assign role ke user via email
php artisan tinker
$user = User::where('email', 'user@example.com')->first();
$user->assignRole('Agent');

# Check role user
$user->roles;

# Check permissions user
$user->getAllPermissions();

# List semua roles
$roles = Spatie\Permission\Models\Role::all();
```

## Troubleshooting

### Permission tidak bekerja

1. **Clear cache**:

```bash
php artisan permission:cache-reset
php artisan config:clear
php artisan cache:clear
```

2. **Cek apakah user punya role/permission**:

```php
$user->roles;
$user->permissions;
$user->getAllPermissions();
```

3. **Cek middleware di route/controller**

### Error "Class 'Spatie\Permission\PermissionRegistrar' not found"

Pastikan package sudah terinstall:

```bash
composer install
```

### Migration error

Pastikan migration sudah di-publish:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

## Best Practices

1. **Gunakan Permission bukan Role** untuk check akses di code
2. **Gunakan Role** untuk grouping permissions yang sering digunakan bersama
3. **Clear cache** setelah mengubah roles/permissions
4. **Gunakan Seeder** untuk maintain consistency
5. **Test permissions** setelah deploy

## Referensi

-   [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission)
-   [Laravel Authorization](https://laravel.com/docs/authorization)
