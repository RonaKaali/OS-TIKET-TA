# 📖 Panduan Mengisi Chatbot Responses

## 📋 Overview

Sistem chatbot responses memungkinkan Anda untuk mengatur jawaban otomatis yang akan dikirim bot ketika user mengirim pesan tertentu. Semua response dikelola melalui Admin Panel.

## 🗄️ Struktur Database

Tabel `chatbot_responses` memiliki kolom-kolom berikut:

| Kolom        | Tipe         | Deskripsi                                           |
| ------------ | ------------ | --------------------------------------------------- |
| `id`         | Integer      | ID unik (auto increment)                            |
| `keyword`    | String (255) | Kata kunci atau pertanyaan yang akan dicocokkan     |
| `response`   | Text         | Jawaban yang akan dikirim bot                       |
| `is_active`  | Boolean      | Status aktif/tidak aktif (default: true)            |
| `priority`   | Integer      | Prioritas matching 0-100 (default: 0)               |
| `match_type` | String       | Tipe pencocokan: `contains`, `exact`, `starts_with` |
| `created_at` | Timestamp    | Waktu pembuatan                                     |
| `updated_at` | Timestamp    | Waktu terakhir diupdate                             |

## 🚀 Cara Mengakses Menu Chatbot Responses

### Opsi 1: Via Admin Panel (Recommended)

1. **Login sebagai Super Admin**

    - Masuk ke aplikasi dengan akun yang memiliki role **Super Admin**

2. **Akses Admin Panel**

    - Klik menu **"Admin Panel"** di navigation bar

3. **Buka Menu Chatbot Responses**
    - Di sidebar Admin Panel, klik **"Chatbot Responses"**
    - Atau langsung akses: `/admin/chatbot-responses`

### Opsi 2: Menggunakan Seeder (Data Contoh)

Jika Anda ingin menggunakan data contoh untuk memulai, jalankan seeder:

```bash
php artisan db:seed --class=ChatbotResponseSeeder
```

Seeder ini akan menambahkan contoh data chatbot responses yang sudah siap digunakan. Setelah itu, Anda bisa menyesuaikan atau menambah data melalui Admin Panel.

## ✏️ Cara Mengisi Data Chatbot

### 1. Tambah Response Baru

1. Klik tombol **"Tambah Response"** di halaman index
2. Isi form dengan detail berikut:

#### **Keyword / Pertanyaan** (Required)

-   **Deskripsi**: Kata kunci yang akan dicocokkan dengan pesan dari user
-   **Contoh**:
    -   `halo`
    -   `selamat pagi`
    -   `info`
    -   `bantuan`
    -   `cara buat tiket`
    -   `status tiket`
-   **Tips**:
    -   Gunakan kata kunci yang umum digunakan user
    -   Gunakan huruf kecil untuk konsistensi
    -   Bisa menggunakan beberapa kata, seperti: `cara buat tiket`
    -   Hindari kata kunci yang terlalu umum (seperti: `a`, `yang`, `dan`)

#### **Response / Jawaban** (Required)

-   **Deskripsi**: Pesan yang akan dikirim bot kepada user
-   **Contoh**:

    ```
    Halo! Selamat datang di CSIRT Kalselprov.

    Saya siap membantu Anda. Anda bisa menanyakan:
    - Cara membuat tiket baru
    - Status tiket Anda
    - Informasi tentang CSIRT

    Ketik /help untuk melihat daftar perintah lengkap.
    ```

-   **Tips**:
    -   Buat response yang ramah dan informatif
    -   Gunakan format yang mudah dibaca (baris baru, bullet points)
    -   Sesuaikan dengan konteks keyword yang digunakan
    -   Bisa menggunakan emoji untuk membuat lebih menarik: 😊 ✅ ⚠️ 📋

#### **Tipe Pencocokan** (Required)

Pilih salah satu dari 3 tipe:

1. **Contains (Mengandung kata)** - **Default, Paling Umum**

    - Cocok jika pesan user **mengandung** keyword
    - **Contoh**: Keyword = `halo`

        - ✅ Match: "halo", "halo admin", "halo saya ingin bertanya"
        - ❌ Tidak match: "halo" (jika tidak ada kata "halo")

    - **Gunakan untuk**: Kata kunci umum yang bisa muncul di mana saja dalam kalimat

2. **Exact (Persis sama)**

    - Cocok hanya jika pesan user **persis sama** dengan keyword
    - **Contoh**: Keyword = `halo`

        - ✅ Match: "halo" (persis sama)
        - ❌ Tidak match: "halo admin", "halo saya"

    - **Gunakan untuk**: Perintah spesifik, seperti `/start`, `/help`, `/status`

3. **Starts With (Dimulai dengan)**

    - Cocok jika pesan user **dimulai dengan** keyword
    - **Contoh**: Keyword = `cara`

        - ✅ Match: "cara buat tiket", "cara login"
        - ❌ Tidak match: "bagaimana cara", "tanya cara"

    - **Gunakan untuk**: Perintah yang dimulai dengan kata tertentu

#### **Priority** (Required)

-   **Range**: 0 - 100 (default: 50)
-   **Deskripsi**: Prioritas matching ketika ada beberapa response dengan keyword yang mirip
-   **Contoh**:
    -   `100`: Untuk response penting/khusus (akan dicocokkan lebih dulu)
    -   `50`: Untuk response standar (default)
    -   `10`: Untuk response umum/fallback
-   **Tips**:
    -   Jika ada 2 response dengan keyword yang sama, yang priority lebih tinggi akan dipilih
    -   Gunakan priority tinggi untuk response yang lebih spesifik
    -   Gunakan priority rendah untuk response umum/fallback

#### **Status Aktif** (Required)

-   **Deskripsi**: Mengaktifkan atau menonaktifkan response ini
-   **Checkbox**: ✅ Centang = Aktif, ❌ Tidak dicentang = Tidak aktif
-   **Default**: Aktif (centang)
-   **Tips**:
    -   Nonaktifkan response jika ingin sementara tidak digunakan (tidak perlu dihapus)
    -   Response yang tidak aktif tidak akan digunakan oleh bot

### 2. Edit Response

1. Di halaman index, klik tombol **"Edit"** pada response yang ingin diubah
2. Ubah data sesuai kebutuhan
3. Klik **"Update Response"** untuk menyimpan perubahan

### 3. Hapus Response

1. Di halaman index, klik tombol **"Hapus"** pada response yang ingin dihapus
2. Konfirmasi penghapusan
3. **Peringatan**: Data yang dihapus tidak bisa dikembalikan

### 4. Cari dan Filter

Di halaman index, Anda bisa:

-   **Search**: Mencari berdasarkan keyword atau response
-   **Filter Status**: Filter berdasarkan status aktif/tidak aktif

## 📝 Contoh Pengisian Lengkap

### Contoh 1: Sapaan Umum

```
Keyword: halo
Response:
  Halo! Selamat datang di CSIRT Kalselprov. 😊

  Saya siap membantu Anda dengan:
  - Membuat tiket baru
  - Mengecek status tiket
  - Memberikan informasi

  Ketik /help untuk bantuan lebih lanjut.

Match Type: Contains
Priority: 50
Status: ✅ Aktif
```

### Contoh 2: Perintah Help

```
Keyword: /help
Response:
  📋 *Daftar Perintah:*

  /start - Memulai bot
  /help - Menampilkan bantuan ini
  /tiket - Membuat tiket baru
  /status - Cek status tiket
  /info - Informasi CSIRT

  Atau langsung ketik pertanyaan Anda!

Match Type: Exact
Priority: 100
Status: ✅ Aktif
```

### Contoh 3: Cara Buat Tiket

```
Keyword: cara buat tiket
Response:
  📝 *Cara Membuat Tiket:*

  1. Login ke portal CSIRT
  2. Klik "Buat Tiket Baru"
  3. Isi form dengan lengkap:
     - Subject
     - Deskripsi masalah
     - Priority
  4. Submit tiket

  Atau hubungi admin untuk bantuan lebih lanjut.

Match Type: Contains
Priority: 80
Status: ✅ Aktif
```

### Contoh 4: Response Fallback

```
Keyword: tidak tahu
Response:
  Maaf, saya tidak mengerti pertanyaan Anda. 😔

  Silakan coba salah satu dari:
  - Ketik /help untuk melihat daftar perintah
  - Ketik "cara buat tiket" untuk panduan
  - Hubungi admin untuk bantuan lebih lanjut

  Terima kasih!

Match Type: Contains
Priority: 10
Status: ✅ Aktif
```

## 💡 Tips & Best Practices

### 1. Keyword Strategy

-   ✅ Gunakan keyword yang umum dan mudah diingat
-   ✅ Buat variasi keyword untuk pertanyaan yang sama
-   ✅ Gunakan lowercase untuk konsistensi
-   ❌ Hindari keyword yang terlalu umum (a, yang, dan, dll)
-   ❌ Hindari keyword yang terlalu panjang

### 2. Response Strategy

-   ✅ Buat response yang jelas dan informatif
-   ✅ Gunakan formatting (baris baru, bullet points)
-   ✅ Gunakan emoji dengan bijak (tidak berlebihan)
-   ✅ Sertakan langkah selanjutnya yang bisa dilakukan user
-   ❌ Jangan membuat response yang terlalu panjang (>500 karakter)
-   ❌ Hindari informasi yang tidak relevan

### 3. Match Type Strategy

-   ✅ **Contains**: Untuk keyword umum (default)
-   ✅ **Exact**: Untuk perintah spesifik (seperti `/start`, `/help`)
-   ✅ **Starts With**: Untuk pertanyaan yang dimulai dengan kata tertentu
-   💡 Kombinasikan dengan priority untuk kontrol yang lebih baik

### 4. Priority Strategy

-   ✅ Priority tinggi (80-100): Response penting/khusus
-   ✅ Priority sedang (40-60): Response standar
-   ✅ Priority rendah (0-30): Response fallback/umum
-   💡 Jika ada keyword yang sama, prioritaskan yang lebih spesifik

### 5. Testing

-   ✅ Test setiap response baru dengan berbagai variasi pesan
-   ✅ Pastikan response yang lebih spesifik memiliki priority lebih tinggi
-   ✅ Nonaktifkan response lama saat membuat yang baru (jika perlu)

## 🔍 Cara Testing Response

1. **Via Telegram Bot**:

    - Kirim pesan ke bot dengan keyword yang sudah dibuat
    - Pastikan bot merespons sesuai yang diharapkan

2. **Via Database**:

    - Cek di tabel `chatbot_responses` apakah data sudah tersimpan
    - Pastikan `is_active = 1` untuk response yang aktif

3. **Via Logs**:
    - Cek log aplikasi untuk melihat apakah bot berhasil mencocokkan keyword

## 📌 Checklist Pengisian

Sebelum mengisi response, pastikan:

-   [ ] Keyword sudah jelas dan mudah diingat
-   [ ] Response informatif dan mudah dipahami
-   [ ] Match type sesuai dengan kebutuhan
-   [ ] Priority sudah diatur dengan benar
-   [ ] Status aktif/nonaktif sudah sesuai
-   [ ] Sudah ditest dengan berbagai variasi pesan
-   [ ] Tidak ada konflik dengan response lain

## 🆘 Troubleshooting

### Bot tidak merespons dengan benar?

1. Cek apakah response `is_active = true`
2. Cek apakah match type sudah sesuai
3. Cek priority - mungkin ada response lain yang lebih tinggi priority-nya
4. Test dengan keyword yang berbeda

### Response tidak muncul?

1. Pastikan response sudah di-save
2. Pastikan `is_active = true`
3. Cek apakah ada response lain dengan keyword yang sama dan priority lebih tinggi
4. Test dengan match type yang berbeda

### Ingin mengupdate response yang sudah ada?

1. Klik tombol "Edit" pada response
2. Ubah data yang diperlukan
3. Save perubahan
4. Test lagi dengan bot

## 📞 Kontak

Jika ada pertanyaan atau masalah saat mengisi chatbot responses, hubungi developer atau admin sistem.

---

**Versi**: 1.0  
**Dibuat untuk**: Sistem CSIRT Kalselprov
