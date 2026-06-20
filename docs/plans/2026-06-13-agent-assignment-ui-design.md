# Design Document: Surat Tugas (Official Assignment Letter) UI & Agent Workflow Fixes

**Date**: 2026-06-13  
**Status**: Approved (Approach A)

## Context & Objectives

The agent workflow currently has navigation locks and layout mismatches:
1. **Action Button Visibility**: "Selesaikan Tiket" and "Kembalikan ke Super Admin" buttons are displayed to non-assigned roles (like Super Admin), resulting in potential 403 errors and visual clutter.
2. **Dashboard circular lock**: Agents with unacknowledged tickets are blocked from clicking anything on the dashboard. The middleware blocks the route `/agent/tickets/{ticket}`.
3. **Surat Tugas Layout**: The assignment letter should look like a professional, official invoice-style letter from Dinas Kominfo Kalselprov (Kop Surat, formal headers, digital seal, and QR code).

## Solution Architecture

### 1. Workflow & Middleware Fixes
- **Bypass Show Page**: Modify [EnsureAssignmentsAcknowledged](file:///c:/Users/LENOVO/OneDrive/Desktop/os-tiket%20last/os-tiket/app/Http/Middleware/EnsureAssignmentsAcknowledged.php) to permit requests on `agent.tickets.show` so that agents can visit unacknowledged tickets to trigger auto-acknowledgment.
- **Always-Clickable Links**: Modify the dashboard view [field-agent.blade.php](file:///c:/Users/LENOVO/OneDrive/Desktop/os-tiket%20last/os-tiket/resources/views/agent/dashboard/field-agent.blade.php) to render active links for all tickets. 
  - Unacknowledged tickets display a glowing orange **"Lihat Surat Tugas"** button.
  - Acknowledged tickets display a neutral **"Buka Tiket"** button.
- **Action Restriction**: Restrict visibility of the sidebar action buttons in [show.blade.php](file:///c:/Users/LENOVO/OneDrive/Desktop/os-tiket%20last/os-tiket/resources/views/agent/tickets/show.blade.php) to the assigned agent (`Auth::id() === $ticket->assigned_to`).

### 2. Surat Tugas Resmi Layout (Dinas Kominfo Kalselprov)
We will replace the generic "Informasi Kontrol Laporan" card inside `show.blade.php` with a beautiful official assignment letter resembling a document sheet:
- **Header (Kop Surat)**: Logo Pemprov Kalsel, title *DINAS KOMUNIKASI, INFORMATIKA, DAN STATISTIK*, subtitle *CSIRT KALSELPROV*, and a double horizontal border.
- **Document Number**: `ST/{TICKET_NUMBER}/CSIRT/{YEAR}`.
- **Dasar Hukum**: Standard formal preamble explaining the security response purpose.
- **Assignee Table & Details**: Invoice-style layout listing incident details (priority, sector, assignment date, and SLA).
- **Security Stamp & QR Code**: QR code generated via SimpleSoftwareIO QrCode facade containing the ticket link, alongside a green holographic "Zero Trust Verified" stamp.

## Verification
- Test login with Agent 2. Check if the dashboard links are clickable.
- Open unacknowledged ticket. Check if the warning disappears.
- View ticket detail page as Super Admin. Check if sidebar action buttons are hidden.
