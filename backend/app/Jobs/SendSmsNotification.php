<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\Sms\IPPanelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Notification $notification,
        protected string $phoneNumber,
        protected string $notificationType,
        protected array $params = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(IPPanelService $smsService): void
    {
        if (! $smsService->isConfigured()) {
            Log::warning('SendSmsNotification: IPPanel service is not configured, skipping SMS');
            $this->markDeliveryStatus('skipped', 'Service not configured');
            return;
        }

        try {
            $result = $smsService->sendTaskNotification(
                $this->phoneNumber,
                $this->notificationType,
                $this->params
            );

            if ($result === null) {
                $this->markDeliveryStatus('skipped', 'No pattern configured for this notification type');
                return;
            }

            $this->markDeliveryStatus('sent', null, $result['message_outbox_ids'] ?? []);

        } catch (\Exception $e) {
            Log::error('SendSmsNotification: Failed to send SMS', [
                'notification_id' => $this->notification->id,
                'phone' => $this->phoneNumber,
                'error' => $e->getMessage(),
            ]);

            $this->markDeliveryStatus('failed', $e->getMessage());

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Update the delivery status for SMS channel on the notification.
     */
    protected function markDeliveryStatus(string $status, ?string $error = null, array $messageIds = []): void
    {
        $deliveryStatus = $this->notification->delivery_status ?? [];

        $deliveryStatus['sms'] = [
            'status' => $status,
            'sent_at' => now()->toIso8601String(),
            'error' => $error,
            'message_ids' => $messageIds,
            'attempt' => $this->attempts(),
        ];

        $this->notification->update(['delivery_status' => $deliveryStatus]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendSmsNotification: Job failed after all retries', [
            'notification_id' => $this->notification->id,
            'phone' => $this->phoneNumber,
            'error' => $exception->getMessage(),
        ]);

        $this->markDeliveryStatus('failed', 'Max retries exceeded: ' . $exception->getMessage());
    }
}
