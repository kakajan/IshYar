# User Management Specification

## Purpose

The User Management module handles employee profiles, personnel data, skill tracking, and user preferences within the IshYar platform.

## Requirements

### Requirement: Employee Profile Management
The system SHALL maintain comprehensive employee profiles with organizational context.

#### Scenario: Creating employee profile
- **WHEN** an admin creates a new employee
- **THEN** generate unique employee ID (format: EMP-YYYY-XXXXX)
- **AND** require essential fields (name, email, department, role)
- **AND** upload profile photo (auto-resize to 256x256, WebP format)
- **AND** associate with organizational hierarchy position

#### Scenario: Updating employee profile
- **WHEN** an employee or manager updates profile data
- **THEN** validate field permissions based on role
- **AND** maintain audit log of all changes
- **AND** trigger notification to HR for sensitive field changes

#### Scenario: Employee onboarding workflow
- **WHEN** a new employee profile is created
- **THEN** send welcome email with credentials
- **AND** assign default role permissions
- **AND** create onboarding task checklist
- **AND** notify direct manager and HR

### Requirement: Skill Matrix Management
The system SHALL track employee skills and competencies with proficiency levels.

#### Scenario: Adding skills to profile
- **WHEN** a skill is added to an employee profile
- **THEN** select from predefined skill taxonomy
- **AND** assign proficiency level (1-5 stars)
- **AND** optionally add certification details
- **AND** record assessment date

#### Scenario: Skill search across organization
- **WHEN** a manager searches for specific skills
- **THEN** return employees matching skill criteria
- **AND** rank by proficiency level
- **AND** filter by department/availability

### Requirement: User Preferences
The system SHALL allow personalization of user experience.

#### Scenario: Notification preferences
- **WHEN** a user configures notification settings
- **THEN** allow per-channel preferences (email, push, SMS, Telegram)
- **AND** support quiet hours configuration
- **AND** allow category-based filtering (tasks, approvals, announcements)

#### Scenario: Display preferences
- **WHEN** a user configures display settings
- **THEN** support theme selection (light/dark/system)
- **AND** language selection (with RTL support)
- **AND** timezone and date format preferences
- **AND** dashboard widget arrangement

### Requirement: Employee Directory
The system SHALL provide searchable organizational directory.

#### Scenario: Searching directory
- **WHEN** a user searches the employee directory
- **THEN** search across name, email, department, title
- **AND** respect visibility settings per organization
- **AND** display org-chart position with visual indicators
- **AND** show availability status (online/away/busy)

#### Scenario: Viewing employee card
- **WHEN** a user views an employee card
- **THEN** display photo, name, title, department
- **AND** show contact information (email, phone, Telegram)
- **AND** display reporting chain (manager, direct reports)
- **AND** show current task workload indicator

### Requirement: Team Management
The system SHALL support team composition and cross-functional groups.

#### Scenario: Creating a team
- **WHEN** a manager creates a team
- **THEN** assign team name and description
- **AND** designate team lead
- **AND** add members from any department
- **AND** set team visibility (public/private)

#### Scenario: Team membership changes
- **WHEN** team membership is modified
- **THEN** notify affected members
- **AND** update task assignment options
- **AND** recalculate team analytics

## API Endpoints

### GET /api/v1/users
List all users with filtering and pagination.

**Query Parameters:**
- `filter[department]`: Filter by department ID
- `filter[role]`: Filter by role
- `filter[status]`: active, inactive, onboarding
- `filter[skills]`: Comma-separated skill IDs
- `include`: department, manager, skills, teams
- `sort`: name, created_at, department
- `page[size]`: Items per page (default: 25)
- `page[number]`: Page number

**Response (200):**
```json
{
  "data": [
    {
      "type": "users",
      "id": "uuid",
      "attributes": {
        "employee_id": "EMP-2025-00001",
        "name": "John Doe",
        "email": "john.doe@company.com",
        "title": "Senior Developer",
        "avatar_url": "https://cdn.ishyar.io/avatars/...",
        "status": "active",
        "timezone": "Asia/Ashgabat",
        "created_at": "2025-01-15T10:00:00Z"
      },
      "relationships": {
        "department": { "data": { "type": "departments", "id": "uuid" } },
        "manager": { "data": { "type": "users", "id": "uuid" } }
      }
    }
  ],
  "meta": {
    "pagination": {
      "total": 150,
      "count": 25,
      "per_page": 25,
      "current_page": 1,
      "total_pages": 6
    }
  }
}
```

### POST /api/v1/users
Create new user/employee.

### GET /api/v1/users/{id}
Get user details with relationships.

### PATCH /api/v1/users/{id}
Update user profile.

### DELETE /api/v1/users/{id}
Soft delete user (archive).

### GET /api/v1/users/{id}/skills
Get user's skill matrix.

### POST /api/v1/users/{id}/skills
Add skill to user profile.

### PATCH /api/v1/users/{id}/skills/{skillId}
Update skill proficiency.

### DELETE /api/v1/users/{id}/skills/{skillId}
Remove skill from profile.

### GET /api/v1/users/{id}/preferences
Get user preferences.

### PATCH /api/v1/users/{id}/preferences
Update user preferences.

### GET /api/v1/users/{id}/teams
Get teams user belongs to.

### GET /api/v1/users/{id}/direct-reports
Get user's direct reports.

### POST /api/v1/users/{id}/avatar
Upload user avatar.

### GET /api/v1/teams
List all teams.

### POST /api/v1/teams
Create new team.

### GET /api/v1/teams/{id}
Get team details.

### PATCH /api/v1/teams/{id}
Update team.

### DELETE /api/v1/teams/{id}
Delete team.

### POST /api/v1/teams/{id}/members
Add member to team.

### DELETE /api/v1/teams/{id}/members/{userId}
Remove member from team.

## Data Schema

### User
```typescript
interface User {
  id: UUID;
  employee_id: string;
  name: string;
  email: string;
  phone?: string;
  telegram_username?: string;
  avatar_url?: string;
  title: string;
  department_id: UUID;
  manager_id?: UUID;
  role_id: UUID;
  status: 'active' | 'inactive' | 'onboarding' | 'offboarding';
  hire_date: Date;
  timezone: string;
  locale: string;
  preferences: UserPreferences;
  created_at: Date;
  updated_at: Date;
  deleted_at?: Date;
}

interface UserPreferences {
  theme: 'light' | 'dark' | 'system';
  notifications: {
    email: boolean;
    push: boolean;
    sms: boolean;
    telegram: boolean;
    quiet_hours: {
      enabled: boolean;
      start: string; // HH:mm
      end: string;
    };
    categories: Record<string, boolean>;
  };
  dashboard_layout: WidgetPosition[];
  date_format: string;
  time_format: '12h' | '24h';
}
```
