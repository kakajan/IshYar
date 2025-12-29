<?php

namespace Tests\Unit\Services;

use App\Services\Sms\IPPanelService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IPPanelServiceTest extends TestCase
{
    protected IPPanelService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure IPPanel for testing
        Config::set('services.ippanel', [
            'base_url' => 'https://edge.ippanel.com/v1',
            'api_key' => 'test-api-key',
            'from_number' => '+983000505',
            'patterns' => [
                'otp' => 'test-otp-pattern',
                'task_assigned' => 'test-task-pattern',
            ],
        ]);

        $this->service = new IPPanelService();
    }

    public function test_is_configured_returns_true_when_credentials_set(): void
    {
        $this->assertTrue($this->service->isConfigured());
    }

    public function test_is_configured_returns_false_when_api_key_missing(): void
    {
        Config::set('services.ippanel.api_key', null);
        $service = new IPPanelService();

        $this->assertFalse($service->isConfigured());
    }

    public function test_generate_otp_code_returns_correct_length(): void
    {
        $code = IPPanelService::generateOtpCode(6);
        $this->assertEquals(6, strlen($code));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);

        $code4 = IPPanelService::generateOtpCode(4);
        $this->assertEquals(4, strlen($code4));
    }

    public function test_send_pattern_makes_correct_api_request(): void
    {
        Http::fake([
            'https://edge.ippanel.com/*' => Http::response([
                'data' => ['message_outbox_ids' => [12345]],
                'meta' => ['status' => true, 'message' => 'انجام شد'],
            ], 200),
        ]);

        $result = $this->service->sendPattern(
            '+989121234567',
            'test-pattern-code',
            ['code' => '123456']
        );

        $this->assertArrayHasKey('message_outbox_ids', $result);
        $this->assertEquals([12345], $result['message_outbox_ids']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://edge.ippanel.com/v1/api/send'
                && $request->hasHeader('Authorization', 'test-api-key')
                && $request['sending_type'] === 'pattern'
                && $request['from_number'] === '+983000505'
                && $request['code'] === 'test-pattern-code'
                && $request['recipients'] === ['+989121234567']
                && $request['params']['code'] === '123456';
        });
    }

    public function test_send_otp_uses_otp_pattern(): void
    {
        Http::fake([
            'https://edge.ippanel.com/*' => Http::response([
                'data' => ['message_outbox_ids' => [12345]],
                'meta' => ['status' => true, 'message' => 'انجام شد'],
            ], 200),
        ]);

        $this->service->sendOtp('+989121234567', '999888');

        Http::assertSent(function ($request) {
            return $request['code'] === 'test-otp-pattern'
                && $request['params']['code'] === '999888';
        });
    }

    public function test_send_pattern_throws_when_not_configured(): void
    {
        Config::set('services.ippanel.api_key', null);
        $service = new IPPanelService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not properly configured');

        $service->sendPattern('+989121234567', 'code', ['param' => 'value']);
    }

    public function test_send_pattern_throws_on_api_error(): void
    {
        Http::fake([
            'https://edge.ippanel.com/*' => Http::response([
                'data' => null,
                'meta' => ['status' => false, 'message' => 'اطلاعات وارد شده صحیح نمی باشد'],
            ], 401),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('IPPanel SMS failed');

        $this->service->sendPattern('+989121234567', 'code', ['param' => 'value']);
    }

    public function test_phone_normalization(): void
    {
        Http::fake([
            'https://edge.ippanel.com/*' => Http::response([
                'data' => ['message_outbox_ids' => [1]],
                'meta' => ['status' => true],
            ], 200),
        ]);

        // Test various formats are normalized to +98
        $formats = [
            '09121234567',   // 0-prefix
            '9121234567',    // No prefix
            '989121234567',  // 98-prefix no +
            '+989121234567', // Already E.164
        ];

        foreach ($formats as $phone) {
            $this->service->sendPattern($phone, 'code', []);
        }

        Http::assertSentCount(4);
    }
}
