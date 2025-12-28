# IshYar - Enterprise WorkSuite

## Project Overview

**IshYar** (Turkmen: "Work") is a next-generation Enterprise Resource Planning (ERP) and Task Management platform designed with an Apple-inspired aesthetic, built for organizations seeking elegant simplicity without sacrificing enterprise-grade functionality.

## Vision Statement

To create the world's most intuitive and visually stunning enterprise work management platform that reduces complexity while maximizing productivity through intelligent automation, visual-first design, and seamless multi-channel communication.

## Technical Stack

### Backend
- **Framework**: Laravel 12+ (PHP 8.3+)
- **Architecture**: API-First, Service Layer Pattern, Action Classes
- **Admin Panel**: Filament 4
- **Database**: PostgreSQL 16+ (primary), Redis 7+ (caching/queues)
- **Search**: Meilisearch (full-text search)
- **Queue System**: Laravel Horizon with Redis

### Frontend
- **Framework**: Nuxt 4 (SPA Mode - Experimental/Latest)
- **UI Library**: Vue.js 3 (Composition API)
- **Styling**: Tailwind CSS 4.0
- **Component Library**: [Shadcn Vue](https://www.shadcn-vue.com/) (built on Radix Vue/Reka UI)
  - ⚠️ **Note**: We use **Shadcn Vue**, NOT Nuxt UI. All new UI implementations should use Shadcn Vue components.
- **State Management**: Pinia
- **Animations**: CSS Transitions, Vue Transitions
- **Charts & Data Visualization**: ApexCharts (vue3-apexcharts)
- **Visual Components**: Pure Vue + Tailwind CSS (Card-based layouts)

### Architecture Patterns
- **Application Type**: Single Page Application (SPA) with PWA capabilities (fully installable)
- **Rendering Strategy**: Client-Side Rendering (optimized for authenticated-only application)
- **Design Philosophy**: Mobile-first responsive
- **API Design**: RESTful + Real-time WebSockets
- **Authentication**: JWT + OAuth2 (SSO support)

### Notification Channels
- Web Push (FCM/VAPID)
- SMS Gateway Integration
- Telegram Bot API
- Email (SMTP/Mailgun/Postmark)
- In-App Real-time Notifications

### External Integrations
- n8n (Workflow Automation)
- Slack/Microsoft Teams
- Google Workspace / Microsoft 365
- Zapier Webhooks
- Custom API Webhooks

## Design Philosophy

### Apple Aesthetic Principles
1. **Generous Whitespace**: Breathing room between elements
2. **Glassmorphism**: Subtle backdrop blurs, frosted glass effects
3. **Typography**: Inter/SF Pro with refined hierarchies
4. **Micro-interactions**: Purposeful, delightful animations
5. **Visual Density Control**: Progressive disclosure of complexity

### UI/UX Guidelines
- **3-Click Rule**: Every action reachable within 3 clicks
- **Visual-First**: Card-based layouts, intuitive visual hierarchy, interactive components
- **Simple & Clean**: Pure Vue + Tailwind components - no complex external visualization libraries
- **Focus Mode**: Distraction-free employee interface
- **Accessibility**: WCAG 2.1 AA compliance

## Core Business Domains

### 1. Organization Management
- Dynamic organizational hierarchy (tree structure)
- Department management
- Role-based access control
- Job description templates

### 2. Personnel Management
- Employee profiles with photos
- Skill matrices and competencies
- Performance history
- Career progression tracking

### 3. Task Engine
- **Routine Tasks**: Automated recurring (Daily/Weekly/Monthly)
- **Situational Tasks**: Ad-hoc one-time assignments
- Priority and deadline management
- Progress tracking with visual indicators
- Dependency management

### 4. Approval Workflows
- Request-Verification loop
- Multi-level approval chains
- Digital signatures (optional)
- Audit trail with timestamps

### 5. Analytics & Reporting
- Real-time productivity heatmaps
- Department-level KPIs
- Individual performance metrics
- Exportable reports (PDF/Excel)

## Project Conventions

### Code Style
- **PHP**: PSR-12, Laravel conventions
- **TypeScript**: ESLint + Prettier (Nuxt defaults)
- **Vue**: Composition API, `<script setup>` syntax
- **CSS**: Tailwind utility-first, minimal custom CSS

### Git Workflow
- **Branching**: Git Flow (main, develop, feature/*, release/*, hotfix/*)
- **Commits**: Conventional Commits (feat, fix, chore, docs, refactor)
- **PRs**: Require 1 approval, CI must pass

### API Conventions
- **Versioning**: URL-based (/api/v1/)
- **Response Format**: JSON:API specification
- **Error Handling**: RFC 7807 Problem Details
- **Rate Limiting**: Token bucket algorithm

### Testing Strategy
- **Backend**: PHPUnit (unit), Pest (feature), 80%+ coverage
- **Frontend**: Vitest (unit), Playwright (E2E)
- **API**: Postman collections, contract testing

## Milestones

### Phase 1: Foundation (Weeks 1-4)
- Project scaffolding (Laravel + Nuxt)
- Authentication system
- Base UI components
- Database schema

### Phase 2: Core Features (Weeks 5-10)
- Organization hierarchy
- User management
- Task engine (basic)
- Notification system

### Phase 3: Advanced Features (Weeks 11-16)
- Approval workflows
- Analytics dashboard
- n8n integration
- PWA implementation

### Phase 4: Polish & Launch (Weeks 17-20)
- Performance optimization
- Security audit
- Documentation
- Deployment automation

## Modular Architecture

### Plugin System
IshYar is built with extensibility at its core. Third-party developers can create modules/plugins that integrate seamlessly with the main application.

#### Module Structure
```
modules/
├── {ModuleName}/
│   ├── module.json          # Manifest file
│   ├── src/
│   │   ├── Providers/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/migrations/
│   ├── config/
│   ├── routes/
│   ├── resources/
│   │   ├── views/
│   │   └── lang/
│   └── tests/
```

#### Module Lifecycle
1. **Discovery**: Manifest scanning
2. **Installation**: Migrations, config publishing
3. **Activation**: Service provider registration
4. **Runtime**: Hooks, events, API extensions

### Default Modules
The following modules are pre-installed with IshYar:

#### Multilingual Module (Built-in Feature)
- **Status**: Core feature using Spatie Translatable package
- **Languages**: English (default), Persian/Farsi (pre-configured)
- **Implementation**: 
  - Backend: Spatie Translatable with JSON column storage
  - Frontend: vue-i18n with dynamic translation loading
  - Models: Organization, Department, Position, Task, RoutineTemplate
- **Features**: 
  - RTL/LTR automatic switching
  - API accepts both plain strings and `{en, fa}` objects
  - Automatic locale detection via headers/query params
  - Translations returned based on user's language preference
- **Request/Response**: 
  - Request: `Accept-Language: fa` or `?lang=fa`
  - Response: Returns translated fields based on locale
  - Validation: `TranslatableValue` rule for all translatable inputs
- **Database**: JSON columns (e.g., `title: {"en": "Task", "fa": "وظیفه"}`)

#### Jalali Date Module
- **Features**: Persian/Jalali (Solar Hijri) calendar support
- **Components**: Date pickers, date range selectors, formatted date display
- **Options**: Persian numerals, holiday highlighting, dual calendar display
- **Integration**: Filament date fields, Vue components, Laravel validation rules
- **Default**: Enabled by default, can be disabled per organization/user

#### Currencies Module
- **Features**: Multi-currency support, real-time exchange rates
- **Providers**: ExchangeRate-API, Open Exchange Rates, Fixer.io, manual entry
- **Special**: Iranian Rial/Toman conversion, Persian numeral display
- **Integration**: Money formatting, currency conversion API

## Installation System

### Application Installer
IshYar includes a guided web-based installer for easy deployment:

1. **System Requirements**: PHP 8.3+, required extensions
2. **Environment Setup**: Database, cache, queue configuration
3. **Database Migration**: Schema creation, seeding
4. **Organization Setup**: Company profile, timezone
5. **Admin Account**: Super admin creation
6. **Initial Configuration**: Notification channels, modules

### CLI Installation
```bash
# Interactive
php artisan ishyar:install

# Silent (automated/Docker)
php artisan ishyar:install --config=install.json --no-interaction
```

### Module Installer
Each module can define its own installation wizard accessible from the Filament admin panel, with:
- Pre-flight requirements checking
- Configuration forms
- Database migrations
- Post-install setup

## Localization & i18n

### Supported Languages
| Code | Language | Direction | Calendar |
|------|----------|-----------|----------|
| en   | English  | LTR       | Gregorian |
| fa   | Persian  | RTL       | Jalali   |

### Calendar Systems
IshYar supports multiple calendar systems through the Jalali Date Module:
- **Gregorian**: Default for English and most languages
- **Jalali (Solar Hijri)**: Persian calendar for Farsi locale
- **Auto Detection**: Automatically selects calendar based on user locale
- **Dual Display**: Option to show Gregorian alongside Jalali dates

### Translation Architecture
- **Backend**: Laravel trans() with JSON/PHP files
- **Frontend**: vue-i18n with lazy loading per locale
- **Admin**: Filament-based translation management
- **Format**: ICU MessageFormat for pluralization

## Team Roles (Suggested)

- **Project Manager**: Overall coordination
- **Backend Lead**: Laravel architecture
- **Frontend Lead**: Nuxt/Vue development
- **UI/UX Designer**: Design system, prototypes
- **DevOps Engineer**: CI/CD, infrastructure
- **QA Engineer**: Testing strategy, automation

## Specification Documents

| Specification | Description |
|--------------|-------------|
| [Auth](specs/auth/spec.md) | Authentication, 2FA, SSO, RBAC |
| [Users](specs/users/spec.md) | User/Employee management, skills |
| [Organization](specs/organization/spec.md) | Hierarchy tree, departments |
| [Tasks](specs/tasks/spec.md) | Routine & Situational tasks |
| [Notifications](specs/notifications/spec.md) | Multi-channel delivery |
| [Analytics](specs/analytics/spec.md) | Dashboards, KPIs, reports |
| [Integrations](specs/integrations/spec.md) | n8n, Slack, Teams, webhooks |
| [UI Design System](specs/ui-design-system/spec.md) | Design tokens, components |
| [Advanced Features](specs/advanced-features/spec.md) | AI, gamification, time tracking |
| [PWA](specs/pwa/spec.md) | Offline, service workers |
| [Modular Architecture](specs/modular-architecture/spec.md) | Plugin system |
| [Multilingual Module](specs/multilingual-module/spec.md) | i18n, RTL support |
| [Jalali Date Module](specs/jalali-date-module/spec.md) | Persian calendar, date pickers |
| [Currencies Module](specs/currencies-module/spec.md) | Multi-currency, exchange rates |
| [Installer](specs/installer/spec.md) | App & module installation |
