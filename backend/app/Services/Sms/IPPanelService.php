<?php

namespace App\Services\Sms;

use App\Contracts\SmsChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IPPanelService implements SmsChannelInterface
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected ?string $fromNumber;
    protected array $patterns;

    public function __construct()
    {
        $this->baseUrl = config('services.ippanel.base_url', 'https://edge.ippanel.com/v1');
        $this->apiKey = config('services.ippanel.api_key');
        $this->fromNumber = config('services.ippanel.from_number');
        $this->patterns = config('services.ippanel.patterns', []);
    }

    /**
     * Check if the service is properly configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey) && ! empty($this->fromNumber);
    }

    /**
     * Send a pattern SMS using IPPanel API.
     *
     * @param string $recipient Phone number in E.164 format
     * @param string $patternCode Pattern code from IPPanel dashboard
     * @param array $params Key-value pairs for pattern placeholders
     * @return array Response with message_outbox_ids
     * @throws \Exception On failure
     */
    public function sendPattern(string $recipient, string $patternCode, array $params): array
    {
        if (! $this->isConfigured()) {
            throw new \Exception('IPPanel SMS service is not properly configured');
        }

        $payload = [
            'sending_type' => 'pattern',
            'from_number' => $this->fromNumber,
            'code' => $patternCode,
            'recipients' => [$this->normalizePhoneNumber($recipient)],
            'params' => $params,
        ];

        Log::info('IPPanel: Sending pattern SMS', [
            'recipient' => $recipient,
            'pattern' => $patternCode,
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->apiKey,
        ])->post("{$this->baseUrl}/api/send", $payload);

        $data = $response->json();

        if (! $response->successful() || ! ($data['meta']['status'] ?? false)) {
            $errorMessage = $data['meta']['message'] ?? 'Unknown error from IPPanel';
            Log::error('IPPanel: Failed to send SMS', [
                'recipient' => $recipient,
                'error' => $errorMessage,
                'response' => $data,
            ]);
            throw new \Exception("IPPanel SMS failed: {$errorMessage}");
        }

        Log::info('IPPanel: SMS sent successfully', [
            'recipient' => $recipient,
            'message_ids' => $data['data']['message_outbox_ids'] ?? [],
        ]);

        return $data['data'] ?? [];
    }

    /**
     * Send an OTP code to a phone number.
     *
     * @param string $recipient Phone number in E.164 format
     * @param string $code The OTP code
     * @return array Response with message_outbox_ids
     */
    public function sendOtp(string $recipient, string $code): array
    {
        $otpPattern = $this->patterns['otp'] ?? null;

        if (empty($otpPattern)) {
            throw new \Exception('OTP pattern code is not configured');
        }

        return $this->sendPattern($recipient, $otpPattern, [
            'code' => $code,
        ]);
    }

    /**
     * Send task notification via SMS.
     *
     * @param string $recipient Phone number
     * @param string $type Notification type (task_assigned, task_completed, etc.)
     * @param array $params Pattern parameters
     * @return array|null Response if pattern exists, null otherwise
     */
    public function sendTaskNotification(string $recipient, string $type, array $params): ?array
    {
        $patternCode = $this->patterns[$type] ?? null;

        if (empty($patternCode)) {
            Log::warning("IPPanel: No pattern configured for type '{$type}'");
            return null;
        }

        return $this->sendPattern($recipient, $patternCode, $params);
    }

    /**
     * Get the pattern code for a notification type.
     */
    public function getPatternCode(string $type): ?string
    {
        return $this->patterns[$type] ?? null;
    }

    /**
     * Normalize phone number to E.164 format for Iran.
     */
    protected function normalizePhoneNumber(string $phone): string
    {
        // Remove any whitespace
        $phone = preg_replace('/\s+/', '', $phone);

        // If already in E.164 format, return as is
        if (str_starts_with($phone, '+98')) {
            return $phone;
        }

        // If starts with 98, add +
        if (str_starts_with($phone, '98')) {
            return '+' . $phone;
        }

        // If starts with 0, replace with +98
        if (str_starts_with($phone, '0')) {
            return '+98' . substr($phone, 1);
        }

        // If starts with 9 (mobile without prefix), add +98
        if (str_starts_with($phone, '9') && strlen($phone) === 10) {
            return '+98' . $phone;
        }

        // Return as is if format is unknown
        return $phone;
    }

    /**
     * Generate a random OTP code.
     *
     * @param int $length Length of the OTP code
     * @return string The generated OTP
     */
    public static function generateOtpCode(int $length = 6): string
    {
        return str_pad((string) random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
