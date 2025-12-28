# Analytics & Dashboard Specification

## Purpose

The Analytics & Dashboard module provides real-time productivity insights, visual KPIs, heatmaps, and performance tracking for owners, managers, and employees at appropriate organizational scopes.

## Requirements

### Requirement: Owner Dashboard
The system SHALL provide high-level executive view of organization-wide metrics.

#### Scenario: Viewing owner dashboard
- **WHEN** owner accesses main dashboard
- **THEN** display organization-wide productivity score
- **AND** show departmental performance comparison
- **AND** render productivity heatmap by department/time
- **AND** highlight trending KPIs (up/down indicators)

#### Scenario: Productivity heatmap
- **WHEN** heatmap widget is rendered
- **THEN** display 7-day x 24-hour grid per department
- **AND** color code by task completion rate (green→yellow→red)
- **AND** support drill-down on cell click
- **AND** animate heatmap updates in real-time

#### Scenario: Department comparison
- **WHEN** comparing departments
- **THEN** display side-by-side metrics cards
- **AND** show task completion rate, overdue rate, avg response time
- **AND** rank departments with visual leaderboard
- **AND** support time range selection (week/month/quarter)

### Requirement: PM Workspace Dashboard
The system SHALL provide department-focused task management view for project managers.

#### Scenario: Viewing PM workspace
- **WHEN** PM accesses their workspace
- **THEN** display department task overview
- **AND** show team member workload distribution
- **AND** list pending approvals requiring action
- **AND** highlight overdue and at-risk tasks

#### Scenario: Team workload visualization
- **WHEN** workload widget is displayed
- **THEN** render stacked bar chart per team member
- **AND** color code by task priority
- **AND** show capacity vs assigned comparison
- **AND** alert for over/under-allocated members

#### Scenario: Task creation quick actions
- **WHEN** PM needs to assign tasks
- **THEN** provide quick task creation form
- **AND** show job description-based task suggestions
- **AND** allow bulk assignment to team members
- **AND** preview workload impact before assigning

### Requirement: Employee Portal (Focus Mode)
The system SHALL provide distraction-free task-focused interface for employees.

#### Scenario: Focus mode view
- **WHEN** employee accesses their portal
- **THEN** display single active task prominently
- **AND** minimize navigation and chrome elements
- **AND** show large progress slider/ring for updates
- **AND** provide quick status update buttons

#### Scenario: Daily task list
- **WHEN** viewing daily task list
- **THEN** display today's tasks with priority ordering
- **AND** show time estimates and deadlines
- **AND** indicate blocked tasks with visual cue
- **AND** allow drag-and-drop reordering

#### Scenario: Progress reporting
- **WHEN** employee reports progress
- **THEN** display intuitive visual slider (0-100%)
- **AND** optionally add text note
- **AND** attach files/screenshots if needed
- **AND** show encouraging progress animations

### Requirement: Visual KPI Cards
The system SHALL display key metrics in visually appealing card format.

#### Scenario: Rendering KPI card
- **WHEN** KPI card is displayed
- **THEN** show large metric value with subtle animation
- **AND** display trend indicator (↑/↓/→) with percentage
- **AND** show sparkline for trend visualization
- **AND** apply appropriate color coding

#### Scenario: KPI drill-down
- **WHEN** user clicks on KPI card
- **THEN** expand to detailed view
- **AND** show breakdown by dimension (time/department/user)
- **AND** provide export option

### Requirement: Progress Visualization
The system SHALL render various progress indicators with smooth animations.

#### Scenario: Progress ring animation
- **WHEN** progress ring is displayed
- **THEN** animate from 0 to current value on load
- **AND** use GSAP for smooth easing
- **AND** display percentage in center
- **AND** apply gradient based on completion level

#### Scenario: Status cards
- **WHEN** status card is rendered
- **THEN** display status icon with subtle background
- **AND** show count and label
- **AND** apply status-appropriate color
- **AND** animate count changes

### Requirement: Report Generation
The system SHALL support exportable reports in multiple formats.

#### Scenario: Generating PDF report
- **WHEN** user requests PDF export
- **THEN** render report with branding
- **AND** include charts as images
- **AND** format data tables appropriately
- **AND** add generation timestamp and filters used

#### Scenario: Generating Excel report
- **WHEN** user requests Excel export
- **THEN** export raw data with formatting
- **AND** include multiple sheets for different views
- **AND** add formulas for calculated fields
- **AND** include chart objects where possible

#### Scenario: Scheduled reports
- **WHEN** user schedules recurring report
- **THEN** generate on schedule (daily/weekly/monthly)
- **AND** deliver via email with attachment
- **AND** support multiple recipients
- **AND** allow report customization

### Requirement: Real-Time Updates
The system SHALL update dashboard metrics in real-time.

#### Scenario: WebSocket metric updates
- **WHEN** metric changes occur
- **THEN** broadcast via WebSocket to connected clients
- **AND** animate metric value changes
- **AND** highlight recently updated cards
- **AND** maintain smooth 60fps animations

## API Endpoints

### GET /api/v1/analytics/owner-dashboard
Get owner-level dashboard metrics.

**Query Parameters:**
- `period`: today, week, month, quarter, year
- `department_id`: Filter by department (optional)

**Response (200):**
```json
{
  "data": {
    "type": "owner_dashboard",
    "attributes": {
      "period": "week",
      "productivity_score": 87.5,
      "productivity_trend": 3.2,
      "total_tasks": 450,
      "completed_tasks": 380,
      "overdue_tasks": 12,
      "pending_approvals": 25,
      "active_employees": 145,
      "departments_summary": [
        {
          "department_id": "uuid",
          "name": "Engineering",
          "score": 92,
          "trend": 5.1,
          "tasks_completed": 120,
          "tasks_total": 130
        }
      ],
      "heatmap_data": {
        "departments": ["Engineering", "Sales", "Support"],
        "hours": [0, 1, 2, "...", 23],
        "data": [[0.8, 0.7, "..."], ["..."]]
      }
    }
  }
}
```

### GET /api/v1/analytics/pm-dashboard
Get PM workspace dashboard metrics.

**Query Parameters:**
- `department_id`: Required - PM's department
- `period`: today, week, month

**Response (200):**
```json
{
  "data": {
    "type": "pm_dashboard",
    "attributes": {
      "department": {
        "id": "uuid",
        "name": "Engineering"
      },
      "task_summary": {
        "total": 45,
        "not_started": 5,
        "in_progress": 25,
        "pending_review": 8,
        "completed": 7,
        "overdue": 3
      },
      "team_workload": [
        {
          "user_id": "uuid",
          "name": "John Doe",
          "avatar_url": "...",
          "assigned_tasks": 8,
          "capacity": 10,
          "utilization": 0.8,
          "tasks_by_priority": {
            "critical": 1,
            "high": 3,
            "medium": 3,
            "low": 1
          }
        }
      ],
      "pending_approvals": [
        {
          "task_id": "uuid",
          "title": "Weekly Report",
          "assignee": "Jane Doe",
          "submitted_at": "2025-01-30T14:00:00Z"
        }
      ],
      "at_risk_tasks": []
    }
  }
}
```

### GET /api/v1/analytics/employee-dashboard
Get employee focus dashboard.

**Response (200):**
```json
{
  "data": {
    "type": "employee_dashboard",
    "attributes": {
      "today_summary": {
        "tasks_due": 3,
        "tasks_completed": 2,
        "hours_logged": 4.5
      },
      "current_task": {
        "id": "uuid",
        "title": "Prepare presentation",
        "progress": 60,
        "due_date": "2025-01-30T17:00:00Z",
        "priority": "high"
      },
      "upcoming_tasks": [],
      "weekly_progress": {
        "completed": 12,
        "total": 15,
        "streak_days": 5
      }
    }
  }
}
```

### GET /api/v1/analytics/productivity-heatmap
Get productivity heatmap data.

**Query Parameters:**
- `department_id`: Filter by department
- `period`: week, month
- `metric`: completion_rate, task_count, avg_time

### GET /api/v1/analytics/department-comparison
Get department comparison metrics.

### GET /api/v1/analytics/user/{id}/performance
Get individual user performance metrics.

### GET /api/v1/analytics/trends
Get historical trend data for charting.

**Query Parameters:**
- `metric`: productivity, completion_rate, overdue_rate
- `period`: week, month, quarter, year
- `granularity`: hour, day, week, month
- `department_id`: Filter (optional)

### POST /api/v1/reports/generate
Generate report on demand.

**Request:**
```json
{
  "type": "productivity",
  "format": "pdf",
  "period": {
    "start": "2025-01-01",
    "end": "2025-01-31"
  },
  "filters": {
    "departments": ["uuid1", "uuid2"]
  },
  "include_charts": true
}
```

### GET /api/v1/reports/scheduled
List scheduled reports.

### POST /api/v1/reports/scheduled
Create scheduled report.

### DELETE /api/v1/reports/scheduled/{id}
Delete scheduled report.

### GET /api/v1/reports/{id}/download
Download generated report.

## Data Schema

### DashboardMetrics
```typescript
interface OwnerDashboard {
  productivity_score: number;
  productivity_trend: number;
  total_tasks: number;
  completed_tasks: number;
  overdue_tasks: number;
  pending_approvals: number;
  active_employees: number;
  departments_summary: DepartmentSummary[];
  heatmap_data: HeatmapData;
  top_performers: UserPerformance[];
  alerts: DashboardAlert[];
}

interface PMDashboard {
  department: DepartmentInfo;
  task_summary: TaskSummary;
  team_workload: TeamMemberWorkload[];
  pending_approvals: PendingApproval[];
  at_risk_tasks: AtRiskTask[];
  routine_templates: RoutineTemplateSummary[];
}

interface EmployeeDashboard {
  today_summary: DaySummary;
  current_task: TaskSummary | null;
  upcoming_tasks: TaskSummary[];
  weekly_progress: WeekProgress;
  achievements: Achievement[];
}

interface HeatmapData {
  departments: string[];
  hours: number[];
  data: number[][]; // [dept][hour] = 0-1 value
  period_start: Date;
  period_end: Date;
}

interface KPICard {
  id: string;
  label: string;
  value: number | string;
  format: 'number' | 'percentage' | 'currency' | 'duration';
  trend: 'up' | 'down' | 'stable';
  trend_value: number;
  sparkline_data?: number[];
  color: 'green' | 'yellow' | 'red' | 'blue' | 'gray';
  click_action?: string;
}

interface ScheduledReport {
  id: UUID;
  name: string;
  type: 'productivity' | 'tasks' | 'approvals' | 'custom';
  format: 'pdf' | 'excel' | 'csv';
  schedule: {
    frequency: 'daily' | 'weekly' | 'monthly';
    day_of_week?: number;
    day_of_month?: number;
    time: string;
  };
  filters: ReportFilters;
  recipients: string[]; // email addresses
  created_by: UUID;
  last_generated_at?: Date;
  next_generation_at: Date;
  is_active: boolean;
}
```

## Widget Configuration

### Available Widgets
| Widget ID | Name | Roles | Size Options |
|-----------|------|-------|--------------|
| productivity_score | Productivity Score | owner, pm | small, medium |
| task_summary | Task Summary | all | small, medium, large |
| team_workload | Team Workload | pm | medium, large |
| heatmap | Productivity Heatmap | owner | large |
| dept_comparison | Department Comparison | owner | medium, large |
| pending_approvals | Pending Approvals | pm | small, medium |
| current_task | Current Task | employee | medium |
| daily_tasks | Today's Tasks | employee | medium, large |
| weekly_progress | Weekly Progress | all | small |
| performance_trend | Performance Trend | owner, pm | medium |
