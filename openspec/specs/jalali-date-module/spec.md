# Jalali Date Module Specification

## Purpose

The Jalali Date Module provides comprehensive Persian/Jalali (Solar Hijri) calendar support for IshYar, enabling date pickers, form fields, display formatting, and date calculations using the Jalali calendar system. This module is installed by default and can be enabled/disabled per organization or user preference.

## Module Manifest

```json
{
  "name": "ishyar/jalali-date",
  "version": "1.0.0",
  "displayName": "Jalali Calendar",
  "description": "Persian/Jalali (Solar Hijri) calendar support for date pickers, forms, and display",
  "author": "IshYar Core Team",
  "license": "MIT",
  "category": "localization",
  "icon": "calendar-days",
  
  "requires": {
    "ishyar/core": "^1.0",
    "ishyar/multilingual": "^1.0",
    "php": "^8.3"
  },
  
  "provides": {
    "services": ["JalaliDateService", "DateFormatterService", "CalendarService"],
    "facades": ["Jalali", "PersianDate"],
    "middleware": [],
    "commands": [
      "jalali:convert-dates",
      "jalali:sync-holidays"
    ]
  },
  
  "hooks": {
    "extend": ["settings.localization", "forms.date-fields", "display.date-format"],
    "listen": ["user.locale-changed", "organization.settings-updated"]
  },
  
  "permissions": [
    "jalali.settings"
  ],
  
  "settings": {
    "enabled": true,
    "default_calendar": "auto",
    "show_gregorian_alongside": false,
    "persian_numerals": true,
    "first_day_of_week": "saturday",
    "holiday_highlight": true
  },
  
  "extra": {
    "ishyar": {
      "namespace": "JalaliDate",
      "provider": "IshYar\\JalaliDate\\Providers\\JalaliDateServiceProvider",
      "alias": "jalali",
      "icon": "heroicon-o-calendar-days",
      "color": "#059669",
      "order": 15,
      "core": false,
      "default_enabled": true,
      "has_installer": true,
      "has_settings": true
    }
  }
}
```

## Requirements

### Requirement: Jalali Calendar System
The system SHALL provide full Jalali (Persian/Solar Hijri) calendar functionality.

#### Scenario: Calendar system selection
- **WHEN** user or organization configures date settings
- **THEN** select calendar system:
  - **Auto**: Based on user's language/locale (Jalali for fa, Gregorian for en)
  - **Jalali**: Always use Jalali calendar
  - **Gregorian**: Always use Gregorian calendar
- **AND** persist preference at organization and user level
- **AND** user preference overrides organization default

#### Scenario: Date conversion accuracy
- **WHEN** converting between Jalali and Gregorian
- **THEN** use precise astronomical algorithms
- **AND** handle leap years correctly (Jalali leap year rules)
- **AND** support full date range (historical and future)
- **AND** maintain time component during conversion

#### Scenario: Jalali date validation
- **WHEN** validating Jalali date input
- **THEN** check month boundaries (31 days for months 1-6, 30 for 7-11, 29/30 for month 12)
- **AND** validate leap years for Esfand (month 12)
- **AND** reject invalid dates with appropriate error messages
- **AND** support multiple input formats

### Requirement: Date Picker Component
The system SHALL provide a Jalali-aware date picker for all date fields.

#### Scenario: Jalali date picker display
- **WHEN** date picker is opened in Jalali mode
- **THEN** display Persian month names (فروردین، اردیبهشت، ...)
- **AND** display Persian day names (شنبه، یکشنبه، ...)
- **AND** show Persian numerals if enabled
- **AND** start week from Saturday (configurable)
- **AND** use RTL layout

#### Scenario: Date picker navigation
- **WHEN** navigating the date picker
- **THEN** support month/year navigation
- **AND** allow quick year selection (decade view)
- **AND** provide today button (امروز)
- **AND** support keyboard navigation
- **AND** animate transitions smoothly

#### Scenario: Date range picker
- **WHEN** selecting date ranges
- **THEN** support Jalali start and end dates
- **AND** highlight selected range visually
- **AND** validate end date is after start date
- **AND** show range duration in Jalali format

#### Scenario: Date picker with time
- **WHEN** datetime picker is used
- **THEN** combine Jalali date with time picker
- **AND** support 12/24 hour formats
- **AND** display time in Persian numerals if enabled
- **AND** handle timezone appropriately

### Requirement: Form Field Integration
The system SHALL integrate Jalali dates into all form components.

#### Scenario: Filament date field
- **WHEN** using date field in Filament admin
- **THEN** automatically use Jalali picker for Persian locale
- **AND** store as Gregorian in database (ISO 8601)
- **AND** display as Jalali when showing
- **AND** support all Filament date field features

#### Scenario: Vue date input component
- **WHEN** using date input in frontend forms
- **THEN** provide `<JalaliDatePicker>` component
- **AND** support v-model with Date or string
- **AND** emit both Jalali and Gregorian values
- **AND** integrate with form validation (Vee-Validate/FormKit)

#### Scenario: Date validation rules
- **WHEN** validating date fields
- **THEN** provide Jalali-aware validation rules:
  - `jalali_date`: Valid Jalali date format
  - `jalali_after:date`: Date is after specified Jalali date
  - `jalali_before:date`: Date is before specified Jalali date
  - `jalali_between:start,end`: Date is between range
- **AND** return localized error messages

#### Scenario: Date field configuration
- **WHEN** configuring date fields
- **THEN** support options:
  - `min_date`: Minimum selectable date (Jalali or Gregorian)
  - `max_date`: Maximum selectable date
  - `disabled_dates`: Array of disabled dates
  - `disabled_days`: Disabled days of week (e.g., Fridays)
  - `format`: Display format string

### Requirement: Date Display Formatting
The system SHALL format dates in Jalali format throughout the application.

#### Scenario: Automatic date formatting
- **WHEN** displaying dates in UI
- **THEN** format according to user's calendar preference
- **AND** use locale-appropriate format patterns
- **AND** support various format lengths:
  - Short: `۱۴۰۳/۱۰/۰۸`
  - Medium: `۸ دی ۱۴۰۳`
  - Long: `شنبه، ۸ دی ۱۴۰۳`
  - Full: `شنبه، هشتم دی یک‌هزار و چهارصد و سه`

#### Scenario: Relative date display
- **WHEN** showing relative dates
- **THEN** display in Persian:
  - `امروز` (Today)
  - `دیروز` (Yesterday)
  - `فردا` (Tomorrow)
  - `۳ روز پیش` (3 days ago)
  - `هفته آینده` (Next week)
- **AND** use Persian numerals if enabled

#### Scenario: Date range display
- **WHEN** displaying date ranges
- **THEN** format appropriately:
  - Same month: `۵ تا ۱۰ دی ۱۴۰۳`
  - Different months: `۲۵ آذر تا ۵ دی ۱۴۰۳`
  - Different years: `۲۵ اسفند ۱۴۰۲ تا ۵ فروردین ۱۴۰۳`

#### Scenario: Dual calendar display
- **WHEN** "show Gregorian alongside" is enabled
- **THEN** display both calendars:
  - `۸ دی ۱۴۰۳ (28 Dec 2024)`
- **AND** show Gregorian in parentheses or smaller text
- **AND** support toggling display mode

### Requirement: Persian Numerals
The system SHALL support Persian numeral display for dates.

#### Scenario: Numeral conversion
- **WHEN** Persian numerals setting is enabled
- **THEN** convert Arabic numerals (0-9) to Persian (۰-۹)
- **AND** apply to:
  - Dates and times
  - Day numbers in calendar
  - Year display
- **AND** maintain Arabic numerals for input (with conversion)

#### Scenario: Numeral toggle
- **WHEN** user toggles numeral preference
- **THEN** immediately update all date displays
- **AND** persist preference
- **AND** apply consistently across application

### Requirement: Week Configuration
The system SHALL support Jalali week configuration.

#### Scenario: First day of week
- **WHEN** Jalali calendar is active
- **THEN** default first day to Saturday (شنبه)
- **AND** allow configuration (Saturday, Sunday, Monday)
- **AND** apply to all calendar displays
- **AND** affect week number calculations

#### Scenario: Weekend highlighting
- **WHEN** displaying calendar
- **THEN** highlight weekend days (Friday by default)
- **AND** support configurable weekend (Thursday-Friday, Friday only)
- **AND** show weekends in distinct color

### Requirement: Holiday Support
The system SHALL support Persian/Iranian holidays.

#### Scenario: Holiday data
- **WHEN** module is installed
- **THEN** include Iranian official holidays:
  - Nowruz (۱-۴ فروردین)
  - Sizdah Bedar (۱۳ فروردین)
  - Islamic Revolution Day (۲۲ بهمن)
  - Oil Nationalization Day (۲۹ اسفند)
  - Islamic holidays (calculated from Hijri)
- **AND** support custom holiday addition
- **AND** sync holidays periodically

#### Scenario: Holiday display
- **WHEN** calendar shows holidays
- **THEN** highlight holiday dates distinctly
- **AND** show holiday name on hover/tap
- **AND** optionally disable holiday selection in date pickers
- **AND** use holiday information in scheduling

#### Scenario: Holiday API integration
- **WHEN** syncing holidays
- **THEN** optionally fetch from external API
- **AND** support manual holiday management
- **AND** handle Islamic holidays (lunar calendar conversion)

### Requirement: Date Calculations
The system SHALL provide Jalali-aware date calculations.

#### Scenario: Date arithmetic
- **WHEN** performing date calculations
- **THEN** support:
  - Add/subtract days, weeks, months, years
  - Difference between dates (in Jalali units)
  - Start/end of Jalali month
  - Start/end of Jalali year
- **AND** handle month/year boundaries correctly

#### Scenario: Age calculation
- **WHEN** calculating age from birth date
- **THEN** calculate in Jalali years
- **AND** display as: `سن: ۳۵ سال`
- **AND** handle edge cases (leap year births)

#### Scenario: Duration formatting
- **WHEN** displaying durations
- **THEN** format in Persian:
  - `۲ سال و ۳ ماه`
  - `۴۵ روز`
  - `۱ هفته و ۲ روز`

### Requirement: Backend Integration
The system SHALL provide backend services for Jalali date handling.

#### Scenario: Laravel Carbon extension
- **WHEN** working with dates in PHP
- **THEN** provide Jalali Carbon methods:
  ```php
  $date->toJalali();           // Convert to Jalali
  $date->formatJalali('Y/m/d'); // Format as Jalali
  Jalali::parse('1403/10/08'); // Parse Jalali string
  Jalali::now();               // Current Jalali date
  ```
- **AND** integrate with Eloquent date casting
- **AND** support Laravel validation rules

#### Scenario: Database storage
- **WHEN** storing dates
- **THEN** always store in Gregorian (ISO 8601 UTC)
- **AND** convert to/from Jalali at application layer
- **AND** support Jalali-formatted queries via scope

#### Scenario: API date handling
- **WHEN** dates are in API requests/responses
- **THEN** accept both Jalali and Gregorian formats
- **AND** detect format automatically
- **AND** return in user's preferred format
- **AND** include calendar type in metadata

### Requirement: Frontend Integration
The system SHALL provide Vue.js components and utilities.

#### Scenario: Vue components
- **WHEN** using dates in frontend
- **THEN** provide components:
  - `<JalaliDatePicker>`: Date selection
  - `<JalaliDateRangePicker>`: Range selection
  - `<JalaliDateTimeInput>`: Date with time
  - `<JalaliCalendar>`: Full calendar display
  - `<FormattedDate>`: Display formatted date
- **AND** support Tailwind CSS styling
- **AND** integrate with Vue Shadcn design system

#### Scenario: Vue composables
- **WHEN** manipulating dates in Vue
- **THEN** provide composables:
  ```typescript
  const { toJalali, toGregorian, formatJalali } = useJalali();
  const { date, formatted, isToday } = useJalaliDate(props.date);
  ```
- **AND** support reactive date handling
- **AND** integrate with Pinia stores

#### Scenario: Nuxt plugin
- **WHEN** Nuxt app initializes
- **THEN** register Jalali plugin globally
- **AND** provide `$jalali` helper
- **AND** support SSR-safe operations
- **AND** configure via `nuxt.config.ts`

### Requirement: Module Settings
The module SHALL provide admin-configurable settings.

#### Scenario: Organization settings
- **WHEN** admin configures Jalali module
- **THEN** provide settings UI in Filament:
  - Enable/disable module
  - Default calendar system (auto/jalali/gregorian)
  - Persian numerals toggle
  - First day of week
  - Show Gregorian alongside
  - Holiday highlighting
- **AND** apply to all organization users

#### Scenario: User preferences
- **WHEN** user customizes date preferences
- **THEN** allow override of organization defaults:
  - Personal calendar preference
  - Numeral preference
- **AND** persist in user profile
- **AND** sync across devices

### Requirement: Migration & Compatibility
The system SHALL ensure backward compatibility and data integrity.

#### Scenario: Existing data handling
- **WHEN** module is enabled
- **THEN** existing Gregorian dates continue to work
- **AND** display converts automatically
- **AND** no database migration required for dates
- **AND** support gradual rollout

#### Scenario: Module disable behavior
- **WHEN** module is disabled
- **THEN** revert to Gregorian display
- **AND** maintain all date data
- **AND** remove Jalali UI components
- **AND** keep settings for re-enable

## API Endpoints

### GET /api/v1/jalali/settings
Get Jalali module settings for current user/organization.

**Response (200):**
```json
{
  "data": {
    "enabled": true,
    "calendar_system": "jalali",
    "persian_numerals": true,
    "first_day_of_week": "saturday",
    "show_gregorian_alongside": false,
    "holiday_highlight": true
  }
}
```

### PATCH /api/v1/jalali/settings
Update Jalali module settings (admin).

**Request:**
```json
{
  "enabled": true,
  "persian_numerals": false
}
```

### GET /api/v1/jalali/convert
Convert dates between calendars.

**Query Parameters:**
- `date`: Date string to convert
- `from`: Source calendar (jalali/gregorian)
- `to`: Target calendar (jalali/gregorian)
- `format`: Output format string

**Response (200):**
```json
{
  "data": {
    "input": "2024-12-28",
    "input_calendar": "gregorian",
    "output": "1403/10/08",
    "output_calendar": "jalali",
    "formatted": "۸ دی ۱۴۰۳",
    "components": {
      "year": 1403,
      "month": 10,
      "day": 8,
      "day_of_week": 6,
      "day_name": "شنبه",
      "month_name": "دی"
    }
  }
}
```

### GET /api/v1/jalali/holidays
Get holidays for a year.

**Query Parameters:**
- `year`: Jalali year (default: current year)
- `month`: Optional month filter

**Response (200):**
```json
{
  "data": [
    {
      "date": "1403/01/01",
      "gregorian": "2024-03-20",
      "title": "نوروز",
      "title_en": "Nowruz",
      "type": "national",
      "is_holiday": true
    },
    {
      "date": "1403/01/13",
      "gregorian": "2024-04-01",
      "title": "سیزده‌بدر",
      "title_en": "Sizdah Bedar",
      "type": "national",
      "is_holiday": true
    }
  ]
}
```

### GET /api/v1/jalali/format
Format a date in Jalali.

**Query Parameters:**
- `date`: Date to format (ISO 8601 or Jalali)
- `format`: Format string (default: medium)
- `relative`: Boolean for relative formatting

**Response (200):**
```json
{
  "data": {
    "formatted": "شنبه، ۸ دی ۱۴۰۳",
    "relative": "امروز",
    "iso": "2024-12-28T00:00:00Z"
  }
}
```

## Data Schema

### JalaliSettings
```typescript
interface JalaliSettings {
  organization_id: UUID | null;
  user_id: UUID | null;
  
  enabled: boolean;
  calendar_system: 'auto' | 'jalali' | 'gregorian';
  persian_numerals: boolean;
  first_day_of_week: 'saturday' | 'sunday' | 'monday';
  show_gregorian_alongside: boolean;
  holiday_highlight: boolean;
  
  created_at: Date;
  updated_at: Date;
}
```

### Holiday
```typescript
interface Holiday {
  id: UUID;
  jalali_date: string;      // Format: MM-DD (month-day)
  gregorian_date?: string;  // For fixed Gregorian holidays
  hijri_date?: string;      // For Islamic holidays (lunar)
  
  title: string;
  title_en: string;
  description?: string;
  
  type: 'national' | 'religious' | 'international' | 'custom';
  is_official_holiday: boolean;
  is_recurring: boolean;    // Repeats every year
  
  year?: number;            // Specific year (for non-recurring)
  
  created_at: Date;
  updated_at: Date;
}
```

### JalaliDate (Value Object)
```typescript
interface JalaliDate {
  year: number;             // e.g., 1403
  month: number;            // 1-12
  day: number;              // 1-31
  
  // Computed
  dayOfWeek: number;        // 0 (Saturday) - 6 (Friday)
  dayOfYear: number;        // 1-366
  weekOfYear: number;       // 1-53
  isLeapYear: boolean;
  
  // Names
  monthName: string;        // Persian month name
  dayName: string;          // Persian day name
  
  // Conversion
  toGregorian(): Date;
  toISO(): string;
  
  // Formatting
  format(pattern: string): string;
  toRelative(): string;
}
```

## Vue Components

### JalaliDatePicker
```vue
<template>
  <JalaliDatePicker
    v-model="selectedDate"
    :min-date="minDate"
    :max-date="maxDate"
    :disabled-dates="disabledDates"
    :disabled-days="[5]"              <!-- Fridays -->
    :show-holidays="true"
    :persian-numerals="true"
    placeholder="تاریخ را انتخاب کنید"
    format="YYYY/MM/DD"
    @change="handleChange"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { JalaliDatePicker } from '@ishyar/jalali-date';

const selectedDate = ref<Date | null>(null);
const minDate = new Date('2024-01-01');
const maxDate = new Date('2025-12-31');
const disabledDates = ['1403/01/01', '1403/01/02'];

const handleChange = (date: Date, jalaliString: string) => {
  console.log('Selected:', jalaliString);
};
</script>
```

### JalaliDateRangePicker
```vue
<template>
  <JalaliDateRangePicker
    v-model:start="startDate"
    v-model:end="endDate"
    :max-range="30"                   <!-- Max 30 days -->
    :presets="rangePresets"
    @range-change="handleRangeChange"
  />
</template>

<script setup lang="ts">
const rangePresets = [
  { label: 'هفته جاری', value: 'this-week' },
  { label: 'ماه جاری', value: 'this-month' },
  { label: 'سه ماه گذشته', value: 'last-3-months' },
];
</script>
```

### FormattedDate
```vue
<template>
  <!-- Outputs: شنبه، ۸ دی ۱۴۰۳ -->
  <FormattedDate :date="task.due_date" format="long" />
  
  <!-- Outputs: ۲ روز پیش -->
  <FormattedDate :date="task.created_at" relative />
  
  <!-- Outputs: ۸ دی ۱۴۰۳ (Dec 28, 2024) -->
  <FormattedDate :date="task.due_date" show-gregorian />
</template>
```

## PHP Usage Examples

### Basic Date Conversion
```php
use IshYar\JalaliDate\Facades\Jalali;

// Current Jalali date
$now = Jalali::now();
echo $now->format('Y/m/d'); // 1403/10/08

// Parse Jalali string
$date = Jalali::parse('1403/10/08');
echo $date->toGregorian()->format('Y-m-d'); // 2024-12-28

// Convert from Gregorian
$gregorian = Carbon::parse('2024-12-28');
$jalali = Jalali::fromGregorian($gregorian);
echo $jalali->format('l، j F Y'); // شنبه، ۸ دی ۱۴۰۳
```

### Eloquent Integration
```php
use IshYar\JalaliDate\Casts\JalaliDate;

class Task extends Model
{
    protected $casts = [
        'due_date' => 'datetime',
    ];
    
    // Accessor for Jalali display
    public function getDueDateJalaliAttribute(): string
    {
        return Jalali::fromGregorian($this->due_date)->format('Y/m/d');
    }
    
    // Scope for Jalali date queries
    public function scopeCreatedInJalaliMonth($query, int $year, int $month)
    {
        $start = Jalali::create($year, $month, 1)->startOfMonth()->toGregorian();
        $end = Jalali::create($year, $month, 1)->endOfMonth()->toGregorian();
        
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
```

### Validation Rules
```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($request->all(), [
    'birth_date' => 'required|jalali_date',
    'start_date' => 'required|jalali_date|jalali_after:today',
    'end_date' => 'required|jalali_date|jalali_after:start_date',
    'event_date' => 'jalali_between:1403/01/01,1403/12/29',
]);
```

## Filament Integration

### Date Field
```php
use Filament\Forms\Components\DatePicker;
use IshYar\JalaliDate\Filament\JalaliDatePicker;

// In form schema
JalaliDatePicker::make('due_date')
    ->label('تاریخ سررسید')
    ->required()
    ->minDate(now())
    ->displayFormat('j F Y')        // ۸ دی ۱۴۰۳
    ->closeOnDateSelection()
    ->native(false);
```

### Table Column
```php
use Filament\Tables\Columns\TextColumn;
use IshYar\JalaliDate\Filament\JalaliDateColumn;

JalaliDateColumn::make('created_at')
    ->label('تاریخ ایجاد')
    ->format('j F Y')               // ۸ دی ۱۴۰۳
    ->sortable()
    ->description(fn ($record) => $record->created_at->diffForHumans());
```

## Directory Structure

```
modules/
└── JalaliDate/
    ├── module.json
    ├── composer.json
    ├── src/
    │   ├── Providers/
    │   │   └── JalaliDateServiceProvider.php
    │   ├── Facades/
    │   │   └── Jalali.php
    │   ├── Services/
    │   │   ├── JalaliDateService.php
    │   │   ├── DateFormatterService.php
    │   │   ├── CalendarService.php
    │   │   └── HolidayService.php
    │   ├── Casts/
    │   │   └── JalaliDate.php
    │   ├── Rules/
    │   │   ├── JalaliDateRule.php
    │   │   ├── JalaliAfterRule.php
    │   │   ├── JalaliBeforeRule.php
    │   │   └── JalaliBetweenRule.php
    │   ├── Http/
    │   │   └── Controllers/
    │   │       └── JalaliController.php
    │   ├── Filament/
    │   │   ├── JalaliDatePicker.php
    │   │   ├── JalaliDateColumn.php
    │   │   └── Pages/
    │   │       └── JalaliSettings.php
    │   ├── ValueObjects/
    │   │   └── JalaliDate.php
    │   └── Helpers/
    │       └── functions.php
    ├── database/
    │   ├── migrations/
    │   │   ├── create_jalali_settings_table.php
    │   │   └── create_holidays_table.php
    │   └── seeders/
    │       └── IranianHolidaysSeeder.php
    ├── config/
    │   └── jalali.php
    ├── routes/
    │   └── api.php
    ├── resources/
    │   ├── lang/
    │   │   ├── en/
    │   │   │   └── jalali.php
    │   │   └── fa/
    │   │       └── jalali.php
    │   └── views/
    │       └── components/
    ├── frontend/
    │   ├── components/
    │   │   ├── JalaliDatePicker.vue
    │   │   ├── JalaliDateRangePicker.vue
    │   │   ├── JalaliDateTimeInput.vue
    │   │   ├── JalaliCalendar.vue
    │   │   └── FormattedDate.vue
    │   ├── composables/
    │   │   ├── useJalali.ts
    │   │   └── useJalaliDate.ts
    │   ├── utils/
    │   │   ├── jalali-converter.ts
    │   │   ├── jalali-formatter.ts
    │   │   └── persian-numerals.ts
    │   ├── types/
    │   │   └── jalali.d.ts
    │   └── plugin/
    │       └── jalali.ts
    ├── tests/
    │   ├── Unit/
    │   │   ├── JalaliDateTest.php
    │   │   ├── ConversionTest.php
    │   │   └── FormatterTest.php
    │   └── Feature/
    │       ├── ApiTest.php
    │       └── ValidationTest.php
    └── README.md
```

## NPM Package Dependencies

```json
{
  "dependencies": {
    "jalaali-js": "^1.2.0"
  }
}
```

## Composer Package Dependencies

```json
{
  "require": {
    "morilog/jalali": "^3.0",
    "hekmatinasser/verta": "^8.0"
  }
}
```

## Jalali Month Names Reference

| # | Persian | English | Days |
|---|---------|---------|------|
| 1 | فروردین | Farvardin | 31 |
| 2 | اردیبهشت | Ordibehesht | 31 |
| 3 | خرداد | Khordad | 31 |
| 4 | تیر | Tir | 31 |
| 5 | مرداد | Mordad | 31 |
| 6 | شهریور | Shahrivar | 31 |
| 7 | مهر | Mehr | 30 |
| 8 | آبان | Aban | 30 |
| 9 | آذر | Azar | 30 |
| 10 | دی | Dey | 30 |
| 11 | بهمن | Bahman | 30 |
| 12 | اسفند | Esfand | 29/30 |

## Jalali Day Names Reference

| # | Persian | English |
|---|---------|---------|
| 0 | شنبه | Shanbe (Saturday) |
| 1 | یکشنبه | Yekshanbe (Sunday) |
| 2 | دوشنبه | Doshanbe (Monday) |
| 3 | سه‌شنبه | Seshanbe (Tuesday) |
| 4 | چهارشنبه | Chaharshanbe (Wednesday) |
| 5 | پنجشنبه | Panjshanbe (Thursday) |
| 6 | جمعه | Jome (Friday) |

## Related Specifications

- [Multilingual Module](../multilingual-module/spec.md) - Language and locale handling
- [Currencies Module](../currencies-module/spec.md) - Number formatting integration
- [UI Design System](../ui-design-system/spec.md) - Component styling guidelines
