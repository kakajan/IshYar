<script setup lang="ts">
/**
 * Jalali Date Picker Component
 * 
 * A date picker that displays Persian/Jalali calendar
 */

import { ref, computed, watch } from 'vue'
import { Calendar, ChevronLeft, ChevronRight, X } from 'lucide-vue-next'
import type { JalaliDate, CalendarDay, Holiday } from '~/types/jalali.d'
import { JALALI_MONTH_NAMES, JALALI_DAY_NAMES_SHORT } from '~/types/jalali.d'
import {
  toJalali,
  toGregorian,
  getJalaliToday,
  getDaysInJalaliMonth,
  getJalaliDayOfWeek,
  isLeapJalaliYear,
  areJalaliDatesEqual,
} from '~/utils/jalali/jalali-converter'
import { toPersianNumerals } from '~/utils/jalali/persian-numerals'
import { formatJalali } from '~/utils/jalali/jalali-formatter'

// Props
interface Props {
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

const props = withDefaults(defineProps<Props>(), {
  modelValue: null,
  disabledDates: () => [],
  disabledDays: () => [],
  showHolidays: true,
  persianNumerals: true,
  placeholder: 'تاریخ را انتخاب کنید',
  format: 'Y/m/d',
  disabled: false,
  readonly: false,
})

// Emits
const emit = defineEmits<{
  'update:modelValue': [value: Date | null]
  'change': [date: Date | null, jalaliString: string]
}>()

// State
const isOpen = ref(false)
const inputRef = ref<HTMLInputElement | null>(null)
const today = getJalaliToday()
const viewYear = ref(today.year)
const viewMonth = ref(today.month)

// Create selected date from modelValue
const selectedDate = computed<JalaliDate | null>(() => {
  if (!props.modelValue) return null
  return toJalali(props.modelValue)
})

// Watch modelValue changes to update view
watch(() => props.modelValue, (val) => {
  if (val) {
    const jDate = toJalali(val)
    viewYear.value = jDate.year
    viewMonth.value = jDate.month
  }
}, { immediate: true })

// Display value in input
const displayValue = computed(() => {
  if (!selectedDate.value) return ''
  return formatJalali(selectedDate.value, props.format, props.persianNumerals)
})

// Get days in current view month
const daysInMonth = computed(() => getDaysInJalaliMonth(viewYear.value, viewMonth.value))

// Get the day of week for the first day of the month (0 = Saturday)
const firstDayOfWeek = computed(() => {
  const firstDay: JalaliDate = { year: viewYear.value, month: viewMonth.value, day: 1 }
  return getJalaliDayOfWeek(firstDay)
})

// Generate calendar days
const calendarDays = computed<CalendarDay[]>(() => {
  const days: CalendarDay[] = []
  
  // Add empty cells for days before the first day
  for (let i = 0; i < firstDayOfWeek.value; i++) {
    days.push({
      day: 0,
      dayOfWeek: i,
      isToday: false,
      isHoliday: false,
      isWeekend: false,
      isDisabled: true,
      gregorian: '',
    })
  }
  
  // Add actual days
  for (let day = 1; day <= daysInMonth.value; day++) {
    const jDate: JalaliDate = { year: viewYear.value, month: viewMonth.value, day }
    const dayOfWeek = getJalaliDayOfWeek(jDate)
    const gregorian = toGregorian(jDate)
    
    // Check if this day is disabled
    const isDisabled = checkIsDisabled(jDate, gregorian)
    
    // Check if selected
    const isSelected = selectedDate.value ? areJalaliDatesEqual(jDate, selectedDate.value) : false
    
    days.push({
      day,
      dayOfWeek,
      isToday: areJalaliDatesEqual(jDate, today),
      isHoliday: dayOfWeek === 6, // Friday
      isWeekend: dayOfWeek === 6,
      isDisabled,
      isSelected,
      gregorian: gregorian.toISOString(),
    })
  }
  
  return days
})

// Check if a day is disabled
function checkIsDisabled(jDate: JalaliDate, gregorian: Date): boolean {
  // Check disabled days of week
  if (props.disabledDays.includes(getJalaliDayOfWeek(jDate))) {
    return true
  }
  
  // Check min date
  if (props.minDate) {
    const min = new Date(props.minDate)
    if (gregorian < min) return true
  }
  
  // Check max date
  if (props.maxDate) {
    const max = new Date(props.maxDate)
    if (gregorian > max) return true
  }
  
  // Check disabled dates
  for (const disabled of props.disabledDates) {
    const disabledDate = new Date(disabled)
    if (
      gregorian.getFullYear() === disabledDate.getFullYear() &&
      gregorian.getMonth() === disabledDate.getMonth() &&
      gregorian.getDate() === disabledDate.getDate()
    ) {
      return true
    }
  }
  
  return false
}

// Navigation
function prevMonth() {
  if (viewMonth.value === 1) {
    viewMonth.value = 12
    viewYear.value--
  } else {
    viewMonth.value--
  }
}

function nextMonth() {
  if (viewMonth.value === 12) {
    viewMonth.value = 1
    viewYear.value++
  } else {
    viewMonth.value++
  }
}

function goToToday() {
  viewYear.value = today.year
  viewMonth.value = today.month
}

// Select a day
function selectDay(day: CalendarDay) {
  if (day.day === 0 || day.isDisabled) return
  
  const gregorian = new Date(day.gregorian)
  const jalaliString = formatJalali(
    { year: viewYear.value, month: viewMonth.value, day: day.day },
    props.format,
    props.persianNumerals
  )
  
  emit('update:modelValue', gregorian)
  emit('change', gregorian, jalaliString)
  isOpen.value = false
}

// Clear selection
function clear() {
  emit('update:modelValue', null)
  emit('change', null, '')
}

// Toggle dropdown
function toggleDropdown() {
  if (props.disabled || props.readonly) return
  isOpen.value = !isOpen.value
}

// Close on outside click
function handleClickOutside(event: MouseEvent) {
  const target = event.target as HTMLElement
  if (!target.closest('.jalali-date-picker')) {
    isOpen.value = false
  }
}

// Format number with Persian numerals if enabled
function formatNum(num: number): string {
  return props.persianNumerals ? toPersianNumerals(num) : String(num)
}

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="jalali-date-picker relative" dir="rtl">
    <!-- Input -->
    <div
      class="relative flex items-center"
      @click="toggleDropdown"
    >
      <input
        ref="inputRef"
        type="text"
        :value="displayValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="true"
        class="w-full px-3 py-2 pe-10 border border-gray-300 rounded-lg bg-white text-right
               focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
               disabled:bg-gray-100 disabled:cursor-not-allowed
               dark:bg-gray-800 dark:border-gray-600 dark:text-white"
      />
      <div class="absolute start-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
        <button
          v-if="modelValue && !disabled && !readonly"
          type="button"
          class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          @click.stop="clear"
        >
          <X class="w-4 h-4" />
        </button>
        <Calendar class="w-5 h-5 text-gray-400" />
      </div>
    </div>
    
    <!-- Dropdown -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute z-50 mt-2 w-72 bg-white rounded-xl shadow-lg border border-gray-200
               dark:bg-gray-800 dark:border-gray-700"
      >
        <!-- Header -->
        <div class="flex items-center justify-between p-3 border-b border-gray-200 dark:border-gray-700">
          <button
            type="button"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
            @click="nextMonth"
          >
            <ChevronRight class="w-5 h-5" />
          </button>
          
          <div class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
            <span>{{ JALALI_MONTH_NAMES[viewMonth] }}</span>
            <span>{{ formatNum(viewYear) }}</span>
          </div>
          
          <button
            type="button"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
            @click="prevMonth"
          >
            <ChevronLeft class="w-5 h-5" />
          </button>
        </div>
        
        <!-- Day names -->
        <div class="grid grid-cols-7 gap-1 p-2 border-b border-gray-200 dark:border-gray-700">
          <div
            v-for="(name, idx) in JALALI_DAY_NAMES_SHORT"
            :key="idx"
            class="text-center text-xs font-medium text-gray-500 dark:text-gray-400"
            :class="{ 'text-red-500 dark:text-red-400': Number(idx) === 6 }"
          >
            {{ name }}
          </div>
        </div>
        
        <!-- Days grid -->
        <div class="grid grid-cols-7 gap-1 p-2">
          <button
            v-for="(day, idx) in calendarDays"
            :key="idx"
            type="button"
            :disabled="day.isDisabled || day.day === 0"
            class="aspect-square flex items-center justify-center text-sm rounded-lg
                   transition-colors duration-150"
            :class="{
              'text-transparent cursor-default': day.day === 0,
              'text-gray-400 cursor-not-allowed': day.isDisabled && day.day !== 0,
              'text-red-500 dark:text-red-400': day.isWeekend && !day.isDisabled && !day.isSelected,
              'bg-primary-500 text-white': day.isSelected,
              'ring-2 ring-primary-500': day.isToday && !day.isSelected,
              'hover:bg-gray-100 dark:hover:bg-gray-700': !day.isDisabled && !day.isSelected && day.day !== 0,
              'text-gray-900 dark:text-white': !day.isWeekend && !day.isDisabled && !day.isSelected,
            }"
            @click="selectDay(day)"
          >
            {{ day.day > 0 ? formatNum(day.day) : '' }}
          </button>
        </div>
        
        <!-- Footer -->
        <div class="flex items-center justify-between p-2 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            class="px-3 py-1.5 text-sm text-primary-600 hover:bg-primary-50 rounded-lg
                   dark:text-primary-400 dark:hover:bg-primary-900/20"
            @click="goToToday"
          >
            امروز
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.jalali-date-picker {
  font-family: inherit;
}
</style>
