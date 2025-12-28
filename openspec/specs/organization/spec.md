# Organization Hierarchy Specification

## Purpose

The Organization Hierarchy module manages the company structure as a dynamic, interactive tree with departments, positions, and reporting relationships, visualized through an animated org-chart.

## Requirements

### Requirement: Hierarchical Organization Structure
The system SHALL maintain a tree-based organizational structure with unlimited depth.

#### Scenario: Creating organization root
- **WHEN** a new organization is initialized
- **THEN** create root node (Company/Holding)
- **AND** assign CEO/President position to root
- **AND** initialize default departments template

#### Scenario: Adding department node
- **WHEN** an admin adds a department to the hierarchy
- **THEN** create node with parent reference
- **AND** assign department head position
- **AND** inherit permission policies from parent
- **AND** trigger org-chart re-render animation

#### Scenario: Moving department in hierarchy
- **WHEN** a department is moved to new parent
- **THEN** update all child references recursively
- **AND** recalculate reporting chains for all members
- **AND** maintain historical structure in audit log
- **AND** animate tree transition smoothly

### Requirement: Department Management
The system SHALL provide comprehensive department lifecycle management.

#### Scenario: Creating department
- **WHEN** an admin creates a department
- **THEN** require name, code, and parent department
- **AND** assign department head (optional initially)
- **AND** set budget allocation (optional)
- **AND** define department type (operational/support/executive)

#### Scenario: Department metrics aggregation
- **WHEN** department metrics are requested
- **THEN** aggregate employee count from all descendants
- **AND** calculate task completion rates
- **AND** sum budget utilization
- **AND** generate productivity score

#### Scenario: Merging departments
- **WHEN** two departments are merged
- **THEN** transfer all employees to target department
- **AND** reassign pending tasks
- **AND** archive source department (soft delete)
- **AND** maintain historical data for reporting

### Requirement: Position Management
The system SHALL manage job positions within the organizational hierarchy.

#### Scenario: Defining position
- **WHEN** a position is created
- **THEN** associate with department
- **AND** define job description template
- **AND** set required skills/competencies
- **AND** specify reporting relationships (reports to, supervises)

#### Scenario: Position vacancy
- **WHEN** a position becomes vacant
- **THEN** update org-chart to show vacancy indicator
- **AND** optionally trigger recruitment workflow
- **AND** redistribute responsibilities temporarily

### Requirement: Visual Org-Chart
The system SHALL render an interactive, visual organizational hierarchy using card-based components.

#### Scenario: Rendering full org-chart
- **WHEN** org-chart view is accessed
- **THEN** render hierarchical tree using Vue card-based components
- **AND** display personnel photos in rounded containers
- **AND** show position title, name, and department
- **AND** use CSS transitions for smooth entrance animations
- **AND** create visual hierarchy with connecting lines using CSS/SVG

#### Scenario: Visual card layout
- **WHEN** org-chart nodes are displayed
- **THEN** render each node as a styled card component
- **AND** show avatar/photo prominently with rounded styling
- **AND** display title and brief description
- **AND** use color coding for different departments/levels
- **AND** support hover states for additional information

#### Scenario: Expanding/collapsing branches
- **WHEN** a user clicks on a department node
- **THEN** toggle visibility of child nodes
- **AND** animate expansion/collapse with CSS transitions (300ms ease)
- **AND** maintain viewport focus on clicked node
- **AND** persist expansion state per user

#### Scenario: Interactive node cards
- **WHEN** a user interacts with an org-chart node
- **THEN** highlight the selected card with visual feedback
- **AND** show action buttons on hover (view details, edit, etc.)
- **AND** display tooltip with quick summary
- **AND** support click to navigate to detail view

#### Scenario: Org-chart search
- **WHEN** a user searches within org-chart
- **THEN** highlight matching nodes
- **AND** auto-expand path to matched nodes
- **AND** dim non-matching branches
- **AND** provide navigation between matches

### Requirement: Reporting Chain Calculation
The system SHALL automatically compute and maintain reporting relationships.

#### Scenario: Calculating reporting chain
- **WHEN** an employee's manager is updated
- **THEN** recalculate full reporting chain (up to CEO)
- **AND** update skip-level manager references
- **AND** recalculate visibility permissions
- **AND** cache chain for performance

#### Scenario: Circular reference prevention
- **WHEN** a manager assignment would create circular reference
- **THEN** reject the change with clear error message
- **AND** suggest valid alternatives

## API Endpoints

### GET /api/v1/organization
Get organization root with hierarchy metadata.

**Response (200):**
```json
{
  "data": {
    "type": "organizations",
    "id": "uuid",
    "attributes": {
      "name": "TechCorp Holdings",
      "code": "TECH",
      "logo_url": "https://cdn.ishyar.io/logos/...",
      "employee_count": 500,
      "department_count": 25,
      "hierarchy_depth": 5
    }
  }
}
```

### GET /api/v1/organization/tree
Get full organizational tree structure.

**Query Parameters:**
- `depth`: Maximum depth to return (default: all)
- `include_employees`: Include employee data in nodes
- `include_metrics`: Include department metrics
- `collapsed_ids[]`: Array of node IDs to return collapsed

**Response (200):**
```json
{
  "data": {
    "type": "org_nodes",
    "id": "root-uuid",
    "attributes": {
      "name": "TechCorp Holdings",
      "type": "company",
      "head": {
        "id": "user-uuid",
        "name": "John Smith",
        "title": "CEO",
        "avatar_url": "..."
      },
      "employee_count": 500,
      "direct_reports": 5
    },
    "children": [
      {
        "type": "org_nodes",
        "id": "dept-uuid",
        "attributes": {
          "name": "Engineering",
          "type": "department",
          "head": { "..." },
          "employee_count": 150
        },
        "children": []
      }
    ]
  }
}
```

### GET /api/v1/departments
List all departments with filtering.

**Query Parameters:**
- `filter[parent_id]`: Filter by parent department
- `filter[type]`: Department type filter
- `include`: head, parent, children, employees
- `sort`: name, employee_count, created_at

### POST /api/v1/departments
Create new department.

**Request:**
```json
{
  "data": {
    "type": "departments",
    "attributes": {
      "name": "Mobile Development",
      "code": "MOB-DEV",
      "description": "Mobile application development team",
      "type": "operational"
    },
    "relationships": {
      "parent": { "data": { "type": "departments", "id": "engineering-uuid" } },
      "head": { "data": { "type": "users", "id": "manager-uuid" } }
    }
  }
}
```

### GET /api/v1/departments/{id}
Get department details.

### PATCH /api/v1/departments/{id}
Update department.

### DELETE /api/v1/departments/{id}
Archive department (soft delete).

### POST /api/v1/departments/{id}/move
Move department to new parent.

**Request:**
```json
{
  "new_parent_id": "uuid"
}
```

### POST /api/v1/departments/{id}/merge
Merge into another department.

**Request:**
```json
{
  "target_department_id": "uuid"
}
```

### GET /api/v1/departments/{id}/employees
Get employees in department.

### GET /api/v1/departments/{id}/metrics
Get department metrics and KPIs.

### GET /api/v1/positions
List all positions.

### POST /api/v1/positions
Create position.

### GET /api/v1/positions/{id}
Get position details.

### PATCH /api/v1/positions/{id}
Update position.

### DELETE /api/v1/positions/{id}
Archive position.

### GET /api/v1/users/{id}/reporting-chain
Get user's full reporting chain.

**Response (200):**
```json
{
  "data": {
    "chain": [
      { "type": "users", "id": "uuid", "attributes": { "name": "CEO", "title": "Chief Executive Officer" } },
      { "type": "users", "id": "uuid", "attributes": { "name": "VP Engineering", "title": "Vice President" } },
      { "type": "users", "id": "uuid", "attributes": { "name": "Team Lead", "title": "Engineering Manager" } }
    ],
    "skip_level_manager": { "type": "users", "id": "uuid" }
  }
}
```

## Data Schema

### Department
```typescript
interface Department {
  id: UUID;
  organization_id: UUID;
  parent_id?: UUID;
  name: string;
  code: string;
  description?: string;
  type: 'executive' | 'operational' | 'support';
  head_id?: UUID;
  budget?: number;
  budget_currency: string;
  settings: DepartmentSettings;
  level: number; // depth in tree
  path: string; // materialized path for queries
  employee_count: number; // denormalized
  created_at: Date;
  updated_at: Date;
  deleted_at?: Date;
}

interface Position {
  id: UUID;
  department_id: UUID;
  title: string;
  code: string;
  description?: string;
  job_description_template_id?: UUID;
  reports_to_position_id?: UUID;
  is_managerial: boolean;
  required_skills: SkillRequirement[];
  max_headcount?: number;
  current_headcount: number;
  created_at: Date;
  updated_at: Date;
}

interface OrgNode {
  id: UUID;
  type: 'company' | 'division' | 'department' | 'team';
  name: string;
  head: UserSummary | null;
  employee_count: number;
  children: OrgNode[];
  collapsed: boolean;
  metrics?: DepartmentMetrics;
}
```
