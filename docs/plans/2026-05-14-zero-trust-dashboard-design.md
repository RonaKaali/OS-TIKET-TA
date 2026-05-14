# Design Document: Zero Trust Security Dashboard (Live Feed)

## 1. Overview
Implementing a high-end, real-time security event monitoring dashboard for the Super Admin panel. This feature aims to provide visual proof of the Zero Trust Security integration for the final project defense (Sidang Akhir).

## 2. Objectives
- Provide visual evidence of "Never Trust, Always Verify" principles.
- Demonstrate real-time monitoring of security events.
- Enhance UI/UX for a "premium" government application feel.

## 3. Architecture
### Data Source
- Table: `security_events`
- Controller: `App\Http\Controllers\Admin\SecurityDashboardController`
- Model: `App\Models\SecurityEvent`

### Real-time Mechanism
- **Alpine.js Polling**: The frontend will poll the server every 60 seconds (1 minute) to fetch new security events.
- **Backend Optimization**: Fetch only the latest 20 events, ordered by `created_at` DESC.

## 4. UI/UX Design
### Visual Style
- **Glassmorphism**: Semi-transparent cards with backdrop blur.
- **Cyber Theme**: Dark mode optimized with vibrant status colors (Blue/Yellow/Red).
- **Animations**: Pulse animations for "Live" status and smooth transitions for new events.

### Components
- **Header**: Stats overview (Logins today, Anomaly count, Avg Trust Score).
- **Timeline Feed**: Vertical list of events with:
    - Event Icon (Shield, Lock, Warning).
    - User Identity (Name/Email).
    - Technical Metadata (IP, Browser, OS, Country Badge).
    - Trust Score Progress Bar.
    - Timestamp.

## 5. Data Flow
1. User logs in/triggers a security event.
2. `SecurityEventLogService` saves event to database.
3. Super Admin opens Zero Trust Dashboard.
4. Alpine.js initializes and fetches initial data.
5. Every 60s, Alpine.js calls `GET /admin/security-feed/latest`.
6. Frontend compares IDs and appends new rows with a slide-down animation.

## 6. Testing Plan
- Simulate login from a new device/incognito to trigger `anomaly_detected`.
- Verify MFA triggers and successes appear in the feed.
- Ensure polling interval works and UI doesn't flicker on update.

---
**Approved by:** User
**Date:** 2026-05-14
