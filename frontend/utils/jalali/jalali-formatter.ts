/**
 * Jalali Date Formatting Utilities
 */

import {
  JALALI_MONTH_NAMES,
  JALALI_DAY_NAMES,
  type JalaliDate,
} from '~/types/jalali.d'
import {
  toJalali,
  getJalaliDayOfWeek,
} from './jalali-converter'
import { toPersianNumerals } from './persian-numerals'

/**
 * Format a Jalali date according to a pattern
 * 
 * Supported format characters:
 * Y - 4-digit year (1403)
 * y - 2-digit year (03)
 * m - Month with leading zero (01-12)
 * n - Month without leading zero (1-12)
 * d - Day with leading zero (01-31)
 * j - Day without leading zero (1-31)
 * F - Full Persian month name (فروردین)
 * M - Short month name (first 3 chars)
 * l - Full Persian day name (شنبه)
 * D - Short day name (first char)
 * H - Hour with leading zero (00-23)
 * i - Minute with leading zero
 * s - Second with leading zero
 */
export function formatJalali(
  date: Date | string | JalaliDate,
  pattern: string = 'Y/m/d',
  persianNumerals: boolean = false
): string {
  const jDate: JalaliDate = 'year' in (date as any)
    ? date as JalaliDate
    : toJalali(date as Date | string)
  
  const dayOfWeek = getJalaliDayOfWeek(jDate)
  
  const replacements: Record<string, string> = {
    'Y': String(jDate.year).padStart(4, '0'),
    'y': String(jDate.year % 100).padStart(2, '0'),
    'm': String(jDate.month).padStart(2, '0'),
    'n': String(jDate.month),
    'd': String(jDate.day).padStart(2, '0'),
    'j': String(jDate.day),
    'F': JALALI_MONTH_NAMES[jDate.month] ?? '',
    'M': (JALALI_MONTH_NAMES[jDate.month] ?? '').substring(0, 3),
    'l': JALALI_DAY_NAMES[dayOfWeek] ?? '',
    'D': (JALALI_DAY_NAMES[dayOfWeek] ?? '').substring(0, 1),
    'H': String(jDate.hour ?? 0).padStart(2, '0'),
    'i': String(jDate.minute ?? 0).padStart(2, '0'),
    's': String(jDate.second ?? 0).padStart(2, '0'),
  }
  
  // Replace format characters
  let result = pattern
  for (const [key, value] of Object.entries(replacements)) {
    result = result.replace(new RegExp(key, 'g'), value)
  }
  
  if (persianNumerals) {
    result = toPersianNumerals(result)
  }
  
  return result
}

/**
 * Format a date in short format (Y/m/d)
 */
export function formatJalaliShort(
  date: Date | string | JalaliDate,
  persianNumerals: boolean = true
): string {
  return formatJalali(date, 'Y/m/d', persianNumerals)
}

/**
 * Format a date in medium format (j F Y)
 */
export function formatJalaliMedium(
  date: Date | string | JalaliDate,
  persianNumerals: boolean = true
): string {
  return formatJalali(date, 'j F Y', persianNumerals)
}

/**
 * Format a date in long format (l، j F Y)
 */
export function formatJalaliLong(
  date: Date | string | JalaliDate,
  persianNumerals: boolean = true
): string {
  return formatJalali(date, 'l، j F Y', persianNumerals)
}

/**
 * Format a date in full format (l، j F Y H:i)
 */
export function formatJaraliFull(
  date: Date | string | JalaliDate,
  persianNumerals: boolean = true
): string {
  return formatJalali(date, 'l، j F Y H:i', persianNumerals)
}

/**
 * Format relative time in Persian
 */
export function formatJalaliRelative(
  date: Date | string,
  persianNumerals: boolean = true
): string {
  const d = typeof date === 'string' ? new Date(date) : date
  const now = new Date()
  const diff = d.getTime() - now.getTime()
  const absDiff = Math.abs(diff)
  const isPast = diff < 0
  
  const seconds = Math.floor(absDiff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)
  const weeks = Math.floor(days / 7)
  const months = Math.floor(days / 30)
  const years = Math.floor(days / 365)
  
  let result: string
  
  if (days === 0) {
    result = 'امروز'
  } else if (days === 1) {
    result = isPast ? 'دیروز' : 'فردا'
  } else if (years > 0) {
    result = isPast ? `${years} سال پیش` : `${years} سال دیگر`
  } else if (months > 0) {
    result = isPast ? `${months} ماه پیش` : `${months} ماه دیگر`
  } else if (weeks > 0) {
    result = isPast ? `${weeks} هفته پیش` : `${weeks} هفته دیگر`
  } else {
    result = isPast ? `${days} روز پیش` : `${days} روز دیگر`
  }
  
  if (persianNumerals) {
    result = toPersianNumerals(result)
  }
  
  return result
}

/**
 * Format a date range in Jalali
 */
export function formatJalaliRange(
  start: Date | string,
  end: Date | string,
  persianNumerals: boolean = true
): string {
  const startJalali = toJalali(start)
  const endJalali = toJalali(end)
  
  // Same day
  if (
    startJalali.year === endJalali.year &&
    startJalali.month === endJalali.month &&
    startJalali.day === endJalali.day
  ) {
    return formatJalali(startJalali, 'j F Y', persianNumerals)
  }
  
  // Same month and year
  if (startJalali.year === endJalali.year && startJalali.month === endJalali.month) {
    const startDay = persianNumerals ? toPersianNumerals(startJalali.day) : String(startJalali.day)
    const endDay = persianNumerals ? toPersianNumerals(endJalali.day) : String(endJalali.day)
    const year = persianNumerals ? toPersianNumerals(startJalali.year) : String(startJalali.year)
    return `${startDay} تا ${endDay} ${JALALI_MONTH_NAMES[startJalali.month]} ${year}`
  }
  
  // Same year
  if (startJalali.year === endJalali.year) {
    const startDay = persianNumerals ? toPersianNumerals(startJalali.day) : String(startJalali.day)
    const endDay = persianNumerals ? toPersianNumerals(endJalali.day) : String(endJalali.day)
    const year = persianNumerals ? toPersianNumerals(startJalali.year) : String(startJalali.year)
    return `${startDay} ${JALALI_MONTH_NAMES[startJalali.month]} تا ${endDay} ${JALALI_MONTH_NAMES[endJalali.month]} ${year}`
  }
  
  // Different years
  return `${formatJalali(startJalali, 'j F Y', persianNumerals)} تا ${formatJalali(endJalali, 'j F Y', persianNumerals)}`
}

/**
 * Format age in Persian
 */
export function formatJalaliAge(
  birthDate: Date | string,
  persianNumerals: boolean = true
): string {
  const birth = toJalali(birthDate)
  const now = toJalali(new Date())
  
  let age = now.year - birth.year
  
  // Adjust if birthday hasn't occurred this year
  if (now.month < birth.month || (now.month === birth.month && now.day < birth.day)) {
    age--
  }
  
  age = Math.max(0, age)
  const ageStr = persianNumerals ? toPersianNumerals(age) : String(age)
  
  return `سن: ${ageStr} سال`
}

/**
 * Format duration between two dates in Persian
 */
export function formatJalaliDuration(
  start: Date | string,
  end: Date | string,
  persianNumerals: boolean = true
): string {
  const startDate = typeof start === 'string' ? new Date(start) : start
  const endDate = typeof end === 'string' ? new Date(end) : end
  
  const diff = Math.abs(endDate.getTime() - startDate.getTime())
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  
  const parts: string[] = []
  
  const years = Math.floor(days / 365)
  const remainingDaysAfterYears = days % 365
  const months = Math.floor(remainingDaysAfterYears / 30)
  const remainingDays = remainingDaysAfterYears % 30
  
  if (years > 0) {
    const yearsStr = persianNumerals ? toPersianNumerals(years) : String(years)
    parts.push(`${yearsStr} سال`)
  }
  
  if (months > 0 && parts.length < 2) {
    const monthsStr = persianNumerals ? toPersianNumerals(months) : String(months)
    parts.push(`${monthsStr} ماه`)
  }
  
  if (remainingDays > 0 && parts.length < 2) {
    if (remainingDays >= 7) {
      const weeks = Math.floor(remainingDays / 7)
      const weeksStr = persianNumerals ? toPersianNumerals(weeks) : String(weeks)
      parts.push(`${weeksStr} هفته`)
    } else {
      const daysStr = persianNumerals ? toPersianNumerals(remainingDays) : String(remainingDays)
      parts.push(`${daysStr} روز`)
    }
  }
  
  if (parts.length === 0) {
    return persianNumerals ? '۰ روز' : '0 روز'
  }
  
  return parts.join(' و ')
}
