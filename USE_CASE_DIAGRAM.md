# Use Case Diagram - OS-Tiket (CSIRT Kalselprov)

## 📋 Daftar Isi

1. [Use Case - Super Admin](#use-case---super-admin)
2. [Use Case - Admin](#use-case---admin)
3. [Use Case - Agent](#use-case---agent)
4. [Use Case - Support Agent](#use-case---support-agent)
5. [Use Case - User (Pelapor)](#use-case---user-pelapor)
6. [Use Case - Guest (Tidak Login)](#use-case---guest-tidak-login)
7. [Use Case Diagram Text Representation](#use-case-diagram-text-representation)

---

## 🎯 Use Case - Super Admin

### Deskripsi

Super Admin memiliki akses penuh ke semua fitur sistem.

### Use Cases

#### 1. **Manajemen User**

-   ✅ **View Users** - Melihat daftar semua user
-   ✅ **Create User** - Membuat user baru
-   ✅ **Update User** - Mengupdate data user
-   ✅ **Delete User** - Menghapus user
-   ✅ **Assign Role** - Menetapkan role ke user
-   ✅ **Manage Permissions** - Mengelola permission user

#### 2. **Manajemen Tiket**

-   ✅ **View All Tickets** - Melihat semua tiket
-   ✅ **View Ticket Detail** - Melihat detail tiket
-   ✅ **Create Ticket** - Membuat tiket baru
-   ✅ **Update Ticket** - Mengupdate tiket (status, prioritas, dll)
-   ✅ **Delete Ticket** - Menghapus tiket
-   ✅ **Assign Ticket** - Menugaskan tiket ke agent
-   ✅ **Close Ticket** - Menutup tiket
-   ✅ **Reply Ticket** - Membalas tiket
-   ✅ **Add Note** - Menambahkan catatan internal

#### 3. **Manajemen Master Data**

-   ✅ **Manage Departments** - CRUD departemen
-   ✅ **Manage Statuses** - CRUD status
-   ✅ **Manage Priorities** - CRUD prioritas
-   ✅ **Manage Help Topics** - CRUD help topic
-   ✅ **Manage SLA Plans** - CRUD SLA plan
-   ✅ **Manage Organizations** - CRUD organisasi
-   ✅ **Manage Teams** - CRUD tim
-   ✅ **Manage Canned Responses** - CRUD template balasan

#### 4. **Dashboard & Reporting**

-   ✅ **View Dashboard** - Melihat dashboard admin
-   ✅ **View Statistics** - Melihat statistik tiket
-   ✅ **Generate Reports** - Generate laporan

#### 5. **Sistem**

-   ✅ **Manage Roles** - Mengelola roles
-   ✅ **Manage Permissions** - Mengelola permissions
-   ✅ **System Configuration** - Konfigurasi sistem

---

## 👨‍💼 Use Case - Admin

### Deskripsi

Admin memiliki akses terbatas untuk mengelola sistem dan tiket.

### Use Cases

#### 1. **Manajemen User**

-   ✅ **View Users** - Melihat daftar user
-   ✅ **Create User** - Membuat user baru
-   ✅ **Update User** - Mengupdate data user
-   ❌ **Delete User** - Tidak bisa menghapus user

#### 2. **Manajemen Tiket**

-   ✅ **View All Tickets** - Melihat semua tiket
-   ✅ **View Ticket Detail** - Melihat detail tiket
-   ✅ **Create Ticket** - Membuat tiket baru
-   ✅ **Update Ticket** - Mengupdate tiket
-   ✅ **Assign Ticket** - Menugaskan tiket ke agent
-   ✅ **Close Ticket** - Menutup tiket
-   ✅ **Reply Ticket** - Membalas tiket
-   ❌ **Delete Ticket** - Tidak bisa menghapus tiket

#### 3. **Manajemen Master Data**

-   ✅ **View Departments** - Melihat departemen
-   ✅ **Create Department** - Membuat departemen
-   ✅ **Update Department** - Mengupdate departemen
-   ❌ **Delete Department** - Tidak bisa menghapus departemen
-   ✅ **Manage Other Master Data** - CRUD untuk master data lain (terbatas)

#### 4. **Dashboard & Reporting**

-   ✅ **View Dashboard** - Melihat dashboard admin
-   ✅ **View Statistics** - Melihat statistik tiket

---

## 👤 Use Case - Agent

### Deskripsi

Agent menangani tiket dan berkomunikasi dengan pelapor.

### Use Cases

#### 1. **Manajemen Tiket**

-   ✅ **View Assigned Tickets** - Melihat tiket yang ditugaskan
-   ✅ **View All Tickets** - Melihat semua tiket (read-only)
-   ✅ **View Ticket Detail** - Melihat detail tiket
-   ✅ **Update Ticket** - Mengupdate tiket (status, prioritas)
-   ✅ **Assign Ticket** - Menugaskan tiket ke agent lain
-   ✅ **Reply Ticket** - Membalas tiket pelapor
-   ✅ **Add Note** - Menambahkan catatan internal
-   ✅ **Upload Attachment** - Mengupload lampiran
-   ❌ **Delete Ticket** - Tidak bisa menghapus tiket
-   ❌ **Close Ticket** - Tidak bisa menutup tiket (hanya update status)

#### 2. **Canned Responses**

-   ✅ **View Canned Responses** - Melihat template balasan
-   ✅ **Use Canned Response** - Menggunakan template balasan

#### 3. **Dashboard**

-   ✅ **View Dashboard** - Melihat dashboard agent
-   ✅ **View Statistics** - Melihat statistik tiket yang ditugaskan

#### 4. **Profile**

-   ✅ **View Profile** - Melihat profil sendiri
-   ✅ **Update Profile** - Mengupdate profil sendiri
-   ✅ **Set Telegram Username** - Mengatur username Telegram

---

## 🛠 Use Case - Support Agent

### Deskripsi

Support Agent memiliki akses terbatas untuk melihat dan mengupdate tiket.

### Use Cases

#### 1. **Manajemen Tiket**

-   ✅ **View Tickets** - Melihat tiket (read-only)
-   ✅ **View Ticket Detail** - Melihat detail tiket
-   ✅ **Update Ticket** - Mengupdate tiket (terbatas)
-   ❌ **Assign Ticket** - Tidak bisa menugaskan tiket
-   ❌ **Delete Ticket** - Tidak bisa menghapus tiket
-   ❌ **Close Ticket** - Tidak bisa menutup tiket

#### 2. **Profile**

-   ✅ **View Profile** - Melihat profil sendiri
-   ✅ **Update Profile** - Mengupdate profil sendiri

---

## 👥 Use Case - User (Pelapor)

### Deskripsi

User adalah pelapor insiden siber yang terdaftar di sistem.

### Use Cases

#### 1. **Pelaporan Insiden**

-   ✅ **Create Ticket** - Membuat laporan insiden baru
-   ✅ **Upload Attachment** - Mengupload lampiran saat membuat tiket
-   ✅ **View My Tickets** - Melihat tiket yang dibuat sendiri
-   ✅ **View Ticket Detail** - Melihat detail tiket sendiri
-   ✅ **Reply Ticket** - Membalas tiket (komunikasi dengan agent)
-   ✅ **Check Ticket Status** - Mengecek status tiket

#### 2. **Autentikasi**

-   ✅ **Register** - Mendaftar akun baru
-   ✅ **Login** - Login ke sistem
-   ✅ **Logout** - Logout dari sistem
-   ✅ **Forgot Password** - Reset password
-   ✅ **Verify Email** - Verifikasi email

#### 3. **Profile Management**

-   ✅ **View Profile** - Melihat profil sendiri
-   ✅ **Update Profile** - Mengupdate profil sendiri
-   ✅ **Change Password** - Mengubah password
-   ✅ **Set Telegram Username** - Mengatur username Telegram
-   ✅ **Delete Account** - Menghapus akun sendiri

#### 4. **Portal**

-   ✅ **View Landing Page** - Melihat halaman utama
-   ✅ **View Help Topics** - Melihat kategori insiden
-   ✅ **View Departments** - Melihat departemen

---

## 🚶 Use Case - Guest (Tidak Login)

### Deskripsi

Guest adalah pengunjung yang belum login.

### Use Cases

#### 1. **Informasi**

-   ✅ **View Landing Page** - Melihat halaman utama
-   ✅ **View About** - Melihat informasi tentang CSIRT
-   ✅ **View Help Topics** - Melihat kategori insiden
-   ✅ **View Departments** - Melihat departemen

#### 2. **Cek Status Tiket**

-   ✅ **Check Ticket Status** - Mengecek status tiket dengan nomor tiket dan email
-   ✅ **View Ticket Detail (Public)** - Melihat detail tiket (terbatas, tanpa balasan)

#### 3. **Autentikasi**

-   ✅ **Register** - Mendaftar akun baru
-   ✅ **Login** - Login ke sistem
-   ✅ **Forgot Password** - Request reset password

---

## 📊 Use Case Diagram Text Representation

```
┌─────────────────────────────────────────────────────────────────┐
│                        OS-Tiket System                         │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                         Super Admin                             │
├─────────────────────────────────────────────────────────────────┤
│ • Manage Users (CRUD)                                           │
│ • Manage Tickets (CRUD + Assign + Close)                        │
│ • Manage Master Data (All CRUD)                                 │
│ • Manage Roles & Permissions                                    │
│ • View Dashboard & Statistics                                   │
│ • System Configuration                                          │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                            Admin                                │
├─────────────────────────────────────────────────────────────────┤
│ • Manage Users (View, Create, Update)                           │
│ • Manage Tickets (View, Create, Update, Assign, Close)         │
│ • Manage Master Data (Limited CRUD)                              │
│ • View Dashboard & Statistics                                   │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                            Agent                                │
├─────────────────────────────────────────────────────────────────┤
│ • View Tickets (All + Assigned)                                │
│ • Update Ticket (Status, Priority)                              │
│ • Assign Ticket                                                 │
│ • Reply Ticket                                                  │
│ • Add Note                                                      │
│ • Upload Attachment                                             │
│ • Use Canned Responses                                         │
│ • View Dashboard                                                │
│ • Manage Profile                                                │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                       Support Agent                            │
├─────────────────────────────────────────────────────────────────┤
│ • View Tickets                                                  │
│ • Update Ticket (Limited)                                       │
│ • Manage Profile                                                │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      User (Pelapor)                            │
├─────────────────────────────────────────────────────────────────┤
│ • Create Ticket                                                 │
│ • View My Tickets                                               │
│ • Reply Ticket                                                  │
│ • Check Ticket Status                                           │
│ • Upload Attachment                                             │
│ • Register / Login / Logout                                     │
│ • Manage Profile                                                │
│ • Set Telegram Username                                         │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                         Guest                                   │
├─────────────────────────────────────────────────────────────────┤
│ • View Landing Page                                             │
│ • View Information (About, Help Topics, Departments)          │
│ • Check Ticket Status (with Ticket Number + Email)             │
│ • Register                                                      │
│ • Login                                                         │
│ • Forgot Password                                               │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      Common Use Cases                           │
├─────────────────────────────────────────────────────────────────┤
│ • Authentication (Login, Logout, Register)                      │
│ • Profile Management                                            │
│ • Notification (Email + Telegram)                              │
│ • File Upload/Download                                          │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Use Case Flow - Create Ticket (User)

```
1. User Login
   ↓
2. Navigate to "Laporkan Insiden"
   ↓
3. Fill Form:
   - Subject
   - Department
   - Help Topic
   - Priority
   - Description
   - Attachments (optional)
   ↓
4. Submit Ticket
   ↓
5. System:
   - Generate Ticket Number
   - Save to Database
   - Send Notification (Email + Telegram)
     • To User (NewTicketSubmitted)
     • To Admin/Agent (NewTicketCreated)
   ↓
6. Redirect to Ticket Detail
   ↓
7. User can:
   - View Ticket
   - Reply to Ticket
   - Check Status
```

---

## 🔄 Use Case Flow - Assign Ticket (Agent)

```
1. Agent Login
   ↓
2. View Ticket List
   ↓
3. Select Ticket
   ↓
4. Click "Assign"
   ↓
5. Select Agent
   ↓
6. System:
   - Update assigned_to
   - Update Status (if needed)
   - Send Notification (TicketAssigned)
     • To Assigned Agent (Email + Telegram)
   ↓
7. Redirect to Ticket Detail
```

---

## 🔄 Use Case Flow - Reply Ticket (Agent)

```
1. Agent Login
   ↓
2. View Assigned Tickets
   ↓
3. Select Ticket
   ↓
4. View Threads
   ↓
5. Click "Reply"
   ↓
6. Fill Reply:
   - Body (or use Canned Response)
   - Attachments (optional)
   ↓
7. Submit Reply
   ↓
8. System:
   - Create TicketThread (type: reply)
   - Update Status (if needed)
   - Send Notification (TicketReplyFromAgent)
     • To Requester (Email + Telegram)
   ↓
9. Redirect to Ticket Detail
```

---

## 🔄 Use Case Flow - Reply Ticket (User)

```
1. User Login
   ↓
2. View My Tickets
   ↓
3. Select Ticket
   ↓
4. View Threads
   ↓
5. Click "Reply"
   ↓
6. Fill Reply:
   - Body
   - Attachments (optional)
   ↓
7. Submit Reply
   ↓
8. System:
   - Create TicketThread (type: message)
   - Update Status (answered)
   - Send Notification (TicketReplyFromRequester)
     • To Assigned Agent (Email + Telegram)
   ↓
9. Redirect to Ticket Detail
```

---

## 🔄 Use Case Flow - Update Status (Agent)

```
1. Agent Login
   ↓
2. View Ticket
   ↓
3. Click "Update Status"
   ↓
4. Select New Status
   ↓
5. System:
   - Update Status
   - Update closed_at (if status is closing)
   - Send Notification (TicketStatusChanged)
     • To Requester (Email + Telegram)
   ↓
6. Redirect to Ticket Detail
```

---

## 📋 Use Case Matrix

| Use Case                      | Super Admin | Admin        | Agent | Support Agent | User     | Guest        |
| ----------------------------- | ----------- | ------------ | ----- | ------------- | -------- | ------------ |
| **Authentication**            |
| Login                         | ✅          | ✅           | ✅    | ✅            | ✅       | ✅           |
| Register                      | ✅          | ✅           | ✅    | ✅            | ✅       | ✅           |
| Logout                        | ✅          | ✅           | ✅    | ✅            | ✅       | ❌           |
| **Ticket Management**         |
| Create Ticket                 | ✅          | ✅           | ❌    | ❌            | ✅       | ❌           |
| View All Tickets              | ✅          | ✅           | ✅    | ✅            | ❌       | ❌           |
| View My Tickets               | ✅          | ✅           | ✅    | ✅            | ✅       | ❌           |
| View Ticket Detail            | ✅          | ✅           | ✅    | ✅            | ✅ (own) | ✅ (limited) |
| Update Ticket                 | ✅          | ✅           | ✅    | ✅ (limited)  | ❌       | ❌           |
| Delete Ticket                 | ✅          | ❌           | ❌    | ❌            | ❌       | ❌           |
| Assign Ticket                 | ✅          | ✅           | ✅    | ❌            | ❌       | ❌           |
| Close Ticket                  | ✅          | ✅           | ❌    | ❌            | ❌       | ❌           |
| Reply Ticket                  | ✅          | ✅           | ✅    | ❌            | ✅ (own) | ❌           |
| Add Note                      | ✅          | ✅           | ✅    | ❌            | ❌       | ❌           |
| **User Management**           |
| View Users                    | ✅          | ✅           | ❌    | ❌            | ❌       | ❌           |
| Create User                   | ✅          | ✅           | ❌    | ❌            | ❌       | ❌           |
| Update User                   | ✅          | ✅           | ❌    | ❌            | ❌       | ❌           |
| Delete User                   | ✅          | ❌           | ❌    | ❌            | ❌       | ❌           |
| Assign Role                   | ✅          | ❌           | ❌    | ❌            | ❌       | ❌           |
| **Master Data**               |
| Manage Departments            | ✅          | ✅ (limited) | ❌    | ❌            | ❌       | ❌           |
| Manage Statuses               | ✅          | ✅ (limited) | ❌    | ❌            | ❌       | ❌           |
| Manage Priorities             | ✅          | ✅ (limited) | ❌    | ❌            | ❌       | ❌           |
| Manage Help Topics            | ✅          | ✅ (limited) | ❌    | ❌            | ❌       | ❌           |
| Manage SLA Plans              | ✅          | ✅ (limited) | ❌    | ❌            | ❌       | ❌           |
| Manage Organizations          | ✅          | ✅ (limited) | ❌    | ❌            | ❌       | ❌           |
| Manage Teams                  | ✅          | ✅ (limited) | ❌    | ❌            | ❌       | ❌           |
| Manage Canned Responses       | ✅          | ✅ (limited) | ❌    | ❌            | ❌       | ❌           |
| **Dashboard**                 |
| View Dashboard                | ✅          | ✅           | ✅    | ❌            | ❌       | ❌           |
| View Statistics               | ✅          | ✅           | ✅    | ❌            | ❌       | ❌           |
| **Profile**                   |
| View Profile                  | ✅          | ✅           | ✅    | ✅            | ✅       | ❌           |
| Update Profile                | ✅          | ✅           | ✅    | ✅            | ✅       | ❌           |
| Change Password               | ✅          | ✅           | ✅    | ✅            | ✅       | ❌           |
| Set Telegram Username         | ✅          | ✅           | ✅    | ✅            | ✅       | ❌           |
| **Portal**                    |
| View Landing Page             | ✅          | ✅           | ✅    | ✅            | ✅       | ✅           |
| Check Ticket Status           | ✅          | ✅           | ✅    | ✅            | ✅       | ✅           |
| **Notifications**             |
| Receive Email Notification    | ✅          | ✅           | ✅    | ✅            | ✅       | ❌           |
| Receive Telegram Notification | ✅          | ✅           | ✅    | ✅            | ✅       | ❌           |

---

**File Controller**: `app/Http/Controllers/`
**File Routes**: `routes/web.php`
**File Middleware**: `app/Http/Middleware/`
