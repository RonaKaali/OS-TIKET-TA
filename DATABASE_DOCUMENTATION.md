# Dokumentasi Database - OS-Tiket (CSIRT Kalselprov)

## 📋 Daftar Isi

1. [CDM (Conceptual Data Model)](#cdm-conceptual-data-model)
2. [LDM (Logical Data Model)](#ldm-logical-data-model)
3. [PDM (Physical Data Model)](#pdm-physical-data-model)
4. [ERD (Entity Relationship Diagram)](#erd-entity-relationship-diagram)
5. [Use Case Diagram](#use-case-diagram)

---

## 🎯 CDM (Conceptual Data Model)

### Deskripsi

CDM menggambarkan entitas-entitas utama dalam sistem dan hubungan konseptualnya tanpa detail teknis.

### Entitas Utama

#### 1. **User (Pengguna)**

-   **Deskripsi**: Pengguna sistem (admin, agent, atau pelapor)
-   **Atribut Kunci**: Email, Nama, Role
-   **Relasi**:
    -   Memiliki banyak tiket (sebagai pelapor)
    -   Ditugaskan ke banyak tiket (sebagai agent)
    -   Berada dalam organisasi
    -   Anggota dari tim

#### 2. **Ticket (Tiket/Laporan)**

-   **Deskripsi**: Laporan insiden siber dari pelapor
-   **Atribut Kunci**: Nomor tiket, Subjek, Status, Prioritas
-   **Relasi**:
    -   Dimiliki oleh User (pelapor)
    -   Ditugaskan ke User (agent)
    -   Memiliki banyak thread (percakapan)
    -   Terkait dengan Departemen, Status, Prioritas, Help Topic, SLA Plan

#### 3. **TicketThread (Thread/Balasan)**

-   **Deskripsi**: Pesan/balasan dalam tiket
-   **Atribut Kunci**: Tipe (message/reply/note), Isi pesan
-   **Relasi**:
    -   Milik Ticket
    -   Dibuat oleh User
    -   Memiliki banyak Attachment

#### 4. **Attachment (Lampiran)**

-   **Deskripsi**: File yang dilampirkan pada thread
-   **Atribut Kunci**: Nama file, Tipe file, Ukuran
-   **Relasi**: Milik TicketThread

#### 5. **Department (Departemen)**

-   **Deskripsi**: Departemen yang menangani kategori tiket tertentu
-   **Atribut Kunci**: Nama, Email
-   **Relasi**: Memiliki banyak Ticket dan Help Topic

#### 6. **Status (Status)**

-   **Deskripsi**: Status tiket (Terbuka, Ditugaskan, Tertutup, dll)
-   **Atribut Kunci**: Nama, Slug, Is Closing
-   **Relasi**: Memiliki banyak Ticket

#### 7. **Priority (Prioritas)**

-   **Deskripsi**: Tingkat prioritas tiket (Low, Normal, High, Critical)
-   **Atribut Kunci**: Nama, Weight
-   **Relasi**: Memiliki banyak Ticket

#### 8. **HelpTopic (Topik Bantuan)**

-   **Deskripsi**: Kategori insiden siber
-   **Atribut Kunci**: Nama, Form Schema
-   **Relasi**:
    -   Milik Department
    -   Memiliki banyak Ticket

#### 9. **SlaPlan (SLA Plan)**

-   **Deskripsi**: Rencana Service Level Agreement untuk penanganan tiket
-   **Atribut Kunci**: Nama, Grace Hours
-   **Relasi**: Memiliki banyak Ticket

#### 10. **Organization (Organisasi)**

-   **Deskripsi**: Organisasi/instansi pengguna
-   **Atribut Kunci**: Nama
-   **Relasi**: Memiliki banyak User

#### 11. **Team (Tim)**

-   **Deskripsi**: Tim agent yang menangani tiket
-   **Atribut Kunci**: Nama
-   **Relasi**: Memiliki banyak User (many-to-many)

#### 12. **CannedResponse (Template Balasan)**

-   **Deskripsi**: Template balasan cepat untuk agent
-   **Atribut Kunci**: Judul, Isi
-   **Relasi**: Tidak ada (standalone)

---

## 🔗 LDM (Logical Data Model)

### Deskripsi

LDM menggambarkan struktur data dengan detail atribut, tipe data, dan relasi yang lebih spesifik.

### Tabel dan Atribut

#### 1. **pengguna**

| Kolom                    | Tipe Data    | Constraint         | Deskripsi              |
| ------------------------ | ------------ | ------------------ | ---------------------- |
| id                       | BIGINT       | PK, AUTO_INCREMENT | Primary key            |
| id_organisasi            | BIGINT       | FK → organisasi.id | Organisasi user        |
| nama                     | VARCHAR(255) | NOT NULL           | Nama lengkap           |
| email                    | VARCHAR(255) | UNIQUE, NOT NULL   | Email (unique)         |
| email_terverifikasi_pada | TIMESTAMP    | NULL               | Waktu verifikasi email |
| kata_sandi               | VARCHAR(255) | NOT NULL           | Password (hashed)      |
| telepon                  | VARCHAR(20)  | NULL               | Nomor telepon          |
| nama_pengguna_telegram   | VARCHAR(255) | NULL               | Username Telegram      |
| id_chat_telegram         | VARCHAR(255) | NULL               | Chat ID Telegram       |
| remember_token           | VARCHAR(100) | NULL               | Token remember         |
| created_at               | TIMESTAMP    | NULL               | Waktu dibuat           |
| updated_at               | TIMESTAMP    | NULL               | Waktu diupdate         |

**Relasi:**

-   `id_organisasi` → `organisasi.id` (Many-to-One)
-   `pengguna.id` ← `tiket.id_pengguna` (One-to-Many)
-   `pengguna.id` ← `tiket.ditugaskan_ke` (One-to-Many)
-   `pengguna.id` ← `utas_tiket.id_pengguna` (One-to-Many)
-   `pengguna.id` ↔ `tim.id` via `tim_pengguna` (Many-to-Many)

#### 2. **tiket**

| Kolom            | Tipe Data    | Constraint                   | Deskripsi                    |
| ---------------- | ------------ | ---------------------------- | ---------------------------- |
| id               | BIGINT       | PK, AUTO_INCREMENT           | Primary key                  |
| uuid             | UUID         | UNIQUE                       | UUID tiket                   |
| nomor_tiket      | VARCHAR(255) | UNIQUE, NOT NULL             | Nomor tiket (OST-000001)     |
| subjek           | VARCHAR(255) | NOT NULL                     | Subjek tiket                 |
| email_pelapor    | VARCHAR(255) | NOT NULL                     | Email pelapor                |
| nama_pelapor     | VARCHAR(255) | NULL                         | Nama pelapor                 |
| id_pengguna      | BIGINT       | FK → pengguna.id, NULL       | User pelapor (jika ada akun) |
| id_departemen    | BIGINT       | FK → departemen.id, NOT NULL | Departemen                   |
| id_topik_bantuan | BIGINT       | FK → topik_bantuan.id, NULL  | Topik bantuan                |
| id_prioritas     | BIGINT       | FK → prioritas.id, NULL      | Prioritas                    |
| id_status        | BIGINT       | FK → status.id, NOT NULL     | Status                       |
| id_rencana_sla   | BIGINT       | FK → rencana_sla.id, NULL    | SLA Plan                     |
| jatuh_tempo_pada | DATETIME     | NULL                         | Batas waktu                  |
| ditutup_pada     | DATETIME     | NULL                         | Waktu ditutup                |
| ditugaskan_ke    | BIGINT       | FK → pengguna.id, NULL       | Agent yang ditugaskan        |
| dikunci_oleh     | BIGINT       | FK → pengguna.id, NULL       | User yang mengunci           |
| dikunci_sampai   | DATETIME     | NULL                         | Waktu kunci berakhir         |
| bidang_kustom    | JSON         | NULL                         | Field custom dari form       |
| created_at       | TIMESTAMP    | NULL                         | Waktu dibuat                 |
| updated_at       | TIMESTAMP    | NULL                         | Waktu diupdate               |

**Index:**

-   `(id_status, id_departemen)`
-   `ditugaskan_ke`
-   `jatuh_tempo_pada`

**Relasi:**

-   `id_pengguna` → `pengguna.id` (Many-to-One, nullable)
-   `id_departemen` → `departemen.id` (Many-to-One)
-   `id_topik_bantuan` → `topik_bantuan.id` (Many-to-One, nullable)
-   `id_prioritas` → `prioritas.id` (Many-to-One, nullable)
-   `id_status` → `status.id` (Many-to-One)
-   `id_rencana_sla` → `rencana_sla.id` (Many-to-One, nullable)
-   `ditugaskan_ke` → `pengguna.id` (Many-to-One, nullable)
-   `dikunci_oleh` → `pengguna.id` (Many-to-One, nullable)
-   `tiket.id` ← `utas_tiket.id_tiket` (One-to-Many)

#### 3. **utas_tiket**

| Kolom       | Tipe Data | Constraint              | Deskripsi                   |
| ----------- | --------- | ----------------------- | --------------------------- |
| id          | BIGINT    | PK, AUTO_INCREMENT      | Primary key                 |
| id_tiket    | BIGINT    | FK → tiket.id, NOT NULL | Tiket terkait               |
| tipe        | ENUM      | NOT NULL                | Tipe: pesan/balasan/catatan |
| id_pengguna | BIGINT    | FK → pengguna.id, NULL  | User yang membuat           |
| isi         | LONGTEXT  | NOT NULL                | Isi pesan                   |
| created_at  | TIMESTAMP | NULL                    | Waktu dibuat                |
| updated_at  | TIMESTAMP | NULL                    | Waktu diupdate              |

**Relasi:**

-   `id_tiket` → `tiket.id` (Many-to-One)
-   `id_pengguna` → `pengguna.id` (Many-to-One, nullable)
-   `utas_tiket.id` ← `lampiran.id_utas_tiket` (One-to-Many)

#### 4. **lampiran**

| Kolom         | Tipe Data       | Constraint                   | Deskripsi            |
| ------------- | --------------- | ---------------------------- | -------------------- |
| id            | BIGINT          | PK, AUTO_INCREMENT           | Primary key          |
| id_utas_tiket | BIGINT          | FK → utas_tiket.id, NOT NULL | Thread terkait       |
| nama_file     | VARCHAR(255)    | NOT NULL                     | Nama file            |
| mime          | VARCHAR(255)    | NOT NULL                     | Tipe MIME            |
| ukuran        | BIGINT UNSIGNED | NOT NULL                     | Ukuran file (bytes)  |
| path          | VARCHAR(255)    | NOT NULL                     | Path file di storage |
| created_at    | TIMESTAMP       | NULL                         | Waktu dibuat         |
| updated_at    | TIMESTAMP       | NULL                         | Waktu diupdate       |

**Relasi:**

-   `id_utas_tiket` → `utas_tiket.id` (Many-to-One)

#### 5. **departemen**

| Kolom      | Tipe Data    | Constraint         | Deskripsi        |
| ---------- | ------------ | ------------------ | ---------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary key      |
| nama       | VARCHAR(255) | NOT NULL           | Nama departemen  |
| email      | VARCHAR(255) | NULL               | Email departemen |
| publik     | BOOLEAN      | DEFAULT TRUE       | Apakah publik    |
| created_at | TIMESTAMP    | NULL               | Waktu dibuat     |
| updated_at | TIMESTAMP    | NULL               | Waktu diupdate   |

**Relasi:**

-   `departemen.id` ← `tiket.id_departemen` (One-to-Many)
-   `departemen.id` ← `topik_bantuan.id_departemen` (One-to-Many)

#### 6. **status**

| Kolom      | Tipe Data    | Constraint         | Deskripsi                |
| ---------- | ------------ | ------------------ | ------------------------ |
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary key              |
| nama       | VARCHAR(255) | NOT NULL           | Nama status              |
| slug       | VARCHAR(255) | UNIQUE, NOT NULL   | Slug (open, closed, dll) |
| menutup    | BOOLEAN      | DEFAULT FALSE      | Apakah status penutup    |
| created_at | TIMESTAMP    | NULL               | Waktu dibuat             |
| updated_at | TIMESTAMP    | NULL               | Waktu diupdate           |

**Relasi:**

-   `status.id` ← `tiket.id_status` (One-to-Many)

#### 7. **prioritas**

| Kolom      | Tipe Data        | Constraint         | Deskripsi      |
| ---------- | ---------------- | ------------------ | -------------- |
| id         | BIGINT           | PK, AUTO_INCREMENT | Primary key    |
| nama       | VARCHAR(255)     | UNIQUE, NOT NULL   | Nama prioritas |
| bobot      | TINYINT UNSIGNED | NOT NULL           | Bobot (1-10)   |
| created_at | TIMESTAMP        | NULL               | Waktu dibuat   |
| updated_at | TIMESTAMP        | NULL               | Waktu diupdate |

**Relasi:**

-   `prioritas.id` ← `tiket.id_prioritas` (One-to-Many, nullable)

#### 8. **topik_bantuan**

| Kolom          | Tipe Data    | Constraint                   | Deskripsi          |
| -------------- | ------------ | ---------------------------- | ------------------ |
| id             | BIGINT       | PK, AUTO_INCREMENT           | Primary key        |
| nama           | VARCHAR(255) | NOT NULL                     | Nama topik         |
| id_departemen  | BIGINT       | FK → departemen.id, NOT NULL | Departemen         |
| skema_formulir | JSON         | NULL                         | Schema form custom |
| created_at     | TIMESTAMP    | NULL                         | Waktu dibuat       |
| updated_at     | TIMESTAMP    | NULL                         | Waktu diupdate     |

**Relasi:**

-   `id_departemen` → `departemen.id` (Many-to-One)
-   `topik_bantuan.id` ← `tiket.id_topik_bantuan` (One-to-Many, nullable)

#### 9. **rencana_sla**

| Kolom      | Tipe Data    | Constraint         | Deskripsi        |
| ---------- | ------------ | ------------------ | ---------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary key      |
| nama       | VARCHAR(255) | UNIQUE, NOT NULL   | Nama SLA plan    |
| jam_grace  | INT UNSIGNED | NOT NULL           | Jam grace period |
| created_at | TIMESTAMP    | NULL               | Waktu dibuat     |
| updated_at | TIMESTAMP    | NULL               | Waktu diupdate   |

**Relasi:**

-   `rencana_sla.id` ← `tiket.id_rencana_sla` (One-to-Many, nullable)

#### 10. **organisasi**

| Kolom      | Tipe Data    | Constraint         | Deskripsi       |
| ---------- | ------------ | ------------------ | --------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary key     |
| nama       | VARCHAR(255) | UNIQUE, NOT NULL   | Nama organisasi |
| created_at | TIMESTAMP    | NULL               | Waktu dibuat    |
| updated_at | TIMESTAMP    | NULL               | Waktu diupdate  |

**Relasi:**

-   `organisasi.id` ← `pengguna.id_organisasi` (One-to-Many, nullable)

#### 11. **tim**

| Kolom      | Tipe Data    | Constraint         | Deskripsi      |
| ---------- | ------------ | ------------------ | -------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary key    |
| nama       | VARCHAR(255) | UNIQUE, NOT NULL   | Nama tim       |
| created_at | TIMESTAMP    | NULL               | Waktu dibuat   |
| updated_at | TIMESTAMP    | NULL               | Waktu diupdate |

**Relasi:**

-   `tim.id` ↔ `pengguna.id` via `tim_pengguna` (Many-to-Many)

#### 12. **tim_pengguna** (Pivot Table)

| Kolom       | Tipe Data | Constraint           | Deskripsi      |
| ----------- | --------- | -------------------- | -------------- |
| id_tim      | BIGINT    | FK → tim.id, PK      | ID tim         |
| id_pengguna | BIGINT    | FK → pengguna.id, PK | ID user        |
| created_at  | TIMESTAMP | NULL                 | Waktu dibuat   |
| updated_at  | TIMESTAMP | NULL                 | Waktu diupdate |

#### 13. **respons_template**

| Kolom      | Tipe Data    | Constraint         | Deskripsi      |
| ---------- | ------------ | ------------------ | -------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary key    |
| judul      | VARCHAR(255) | NOT NULL           | Judul template |
| isi        | LONGTEXT     | NOT NULL           | Isi template   |
| created_at | TIMESTAMP    | NULL               | Waktu dibuat   |
| updated_at | TIMESTAMP    | NULL               | Waktu diupdate |

**Relasi:** Tidak ada (standalone)

#### 14. **roles** (Spatie Permission)

| Kolom      | Tipe Data    | Constraint         | Deskripsi      |
| ---------- | ------------ | ------------------ | -------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary key    |
| name       | VARCHAR(255) | UNIQUE, NOT NULL   | Nama role      |
| guard_name | VARCHAR(255) | DEFAULT 'web'      | Guard name     |
| created_at | TIMESTAMP    | NULL               | Waktu dibuat   |
| updated_at | TIMESTAMP    | NULL               | Waktu diupdate |

#### 15. **permissions** (Spatie Permission)

| Kolom      | Tipe Data    | Constraint         | Deskripsi       |
| ---------- | ------------ | ------------------ | --------------- |
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary key     |
| name       | VARCHAR(255) | UNIQUE, NOT NULL   | Nama permission |
| guard_name | VARCHAR(255) | DEFAULT 'web'      | Guard name      |
| created_at | TIMESTAMP    | NULL               | Waktu dibuat    |
| updated_at | TIMESTAMP    | NULL               | Waktu diupdate  |

#### 16. **model_has_roles** (Spatie Permission)

| Kolom      | Tipe Data    | Constraint        | Deskripsi                    |
| ---------- | ------------ | ----------------- | ---------------------------- |
| role_id    | BIGINT       | FK → roles.id, PK | ID role                      |
| model_type | VARCHAR(255) | PK                | Model type (App\Models\User) |
| model_id   | BIGINT       | PK                | ID model                     |

#### 17. **role_has_permissions** (Spatie Permission)

| Kolom         | Tipe Data | Constraint              | Deskripsi     |
| ------------- | --------- | ----------------------- | ------------- |
| permission_id | BIGINT    | FK → permissions.id, PK | ID permission |
| role_id       | BIGINT    | FK → roles.id, PK       | ID role       |

---

## 💾 PDM (Physical Data Model)

### Deskripsi

PDM menggambarkan implementasi fisik database dengan detail storage, indexing, dan optimasi.

### Database Engine

-   **Engine**: InnoDB (MySQL/MariaDB)
-   **Charset**: utf8mb4
-   **Collation**: utf8mb4_unicode_ci

### Indexing Strategy

#### Primary Indexes

-   Semua tabel menggunakan `id` sebagai PRIMARY KEY dengan AUTO_INCREMENT

#### Unique Indexes

-   `pengguna.email` - UNIQUE
-   `tiket.uuid` - UNIQUE
-   `tiket.nomor_tiket` - UNIQUE
-   `status.slug` - UNIQUE
-   `prioritas.nama` - UNIQUE
-   `rencana_sla.nama` - UNIQUE
-   `organisasi.nama` - UNIQUE
-   `tim.nama` - UNIQUE
-   `roles.name` - UNIQUE
-   `permissions.name` - UNIQUE

#### Composite Indexes

-   `tiket(id_status, id_departemen)` - Untuk query filter tiket
-   `tiket.ditugaskan_ke` - Untuk query tiket yang ditugaskan
-   `tiket.jatuh_tempo_pada` - Untuk query SLA dan due date

#### Foreign Key Indexes

-   Semua foreign key otomatis ter-index oleh MySQL

### Storage Considerations

#### Large Text Fields

-   `utas_tiket.isi` - LONGTEXT (untuk pesan panjang)
-   `respons_template.isi` - LONGTEXT (untuk template panjang)
-   `tiket.bidang_kustom` - JSON (untuk field custom)
-   `topik_bantuan.skema_formulir` - JSON (untuk schema form)

#### File Storage

-   `lampiran.path` - VARCHAR(255) (path relatif ke storage disk)
-   File fisik disimpan di `storage/app/public/attachments/`

### Constraints

#### Foreign Key Constraints

-   **ON DELETE CASCADE**:

    -   `tiket.id_departemen` → `departemen.id`
    -   `tiket.id_status` → `status.id`
    -   `utas_tiket.id_tiket` → `tiket.id`
    -   `lampiran.id_utas_tiket` → `utas_tiket.id`
    -   `topik_bantuan.id_departemen` → `departemen.id`

-   **ON DELETE NULL**:
    -   `tiket.id_pengguna` → `pengguna.id`
    -   `tiket.ditugaskan_ke` → `pengguna.id`
    -   `tiket.id_topik_bantuan` → `topik_bantuan.id`
    -   `tiket.id_prioritas` → `prioritas.id`
    -   `tiket.id_rencana_sla` → `rencana_sla.id`
    -   `utas_tiket.id_pengguna` → `pengguna.id`
    -   `pengguna.id_organisasi` → `organisasi.id`

### Performance Optimization

#### Query Optimization

1. **Tiket Listing**: Index pada `(id_status, id_departemen)` untuk filter cepat
2. **Assigned Tickets**: Index pada `ditugaskan_ke` untuk query tiket per agent
3. **SLA Monitoring**: Index pada `jatuh_tempo_pada` untuk query tiket yang akan due

#### Caching Strategy

-   Master data (departemen, status, prioritas) dapat di-cache
-   User roles dan permissions di-cache oleh Spatie Permission

---

## 🔗 ERD (Entity Relationship Diagram)

### Text-based ERD

```
┌─────────────────┐
│   organisasi    │
│─────────────────│
│ PK id           │
│    nama (UK)    │
└────────┬────────┘
         │
         │ 1
         │
         │ N
┌────────▼────────┐
│    pengguna     │
│─────────────────│
│ PK id           │
│ FK id_organisasi│
│    nama         │
│    email (UK)   │
│    kata_sandi   │
│    telepon      │
│ nama_pengguna_telegram
│ id_chat_telegram │
└────┬───────┬────┘
     │       │
     │ 1     │ 1
     │       │
     │ N     │ N
     │       │
┌────▼────┐  │  ┌──────────────┐
│  tiket  │  │  │  utas_tiket  │
│─────────│  │  │──────────────│
│ PK id   │  │  │ PK id        │
│ FK id_pengguna │  │ FK id_tiket  │
│ FK ditugaskan_ke││ FK id_pengguna│
│ FK id_departemen│ tipe        │
│ FK id_status   │ isi          │
│ FK id_prioritas│              │
│ FK id_topik_bantuan│          │
│ FK id_rencana_sla│            │
│ nomor_tiket(UK)│              │
│ subjek         │              │
│ email_pelapor  │              │
│ nama_pelapor    │              │
│ jatuh_tempo_pada│             │
│ ditutup_pada    │             │
│ bidang_kustom   │             │
└────┬─────┘     │              │
     │           │              │
     │ 1         │ 1            │
     │           │              │
     │ N         │ N            │
     │           │              │
     │      ┌────▼──────────────┘
     │      │
     │      │ 1
     │      │
     │      │ N
     │      │
┌────▼─────▼──────┐
│   lampiran      │
│─────────────────│
│ PK id           │
│ FK id_utas_tiket│
│    nama_file    │
│    mime         │
│    ukuran       │
│    path         │
└─────────────────┘

┌──────────────┐      ┌──────────────┐
│ departemen   │      │    status    │
│──────────────│      │──────────────│
│ PK id        │      │ PK id        │
│    nama      │      │    nama      │
│    email     │      │    slug (UK) │
│    publik    │      │ menutup      │
└──────┬───────┘      └──────┬───────┘
       │                     │
       │ 1                    │ 1
       │                      │
       │ N                    │ N
       │                      │
┌──────▼───────┐      ┌──────▼───────┐
│ topik_bantuan│      │    tiket     │
│──────────────│      │              │
│ PK id        │      │              │
│ FK id_departemen    │              │
│    nama      │      │              │
│ skema_formulir│     │              │
└──────┬───────┘      └──────────────┘
       │
       │ 1
       │
       │ N
       │
┌──────▼───────┐
│    tiket     │
│              │
└──────────────┘

┌──────────────┐      ┌──────────────┐
│  prioritas   │      │ rencana_sla  │
│──────────────│      │──────────────│
│ PK id        │      │ PK id        │
│    nama (UK) │      │    nama (UK) │
│    bobot     │      │ jam_grace    │
└──────┬───────┘      └──────┬───────┘
       │                     │
       │ 1                    │ 1
       │                      │
       │ N                    │ N
       │                      │
┌──────▼───────┐      ┌──────▼───────┐
│    tiket     │      │    tiket     │
│              │      │              │
└──────────────┘      └──────────────┘

┌──────────────┐
│     tim      │
│──────────────│
│ PK id        │
│    nama (UK) │
└──────┬───────┘
       │
       │ N
       │
       │ N (via tim_pengguna)
       │
┌──────▼───────┐
│   pengguna   │
│              │
└──────────────┘

┌─────────────────┐
│ respons_template│
│─────────────────│
│ PK id           │
│    judul        │
│    isi          │
└─────────────────┘
(Standalone - no relations)

┌──────────────┐      ┌──────────────┐
│    roles      │      │ permissions  │
│──────────────│      │──────────────│
│ PK id        │      │ PK id        │
│    name (UK) │      │    name (UK) │
│ guard_name   │      │ guard_name   │
└──────┬───────┘      └──────┬───────┘
       │                    │
       │ N                   │ N
       │                     │
       │ (via role_has_permissions)
       │                     │
       │                     │
       │ N                   │
       │                     │
       │ (via model_has_roles)
       │                     │
┌──────▼───────┐
│   pengguna   │
│              │
└──────────────┘
```

### Relasi Utama

1. **Pengguna ↔ Tiket**

    - Pengguna (pelapor) → Tiket: One-to-Many (via `id_pengguna`)
    - Pengguna (agent) → Tiket: One-to-Many (via `ditugaskan_ke`)

2. **Tiket → UtasTiket**

    - One-to-Many: Satu tiket memiliki banyak thread

3. **UtasTiket → Lampiran**

    - One-to-Many: Satu thread memiliki banyak attachment

4. **Departemen → Tiket**

    - One-to-Many: Satu departemen memiliki banyak tiket

5. **Departemen → TopikBantuan**

    - One-to-Many: Satu departemen memiliki banyak help topic

6. **Status → Tiket**

    - One-to-Many: Satu status memiliki banyak tiket

7. **Prioritas → Tiket**

    - One-to-Many: Satu prioritas memiliki banyak tiket

8. **RencanaSla → Tiket**

    - One-to-Many: Satu SLA plan memiliki banyak tiket

9. **Organisasi → Pengguna**

    - One-to-Many: Satu organisasi memiliki banyak user

10. **Tim ↔ Pengguna**

    - Many-to-Many: Via pivot table `tim_pengguna`

11. **User ↔ Role**

    - Many-to-Many: Via Spatie Permission (`model_has_roles`)

12. **Role ↔ Permission**
    - Many-to-Many: Via Spatie Permission (`role_has_permissions`)

---

## 📊 Use Case Diagram

Lihat file terpisah: `USE_CASE_DIAGRAM.md`

---

**File Migration**: `database/migrations/`
**File Seeder**: `database/seeders/`
