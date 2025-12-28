# Installer System Specification

## Purpose

The Installer System provides a guided, user-friendly installation experience for IshYar, including the Laravel application installer and individual module installers accessible from the admin panel.

## Requirements

### Requirement: Application Installer
The system SHALL provide a web-based installation wizard for initial Laravel setup.

#### Scenario: Accessing installer
- **WHEN** user accesses IshYar for the first time
- **THEN** detect missing .env or database configuration
- **AND** redirect to installation wizard
- **AND** prevent access to main application until complete
- **AND** display welcome screen with requirements

#### Scenario: Step 1 - System requirements check
- **WHEN** installer checks system requirements
- **THEN** verify PHP version (8.3+)
- **AND** check required PHP extensions (pdo, mbstring, openssl, json, curl, gd, zip, intl)
- **AND** verify directory permissions (storage, bootstrap/cache)
- **AND** check optional extensions (redis, imagick)
- **AND** display pass/fail for each requirement
- **AND** block progression if critical requirements fail

#### Scenario: Step 2 - Environment configuration
- **WHEN** user configures environment
- **THEN** set application name and URL
- **AND** configure database connection (MySQL/PostgreSQL)
- **AND** test database connection
- **AND** configure cache driver (file/redis)
- **AND** configure queue driver (sync/redis/database)
- **AND** configure mail settings (optional)
- **AND** generate APP_KEY automatically

#### Scenario: Step 3 - Database setup
- **WHEN** database configuration is confirmed
- **THEN** run all migrations
- **AND** show migration progress
- **AND** seed default data (languages, settings)
- **AND** rollback on failure with clear error message

#### Scenario: Step 4 - Organization setup
- **WHEN** database is ready
- **THEN** create organization/company profile
- **AND** set organization name and details
- **AND** configure timezone and locale
- **AND** set default language (en)

#### Scenario: Step 5 - Admin account creation
- **WHEN** organization is created
- **THEN** create super admin account
- **AND** set name, email, password
- **AND** validate password strength
- **AND** send welcome email (if mail configured)
- **AND** assign all permissions

#### Scenario: Step 6 - Initial configuration
- **WHEN** admin account is created
- **THEN** configure notification channels
- **AND** set application settings
- **AND** optionally enable default modules
- **AND** optionally import sample data

#### Scenario: Step 7 - Installation complete
- **WHEN** installation finishes successfully
- **THEN** create installation lock file
- **AND** display success message with credentials
- **AND** provide link to admin panel
- **AND** offer to download installation log
- **AND** redirect to login page

#### Scenario: Installation lock
- **WHEN** installation is complete
- **THEN** create `storage/installed` lock file
- **AND** prevent re-running installer
- **AND** provide admin command to re-enable installer

### Requirement: Module Installer Framework
The system SHALL provide a standardized installer framework for modules.

#### Scenario: Module defines installer
- **WHEN** module has installation requirements
- **THEN** implement InstallerInterface
- **AND** define installation steps
- **AND** define configuration fields
- **AND** define post-install actions

#### Scenario: Running module installer
- **WHEN** admin installs module with installer
- **THEN** display module installer wizard
- **AND** show module-specific configuration
- **AND** validate inputs
- **AND** run installation steps
- **AND** show progress and results

#### Scenario: Module pre-flight checks
- **WHEN** module installation starts
- **THEN** check module dependencies
- **AND** verify required permissions
- **AND** check disk space for assets
- **AND** validate configuration prerequisites

### Requirement: Installer UI/UX
The system SHALL provide a visually appealing, step-by-step installation interface.

#### Scenario: Installer appearance
- **WHEN** installer is displayed
- **THEN** use IshYar branding
- **AND** display step progress indicator
- **AND** show current step clearly
- **AND** provide back/next navigation
- **AND** disable progression until step valid

#### Scenario: Error handling
- **WHEN** installation step fails
- **THEN** display clear error message
- **AND** provide troubleshooting suggestions
- **AND** allow retry without losing progress
- **AND** offer rollback option where safe

#### Scenario: Progress indication
- **WHEN** long operation runs (migrations, seeding)
- **THEN** show progress bar
- **AND** display current action
- **AND** estimate remaining time
- **AND** prevent timeout issues

### Requirement: CLI Installation
The system SHALL support command-line installation for automated deployments.

#### Scenario: CLI install command
- **WHEN** admin runs `php artisan ishyar:install`
- **THEN** prompt for required configuration
- **AND** accept parameters for automation
- **AND** run same installation steps as web
- **AND** output progress to console

#### Scenario: Silent installation
- **WHEN** all parameters provided via command/file
- **THEN** run installation without prompts
- **AND** validate all inputs
- **AND** fail gracefully with clear errors
- **AND** suitable for Docker/CI environments

#### Scenario: Environment file generation
- **WHEN** running CLI install
- **THEN** generate .env from .env.example
- **AND** fill with provided values
- **AND** generate secure APP_KEY
- **AND** preserve existing values if specified

### Requirement: Upgrade System
The system SHALL support smooth upgrades between versions.

#### Scenario: Version check
- **WHEN** admin accesses update section
- **THEN** check for available updates
- **AND** display current vs available version
- **AND** show changelog highlights
- **AND** indicate breaking changes

#### Scenario: Running upgrade
- **WHEN** admin initiates upgrade
- **THEN** backup current state (optional)
- **AND** enter maintenance mode
- **AND** run new migrations
- **AND** update module versions
- **AND** clear caches
- **AND** exit maintenance mode
- **AND** verify successful upgrade

### Requirement: Installation Health Check
The system SHALL provide ongoing installation health monitoring.

#### Scenario: Health check command
- **WHEN** admin runs `php artisan ishyar:health`
- **THEN** check database connectivity
- **AND** verify cache functionality
- **AND** check queue worker status
- **AND** verify file permissions
- **AND** validate module integrity
- **AND** report issues with fix suggestions

#### Scenario: Admin health dashboard
- **WHEN** admin views system health
- **THEN** display status indicators
- **AND** show recent errors
- **AND** monitor queue backlog
- **AND** check disk space
- **AND** verify external service connections

## API Endpoints

### GET /install
Display installer wizard (only when not installed).

### POST /install/requirements
Check system requirements.

**Response (200):**
```json
{
  "data": {
    "passed": true,
    "requirements": {
      "php_version": {
        "required": "8.3.0",
        "current": "8.3.14",
        "passed": true
      },
      "extensions": {
        "pdo": { "required": true, "installed": true, "passed": true },
        "mbstring": { "required": true, "installed": true, "passed": true },
        "openssl": { "required": true, "installed": true, "passed": true },
        "json": { "required": true, "installed": true, "passed": true },
        "curl": { "required": true, "installed": true, "passed": true },
        "gd": { "required": true, "installed": true, "passed": true },
        "intl": { "required": true, "installed": true, "passed": true },
        "redis": { "required": false, "installed": true, "passed": true }
      },
      "directories": {
        "storage": { "writable": true, "passed": true },
        "bootstrap/cache": { "writable": true, "passed": true }
      }
    }
  }
}
```

### POST /install/database
Test and configure database connection.

**Request:**
```json
{
  "driver": "pgsql",
  "host": "localhost",
  "port": 5432,
  "database": "ishyar",
  "username": "ishyar_user",
  "password": "secure_password"
}
```

### POST /install/environment
Save environment configuration.

### POST /install/migrate
Run database migrations.

### POST /install/organization
Create organization.

**Request:**
```json
{
  "name": "TechCorp Holdings",
  "timezone": "Asia/Tehran",
  "default_language": "en"
}
```

### POST /install/admin
Create admin account.

**Request:**
```json
{
  "name": "Admin User",
  "email": "admin@company.com",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!"
}
```

### POST /install/finalize
Complete installation.

### GET /api/v1/admin/system/health
Get system health status.

### POST /api/v1/admin/system/upgrade/check
Check for available upgrades.

### POST /api/v1/admin/system/upgrade/run
Run system upgrade.

### GET /api/v1/admin/modules/{name}/installer
Get module installer configuration.

### POST /api/v1/admin/modules/{name}/installer
Run module installer.

## Installation Steps Configuration

```php
// config/installer.php
return [
    'steps' => [
        'requirements' => [
            'title' => 'System Requirements',
            'description' => 'Checking server requirements',
            'view' => 'installer.requirements',
        ],
        'environment' => [
            'title' => 'Environment Setup',
            'description' => 'Configure application environment',
            'view' => 'installer.environment',
        ],
        'database' => [
            'title' => 'Database Setup',
            'description' => 'Configure and migrate database',
            'view' => 'installer.database',
        ],
        'organization' => [
            'title' => 'Organization',
            'description' => 'Set up your organization',
            'view' => 'installer.organization',
        ],
        'admin' => [
            'title' => 'Admin Account',
            'description' => 'Create administrator account',
            'view' => 'installer.admin',
        ],
        'configuration' => [
            'title' => 'Configuration',
            'description' => 'Initial application settings',
            'view' => 'installer.configuration',
        ],
        'finish' => [
            'title' => 'Complete',
            'description' => 'Installation complete',
            'view' => 'installer.finish',
        ],
    ],
    
    'requirements' => [
        'php' => '8.3.0',
        'extensions' => [
            'required' => ['pdo', 'mbstring', 'openssl', 'json', 'curl', 'gd', 'zip', 'intl', 'bcmath'],
            'optional' => ['redis', 'imagick', 'pcntl'],
        ],
        'directories' => [
            'storage/app' => '0775',
            'storage/framework' => '0775',
            'storage/logs' => '0775',
            'bootstrap/cache' => '0775',
        ],
    ],
    
    'defaults' => [
        'language' => 'en',
        'timezone' => 'UTC',
        'cache_driver' => 'file',
        'queue_driver' => 'sync',
        'session_driver' => 'file',
    ],
];
```

## Module Installer Interface

```php
<?php

namespace IshYar\Core\Contracts;

interface ModuleInstallerInterface
{
    /**
     * Get installer steps configuration.
     */
    public function getSteps(): array;
    
    /**
     * Get pre-flight checks.
     */
    public function getRequirements(): array;
    
    /**
     * Run pre-installation checks.
     */
    public function checkRequirements(): InstallerCheckResult;
    
    /**
     * Run installation with given configuration.
     */
    public function install(array $config): InstallerResult;
    
    /**
     * Run uninstallation.
     */
    public function uninstall(bool $removeData = false): InstallerResult;
    
    /**
     * Get configuration schema for installer form.
     */
    public function getConfigSchema(): array;
}
```

## Example Module Installer

```php
<?php

namespace IshYar\Multilingual\Installer;

use IshYar\Core\Contracts\ModuleInstallerInterface;

class MultilingualInstaller implements ModuleInstallerInterface
{
    public function getSteps(): array
    {
        return [
            [
                'id' => 'languages',
                'title' => 'Language Configuration',
                'description' => 'Configure initial languages',
            ],
            [
                'id' => 'import',
                'title' => 'Import Translations',
                'description' => 'Import base translation files',
            ],
            [
                'id' => 'complete',
                'title' => 'Complete',
                'description' => 'Module installed successfully',
            ],
        ];
    }
    
    public function getConfigSchema(): array
    {
        return [
            'default_language' => [
                'type' => 'select',
                'label' => 'Default Language',
                'options' => [
                    'en' => 'English',
                    'fa' => 'فارسی (Persian)',
                ],
                'default' => 'en',
                'required' => true,
            ],
            'enable_persian' => [
                'type' => 'toggle',
                'label' => 'Enable Persian/Farsi',
                'default' => true,
            ],
            'auto_detect' => [
                'type' => 'toggle',
                'label' => 'Auto-detect browser language',
                'default' => true,
            ],
        ];
    }
    
    public function install(array $config): InstallerResult
    {
        // 1. Run migrations
        Artisan::call('migrate', [
            '--path' => 'modules/Multilingual/database/migrations',
            '--force' => true,
        ]);
        
        // 2. Seed languages
        $this->seedLanguages($config);
        
        // 3. Import translations
        $this->importTranslations();
        
        // 4. Configure settings
        $this->saveSettings($config);
        
        return InstallerResult::success('Multilingual module installed successfully');
    }
}
```

## Data Schema

### InstallationLog
```typescript
interface InstallationLog {
  id: UUID;
  type: 'application' | 'module' | 'upgrade';
  target: string;          // 'ishyar' or module name
  version: string;
  status: 'started' | 'completed' | 'failed' | 'rolled_back';
  
  steps_completed: string[];
  current_step?: string;
  
  config: Record<string, any>;  // Sanitized (no passwords)
  
  error_message?: string;
  error_trace?: string;
  
  started_at: Date;
  completed_at?: Date;
  
  performed_by?: UUID;      // Admin user ID
  ip_address?: string;
}

interface SystemHealth {
  overall_status: 'healthy' | 'degraded' | 'critical';
  
  checks: {
    database: HealthCheck;
    cache: HealthCheck;
    queue: HealthCheck;
    storage: HealthCheck;
    mail: HealthCheck;
    modules: HealthCheck;
  };
  
  metrics: {
    disk_usage: number;      // percentage
    queue_size: number;
    failed_jobs: number;
    cache_hit_rate: number;
  };
  
  checked_at: Date;
}

interface HealthCheck {
  status: 'pass' | 'fail' | 'warn';
  message?: string;
  details?: Record<string, any>;
  last_checked: Date;
}
```

## CLI Commands

### Application Installation
```bash
# Interactive installation
php artisan ishyar:install

# Silent installation with parameters
php artisan ishyar:install \
  --db-driver=pgsql \
  --db-host=localhost \
  --db-port=5432 \
  --db-database=ishyar \
  --db-username=ishyar \
  --db-password=secret \
  --admin-name="Admin User" \
  --admin-email=admin@example.com \
  --admin-password=SecurePass123! \
  --org-name="My Company" \
  --timezone=UTC \
  --no-interaction

# Using configuration file
php artisan ishyar:install --config=install.json
```

### Health Check
```bash
# Run health check
php artisan ishyar:health

# Output format options
php artisan ishyar:health --format=json

# Specific checks only
php artisan ishyar:health --check=database,cache,queue
```

### Module Management
```bash
# Install module
php artisan module:install multilingual

# Enable/disable module
php artisan module:enable multilingual
php artisan module:disable multilingual

# Uninstall module
php artisan module:uninstall multilingual --remove-data

# List modules
php artisan module:list
```

### Upgrade
```bash
# Check for updates
php artisan ishyar:upgrade --check

# Run upgrade
php artisan ishyar:upgrade --backup --force
```
