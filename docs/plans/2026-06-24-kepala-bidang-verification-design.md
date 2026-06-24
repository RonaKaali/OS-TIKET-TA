# Design Document: Kepala Bidang Verification Workflow & Security Log Spam Fix

## 1. Kepala Bidang (Support Agent) Verification Workflow

### Objective
Modify the "Support Agent" role to function as "Kepala Bidang". When an Admin assigns a ticket (Surat Tugas) to an Agent, the assignment must first be verified and approved by a Kepala Bidang before the Agent is notified and can start working.

### Workflow (Option A: Verifikasi Persetujuan)
1. **Assignment Initiation:** Admin selects an Agent to assign a ticket to.
2. **Pending Verification State:** The ticket is assigned to the Agent, but its status is set to `menunggu_verifikasi_kepala_bidang`.
3. **Kepala Bidang Review:** Users with the `Kepala Bidang` role see a queue of tickets pending verification.
4. **Approval:** The Kepala Bidang reviews the assignment and clicks "Verifikasi/Setujui".
5. **Activation:** The ticket status changes to `assigned`. A notification is sent to the assigned Agent, and they can now view and work on the ticket.

### Database & Model Changes
- Add a new status `menunggu_verifikasi_kepala_bidang` to the `status` table via a database seeder.
- Modify `RoleUi.php` to rename constant/label from "Support Agent" to "Kepala Bidang".
- The assignment logic will check if the assigned status is used, but if it needs verification, it uses the new status instead.

### UI/UX Changes
- **Admin Assignment:** UI remains mostly the same, but success message changes to indicate it's awaiting verification.
- **Kepala Bidang Dashboard:** Needs a new section or view to list tickets pending verification. Add an "Approve" button for these tickets.
- **Agent Dashboard:** Agents should NOT see tickets that are still pending verification in their active queue. We will filter tickets where `status.slug === 'menunggu_verifikasi_kepala_bidang'` from their main list.

## 2. Security Event Logging Spam Fix

### Objective
Stop the repeated generation of "Device registered" security event logs caused by overly sensitive device fingerprinting.

### Root Cause
The `generateFingerprint` method in `DeviceFingerprintService.php` uses highly volatile HTTP headers such as `Accept-Encoding`, `Accept-Language`, `Accept`, and `Connection`. Any slight variation in these headers (which is common across different requests from the same browser) generates a new hash, causing the system to treat the device as new and register it repeatedly.

### Solution
- Simplify the fingerprint components to only use stable indicators:
  - `User-Agent`
  - `IP Address` (Client IP)
  - `Screen Resolution` (if available via JS)
  - `Timezone` (if available via JS)
- Remove `Accept`, `Accept-Encoding`, `Accept-Language`, `DNT`, and `Connection` from the hashing array in `DeviceFingerprintService.php`.
- This ensures the fingerprint remains stable across normal session requests, preventing the spam of "Device registered" logs while still identifying truly new devices.
