/**
 * Reactive Jalali Date Composable
 * 
 * Provides a reactive wrapper around a date value with Jalali properties
 */

import { computed, ref, watch, type Ref } from 'vue'
import type { JalaliDate as JalaliDateType, JalaliDateFull } from '~/types/jalali.d'
import { JALALI_MONTH_NAMES, JALALI_DAY_NAMES } from '~/types/jalali.d'
import {
  toJalali,
  toGregorian,
  getJalaliDayOfWeek,
  isLeapJalaliYear,
  isJalaliToday,
  isJalaliPast,
  isJalaliFuture,
  addJalaliDays,
  addJalaliMonths,
  compareJalaliDates,
  getDaysInJalaliMonth,
} from '~/utils/jalali/jalali-converter'
import { toPersianNumerals } from '~/utils/jalali/persian-numerals'
import {
  formatJalali,
  formatJalaliRelative,
} from '~/utils/jalali/jalali-formatter'

export interface UseJalaliDateOptions {
  persianNumerals?: boolean
  format?: string
}

export function useJalaliDate(
  inputDate: Ref<Date | string | null | undefined> | Date | string | null | undefined,
  options: UseJalaliDateOptions = {}
) {
  const { persianNumerals = true, format: defaultFormat = 'j F Y' } = options
  
  // Normalize input to a ref
  const dateRef: Ref<Date | string | null | undefined> = 
    (inputDate && typeof inputDate === 'object' && 'value' in inputDate)
      ? inputDate
      : ref(inputDate)
  
  // Core Jalali date computed
  const jalaliDate = computed<JalaliDateType | null>(() => {
    if (!dateRef.value) return null
    return toJalali(dateRef.value as Date | string)
  })
  
  // Individual date parts
  const year = computed(() => jalaliDate.value?.year ?? 0)
  const month = computed(() => jalaliDate.value?.month ?? 0)
  const day = computed(() => jalaliDate.value?.day ?? 0)
  const hour = computed(() => jalaliDate.value?.hour ?? 0)
  const minute = computed(() => jalaliDate.value?.minute ?? 0)
  const second = computed(() => jalaliDate.value?.second ?? 0)
  
  // Day of week (0 = Saturday)
  const dayOfWeek = computed(() => {
    if (!jalaliDate.value) return 0
    return getJalaliDayOfWeek(jalaliDate.value)
  })
  
  // Day of year (1-366)
  const dayOfYear = computed(() => {
    if (!jalaliDate.value) return 0
    let days = 0
    for (let i = 1; i < month.value; i++) {
      days += getDaysInJalaliMonth(year.value, i)
    }
    return days + day.value
  })
  
  // Week of year
  const weekOfYear = computed(() => Math.ceil(dayOfYear.value / 7))
  
  // Is leap year
  const isLeapYear = computed(() => isLeapJalaliYear(year.value))
  
  // Month name in Persian
  const monthName = computed(() => JALALI_MONTH_NAMES[month.value] ?? '')
  
  // Day name in Persian
  const dayName = computed(() => JALALI_DAY_NAMES[dayOfWeek.value] ?? '')
  
  // Formatted date string
  const formatted = computed(() => {
    if (!jalaliDate.value) return ''
    return formatJalali(jalaliDate.value, defaultFormat, persianNumerals)
  })
  
  // Short formatted (Y/m/d)
  const formattedShort = computed(() => {
    if (!jalaliDate.value) return ''
    return formatJalali(jalaliDate.value, 'Y/m/d', persianNumerals)
  })
  
  // Long formatted (l، j F Y)
  const formattedLong = computed(() => {
    if (!jalaliDate.value) return ''
    return formatJalali(jalaliDate.value, 'l، j F Y', persianNumerals)
  })
  
  // Relative time string
  const relative = computed(() => {
    if (!dateRef.value) return ''
    return formatJalaliRelative(dateRef.value as Date | string, persianNumerals)
  })
  
  // ISO string (Gregorian)
  const iso = computed(() => {
    if (!jalaliDate.value) return ''
    return toGregorian(jalaliDate.value).toISOString()
  })
  
  // Gregorian date
  const gregorian = computed(() => {
    if (!jalaliDate.value) return null
    return toGregorian(jalaliDate.value)
  })
  
  // Boolean checks
  const isToday = computed(() => jalaliDate.value ? isJalaliToday(jalaliDate.value) : false)
  const isPast = computed(() => jalaliDate.value ? isJalaliPast(jalaliDate.value) : false)
  const isFuture = computed(() => jalaliDate.value ? isJalaliFuture(jalaliDate.value) : false)
  const isOverdue = isPast // alias
  
  // Methods
  const format = (pattern: string, usePersiaNumerals?: boolean) => {
    if (!jalaliDate.value) return ''
    return formatJalali(jalaliDate.value, pattern, usePersiaNumerals ?? persianNumerals)
  }
  
  const addDays = (days: number): JalaliDateType | null => {
    if (!jalaliDate.value) return null
    return addJalaliDays(jalaliDate.value, days)
  }
  
  const addMonths = (months: number): JalaliDateType | null => {
    if (!jalaliDate.value) return null
    return addJalaliMonths(jalaliDate.value, months)
  }
  
  const compare = (other: JalaliDateType | Date | string): number => {
    if (!jalaliDate.value) return 0
    const otherJalali = 'year' in (other as any) ? other as JalaliDateType : toJalali(other as Date | string)
    return compareJalaliDates(jalaliDate.value, otherJalali)
  }
  
  const isAfter = (other: JalaliDateType | Date | string): boolean => compare(other) > 0
  const isBefore = (other: JalaliDateType | Date | string): boolean => compare(other) < 0
  const isSame = (other: JalaliDateType | Date | string): boolean => compare(other) === 0
  
  return {
    // Raw Jalali date object
    jalaliDate,
    
    // Date parts
    year,
    month,
    day,
    hour,
    minute,
    second,
    
    // Computed values
    dayOfWeek,
    dayOfYear,
    weekOfYear,
    isLeapYear,
    
    // Names
    monthName,
    dayName,
    
    // Formatted strings
    formatted,
    formattedShort,
    formattedLong,
    relative,
    iso,
    
    // Gregorian
    gregorian,
    
    // Boolean checks
    isToday,
    isPast,
    isFuture,
    isOverdue,
    
    // Methods
    format,
    addDays,
    addMonths,
    compare,
    isAfter,
    isBefore,
    isSame,
  }
}
