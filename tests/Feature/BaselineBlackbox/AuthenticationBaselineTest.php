<?php

namespace Tests\Feature\BaselineBlackbox;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationBaselineTest extends TestCase
{
    use RefreshDatabase;
    protected bool $seed = true;

    // ========================================================================
    // TC-001: Registrasi Pengguna Baru
    // ========================================================================
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Register');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Verifikasi data di database
        $this->assertDatabaseHas('pengguna', [
            'email' => 'testuser@example.com',
            'name' => 'Test User',
        ]);
    }

    public function test_registration_fails_with_existing_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_registration_fails_with_mismatched_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different456',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('password');
    }

    // ========================================================================
    // TC-002: Login dengan Kredensial Valid
    // ========================================================================
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Log in');
    }

    public function test_users_can_login_with_valid_credentials(): void
    {
        $user = User::where('email', 'agent@csirt.kalselprov.go.id')->first();
        $this->assertNotNull($user);

        $response = $this->post('/login', [
            'email' => 'agent@csirt.kalselprov.go.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_super_admin_can_login(): void
    {
        $user = User::where('email', 'admin@csirt.kalselprov.go.id')->first();
        $this->assertNotNull($user);

        $response = $this->post('/login', [
            'email' => 'admin@csirt.kalselprov.go.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    // ========================================================================
    // TC-003: Login dengan Kredensial Invalid
    // ========================================================================
    public function test_users_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    public function test_users_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    // ========================================================================
    // TC-004: Logout
    // ========================================================================
    public function test_users_can_logout(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    // ========================================================================
    // TC-005: Akses Halaman Terproteksi Tanpa Login
    // ========================================================================
    public function test_agent_page_redirects_to_login_when_unauthenticated(): void
    {
        $response = $this->get('/agent');
        $response->assertRedirect('/login');
    }

    public function test_admin_page_redirects_to_login_when_unauthenticated(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_agent_page_requires_permission(): void
    {
        // User biasa (tanpa role admin.panel) tidak bisa akses /agent
        $user = User::factory()->create([
            'email' => 'regular-user@example.com',
        ]);

        $response = $this->actingAs($user)->get('/agent');
        $response->assertStatus(403);
    }

    // ========================================================================
    // Forgot & Reset Password (TC-014 to TC-019 equivalent)
    // ========================================================================
    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_forgot_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'forgot-test@example.com',
        ]);

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);
            $response->assertStatus(200);
            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('login'));

            // Verify password was updated
            $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));

            return true;
        });
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors();
    }

    // ========================================================================
    // Email Verification (TC-020 to TC-023 equivalent)
    // ========================================================================
    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');
        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_notification_can_be_resent(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');
    }

    // ========================================================================
    // Password Update from Profile
    // ========================================================================
    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }
}
