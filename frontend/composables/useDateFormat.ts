/**
 * Composable for common date and time formatting utilities
 * Supports both English and Persian (Jalali) date formats
 */
export const useDateFormat = () => {
  const { locale } = useI18n()

  /**
   * Format date in localized short format
   * @param dateStr - ISO date string or Date object
   * @returns Formatted date string
   */
  const formatDate = (dateStr: string | Date | null | undefined): string => {
    if (!dateStr) return '—'
    const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr
    return date.toLocaleDateString(locale.value === 'fa' ? 'fa-IR' : 'en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  }

  /**
   * Format date with time in localized format
   * @param dateStr - ISO date string or Date object
   * @returns Formatted datetime string
   */
  const formatDateTime = (dateStr: string | Date | null | undefined): string => {
    if (!dateStr) return '—'
    const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr
    return date.toLocaleString(locale.value === 'fa' ? 'fa-IR' : 'en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  }

  /**
   * Format time only
   * @param dateStr - ISO date string or Date object
   * @returns Formatted time string
   */
  const formatTime = (dateStr: string | Date | null | undefined): string => {
    if (!dateStr) return '—'
    const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr
    return date.toLocaleTimeString(locale.value === 'fa' ? 'fa-IR' : 'en-US', {
      hour: '2-digit',
      minute: '2-digit',
    })
  }

  /**
   * Get relative time (e.g., "2 hours ago", "in 3 days")
   * @param dateStr - ISO date string or Date object
   * @returns Relative time string
   */
  const formatRelative = (dateStr: string | Date | null | undefined): string => {
    if (!dateStr) return '—'
    const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr
    const now = new Date()
    const diff = date.getTime() - now.getTime()
    const absDiff = Math.abs(diff)
    const isPast = diff < 0

    const seconds = Math.floor(absDiff / 1000)
    const minutes = Math.floor(seconds / 60)
    const hours = Math.floor(minutes / 60)
    const days = Math.floor(hours / 24)
    const weeks = Math.floor(days / 7)
    const months = Math.floor(days / 30)
    const years = Math.floor(days / 365)

    const rtf = new Intl.RelativeTimeFormat(locale.value === 'fa' ? 'fa-IR' : 'en-US', {
      numeric: 'auto',
    })

    if (years > 0) return rtf.format(isPast ? -years : years, 'year')
    if (months > 0) return rtf.format(isPast ? -months : months, 'month')
    if (weeks > 0) return rtf.format(isPast ? -weeks : weeks, 'week')
    if (days > 0) return rtf.format(isPast ? -days : days, 'day')
    if (hours > 0) return rtf.format(isPast ? -hours : hours, 'hour')
    if (minutes > 0) return rtf.format(isPast ? -minutes : minutes, 'minute')
    return rtf.format(isPast ? -seconds : seconds, 'second')
  }

  /**
   * Check if a date is overdue
   * @param dateStr - ISO date string or Date object
   * @returns True if the date is in the past
   */
  const isOverdue = (dateStr: string | Date | null | undefined): boolean => {
    if (!dateStr) return false
    const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr
    return date < new Date()
  }

  /**
   * Check if a date is today
   * @param dateStr - ISO date string or Date object
   * @returns True if the date is today
   */
  const isToday = (dateStr: string | Date | null | undefined): boolean => {
    if (!dateStr) return false
    const date = typeof dateStr === 'string' ? new Date(dateStr) : dateStr
    const today = new Date()
    return (
      date.getDate() === today.getDate() &&
      date.getMonth() === today.getMonth() &&
      date.getFullYear() === today.getFullYear()
    )
  }

  /**
   * Format date range
   * @param startDate - Start date
   * @param endDate - End date
   * @returns Formatted date range string
   */
  const formatDateRange = (
    startDate: string | Date | null | undefined,
    endDate: string | Date | null | undefined
  ): string => {
    const start = formatDate(startDate)
    const end = formatDate(endDate)
    if (start === '—' && end === '—') return '—'
    if (start === '—') return `Until ${end}`
    if (end === '—') return `From ${start}`
    return `${start} – ${end}`
  }

  return {
    formatDate,
    formatDateTime,
    formatTime,
    formatRelative,
    isOverdue,
    isToday,
    formatDateRange,
  }
}
