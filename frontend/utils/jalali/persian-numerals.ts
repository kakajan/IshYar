/**
 * Persian Numerals Utility
 */

const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹']
const ARABIC_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']

/**
 * Convert Arabic/Western numerals to Persian
 */
export function toPersianNumerals(input: string | number): string {
  return String(input).replace(/[0-9]/g, (d) => PERSIAN_DIGITS[parseInt(d, 10)] ?? d)
}

/**
 * Convert Persian numerals to Arabic/Western
 */
export function toArabicNumerals(input: string): string {
  const map: Record<string, string> = {
    '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
    '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9',
  }
  return input.replace(/[۰-۹]/g, (d) => map[d] ?? d)
}

/**
 * Check if a string contains Persian numerals
 */
export function hasPersianNumerals(input: string): boolean {
  return /[۰-۹]/.test(input)
}

/**
 * Format a number with Persian numerals
 */
export function formatNumberPersian(value: number, options?: Intl.NumberFormatOptions): string {
  const formatted = new Intl.NumberFormat('fa-IR', options).format(value)
  return formatted
}

/**
 * Parse a Persian numeral string to number
 */
export function parsePersianNumber(input: string): number {
  const arabicStr = toArabicNumerals(input.replace(/[^\d۰-۹.-]/g, ''))
  return parseFloat(arabicStr)
}

/**
 * Format currency with Persian numerals
 */
export function formatCurrency(value: number, currency: string = 'IRR'): string {
  return formatCurrencyPersian(value, currency)
}

/**
 * Format currency with Persian numerals (Deprecated alias)
 */
export function formatCurrencyPersian(value: number, currency: string = 'IRR'): string {
  return new Intl.NumberFormat('fa-IR', {
    style: 'currency',
    currency,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value)
}

/**
 * Format percentage with Persian numerals
 */
export function formatPercentPersian(value: number): string {
  return new Intl.NumberFormat('fa-IR', {
    style: 'percent',
    minimumFractionDigits: 0,
    maximumFractionDigits: 1,
  }).format(value)
}
