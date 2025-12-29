<?php

namespace Tests\Feature;

use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\Sms\IPPanelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PhoneVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure IPPanel for testing
        config([
            'services.ippanel' => [
                'base_url' => 'https://edge.ippanel.com/v1',
                'api_key' => 'test-api-key',
                'from_number' => '+983000505',
                'patterns' => [
                    'otp' => 'test-otp-pattern',
                ],
            ],
        ]);

        // Mock successful SMS responses
        Http::fake([
            'https://edge.ippanel.com/*' => Http::response([
                'data' => ['message_outbox_ids' => [12345]],
                'meta' => ['status' => true, 'message' => 'انجام شد'],
            ], 200),
        ]);
    }

    public function test_send_otp_creates_verification_record(): void
    {
        $response = $this->postJson('/api/v1/auth/phone/send-otp', [
            'phone' => '09121234567',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Verification code sent successfully.',
            ]);

        $this->assertDatabaseHas('phone_verifications', [
            'phone' => '+989121234567',
        ]);
    }

    public function test_send_otp_validates_phone_format(): void
    {
        $response = $this->postJson('/api/v1/auth/phone/send-otp', [
            'phone' => 'invalid-phone',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_verify_otp_marks_verification_as_verified(): void
    {
        // Create a pending verification
        $verification = PhoneVerification::create([
            'phone' => '+989121234567',
            'code' => '123456',
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->postJson('/api/v1/auth/phone/verify', [
            'phone' => '+989121234567',
            'code' => '123456',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Phone number verified successfully.',
            ]);

        $this->assertNotNull($verification->fresh()->verified_at);
    }

    public function test_verify_otp_fails_with_wrong_code(): void
    {
        PhoneVerification::create([
            'phone' => '+989121234567',
            'code' => '123456',
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->postJson('/api/v1/auth/phone/verify', [
            'phone' => '+989121234567',
            'code' => '999999',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid verification code.',
            ]);
    }

    public function test_verify_otp_fails_with_expired_code(): void
    {
        PhoneVerification::create([
            'phone' => '+989121234567',
            'code' => '123456',
            'expires_at' => now()->subMinutes(5), // Expired
        ]);

        $response = $this->postJson('/api/v1/auth/phone/verify', [
            'phone' => '+989121234567',
            'code' => '123456',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No pending verification found. Please request a new code.',
            ]);
    }

    public function test_authenticated_user_gets_phone_verified(): void
    {
        $user = User::factory()->create([
            'phone' => null,
            'phone_verified_at' => null,
        ]);

        PhoneVerification::create([
            'user_id' => $user->id,
            'phone' => '+989121234567',
            'code' => '123456',
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/v1/auth/phone/verify', [
                'phone' => '+989121234567',
                'code' => '123456',
            ]);

        $response->assertOk();

        $user->refresh();
        $this->assertEquals('+989121234567', $user->phone);
        $this->assertNotNull($user->phone_verified_at);
    }

    public function test_phone_status_returns_verification_status(): void
    {
        $user = User::factory()->create([
            'phone' => '+989121234567',
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/auth/phone/status');

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'verified' => true,
                ],
            ]);
    }
}
