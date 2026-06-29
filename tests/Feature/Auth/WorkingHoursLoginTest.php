<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkingHoursLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'zero_trust.working_hours.enabled' => true,
            'zero_trust.working_hours.start' => '08:00',
            'zero_trust.working_hours.end' => '17:00',
            'zero_trust.working_hours.days' => [1, 2, 3, 4, 5],
            'zero_trust.working_hours.timezone' => 'Asia/Makassar',
            'zero_trust.risk_score_threshold_high' => 70,
            'zero_trust.vpn_block_enabled' => false,
            'zero_trust.mfa_enabled' => false,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_login_is_blocked_before_working_hours(): void
    {
        $this->freezeTimeAt('2026-06-29 07:59:00');
        $user = User::factory()->create();

        $response = $this->login($user);

        $this->assertGuest();
        $response->assertRedirect(route('login.work-hours-blocked'));
        $response->assertSessionHas('working_hours_access_time', '07.59');
        $this->assertDatabaseHas('security_events', [
            'user_id' => $user->id,
            'event_type' => 'after_hours_login_blocked',
            'severity' => 'high',
            'risk_score' => 70,
        ]);
    }

    public function test_login_is_allowed_at_start_of_working_hours(): void
    {
        $this->freezeTimeAt('2026-06-29 08:00:00');
        $user = User::factory()->create();

        $response = $this->login($user);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('welcome', absolute: false));
    }

    public function test_login_is_allowed_one_minute_before_end_of_working_hours(): void
    {
        $this->freezeTimeAt('2026-06-29 16:59:00');
        $user = User::factory()->create();

        $this->login($user);

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_is_blocked_at_end_of_working_hours(): void
    {
        $this->freezeTimeAt('2026-06-29 17:00:00');
        $user = User::factory()->create();

        $response = $this->login($user);

        $this->assertGuest();
        $response->assertRedirect(route('login.work-hours-blocked'));
    }

    public function test_login_is_blocked_on_weekend(): void
    {
        $this->freezeTimeAt('2026-06-27 10:00:00');
        $user = User::factory()->create();

        $response = $this->login($user);

        $this->assertGuest();
        $response->assertRedirect(route('login.work-hours-blocked'));
    }

    public function test_user_with_explicit_exception_can_login_after_hours(): void
    {
        $this->freezeTimeAt('2026-06-29 19:00:00');
        $user = User::factory()->create(['allow_after_hours_access' => true]);

        $this->login($user);

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseMissing('security_events', [
            'user_id' => $user->id,
            'event_type' => 'after_hours_login_blocked',
        ]);
    }

    public function test_login_is_allowed_when_working_hours_policy_is_disabled(): void
    {
        config(['zero_trust.working_hours.enabled' => false]);
        $this->freezeTimeAt('2026-06-28 02:00:00');
        $user = User::factory()->create();

        $this->login($user);

        $this->assertAuthenticatedAs($user);
    }

    public function test_working_hours_blocked_page_can_be_rendered(): void
    {
        $this->get(route('login.work-hours-blocked'))
            ->assertOk()
            ->assertSee('Akses Ditolak')
            ->assertSee('Kebijakan Jam Kerja Aktif');
    }

    private function freezeTimeAt(string $dateTime): void
    {
        Carbon::setTestNow(Carbon::parse($dateTime, 'Asia/Makassar'));
    }

    private function login(User $user)
    {
        return $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
    }
}
