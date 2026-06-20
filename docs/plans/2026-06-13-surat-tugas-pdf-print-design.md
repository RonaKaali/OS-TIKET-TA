# Surat Tugas Print to PDF Design

## Overview
User wants the "Lihat Selengkapnya" button on the agent ticket view to function as a print/download feature for the "Surat Tugas" (Assignment Letter) in PDF format without adding heavy backend dependencies.

## Approved Approach
"Cetak via Browser (Print to PDF)" - Creates a dedicated, clean HTML view of the letter that automatically opens the browser's print dialog, allowing the user to "Save as PDF".

## Implementation Details
1. **Routing:**
   - Add a new route in `routes/web.php` for `agent.tickets.print`.

2. **Controller:**
   - Add `print(Ticket $ticket)` method to `Agent\TicketController` that returns the `agent.tickets.print` view.

3. **View (`resources/views/agent/tickets/print.blade.php`):**
   - Clean, standalone HTML/Blade file (no dashboard layout).
   - Use Tailwind CSS to replicate the Surat Tugas design.
   - Include `@media print` rules: `print-color-adjust: exact`, `@page { margin: 0; }`.
   - Add `<script>window.onload = function() { window.print(); }</script>`.

4. **UI Update:**
   - Update `show.blade.php` button text to "Cetak / Download PDF" and link to `route('agent.tickets.print', $ticket)`.
