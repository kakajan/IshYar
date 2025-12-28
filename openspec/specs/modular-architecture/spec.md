# Modular Architecture Specification

## Purpose

The Modular Architecture enables IshYar to be extended through plugins/modules developed by third parties, allowing the open-source community to build and share functionality while maintaining core system stability and security.

## Requirements

### Requirement: Module System Core
The system SHALL provide a robust plugin/module system that allows extending functionality without modifying core code.

#### Scenario: Module structure definition
- **WHEN** a developer creates a new module
- **THEN** follow standardized directory structure
- **AND** include module manifest (module.json)
- **AND** define dependencies and compatibility
- **AND** register service providers and routes

#### Scenario: Module discovery
- **WHEN** the system boots
- **THEN** scan registered module directories
- **AND** validate module manifests
- **AND** check version compatibility
- **AND** load enabled modules in dependency order

#### Scenario: Module isolation
- **WHEN** modules are loaded
- **THEN** isolate module namespaces
- **AND** prevent direct core file modifications
- **AND** use dependency injection for core services
- **AND** sandbox module database migrations

### Requirement: Module Lifecycle Management
The system SHALL support complete module lifecycle from installation to removal.

#### Scenario: Installing a module
- **WHEN** admin installs a module
- **THEN** validate module package integrity
- **AND** check system requirements and dependencies
- **AND** run module installer wizard if present
- **AND** execute database migrations
- **AND** publish module assets
- **AND** register module in system

#### Scenario: Enabling/Disabling module
- **WHEN** admin toggles module status
- **THEN** enable/disable without uninstalling
- **AND** preserve module data and settings
- **AND** show/hide module UI elements
- **AND** enable/disable module routes

#### Scenario: Updating a module
- **WHEN** module update is available
- **THEN** show update notification in admin
- **AND** backup current module state
- **AND** run update migrations
- **AND** clear relevant caches
- **AND** rollback on failure

#### Scenario: Uninstalling a module
- **WHEN** admin uninstalls a module
- **THEN** run module uninstaller if present
- **AND** optionally remove module data
- **AND** rollback database migrations
- **AND** remove published assets
- **AND** deregister from system

### Requirement: Module Extension Points
The system SHALL provide hooks and extension points for modules to integrate with core functionality.

#### Scenario: Registering hooks
- **WHEN** module wants to extend functionality
- **THEN** register with defined hook points
- **AND** specify priority for hook execution
- **AND** receive context data from hook caller
- **AND** return modified data or trigger actions

#### Scenario: Adding menu items
- **WHEN** module needs navigation entries
- **THEN** register menu items via manifest
- **AND** specify placement (sidebar, header, settings)
- **AND** define permission requirements
- **AND** support nested menu structures

#### Scenario: Extending API
- **WHEN** module adds API endpoints
- **THEN** register routes with module prefix
- **AND** apply authentication middleware
- **AND** document via OpenAPI annotations
- **AND** respect API versioning

#### Scenario: Adding admin pages
- **WHEN** module needs admin interface
- **THEN** register Filament resources/pages
- **AND** integrate with admin navigation
- **AND** use consistent admin UI components
- **AND** respect admin permissions

### Requirement: Module Dependencies
The system SHALL manage inter-module dependencies and version constraints.

#### Scenario: Declaring dependencies
- **WHEN** module depends on other modules
- **THEN** declare in module manifest
- **AND** specify version constraints (semver)
- **AND** distinguish required vs optional dependencies
- **AND** prevent installation if dependencies unmet

#### Scenario: Dependency resolution
- **WHEN** installing module with dependencies
- **THEN** resolve full dependency tree
- **AND** detect circular dependencies
- **AND** prompt to install missing dependencies
- **AND** respect version constraints

#### Scenario: Preventing breaking changes
- **WHEN** uninstalling a module
- **THEN** check for dependent modules
- **AND** prevent uninstall if dependents exist
- **AND** show dependency tree to admin
- **AND** offer cascade uninstall option

### Requirement: Module Permissions
The system SHALL integrate module permissions with the core RBAC system.

#### Scenario: Defining module permissions
- **WHEN** module requires custom permissions
- **THEN** register permissions in manifest
- **AND** group under module namespace
- **AND** provide permission descriptions
- **AND** set default role assignments

#### Scenario: Checking module permissions
- **WHEN** module functionality is accessed
- **THEN** verify user has required permissions
- **AND** use consistent permission gate
- **AND** integrate with Filament Shield/Policies

### Requirement: Module Configuration
The system SHALL provide configuration management for modules.

#### Scenario: Defining module settings
- **WHEN** module has configurable options
- **THEN** define settings schema
- **AND** provide default values
- **AND** support setting validation
- **AND** auto-generate settings UI in admin

#### Scenario: Accessing module settings
- **WHEN** module code needs configuration
- **THEN** access via settings service
- **AND** support environment overrides
- **AND** cache settings for performance
- **AND** emit events on setting changes

### Requirement: Module Marketplace (Future)
The system SHALL support a module marketplace for discovery and distribution.

#### Scenario: Browsing marketplace
- **WHEN** admin accesses marketplace
- **THEN** display available modules
- **AND** show ratings, downloads, compatibility
- **AND** filter by category and search
- **AND** show verified/trusted badges

#### Scenario: One-click installation
- **WHEN** admin selects marketplace module
- **THEN** download from repository
- **AND** verify package signature
- **AND** run standard installation flow
- **AND** handle payment if premium module

## Module Manifest Schema

```json
{
  "$schema": "https://ishyar.io/schemas/module-manifest.json",
  "name": "ishyar/example-module",
  "version": "1.0.0",
  "type": "module",
  "title": "Example Module",
  "description": "An example module for IshYar",
  "keywords": ["example", "demo"],
  "homepage": "https://github.com/ishyar/example-module",
  "license": "MIT",
  "authors": [
    {
      "name": "Developer Name",
      "email": "dev@example.com"
    }
  ],
  "require": {
    "ishyar/core": "^1.0",
    "ishyar/multilingual": "^1.0"
  },
  "require-dev": {},
  "autoload": {
    "psr-4": {
      "IshYar\\ExampleModule\\": "src/"
    }
  },
  "extra": {
    "ishyar": {
      "namespace": "ExampleModule",
      "provider": "IshYar\\ExampleModule\\Providers\\ModuleServiceProvider",
      "alias": "example",
      "icon": "heroicon-o-puzzle-piece",
      "color": "#3B82F6",
      "order": 100,
      "core": false,
      "default_enabled": false,
      "has_installer": true,
      "has_settings": true,
      "permissions": [
        {
          "name": "example.view",
          "title": "View Example",
          "description": "Can view example module content"
        },
        {
          "name": "example.manage",
          "title": "Manage Example",
          "description": "Can manage example module settings"
        }
      ],
      "menu": [
        {
          "title": "Example",
          "route": "example.index",
          "icon": "heroicon-o-puzzle-piece",
          "permission": "example.view",
          "order": 50
        }
      ],
      "settings": {
        "group": "modules",
        "fields": [
          {
            "name": "example_enabled",
            "type": "toggle",
            "label": "Enable Feature",
            "default": true
          }
        ]
      }
    }
  }
}
```

## Module Directory Structure

```
modules/
└── ExampleModule/
    ├── module.json              # Module manifest
    ├── composer.json            # Composer dependencies (optional)
    ├── src/
    │   ├── Providers/
    │   │   └── ModuleServiceProvider.php
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   └── Middleware/
    │   ├── Models/
    │   ├── Services/
    │   ├── Actions/
    │   ├── Filament/
    │   │   ├── Resources/
    │   │   └── Pages/
    │   ├── Events/
    │   ├── Listeners/
    │   └── Notifications/
    ├── database/
    │   ├── migrations/
    │   ├── seeders/
    │   └── factories/
    ├── config/
    │   └── module.php
    ├── routes/
    │   ├── web.php
    │   ├── api.php
    │   └── admin.php
    ├── resources/
    │   ├── views/
    │   ├── lang/
    │   │   ├── en/
    │   │   └── fa/
    │   └── assets/
    │       ├── js/
    │       └── css/
    ├── tests/
    │   ├── Unit/
    │   └── Feature/
    └── README.md
```

## API Endpoints

### GET /api/v1/admin/modules
List all modules (admin only).

**Response (200):**
```json
{
  "data": [
    {
      "type": "modules",
      "id": "multilingual",
      "attributes": {
        "name": "ishyar/multilingual",
        "title": "Multilingual",
        "description": "Multi-language support for IshYar",
        "version": "1.0.0",
        "status": "enabled",
        "is_core": true,
        "has_updates": false,
        "installed_at": "2025-01-15T10:00:00Z"
      }
    }
  ]
}
```

### POST /api/v1/admin/modules/{name}/install
Install a module.

### POST /api/v1/admin/modules/{name}/enable
Enable a module.

### POST /api/v1/admin/modules/{name}/disable
Disable a module.

### DELETE /api/v1/admin/modules/{name}
Uninstall a module.

### GET /api/v1/admin/modules/{name}/settings
Get module settings.

### PATCH /api/v1/admin/modules/{name}/settings
Update module settings.

## Data Schema

### Module
```typescript
interface Module {
  id: UUID;
  name: string;           // e.g., "ishyar/multilingual"
  namespace: string;      // e.g., "Multilingual"
  alias: string;          // e.g., "multilingual"
  version: string;
  title: string;
  description?: string;
  
  status: 'enabled' | 'disabled' | 'pending' | 'error';
  is_core: boolean;       // Core modules cannot be uninstalled
  
  path: string;           // Filesystem path
  provider: string;       // Service provider class
  
  settings: Record<string, any>;
  
  installed_at: Date;
  enabled_at?: Date;
  updated_at: Date;
  
  error_message?: string;
}

interface ModuleDependency {
  module_id: UUID;
  depends_on: string;     // Module name
  version_constraint: string;
  is_optional: boolean;
}
```

## Hook System

### Available Core Hooks
| Hook | Description | Parameters |
|------|-------------|------------|
| `task.creating` | Before task creation | Task data |
| `task.created` | After task creation | Task model |
| `task.updating` | Before task update | Task, changes |
| `task.completed` | Task marked complete | Task model |
| `task.approved` | Task approved | Task, approver |
| `user.creating` | Before user creation | User data |
| `user.created` | After user creation | User model |
| `notification.sending` | Before notification | Notification |
| `dashboard.widgets` | Dashboard rendering | Widget collection |
| `menu.building` | Menu construction | Menu builder |
| `settings.saving` | Before settings save | Settings data |

### Hook Registration Example
```php
// In ModuleServiceProvider
public function boot(): void
{
    Hook::register('task.created', function (Task $task) {
        // Custom logic when task is created
        $this->myService->handleNewTask($task);
    }, priority: 10);
    
    Hook::register('dashboard.widgets', function (WidgetCollection $widgets) {
        $widgets->add(MyCustomWidget::class, order: 50);
        return $widgets;
    });
}
```
