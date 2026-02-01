# IshYar API Specification

> **API Version**: 1.0.0  
> **Base URL**: `https://api.ishyar.com/v1`  
> **Standard**: RFC 9457 (Problem Details for HTTP APIs)

---

## Authentication

### JWT Token Flow

```
Authorization: Bearer <access_token>
```

| Token Type | Lifetime | Storage |
|------------|----------|---------|
| Access Token | 15 minutes | Memory only |
| Refresh Token | 7 days | HTTP-only cookie |

---

## Standard Headers

### Request Headers

| Header | Required | Description |
|--------|----------|-------------|
| `Authorization` | Yes* | Bearer token for authenticated routes |
| `Accept` | Yes | `application/json` |
| `Content-Type` | Yes** | `application/json` for POST/PUT/PATCH |
| `Accept-Language` | No | `en` or `fa` (default: `en`) |
| `X-Timezone` | No | IANA timezone (default: `UTC`) |
| `X-Request-ID` | No | UUID for request tracing |

### Response Headers

| Header | Description |
|--------|-------------|
| `X-Request-ID` | Request tracing ID |
| `X-RateLimit-Limit` | Rate limit ceiling |
| `X-RateLimit-Remaining` | Remaining requests |
| `X-RateLimit-Reset` | Rate limit reset time (Unix timestamp) |

---

## Response Format

### Success Response

```json
{
  "data": { ... },
  "meta": {
    "message": "Resource created successfully",
    "timestamp": "2026-02-01T12:00:00Z"
  }
}
```

### Collection Response

```json
{
  "data": [ ... ],
  "links": {
    "first": "https://api.ishyar.com/v1/tasks?page=1",
    "last": "https://api.ishyar.com/v1/tasks?page=10",
    "prev": null,
    "next": "https://api.ishyar.com/v1/tasks?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 20,
    "to": 20,
    "total": 200,
    "path": "https://api.ishyar.com/v1/tasks"
  }
}
```

### Error Response (RFC 9457)

```json
{
  "type": "https://ishyar.com/errors/validation-error",
  "title": "Validation Error",
  "status": 422,
  "detail": "The given data was invalid.",
  "instance": "/v1/tasks",
  "errors": {
    "title": ["The title field is required."],
    "assignees": ["At least one assignee is required."]
  }
}
```

---

## API Endpoints

### Authentication

#### POST /auth/login
Login with credentials.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "remember": true
}
```

**Response (200):**
```json
{
  "data": {
    "access_token": "eyJ...",
    "token_type": "bearer",
    "expires_in": 900,
    "user": {
      "id": "uuid",
      "name": "John Doe",
      "email": "user@example.com",
      "avatar": "https://...",
      "locale": "en",
      "timezone": "Asia/Tehran",
      "organization": {
        "id": "uuid",
        "name": "Acme Corp"
      },
      "permissions": ["tasks.view", "tasks.create", ...]
    }
  }
}
```

#### POST /auth/register
Register new account.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "password_confirmation": "SecurePassword123!",
  "organization_name": "Acme Corp",
  "locale": "en"
}
```

#### POST /auth/refresh
Refresh access token using HTTP-only cookie.

**Response (200):**
```json
{
  "data": {
    "access_token": "eyJ...",
    "token_type": "bearer",
    "expires_in": 900
  }
}
```

#### POST /auth/logout
Invalidate tokens.

#### POST /auth/forgot-password
Request password reset.

**Request:**
```json
{
  "email": "user@example.com"
}
```

#### POST /auth/reset-password
Reset password with token.

**Request:**
```json
{
  "email": "user@example.com",
  "token": "reset-token",
  "password": "NewPassword123!",
  "password_confirmation": "NewPassword123!"
}
```

---

### Users

#### GET /users
List users in organization.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `page` | int | Page number |
| `per_page` | int | Items per page (max: 100) |
| `search` | string | Search by name/email |
| `filter[department_id]` | uuid | Filter by department |
| `filter[position_id]` | uuid | Filter by position |
| `filter[status]` | string | active, inactive |
| `sort` | string | name, -name, created_at, -created_at |
| `include` | string | department,position,roles |

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "John Doe",
      "email": "john@example.com",
      "avatar": "https://...",
      "phone": "+989121234567",
      "status": "active",
      "department": {
        "id": "uuid",
        "name": "Engineering"
      },
      "position": {
        "id": "uuid",
        "name": "Senior Developer"
      },
      "created_at": "2026-01-15T10:00:00Z"
    }
  ],
  "meta": { ... }
}
```

#### GET /users/{id}
Get user details.

#### POST /users
Create new user.

**Request:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "TempPassword123!",
  "department_id": "uuid",
  "position_id": "uuid",
  "phone": "+989121234567",
  "roles": ["employee"]
}
```

#### PUT /users/{id}
Update user.

#### DELETE /users/{id}
Delete user (soft delete).

#### GET /users/me
Get current user profile.

#### PUT /users/me
Update current user profile.

---

### Tasks

#### GET /tasks
List tasks.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `filter[status]` | string | not_started, in_progress, pending_review, completed |
| `filter[priority]` | string | low, medium, high, critical |
| `filter[assignee]` | uuid | Filter by assignee |
| `filter[creator]` | uuid | Filter by creator |
| `filter[department]` | uuid | Filter by department |
| `filter[label]` | uuid | Filter by label |
| `filter[due_from]` | date | Due date from |
| `filter[due_to]` | date | Due date to |
| `filter[overdue]` | bool | Only overdue tasks |
| `filter[my_tasks]` | bool | Only assigned to me |
| `search` | string | Search title/description |
| `sort` | string | due_date, -priority, created_at |
| `include` | string | assignees,labels,creator,subtasks |

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "title": "Implement login feature",
      "description": "Create login page with JWT auth",
      "status": "in_progress",
      "priority": "high",
      "progress": 60,
      "due_date": "2026-02-15",
      "due_date_jalali": "1404/11/26",
      "estimated_hours": 8,
      "actual_hours": 5,
      "is_overdue": false,
      "creator": {
        "id": "uuid",
        "name": "John Doe",
        "avatar": null
      },
      "assignees": [
        {
          "id": "uuid",
          "name": "Jane Doe",
          "avatar": null
        }
      ],
      "labels": [
        {
          "id": "uuid",
          "name": "Feature",
          "color": "#22c55e"
        }
      ],
      "subtasks_count": 3,
      "subtasks_completed": 2,
      "comments_count": 5,
      "attachments_count": 2,
      "created_at": "2026-01-20T10:00:00Z",
      "updated_at": "2026-02-01T15:30:00Z"
    }
  ],
  "meta": { ... }
}
```

#### POST /tasks
Create task.

**Request:**
```json
{
  "title": "New Task",
  "description": "Task description with **markdown** support",
  "priority": "high",
  "status": "not_started",
  "due_date": "2026-02-20",
  "estimated_hours": 10,
  "department_id": "uuid",
  "assignees": ["uuid1", "uuid2"],
  "labels": ["uuid1"],
  "parent_id": null
}
```

#### GET /tasks/{id}
Get task details with all relations.

#### PUT /tasks/{id}
Update task.

#### DELETE /tasks/{id}
Delete task.

#### PATCH /tasks/{id}/status
Update task status.

**Request:**
```json
{
  "status": "completed",
  "completion_note": "All requirements met"
}
```

#### PATCH /tasks/{id}/progress
Update task progress.

**Request:**
```json
{
  "progress": 80
}
```

#### POST /tasks/{id}/assign
Assign users to task.

**Request:**
```json
{
  "user_ids": ["uuid1", "uuid2"],
  "mode": "append"  // append | replace
}
```

#### POST /tasks/{id}/comments
Add comment to task.

**Request:**
```json
{
  "body": "Comment with **markdown**",
  "mentions": ["uuid1"]
}
```

#### GET /tasks/{id}/comments
List task comments.

#### POST /tasks/{id}/attachments
Upload attachment.

**Content-Type:** `multipart/form-data`

| Field | Type | Description |
|-------|------|-------------|
| `file` | file | File to upload (max 10MB) |
| `description` | string | Optional description |

#### GET /tasks/{id}/time-entries
List time entries for task.

#### POST /tasks/{id}/time-entries
Add time entry.

**Request:**
```json
{
  "description": "Worked on feature",
  "hours": 2.5,
  "date": "2026-02-01"
}
```

---

### Departments

#### GET /departments
List departments (with hierarchy).

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Engineering",
      "description": "Software development team",
      "parent_id": null,
      "head": {
        "id": "uuid",
        "name": "John Smith",
        "avatar": null
      },
      "members_count": 15,
      "children": [
        {
          "id": "uuid",
          "name": "Frontend Team",
          "parent_id": "uuid",
          "head": null,
          "members_count": 5,
          "children": []
        }
      ]
    }
  ]
}
```

#### POST /departments
Create department.

#### PUT /departments/{id}
Update department.

#### DELETE /departments/{id}
Delete department.

#### GET /departments/{id}/members
List department members.

---

### Notifications

#### GET /notifications
List notifications.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `filter[read]` | bool | Filter by read status |
| `filter[type]` | string | Notification type |

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "type": "task_assigned",
      "title": "New task assigned",
      "body": "You have been assigned to 'Implement login'",
      "data": {
        "task_id": "uuid",
        "task_title": "Implement login"
      },
      "read_at": null,
      "created_at": "2026-02-01T10:00:00Z"
    }
  ],
  "meta": {
    "unread_count": 5
  }
}
```

#### POST /notifications/mark-read
Mark notifications as read.

**Request:**
```json
{
  "ids": ["uuid1", "uuid2"],
  "all": false
}
```

#### DELETE /notifications/{id}
Delete notification.

---

### CRM - Contacts

#### GET /crm/contacts
List contacts.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `filter[company_id]` | uuid | Filter by company |
| `filter[owner_id]` | uuid | Filter by owner |
| `filter[source]` | string | Lead source |
| `search` | string | Search name/email/phone |
| `sort` | string | name, -created_at |
| `include` | string | company,owner,deals,activities |

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@company.com",
      "phone": "+989121234567",
      "mobile": "+989121234567",
      "job_title": "CEO",
      "company": {
        "id": "uuid",
        "name": "Acme Corp"
      },
      "owner": {
        "id": "uuid",
        "name": "Sales Rep"
      },
      "source": "website",
      "tags": ["decision-maker", "enterprise"],
      "custom_fields": {
        "linkedin": "https://linkedin.com/in/johndoe"
      },
      "deals_count": 2,
      "deals_value": 50000.00,
      "last_activity_at": "2026-02-01T10:00:00Z",
      "created_at": "2026-01-15T10:00:00Z"
    }
  ],
  "meta": { ... }
}
```

#### POST /crm/contacts
Create contact.

#### GET /crm/contacts/{id}
Get contact details.

#### PUT /crm/contacts/{id}
Update contact.

#### DELETE /crm/contacts/{id}
Delete contact.

#### GET /crm/contacts/{id}/activities
List contact activities.

#### POST /crm/contacts/{id}/activities
Log activity for contact.

---

### CRM - Companies

#### GET /crm/companies
List companies.

#### POST /crm/companies
Create company.

**Request:**
```json
{
  "name": "Acme Corporation",
  "domain": "acme.com",
  "industry": "Technology",
  "size": "50-200",
  "website": "https://acme.com",
  "phone": "+989121234567",
  "address": {
    "street": "123 Main St",
    "city": "Tehran",
    "state": "Tehran",
    "country": "IR",
    "postal_code": "12345"
  },
  "owner_id": "uuid"
}
```

#### GET /crm/companies/{id}
Get company details with contacts and deals.

#### PUT /crm/companies/{id}
Update company.

#### DELETE /crm/companies/{id}
Delete company.

---

### CRM - Deals

#### GET /crm/deals
List deals.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `filter[pipeline_id]` | uuid | Filter by pipeline |
| `filter[stage_id]` | uuid | Filter by stage |
| `filter[status]` | string | open, won, lost |
| `filter[owner_id]` | uuid | Filter by owner |
| `filter[close_from]` | date | Expected close from |
| `filter[close_to]` | date | Expected close to |
| `filter[value_min]` | number | Minimum value |
| `filter[value_max]` | number | Maximum value |

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Enterprise License Deal",
      "value": 50000.00,
      "currency": "USD",
      "status": "open",
      "probability": 75,
      "expected_close_date": "2026-03-15",
      "pipeline": {
        "id": "uuid",
        "name": "Sales Pipeline"
      },
      "stage": {
        "id": "uuid",
        "name": "Proposal",
        "position": 3
      },
      "company": {
        "id": "uuid",
        "name": "Acme Corp"
      },
      "contacts": [
        {
          "id": "uuid",
          "name": "John Doe",
          "role": "primary"
        }
      ],
      "owner": {
        "id": "uuid",
        "name": "Sales Rep"
      },
      "days_in_stage": 5,
      "total_days": 30,
      "activities_count": 10,
      "next_activity": {
        "id": "uuid",
        "type": "call",
        "due_at": "2026-02-02T14:00:00Z"
      },
      "created_at": "2026-01-01T10:00:00Z"
    }
  ],
  "meta": { ... }
}
```

#### POST /crm/deals
Create deal.

**Request:**
```json
{
  "name": "New Deal",
  "value": 25000.00,
  "currency": "USD",
  "pipeline_id": "uuid",
  "stage_id": "uuid",
  "company_id": "uuid",
  "contacts": [
    {
      "contact_id": "uuid",
      "role": "primary"
    }
  ],
  "owner_id": "uuid",
  "expected_close_date": "2026-04-01",
  "probability": 50
}
```

#### GET /crm/deals/{id}
Get deal details.

#### PUT /crm/deals/{id}
Update deal.

#### PATCH /crm/deals/{id}/stage
Move deal to different stage.

**Request:**
```json
{
  "stage_id": "uuid",
  "reason": "Moved to proposal after successful demo"
}
```

#### PATCH /crm/deals/{id}/status
Change deal status (won/lost).

**Request:**
```json
{
  "status": "won",
  "close_date": "2026-02-01",
  "won_reason": "Competitive pricing",
  "actual_value": 52000.00
}
```

#### GET /crm/deals/{id}/activities
List deal activities.

#### POST /crm/deals/{id}/activities
Log activity for deal.

---

### CRM - Pipelines

#### GET /crm/pipelines
List pipelines.

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Sales Pipeline",
      "is_default": true,
      "stages": [
        {
          "id": "uuid",
          "name": "Lead",
          "position": 1,
          "probability": 10,
          "color": "#3b82f6",
          "deals_count": 15,
          "deals_value": 150000.00
        },
        {
          "id": "uuid",
          "name": "Qualified",
          "position": 2,
          "probability": 25,
          "color": "#22c55e",
          "deals_count": 10,
          "deals_value": 250000.00
        }
      ],
      "total_deals": 50,
      "total_value": 1000000.00
    }
  ]
}
```

#### POST /crm/pipelines
Create pipeline.

#### PUT /crm/pipelines/{id}
Update pipeline.

#### POST /crm/pipelines/{id}/stages
Add stage to pipeline.

#### PUT /crm/pipelines/{id}/stages/{stageId}
Update stage.

#### DELETE /crm/pipelines/{id}/stages/{stageId}
Delete stage.

#### PUT /crm/pipelines/{id}/stages/reorder
Reorder stages.

**Request:**
```json
{
  "order": ["uuid1", "uuid2", "uuid3"]
}
```

---

### CRM - Activities

#### GET /crm/activities
List all activities.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `filter[type]` | string | call, email, meeting, note, task |
| `filter[date_from]` | date | Activity date from |
| `filter[date_to]` | date | Activity date to |
| `filter[completed]` | bool | Completion status |
| `filter[entity_type]` | string | contact, company, deal |
| `filter[entity_id]` | uuid | Related entity ID |

**Response (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "type": "call",
      "title": "Follow-up call",
      "description": "Discussed pricing options",
      "due_at": "2026-02-01T14:00:00Z",
      "completed_at": null,
      "duration_minutes": 30,
      "outcome": null,
      "entity_type": "deal",
      "entity_id": "uuid",
      "entity": {
        "id": "uuid",
        "name": "Enterprise Deal"
      },
      "owner": {
        "id": "uuid",
        "name": "Sales Rep"
      },
      "created_at": "2026-01-30T10:00:00Z"
    }
  ],
  "meta": { ... }
}
```

#### POST /crm/activities
Create activity.

**Request:**
```json
{
  "type": "meeting",
  "title": "Product demo",
  "description": "Demonstrate enterprise features",
  "due_at": "2026-02-05T10:00:00Z",
  "duration_minutes": 60,
  "entity_type": "deal",
  "entity_id": "uuid",
  "attendees": ["uuid1", "uuid2"]
}
```

#### PUT /crm/activities/{id}
Update activity.

#### PATCH /crm/activities/{id}/complete
Mark activity as completed.

**Request:**
```json
{
  "outcome": "successful",
  "notes": "Client interested in proposal",
  "next_steps": "Send proposal by EOD"
}
```

#### DELETE /crm/activities/{id}
Delete activity.

---

### Analytics

#### GET /analytics/dashboard
Get dashboard analytics.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `period` | string | today, week, month, quarter, year, custom |
| `from` | date | Start date (required for custom) |
| `to` | date | End date (required for custom) |

**Response (200):**
```json
{
  "data": {
    "tasks": {
      "total": 150,
      "completed": 85,
      "overdue": 12,
      "completion_rate": 56.67,
      "by_status": {
        "not_started": 30,
        "in_progress": 45,
        "pending_review": 20,
        "completed": 85
      },
      "by_priority": {
        "critical": 5,
        "high": 25,
        "medium": 80,
        "low": 40
      },
      "trend": [
        {"date": "2026-01-25", "created": 10, "completed": 8},
        {"date": "2026-01-26", "created": 12, "completed": 15}
      ]
    },
    "team": {
      "active_users": 25,
      "productivity_score": 78.5,
      "top_performers": [
        {"id": "uuid", "name": "John Doe", "completed": 25}
      ]
    },
    "crm": {
      "deals_won": 5,
      "deals_lost": 2,
      "revenue_won": 125000.00,
      "pipeline_value": 500000.00,
      "win_rate": 71.43,
      "avg_deal_size": 25000.00,
      "avg_sales_cycle": 45
    }
  }
}
```

#### GET /analytics/tasks
Get task analytics.

#### GET /analytics/team
Get team performance analytics.

#### GET /analytics/crm
Get CRM analytics.

#### GET /analytics/reports
List saved reports.

#### POST /analytics/reports
Create saved report.

---

### Settings

#### GET /settings/organization
Get organization settings.

#### PUT /settings/organization
Update organization settings.

#### GET /settings/preferences
Get user preferences.

#### PUT /settings/preferences
Update user preferences.

**Request:**
```json
{
  "locale": "fa",
  "timezone": "Asia/Tehran",
  "date_format": "jalali",
  "notifications": {
    "email": {
      "task_assigned": true,
      "task_completed": true,
      "mentions": true,
      "daily_digest": false
    },
    "push": {
      "task_assigned": true,
      "mentions": true
    }
  },
  "theme": "dark",
  "sidebar_collapsed": false
}
```

---

## WebSocket Events

### Connection

```javascript
// Laravel Reverb connection
const socket = new WebSocket('wss://ws.ishyar.com/app/key');

// Or using Laravel Echo
const Echo = new Echo({
    broadcaster: 'reverb',
    key: 'app-key',
    wsHost: 'ws.ishyar.com',
    wsPort: 443,
    wssPort: 443,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
});
```

### Private Channels

| Channel | Description |
|---------|-------------|
| `private-user.{userId}` | User-specific notifications |
| `private-organization.{orgId}` | Organization-wide updates |
| `private-department.{deptId}` | Department updates |
| `private-task.{taskId}` | Task-specific updates |

### Events

| Event | Channel | Payload |
|-------|---------|---------|
| `task.created` | user, department | `{ task: TaskData }` |
| `task.updated` | user, task | `{ task: TaskData }` |
| `task.deleted` | user, task | `{ task_id: string }` |
| `task.assigned` | user | `{ task: TaskData, assignees: UserData[] }` |
| `task.commented` | task | `{ comment: CommentData }` |
| `notification.new` | user | `{ notification: NotificationData }` |
| `deal.updated` | user | `{ deal: DealData }` |
| `deal.stage_changed` | user | `{ deal: DealData, from_stage: string, to_stage: string }` |

---

## Rate Limiting

| Endpoint | Limit | Window |
|----------|-------|--------|
| `POST /auth/login` | 5 | 1 minute |
| `POST /auth/forgot-password` | 3 | 1 minute |
| General API | 60 | 1 minute |
| File uploads | 10 | 1 minute |

---

## Error Codes

| Code | Type | Description |
|------|------|-------------|
| 400 | `bad-request` | Invalid request format |
| 401 | `unauthorized` | Authentication required |
| 403 | `forbidden` | Insufficient permissions |
| 404 | `not-found` | Resource not found |
| 409 | `conflict` | Resource conflict |
| 422 | `validation-error` | Validation failed |
| 429 | `rate-limit` | Too many requests |
| 500 | `server-error` | Internal server error |

---

*This API specification follows REST best practices and RFC 9457 for error responses.*
