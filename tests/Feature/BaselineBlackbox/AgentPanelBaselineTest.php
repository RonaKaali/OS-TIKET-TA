<?php

namespace Tests\Feature\BaselineBlackbox;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentPanelBaselineTest extends TestCase
{
    use RefreshDatabase;
    protected bool $seed = true;

    private ?User $superAdmin = null;
    private ?User $agent = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::where('email', 'admin@csirt.kalselprov.go.id')->first();
        $this->agent = User::where('email', 'agent@csirt.kalselprov.go.id')->first();
    }

    // ========================================================================
    // TC-012: Akses Dashboard Agent
    // ========================================================================
    public function test_agent_dashboard_is_accessible_by_agent(): void
    {
        $this->assertNotNull($this->agent);

        $response = $this->actingAs($this->agent)
            ->get(route('agent.dashboard'));

        $response->assertStatus(200);
    }

    public function test_agent_dashboard_is_accessible_by_super_admin(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('agent.dashboard'));

        $response->assertStatus(200);
    }

    public function test_agent_dashboard_is_blocked_for_regular_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('agent.dashboard'));

        $response->assertStatus(403);
    }

    // ========================================================================
    // TC-013: Melihat Daftar Tiket (Agent Panel)
    // ========================================================================
    public function test_agent_ticket_list_is_accessible(): void
    {
        $this->assertNotNull($this->agent);

        $response = $this->actingAs($this->agent)
            ->get(route('agent.tickets.index'));

        $response->assertStatus(200);
    }

    public function test_agent_ticket_list_shows_tickets(): void
    {
        $this->assertNotNull($this->agent);

        $response = $this->actingAs($this->agent)
            ->get(route('agent.tickets.index'));

        $response->assertStatus(200);
        $response->assertSee('Tiket');
    }

    public function test_super_admin_can_see_all_tickets(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('agent.tickets.index'));

        $response->assertStatus(200);
    }

    // ========================================================================
    // TC-014: Melihat Detail Tiket
    // ========================================================================
    public function test_agent_can_view_assigned_ticket_detail(): void
    {
        $this->assertNotNull($this->agent);

        // Agent 1 hanya bisa lihat tiket yang ditugaskan kepadanya
        // Ambil tiket yang ditugaskan ke agent ini
        $ticket = Ticket::where('assigned_to', $this->agent->id)->first();

        if (!$ticket) {
            // Buat tiket yang ditugaskan ke agent
            $ticket = $this->createTicketAssignedTo($this->agent);
        }

        $response = $this->actingAs($this->agent)
            ->get(route('agent.tickets.show', $ticket));

        $response->assertStatus(200);
        $response->assertSee($ticket->ticket_number);
    }

    public function test_super_admin_can_view_any_ticket_detail(): void
    {
        $this->assertNotNull($this->superAdmin);

        $ticket = Ticket::first();
        $this->assertNotNull($ticket);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('agent.tickets.show', $ticket));

        $response->assertStatus(200);
        $response->assertSee($ticket->ticket_number);
    }

    public function test_agent_cannot_view_unassigned_ticket(): void
    {
        $this->assertNotNull($this->agent);

        // Pastikan ada tiket yang tidak ditugaskan ke agent ini
        $ticket = Ticket::where('assigned_to', '!=', $this->agent->id)
            ->orWhereNull('assigned_to')
            ->first();

        $this->assertNotNull($ticket);

        // Field agent tidak bisa lihat tiket yang bukan miliknya
        $response = $this->actingAs($this->agent)
            ->get(route('agent.tickets.show', $ticket));

        $response->assertStatus(403);
    }

    // ========================================================================
    // TC-015: Membalas Tiket (Agent)
    // ========================================================================
    public function test_agent_can_reply_to_assigned_ticket(): void
    {
        $this->assertNotNull($this->agent);

        $ticket = Ticket::where('assigned_to', $this->agent->id)->first();
        if (!$ticket) {
            $ticket = $this->createTicketAssignedTo($this->agent);
        }

        $response = $this->actingAs($this->agent)
            ->post(route('agent.tickets.reply', $ticket), [
                'message' => 'Balasan dari agent untuk tiket ini. Kami sedang menangani.',
            ]);

        $response->assertSessionHas('ok', 'Balasan terkirim.');

        $this->assertDatabaseHas('utas_tiket', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->agent->id,
            'body' => 'Balasan dari agent untuk tiket ini. Kami sedang menangani.',
        ]);
    }

    public function test_agent_reply_fails_without_message(): void
    {
        $this->assertNotNull($this->agent);

        $ticket = Ticket::where('assigned_to', $this->agent->id)->first();
        if (!$ticket) {
            $ticket = $this->createTicketAssignedTo($this->agent);
        }

        $response = $this->actingAs($this->agent)
            ->post(route('agent.tickets.reply', $ticket), []);

        $response->assertSessionHasErrors('message');
    }

    // ========================================================================
    // TC-016: Menambahkan Catatan Internal (Agent)
    // ========================================================================
    public function test_agent_can_add_note_to_ticket(): void
    {
        $this->assertNotNull($this->agent);

        $ticket = Ticket::where('assigned_to', $this->agent->id)->first();
        if (!$ticket) {
            $ticket = $this->createTicketAssignedTo($this->agent);
        }

        $response = $this->actingAs($this->agent)
            ->post(route('agent.tickets.note', $ticket), [
                'message' => 'Catatan internal: perlu investigasi lebih lanjut.',
            ]);

        $response->assertSessionHas('ok');
    }

    // ========================================================================
    // TC-017: Mengubah Status Tiket
    // ========================================================================
    public function test_agent_can_change_ticket_status(): void
    {
        $this->assertNotNull($this->agent);

        $ticket = Ticket::where('assigned_to', $this->agent->id)->first();
        if (!$ticket) {
            $ticket = $this->createTicketAssignedTo($this->agent);
        }

        $inProgressStatus = Status::where('slug', 'in_progress')->first();
        $this->assertNotNull($inProgressStatus);

        $response = $this->actingAs($this->agent)
            ->post(route('agent.tickets.status', $ticket), [
                'status_id' => $inProgressStatus->id,
            ]);

        $response->assertSessionHas('ok', 'Status diperbarui.');
        $this->assertEquals($inProgressStatus->id, $ticket->fresh()->status_id);
    }

    public function test_agent_can_complete_ticket(): void
    {
        $this->assertNotNull($this->agent);

        $ticket = Ticket::where('assigned_to', $this->agent->id)->first();
        if (!$ticket) {
            $ticket = $this->createTicketAssignedTo($this->agent);
        }

        $response = $this->actingAs($this->agent)
            ->post(route('agent.tickets.complete', $ticket));

        $response->assertSessionHas('ok', 'Tiket berhasil diselesaikan. Pelapor telah diberitahu melalui email.');

        // Verifikasi status berubah menjadi closed
        $closedStatus = Status::where('slug', 'closed')->first();
        $this->assertNotNull($closedStatus);
        $this->assertEquals($closedStatus->id, $ticket->fresh()->status_id);
        $this->assertNotNull($ticket->fresh()->closed_at);
    }

    public function test_agent_can_return_ticket_to_admin(): void
    {
        $this->assertNotNull($this->agent);

        $ticket = Ticket::where('assigned_to', $this->agent->id)->first();
        if (!$ticket) {
            $ticket = $this->createTicketAssignedTo($this->agent);
        }

        $response = $this->actingAs($this->agent)
            ->post(route('agent.tickets.return', $ticket));

        $response->assertSessionHas('ok', 'Tiket dikembalikan ke Super Admin untuk ditugaskan ulang.');

        // Verifikasi ticket di-unassign
        $this->assertNull($ticket->fresh()->assigned_to);
    }

    // ========================================================================
    // TC-015b: Menugaskan Tiket (Super Admin / Admin only)
    // ========================================================================
    public function test_super_admin_can_assign_ticket(): void
    {
        $this->assertNotNull($this->superAdmin);
        $this->assertNotNull($this->agent);

        $ticket = Ticket::whereNull('assigned_to')->first();
        if (!$ticket) {
            $ticket = $this->createTicketAssignedTo($this->agent);
            // Unassign it first
            $ticket->update(['assigned_to' => null, 'acknowledged_at' => null]);
        }

        $response = $this->actingAs($this->superAdmin)
            ->post(route('agent.tickets.assign', $ticket), [
                'agent_id' => $this->agent->id,
            ]);

        $response->assertSessionHas('ok');
    }

    // ========================================================================
    // TC-019: Validasi Agent Permission
    // ========================================================================
    public function test_agent_without_assign_permission_cannot_assign(): void
    {
        $this->assertNotNull($this->agent);

        $ticket = Ticket::first();
        $this->assertNotNull($ticket);

        // Agent tidak punya permission tickets.assign
        $response = $this->actingAs($this->agent)
            ->post(route('agent.tickets.assign', $ticket), [
                'agent_id' => $this->agent->id,
            ]);

        $response->assertStatus(403);
    }

    // ========================================================================
    // Helper: Create ticket assigned to a specific agent
    // ========================================================================
    private function createTicketAssignedTo(User $agent): Ticket
    {
        $openStatus = Status::where('slug', 'open')->first();
        $assignedStatus = Status::where('slug', 'assigned')->first()
            ?? Status::where('slug', 'in_progress')->first()
            ?? Status::where('slug', 'open')->first();

        return Ticket::factory()->create([
            'assigned_to' => $agent->id,
            'assigned_at' => now(),
            'status_id' => $assignedStatus->id,
        ]);
    }
}
