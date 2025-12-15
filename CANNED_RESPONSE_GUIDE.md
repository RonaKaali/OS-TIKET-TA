# 📖 Panduan Menggunakan Canned Response

## 📋 Overview

Canned Response (Template Balasan) adalah fitur yang memungkinkan Admin dan Agent untuk menggunakan template balasan yang sudah disiapkan sebelumnya saat membalas tiket. Fitur ini membantu mempercepat proses respon dan memastikan konsistensi dalam komunikasi dengan pelapor.

---

## 👨‍💼 Untuk Super Admin: Mengelola Canned Response

### 1. **Mengakses Menu Canned Response**

1. Login sebagai **Super Admin**
2. Klik menu **"Admin Panel"** di navigation bar
3. Di sidebar, klik **"Canned Response"**
4. Atau akses langsung: `/admin/canned`

### 2. **Menambah Canned Response Baru**

1. Di halaman daftar Canned Response, klik tombol **"+ Tambah Template"**
2. Isi form:
   - **Title**: Judul/topik template (contoh: "Respon Standar untuk Laporan Phishing")
   - **Body**: Isi template balasan yang akan digunakan
3. Klik **"Simpan"**
4. Template akan tersimpan dan bisa digunakan oleh agent

**Contoh Template:**
- **Title**: "Konfirmasi Penerimaan Laporan"
- **Body**: 
  ```
  Terima kasih telah melaporkan insiden siber kepada kami.
  
  Laporan Anda dengan nomor {{TICKET_NUMBER}} telah kami terima dan sedang dalam proses penanganan.
  
  Tim CSIRT akan segera menindaklanjuti laporan Anda.
  
  Salam,
  Tim CSIRT Kalselprov
  ```

### 3. **Mengedit Canned Response**

1. Di halaman daftar Canned Response, klik **"Edit"** pada template yang ingin diubah
2. Ubah Title atau Body sesuai kebutuhan
3. Klik **"Update"** untuk menyimpan perubahan

### 4. **Menghapus Canned Response**

1. Di halaman daftar Canned Response, klik **"Hapus"** pada template yang ingin dihapus
2. Konfirmasi penghapusan
3. Template akan dihapus dari sistem

---

## 👤 Untuk Agent/Admin: Menggunakan Canned Response

### Cara Menggunakan Canned Response saat Membalas Tiket

1. **Buka Detail Tiket**
   - Login sebagai Agent atau Admin
   - Buka tiket yang ingin dibalas dari daftar tiket
   - Scroll ke bagian **"Balas Laporan"**

2. **Pilih Template**
   - Di form balasan, klik tombol **"Pilih Template"** atau **"Select Canned Response"**
   - Sistem akan menampilkan daftar template yang tersedia
   - Pilih template yang sesuai dengan konteks balasan

3. **Template Otomatis Terisi**
   - Setelah memilih template, isi template akan otomatis terisi di textarea pesan
   - Anda dapat mengedit template sesuai kebutuhan sebelum mengirim

4. **Personaliasi Balasan (Opsional)**
   - Edit template untuk menambahkan informasi spesifik tiket
   - Tambahkan detail tambahan jika diperlukan
   - Template bisa diubah sepenuhnya atau digunakan sebagai dasar

5. **Kirim Balasan**
   - Setelah selesai mengedit, klik **"Kirim Balasan"**
   - Balasan akan terkirim ke pelapor

### Tips Menggunakan Canned Response

✅ **DO (Lakukan):**
- Gunakan template sebagai dasar, lalu personalisasi sesuai konteks tiket
- Edit template untuk menambahkan informasi spesifik (nomor tiket, nama pelapor, dll)
- Pilih template yang paling sesuai dengan jenis insiden

❌ **DON'T (Jangan):**
- Jangan mengirim template tanpa mengedit sama sekali (kecuali memang sesuai 100%)
- Jangan menggunakan template yang tidak relevan dengan konteks tiket
- Jangan lupa menyesuaikan template dengan situasi spesifik

---

## 📝 Contoh Use Case

### Scenario 1: Konfirmasi Penerimaan Laporan

**Template:**
```
Terima kasih telah melaporkan insiden siber kepada kami.

Laporan Anda dengan nomor {{TICKET_NUMBER}} telah kami terima dan sedang dalam proses penanganan.

Tim CSIRT akan segera menindaklanjuti laporan Anda.

Salam,
Tim CSIRT Kalselprov
```

**Setelah Dipilih dan Diedit:**
```
Terima kasih telah melaporkan insiden siber kepada kami.

Laporan Anda dengan nomor TKT-2024-001234 telah kami terima dan sedang dalam proses penanganan.

Berdasarkan laporan Anda tentang phishing email, kami akan melakukan investigasi lebih lanjut dan akan memberikan update dalam 24 jam ke depan.

Tim CSIRT akan segera menindaklanjuti laporan Anda.

Salam,
Tim CSIRT Kalselprov
```

### Scenario 2: Permintaan Informasi Tambahan

**Template:**
```
Halo,

Terima kasih atas laporan Anda. Untuk dapat menindaklanjuti laporan ini dengan lebih baik, kami memerlukan informasi tambahan:

1. [Informasi yang diperlukan]
2. [Informasi yang diperlukan]

Mohon dapat memberikan informasi tersebut secepatnya.

Terima kasih,
Tim CSIRT Kalselprov
```

---

## 🔧 Fitur Teknis

### Struktur Data

- **Title**: Judul template (maksimal 255 karakter)
- **Body**: Isi template (maksimal 50,000 karakter)
- **Timestamps**: Created at dan Updated at

### Akses

- **Mengelola Template**: Hanya Super Admin
- **Menggunakan Template**: Agent dan Admin yang memiliki akses ke panel admin

### Batasan

- Maksimal 255 karakter untuk Title
- Maksimal 50,000 karakter untuk Body
- Template dapat diedit atau dihapus kapan saja oleh Super Admin

---

## ❓ FAQ

**Q: Apakah template bisa digunakan untuk semua jenis tiket?**  
A: Ya, template bisa digunakan untuk semua jenis tiket. Pilih template yang paling sesuai dengan konteks.

**Q: Apakah template bisa diedit setelah dipilih?**  
A: Ya, template bisa dan sebaiknya diedit untuk menyesuaikan dengan konteks tiket spesifik.

**Q: Apakah ada batasan jumlah template?**  
A: Tidak ada batasan jumlah template yang bisa dibuat.

**Q: Apakah template bisa digunakan untuk email otomatis?**  
A: Saat ini template digunakan manual oleh agent saat membalas tiket. Untuk email otomatis, gunakan fitur Chatbot Response.

---

## 📚 Related Documentation

- [Chatbot Responses Guide](./CHATBOT_RESPONSES_GUIDE.md) - Untuk template otomatis chatbot
- [Use Case Detailed](./USE_CASE_DETAILED.md) - Detail use case penggunaan canned response
- [Database Documentation](./DATABASE_DOCUMENTATION.md) - Struktur database canned response

