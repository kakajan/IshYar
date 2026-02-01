<script setup lang="ts">
/**
 * Jalali Date Picker Component
 * 
 * A date picker that displays Persian/Jalali calendar
 */

import { ref, computed, watch } from 'vue'
import { Calendar, ChevronLeft, ChevronRight, X } from 'lucide-vue-next'
import type { JalaliDate, CalendarDay } from '~/types/jalali.d'
import { JALALI_MONTH_NAMES, JALALI_DAY_NAMES_SHORT } from '~/types/jalali.d'
import {
  toJalali,
  toGregorian,
  getJalaliToday,
  getDaysInJalaliMonth,
  getJalaliDayOfWeek,
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
  minDate: undefined,
  maxDate: undefined,
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
const viewMode = ref<'day' | 'month' | 'year'>('day')
const dropdownPosition = ref<'top' | 'bottom'>('bottom')
const dropdownAlign = ref<'start' | 'end'>('start')

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

// Years for year view
const yearRange = computed(() => {
  const start = Math.floor(viewYear.value / 15) * 15
  return Array.from({ length: 15 }, (_, i) => start + i)
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

// Go to today
function goToToday() {
  viewYear.value = today.year
  viewMonth.value = today.month
  viewMode.value = 'day'
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
  emit('change', gregorian, jalaliString)
  isOpen.value = false
}

function selectMonth(monthIndex: number) {
  viewMonth.value = monthIndex
  viewMode.value = 'day'
}

function selectYear(year: number) {
  viewYear.value = year
  viewMode.value = 'month'
}

function updatePosition() {
  if (!inputRef.value) return
  const rect = inputRef.value.getBoundingClientRect()
  const spaceBelow = window.innerHeight - rect.bottom
  // If less than 350px space below, show on top
  dropdownPosition.value = spaceBelow < 350 ? 'top' : 'bottom'

  // Horizontal Logic (RTL support)
  // Default is 'start' (Right in RTL), expanding Left
  // We check if expanding Left would go off-screen
  const dropdownWidth = 320 // w-80 approx
  const viewportWidth = window.innerWidth
  
  // In RTL, Start=Right. 
  // Left edge of dropdown = input.right - dropdownWidth
  const leftEdge = rect.right - dropdownWidth
  
  if (leftEdge < 10) {
    // If it would overflow left, align to End (Left in RTL), expanding Right
    dropdownAlign.value = 'end'
  } else {
    // Otherwise check if aligning to End would overflow Right (though less likely in this context?)
    // Actually, stick to Start unless forced to swap
    dropdownAlign.value = 'start'
  }
}

watch(isOpen, (val) => {
  if (val) {
    viewMode.value = 'day'
    // Use nextTick to ensure element is visible/layout is computed if needed, 
    // though rect uses input which is always visible.
    // However, window resize might change things, so good to check now.
    updatePosition()
  }
})

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
        class="w-full px-4 py-2.5 pe-10 border border-gray-200 rounded-xl bg-gray-50/50 text-right
               text-sm font-medium text-gray-700
               transition-all duration-200
               placeholder:text-gray-400
               focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white
               disabled:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-60
               dark:bg-gray-900/50 dark:border-gray-700 dark:text-gray-200
               dark:focus:bg-gray-900 dark:focus:ring-primary-400/10 dark:focus:border-primary-400
               dark:placeholder:text-gray-500"
      >
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
        class="absolute z-50 w-80 bg-white rounded-2xl shadow-xl border border-gray-100
               ring-1 ring-black/5
               backdrop-blur-xl
               dark:bg-gray-800/95 dark:border-gray-700/50 dark:ring-white/5 dark:shadow-2xl"
        :class="[
          dropdownPosition === 'top' ? 'bottom-full mb-3' : 'top-full mt-3',
          dropdownAlign === 'end' ? 'left-0' : 'right-0'
        ]"
      >
      >
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-100/10 dark:border-gray-700/50">
          <button
            v-if="viewMode === 'day'"
            type="button"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
            @click="prevMonth"
          >
            <ChevronRight class="w-5 h-5" />
          </button>
          <div v-else class="w-8" />
          
          <div class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
            <button 
              type="button"
              class="hover:text-primary-600 transition-colors px-1 rounded"
              @click="viewMode = viewMode === 'month' ? 'day' : 'month'"
            >
              {{ JALALI_MONTH_NAMES[viewMonth] }}
            </button>
            <button 
              type="button"
              class="hover:text-primary-600 transition-colors px-1 rounded"
              @click="viewMode = viewMode === 'year' ? 'day' : 'year'"
            >
              {{ formatNum(viewYear) }}
            </button>
          </div>
          
          <button
            v-if="viewMode === 'day'"
            type="button"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
            @click="nextMonth"
          >
            <ChevronLeft class="w-5 h-5" />
          </button>
          <div v-else class="w-8" />
        </div>
        
        <!-- Views -->
        <div class="p-3">
          <!-- Day View -->
          <div v-if="viewMode === 'day'">
            <!-- Day names -->
            <div class="grid grid-cols-7 gap-1 mb-3">
              <div
                v-for="(name, idx) in JALALI_DAY_NAMES_SHORT"
                :key="idx"
                class="text-center text-xs font-semibold text-gray-400 dark:text-gray-500"
                :class="{ 'text-red-500/80 dark:text-red-400/80': Number(idx) === 6 }"
              >
                {{ name }}
              </div>
            </div>
            
            <!-- Days grid -->
            <div class="grid grid-cols-7 gap-1.5">
              <button
                v-for="(day, idx) in calendarDays"
                :key="idx"
                type="button"
                :disabled="day.isDisabled || day.day === 0"
                class="relative aspect-square flex items-center justify-center text-sm rounded-xl
                       transition-all duration-200 group"
                :class="{
                  'invisible': day.day === 0,
                  'text-gray-300 cursor-not-allowed': day.isDisabled && day.day !== 0,
                  'text-red-500 dark:text-red-400 font-medium': day.isWeekend && !day.isDisabled && !day.isSelected,
                  'bg-primary-600 text-white shadow-lg shadow-primary-500/30 scale-100': day.isSelected,
                  'ring-1 ring-primary-500 text-primary-600 bg-primary-50 dark:bg-primary-900/10 dark:text-primary-400': day.isToday && !day.isSelected,
                  'hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-200': !day.isDisabled && !day.isSelected && day.day !== 0 && !day.isWeekend && !day.isToday,
                  'hover:bg-red-50 dark:hover:bg-red-900/10': !day.isDisabled && !day.isSelected && day.isWeekend,
                }"
                @click="selectDay(day)"
              >
                {{ day.day > 0 ? formatNum(day.day) : '' }}
                <span 
                  v-if="day.isToday && !day.isSelected" 
                  class="absolute bottom-1 w-1 h-1 rounded-full bg-primary-500"
                />
              </button>
            </div>
          </div>

          <!-- Month View -->
          <div v-else-if="viewMode === 'month'" class="grid grid-cols-3 gap-3">
            <button
              v-for="(month, idx) in JALALI_MONTH_NAMES"
              :key="idx"
              type="button"
              class="p-3 rounded-xl text-sm font-medium transition-all duration-200 border border-transparent"
              :class="{
                'bg-primary-600 text-white shadow-lg shadow-primary-500/30': Number(idx) === viewMonth,
                'hover:bg-gray-50 hover:border-gray-200 dark:hover:bg-gray-700/50 dark:hover:border-gray-600 text-gray-700 dark:text-gray-200': Number(idx) !== viewMonth
              }"
              @click.stop="selectMonth(Number(idx))"
            >
              {{ month }}
            </button>
          </div>

          <!-- Year View -->
          <div v-else-if="viewMode === 'year'" class="grid grid-cols-3 gap-3">
            <button
              v-for="year in yearRange"
              :key="year"
              type="button"
              class="p-3 rounded-xl text-sm font-medium transition-all duration-200 border border-transparent"
              :class="{
                'bg-primary-600 text-white shadow-lg shadow-primary-500/30': year === viewYear,
                'hover:bg-gray-50 hover:border-gray-200 dark:hover:bg-gray-700/50 dark:hover:border-gray-600 text-gray-700 dark:text-gray-200': year !== viewYear
              }"
              @click.stop="selectYear(year)"
            >
              {{ formatNum(year) }}
            </button>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="flex items-center justify-center p-3 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50 rounded-b-2xl">
          <button
            type="button"
            class="px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-700 
                   bg-primary-50 hover:bg-primary-100 
                   dark:text-primary-400 dark:bg-primary-900/10 dark:hover:bg-primary-900/20
                   rounded-lg transition-colors duration-200 w-full"
            @click="goToToday"
          >
            برو به امروز
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
