# Automation & Integrations Specification

## Purpose

The Automation & Integrations module provides seamless connectivity with external workflow automation platforms (n8n), messaging systems (Slack, Teams), calendars, and custom webhooks to extend IshYar functionality.

## Requirements

### Requirement: n8n Integration
The system SHALL provide deep integration with n8n for workflow automation.

#### Scenario: Configuring n8n connection
- **WHEN** admin configures n8n integration
- **THEN** generate API credentials for n8n
- **AND** provide IshYar node package for n8n
- **AND** verify connection with test webhook

#### Scenario: Triggering n8n workflow from IshYar event
- **WHEN** configured event occurs in IshYar
- **THEN** send webhook to n8n with event payload
- **AND** include all relevant entity data
- **AND** support filtering by event conditions
- **AND** log webhook delivery status

#### Scenario: n8n actions in IshYar
- **WHEN** n8n workflow calls IshYar API
- **THEN** support all CRUD operations on entities
- **AND** validate n8n API credentials
- **AND** respect role-based permissions
- **AND** trigger appropriate notifications

#### Scenario: Pre-built n8n templates
- **WHEN** user accesses integration templates
- **THEN** provide templates for common workflows
- **AND** include task escalation automation
- **AND** include external system sync workflows
- **AND** support one-click template import to n8n

### Requirement: Slack Integration
The system SHALL integrate with Slack for notifications and actions.

#### Scenario: Installing Slack app
- **WHEN** admin installs IshYar Slack app
- **THEN** complete OAuth authorization flow
- **AND** map Slack users to IshYar users (by email)
- **AND** configure default notification channel
- **AND** enable slash commands

#### Scenario: Slack notifications
- **WHEN** notification is sent to Slack channel
- **THEN** format as rich Slack message (Block Kit)
- **AND** include action buttons (approve, view, snooze)
- **AND** thread related notifications together
- **AND** respect user DND settings

#### Scenario: Slack slash commands
- **WHEN** user invokes /ishyar command
- **THEN** support /ishyar task [title] - create task
- **AND** support /ishyar status - view pending tasks
- **AND** support /ishyar approve [id] - approve task
- **AND** show interactive modals for complex inputs

#### Scenario: Slack actions (buttons/menus)
- **WHEN** user clicks action button in Slack
- **THEN** verify user authorization
- **AND** execute action in IshYar
- **AND** update Slack message to reflect new state
- **AND** send confirmation ephemeral message

### Requirement: Microsoft Teams Integration
The system SHALL integrate with Microsoft Teams for enterprise environments.

#### Scenario: Installing Teams app
- **WHEN** admin installs IshYar Teams app
- **THEN** complete Azure AD authorization
- **AND** register bot with Teams
- **AND** configure tabs for dashboards
- **AND** enable message extensions

#### Scenario: Teams notifications
- **WHEN** notification is sent to Teams
- **THEN** format as Adaptive Card
- **AND** include action buttons
- **AND** support threaded conversations
- **AND** respect Teams channel preferences

#### Scenario: Teams tabs
- **WHEN** IshYar tab is added to channel
- **THEN** display embedded dashboard view
- **AND** support SSO authentication
- **AND** filter data by team context
- **AND** enable full functionality within Teams

### Requirement: Calendar Integration
The system SHALL sync with Google Calendar and Outlook.

#### Scenario: Connecting calendar
- **WHEN** user connects calendar account
- **THEN** complete OAuth authorization
- **AND** sync task deadlines to calendar
- **AND** respect calendar permissions

#### Scenario: Creating calendar events from tasks
- **WHEN** task with deadline is created
- **THEN** optionally create calendar block
- **AND** include task details in description
- **AND** add IshYar link to event
- **AND** update event when task changes

#### Scenario: Availability checking
- **WHEN** assigning task to user
- **THEN** optionally check calendar availability
- **AND** suggest alternative deadlines if busy
- **AND** respect working hours from calendar

### Requirement: Webhook System
The system SHALL provide flexible webhook support for custom integrations.

#### Scenario: Creating outgoing webhook
- **WHEN** admin creates outgoing webhook
- **THEN** specify target URL and events
- **AND** configure authentication (none/basic/bearer/hmac)
- **AND** set retry policy
- **AND** generate webhook secret for signing

#### Scenario: Webhook event delivery
- **WHEN** subscribed event occurs
- **THEN** construct JSON payload with event data
- **AND** sign payload with HMAC-SHA256
- **AND** deliver with configurable timeout
- **AND** retry on failure (exponential backoff)
- **AND** log delivery attempts

#### Scenario: Incoming webhooks
- **WHEN** external system calls IshYar webhook
- **THEN** validate webhook signature/token
- **AND** parse payload according to webhook type
- **AND** trigger appropriate internal action
- **AND** return standardized response

#### Scenario: Webhook management
- **WHEN** admin manages webhooks
- **THEN** view delivery history and status
- **AND** test webhook with sample payload
- **AND** pause/resume webhook delivery
- **AND** view error logs and retry failures

### Requirement: Zapier Integration
The system SHALL provide Zapier app for broad integration support.

#### Scenario: Zapier triggers
- **WHEN** user creates Zap with IshYar trigger
- **THEN** support triggers: new task, task completed, task approved, new user
- **AND** provide webhook-based instant triggers
- **AND** include full entity data in trigger payload

#### Scenario: Zapier actions
- **WHEN** user creates Zap with IshYar action
- **THEN** support actions: create task, update task, create user
- **AND** validate required fields
- **AND** return created entity data

### Requirement: API for Custom Integrations
The system SHALL expose comprehensive API for custom integrations.

#### Scenario: API authentication
- **WHEN** external system authenticates to API
- **THEN** support API key authentication
- **AND** support OAuth2 client credentials flow
- **AND** rate limit based on plan/key
- **AND** log API usage for billing/analytics

#### Scenario: API versioning
- **WHEN** API version changes
- **THEN** maintain backward compatibility
- **AND** deprecate old versions with notice
- **AND** document breaking changes
- **AND** provide migration guides

## API Endpoints

### GET /api/v1/integrations
List all configured integrations.

**Response (200):**
```json
{
  "data": [
    {
      "type": "integrations",
      "id": "uuid",
      "attributes": {
        "provider": "slack",
        "name": "Slack Workspace",
        "status": "active",
        "connected_at": "2025-01-15T10:00:00Z",
        "last_sync_at": "2025-01-30T14:00:00Z",
        "config": {
          "team_id": "T123456",
          "team_name": "TechCorp",
          "default_channel": "#ishyar-notifications"
        }
      }
    }
  ]
}
```

### POST /api/v1/integrations/{provider}/connect
Initiate OAuth flow for integration.

### DELETE /api/v1/integrations/{provider}
Disconnect integration.

### GET /api/v1/integrations/{provider}/settings
Get integration settings.

### PATCH /api/v1/integrations/{provider}/settings
Update integration settings.

### POST /api/v1/integrations/n8n/credentials
Generate n8n API credentials.

**Response (200):**
```json
{
  "data": {
    "api_key": "n8n_xxx...",
    "api_secret": "xxx...",
    "webhook_url": "https://api.ishyar.io/webhooks/n8n/xxx",
    "api_base_url": "https://api.ishyar.io/v1"
  }
}
```

### GET /api/v1/integrations/n8n/templates
List n8n workflow templates.

### GET /api/v1/webhooks
List webhooks.

**Query Parameters:**
- `filter[direction]`: outgoing, incoming
- `filter[status]`: active, paused, error

### POST /api/v1/webhooks
Create webhook.

**Request:**
```json
{
  "data": {
    "type": "webhooks",
    "attributes": {
      "name": "Task Created Webhook",
      "direction": "outgoing",
      "url": "https://example.com/webhooks/ishyar",
      "events": ["task.created", "task.completed"],
      "authentication": {
        "type": "hmac",
        "secret": "webhook_secret_xxx"
      },
      "retry_policy": {
        "max_attempts": 3,
        "backoff": "exponential"
      },
      "filters": {
        "department_id": "uuid"
      }
    }
  }
}
```

### GET /api/v1/webhooks/{id}
Get webhook details.

### PATCH /api/v1/webhooks/{id}
Update webhook.

### DELETE /api/v1/webhooks/{id}
Delete webhook.

### POST /api/v1/webhooks/{id}/test
Test webhook with sample payload.

### GET /api/v1/webhooks/{id}/deliveries
Get webhook delivery history.

### POST /api/v1/webhooks/{id}/deliveries/{deliveryId}/retry
Retry failed delivery.

### POST /api/v1/webhooks/incoming/{token}
Receive incoming webhook.

### GET /api/v1/api-keys
List API keys.

### POST /api/v1/api-keys
Create API key.

### DELETE /api/v1/api-keys/{id}
Revoke API key.

## Webhook Events

### Available Events
| Event | Description | Payload |
|-------|-------------|---------|
| `task.created` | New task created | Task object |
| `task.updated` | Task modified | Task object + changes |
| `task.completed` | Task marked complete | Task object |
| `task.approved` | Task approved | Task object + approver |
| `task.rejected` | Task rejected | Task object + reason |
| `task.overdue` | Task became overdue | Task object |
| `user.created` | New user added | User object |
| `user.updated` | User modified | User object + changes |
| `approval.pending` | New approval needed | Task + assignee |
| `department.created` | Department added | Department object |
| `department.updated` | Department modified | Department + changes |

### Webhook Payload Structure
```json
{
  "event": "task.created",
  "timestamp": "2025-01-30T14:30:00Z",
  "webhook_id": "uuid",
  "delivery_id": "uuid",
  "data": {
    "task": { "..." },
    "related": {
      "assignee": { "..." },
      "department": { "..." }
    }
  },
  "meta": {
    "triggered_by": "user_uuid",
    "source": "api"
  }
}
```

## Data Schema

### Integration
```typescript
interface Integration {
  id: UUID;
  organization_id: UUID;
  provider: 'slack' | 'teams' | 'google_calendar' | 'outlook' | 'n8n' | 'zapier';
  name: string;
  status: 'active' | 'inactive' | 'error';
  
  credentials: EncryptedCredentials;
  config: ProviderConfig;
  
  connected_by: UUID;
  connected_at: Date;
  last_sync_at?: Date;
  error_message?: string;
  
  created_at: Date;
  updated_at: Date;
}

interface Webhook {
  id: UUID;
  organization_id: UUID;
  
  name: string;
  direction: 'outgoing' | 'incoming';
  url?: string; // for outgoing
  token?: string; // for incoming
  
  events: string[];
  
  authentication: {
    type: 'none' | 'basic' | 'bearer' | 'hmac';
    username?: string;
    password?: EncryptedString;
    token?: EncryptedString;
    secret?: EncryptedString;
  };
  
  retry_policy: {
    max_attempts: number;
    backoff: 'linear' | 'exponential';
    initial_delay: number; // seconds
  };
  
  filters?: Record<string, any>;
  headers?: Record<string, string>;
  
  status: 'active' | 'paused' | 'error';
  error_count: number;
  last_error?: string;
  last_triggered_at?: Date;
  
  created_by: UUID;
  created_at: Date;
  updated_at: Date;
}

interface WebhookDelivery {
  id: UUID;
  webhook_id: UUID;
  
  event: string;
  payload: Record<string, any>;
  
  status: 'pending' | 'success' | 'failed';
  response_status?: number;
  response_body?: string;
  error_message?: string;
  
  attempt_count: number;
  next_retry_at?: Date;
  
  duration_ms?: number;
  
  created_at: Date;
  completed_at?: Date;
}

interface ApiKey {
  id: UUID;
  organization_id: UUID;
  
  name: string;
  key_prefix: string; // first 8 chars for identification
  key_hash: string; // bcrypt hash
  
  scopes: string[]; // 'read:tasks', 'write:tasks', etc.
  
  rate_limit: {
    requests_per_minute: number;
    requests_per_day: number;
  };
  
  last_used_at?: Date;
  usage_count: number;
  
  expires_at?: Date;
  
  created_by: UUID;
  created_at: Date;
  revoked_at?: Date;
}
```

## n8n Node Actions

### Triggers (Webhooks)
- New Task Created
- Task Status Changed
- Task Approved/Rejected
- Task Overdue
- New User Created
- Approval Required

### Actions (API Calls)
- Create Task
- Update Task
- Get Task Details
- List Tasks (with filters)
- Approve Task
- Reject Task
- Create User
- Update User
- Get Department
- Send Notification
