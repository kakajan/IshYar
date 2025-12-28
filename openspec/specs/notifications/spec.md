# Notification Service Specification

## Purpose

The Notification Service provides multi-channel delivery of real-time notifications including Web Push, SMS, Telegram, Email, and in-app notifications with intelligent batching and user preference respect.

## Requirements

### Requirement: Multi-Channel Delivery
The system SHALL deliver notifications through multiple channels based on user preferences and notification priority.

#### Scenario: Sending notification to user
- **WHEN** a notification is triggered
- **THEN** check user's channel preferences
- **AND** evaluate notification priority for channel selection
- **AND** queue delivery to enabled channels
- **AND** track delivery status per channel

#### Scenario: Channel fallback
- **WHEN** primary channel delivery fails
- **THEN** attempt delivery via fallback channel
- **AND** respect maximum retry attempts (3)
- **AND** log failure reason for analytics

#### Scenario: Critical notifications
- **WHEN** notification priority is critical
- **THEN** bypass quiet hours restrictions
- **AND** attempt all enabled channels simultaneously
- **AND** require acknowledgment for certain categories

### Requirement: Web Push Notifications
The system SHALL support browser push notifications via VAPID/FCM.

#### Scenario: Subscribing to push notifications
- **WHEN** user enables push notifications
- **THEN** request browser permission
- **AND** generate and store VAPID subscription
- **AND** associate subscription with user session
- **AND** confirm subscription with test notification

#### Scenario: Sending web push
- **WHEN** push notification is triggered
- **THEN** retrieve user's push subscriptions
- **AND** send to all active browsers/devices
- **AND** include notification icon and badge
- **AND** support click action URL

#### Scenario: Managing subscriptions
- **WHEN** push subscription expires or fails
- **THEN** remove invalid subscription
- **AND** optionally prompt user to re-subscribe
- **AND** update subscription health metrics

### Requirement: SMS Gateway Integration
The system SHALL support SMS delivery via configurable gateway providers.

#### Scenario: Sending SMS notification
- **WHEN** SMS notification is triggered
- **THEN** validate user has verified phone number
- **AND** format message within 160 character limit
- **AND** send via configured SMS gateway
- **AND** track delivery status and receipt

#### Scenario: SMS gateway configuration
- **WHEN** SMS gateway is configured
- **THEN** support multiple providers (Twilio, SMS.to, local gateways)
- **AND** provide fallback gateway option
- **AND** monitor gateway health and costs

#### Scenario: SMS rate limiting
- **WHEN** SMS notifications are sent
- **THEN** enforce daily limit per user (configurable)
- **AND** batch non-urgent SMS into daily digest option
- **AND** track SMS costs per department

### Requirement: Telegram Bot Integration
The system SHALL integrate with Telegram for notification delivery.

#### Scenario: Linking Telegram account
- **WHEN** user links Telegram account
- **THEN** generate unique linking code
- **AND** user sends code to IshYar bot
- **AND** verify and associate Telegram user ID
- **AND** send confirmation message via Telegram

#### Scenario: Sending Telegram notification
- **WHEN** Telegram notification is triggered
- **THEN** format message with Markdown
- **AND** include action buttons when applicable
- **AND** send via Telegram Bot API
- **AND** handle rate limits (30 messages/second)

#### Scenario: Telegram bot commands
- **WHEN** user sends command to bot
- **THEN** support /status - view pending tasks
- **AND** support /today - today's tasks summary
- **AND** support /quick [task] - create quick task
- **AND** support /mute [duration] - temporary mute

### Requirement: Email Notifications
The system SHALL send transactional and digest emails.

#### Scenario: Sending transactional email
- **WHEN** email notification is triggered
- **THEN** render email template with data
- **AND** send via configured provider (SMTP/Mailgun/Postmark)
- **AND** track open and click events
- **AND** handle bounces and complaints

#### Scenario: Daily digest email
- **WHEN** daily digest is scheduled
- **THEN** aggregate pending tasks, approvals, announcements
- **AND** send personalized summary email
- **AND** allow configurable digest time
- **AND** skip if no items to report

### Requirement: In-App Real-Time Notifications
The system SHALL provide real-time in-app notification delivery via WebSocket.

#### Scenario: Receiving in-app notification
- **WHEN** notification is triggered for online user
- **THEN** broadcast via WebSocket immediately
- **AND** display toast notification
- **AND** update notification badge count
- **AND** play subtle notification sound (if enabled)

#### Scenario: Notification center
- **WHEN** user opens notification center
- **THEN** display all notifications with pagination
- **AND** group by date and category
- **AND** show read/unread status
- **AND** support mark as read (individual/all)

#### Scenario: Offline notification sync
- **WHEN** offline user comes online
- **THEN** sync missed notifications
- **AND** display count of new notifications
- **AND** mark delivery timestamp

### Requirement: Notification Batching
The system SHALL intelligently batch non-critical notifications.

#### Scenario: Batching notifications
- **WHEN** multiple low-priority notifications queue
- **THEN** batch within 5-minute window
- **AND** aggregate into single notification
- **AND** provide "View all" action in batched notification

#### Scenario: Debouncing notifications
- **WHEN** repeated notifications for same event occur
- **THEN** debounce within 1-minute window
- **AND** show latest state in single notification
- **AND** indicate update count

### Requirement: User Preference Respect
The system SHALL respect all user notification preferences.

#### Scenario: Quiet hours enforcement
- **WHEN** notification triggers during quiet hours
- **THEN** queue for delivery after quiet hours end
- **AND** except for critical priority
- **AND** show queued status in notification log

#### Scenario: Category preferences
- **WHEN** notification belongs to disabled category
- **THEN** skip delivery for that channel
- **AND** still log notification for audit

## API Endpoints

### GET /api/v1/notifications
List user's notifications.

**Query Parameters:**
- `filter[read]`: true/false
- `filter[category]`: task, approval, announcement, system
- `filter[after]`: Date filter
- `page[size]`: Items per page
- `page[number]`: Page number

**Response (200):**
```json
{
  "data": [
    {
      "type": "notifications",
      "id": "uuid",
      "attributes": {
        "title": "Task Approved",
        "body": "Your task 'Weekly Report' has been approved by John Smith",
        "category": "approval",
        "priority": "medium",
        "is_read": false,
        "action_url": "/tasks/uuid",
        "metadata": {
          "task_id": "uuid",
          "approver_id": "uuid"
        },
        "created_at": "2025-01-30T14:30:00Z",
        "read_at": null
      }
    }
  ],
  "meta": {
    "unread_count": 5,
    "counts_by_category": {
      "task": 3,
      "approval": 2,
      "announcement": 0,
      "system": 0
    }
  }
}
```

### POST /api/v1/notifications/{id}/read
Mark notification as read.

### POST /api/v1/notifications/read-all
Mark all notifications as read.

### DELETE /api/v1/notifications/{id}
Delete notification.

### GET /api/v1/notifications/preferences
Get user notification preferences.

### PATCH /api/v1/notifications/preferences
Update notification preferences.

**Request:**
```json
{
  "channels": {
    "email": true,
    "push": true,
    "sms": false,
    "telegram": true
  },
  "quiet_hours": {
    "enabled": true,
    "start": "22:00",
    "end": "08:00",
    "timezone": "Asia/Ashgabat"
  },
  "categories": {
    "task_assigned": { "email": true, "push": true },
    "task_approved": { "email": false, "push": true },
    "daily_digest": { "email": true, "push": false }
  }
}
```

### GET /api/v1/notifications/push/vapid-key
Get VAPID public key for push subscription.

### POST /api/v1/notifications/push/subscribe
Register push subscription.

**Request:**
```json
{
  "subscription": {
    "endpoint": "https://fcm.googleapis.com/...",
    "keys": {
      "p256dh": "...",
      "auth": "..."
    }
  },
  "device_info": {
    "browser": "Chrome",
    "os": "Windows",
    "device_name": "Work Laptop"
  }
}
```

### DELETE /api/v1/notifications/push/subscribe
Unregister push subscription.

### POST /api/v1/notifications/telegram/link
Generate Telegram linking code.

**Response (200):**
```json
{
  "data": {
    "linking_code": "ABCD1234",
    "bot_username": "ishyar_bot",
    "expires_at": "2025-01-30T15:00:00Z",
    "link_url": "https://t.me/ishyar_bot?start=ABCD1234"
  }
}
```

### DELETE /api/v1/notifications/telegram/link
Unlink Telegram account.

### POST /api/v1/notifications/send (Admin)
Send notification to users (admin only).

**Request:**
```json
{
  "title": "System Maintenance",
  "body": "Scheduled maintenance on Saturday 10:00-12:00",
  "category": "announcement",
  "priority": "high",
  "recipients": {
    "type": "department",
    "id": "uuid"
  },
  "channels": ["push", "email"],
  "scheduled_at": "2025-02-01T08:00:00Z"
}
```

## Data Schema

### Notification
```typescript
interface Notification {
  id: UUID;
  user_id: UUID;
  
  title: string;
  body: string;
  category: 'task' | 'approval' | 'announcement' | 'system' | 'reminder';
  priority: 'low' | 'medium' | 'high' | 'critical';
  
  action_url?: string;
  action_data?: Record<string, any>;
  metadata?: Record<string, any>;
  
  is_read: boolean;
  read_at?: Date;
  
  delivery_status: DeliveryStatus;
  
  scheduled_at?: Date;
  created_at: Date;
  expires_at?: Date;
}

interface DeliveryStatus {
  email?: ChannelStatus;
  push?: ChannelStatus;
  sms?: ChannelStatus;
  telegram?: ChannelStatus;
  in_app?: ChannelStatus;
}

interface ChannelStatus {
  status: 'pending' | 'sent' | 'delivered' | 'failed' | 'skipped';
  sent_at?: Date;
  delivered_at?: Date;
  error?: string;
  attempt_count: number;
}

interface NotificationPreferences {
  user_id: UUID;
  
  channels: {
    email: boolean;
    push: boolean;
    sms: boolean;
    telegram: boolean;
  };
  
  quiet_hours: {
    enabled: boolean;
    start: string; // HH:mm
    end: string;
    timezone: string;
    days: number[]; // 0-6
  };
  
  categories: Record<string, ChannelPreferences>;
  
  digest: {
    enabled: boolean;
    frequency: 'daily' | 'weekly';
    time: string; // HH:mm
  };
}

interface PushSubscription {
  id: UUID;
  user_id: UUID;
  endpoint: string;
  p256dh: string;
  auth: string;
  device_name?: string;
  browser?: string;
  os?: string;
  is_active: boolean;
  last_used_at: Date;
  created_at: Date;
}

interface TelegramLink {
  id: UUID;
  user_id: UUID;
  telegram_user_id: string;
  telegram_username?: string;
  telegram_chat_id: string;
  is_active: boolean;
  linked_at: Date;
}
```

## Telegram Bot Commands

| Command | Description |
|---------|-------------|
| `/start` | Initialize bot, link account |
| `/status` | View pending tasks and approvals |
| `/today` | Today's task summary |
| `/week` | Week's task overview |
| `/quick <task>` | Create quick situational task |
| `/complete <id>` | Mark task as complete |
| `/mute <duration>` | Mute notifications (1h, 4h, today, forever) |
| `/unmute` | Resume notifications |
| `/settings` | View/modify notification settings |
| `/help` | Show available commands |
