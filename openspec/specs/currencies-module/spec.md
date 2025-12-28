# Currencies Module Specification

## Purpose

The Currencies Module provides comprehensive multi-currency support for IshYar, enabling organizations to work with multiple currencies, real-time exchange rates, automatic conversions, and localized number formatting.

## Module Manifest

```json
{
  "name": "ishyar/currencies",
  "version": "1.0.0",
  "displayName": "Currencies & Exchange Rates",
  "description": "Multi-currency support with real-time exchange rates and automatic conversions",
  "author": "IshYar Core Team",
  "license": "MIT",
  "category": "finance",
  "icon": "currency-dollar",
  
  "requires": {
    "ishyar/core": "^1.0",
    "php": "^8.3"
  },
  
  "provides": {
    "services": ["CurrencyService", "ExchangeRateService", "MoneyFormatter"],
    "facades": ["Currency", "ExchangeRate", "Money"],
    "middleware": [],
    "commands": [
      "currencies:sync-rates",
      "currencies:seed-defaults",
      "currencies:cleanup-history"
    ]
  },
  
  "hooks": {
    "extend": ["settings.finance", "reports.filters", "dashboard.widgets"],
    "listen": ["organization.created", "settings.updated"]
  },
  
  "permissions": [
    "currencies.view",
    "currencies.manage",
    "currencies.rates.update",
    "currencies.settings"
  ],
  
  "settings": {
    "default_currency": "USD",
    "exchange_rate_provider": "exchangerate-api",
    "auto_sync_rates": true,
    "sync_interval_hours": 6,
    "rate_history_days": 365
  }
}
```

## Requirements

### Requirement: Currency Management
The system SHALL support multiple currencies with comprehensive configuration.

#### Scenario: Currency CRUD operations
- **WHEN** admin manages currencies
- **THEN** create, read, update, delete currencies
- **AND** set currency code (ISO 4217)
- **AND** set currency name and symbol
- **AND** configure decimal places (0-4)
- **AND** set symbol position (before/after)
- **AND** enable/disable currencies

#### Scenario: Default currency configuration
- **WHEN** organization is set up
- **THEN** set primary/base currency
- **AND** set display currency for reports
- **AND** configure fallback currency
- **AND** persist in organization settings

#### Scenario: Currency activation
- **WHEN** currency is activated
- **THEN** make available for transactions
- **AND** fetch initial exchange rate
- **AND** add to currency selectors
- **AND** create rate history entry

#### Scenario: Pre-seeded currencies
- **WHEN** module is installed
- **THEN** seed common currencies (USD, EUR, GBP, IRR, etc.)
- **AND** include ISO 4217 metadata
- **AND** set appropriate decimal places
- **AND** configure default symbols

### Requirement: Exchange Rate Management
The system SHALL support real-time and manual exchange rate updates.

#### Scenario: Automatic rate synchronization
- **WHEN** auto-sync is enabled
- **THEN** fetch rates from configured provider
- **AND** run at configured interval
- **AND** update all active currency pairs
- **AND** store rate history
- **AND** log sync results

#### Scenario: Manual rate entry
- **WHEN** admin enters manual rate
- **THEN** validate rate value
- **AND** record as manual override
- **AND** store effective date
- **AND** maintain audit trail

#### Scenario: Rate providers
- **WHEN** fetching exchange rates
- **THEN** support multiple providers:
  - ExchangeRate-API (default)
  - Open Exchange Rates
  - Fixer.io
  - CurrencyLayer
  - Central Bank APIs (Iran, ECB)
- **AND** handle provider failures gracefully
- **AND** fallback to secondary provider

#### Scenario: Historical rates
- **WHEN** querying historical transactions
- **THEN** use rate from transaction date
- **AND** store rate snapshot with transactions
- **AND** support backdated rate entry
- **AND** calculate historical conversions

#### Scenario: Rate alerts
- **WHEN** rate changes significantly
- **THEN** optionally notify administrators
- **AND** define threshold percentages
- **AND** log significant fluctuations

### Requirement: Currency Conversion
The system SHALL provide accurate and efficient currency conversions.

#### Scenario: Real-time conversion
- **WHEN** converting between currencies
- **THEN** use latest available rate
- **AND** apply configured rounding
- **AND** handle inverse calculations
- **AND** support triangulation through base currency

#### Scenario: Batch conversion
- **WHEN** converting multiple amounts
- **THEN** process efficiently in batch
- **AND** use consistent rate for batch
- **AND** return array of results
- **AND** support different target currencies

#### Scenario: Conversion with date
- **WHEN** converting for specific date
- **THEN** use rate from that date
- **AND** fallback to nearest available rate
- **AND** warn if rate is stale

#### Scenario: Rounding rules
- **WHEN** applying currency rounding
- **THEN** respect currency decimal places
- **AND** support rounding modes (half-up, half-down, banker's)
- **AND** apply configured global rule
- **AND** allow per-conversion override

### Requirement: Money Formatting
The system SHALL format monetary values according to locale and currency.

#### Scenario: Display formatting
- **WHEN** displaying money amounts
- **THEN** use currency symbol
- **AND** apply thousand separators
- **AND** use correct decimal separator
- **AND** position symbol correctly (before/after)
- **AND** respect locale conventions

#### Scenario: Locale-specific formatting
- **WHEN** formatting for Persian locale
- **THEN** use Persian thousands separator
- **AND** optionally use Persian numerals
- **AND** display "ریال" or "تومان" appropriately
- **AND** handle Rial/Toman conversion (÷10)

#### Scenario: Input parsing
- **WHEN** parsing money input
- **THEN** accept various formats
- **AND** strip currency symbols
- **AND** handle locale-specific separators
- **AND** normalize to decimal value

#### Scenario: Compact notation
- **WHEN** displaying large amounts
- **THEN** support compact format (1.5M, 2.3B)
- **AND** localize abbreviations
- **AND** maintain precision on hover/expand

### Requirement: Iranian Rial/Toman Support
The system SHALL provide special handling for Iranian currency conventions.

#### Scenario: Toman display option
- **WHEN** Iranian Rial is selected
- **THEN** offer display in Toman (IRR ÷ 10)
- **AND** clearly indicate unit (ریال vs تومان)
- **AND** store internally as Rial
- **AND** convert display automatically

#### Scenario: Persian numeral display
- **WHEN** Persian locale is active
- **THEN** optionally display Persian numerals (۱۲۳۴)
- **AND** make configurable per user
- **AND** maintain Western digits in inputs
- **AND** support mixed display contexts

### Requirement: Integration Points
The system SHALL integrate with other IshYar modules.

#### Scenario: Task/project budgets
- **WHEN** tasks have budget fields
- **THEN** store amount with currency
- **AND** convert for reporting
- **AND** aggregate in base currency

#### Scenario: Report currency
- **WHEN** generating financial reports
- **THEN** convert all values to report currency
- **AND** show original currency optionally
- **AND** indicate conversion rate used
- **AND** allow currency filter

#### Scenario: Dashboard widgets
- **WHEN** displaying financial widgets
- **THEN** show in user's preferred currency
- **AND** provide currency toggle
- **AND** display exchange rate info

### Requirement: Admin Interface
The system SHALL provide Filament admin pages for currency management.

#### Scenario: Currency list page
- **WHEN** admin views currencies
- **THEN** display all currencies in table
- **AND** show code, name, symbol, status
- **AND** show current rate to base currency
- **AND** provide quick actions (enable/disable)

#### Scenario: Exchange rate page
- **WHEN** admin views exchange rates
- **THEN** display rate matrix
- **AND** show last update time
- **AND** provide manual rate entry
- **AND** show rate history chart
- **AND** trigger manual sync

#### Scenario: Settings page
- **WHEN** admin configures currency settings
- **THEN** select base/default currency
- **AND** configure rate provider
- **AND** set sync schedule
- **AND** configure display options
- **AND** set rounding rules

## API Endpoints

### Currency Management

#### GET /api/v1/currencies
List all currencies.

**Query Parameters:**
- `active` (boolean): Filter by active status
- `include` (string): Include relations (rates)

**Response (200):**
```json
{
  "data": [
    {
      "id": "usd",
      "type": "currencies",
      "attributes": {
        "code": "USD",
        "name": "US Dollar",
        "name_local": "دلار آمریکا",
        "symbol": "$",
        "symbol_native": "$",
        "decimal_places": 2,
        "symbol_position": "before",
        "thousand_separator": ",",
        "decimal_separator": ".",
        "is_active": true,
        "is_base": true,
        "rate_to_base": 1.0
      }
    },
    {
      "id": "irr",
      "type": "currencies",
      "attributes": {
        "code": "IRR",
        "name": "Iranian Rial",
        "name_local": "ریال ایران",
        "symbol": "﷼",
        "symbol_native": "ریال",
        "decimal_places": 0,
        "symbol_position": "after",
        "thousand_separator": "٬",
        "decimal_separator": "٫",
        "is_active": true,
        "is_base": false,
        "rate_to_base": 0.0000238,
        "display_as_toman": true
      }
    }
  ],
  "meta": {
    "base_currency": "USD",
    "last_sync": "2025-12-28T10:00:00Z"
  }
}
```

#### GET /api/v1/currencies/{code}
Get single currency details.

#### POST /api/v1/admin/currencies
Create new currency.

**Request:**
```json
{
  "code": "AED",
  "name": "UAE Dirham",
  "name_local": "درهم امارات",
  "symbol": "د.إ",
  "decimal_places": 2,
  "symbol_position": "before",
  "is_active": true
}
```

#### PATCH /api/v1/admin/currencies/{code}
Update currency.

#### DELETE /api/v1/admin/currencies/{code}
Delete currency (soft delete).

### Exchange Rates

#### GET /api/v1/exchange-rates
Get current exchange rates.

**Query Parameters:**
- `base` (string): Base currency (default: organization base)
- `currencies` (string): Comma-separated target currencies
- `date` (date): Historical rate date

**Response (200):**
```json
{
  "data": {
    "base": "USD",
    "date": "2025-12-28",
    "rates": {
      "EUR": 0.9234,
      "GBP": 0.7891,
      "IRR": 42050.00,
      "AED": 3.6725
    }
  },
  "meta": {
    "provider": "exchangerate-api",
    "updated_at": "2025-12-28T10:00:00Z"
  }
}
```

#### GET /api/v1/exchange-rates/history
Get historical exchange rates.

**Query Parameters:**
- `base` (string): Base currency
- `target` (string): Target currency
- `start_date` (date): Start of period
- `end_date` (date): End of period

**Response (200):**
```json
{
  "data": [
    {
      "date": "2025-12-28",
      "rate": 42050.00,
      "source": "api"
    },
    {
      "date": "2025-12-27",
      "rate": 41980.00,
      "source": "api"
    }
  ],
  "meta": {
    "base": "USD",
    "target": "IRR",
    "period": {
      "start": "2025-12-01",
      "end": "2025-12-28"
    }
  }
}
```

#### POST /api/v1/admin/exchange-rates/sync
Trigger manual rate synchronization.

#### POST /api/v1/admin/exchange-rates
Create manual rate entry.

**Request:**
```json
{
  "base_currency": "USD",
  "target_currency": "IRR",
  "rate": 42100.00,
  "effective_date": "2025-12-28",
  "source": "manual",
  "notes": "Central bank rate"
}
```

### Currency Conversion

#### POST /api/v1/convert
Convert amount between currencies.

**Request:**
```json
{
  "amount": 1000.00,
  "from": "USD",
  "to": "IRR",
  "date": null
}
```

**Response (200):**
```json
{
  "data": {
    "original": {
      "amount": 1000.00,
      "currency": "USD",
      "formatted": "$1,000.00"
    },
    "converted": {
      "amount": 42050000,
      "currency": "IRR",
      "formatted": "۴۲,۰۵۰,۰۰۰ ریال",
      "formatted_toman": "۴,۲۰۵,۰۰۰ تومان"
    },
    "rate": {
      "value": 42050.00,
      "date": "2025-12-28",
      "source": "exchangerate-api"
    }
  }
}
```

#### POST /api/v1/convert/batch
Convert multiple amounts.

**Request:**
```json
{
  "conversions": [
    { "amount": 100, "from": "USD", "to": "EUR" },
    { "amount": 500, "from": "USD", "to": "IRR" },
    { "amount": 1000, "from": "EUR", "to": "GBP" }
  ]
}
```

### Formatting

#### POST /api/v1/format
Format money according to locale.

**Request:**
```json
{
  "amount": 1234567.89,
  "currency": "IRR",
  "locale": "fa",
  "options": {
    "display_as_toman": true,
    "use_persian_numerals": true,
    "compact": false
  }
}
```

**Response (200):**
```json
{
  "data": {
    "formatted": "۱۲۳,۴۵۶ تومان",
    "raw": 1234567.89,
    "currency": "IRR",
    "locale": "fa"
  }
}
```

## Data Schema

### Currency
```typescript
interface Currency {
  id: string;              // ISO 4217 code (lowercase)
  code: string;            // ISO 4217 code (uppercase)
  
  name: string;            // English name
  name_local: string;      // Localized name
  
  symbol: string;          // Primary symbol ($, €, £)
  symbol_native: string;   // Native symbol (ریال)
  
  decimal_places: number;  // 0-4
  symbol_position: 'before' | 'after';
  
  thousand_separator: string;
  decimal_separator: string;
  
  is_active: boolean;
  is_base: boolean;        // Is organization's base currency
  
  // Special handling
  display_as_toman?: boolean;  // IRR special case
  
  created_at: Date;
  updated_at: Date;
}

interface ExchangeRate {
  id: UUID;
  base_currency_code: string;
  target_currency_code: string;
  
  rate: number;            // Decimal precision
  inverse_rate: number;    // Calculated inverse
  
  effective_date: Date;
  fetched_at: Date;
  
  source: 'api' | 'manual' | 'import';
  provider?: string;       // API provider name
  
  notes?: string;          // For manual entries
  created_by?: UUID;       // For manual entries
}

interface ExchangeRateHistory {
  id: UUID;
  base_currency_code: string;
  target_currency_code: string;
  
  rate: number;
  date: Date;
  
  high?: number;           // Daily high
  low?: number;            // Daily low
  
  source: string;
}

interface CurrencySettings {
  base_currency: string;
  display_currency: string;
  
  rate_provider: 'exchangerate-api' | 'openexchangerates' | 'fixer' | 'manual';
  provider_api_key?: string;
  
  auto_sync: boolean;
  sync_interval_hours: number;
  
  rounding_mode: 'half-up' | 'half-down' | 'bankers' | 'floor' | 'ceiling';
  
  show_currency_symbol: boolean;
  compact_large_amounts: boolean;
  
  // Iranian-specific
  display_irr_as_toman: boolean;
  use_persian_numerals: boolean;
}

interface MoneyValue {
  amount: number;
  currency_code: string;
  formatted: string;
  
  converted?: {
    amount: number;
    currency_code: string;
    rate: number;
    rate_date: Date;
  };
}
```

## Database Schema

### currencies table
```sql
CREATE TABLE currencies (
    code VARCHAR(3) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    name_local VARCHAR(100),
    symbol VARCHAR(10) NOT NULL,
    symbol_native VARCHAR(20),
    decimal_places TINYINT DEFAULT 2,
    symbol_position ENUM('before', 'after') DEFAULT 'before',
    thousand_separator CHAR(1) DEFAULT ',',
    decimal_separator CHAR(1) DEFAULT '.',
    is_active BOOLEAN DEFAULT false,
    is_base BOOLEAN DEFAULT false,
    display_as_toman BOOLEAN DEFAULT false,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### exchange_rates table
```sql
CREATE TABLE exchange_rates (
    id UUID PRIMARY KEY,
    base_currency_code VARCHAR(3) NOT NULL,
    target_currency_code VARCHAR(3) NOT NULL,
    rate DECIMAL(20, 10) NOT NULL,
    inverse_rate DECIMAL(20, 10) GENERATED ALWAYS AS (1 / rate) STORED,
    effective_date DATE NOT NULL,
    fetched_at TIMESTAMP NOT NULL,
    source ENUM('api', 'manual', 'import') DEFAULT 'api',
    provider VARCHAR(50),
    notes TEXT,
    created_by UUID REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_rate (base_currency_code, target_currency_code, effective_date),
    FOREIGN KEY (base_currency_code) REFERENCES currencies(code),
    FOREIGN KEY (target_currency_code) REFERENCES currencies(code)
);
```

### exchange_rate_history table
```sql
CREATE TABLE exchange_rate_history (
    id UUID PRIMARY KEY,
    base_currency_code VARCHAR(3) NOT NULL,
    target_currency_code VARCHAR(3) NOT NULL,
    rate DECIMAL(20, 10) NOT NULL,
    high_rate DECIMAL(20, 10),
    low_rate DECIMAL(20, 10),
    date DATE NOT NULL,
    source VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_history (base_currency_code, target_currency_code, date),
    INDEX idx_date (date),
    FOREIGN KEY (base_currency_code) REFERENCES currencies(code),
    FOREIGN KEY (target_currency_code) REFERENCES currencies(code)
);
```

## Services

### CurrencyService
```php
<?php

namespace IshYar\Currencies\Services;

class CurrencyService
{
    public function getActive(): Collection;
    public function getBase(): Currency;
    public function find(string $code): ?Currency;
    public function activate(string $code): Currency;
    public function deactivate(string $code): Currency;
    public function setAsBase(string $code): Currency;
}
```

### ExchangeRateService
```php
<?php

namespace IshYar\Currencies\Services;

class ExchangeRateService
{
    public function getRate(string $from, string $to, ?Carbon $date = null): float;
    public function syncRates(): SyncResult;
    public function setManualRate(string $from, string $to, float $rate, Carbon $date): ExchangeRate;
    public function getHistory(string $from, string $to, Carbon $start, Carbon $end): Collection;
}
```

### MoneyFormatter
```php
<?php

namespace IshYar\Currencies\Services;

class MoneyFormatter
{
    public function format(float $amount, string $currency, ?string $locale = null): string;
    public function parse(string $formatted, string $currency): float;
    public function formatCompact(float $amount, string $currency): string;
    public function toToman(float $rialAmount): float;
    public function toRial(float $tomanAmount): float;
}
```

### CurrencyConverter
```php
<?php

namespace IshYar\Currencies\Services;

class CurrencyConverter
{
    public function convert(float $amount, string $from, string $to, ?Carbon $date = null): ConversionResult;
    public function convertBatch(array $conversions): array;
    public function getConversionRate(string $from, string $to, ?Carbon $date = null): float;
}
```

## Frontend Integration

### Vue Composable
```typescript
// composables/useCurrency.ts
export function useCurrency() {
  const { locale } = useI18n()
  const currencyStore = useCurrencyStore()
  
  const formatMoney = (amount: number, currency: string, options?: FormatOptions) => {
    const curr = currencyStore.getCurrency(currency)
    const formatter = new Intl.NumberFormat(locale.value, {
      style: 'currency',
      currency: currency,
      minimumFractionDigits: curr.decimal_places,
      maximumFractionDigits: curr.decimal_places,
    })
    
    let formatted = formatter.format(amount)
    
    // Handle Toman display
    if (currency === 'IRR' && options?.displayAsToman) {
      formatted = formatToman(amount / 10, locale.value)
    }
    
    // Persian numerals
    if (locale.value === 'fa' && options?.persianNumerals) {
      formatted = toPersianNumerals(formatted)
    }
    
    return formatted
  }
  
  const convert = async (amount: number, from: string, to: string) => {
    return await currencyStore.convert(amount, from, to)
  }
  
  return { formatMoney, convert, currencies: currencyStore.currencies }
}
```

### Currency Input Component
```vue
<template>
  <div class="currency-input" :class="{ 'rtl': isRtl }">
    <select v-model="selectedCurrency" class="currency-select">
      <option v-for="curr in currencies" :key="curr.code" :value="curr.code">
        {{ curr.symbol }} {{ curr.code }}
      </option>
    </select>
    <input
      type="text"
      v-model="displayValue"
      @blur="formatOnBlur"
      :dir="'ltr'"
      class="amount-input"
    />
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  modelValue: { amount: number; currency: string }
}>()

const emit = defineEmits<{
  'update:modelValue': [value: { amount: number; currency: string }]
}>()

const { formatMoney, currencies } = useCurrency()
</script>
```

### Currency Display Component
```vue
<template>
  <span class="money-display" :class="{ 'negative': amount < 0 }">
    <span v-if="showOriginal && convertedAmount" class="original">
      {{ formatMoney(amount, currency) }}
    </span>
    <span class="converted">
      {{ formatMoney(displayAmount, displayCurrency, formatOptions) }}
    </span>
  </span>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
  amount: number
  currency: string
  displayCurrency?: string
  showOriginal?: boolean
  compact?: boolean
  persianNumerals?: boolean
}>(), {
  showOriginal: false,
  compact: false,
  persianNumerals: false,
})
</script>
```

## CLI Commands

```bash
# Sync exchange rates
php artisan currencies:sync-rates
php artisan currencies:sync-rates --provider=openexchangerates

# Seed default currencies
php artisan currencies:seed-defaults
php artisan currencies:seed-defaults --activate=USD,EUR,IRR

# Cleanup old rate history
php artisan currencies:cleanup-history --keep-days=365

# Export/Import currencies
php artisan currencies:export --file=currencies.json
php artisan currencies:import --file=currencies.json
```

## Module Installer

```php
<?php

namespace IshYar\Currencies\Installer;

use IshYar\Core\Contracts\ModuleInstallerInterface;

class CurrenciesInstaller implements ModuleInstallerInterface
{
    public function getSteps(): array
    {
        return [
            [
                'id' => 'currencies',
                'title' => 'Base Currency',
                'description' => 'Select your organization\'s base currency',
            ],
            [
                'id' => 'rates',
                'title' => 'Exchange Rates',
                'description' => 'Configure exchange rate provider',
            ],
            [
                'id' => 'complete',
                'title' => 'Complete',
                'description' => 'Currency module installed',
            ],
        ];
    }
    
    public function getConfigSchema(): array
    {
        return [
            'base_currency' => [
                'type' => 'select',
                'label' => 'Base Currency',
                'options' => $this->getCurrencyOptions(),
                'default' => 'USD',
                'required' => true,
            ],
            'additional_currencies' => [
                'type' => 'multiselect',
                'label' => 'Additional Active Currencies',
                'options' => $this->getCurrencyOptions(),
                'default' => ['EUR', 'IRR'],
            ],
            'rate_provider' => [
                'type' => 'select',
                'label' => 'Exchange Rate Provider',
                'options' => [
                    'exchangerate-api' => 'ExchangeRate-API (Free)',
                    'openexchangerates' => 'Open Exchange Rates',
                    'fixer' => 'Fixer.io',
                    'manual' => 'Manual Entry Only',
                ],
                'default' => 'exchangerate-api',
            ],
            'auto_sync' => [
                'type' => 'toggle',
                'label' => 'Automatically sync exchange rates',
                'default' => true,
            ],
            'display_irr_as_toman' => [
                'type' => 'toggle',
                'label' => 'Display Iranian Rial as Toman',
                'default' => true,
            ],
        ];
    }
    
    public function install(array $config): InstallerResult
    {
        // Run migrations
        Artisan::call('migrate', [
            '--path' => 'modules/Currencies/database/migrations',
            '--force' => true,
        ]);
        
        // Seed currencies
        $this->seedCurrencies();
        
        // Set base currency
        $this->setBaseCurrency($config['base_currency']);
        
        // Activate additional currencies
        $this->activateCurrencies($config['additional_currencies']);
        
        // Configure provider
        $this->configureProvider($config);
        
        // Initial rate sync
        if ($config['auto_sync']) {
            Artisan::call('currencies:sync-rates');
        }
        
        return InstallerResult::success('Currencies module installed successfully');
    }
}
```

## Pre-seeded Currencies

| Code | Name | Symbol | Decimals | Notes |
|------|------|--------|----------|-------|
| USD | US Dollar | $ | 2 | Default base |
| EUR | Euro | € | 2 | |
| GBP | British Pound | £ | 2 | |
| IRR | Iranian Rial | ﷼ | 0 | Toman option |
| AED | UAE Dirham | د.إ | 2 | |
| TRY | Turkish Lira | ₺ | 2 | |
| SAR | Saudi Riyal | ﷼ | 2 | |
| INR | Indian Rupee | ₹ | 2 | |
| CNY | Chinese Yuan | ¥ | 2 | |
| JPY | Japanese Yen | ¥ | 0 | |
| RUB | Russian Ruble | ₽ | 2 | |
| CHF | Swiss Franc | CHF | 2 | |
| CAD | Canadian Dollar | $ | 2 | |
| AUD | Australian Dollar | $ | 2 | |
