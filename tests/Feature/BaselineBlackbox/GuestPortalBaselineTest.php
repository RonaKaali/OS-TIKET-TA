<?php

namespace Tests\Feature\BaselineBlackbox;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestPortalBaselineTest extends TestCase
{
    use RefreshDatabase;
    protected bool $seed = true;

    // ========================================================================
    // TC-006: Halaman Welcome (Public)
    // ========================================================================
    public function test_welcome_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_welcome_page_has_correct_content(): void
    {
        $response = $this->get('/');
        $response->assertSee('CSIRT');
    }

    // ========================================================================
    // TC-007: Cek Status Tiket (Public + Verifikasi)
    // ========================================================================
    public function test_ticket_status_form_is_accessible(): void
    {
        $response = $this->get(route('portal.ticket.status.form'));
        $response->assertStatus(200);
    }

    public function test_ticket_status_check_with_valid_credentials(): void
    {
        // Ambil tiket dari seeder
        $ticket = Ticket::where('status_id', function ($q) {
            $q->select('id')->from('status')->where('slug', 'open')->limit(1);
        })->first();

        $this->assertNotNull($ticket, 'No open ticket found from seeder');

        $response = $this->post(route('portal.ticket.status.check'), [
            'ticket_number' => $ticket->ticket_number,
            'email' => $ticket->reporter_email,
        ]);

        $response->assertRedirect(route('portal.ticket.show', $ticket->ticket_number));
    }

    public function test_ticket_status_check_fails_with_invalid_ticket_number(): void
    {
        $response = $this->post(route('portal.ticket.status.check'), [
            'ticket_number' => 'NONEXISTENT-0000',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('not_found');
    }

    public function test_ticket_status_check_fails_with_wrong_email(): void
    {
        $ticket = Ticket::first();
        $this->assertNotNull($ticket);

        $response = $this->post(route('portal.ticket.status.check'), [
            'ticket_number' => $ticket->ticket_number,
            'email' => 'wrong@example.com',
        ]);

        $response->assertSessionHasErrors('not_found');
    }

    // ========================================================================
    // TC-008: Lihat Tiket via Nomor (Setelah Verifikasi)
    // ========================================================================
    public function test_cannot_view_ticket_without_verification(): void
    {
        $ticket = Ticket::first();
        $this->assertNotNull($ticket);

        $response = $this->get(route('portal.ticket.show', $ticket->ticket_number));

        $response->assertRedirect(route('portal.ticket.status.form'));
    }

    public function test_can_view_ticket_after_verification(): void
    {
        $ticket = Ticket::first();
        $this->assertNotNull($ticket);

        // First, verify via status check (sets session)
        $this->post(route('portal.ticket.status.check'), [
            'ticket_number' => $ticket->ticket_number,
            'email' => $ticket->reporter_email,
        ]);

        // Then access the ticket
        $response = $this->get(route('portal.ticket.show', $ticket->ticket_number));
        $response->assertStatus(200);
        $response->assertSee($ticket->ticket_number);
        $response->assertSee($ticket->subject);
    }

    public function test_logged_in_owner_can_view_ticket_directly(): void
    {
        $ticket = Ticket::first();
        $this->assertNotNull($ticket);

        // Login sebagai user yang memiliki tiket
        $user = \App\Models\User::where('email', $ticket->reporter_email)->first();
        if (!$user) {
            // Jika pelapor bukan user terdaftar, buat user
            $user = \App\Models\User::factory()->create([
                'email' => $ticket->reporter_email,
            ]);
        }

        $response = $this->actingAs($user)
            ->get(route('portal.ticket.show', $ticket->ticket_number));

        $response->assertStatus(200);
        $response->assertSee($ticket->ticket_number);
    }
}
