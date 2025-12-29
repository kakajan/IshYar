/**
 * Jalali/Persian Calendar Types
 */

export interface JalaliDate {
  year: number
  month: number
  day: number
  hour?: number
  minute?: number
  second?: number
}

export interface JalaliDateFull extends JalaliDate {
  dayOfWeek: number
  dayOfYear: number
  weekOfYear: number
  isLeapYear: boolean
  monthName: string
  dayName: string
  formatted: string
  gregorian: string
}

export interface JalaliSettings {
  enabled: boolean
  calendar_system: 'auto' | 'jalali' | 'gregorian'
  persian_numerals: boolean
  first_day_of_week: 'saturday' | 'sunday' | 'monday'
  show_gregorian_alongside: boolean
  holiday_highlight: boolean
}

export interface Holiday {
  id: string
  date: string
  gregorian: string
  title: string
  title_en: string
  description?: string
  type: 'national' | 'religious' | 'international' | 'custom'
  is_holiday: boolean
}

export interface CalendarDay {
  day: number
  dayOfWeek: number
  isToday: boolean
  isHoliday: boolean
  isWeekend: boolean
  isDisabled?: boolean
  isSelected?: boolean
  isInRange?: boolean
  isRangeStart?: boolean
  isRangeEnd?: boolean
  isOtherMonth?: boolean
  gregorian: string
  holidayInfo?: Holiday
}

export interface CalendarMonth {
  year: number
  month: number
  monthName: string
  daysInMonth: number
  firstDayOfWeek: number
  isLeapYear: boolean
  days: CalendarDay[]
  holidays: Holiday[]
}

export type JalaliFormat = 'short' | 'medium' | 'long' | 'full' | 'custom'

export interface JalaliDatePickerProps {
  modelValue?: Date | string | null
  minDate?: Date | string
  maxDate?: Date | string
  disabledDates?: (string | Date)[]
  disabledDays?: number[] // 0-6, Saturday=0
  showHolidays?: boolean
  persianNumerals?: boolean
  placeholder?: string
  format?: string
  disabled?: boolean
  readonly?: boolean
}

export interface JalaliDateRangePickerProps extends JalaliDatePickerProps {
  start?: Date | string | null
  end?: Date | string | null
  maxRange?: number // Max days in range
  presets?: RangePreset[]
}

export interface RangePreset {
  label: string
  value: string
  getRange: () => { start: Date; end: Date }
}

export interface JalaliCalendarProps {
  modelValue?: Date | string | null
  minDate?: Date | string
  maxDate?: Date | string
  disabledDates?: (string | Date)[]
  disabledDays?: number[]
  showHolidays?: boolean
  persianNumerals?: boolean
  multiple?: boolean
  range?: boolean
}

/**
 * Persian month names
 */
export const JALALI_MONTH_NAMES: Record<number, string> = {
  1: 'فروردین',
  2: 'اردیبهشت',
  3: 'خرداد',
  4: 'تیر',
  5: 'مرداد',
  6: 'شهریور',
  7: 'مهر',
  8: 'آبان',
  9: 'آذر',
  10: 'دی',
  11: 'بهمن',
  12: 'اسفند',
}

export const JALALI_MONTH_NAMES_EN: Record<number, string> = {
  1: 'Farvardin',
  2: 'Ordibehesht',
  3: 'Khordad',
  4: 'Tir',
  5: 'Mordad',
  6: 'Shahrivar',
  7: 'Mehr',
  8: 'Aban',
  9: 'Azar',
  10: 'Dey',
  11: 'Bahman',
  12: 'Esfand',
}

/**
 * Persian day names (0 = Saturday)
 */
export const JALALI_DAY_NAMES: Record<number, string> = {
  0: 'شنبه',
  1: 'یکشنبه',
  2: 'دوشنبه',
  3: 'سه‌شنبه',
  4: 'چهارشنبه',
  5: 'پنجشنبه',
  6: 'جمعه',
}

export const JALALI_DAY_NAMES_SHORT: Record<number, string> = {
  0: 'ش',
  1: 'ی',
  2: 'د',
  3: 'س',
  4: 'چ',
  5: 'پ',
  6: 'ج',
}

/**
 * Days in each Jalali month (normal year)
 */
export const JALALI_DAYS_IN_MONTH: Record<number, number> = {
  1: 31,
  2: 31,
  3: 31,
  4: 31,
  5: 31,
  6: 31,
  7: 30,
  8: 30,
  9: 30,
  10: 30,
  11: 30,
  12: 29, // 30 in leap year
}
