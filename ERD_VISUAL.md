# ERD Visual - OS-Tiket (CSIRT Kalselprov)

## 📊 Entity Relationship Diagram (Text-based)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          ERD - OS-Tiket System                          │
└─────────────────────────────────────────────────────────────────────────┘

┌──────────────┐
│ organisasi   │
│──────────────│
│ PK id        │
│    nama (UK) │
└──────┬───────┘
       │
       │ 1:N
       │
┌──────▼──────────────┐
│     pengguna        │
│─────────────────────│
│ PK id               │
│ FK id_organisasi    │
│    nama             │
│    email (UK)       │
│    kata_sandi       │
│    telepon          │
│ nama_pengguna_telegram
│ id_chat_telegram    │
└──┬──────┬──────┬────┘
   │      │      │
   │ 1:N  │ 1:N  │ N:M
   │      │      │
   │      │      └──────────┐
   │      │                 │
┌──▼──────▼──────┐    ┌─────▼──────┐
│     tiket      │    │    tim     │
│────────────────│    │────────────│
│ PK id          │    │ PK id      │
│ FK id_pengguna │    │    nama(UK)│
│ FK ditugaskan_ke│    └────────────┘
│ FK id_departemen│
│ FK id_status   │
│ FK id_prioritas │
│ FK id_topik_bantuan│
│ FK id_rencana_sla│
│ nomor_tiket(UK)│
│ subjek         │
│ email_pelapor  │
│ nama_pelapor   │
│ jatuh_tempo_pada│
│ ditutup_pada   │
│ bidang_kustom  │
└──┬─────────────┘
   │
   │ 1:N
   │
┌──▼──────────────┐
│  utas_tiket     │
│────────────────│
│ PK id           │
│ FK id_tiket     │
│ FK id_pengguna  │
│ tipe            │
│ isi             │
└──┬─────────────┘
   │
   │ 1:N
   │
┌──▼──────────────┐
│   lampiran      │
│────────────────│
│ PK id           │
│ FK id_utas_tiket│
│ nama_file       │
│ mime            │
│ ukuran          │
│ path            │
└────────────────┘

┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│ departemen   │      │    status    │      │  prioritas   │
│──────────────│      │──────────────│      │──────────────│
│ PK id        │      │ PK id        │      │ PK id        │
│    nama      │      │    nama      │      │    nama (UK) │
│    email     │      │ slug (UK)    │      │    bobot     │
│ publik       │      │ menutup      │      └──────┬───────┘
└──────┬───────┘      └──────┬───────┘            │
       │                     │                     │
       │ 1:N                 │ 1:N                │ 1:N
       │                     │                     │
       │                     │                     │
┌──────▼───────┐      ┌──────▼───────┐      ┌──────▼───────┐
│ topik_bantuan│      │    tiket     │      │    tiket     │
│──────────────│      │              │      │              │
│ PK id        │      │              │      │              │
│ FK id_departemen│    │              │      │              │
│    nama      │      │              │      │              │
│ skema_formulir│     │              │      │              │
└──────┬───────┘      └──────────────┘      └──────────────┘
       │
       │ 1:N
       │
┌──────▼───────┐
│    tiket     │
│              │
└─────────────┘

┌──────────────┐
│ rencana_sla  │
│──────────────│
│ PK id        │
│    nama (UK) │
│ jam_grace    │
└──────┬───────┘
       │
       │ 1:N
       │
┌──────▼───────┐
│    tiket     │
│              │
└─────────────┘

┌─────────────────┐
│ respons_template│
│─────────────────│
│ PK id           │
│    judul        │
│    isi          │
└─────────────────┘
(No Relations)

┌──────────────┐      ┌──────────────┐
│    roles      │      │ permissions  │
│──────────────│      │──────────────│
│ PK id        │      │ PK id        │
│    name (UK) │      │    name (UK) │
│ guard_name   │      │ guard_name   │
└──────┬───────┘      └──────┬───────┘
       │                    │
       │ N:M                │ N:M
       │                    │
       │ (via role_has_permissions)
       │                    │
       │ N:M                │
       │                    │
       │ (via model_has_roles)
       │                    │
┌──────▼───────┐
│    users     │
│              │
└──────────────┘
```

## 🔗 Relasi Detail

### One-to-Many (1:N)

1. **Organisasi → Pengguna**

    - Satu organisasi memiliki banyak user
    - `organisasi.id` → `pengguna.id_organisasi`

2. **Pengguna → Tiket (as Requester)**

    - Satu user dapat membuat banyak tiket
    - `pengguna.id` → `tiket.id_pengguna`

3. **Pengguna → Tiket (as Agent)**

    - Satu agent dapat ditugaskan ke banyak tiket
    - `pengguna.id` → `tiket.ditugaskan_ke`

4. **Pengguna → UtasTiket**

    - Satu user dapat membuat banyak thread
    - `pengguna.id` → `utas_tiket.id_pengguna`

5. **Tiket → UtasTiket**

    - Satu tiket memiliki banyak thread
    - `tiket.id` → `utas_tiket.id_tiket`

6. **UtasTiket → Lampiran**

    - Satu thread dapat memiliki banyak attachment
    - `utas_tiket.id` → `lampiran.id_utas_tiket`

7. **Departemen → Tiket**

    - Satu departemen memiliki banyak tiket
    - `departemen.id` → `tiket.id_departemen`

8. **Departemen → TopikBantuan**

    - Satu departemen memiliki banyak help topic
    - `departemen.id` → `topik_bantuan.id_departemen`

9. **Status → Tiket**

    - Satu status dapat digunakan oleh banyak tiket
    - `status.id` → `tiket.id_status`

10. **Prioritas → Tiket**

    - Satu prioritas dapat digunakan oleh banyak tiket
    - `prioritas.id` → `tiket.id_prioritas`

11. **TopikBantuan → Tiket**

    - Satu help topic dapat digunakan oleh banyak tiket
    - `topik_bantuan.id` → `tiket.id_topik_bantuan`

12. **RencanaSla → Tiket**
    - Satu SLA plan dapat digunakan oleh banyak tiket
    - `rencana_sla.id` → `tiket.id_rencana_sla`

### Many-to-Many (N:M)

1. **Pengguna ↔ Tim**

    - Satu user dapat berada di banyak tim
    - Satu tim memiliki banyak user
    - Via pivot table: `tim_pengguna`
    - `pengguna.id` ↔ `tim.id`

2. **Pengguna ↔ Role** (Spatie Permission)

    - Satu user dapat memiliki banyak role
    - Satu role dapat dimiliki banyak user
    - Via pivot table: `model_has_roles`
    - `pengguna.id` ↔ `roles.id`

3. **Role ↔ Permission** (Spatie Permission)
    - Satu role dapat memiliki banyak permission
    - Satu permission dapat dimiliki banyak role
    - Via pivot table: `role_has_permissions`
    - `roles.id` ↔ `permissions.id`

## 📊 Cardinality Summary

| Entity 1     | Relationship | Entity 2     | Type |
| ------------ | ------------ | ------------ | ---- |
| Organisasi   | has          | Pengguna     | 1:N  |
| Pengguna     | creates      | Tiket        | 1:N  |
| Pengguna     | assigned to  | Tiket        | 1:N  |
| Pengguna     | writes       | UtasTiket    | 1:N  |
| Pengguna     | belongs to   | Tim          | N:M  |
| Pengguna     | has          | Roles        | N:M  |
| Tiket        | has          | UtasTiket    | 1:N  |
| UtasTiket    | has          | Lampiran     | 1:N  |
| Departemen   | has          | Tiket        | 1:N  |
| Departemen   | has          | TopikBantuan | 1:N  |
| Status       | used by      | Tiket        | 1:N  |
| Prioritas    | used by      | Tiket        | 1:N  |
| TopikBantuan | used by      | Tiket        | 1:N  |
| RencanaSla   | used by      | Tiket        | 1:N  |
| Role         | has          | Permissions  | N:M  |

## 🎯 Key Relationships

### Core Ticket Flow

```
Pengguna (Pelapor) → Tiket → UtasTiket → Lampiran
                      ↓
                  Departemen
                      ↓
                  TopikBantuan
                      ↓
                  Status, Prioritas, RencanaSla
                      ↓
                  Pengguna (Agent)
```

### User Management Flow

```
Organisasi → Pengguna → Role → Permission
                ↓
              Tim
```

---

**File Migration**: `database/migrations/`
**File Models**: `app/Models/`
