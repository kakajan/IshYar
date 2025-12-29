<?php

namespace App\Contracts;

interface SmsChannelInterface
{
    /**
     * Send a pattern SMS.
     *
     * @param string $recipient Phone number in E.164 format (e.g., +989120000000)
     * @param string $patternCode Pattern code from SMS provider
     * @param array $params Key-value pairs for pattern placeholders
     * @return array Response with message_outbox_ids
     * @throws \Exception On failure
     */
    public function sendPattern(string $recipient, string $patternCode, array $params): array;

    /**
     * Send an OTP code to a phone number.
     *
     * @param string $recipient Phone number in E.164 format
     * @param string $code The OTP code
     * @return array Response with message_outbox_ids
     * @throws \Exception On failure
     */
    public function sendOtp(string $recipient, string $code): array;

    /**
     * Check if the service is properly configured.
     */
    public function isConfigured(): bool;
}
