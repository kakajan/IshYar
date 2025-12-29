/**
 * Jalali Calendar Composable
 * 
 * Provides Jalali date utilities and formatting functions
 */

import { computed, ref } from 'vue'
import type { JalaliDate, JalaliSettings } from '~/types/jalali.d'
import {
  toJalali,
  toGregorian,
  parseJalaliString,
  isLeapJalaliYear,
  getDaysInJalaliMonth,
  isValidJalaliDate,
  getJalaliToday,
  getCurrentJalaliYear,
  getCurrentJalaliMonth,
} from '~/utils/jalali/jalali-converter'
import {
  toPersianNumerals,
  toArabicNumerals,
} from '~/utils/jalali/persian-numerals'
import {
  formatJalali,
  formatJalaliShort,
  formatJalaliMedium,
  formatJalaliLong,
  formatJalaliRelative,
  formatJalaliRange,
  formatJalaliAge,
  formatJalaliDuration,
} from '~/utils/jalali/jalali-formatter'

export function useJalali() {
  const { locale } = useI18n()
  
  // Settings state (can be loaded from API)
  const settings = ref<JalaliSettings>({
    enabled: true,
    calendar_system: 'auto',
    persian_numerals: true,
    first_day_of_week: 'saturday',
    show_gregorian_alongside: false,
    holiday_highlight: true,
  })
  
  // Computed: Check if Jalali should be used based on settings
  const isJalaliEnabled = computed(() => {
    if (!settings.value.enabled) return false
    if (settings.value.calendar_system === 'jalali') return true
    if (settings.value.calendar_system === 'gregorian') return false
    // Auto: use Jalali for Persian locale
    return locale.value === 'fa'
  })
  
  // Computed: Use Persian numerals
  const usePersianNumerals = computed(() => 
    isJalaliEnabled.value && settings.value.persian_numerals
  )
  
  /**
   * Load settings from API
   */
  const loadSettings = async () => {
    try {
      const { data } = await useApi<{ data: JalaliSettings }>('/api/v1/jalali/settings')
      if (data.value?.data) {
        settings.value = data.value.data
      }
    } catch (error) {
      console.error('Failed to load Jalali settings:', error)
    }
  }
  
  /**
   * Update settings via API
   */
  const updateSettings = async (newSettings: Partial<JalaliSettings>) => {
    try {
      const { data } = await useApi<{ data: JalaliSettings }>('/api/v1/jalali/settings', {
        method: 'PATCH',
        body: newSettings,
      })
      if (data.value?.data) {
        settings.value = data.value.data
      }
    } catch (error) {
      console.error('Failed to update Jalali settings:', error)
    }
  }
  
  /**
   * Format a date according to current settings
   */
  const format = (
    date: Date | string | JalaliDate,
    pattern: string = 'Y/m/d'
  ): string => {
    if (!isJalaliEnabled.value) {
      const d = 'year' in (date as any) ? toGregorian(date as JalaliDate) : new Date(date as any)
      return d.toLocaleDateString('en-US')
    }
    return formatJalali(date, pattern, usePersianNumerals.value)
  }
  
  /**
   * Format date in short format
   */
  const formatShort = (date: Date | string): string => {
    if (!isJalaliEnabled.value) {
      return new Date(date).toLocaleDateString('en-US')
    }
    return formatJalaliShort(date, usePersianNumerals.value)
  }
  
  /**
   * Format date in medium format
   */
  const formatMedium = (date: Date | string): string => {
    if (!isJalaliEnabled.value) {
      return new Date(date).toLocaleDateString('en-US', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
      })
    }
    return formatJalaliMedium(date, usePersianNumerals.value)
  }
  
  /**
   * Format date in long format
   */
  const formatLong = (date: Date | string): string => {
    if (!isJalaliEnabled.value) {
      return new Date(date).toLocaleDateString('en-US', { 
        weekday: 'long',
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
      })
    }
    return formatJalaliLong(date, usePersianNumerals.value)
  }
  
  /**
   * Format relative time
   */
  const formatRelative = (date: Date | string): string => {
    if (!isJalaliEnabled.value) {
      const d = new Date(date)
      const now = new Date()
      const diff = d.getTime() - now.getTime()
      const days = Math.floor(Math.abs(diff) / (1000 * 60 * 60 * 24))
      
      if (days === 0) return 'Today'
      if (days === 1) return diff < 0 ? 'Yesterday' : 'Tomorrow'
      return diff < 0 ? `${days} days ago` : `In ${days} days`
    }
    return formatJalaliRelative(date, usePersianNumerals.value)
  }
  
  /**
   * Format date range
   */
  const formatRange = (start: Date | string, end: Date | string): string => {
    if (!isJalaliEnabled.value) {
      const s = new Date(start)
      const e = new Date(end)
      return `${s.toLocaleDateString('en-US')} – ${e.toLocaleDateString('en-US')}`
    }
    return formatJalaliRange(start, end, usePersianNumerals.value)
  }
  
  /**
   * Format age
   */
  const formatAge = (birthDate: Date | string): string => {
    if (!isJalaliEnabled.value) {
      const birth = new Date(birthDate)
      const now = new Date()
      let age = now.getFullYear() - birth.getFullYear()
      if (now.getMonth() < birth.getMonth() || 
          (now.getMonth() === birth.getMonth() && now.getDate() < birth.getDate())) {
        age--
      }
      return `Age: ${age} years`
    }
    return formatJalaliAge(birthDate, usePersianNumerals.value)
  }
  
  /**
   * Format duration
   */
  const formatDuration = (start: Date | string, end: Date | string): string => {
    return formatJalaliDuration(start, end, usePersianNumerals.value)
  }
  
  return {
    // Settings
    settings,
    isJalaliEnabled,
    usePersianNumerals,
    loadSettings,
    updateSettings,
    
    // Conversion
    toJalali,
    toGregorian,
    parseJalaliString,
    
    // Validation
    isLeapJalaliYear,
    getDaysInJalaliMonth,
    isValidJalaliDate,
    
    // Current date
    getJalaliToday,
    getCurrentJalaliYear,
    getCurrentJalaliMonth,
    
    // Formatting
    format,
    formatShort,
    formatMedium,
    formatLong,
    formatRelative,
    formatRange,
    formatAge,
    formatDuration,
    
    // Numeral conversion
    toPersianNumerals,
    toArabicNumerals,
  }
}
