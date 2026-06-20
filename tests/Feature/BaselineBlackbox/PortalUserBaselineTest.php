<?php

namespace Tests\Feature\BaselineBlackbox;

use App\Models\HelpTopic;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PortalUserBaselineTest extends TestCase
{
    use RefreshDatabase;
    protected bool $seed = true;

    private ?User $user = null;
    private ?HelpTopic $topic = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat atau ambil user biasa (tanpa role admin/agent)
        $this->user = User::factory()->create([
            'name' => 'Portal User',
            'email' => 'portal-user@example.com',
        ]);

        $this->topic = HelpTopic::with('department')->first();
    }

    // ========================================================================
    // TC-009: Membuat Tiket Baru (via Portal)
    // ========================================================================
    public function test_create_ticket_form_is_accessible_when_logged_in(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('portal.ticket.create'));

        $response->assertStatus(200);
        $response->assertSee('help_topic_id');
    }

    public function test_create_ticket_form_redirects_when_not_logged_in(): void
    {
        $response = $this->get(route('portal.ticket.create'));
        $response->assertRedirect('/login');
    }

    public function test_user_can_create_ticket(): void
    {
        $this->assertNotNull($this->topic, 'No help topic found from seeder');

        $org = Organization::first();
        $this->assertNotNull($org);

        $response = $this->actingAs($this->user)
            ->post(route('portal.ticket.store'), [
                'subject' => 'Test Laporan Keamanan',
                'help_topic_id' => $this->topic->id,
                'reporter_organization' => $org->name,
                'message' => 'Ini adalah test laporan untuk menguji fitur pembuatan tiket.',
            ]);

        // Tiket baru disimpan dengan redirect ke halaman show
        $response->assertStatus(200); // store returns view, not redirect
        $response->assertSee('Test Laporan Keamanan');
        $response->assertSee($this->user->email);

        // Verifikasi database
        $this->assertDatabaseHas('tiket', [
            'subject' => 'Test Laporan Keamanan',
            'user_id' => $this->user->id,
            'reporter_email' => $this->user->email,
        ]);
    }

    public function test_create_ticket_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('portal.ticket.store'), []);

        $response->assertSessionHasErrors(['subject', 'help_topic_id', 'reporter_organization', 'message']);
    }

    // ========================================================================
    // TC-010: Melihat Tiket Saya (setelah login)
    // ========================================================================
    public function test_logged_in_user_can_see_their_ticket(): void
    {
        // Buat tiket untuk user ini
        $ticket = Ticket::factory()->create([
            'user_id' => $this->user->id,
            'reporter_email' => $this->user->email,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('portal.ticket.show', $ticket->ticket_number));

        $response->assertStatus(200);
        $response->assertSee($ticket->ticket_number);
        $response->assertSee($ticket->subject);
    }

    public function test_user_cannot_see_others_ticket_without_auth(): void
    {
        $otherUser = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $otherUser->id,
            'reporter_email' => $otherUser->email,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('portal.ticket.show', $ticket->ticket_number));

        // Harusnya redirect karena bukan pemilik
        $response->assertRedirect(route('portal.ticket.status.form'));
    }

    // ========================================================================
    // TC-011: Membalas Tiket (via Portal)
    // ========================================================================
    public function test_user_can_reply_to_their_ticket(): void
    {
        // Buat tiket untuk user ini
        $ticket = Ticket::factory()->create([
            'user_id' => $this->user->id,
            'reporter_email' => $this->user->email,
            'status_id' => \App\Models\Status::where('slug', 'answered')->value('id')
                ?? \App\Models\Status::where('slug', 'open')->value('id'),
        ]);

        // Buat thread awal
        TicketThread::create([
            'ticket_id' => $ticket->id,
            'type' => 'message',
            'user_id' => $this->user->id,
            'body' => 'Laporan awal',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('portal.ticket.reply', $ticket->ticket_number), [
                'message' => 'Balasan dari user untuk tiket ini.',
            ]);

        $response->assertSessionHas('ok', 'Balasan terkirim.');

        // Verifikasi thread tersimpan
        $this->assertDatabaseHas('utas_tiket', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->user->id,
            'body' => 'Balasan dari user untuk tiket ini.',
        ]);
    }

    public function test_user_cannot_reply_to_others_ticket(): void
    {
        $otherUser = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $otherUser->id,
            'reporter_email' => $otherUser->email,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('portal.ticket.reply', $ticket->ticket_number), [
                'message' => 'Mencoba balas tiket orang lain.',
            ]);

        $response->assertStatus(403);
    }

    public function test_reply_fails_without_message(): void
    {
        $ticket = Ticket::factory()->create([
            'user_id' => $this->user->id,
            'reporter_email' => $this->user->email,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('portal.ticket.reply', $ticket->ticket_number), []);

        $response->assertSessionHasErrors('message');
    }
}
