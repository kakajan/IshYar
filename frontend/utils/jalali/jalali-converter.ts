/**
 * Jalali (Persian/Solar Hijri) Calendar Conversion Utilities
 * Using jalaali-js library for core conversion
 */

import jalaali from 'jalaali-js'
import type { JalaliDate } from '~/types/jalali.d'
import { toArabicNumerals } from './persian-numerals'

/**
 * Convert Gregorian date to Jalali
 */
export function toJalali(date: Date | string): JalaliDate {
  const d = typeof date === 'string' ? new Date(date) : date
  const { jy, jm, jd } = jalaali.toJalaali(d.getFullYear(), d.getMonth() + 1, d.getDate())
  
  return {
    year: jy,
    month: jm,
    day: jd,
    hour: d.getHours(),
    minute: d.getMinutes(),
    second: d.getSeconds(),
  }
}

/**
 * Convert Jalali date to Gregorian
 */
export function toGregorian(jDate: JalaliDate): Date {
  const { gy, gm, gd } = jalaali.toGregorian(jDate.year, jDate.month, jDate.day)
  
  return new Date(
    gy,
    gm - 1,
    gd,
    jDate.hour ?? 0,
    jDate.minute ?? 0,
    jDate.second ?? 0
  )
}

/**
 * Parse a Jalali date string to JalaliDate object
 * Supports formats: Y/m/d, Y-m-d, Y/m/d H:i:s
 */
export function parseJalaliString(dateString: string): JalaliDate {
  // Convert Persian numerals to Arabic
  const normalized = toArabicNumerals(dateString.trim())
  
  // Split date and time
  const parts = normalized.split(/[\s]+/)
  const datePart = parts[0] ?? ''
  const timePart = parts[1] ?? '00:00:00'
  
  // Parse date
  const dateComponents = datePart.split(/[\/\-]/)
  const year = parseInt(dateComponents[0] ?? '0', 10)
  const month = parseInt(dateComponents[1] ?? '1', 10)
  const day = parseInt(dateComponents[2] ?? '1', 10)
  
  // Parse time
  const timeComponents = timePart.split(':')
  const hour = parseInt(timeComponents[0] ?? '0', 10)
  const minute = parseInt(timeComponents[1] ?? '0', 10)
  const second = parseInt(timeComponents[2] ?? '0', 10)
  
  return { year, month, day, hour, minute, second }
}

/**
 * Check if a Jalali year is a leap year
 */
export function isLeapJalaliYear(year: number): boolean {
  return jalaali.isLeapJalaaliYear(year)
}

/**
 * Get the number of days in a Jalali month
 */
export function getDaysInJalaliMonth(year: number, month: number): number {
  return jalaali.jalaaliMonthLength(year, month)
}

/**
 * Validate a Jalali date
 */
export function isValidJalaliDate(year: number, month: number, day: number): boolean {
  if (year < 1 || month < 1 || month > 12 || day < 1) {
    return false
  }
  
  const daysInMonth = getDaysInJalaliMonth(year, month)
  return day <= daysInMonth
}

/**
 * Get today's date in Jalali
 */
export function getJalaliToday(): JalaliDate {
  return toJalali(new Date())
}

/**
 * Get the current Jalali year
 */
export function getCurrentJalaliYear(): number {
  return toJalali(new Date()).year
}

/**
 * Get the current Jalali month
 */
export function getCurrentJalaliMonth(): number {
  return toJalali(new Date()).month
}

/**
 * Get day of week for a Jalali date (0 = Saturday, 6 = Friday)
 */
export function getJalaliDayOfWeek(jDate: JalaliDate): number {
  const gregorian = toGregorian(jDate)
  const dayOfWeek = gregorian.getDay() // 0 = Sunday
  
  // Convert to Jalali week (0 = Saturday)
  return (dayOfWeek + 1) % 7
}

/**
 * Compare two Jalali dates
 * Returns: -1 if a < b, 0 if a == b, 1 if a > b
 */
export function compareJalaliDates(a: JalaliDate, b: JalaliDate): number {
  if (a.year !== b.year) return a.year < b.year ? -1 : 1
  if (a.month !== b.month) return a.month < b.month ? -1 : 1
  if (a.day !== b.day) return a.day < b.day ? -1 : 1
  return 0
}

/**
 * Check if two Jalali dates are equal (only date part)
 */
export function areJalaliDatesEqual(a: JalaliDate, b: JalaliDate): boolean {
  return a.year === b.year && a.month === b.month && a.day === b.day
}

/**
 * Check if a Jalali date is today
 */
export function isJalaliToday(jDate: JalaliDate): boolean {
  return areJalaliDatesEqual(jDate, getJalaliToday())
}

/**
 * Check if a Jalali date is in the past
 */
export function isJalaliPast(jDate: JalaliDate): boolean {
  return toGregorian(jDate) < new Date()
}

/**
 * Check if a Jalali date is in the future
 */
export function isJalaliFuture(jDate: JalaliDate): boolean {
  return toGregorian(jDate) > new Date()
}

/**
 * Add days to a Jalali date
 */
export function addJalaliDays(jDate: JalaliDate, days: number): JalaliDate {
  const gregorian = toGregorian(jDate)
  gregorian.setDate(gregorian.getDate() + days)
  return toJalali(gregorian)
}

/**
 * Add months to a Jalali date
 */
export function addJalaliMonths(jDate: JalaliDate, months: number): JalaliDate {
  let year = jDate.year
  let month = jDate.month + months
  
  while (month > 12) {
    month -= 12
    year++
  }
  while (month < 1) {
    month += 12
    year--
  }
  
  // Adjust day if it exceeds the days in the new month
  const daysInMonth = getDaysInJalaliMonth(year, month)
  const day = Math.min(jDate.day, daysInMonth)
  
  return { ...jDate, year, month, day }
}

/**
 * Get the start of a Jalali month
 */
export function startOfJalaliMonth(jDate: JalaliDate): JalaliDate {
  return { ...jDate, day: 1 }
}

/**
 * Get the end of a Jalali month
 */
export function endOfJalaliMonth(jDate: JalaliDate): JalaliDate {
  const daysInMonth = getDaysInJalaliMonth(jDate.year, jDate.month)
  return { ...jDate, day: daysInMonth }
}

/**
 * Get difference in days between two Jalali dates
 */
export function diffInDays(a: JalaliDate, b: JalaliDate): number {
  const dateA = toGregorian(a)
  const dateB = toGregorian(b)
  const diffTime = Math.abs(dateB.getTime() - dateA.getTime())
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

// End of file
