## Catatan Perubahan Proteksi Clickjacking

- **Tanggal perubahan**: 26 Februari 2026
- **Tujuan**: Menambahkan proteksi terhadap serangan clickjacking dengan mengirim header `X-Frame-Options`.

### File yang diubah

- **File**: `app/Http/Middleware/CheckUserActivity.php`
  - **Method**: `handle`
  - **Perubahan utama**:
    - Sebelumnya, method `handle` mengembalikan response langsung dari:
      - `return $next($request);`
    - Sekarang, response disimpan terlebih dahulu ke variabel dan ditambahkan header keamanan:

```php
$response = $next($request);
$response->headers->set('X-Frame-Options', 'SAMEORIGIN');

return $response;
```

### Dampak Perubahan

- Semua response pada grup middleware `web` sekarang mengirim header:
  - `X-Frame-Options: SAMEORIGIN`
- Hal ini mencegah halaman aplikasi dibingkai (`iframe`, `frame`, `object`) oleh domain lain (proteksi clickjacking).

