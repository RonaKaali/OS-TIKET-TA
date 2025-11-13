# Index Dokumentasi Database - OS-Tiket

## 📚 Daftar Dokumentasi

### 1. **DATABASE_DOCUMENTATION.md**

Dokumentasi lengkap database yang mencakup:

-   ✅ CDM (Conceptual Data Model) - Model konseptual entitas dan relasi
-   ✅ LDM (Logical Data Model) - Model logis dengan detail atribut dan tipe data
-   ✅ PDM (Physical Data Model) - Model fisik dengan indexing dan optimasi
-   ✅ ERD (Entity Relationship Diagram) - Diagram relasi entitas

**Lokasi**: `DATABASE_DOCUMENTATION.md`

---

### 2. **USE_CASE_DIAGRAM.md**

Dokumentasi use case untuk setiap role:

-   ✅ Use Case - Super Admin
-   ✅ Use Case - Admin
-   ✅ Use Case - Agent
-   ✅ Use Case - Support Agent
-   ✅ Use Case - User (Pelapor)
-   ✅ Use Case - Guest (Tidak Login)
-   ✅ Use Case Flow Diagrams
-   ✅ Use Case Matrix

**Lokasi**: `USE_CASE_DIAGRAM.md`

---

### 3. **ERD_VISUAL.md**

ERD visual dengan representasi text-based:

-   ✅ Entity Relationship Diagram (Text-based)
-   ✅ Relasi Detail (One-to-Many, Many-to-Many)
-   ✅ Cardinality Summary
-   ✅ Key Relationships

**Lokasi**: `ERD_VISUAL.md`

---

## 🎯 Quick Reference

### Tabel Utama

1. **pengguna** - Data pengguna (admin, agent, pelapor)
2. **tiket** - Data tiket/laporan insiden
3. **utas_tiket** - Thread/balasan tiket
4. **lampiran** - File lampiran
5. **departemen** - Departemen
6. **status** - Status tiket
7. **prioritas** - Prioritas tiket
8. **topik_bantuan** - Kategori insiden
9. **rencana_sla** - SLA plan
10. **organisasi** - Organisasi
11. **tim** - Tim agent
12. **respons_template** - Template balasan
13. **roles** - Role (Spatie Permission)
14. **permissions** - Permission (Spatie Permission)

### Roles

1. **Super Admin** - Full access
2. **Admin** - Admin panel access
3. **Agent** - Agent panel access
4. **Support Agent** - Support access
5. **User** - Portal access only
6. **Guest** - Public access only

---

## 📁 File Terkait

### Migrations

-   `database/migrations/` - Semua file migration database

### Models

-   `app/Models/` - Eloquent models

### Seeders

-   `database/seeders/` - Database seeders

### Controllers

-   `app/Http/Controllers/` - Controllers yang menggunakan models

---

## 🔍 Cara Menggunakan Dokumentasi

1. **Untuk memahami struktur database**: Baca `DATABASE_DOCUMENTATION.md`
2. **Untuk memahami use case per role**: Baca `USE_CASE_DIAGRAM.md`
3. **Untuk melihat diagram relasi**: Baca `ERD_VISUAL.md`
4. **Untuk quick reference**: Gunakan file ini (`DATABASE_INDEX.md`)

---

## 📊 Diagram Overview

```
DATABASE_DOCUMENTATION.md
├── CDM (Conceptual Data Model)
├── LDM (Logical Data Model)
├── PDM (Physical Data Model)
└── ERD (Entity Relationship Diagram)

USE_CASE_DIAGRAM.md
├── Use Case - Super Admin
├── Use Case - Admin
├── Use Case - Agent
├── Use Case - Support Agent
├── Use Case - User
├── Use Case - Guest
└── Use Case Matrix

ERD_VISUAL.md
├── ERD Text-based
├── Relasi Detail
└── Cardinality Summary
```

---

**Selamat menggunakan dokumentasi! 🎉**
