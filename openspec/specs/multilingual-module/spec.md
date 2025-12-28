# Multilingual Module Specification

## Purpose

The Multilingual Module is a core/default module that provides comprehensive internationalization (i18n) and localization (l10n) support for IshYar, enabling the entire application to be translated into multiple languages with an admin interface for managing translations.

## Requirements

### Requirement: Multi-Language Support
The system SHALL support multiple languages with complete UI and content translation.

#### Scenario: Defining supported languages
- **WHEN** admin configures languages
- **THEN** enable/disable languages
- **AND** set default language (English by default)
- **AND** set fallback language chain
- **AND** configure RTL/LTR per language

#### Scenario: Initial language setup
- **WHEN** IshYar is installed
- **THEN** English (en) is default and active
- **AND** Farsi/Persian (fa) is pre-installed
- **AND** English is set as default language
- **AND** translations for both languages are included

#### Scenario: Adding new language
- **WHEN** admin adds a new language
- **THEN** specify language code (ISO 639-1)
- **AND** specify locale code (e.g., en_US)
- **AND** set language name (native and English)
- **AND** configure text direction (LTR/RTL)
- **AND** optionally import base translations

### Requirement: Translation Management
The system SHALL provide admin interface for managing all application translations.

#### Scenario: Viewing translations
- **WHEN** admin accesses translation manager
- **THEN** display all translation keys grouped by category
- **AND** show translations for each enabled language
- **AND** highlight missing translations
- **AND** support search and filtering

#### Scenario: Editing translations
- **WHEN** admin edits a translation
- **THEN** provide inline editing interface
- **AND** support rich text for HTML translations
- **AND** show placeholder/variable indicators
- **AND** validate translation format
- **AND** save to database (override file-based)

#### Scenario: Adding custom translations
- **WHEN** admin adds new translation key
- **THEN** define key with namespace
- **AND** add translations for each language
- **AND** categorize the translation
- **AND** mark as custom (not core)

#### Scenario: Importing translations
- **WHEN** admin imports translation file
- **THEN** support JSON, PHP array, CSV formats
- **AND** merge with existing translations
- **AND** option to overwrite or skip existing
- **AND** validate import file structure

#### Scenario: Exporting translations
- **WHEN** admin exports translations
- **THEN** export selected languages
- **AND** export selected categories
- **AND** support JSON, PHP, CSV formats
- **AND** include metadata (version, date)

### Requirement: User Language Preference
The system SHALL respect user language preferences.

#### Scenario: Setting user language
- **WHEN** user changes language preference
- **THEN** store preference in user profile
- **AND** immediately switch interface language
- **AND** persist across sessions
- **AND** apply to all UI and notifications

#### Scenario: Language detection
- **WHEN** anonymous user visits application
- **THEN** detect browser language preference
- **AND** fall back to default if unsupported
- **AND** store preference in session/cookie

#### Scenario: Language switching
- **WHEN** user switches language via UI
- **THEN** update current session immediately
- **AND** if logged in, save to profile
- **AND** redirect to current page with new locale
- **AND** preserve page state where possible

### Requirement: Content Translation
The system SHALL support translation of user-generated content using Spatie Translatable.

#### Scenario: Translating task content
- **WHEN** task is created with translatable fields
- **THEN** store content as JSON with language keys: `{"en": "value", "fa": "قیمت"}`
- **AND** allow adding translations for other languages
- **AND** display in user's preferred language using `$task->translate('title', 'fa')`
- **AND** fall back to default language if translation missing
- **AND** support both plain strings and locale objects in API

#### Scenario: Translating model content
- **WHEN** model uses `HasTranslations` trait
- **THEN** mark fields as translatable in `$translatable` array
- **AND** store translations as JSON in single column
- **AND** automatically return translated value based on `app()->getLocale()`
- **AND** accept both string and `{en, fa}` object in API create/update

#### Scenario: API request with translations
- **WHEN** API receives translatable field value
- **THEN** accept plain string (stored in default locale)
- **AND** accept object with locale keys: `{"en": "Task", "fa": "وظیفه"}`
- **AND** validate using `TranslatableValue` rule
- **AND** automatically cast and store via Spatie Translatable

#### Scenario: Translating notifications
- **WHEN** notification is sent
- **THEN** generate in recipient's preferred language
- **AND** use translated templates
- **AND** translate dynamic content where possible

#### Scenario: Translating email templates
- **WHEN** email template is edited
- **THEN** provide version per language
- **AND** use recipient language for sending
- **AND** fall back to default language template

### Requirement: RTL (Right-to-Left) Support
The system SHALL fully support RTL languages like Farsi, Arabic, Hebrew.

#### Scenario: RTL layout switching
- **WHEN** RTL language is active
- **THEN** flip entire UI layout
- **AND** adjust CSS direction properties
- **AND** flip icons where appropriate
- **AND** maintain proper text alignment

#### Scenario: Mixed content handling
- **WHEN** content contains both RTL and LTR text
- **THEN** apply proper bidirectional text rules
- **AND** use Unicode bidi marks where needed
- **AND** preserve number formatting

### Requirement: Pluralization and Formatting
The system SHALL support language-specific pluralization and formatting.

#### Scenario: Plural translations
- **WHEN** translating countable items
- **THEN** support language-specific plural rules
- **AND** handle zero, one, few, many, other forms
- **AND** use ICU MessageFormat syntax

#### Scenario: Date/Time formatting
- **WHEN** displaying dates and times
- **THEN** format according to locale
- **AND** support calendar systems (Gregorian, Persian/Jalali)
- **AND** respect timezone with locale display

#### Scenario: Number formatting
- **WHEN** displaying numbers
- **THEN** use locale-specific separators
- **AND** format currency per locale
- **AND** support percentage formatting

### Requirement: Module Installer
The Multilingual module SHALL have an admin-accessible installer.

#### Scenario: First-time installation
- **WHEN** module is installed
- **THEN** run database migrations
- **AND** seed default languages (en, fa)
- **AND** import base translation files
- **AND** configure default settings
- **AND** show installation success

#### Scenario: Module settings page
- **WHEN** admin accesses module settings
- **THEN** display language management interface
- **AND** show translation statistics
- **AND** provide quick actions (import/export)
- **AND** configure fallback behavior

### Requirement: Developer API
The system SHALL provide clear API for developers to use translations.

#### Scenario: Using translation helper
- **WHEN** developer needs translated string
- **THEN** use `__('key')` or `trans('key')` helpers
- **AND** support parameter substitution
- **AND** support pluralization
- **AND** return key if translation missing (dev mode)

#### Scenario: Module translations
- **WHEN** module provides translations
- **THEN** place in `resources/lang/{locale}/` directory
- **AND** namespace under module alias
- **AND** auto-register on module boot
- **AND** allow override via admin panel

## API Endpoints

### GET /api/v1/languages
List available languages.

**Response (200):**
```json
{
  "data": [
    {
      "type": "languages",
      "id": "en",
      "attributes": {
        "code": "en",
        "locale": "en_US",
        "name": "English",
        "native_name": "English",
        "direction": "ltr",
        "is_default": true,
        "is_active": true,
        "completion": 100
      }
    },
    {
      "type": "languages",
      "id": "fa",
      "attributes": {
        "code": "fa",
        "locale": "fa_IR",
        "name": "Persian",
        "native_name": "فارسی",
        "direction": "rtl",
        "is_default": false,
        "is_active": true,
        "completion": 95
      }
    }
  ]
}
```

### GET /api/v1/languages/{code}
Get language details.

### POST /api/v1/admin/languages
Create new language (admin).

**Request:**
```json
{
  "data": {
    "type": "languages",
    "attributes": {
      "code": "ar",
      "locale": "ar_SA",
      "name": "Arabic",
      "native_name": "العربية",
      "direction": "rtl",
      "is_active": true
    }
  }
}
```

### PATCH /api/v1/admin/languages/{code}
Update language settings.

### DELETE /api/v1/admin/languages/{code}
Delete language (cannot delete default).

### POST /api/v1/admin/languages/{code}/set-default
Set language as default.

### GET /api/v1/translations
Get translations for current locale.

**Query Parameters:**
- `locale`: Language code (optional, defaults to user preference)
- `group`: Translation group/namespace
- `search`: Search in keys and values

**Response (200):**
```json
{
  "data": {
    "locale": "en",
    "groups": {
      "common": {
        "save": "Save",
        "cancel": "Cancel",
        "delete": "Delete",
        "confirm": "Confirm"
      },
      "tasks": {
        "title": "Tasks",
        "create": "Create Task",
        "assigned_to": "Assigned to {name}",
        "due_date": "Due Date",
        "status": {
          "pending": "Pending",
          "in_progress": "In Progress",
          "completed": "Completed"
        }
      }
    }
  }
}
```

### GET /api/v1/admin/translations
List all translations for management (admin).

**Query Parameters:**
- `locale`: Filter by language
- `group`: Filter by group
- `missing`: Only show missing translations
- `search`: Search query

### PATCH /api/v1/admin/translations
Update translations (admin).

**Request:**
```json
{
  "translations": [
    {
      "locale": "fa",
      "group": "tasks",
      "key": "create",
      "value": "ایجاد وظیفه"
    },
    {
      "locale": "fa",
      "group": "tasks",
      "key": "title",
      "value": "وظایف"
    }
  ]
}
```

### POST /api/v1/admin/translations/import
Import translations from file.

### GET /api/v1/admin/translations/export
Export translations.

**Query Parameters:**
- `locale`: Languages to export (comma-separated or 'all')
- `group`: Groups to export (comma-separated or 'all')
- `format`: json, php, csv

### GET /api/v1/admin/translations/stats
Get translation statistics.

**Response (200):**
```json
{
  "data": {
    "total_keys": 1250,
    "languages": {
      "en": {
        "translated": 1250,
        "missing": 0,
        "completion": 100
      },
      "fa": {
        "translated": 1187,
        "missing": 63,
        "completion": 95
      }
    },
    "groups": {
      "common": { "keys": 50, "en": 50, "fa": 50 },
      "tasks": { "keys": 120, "en": 120, "fa": 115 },
      "notifications": { "keys": 80, "en": 80, "fa": 72 }
    }
  }
}
```

### PATCH /api/v1/users/me/language
Update current user's language preference.

**Request:**
```json
{
  "language": "fa"
}
```

## Implementation Details

### Backend (Laravel + Spatie Translatable)

#### Package Installation
```bash
composer require spatie/laravel-translatable
```

#### Model Configuration
```php
use Spatie\Translatable\HasTranslations;

class Task extends Model
{
    use HasTranslations;
    
    // Define translatable attributes
    public $translatable = ['title', 'description'];
    
    protected $casts = [
        'title' => 'array',
        'description' => 'array',
    ];
}
```

#### Database Schema
Translatable fields are stored as JSON columns:
```php
Schema::create('tasks', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->json('title');        // {"en": "Task", "fa": "وظیفه"}
    $table->json('description');  // {"en": "Description", "fa": "توضیحات"}
    // ... other fields
});
```

#### Locale Detection Middleware
```php
// app/Http/Middleware/SetRequestLocale.php
class SetRequestLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('lang')
            ?? $request->header('X-Locale')
            ?? $request->header('Accept-Language');
            
        if ($locale && in_array($locale, ['fa', 'en'])) {
            app()->setLocale(explode('-', explode(',', $locale)[0])[0]);
        }
        
        return $next($request);
    }
}
```

#### API Validation Rule
```php
// app/Rules/TranslatableValue.php
class TranslatableValue implements Rule
{
    public function passes($attribute, $value)
    {
        // Accept plain string
        if (is_string($value)) {
            return strlen($value) <= $this->max;
        }
        
        // Accept {en, fa} object
        if (is_array($value)) {
            foreach ($value as $locale => $text) {
                if (!in_array($locale, ['en', 'fa'])) {
                    return false;
                }
                if (!is_string($text) || strlen($text) > $this->max) {
                    return false;
                }
            }
            return true;
        }
        
        return false;
    }
}
```

#### Controller Usage
```php
// Accept both string and translated object
$validated = $request->validate([
    'title' => ['required', new TranslatableValue(max: 255)],
    'description' => ['nullable', new TranslatableValue()],
]);

// Create with translations
$task = Task::create([
    'title' => $validated['title'],  // Auto-handled by Spatie
    'description' => $validated['description'],
]);

// Returns translated value based on app()->getLocale()
echo $task->title;  // "Task" when locale is 'en'
echo $task->translate('title', 'fa');  // "وظیفه"
```

#### Supported Models with Translations

The following models support multilingual content:

| Model | Translatable Fields |
|-------|---------------------|
| `Organization` | name, description |
| `Department` | name, description |
| `Position` | name, description |
| `Task` | title, description |
| `RoutineTemplate` | title, description |

## Data Schema

### Language
```typescript
interface Language {
  code: string;          // ISO 639-1 (e.g., 'en', 'fa')
  locale: string;        // Full locale (e.g., 'en_US', 'fa_IR')
  name: string;          // English name
  native_name: string;   // Native script name
  direction: 'ltr' | 'rtl';
  is_default: boolean;
  is_active: boolean;
  
  // Calendar system for dates
  calendar: 'gregorian' | 'persian' | 'arabic';
  
  // Formatting options
  date_format: string;
  time_format: string;
  number_format: {
    decimal_separator: string;
    thousands_separator: string;
  };
  
  created_at: Date;
  updated_at: Date;
}

interface Translation {
  id: UUID;
  locale: string;
  group: string;         // Namespace (e.g., 'tasks', 'common', 'notifications')
  key: string;           // Translation key
  value: string;         // Translated text
  
  is_custom: boolean;    // User-added vs core translation
  is_html: boolean;      // Contains HTML formatting
  
  created_at: Date;
  updated_at: Date;
}

interface TranslationOverride {
  id: UUID;
  translation_id: UUID;
  organization_id?: UUID;  // For org-specific overrides
  value: string;
  
  created_by: UUID;
  created_at: Date;
  updated_at: Date;
}
```

## Default Translation Groups

| Group | Description | Example Keys |
|-------|-------------|--------------|
| `common` | Common UI elements | save, cancel, delete, confirm, search |
| `auth` | Authentication | login, logout, register, forgot_password |
| `validation` | Form validation | required, email, min, max, unique |
| `tasks` | Task module | create_task, assign, due_date, priority |
| `users` | User management | profile, settings, permissions |
| `notifications` | Notification messages | task_assigned, task_approved |
| `dashboard` | Dashboard UI | overview, analytics, recent_activity |
| `errors` | Error messages | not_found, unauthorized, server_error |
| `dates` | Date/time | today, yesterday, tomorrow, ago |

## Module Structure

```
modules/
└── Multilingual/
    ├── module.json
    ├── src/
    │   ├── Providers/
    │   │   └── MultilingualServiceProvider.php
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   ├── LanguageController.php
    │   │   │   └── TranslationController.php
    │   │   └── Middleware/
    │   │       └── SetLocale.php
    │   ├── Models/
    │   │   ├── Language.php
    │   │   └── Translation.php
    │   ├── Services/
    │   │   ├── TranslationManager.php
    │   │   ├── LocaleDetector.php
    │   │   └── TranslationLoader.php
    │   ├── Filament/
    │   │   ├── Resources/
    │   │   │   ├── LanguageResource.php
    │   │   │   └── TranslationResource.php
    │   │   └── Pages/
    │   │       ├── TranslationManager.php
    │   │       └── ModuleSettings.php
    │   └── Helpers/
    │       └── TranslationHelper.php
    ├── database/
    │   ├── migrations/
    │   │   ├── create_languages_table.php
    │   │   └── create_translations_table.php
    │   └── seeders/
    │       ├── LanguageSeeder.php
    │       └── TranslationSeeder.php
    ├── config/
    │   └── multilingual.php
    ├── routes/
    │   ├── api.php
    │   └── admin.php
    └── resources/
        └── lang/
            ├── en/
            │   ├── common.php
            │   ├── auth.php
            │   ├── validation.php
            │   ├── tasks.php
            │   ├── users.php
            │   ├── notifications.php
            │   └── ...
            └── fa/
                ├── common.php
                ├── auth.php
                ├── validation.php
                ├── tasks.php
                ├── users.php
                ├── notifications.php
                └── ...
```

## Farsi (Persian) Translation Sample

```php
// resources/lang/fa/common.php
return [
    'save' => 'ذخیره',
    'cancel' => 'انصراف',
    'delete' => 'حذف',
    'edit' => 'ویرایش',
    'create' => 'ایجاد',
    'search' => 'جستجو',
    'filter' => 'فیلتر',
    'confirm' => 'تأیید',
    'back' => 'بازگشت',
    'next' => 'بعدی',
    'previous' => 'قبلی',
    'loading' => 'در حال بارگذاری...',
    'no_results' => 'نتیجه‌ای یافت نشد',
    'actions' => 'عملیات',
    'status' => 'وضعیت',
    'date' => 'تاریخ',
    'time' => 'زمان',
    'yes' => 'بله',
    'no' => 'خیر',
];

// resources/lang/fa/tasks.php
return [
    'title' => 'وظایف',
    'create_task' => 'ایجاد وظیفه',
    'edit_task' => 'ویرایش وظیفه',
    'delete_task' => 'حذف وظیفه',
    'task_title' => 'عنوان وظیفه',
    'description' => 'توضیحات',
    'assigned_to' => 'محول شده به :name',
    'due_date' => 'مهلت انجام',
    'priority' => 'اولویت',
    'status' => [
        'not_started' => 'شروع نشده',
        'in_progress' => 'در حال انجام',
        'pending_review' => 'در انتظار بررسی',
        'completed' => 'تکمیل شده',
        'cancelled' => 'لغو شده',
    ],
    'priority_levels' => [
        'low' => 'کم',
        'medium' => 'متوسط',
        'high' => 'زیاد',
        'critical' => 'بحرانی',
    ],
];
```

## Frontend Integration (Nuxt)

### Translation Plugin Setup
```typescript
// plugins/i18n.ts
import { createI18n } from 'vue-i18n'

export default defineNuxtPlugin(async (nuxtApp) => {
  const locale = useCookie('locale').value || 'en'
  
  const i18n = createI18n({
    legacy: false,
    locale,
    fallbackLocale: 'en',
    messages: {}, // Loaded dynamically
  })
  
  // Load translations from API
  const { data } = await useFetch(`/api/v1/translations?locale=${locale}`)
  i18n.global.setLocaleMessage(locale, data.value.groups)
  
  nuxtApp.vueApp.use(i18n)
})
```

### RTL Support
```typescript
// composables/useDirection.ts
export const useDirection = () => {
  const { locale } = useI18n()
  
  const rtlLocales = ['fa', 'ar', 'he']
  
  const direction = computed(() => 
    rtlLocales.includes(locale.value) ? 'rtl' : 'ltr'
  )
  
  const isRtl = computed(() => direction.value === 'rtl')
  
  // Update document direction
  watch(direction, (dir) => {
    document.documentElement.dir = dir
    document.documentElement.lang = locale.value
  }, { immediate: true })
  
  return { direction, isRtl }
}
```

### Persian Calendar Support
```typescript
// composables/usePersianDate.ts
import { format as jalaliFormat } from 'date-fns-jalali'

export const usePersianDate = () => {
  const { locale } = useI18n()
  
  const formatDate = (date: Date, formatStr: string) => {
    if (locale.value === 'fa') {
      return jalaliFormat(date, formatStr, { locale: faIR })
    }
    return format(date, formatStr)
  }
  
  return { formatDate }
}
```
