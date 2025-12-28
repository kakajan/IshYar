# OpenSpec Instructions

Instructions for AI coding assistants using OpenSpec for IshYar development.

## TL;DR Quick Checklist

- Search existing work: Review `openspec/project.md`, explore specs with `ls openspec/specs/`
- Decide scope: new capability vs modify existing capability
- Pick a unique `change-id`: kebab-case, verb-led (`add-`, `update-`, `remove-`, `refactor-`)
- Scaffold: `proposal.md`, `tasks.md`, `design.md` (only if needed), and delta specs per affected capability
- Write deltas: use `## ADDED|MODIFIED|REMOVED Requirements`; include at least one `#### Scenario:` per requirement
- Request approval: Do not start implementation until proposal is approved

## Project Context

IshYar is an enterprise ERP and Task Management system with:
- **Backend**: Laravel 12+ (PHP 8.3+), Filament 4 Admin
- **Frontend**: Nuxt 4, Vue.js 3 (Composition API), Tailwind CSS 4.0
- **Components**: [Shadcn Vue](https://www.shadcn-vue.com/) (built on Radix Vue/Reka UI)
  - ⚠️ **IMPORTANT**: We use **Shadcn Vue**, NOT Nuxt UI. All new UI implementations MUST use Shadcn Vue components.
- **Architecture**: PWA, Mobile-first, API-first

See `openspec/project.md` for full technical stack and conventions.

## Three-Stage Workflow

### Stage 1: Creating Changes

Create proposal when you need to:
- Add features or functionality
- Modify existing behavior
- Fix bugs that require spec changes
- Refactor with behavioral impact

Decision tree:
```
├─ New capability? → Create proposal
├─ Breaking change? → Create proposal
├─ Architecture change? → Create proposal
└─ Unclear? → Create proposal (safer)
```

### Stage 2: Implementing Changes

1. Work through tasks in order
2. Reference specs while coding
3. Mark tasks complete as you go
4. Run tests to validate

### Stage 3: Archiving Changes

After all tasks complete:
1. Verify implementation matches specs
2. Archive change to merge deltas into source specs

## File Templates

### proposal.md
```markdown
# Change: [Brief description of change]

## Why
[1-2 sentences on problem/opportunity]

## What Changes
- [Bullet list of changes]
- [Mark breaking changes with **BREAKING**]

## Impact
- Affected specs: [list capabilities]
- Affected code: [key files/systems]
```

### tasks.md
```markdown
## 1. [Task Group Name]

- [ ] 1.1 [Task description]
- [ ] 1.2 [Task description]

## 2. [Task Group Name]

- [ ] 2.1 [Task description]
- [ ] 2.2 [Task description]
```

### Spec Delta (in `changes/{name}/specs/{capability}/spec.md`)
```markdown
## ADDED Requirements

### Requirement: [Requirement Name]
The system SHALL [describe behavior]...

#### Scenario: [Scenario name]
- **WHEN** [condition]
- **THEN** [expected outcome]
- **AND** [additional outcome]

## MODIFIED Requirements

### Requirement: [Existing Requirement Name]
[Complete updated requirement text]

#### Scenario: [Updated scenario]
- **WHEN** [condition]
- **THEN** [expected outcome]
```

## Spec File Format

### Critical: Scenario Formatting

**CORRECT** (use #### headers):
```markdown
#### Scenario: User login success
- **WHEN** valid credentials provided
- **THEN** return JWT token
```

**WRONG** (don't use bullets or bold for scenario headers):
```markdown
- **Scenario: User login**  ❌
**Scenario**: User login     ❌
### Scenario: User login      ❌
```

Every requirement MUST have at least one scenario.

### Requirement Wording
- Use SHALL/MUST for normative requirements
- Avoid should/may unless intentionally non-normative

### Delta Operations
- `## ADDED Requirements` - New capabilities
- `## MODIFIED Requirements` - Changed behavior (include complete text)
- `## REMOVED Requirements` - Deprecated features
- `## RENAMED Requirements` - Name changes (include old → new)

## IshYar Specific Guidelines

### API Conventions
- All APIs are RESTful following JSON:API specification
- Versioning: `/api/v1/`
- Authentication: JWT Bearer tokens
- Error format: RFC 7807 Problem Details

### Code Organization

**Laravel (Backend)**:
```
app/
├── Actions/           # Single-purpose action classes
├── Services/          # Business logic services
├── Http/
│   ├── Controllers/   # Thin controllers
│   └── Resources/     # JSON:API resources
├── Models/            # Eloquent models
└── Notifications/     # Multi-channel notifications
```

**Nuxt (Frontend)**:
```
├── components/
│   ├── ui/            # Shadcn components
│   ├── features/      # Feature-specific
│   └── layout/        # Layout components
├── composables/       # Vue composables
├── stores/            # Pinia stores
├── pages/             # File-based routing
└── server/            # API routes
```

### Component Naming
- Use PascalCase for components
- Prefix with feature name: `TaskCard`, `TaskList`, `OrgChartNode`
- UI components: `UiButton`, `UiCard`, `UiModal`

### Testing Requirements
- Backend: PHPUnit for unit, Pest for feature tests
- Frontend: Vitest for unit, Playwright for E2E
- Minimum 80% coverage for new code

## Existing Specs Reference

Current specifications in `openspec/specs/`:

| Spec | Purpose |
|------|---------|
| `auth` | Authentication, authorization, SSO |
| `users` | User/employee management, skills |
| `organization` | Org hierarchy, departments, positions |
| `tasks` | Task engine, routine/situational tasks |
| `notifications` | Multi-channel notification delivery |
| `analytics` | Dashboards, KPIs, reporting |
| `integrations` | n8n, Slack, Teams, webhooks |
| `ui-design-system` | Design tokens, components, animations |
| `advanced-features` | AI, gamification, time tracking |
| `pwa` | PWA installation, offline, service worker |

## Common Troubleshooting

**"Requirement must have at least one scenario"**
- Check scenarios use `#### Scenario:` format (4 hashtags)
- Don't use bullet points or bold for scenario headers

**"Missing Purpose section"**
- Every spec needs `## Purpose` at the top
- Add 1-2 sentences describing the module

**Silent scenario parsing failures**
- Exact format required: `#### Scenario: Name`
- Check for typos in header format
