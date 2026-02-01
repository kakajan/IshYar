# CRM Module Specification

## Purpose

The CRM (Customer Relationship Management) module extends IshYar's WorkSuite capabilities to include contact management, lead tracking, deal pipeline, customer communications, and sales analytics. This module transforms IshYar into a complete business management platform.

## Module Manifest

```json
{
  "name": "ishyar/crm",
  "version": "1.0.0",
  "displayName": "Customer Relationship Management",
  "description": "Complete CRM with contacts, leads, deals, and sales pipeline",
  "author": "IshYar Core Team",
  "license": "MIT",
  "category": "sales",
  "icon": "users-round",
  
  "requires": {
    "ishyar/core": "^1.0",
    "php": "^8.3"
  },
  
  "provides": {
    "services": ["ContactService", "LeadService", "DealService", "PipelineService"],
    "facades": ["CRM", "Sales"],
    "models": ["Contact", "Company", "Lead", "Deal", "Pipeline", "Stage", "Activity"],
    "commands": [
      "crm:sync-contacts",
      "crm:cleanup-stale-leads",
      "crm:send-reminders"
    ]
  },
  
  "hooks": {
    "extend": ["dashboard.widgets", "search.providers", "reports.categories"],
    "listen": ["task.completed", "user.created", "organization.created"]
  },
  
  "permissions": [
    "crm.contacts.view",
    "crm.contacts.create",
    "crm.contacts.edit",
    "crm.contacts.delete",
    "crm.contacts.export",
    "crm.leads.view",
    "crm.leads.manage",
    "crm.deals.view",
    "crm.deals.manage",
    "crm.pipeline.configure",
    "crm.reports.view"
  ]
}
```

---

## Requirements

### Requirement: Contact Management
The system SHALL provide comprehensive contact and company management.

#### Scenario: Creating a contact
- **WHEN** user creates a new contact
- **THEN** capture essential fields (name, email, phone, company)
- **AND** automatically detect duplicate contacts
- **AND** enrich contact data from external sources (optional)
- **AND** assign to account owner
- **AND** create activity log entry

#### Scenario: Contact profile view
- **WHEN** user views a contact profile
- **THEN** display contact information in organized sections
- **AND** show associated company details
- **AND** display communication history timeline
- **AND** list related deals and opportunities
- **AND** show linked tasks and activities
- **AND** display custom fields

#### Scenario: Company management
- **WHEN** user manages company records
- **THEN** support company CRUD operations
- **AND** link multiple contacts to a company
- **AND** track company hierarchy (parent/subsidiary)
- **AND** display company-level metrics
- **AND** show all deals associated with company

#### Scenario: Contact import/export
- **WHEN** user imports contacts
- **THEN** accept CSV, Excel, vCard formats
- **AND** map columns to contact fields
- **AND** detect and handle duplicates
- **AND** validate data before import
- **AND** provide import summary report

### Requirement: Lead Management
The system SHALL track and nurture potential customers through lead stages.

#### Scenario: Lead capture
- **WHEN** a lead is captured
- **THEN** create lead record with source tracking
- **AND** assign lead score based on criteria
- **AND** assign to sales representative (round-robin or rules-based)
- **AND** trigger welcome automation
- **AND** add to nurture campaign

#### Scenario: Lead qualification
- **WHEN** sales rep qualifies a lead
- **THEN** update lead status (New → Contacted → Qualified → Converted)
- **AND** capture qualification notes
- **AND** update lead score
- **AND** create follow-up tasks automatically
- **AND** convert to contact + deal when ready

#### Scenario: Lead scoring
- **WHEN** lead attributes or behavior changes
- **THEN** recalculate lead score based on:
  - Demographic fit (title, company size, industry)
  - Behavioral signals (email opens, website visits)
  - Engagement level (responses, meetings)
- **AND** trigger alerts for high-score leads
- **AND** update priority accordingly

#### Scenario: Lead nurturing
- **WHEN** lead enters nurture sequence
- **THEN** send scheduled communications
- **AND** track engagement metrics
- **AND** adjust sequence based on behavior
- **AND** alert sales on engagement spikes

### Requirement: Deal Pipeline
The system SHALL provide visual deal tracking through customizable pipelines.

#### Scenario: Pipeline configuration
- **WHEN** admin configures sales pipeline
- **THEN** create custom stages with names and colors
- **AND** set probability percentages per stage
- **AND** define required fields per stage
- **AND** configure automation triggers
- **AND** set stage-specific workflows

#### Scenario: Deal management
- **WHEN** sales rep manages a deal
- **THEN** display deal details (value, close date, stage)
- **AND** track contacts and decision makers
- **AND** log all activities and communications
- **AND** attach proposals and documents
- **AND** track competitors

#### Scenario: Visual pipeline board
- **WHEN** user views pipeline board
- **THEN** display Kanban-style deal cards
- **AND** support drag-and-drop stage changes
- **AND** show weighted value per stage
- **AND** highlight stale deals
- **AND** filter by rep, date, value
- **AND** update in real-time

#### Scenario: Deal progression
- **WHEN** deal moves to new stage
- **THEN** validate required fields for new stage
- **AND** update probability and forecast
- **AND** create stage-appropriate tasks
- **AND** notify relevant stakeholders
- **AND** log stage change with timestamp

#### Scenario: Deal won/lost
- **WHEN** deal is marked won or lost
- **THEN** capture win/loss reason
- **AND** update pipeline metrics
- **AND** trigger celebration notification (won)
- **AND** move contacts to customer segment (won)
- **AND** create onboarding tasks (won)
- **AND** schedule follow-up (lost - future opportunity)

### Requirement: Activity Tracking
The system SHALL log all customer-related activities and interactions.

#### Scenario: Activity types
- **WHEN** user logs an activity
- **THEN** support multiple types:
  - Call (with duration and outcome)
  - Email (sent/received tracking)
  - Meeting (with attendees and notes)
  - Note (internal observations)
  - Task (action items)
- **AND** capture relevant metadata
- **AND** link to contact/company/deal

#### Scenario: Activity timeline
- **WHEN** viewing contact/deal timeline
- **THEN** display chronological activity list
- **AND** filter by activity type
- **AND** show rich activity details
- **AND** enable inline activity creation
- **AND** paginate for performance

#### Scenario: Activity reminders
- **WHEN** activity has scheduled follow-up
- **THEN** create task or reminder
- **AND** send notification at due time
- **AND** escalate if overdue

### Requirement: Communication Hub
The system SHALL centralize customer communications.

#### Scenario: Email integration
- **WHEN** email is connected
- **THEN** sync sent/received emails
- **AND** match emails to contacts automatically
- **AND** support sending emails from CRM
- **AND** track opens and clicks
- **AND** provide email templates

#### Scenario: Email templates
- **WHEN** user creates email template
- **THEN** support rich text formatting
- **AND** allow variable placeholders
- **AND** preview with sample data
- **AND** track template performance

#### Scenario: Call logging
- **WHEN** call is logged
- **THEN** capture call details (type, duration, outcome)
- **AND** record call notes
- **AND** suggest follow-up actions
- **AND** optionally integrate with VoIP

### Requirement: Sales Analytics
The system SHALL provide actionable sales insights and forecasting.

#### Scenario: Pipeline dashboard
- **WHEN** manager views sales dashboard
- **THEN** display pipeline value by stage (funnel)
- **AND** show conversion rates between stages
- **AND** compare to targets/quotas
- **AND** highlight pipeline health metrics

#### Scenario: Sales forecasting
- **WHEN** forecast is generated
- **THEN** calculate weighted pipeline value
- **AND** apply historical conversion rates
- **AND** consider deal age and activity
- **AND** show forecast vs quota comparison
- **AND** break down by rep/team/product

#### Scenario: Rep performance
- **WHEN** viewing rep metrics
- **THEN** display activities performed
- **AND** show deals created/closed
- **AND** calculate conversion rates
- **AND** compare to team average
- **AND** track quota attainment

#### Scenario: Sales reports
- **WHEN** user generates sales report
- **THEN** offer report templates:
  - Pipeline report
  - Activity report
  - Win/loss analysis
  - Lead source report
  - Forecast report
- **AND** support custom date ranges
- **AND** export to PDF/Excel
- **AND** schedule recurring reports

### Requirement: Automation Rules
The system SHALL automate routine sales processes.

#### Scenario: Lead assignment rules
- **WHEN** new lead is created
- **THEN** evaluate assignment rules
- **AND** assign based on:
  - Round-robin distribution
  - Territory/region
  - Lead score threshold
  - Industry/company size
- **AND** notify assigned rep

#### Scenario: Follow-up automation
- **WHEN** activity trigger occurs
- **THEN** create follow-up task
- **AND** send reminder emails
- **AND** escalate if no response
- **AND** log automation actions

#### Scenario: Deal stage automation
- **WHEN** deal enters specific stage
- **THEN** execute stage automation:
  - Send proposal template
  - Create contract task
  - Notify manager
  - Schedule demo
- **AND** track automation effectiveness

---

## Database Schema

### Core Tables

```sql
-- Companies
CREATE TABLE crm_companies (
  id UUID PRIMARY KEY,
  organization_id UUID REFERENCES organizations(id),
  name JSONB NOT NULL, -- Translatable
  industry VARCHAR(100),
  size_range VARCHAR(50), -- 1-10, 11-50, 51-200, etc.
  website VARCHAR(255),
  phone VARCHAR(50),
  address JSONB,
  parent_company_id UUID REFERENCES crm_companies(id),
  owner_id UUID REFERENCES users(id),
  custom_fields JSONB,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  deleted_at TIMESTAMP
);

-- Contacts
CREATE TABLE crm_contacts (
  id UUID PRIMARY KEY,
  organization_id UUID REFERENCES organizations(id),
  company_id UUID REFERENCES crm_companies(id),
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255),
  phone VARCHAR(50),
  mobile VARCHAR(50),
  job_title VARCHAR(100),
  department VARCHAR(100),
  source VARCHAR(50), -- website, referral, event, etc.
  owner_id UUID REFERENCES users(id),
  avatar_url VARCHAR(500),
  social_profiles JSONB,
  custom_fields JSONB,
  last_contacted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  deleted_at TIMESTAMP
);

-- Leads
CREATE TABLE crm_leads (
  id UUID PRIMARY KEY,
  organization_id UUID REFERENCES organizations(id),
  contact_id UUID REFERENCES crm_contacts(id),
  status VARCHAR(50) DEFAULT 'new', -- new, contacted, qualified, converted, lost
  source VARCHAR(50),
  score INTEGER DEFAULT 0,
  score_factors JSONB,
  assigned_to UUID REFERENCES users(id),
  converted_at TIMESTAMP,
  converted_to_deal_id UUID,
  notes TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Pipelines
CREATE TABLE crm_pipelines (
  id UUID PRIMARY KEY,
  organization_id UUID REFERENCES organizations(id),
  name JSONB NOT NULL, -- Translatable
  is_default BOOLEAN DEFAULT false,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Pipeline Stages
CREATE TABLE crm_stages (
  id UUID PRIMARY KEY,
  pipeline_id UUID REFERENCES crm_pipelines(id),
  name JSONB NOT NULL, -- Translatable
  color VARCHAR(20),
  probability INTEGER DEFAULT 0, -- 0-100
  position INTEGER,
  required_fields JSONB,
  automations JSONB,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Deals
CREATE TABLE crm_deals (
  id UUID PRIMARY KEY,
  organization_id UUID REFERENCES organizations(id),
  pipeline_id UUID REFERENCES crm_pipelines(id),
  stage_id UUID REFERENCES crm_stages(id),
  name VARCHAR(255) NOT NULL,
  value DECIMAL(15,2),
  currency VARCHAR(3) DEFAULT 'USD',
  expected_close_date DATE,
  actual_close_date DATE,
  status VARCHAR(20) DEFAULT 'open', -- open, won, lost
  win_loss_reason VARCHAR(255),
  probability INTEGER,
  company_id UUID REFERENCES crm_companies(id),
  primary_contact_id UUID REFERENCES crm_contacts(id),
  owner_id UUID REFERENCES users(id),
  source VARCHAR(50),
  description TEXT,
  custom_fields JSONB,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  deleted_at TIMESTAMP
);

-- Deal Contacts (junction)
CREATE TABLE crm_deal_contacts (
  deal_id UUID REFERENCES crm_deals(id),
  contact_id UUID REFERENCES crm_contacts(id),
  role VARCHAR(50), -- decision_maker, influencer, champion, etc.
  PRIMARY KEY (deal_id, contact_id)
);

-- Activities
CREATE TABLE crm_activities (
  id UUID PRIMARY KEY,
  organization_id UUID REFERENCES organizations(id),
  type VARCHAR(50) NOT NULL, -- call, email, meeting, note, task
  subject VARCHAR(255),
  description TEXT,
  contact_id UUID REFERENCES crm_contacts(id),
  company_id UUID REFERENCES crm_companies(id),
  deal_id UUID REFERENCES crm_deals(id),
  user_id UUID REFERENCES users(id),
  scheduled_at TIMESTAMP,
  completed_at TIMESTAMP,
  duration_minutes INTEGER,
  outcome VARCHAR(50),
  metadata JSONB, -- type-specific data
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Email Tracking
CREATE TABLE crm_emails (
  id UUID PRIMARY KEY,
  activity_id UUID REFERENCES crm_activities(id),
  message_id VARCHAR(255),
  from_email VARCHAR(255),
  to_emails JSONB,
  cc_emails JSONB,
  subject VARCHAR(500),
  body_html TEXT,
  body_text TEXT,
  sent_at TIMESTAMP,
  opened_at TIMESTAMP,
  clicked_at TIMESTAMP,
  open_count INTEGER DEFAULT 0,
  click_count INTEGER DEFAULT 0,
  status VARCHAR(20) -- draft, sent, delivered, bounced
);
```

---

## API Endpoints

### Contacts

```
GET    /api/v1/crm/contacts           - List contacts with filters
POST   /api/v1/crm/contacts           - Create contact
GET    /api/v1/crm/contacts/{id}      - Get contact details
PUT    /api/v1/crm/contacts/{id}      - Update contact
DELETE /api/v1/crm/contacts/{id}      - Delete contact
GET    /api/v1/crm/contacts/{id}/activities - Contact activities
POST   /api/v1/crm/contacts/import    - Import contacts
GET    /api/v1/crm/contacts/export    - Export contacts
POST   /api/v1/crm/contacts/{id}/merge - Merge duplicate contacts
```

### Companies

```
GET    /api/v1/crm/companies          - List companies
POST   /api/v1/crm/companies          - Create company
GET    /api/v1/crm/companies/{id}     - Get company details
PUT    /api/v1/crm/companies/{id}     - Update company
DELETE /api/v1/crm/companies/{id}     - Delete company
GET    /api/v1/crm/companies/{id}/contacts - Company contacts
GET    /api/v1/crm/companies/{id}/deals    - Company deals
```

### Leads

```
GET    /api/v1/crm/leads              - List leads
POST   /api/v1/crm/leads              - Create lead
GET    /api/v1/crm/leads/{id}         - Get lead details
PUT    /api/v1/crm/leads/{id}         - Update lead
DELETE /api/v1/crm/leads/{id}         - Delete lead
POST   /api/v1/crm/leads/{id}/convert - Convert lead to deal
POST   /api/v1/crm/leads/{id}/assign  - Assign lead
PUT    /api/v1/crm/leads/{id}/score   - Update lead score
```

### Deals

```
GET    /api/v1/crm/deals              - List deals
POST   /api/v1/crm/deals              - Create deal
GET    /api/v1/crm/deals/{id}         - Get deal details
PUT    /api/v1/crm/deals/{id}         - Update deal
DELETE /api/v1/crm/deals/{id}         - Delete deal
PUT    /api/v1/crm/deals/{id}/stage   - Move deal stage
PUT    /api/v1/crm/deals/{id}/won     - Mark deal won
PUT    /api/v1/crm/deals/{id}/lost    - Mark deal lost
GET    /api/v1/crm/deals/{id}/activities - Deal activities
```

### Pipelines

```
GET    /api/v1/crm/pipelines          - List pipelines
POST   /api/v1/crm/pipelines          - Create pipeline
GET    /api/v1/crm/pipelines/{id}     - Get pipeline with stages
PUT    /api/v1/crm/pipelines/{id}     - Update pipeline
DELETE /api/v1/crm/pipelines/{id}     - Delete pipeline
POST   /api/v1/crm/pipelines/{id}/stages - Add stage
PUT    /api/v1/crm/pipelines/{id}/stages/reorder - Reorder stages
```

### Activities

```
GET    /api/v1/crm/activities         - List activities
POST   /api/v1/crm/activities         - Log activity
GET    /api/v1/crm/activities/{id}    - Get activity
PUT    /api/v1/crm/activities/{id}    - Update activity
DELETE /api/v1/crm/activities/{id}    - Delete activity
PUT    /api/v1/crm/activities/{id}/complete - Mark complete
```

### Analytics

```
GET    /api/v1/crm/analytics/pipeline  - Pipeline metrics
GET    /api/v1/crm/analytics/forecast  - Sales forecast
GET    /api/v1/crm/analytics/activities - Activity metrics
GET    /api/v1/crm/analytics/conversion - Conversion rates
GET    /api/v1/crm/analytics/reps      - Rep performance
```

---

## UI Components

### Contact Card

```vue
<template>
  <Card class="hover:shadow-lg transition-shadow">
    <CardContent class="p-4">
      <div class="flex items-start gap-3">
        <Avatar :src="contact.avatar" :name="contact.full_name" size="lg" />
        <div class="flex-1 min-w-0">
          <h3 class="font-medium truncate">{{ contact.full_name }}</h3>
          <p class="text-sm text-neutral-500 truncate">{{ contact.job_title }}</p>
          <p class="text-sm text-neutral-500 truncate">{{ contact.company?.name }}</p>
        </div>
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button variant="ghost" size="icon">
              <MoreVertical class="w-4 h-4" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            <DropdownMenuItem @click="$emit('edit', contact)">Edit</DropdownMenuItem>
            <DropdownMenuItem @click="$emit('call', contact)">Call</DropdownMenuItem>
            <DropdownMenuItem @click="$emit('email', contact)">Email</DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem class="text-red-500" @click="$emit('delete', contact)">
              Delete
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
      
      <div class="mt-4 space-y-2">
        <div v-if="contact.email" class="flex items-center gap-2 text-sm">
          <Mail class="w-4 h-4 text-neutral-400" />
          <a :href="`mailto:${contact.email}`" class="text-primary-600 hover:underline">
            {{ contact.email }}
          </a>
        </div>
        <div v-if="contact.phone" class="flex items-center gap-2 text-sm">
          <Phone class="w-4 h-4 text-neutral-400" />
          <a :href="`tel:${contact.phone}`" class="text-primary-600 hover:underline">
            {{ contact.phone }}
          </a>
        </div>
      </div>
      
      <div class="mt-4 pt-4 border-t flex items-center justify-between text-xs text-neutral-500">
        <span>Last contacted {{ formatRelative(contact.last_contacted_at) }}</span>
        <Badge :variant="sourceVariant">{{ contact.source }}</Badge>
      </div>
    </CardContent>
  </Card>
</template>
```

### Deal Pipeline Board

```vue
<template>
  <div class="flex gap-4 overflow-x-auto pb-4 px-4">
    <div 
      v-for="stage in pipeline.stages" 
      :key="stage.id"
      class="flex-shrink-0 w-80"
    >
      <!-- Stage Header -->
      <div 
        class="flex items-center justify-between p-3 rounded-t-lg"
        :style="{ backgroundColor: `${stage.color}20` }"
      >
        <div class="flex items-center gap-2">
          <div 
            class="w-3 h-3 rounded-full" 
            :style="{ backgroundColor: stage.color }"
          />
          <span class="font-medium">{{ stage.name }}</span>
          <Badge variant="secondary" size="sm">{{ stage.deals.length }}</Badge>
        </div>
        <span class="text-sm text-neutral-500">
          {{ formatCurrency(stageValue(stage)) }}
        </span>
      </div>
      
      <!-- Deal Cards -->
      <div 
        class="bg-neutral-100 dark:bg-neutral-900 rounded-b-lg p-2 min-h-[200px]"
      >
        <draggable
          v-model="stage.deals"
          group="deals"
          item-key="id"
          @change="onDealMove($event, stage)"
          class="space-y-2"
        >
          <template #item="{ element: deal }">
            <DealCard 
              :deal="deal" 
              @click="openDeal(deal)"
              @edit="editDeal(deal)"
            />
          </template>
        </draggable>
        
        <Button 
          variant="ghost" 
          class="w-full mt-2 border-2 border-dashed"
          @click="createDeal(stage)"
        >
          <Plus class="w-4 h-4 mr-2" />
          Add Deal
        </Button>
      </div>
    </div>
  </div>
</template>
```

### Activity Timeline

```vue
<template>
  <div class="space-y-4">
    <div 
      v-for="activity in activities" 
      :key="activity.id"
      class="flex gap-3"
    >
      <!-- Timeline Line -->
      <div class="flex flex-col items-center">
        <div 
          :class="[
            'w-8 h-8 rounded-full flex items-center justify-center',
            activityIconBg[activity.type]
          ]"
        >
          <component 
            :is="activityIcons[activity.type]" 
            class="w-4 h-4 text-white" 
          />
        </div>
        <div class="w-px h-full bg-neutral-200 dark:bg-neutral-700" />
      </div>
      
      <!-- Activity Content -->
      <div class="flex-1 pb-6">
        <div class="flex items-start justify-between">
          <div>
            <p class="font-medium">{{ activity.subject }}</p>
            <p class="text-sm text-neutral-500">
              {{ activity.user.name }} · {{ formatRelative(activity.created_at) }}
            </p>
          </div>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="icon" class="h-8 w-8">
                <MoreHorizontal class="w-4 h-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem @click="editActivity(activity)">Edit</DropdownMenuItem>
              <DropdownMenuItem @click="deleteActivity(activity)">Delete</DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
        
        <div v-if="activity.description" class="mt-2 text-sm text-neutral-600">
          {{ activity.description }}
        </div>
        
        <!-- Activity-specific details -->
        <div v-if="activity.type === 'call'" class="mt-2 flex items-center gap-4 text-sm">
          <span class="flex items-center gap-1">
            <Clock class="w-4 h-4" />
            {{ activity.duration_minutes }} min
          </span>
          <Badge :variant="outcomeVariant(activity.outcome)">
            {{ activity.outcome }}
          </Badge>
        </div>
      </div>
    </div>
  </div>
</template>
```

---

## Integration with Tasks

### Task-Deal Linking

```php
// When task is linked to a deal
class TaskDealService
{
    public function linkTaskToDeal(Task $task, Deal $deal): void
    {
        $task->update(['deal_id' => $deal->id]);
        
        // Log activity on deal
        Activity::create([
            'type' => 'task',
            'subject' => "Task created: {$task->title}",
            'deal_id' => $deal->id,
            'user_id' => auth()->id(),
        ]);
    }
    
    // When deal tasks are completed
    public function onTaskCompleted(Task $task): void
    {
        if ($task->deal_id) {
            $deal = $task->deal;
            
            // Check if all stage-required tasks are done
            $this->checkStageProgression($deal);
        }
    }
}
```

### CRM Dashboard Widgets

```vue
<!-- Dashboard widget for sales overview -->
<template>
  <Card>
    <CardHeader>
      <CardTitle>Sales Pipeline</CardTitle>
    </CardHeader>
    <CardContent>
      <!-- Funnel Chart -->
      <ApexChart
        type="bar"
        :options="funnelOptions"
        :series="funnelSeries"
        height="300"
      />
      
      <!-- Summary Stats -->
      <div class="grid grid-cols-3 gap-4 mt-4">
        <div class="text-center">
          <p class="text-2xl font-bold">{{ openDealsCount }}</p>
          <p class="text-sm text-neutral-500">Open Deals</p>
        </div>
        <div class="text-center">
          <p class="text-2xl font-bold text-success-600">
            {{ formatCurrency(totalPipelineValue) }}
          </p>
          <p class="text-sm text-neutral-500">Pipeline Value</p>
        </div>
        <div class="text-center">
          <p class="text-2xl font-bold text-primary-600">
            {{ formatCurrency(forecastValue) }}
          </p>
          <p class="text-sm text-neutral-500">Forecast</p>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
```

---

## Implementation Priority

### Phase 1: Foundation (Weeks 1-2)
- [ ] Database migrations
- [ ] Contact & Company CRUD
- [ ] Basic API endpoints
- [ ] Contact list & detail views

### Phase 2: Leads & Deals (Weeks 3-4)
- [ ] Lead management
- [ ] Pipeline configuration
- [ ] Deal CRUD
- [ ] Pipeline board UI

### Phase 3: Activities & Communication (Weeks 5-6)
- [ ] Activity logging
- [ ] Activity timeline
- [ ] Email integration basics
- [ ] Call logging

### Phase 4: Analytics & Automation (Weeks 7-8)
- [ ] Sales analytics
- [ ] Forecasting
- [ ] Automation rules
- [ ] Reports

---

*This specification is part of the IshYar WorkSuite CRM module.*
