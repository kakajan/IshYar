<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\Sms\IPPanelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class PhoneVerificationController extends Controller
{
    protected IPPanelService $smsService;

    public function __construct(IPPanelService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send OTP to phone number.
     *
     * POST /api/v1/auth/phone/send-otp
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^(\+98|98|0)?9[0-9]{9}$/'],
        ], [
            'phone.regex' => 'Please enter a valid Iranian mobile number.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $this->normalizePhone($request->phone);
        $user = $request->user();

        // Rate limiting: 3 attempts per phone per 10 minutes
        $rateLimitKey = 'phone-otp:' . $phone;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json([
                'message' => 'Too many OTP requests. Please try again later.',
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($rateLimitKey, 600); // 10 minutes

        // Check if SMS service is configured
        if (! $this->smsService->isConfigured()) {
            Log::warning('PhoneVerification: SMS service is not configured');
            return response()->json([
                'message' => 'SMS service is temporarily unavailable. Please try again later.',
            ], 503);
        }

        try {
            // Create verification record
            $verification = PhoneVerification::createFor(
                phone: $phone,
                userId: $user?->id,
                expiryMinutes: 5
            );

            // Send OTP via SMS
            $this->smsService->sendOtp($phone, $verification->code);

            Log::info('PhoneVerification: OTP sent', [
                'phone' => $phone,
                'user_id' => $user?->id,
            ]);

            return response()->json([
                'message' => 'Verification code sent successfully.',
                'data' => [
                    'expires_in' => 300, // 5 minutes in seconds
                    'phone' => $this->maskPhone($phone),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('PhoneVerification: Failed to send OTP', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to send verification code. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify OTP code.
     *
     * POST /api/v1/auth/phone/verify
     */
    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^(\+98|98|0)?9[0-9]{9}$/'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $this->normalizePhone($request->phone);
        $code = $request->code;
        $user = $request->user();

        // Find valid verification
        $verification = PhoneVerification::where('phone', $phone)
            ->valid()
            ->latest()
            ->first();

        if (! $verification) {
            return response()->json([
                'message' => 'No pending verification found. Please request a new code.',
            ], 404);
        }

        // Check attempts
        if ($verification->hasExceededAttempts()) {
            $verification->delete();
            return response()->json([
                'message' => 'Too many failed attempts. Please request a new code.',
            ], 429);
        }

        // Verify code
        if ($verification->code !== $code) {
            $verification->incrementAttempts();
            return response()->json([
                'message' => 'Invalid verification code.',
                'attempts_remaining' => 5 - $verification->attempts,
            ], 400);
        }

        // Mark as verified
        $verification->markAsVerified();

        // Update user's phone if authenticated
        if ($user) {
            $user->update([
                'phone' => $phone,
                'phone_verified_at' => now(),
            ]);

            Log::info('PhoneVerification: Phone verified for user', [
                'user_id' => $user->id,
                'phone' => $phone,
            ]);

            return response()->json([
                'message' => 'Phone number verified successfully.',
                'data' => [
                    'phone' => $phone,
                    'verified_at' => now()->toIso8601String(),
                ],
            ]);
        }

        // If not authenticated, return verification ID for use in registration/login
        return response()->json([
            'message' => 'Phone number verified successfully.',
            'data' => [
                'verification_id' => $verification->id,
                'phone' => $phone,
            ],
        ]);
    }

    /**
     * Check current phone verification status.
     *
     * GET /api/v1/auth/phone/status
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'data' => [
                'phone' => $user->phone ? $this->maskPhone($user->phone) : null,
                'verified' => $user->phone_verified_at !== null,
                'verified_at' => $user->phone_verified_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Normalize phone number to +98 format.
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);

        if (str_starts_with($phone, '+98')) {
            return $phone;
        }

        if (str_starts_with($phone, '98')) {
            return '+' . $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '+98' . substr($phone, 1);
        }

        if (str_starts_with($phone, '9') && strlen($phone) === 10) {
            return '+98' . $phone;
        }

        return $phone;
    }

    /**
     * Mask phone number for display.
     */
    protected function maskPhone(string $phone): string
    {
        // Show first 4 and last 2 digits: +98912****00
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 6) . '****' . substr($phone, -2);
        }
        return $phone;
    }
}
