# Debug: MFA Tidak Muncul Saat Login

## 🔍 Masalah

Saat login, tidak ada verifikasi 2FA meskipun sudah setup MFA.

## ✅ Perbaikan yang Diterapkan

### 1. Update `isMfaEnabled()` Method

Sekarang cek dari:
- **Cache** (lebih cepat)
- **Database** (jika cache tidak ada)

Ini memastikan MFA terdeteksi meskipun cache expired atau di-clear.

### 2. Update Login Flow

Login sekarang cek MFA dari:
- Database field `mfa_enabled` langsung
- Service `isMfaEnabled()` sebagai fallback

## 🧪 Cara Test

### 1. Cek Status MFA User

```bash
php artisan tinker
```

```php
$user = App\Models\User::find(1); // Ganti dengan ID user Anda

// Cek MFA di database
echo "MFA Enabled (DB): " . ($user->mfa_enabled ? 'true' : 'false') . "\n";
echo "MFA Secret (DB): " . ($user->mfa_secret ? 'exists' : 'not exists') . "\n";
echo "MFA Enabled At: " . ($user->mfa_enabled_at ?? 'null') . "\n";

// Cek via service
$mfaService = app(App\Services\MfaService::class);
echo "MFA Enabled (Service): " . ($mfaService->isMfaEnabled($user) ? 'true' : 'false') . "\n";

// Cek cache
echo "MFA Secret (Cache): " . (cache()->has("mfa_secret:{$user->id}") ? 'exists' : 'not exists') . "\n";
```

### 2. Enable MFA untuk User (Jika Belum)

Jika MFA belum enable, enable melalui:

**Opsi A: Via Web Interface**
1. Login ke aplikasi
2. Akses: `http://localhost:8000/mfa/setup`
3. Setup MFA sesuai panduan

**Opsi B: Via Database (Manual)**

```php
// Di tinker
$user = App\Models\User::find(1);

// Generate secret
$mfaService = app(App\Services\MfaService::class);
$secret = $mfaService->generateSecret($user);

// Simpan ke database (encrypted)
$user->mfa_secret = encrypt($secret);
$user->mfa_enabled = true;
$user->mfa_enabled_at = now();
$user->save();

// Simpan ke cache juga
cache()->put("mfa_secret:{$user->id}", $secret, now()->addYears(10));

echo "MFA enabled untuk user: " . $user->email . "\n";
echo "Secret: " . $secret . "\n";
echo "QR URL: " . $mfaService->getQrCodeUrl($user, $secret) . "\n";
```

**PENTING**: Setelah enable manual, user tetap perlu scan QR code di aplikasi authenticator!

### 3. Test Login Flow

1. **Logout** dari aplikasi (jika sudah login)
2. **Login** dengan email & password
3. **Cek apakah redirect ke `/mfa/verify`**
4. Jika tidak redirect, cek log untuk error

### 4. Cek Log

```bash
tail -f storage/logs/laravel.log
```

Cari log dengan keyword:
- "MFA enabled for user"
- "User tidak punya MFA enabled"
- "MFA verification"

## 🔧 Troubleshooting

### Masalah: MFA tidak muncul saat login

**Penyebab:**
1. User belum enable MFA (`mfa_enabled = false`)
2. Cache expired dan database tidak terdeteksi
3. `isMfaEnabled()` tidak bekerja dengan benar

**Solusi:**

#### Step 1: Verifikasi MFA Enabled

```php
// Di tinker
$user = App\Models\User::find(1);
echo "MFA Enabled: " . ($user->mfa_enabled ? 'true' : 'false') . "\n";
```

Jika `false`, user perlu setup MFA terlebih dahulu.

#### Step 2: Clear Cache dan Test

```bash
php artisan cache:clear
```

Kemudian test login lagi.

#### Step 3: Force Enable MFA (Testing)

Jika ingin test tanpa setup lengkap:

```php
// Di tinker
$user = App\Models\User::find(1);
$user->mfa_enabled = true;
$user->save();

// Test login
// User akan di-redirect ke /mfa/verify
// Tapi verifikasi akan gagal karena tidak ada secret
// Ini hanya untuk test flow, bukan untuk production
```

## 📋 Checklist

- [ ] User sudah setup MFA (`mfa_enabled = true` di database)
- [ ] `mfa_secret` ada di database (encrypted)
- [ ] Cache `mfa_secret:{user_id}` ada (atau akan di-load dari database)
- [ ] `isMfaEnabled()` return `true`
- [ ] Login redirect ke `/mfa/verify`
- [ ] Halaman MFA verification muncul

## 🚀 Flow Login dengan MFA

```
1. User submit login (email + password)
   ↓
2. AuthenticatedSessionController->store()
   - Validasi email & password
   - Cek mfa_enabled di database
   - Cek isMfaEnabled() via service
   ↓
3. Jika MFA enabled:
   - Redirect ke /mfa/verify
   - Jangan complete session dulu
   ↓
4. User masukkan kode MFA
   ↓
5. MfaVerificationController->store()
   - Verify TOTP code
   - Complete session setup
   - Redirect ke dashboard
```

## ⚠️ Catatan Penting

1. **MFA harus di-enable terlebih dahulu** sebelum login akan meminta verifikasi
2. **Setup MFA** harus dilakukan melalui `/mfa/setup` untuk generate secret dan QR code
3. **Secret harus ada** di database atau cache untuk verifikasi bekerja
4. **Cache bisa expired**, tapi sekarang akan auto-load dari database

## 📚 Referensi

- [GOOGLE2FA_SETUP_GUIDE.md](./GOOGLE2FA_SETUP_GUIDE.md) - Panduan setup MFA
- [MFA_TROUBLESHOOTING.md](./MFA_TROUBLESHOOTING.md) - Troubleshooting MFA

---

**Version**: 1.0


