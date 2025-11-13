# ✅ Test Checklist - OS-Tiket CSIRT Kalselprov

**Tanggal Testing**: ******\_\_\_******  
**Tester**: ******\_\_\_******  
**Environment**: ******\_\_\_******

---

## 🔐 MODUL 1: AUTHENTICATION & AUTHORIZATION

| ID     | Test Case                             | Status | Notes |
| ------ | ------------------------------------- | ------ | ----- |
| TC-001 | Registrasi Pengguna Baru              | ⬜     |       |
| TC-002 | Login dengan Kredensial Valid         | ⬜     |       |
| TC-003 | Login dengan Kredensial Invalid       | ⬜     |       |
| TC-004 | Logout                                | ⬜     |       |
| TC-005 | Akses Halaman Terproteksi Tanpa Login | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## 📝 MODUL 2: PORTAL PELAPOR

| ID     | Test Case                          | Status | Notes |
| ------ | ---------------------------------- | ------ | ----- |
| TC-006 | Membuat Tiket Baru (User Login)    | ⬜     |       |
| TC-007 | Membuat Tiket dengan Lampiran      | ⬜     |       |
| TC-008 | Cek Status Tiket (Guest/Non-Login) | ⬜     |       |
| TC-009 | Melihat Detail Tiket (Guest)       | ⬜     |       |
| TC-010 | Balasan dari Pelapor (Guest)       | ⬜     |       |
| TC-011 | Balasan dari Pelapor (User Login)  | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## 👨‍💼 MODUL 3: AGENT PANEL

| ID     | Test Case                          | Status | Notes |
| ------ | ---------------------------------- | ------ | ----- |
| TC-012 | Akses Agent Dashboard              | ⬜     |       |
| TC-013 | Melihat Daftar Tiket (Agent)       | ⬜     |       |
| TC-014 | Filter Tiket (Agent)               | ⬜     |       |
| TC-015 | Melihat Detail Tiket (Agent)       | ⬜     |       |
| TC-016 | Membalas Tiket (Agent)             | ⬜     |       |
| TC-017 | Mengubah Status Tiket (Agent)      | ⬜     |       |
| TC-018 | Assign Tiket ke Agent              | ⬜     |       |
| TC-019 | Membuat Catatan Internal (Agent)   | ⬜     |       |
| TC-020 | Akses Agent Panel Tanpa Permission | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## 🔧 MODUL 4: ADMIN PANEL

| ID     | Test Case                           | Status | Notes |
| ------ | ----------------------------------- | ------ | ----- |
| TC-021 | Akses Admin Panel (Super Admin)     | ⬜     |       |
| TC-022 | Akses Admin Panel (Non-Super Admin) | ⬜     |       |
| TC-023 | CRUD Users (Admin)                  | ⬜     |       |
| TC-024 | CRUD Departments (Admin)            | ⬜     |       |
| TC-025 | CRUD Help Topics (Admin)            | ⬜     |       |
| TC-026 | CRUD SLA Plans (Admin)              | ⬜     |       |
| TC-027 | CRUD Priorities (Admin)             | ⬜     |       |
| TC-028 | CRUD Statuses (Admin)               | ⬜     |       |
| TC-029 | CRUD Teams (Admin)                  | ⬜     |       |
| TC-030 | CRUD Canned Responses (Admin)       | ⬜     |       |
| TC-031 | CRUD Organizations (Admin)          | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## ✔️ MODUL 5: VALIDASI & ERROR HANDLING

| ID     | Test Case                    | Status | Notes |
| ------ | ---------------------------- | ------ | ----- |
| TC-032 | Validasi Form Tiket (Portal) | ⬜     |       |
| TC-033 | Validasi Upload File         | ⬜     |       |
| TC-034 | Validasi Email               | ⬜     |       |
| TC-035 | Handling Error 404           | ⬜     |       |
| TC-036 | Handling Error 403           | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## 📧 MODUL 6: NOTIFIKASI EMAIL

| ID     | Test Case                              | Status | Notes |
| ------ | -------------------------------------- | ------ | ----- |
| TC-037 | Notifikasi Tiket Baru ke Pelapor       | ⬜     |       |
| TC-038 | Notifikasi Balasan Agent ke Pelapor    | ⬜     |       |
| TC-039 | Notifikasi Perubahan Status ke Pelapor | ⬜     |       |
| TC-040 | Notifikasi Assignment ke Agent         | ⬜     |       |
| TC-041 | Notifikasi Balasan Pelapor ke Agent    | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## 📱 MODUL 7: UI/UX & RESPONSIVENESS

| ID     | Test Case                  | Status | Notes |
| ------ | -------------------------- | ------ | ----- |
| TC-042 | Responsive Design (Mobile) | ⬜     |       |
| TC-043 | Loading State & Feedback   | ⬜     |       |
| TC-044 | Navigation & Breadcrumb    | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## 🔄 MODUL 8: INTEGRASI & ALUR BISNIS

| ID     | Test Case                               | Status | Notes |
| ------ | --------------------------------------- | ------ | ----- |
| TC-045 | Alur Lengkap: Pelapor → Agent → Selesai | ⬜     |       |
| TC-046 | Alur dengan Assignment                  | ⬜     |       |
| TC-047 | Multiple Thread/Conversation            | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## 🔒 MODUL 9: KEAMANAN

| ID     | Test Case                | Status | Notes |
| ------ | ------------------------ | ------ | ----- |
| TC-048 | SQL Injection Prevention | ⬜     |       |
| TC-049 | XSS Prevention           | ⬜     |       |
| TC-050 | CSRF Protection          | ⬜     |       |
| TC-051 | Authorization Check      | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## ⚡ MODUL 10: PERFORMANCE & EDGE CASES

| ID     | Test Case             | Status | Notes |
| ------ | --------------------- | ------ | ----- |
| TC-052 | Pagination (Jika Ada) | ⬜     |       |
| TC-053 | Search Functionality  | ⬜     |       |
| TC-054 | Concurrent Access     | ⬜     |       |
| TC-055 | Large File Upload     | ⬜     |       |

**Status**: ⬜ Not Tested | ✅ Pass | ❌ Fail | ⚠️ Blocked

---

## 📊 SUMMARY

### Test Results Summary

| Module                         | Total  | Passed   | Failed   | Blocked  | Pass Rate |
| ------------------------------ | ------ | -------- | -------- | -------- | --------- |
| Authentication & Authorization | 5      | \_\_     | \_\_     | \_\_     | \_\_%     |
| Portal Pelapor                 | 6      | \_\_     | \_\_     | \_\_     | \_\_%     |
| Agent Panel                    | 9      | \_\_     | \_\_     | \_\_     | \_\_%     |
| Admin Panel                    | 11     | \_\_     | \_\_     | \_\_     | \_\_%     |
| Validasi & Error Handling      | 5      | \_\_     | \_\_     | \_\_     | \_\_%     |
| Notifikasi Email               | 5      | \_\_     | \_\_     | \_\_     | \_\_%     |
| UI/UX & Responsiveness         | 3      | \_\_     | \_\_     | \_\_     | \_\_%     |
| Integrasi & Alur Bisnis        | 3      | \_\_     | \_\_     | \_\_     | \_\_%     |
| Keamanan                       | 4      | \_\_     | \_\_     | \_\_     | \_\_%     |
| Performance & Edge Cases       | 4      | \_\_     | \_\_     | \_\_     | \_\_%     |
| **TOTAL**                      | **55** | **\_\_** | **\_\_** | **\_\_** | **\_\_%** |

---

## 🐛 BUGS FOUND

### Critical Bugs

| Bug ID | Description | TC ID | Status |
| ------ | ----------- | ----- | ------ |
|        |             |       |        |

### High Priority Bugs

| Bug ID | Description | TC ID | Status |
| ------ | ----------- | ----- | ------ |
|        |             |       |        |

### Medium Priority Bugs

| Bug ID | Description | TC ID | Status |
| ------ | ----------- | ----- | ------ |
|        |             |       |        |

### Low Priority Bugs

| Bug ID | Description | TC ID | Status |
| ------ | ----------- | ----- | ------ |
|        |             |       |        |

---

## 📝 NOTES & OBSERVATIONS

### Positive Findings

-
-
-

### Issues & Concerns

-
-
-

### Recommendations

-
-
-

---

## ✅ SIGN-OFF

**Tester Name**: ******\_\_\_******  
**Date**: ******\_\_\_******  
**Signature**: ******\_\_\_******

**Reviewer Name**: ******\_\_\_******  
**Date**: ******\_\_\_******  
**Signature**: ******\_\_\_******

---

## 📌 HOW TO USE THIS CHECKLIST

1. **Copy file ini** untuk setiap test session
2. **Isi informasi** di bagian header (Tanggal, Tester, Environment)
3. **Test setiap test case** sesuai urutan atau prioritas
4. **Update status** dengan simbol:
    - ⬜ = Not Tested (belum di-test)
    - ✅ = Pass (berhasil)
    - ❌ = Fail (gagal)
    - ⚠️ = Blocked (terblokir, tidak bisa di-test)
5. **Tulis notes** jika ada catatan penting
6. **Update summary** setelah selesai testing
7. **Dokumentasikan bugs** yang ditemukan
8. **Isi notes & observations** untuk feedback
9. **Sign-off** setelah selesai

---

**Good Luck with Testing! 🚀**
