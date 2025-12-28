# Task Engine Specification

## Purpose

The Task Engine is the core productivity module that manages routine (recurring) and situational (ad-hoc) tasks with progress tracking, dependencies, approval workflows, and visual reporting.

## Requirements

### Requirement: Task Type Classification
The system SHALL distinguish between Routine Tasks and Situational Tasks with appropriate behavior for each.

#### Scenario: Creating routine task
- **WHEN** a manager creates a routine task
- **THEN** define recurrence pattern (daily/weekly/monthly/custom)
- **AND** set template title and description
- **AND** specify default assignee(s) or role-based assignment
- **AND** auto-generate task instances based on schedule

#### Scenario: Creating situational task
- **WHEN** a manager creates a situational task
- **THEN** set one-time deadline
- **AND** assign to specific employee(s)
- **AND** define priority level (low/medium/high/critical)
- **AND** optionally link to parent task or project

#### Scenario: Routine task instance generation
- **WHEN** recurrence schedule triggers
- **THEN** create new task instance from template
- **AND** assign to configured assignees
- **AND** set deadline based on recurrence rules
- **AND** send notification to assignees
- **AND** carry over incomplete subtasks (optional)

### Requirement: Task Assignment
The system SHALL support flexible task assignment based on individuals, roles, or job descriptions.

#### Scenario: Assigning to individual
- **WHEN** a task is assigned to specific employee
- **THEN** add to employee's task list
- **AND** send real-time notification
- **AND** update workload metrics

#### Scenario: Role-based assignment
- **WHEN** a task is assigned to a role
- **THEN** assign to all employees with that role
- **AND** OR assign round-robin based on workload
- **AND** track individual vs collective completion

#### Scenario: Job description-based assignment
- **WHEN** a task is linked to job description
- **THEN** auto-assign to employees in matching positions
- **AND** include task in onboarding for new hires in role

### Requirement: Progress Tracking
The system SHALL provide visual progress indicators with granular status updates.

#### Scenario: Updating task progress
- **WHEN** an employee updates task progress
- **THEN** accept percentage (0-100) or visual slider input
- **AND** optionally add progress note
- **AND** update last activity timestamp
- **AND** recalculate parent task/project progress

#### Scenario: Progress visualization
- **WHEN** task progress is displayed
- **THEN** render animated progress ring/bar
- **AND** show completion percentage
- **AND** display time-based progress (days remaining/overdue)
- **AND** use color coding (green/yellow/red) based on status

#### Scenario: Status transitions
- **WHEN** task status changes
- **THEN** validate allowed transitions (not_started → in_progress → pending_review → completed)
- **AND** require comment for certain transitions
- **AND** log status change with timestamp and user
- **AND** trigger appropriate notifications

### Requirement: Task Dependencies
The system SHALL support task dependencies and blocking relationships.

#### Scenario: Creating dependency
- **WHEN** a dependency is defined between tasks
- **THEN** specify relationship type (blocks/blocked_by/relates_to)
- **AND** prevent circular dependencies
- **AND** visually indicate blocked tasks
- **AND** auto-notify when blocker is completed

#### Scenario: Dependency chain resolution
- **WHEN** a blocking task is completed
- **THEN** automatically unblock dependent tasks
- **AND** notify assignees of unblocked tasks
- **AND** update task availability status

### Requirement: Approval Workflow
The system SHALL implement request-verification loop for task completion.

#### Scenario: Submitting for approval
- **WHEN** an employee marks task as complete (100%)
- **THEN** change status to "pending_review"
- **AND** notify assigned approver (manager or task creator)
- **AND** attach any deliverables/evidence
- **AND** start approval timer (SLA tracking)

#### Scenario: Approving task completion
- **WHEN** approver reviews and approves task
- **THEN** change status to "completed"
- **AND** record approval timestamp and approver
- **AND** update employee productivity metrics
- **AND** notify employee of approval
- **AND** trigger any downstream workflows

#### Scenario: Requesting revision
- **WHEN** approver rejects task completion
- **THEN** change status back to "in_progress"
- **AND** require rejection reason
- **AND** optionally set revision deadline
- **AND** notify employee with feedback
- **AND** increment revision counter

### Requirement: Task Templates
The system SHALL support reusable task templates for common workflows.

#### Scenario: Creating task template
- **WHEN** a manager creates a task template
- **THEN** define title, description, checklist items
- **AND** set estimated duration
- **AND** define default priority and labels
- **AND** specify approval requirements

#### Scenario: Using task template
- **WHEN** a task is created from template
- **THEN** copy all template fields
- **AND** allow override of any field
- **AND** maintain template reference for analytics

### Requirement: Subtasks and Checklists
The system SHALL support hierarchical task breakdown.

#### Scenario: Adding subtasks
- **WHEN** subtasks are added to a task
- **THEN** create child tasks with independent tracking
- **AND** assign subtasks to same or different employees
- **AND** aggregate subtask progress to parent

#### Scenario: Checklist items
- **WHEN** checklist items are defined
- **THEN** provide simple checkbox interface
- **AND** calculate completion percentage from checked items
- **AND** optionally require all items for completion

### Requirement: Task Comments and Activity
The system SHALL maintain rich activity log for each task.

#### Scenario: Adding comment
- **WHEN** a user adds comment to task
- **THEN** support rich text formatting
- **AND** allow @mentions for notifications
- **AND** support file attachments
- **AND** display in chronological activity feed

#### Scenario: Activity timeline
- **WHEN** task activity is viewed
- **THEN** show all status changes, comments, updates
- **AND** display user avatars and timestamps
- **AND** highlight system events vs user actions

## API Endpoints

### GET /api/v1/tasks
List tasks with filtering and pagination.

**Query Parameters:**
- `filter[type]`: routine, situational
- `filter[status]`: not_started, in_progress, pending_review, completed, cancelled
- `filter[assignee_id]`: Filter by assignee
- `filter[creator_id]`: Filter by creator
- `filter[department_id]`: Filter by department
- `filter[priority]`: low, medium, high, critical
- `filter[due_before]`: Date filter
- `filter[due_after]`: Date filter
- `filter[overdue]`: boolean
- `include`: assignee, creator, department, subtasks, parent
- `sort`: due_date, priority, created_at, updated_at, progress

**Response (200):**
```json
{
  "data": [
    {
      "type": "tasks",
      "id": "uuid",
      "attributes": {
        "title": "Weekly Status Report",
        "description": "Prepare and submit weekly team status report",
        "type": "routine",
        "status": "in_progress",
        "priority": "medium",
        "progress": 60,
        "due_date": "2025-01-31T17:00:00Z",
        "recurrence": {
          "type": "weekly",
          "days": ["friday"],
          "time": "15:00"
        },
        "estimated_hours": 2,
        "actual_hours": 1.5,
        "is_overdue": false,
        "created_at": "2025-01-27T09:00:00Z",
        "updated_at": "2025-01-30T14:30:00Z"
      },
      "relationships": {
        "assignee": { "data": { "type": "users", "id": "uuid" } },
        "creator": { "data": { "type": "users", "id": "uuid" } },
        "department": { "data": { "type": "departments", "id": "uuid" } }
      }
    }
  ],
  "meta": {
    "stats": {
      "total": 45,
      "by_status": {
        "not_started": 5,
        "in_progress": 25,
        "pending_review": 8,
        "completed": 7
      }
    }
  }
}
```

### POST /api/v1/tasks
Create new task.

**Request:**
```json
{
  "data": {
    "type": "tasks",
    "attributes": {
      "title": "Prepare Q1 Budget Proposal",
      "description": "Draft budget proposal for Q1 2025...",
      "type": "situational",
      "priority": "high",
      "due_date": "2025-02-15T17:00:00Z",
      "estimated_hours": 8,
      "checklist": [
        { "text": "Gather expense data", "completed": false },
        { "text": "Create projections", "completed": false },
        { "text": "Review with finance", "completed": false }
      ]
    },
    "relationships": {
      "assignee": { "data": { "type": "users", "id": "uuid" } },
      "department": { "data": { "type": "departments", "id": "uuid" } }
    }
  }
}
```

### GET /api/v1/tasks/{id}
Get task details.

### PATCH /api/v1/tasks/{id}
Update task.

### DELETE /api/v1/tasks/{id}
Cancel/archive task.

### POST /api/v1/tasks/{id}/progress
Update task progress.

**Request:**
```json
{
  "progress": 75,
  "note": "Completed initial analysis, moving to drafting phase"
}
```

### POST /api/v1/tasks/{id}/status
Change task status.

**Request:**
```json
{
  "status": "pending_review",
  "comment": "Ready for review"
}
```

### POST /api/v1/tasks/{id}/submit-for-approval
Submit completed task for approval.

### POST /api/v1/tasks/{id}/approve
Approve task completion.

### POST /api/v1/tasks/{id}/request-revision
Request revision on task.

**Request:**
```json
{
  "reason": "Please include competitor analysis section",
  "revision_deadline": "2025-02-10T17:00:00Z"
}
```

### GET /api/v1/tasks/{id}/comments
Get task comments.

### POST /api/v1/tasks/{id}/comments
Add comment to task.

### GET /api/v1/tasks/{id}/activity
Get task activity log.

### GET /api/v1/tasks/{id}/subtasks
Get task subtasks.

### POST /api/v1/tasks/{id}/subtasks
Add subtask.

### GET /api/v1/tasks/{id}/dependencies
Get task dependencies.

### POST /api/v1/tasks/{id}/dependencies
Add dependency.

### DELETE /api/v1/tasks/{id}/dependencies/{depId}
Remove dependency.

### GET /api/v1/routine-templates
List routine task templates.

### POST /api/v1/routine-templates
Create routine task template.

### GET /api/v1/routine-templates/{id}
Get template details.

### PATCH /api/v1/routine-templates/{id}
Update template.

### DELETE /api/v1/routine-templates/{id}
Archive template.

### POST /api/v1/routine-templates/{id}/generate
Manually generate task instance from template.

## Data Schema

### Task
```typescript
interface Task {
  id: UUID;
  title: string;
  description?: string;
  type: 'routine' | 'situational';
  status: 'not_started' | 'in_progress' | 'pending_review' | 'completed' | 'cancelled';
  priority: 'low' | 'medium' | 'high' | 'critical';
  progress: number; // 0-100
  
  creator_id: UUID;
  assignee_id?: UUID;
  assignee_ids?: UUID[]; // for multi-assignee
  department_id: UUID;
  approver_id?: UUID;
  
  parent_id?: UUID; // for subtasks
  routine_template_id?: UUID;
  
  due_date?: Date;
  start_date?: Date;
  completed_at?: Date;
  approved_at?: Date;
  approved_by?: UUID;
  
  estimated_hours?: number;
  actual_hours?: number;
  
  recurrence?: RecurrenceRule;
  checklist?: ChecklistItem[];
  labels?: string[];
  
  revision_count: number;
  
  created_at: Date;
  updated_at: Date;
  deleted_at?: Date;
}

interface RecurrenceRule {
  type: 'daily' | 'weekly' | 'monthly' | 'custom';
  interval: number; // every N days/weeks/months
  days_of_week?: number[]; // 0-6 for weekly
  day_of_month?: number; // for monthly
  time: string; // HH:mm
  end_date?: Date;
  occurrences?: number;
  last_generated_at?: Date;
}

interface RoutineTemplate {
  id: UUID;
  title: string;
  description?: string;
  department_id: UUID;
  creator_id: UUID;
  
  default_assignee_id?: UUID;
  default_assignee_role?: string;
  assignment_strategy: 'specific' | 'role' | 'round_robin';
  
  recurrence: RecurrenceRule;
  priority: 'low' | 'medium' | 'high' | 'critical';
  estimated_hours?: number;
  checklist?: ChecklistItem[];
  
  requires_approval: boolean;
  auto_approve_conditions?: ApprovalCondition[];
  
  is_active: boolean;
  created_at: Date;
  updated_at: Date;
}

interface TaskDependency {
  id: UUID;
  task_id: UUID;
  depends_on_task_id: UUID;
  type: 'blocks' | 'blocked_by' | 'relates_to';
  created_at: Date;
}

interface ChecklistItem {
  id: UUID;
  text: string;
  completed: boolean;
  completed_at?: Date;
  completed_by?: UUID;
  order: number;
}
```
