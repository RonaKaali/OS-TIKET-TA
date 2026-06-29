# Desain Halaman Tentang & Creator

## Tujuan

Menambahkan halaman publik yang menjelaskan identitas sistem, ruang lingkup OS-Tiket/CSIRT Kalselprov, fitur keamanan Zero Trust, dan informasi pembuat sistem untuk kebutuhan presentasi tugas akhir.

## Struktur Halaman

- Hero singkat dengan judul "Tentang & Creator Sistem" dan tombol menuju pelaporan atau pelacakan tiket.
- Ringkasan sistem yang menjelaskan fungsi OS-Tiket sebagai portal pelaporan insiden siber.
- Sorotan kapabilitas utama: pelaporan tiket, pelacakan status, panel admin/agent, MFA, verifikasi perangkat, dan monitoring Zero Trust.
- Bagian creator yang menempatkan sistem sebagai karya tugas akhir dan menyediakan identitas pengembang yang dapat diperbarui.
- Ringkasan alur kerja dari laporan masuk hingga tindak lanjut tim.

## Integrasi

- Route publik baru: `/about` dengan nama route `about`.
- View Blade baru: `resources/views/about.blade.php`.
- Link "Tentang" ditambahkan pada navbar dan footer halaman `welcome`.

## Antarmuka

Tampilan mengikuti bahasa visual halaman publik saat ini: latar cyber-grid, aksen emerald/blue, logo Kalselprov, dark mode, panel ringan, dan layout responsif. Halaman tetap dapat diakses tanpa autentikasi.

## Pengujian

Validasi dilakukan dengan `php artisan route:list --name=about` dan pemeriksaan render Blade melalui rute publik.
