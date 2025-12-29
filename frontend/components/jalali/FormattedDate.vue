<script setup lang="ts">
/**
 * Formatted Date Component
 * 
 * Displays a date in Jalali format with various options
 */

import { computed } from 'vue'
import type { JalaliDate } from '~/types/jalali.d'
import { toJalali, toGregorian } from '~/utils/jalali/jalali-converter'
import { toPersianNumerals } from '~/utils/jalali/persian-numerals'
import { formatJalali, formatJalaliRelative } from '~/utils/jalali/jalali-formatter'

interface Props {
  date: Date | string | null | undefined
  format?: 'short' | 'medium' | 'long' | 'full' | string
  relative?: boolean
  showGregorian?: boolean
  persianNumerals?: boolean
  tag?: string
}

const props = withDefaults(defineProps<Props>(), {
  format: 'medium',
  relative: false,
  showGregorian: false,
  persianNumerals: true,
  tag: 'span',
})

// Convert to Jalali if valid date
const jalaliDate = computed<JalaliDate | null>(() => {
  if (!props.date) return null
  try {
    return toJalali(props.date)
  } catch {
    return null
  }
})

// Determine format pattern
const formatPattern = computed(() => {
  switch (props.format) {
    case 'short': return 'Y/m/d'
    case 'medium': return 'j F Y'
    case 'long': return 'l، j F Y'
    case 'full': return 'l، j F Y H:i'
    default: return props.format
  }
})

// Formatted Jalali date
const formattedDate = computed(() => {
  if (!jalaliDate.value) return '—'
  return formatJalali(jalaliDate.value, formatPattern.value, props.persianNumerals)
})

// Relative time string
const relativeTime = computed(() => {
  if (!props.date) return ''
  return formatJalaliRelative(props.date, props.persianNumerals)
})

// Gregorian date string
const gregorianDate = computed(() => {
  if (!props.date) return ''
  const d = typeof props.date === 'string' ? new Date(props.date) : props.date
  return d.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
})

// Display value
const displayValue = computed(() => {
  if (!jalaliDate.value) return '—'
  
  if (props.relative) {
    return relativeTime.value
  }
  
  return formattedDate.value
})
</script>

<template>
  <component :is="tag" class="formatted-date">
    <span>{{ displayValue }}</span>
    <span
      v-if="showGregorian && gregorianDate"
      class="text-gray-500 dark:text-gray-400 text-sm ms-1"
    >
      ({{ gregorianDate }})
    </span>
    <span
      v-if="relative && !props.relative && relativeTime"
      class="text-gray-500 dark:text-gray-400 text-sm ms-1"
    >
      ({{ relativeTime }})
    </span>
  </component>
</template>
