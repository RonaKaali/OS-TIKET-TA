# Design Document: Agent Notification and Action Buttons Enhancement

## Overview
This document outlines the design to replace the disruptive new assignment popup with an interactive notification bell dropdown in the navbar, and to add standard completion/reassignment actions to the ticket detail page.

## Proposed Changes

### 1. Notification Bell Component
- Replace the popup component (`new-assignment-popup.blade.php`) with an AlpineJS dropdown notification bell in `agent-layout.blade.php`.
- The notification dropdown polls `/agent/assignments/pending` and updates a counter badge.
- Clicking an item in the dropdown redirects the agent to the ticket detail page.

### 2. Auto-Acknowledgment
- When an agent views a ticket via `agent.tickets.show`, the application automatically marks the assignment as acknowledged if `acknowledged_at` is null.
- This removes it from the pending notifications list immediately.

### 3. Ticket Detail Actions
- Add two prominent action buttons to the sidebar of `agent/tickets/show.blade.php`:
  1. **Berhasil Dikerjakan** (Mark as Completed): Sets status to 'closed' and sets `closed_at` to now. Automatically triggers the email notification to the reporter.
  2. **Kembalikan ke Super Admin** (Return to Super Admin): Clears the agent assignment (`assigned_to` and `assigned_at` set to null) and sets status back to 'open'.

## Database Requirements
- Ensure `acknowledged_at` column exists on `tiket` table to track when a ticket assignment was acknowledged/opened by the agent.
