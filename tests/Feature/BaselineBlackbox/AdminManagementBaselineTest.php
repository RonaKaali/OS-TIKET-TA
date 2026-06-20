<?php

namespace Tests\Feature\BaselineBlackbox;

use App\Models\User;
use App\Models\Department;
use App\Models\HelpTopic;
use App\Models\Priority;
use App\Models\Organization;
use App\Models\SlaPlan;
use App\Models\Status;
use App\Models\Team;
use App\Models\CannedResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementBaselineTest extends TestCase
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
    // TC-021: Akses Admin Panel
    // ========================================================================
    public function test_admin_panel_is_accessible_by_super_admin(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.index'));

        $response->assertStatus(200);
    }

    public function test_admin_panel_is_blocked_for_agent(): void
    {
        $this->assertNotNull($this->agent);

        $response = $this->actingAs($this->agent)
            ->get(route('admin.index'));

        $response->assertStatus(403);
    }

    public function test_admin_panel_is_blocked_for_regular_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.index'));

        $response->assertStatus(403);
    }

    // ========================================================================
    // TC-022: Manajemen Users (CRUD)
    // ========================================================================
    public function test_admin_can_list_users(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_create_user_page(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_user(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'New Test User',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'User',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('pengguna', [
            'email' => 'newuser@example.com',
            'name' => 'New Test User',
        ]);
    }

    public function test_admin_can_edit_user(): void
    {
        $this->assertNotNull($this->superAdmin);

        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.users.edit', $user));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_user(): void
    {
        $this->assertNotNull($this->superAdmin);

        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated Name',
                'email' => $user->email,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('pengguna', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $this->assertNotNull($this->superAdmin);

        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('pengguna', ['id' => $user->id]);
    }

    // ========================================================================
    // TC-023: Manajemen Departemen (CRUD)
    // ========================================================================
    public function test_admin_can_list_departments(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.departments.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_department(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.departments.store'), [
                'name' => 'Test Department',
                'email' => 'testdept@example.com',
                'is_public' => true,
            ]);

        $response->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseHas('departemen', [
            'name' => 'Test Department',
        ]);
    }

    public function test_admin_can_update_department(): void
    {
        $this->assertNotNull($this->superAdmin);

        $dept = Department::first();
        $this->assertNotNull($dept);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.departments.update', $dept), [
                'name' => 'Updated Department',
                'email' => $dept->email,
                'is_public' => $dept->is_public,
            ]);

        $response->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseHas('departemen', [
            'id' => $dept->id,
            'name' => 'Updated Department',
        ]);
    }

    public function test_admin_can_delete_department(): void
    {
        $this->assertNotNull($this->superAdmin);

        $dept = Department::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept));

        $response->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseMissing('departemen', ['id' => $dept->id]);
    }

    // ========================================================================
    // TC-024: Manajemen Help Topics (CRUD)
    // ========================================================================
    public function test_admin_can_list_help_topics(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.help-topics.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_help_topic(): void
    {
        $this->assertNotNull($this->superAdmin);

        $dept = Department::first();
        $this->assertNotNull($dept);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.help-topics.store'), [
                'name' => 'Test Help Topic',
                'department_id' => $dept->id,
            ]);

        $response->assertRedirect(route('admin.help-topics.index'));
        $this->assertDatabaseHas('topik_bantuan', [
            'name' => 'Test Help Topic',
        ]);
    }

    // ========================================================================
    // TC-025: Manajemen Prioritas (CRUD)
    // ========================================================================
    public function test_admin_can_list_priorities(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.priorities.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_priority(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.priorities.store'), [
                'name' => 'Test Priority',
                'weight' => 10,
            ]);

        $response->assertRedirect(route('admin.priorities.index'));
        $this->assertDatabaseHas('prioritas', [
            'name' => 'Test Priority',
        ]);
    }

    // ========================================================================
    // TC-026: Manajemen SLA (CRUD)
    // ========================================================================
    public function test_admin_can_list_sla_plans(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.sla.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_sla_plan(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.sla.store'), [
                'name' => 'Test SLA (1 Jam)',
                'grace_hours' => 1,
            ]);

        $response->assertRedirect(route('admin.sla.index'));
        $this->assertDatabaseHas('sla_plans', [
            'name' => 'Test SLA (1 Jam)',
        ]);
    }

    // ========================================================================
    // TC-027: Manajemen Status (CRUD)
    // ========================================================================
    public function test_admin_can_list_statuses(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.statuses.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_status(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.statuses.store'), [
                'name' => 'Test Status',
                'slug' => 'test_status',
                'is_closed' => false,
            ]);

        $response->assertRedirect(route('admin.statuses.index'));
        $this->assertDatabaseHas('status', [
            'slug' => 'test_status',
        ]);
    }

    // ========================================================================
    // TC-028: Manajemen Organisasi (CRUD)
    // ========================================================================
    public function test_admin_can_list_organizations(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.organizations.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_organization(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.organizations.store'), [
                'name' => 'Test Organization',
            ]);

        $response->assertRedirect(route('admin.organizations.index'));
        $this->assertDatabaseHas('organisasi', [
            'name' => 'Test Organization',
        ]);
    }

    // ========================================================================
    // TC-029: Manajemen Tim (CRUD)
    // ========================================================================
    public function test_admin_can_list_teams(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.teams.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_team(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.teams.store'), [
                'name' => 'Test Team',
            ]);

        $response->assertRedirect(route('admin.teams.index'));
        $this->assertDatabaseHas('tim', [
            'name' => 'Test Team',
        ]);
    }

    // ========================================================================
    // Additional: Manajemen Canned Responses (CRUD)
    // ========================================================================
    public function test_admin_can_list_canned_responses(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.canned.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_canned_response(): void
    {
        $this->assertNotNull($this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.canned.store'), [
                'title' => 'Test Canned Response',
                'body' => 'This is a test canned response body.',
            ]);

        $response->assertRedirect(route('admin.canned.index'));
        $this->assertDatabaseHas('canned_responses', [
            'title' => 'Test Canned Response',
        ]);
    }
}
