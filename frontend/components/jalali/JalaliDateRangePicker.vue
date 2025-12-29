<script setup lang="ts">
/**
 * Jalali Date Range Picker Component
 * 
 * Select a date range (start and end dates)
 */

import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Calendar, ChevronLeft, ChevronRight, X } from 'lucide-vue-next'
import type { JalaliDate, CalendarDay } from '~/types/jalali.d'
import { JALALI_MONTH_NAMES, JALALI_DAY_NAMES_SHORT } from '~/types/jalali.d'
import {
  toJalali,
  toGregorian,
  getJalaliToday,
  getDaysInJalaliMonth,
  getJalaliDayOfWeek,
  compareJalaliDates,
  addJalaliMonths,
  areJalaliDatesEqual,
} from '~/utils/jalali/jalali-converter'
import { toPersianNumerals } from '~/utils/jalali/persian-numerals'
import { formatJalali } from '~/utils/jalali/jalali-formatter'

interface Props {
  modelValue?: [Date | string | null, Date | string | null] // [start, end]
  minDate?: Date | string
  maxDate?: Date | string
  disabledDates?: (string | Date)[]
  disabledDays?: number[]
  showHolidays?: boolean
  persianNumerals?: boolean
  placeholder?: string
  format?: string
  disabled?: boolean
  readonly?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: () => [null, null],
  disabledDates: () => [],
  disabledDays: () => [],
  showHolidays: true,
  persianNumerals: true,
  placeholder: 'بازه زمانی را انتخاب کنید',
  format: 'Y/m/d',
  disabled: false,
  readonly: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: [Date | null, Date | null]]
  'change': [range: [Date | null, Date | null], formatted: string]
}>()

// State
const isOpen = ref(false)
const inputRef = ref<HTMLInputElement | null>(null)
const today = getJalaliToday()

// View state (showing 2 months). 
// viewDate1 is the left month, viewDate2 is the right month (viewDate1 + 1 month)
const viewDate1 = ref<JalaliDate>({ year: today.year, month: today.month, day: 1 })

// Selection state
const startDate = ref<JalaliDate | null>(null)
const endDate = ref<JalaliDate | null>(null)
const hoverDate = ref<JalaliDate | null>(null)

// Computed next month for view 2
const viewDate2 = computed(() => addJalaliMonths(viewDate1.value, 1))

// Initialize from props
watch(() => props.modelValue, (val) => {
  if (val && val[0]) {
    startDate.value = toJalali(val[0])
    // If we have a start date, ensure it's visible
    if (!isOpen.value) { // Only update view if closed to avoid jumping while navigating
       viewDate1.value = { year: startDate.value.year, month: startDate.value.month, day: 1 }
    }
  } else {
    startDate.value = null
  }
  
  if (val && val[1]) {
    endDate.value = toJalali(val[1])
  } else {
    endDate.value = null
  }
}, { immediate: true })

// Display string
const displayValue = computed(() => {
  if (!startDate.value) return ''
  
  const startStr = formatJalali(startDate.value, props.format, props.persianNumerals)
  
  if (!endDate.value) return startStr
  
  const endStr = formatJalali(endDate.value, props.format, props.persianNumerals)
  
  return `${startStr} - ${endStr}`
})

// Helper to generate days for a month view
function generateMonthDays(year: number, month: number): CalendarDay[] {
  const daysInMonth = getDaysInJalaliMonth(year, month)
  const firstDay: JalaliDate = { year, month, day: 1 }
  const firstDayOfWeek = getJalaliDayOfWeek(firstDay)
  
  const days: CalendarDay[] = []
  
  // Empty slots
  for (let i = 0; i < firstDayOfWeek; i++) {
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
  
  // Actual days
  for (let day = 1; day <= daysInMonth; day++) {
    const jDate: JalaliDate = { year, month, day }
    const dayOfWeek = getJalaliDayOfWeek(jDate)
    const gregorian = toGregorian(jDate)
    
    // Check disabled
    const isDisabled = checkIsDisabled(jDate, gregorian)
    
    // Selection logic
    const isSelectedStart = startDate.value ? areJalaliDatesEqual(jDate, startDate.value) : false
    const isSelectedEnd = endDate.value ? areJalaliDatesEqual(jDate, endDate.value) : false
    const isSelected = isSelectedStart || isSelectedEnd
    
    // Range logic
    let isInRange = false
    if (startDate.value && endDate.value) {
      // Complete range
      isInRange = compareJalaliDates(jDate, startDate.value) > 0 && compareJalaliDates(jDate, endDate.value) < 0
    } else if (startDate.value && hoverDate.value && !endDate.value) {
      // Proposed range (hover)
      // Only if hover is after start
      if (compareJalaliDates(hoverDate.value, startDate.value) > 0) {
        isInRange = compareJalaliDates(jDate, startDate.value) > 0 && compareJalaliDates(jDate, hoverDate.value) <= 0
      }
    }

    days.push({
      day,
      dayOfWeek,
      isToday: areJalaliDatesEqual(jDate, today),
      isHoliday: dayOfWeek === 6,
      isWeekend: dayOfWeek === 6,
      isDisabled,
      isSelected,
      isInRange,
      gregorian: gregorian.toISOString(),
      // Custom flags for styling ends of range
      isRangeStart: isSelectedStart,
      isRangeEnd: isSelectedEnd || (startDate.value && hoverDate.value && areJalaliDatesEqual(jDate, hoverDate.value) && !endDate.value && compareJalaliDates(hoverDate.value, startDate.value) > 0), 
    } as CalendarDay & { isRangeStart?: boolean, isRangeEnd?: boolean })
  }
  
  return days
}

// Computed days for both views
const month1Days = computed(() => generateMonthDays(viewDate1.value.year, viewDate1.value.month))
const month2Days = computed(() => generateMonthDays(viewDate2.value.year, viewDate2.value.month))

function checkIsDisabled(jDate: JalaliDate, gregorian: Date): boolean {
  if (props.disabledDays.includes(getJalaliDayOfWeek(jDate))) return true
  if (props.minDate && gregorian < new Date(props.minDate)) return true
  if (props.maxDate && gregorian > new Date(props.maxDate)) return true
  
  // Check specific disabled dates
  // (Simplified for performance, assume string format YYYY-MM-DD or comparable)
  return false 
}

// Navigation
function prevMonth() {
  let newMonth = viewDate1.value.month - 1
  let newYear = viewDate1.value.year
  if (newMonth < 1) {
    newMonth = 12
    newYear--
  }
  viewDate1.value = { year: newYear, month: newMonth, day: 1 }
}

function nextMonth() {
  let newMonth = viewDate1.value.month + 1
  let newYear = viewDate1.value.year
  if (newMonth > 12) {
    newMonth = 1
    newYear++
  }
  viewDate1.value = { year: newYear, month: newMonth, day: 1 }
}

// Interaction
function handleDayClick(day: CalendarDay, year: number, month: number) {
  if (day.isDisabled || day.day === 0) return
  
  const clickedDate: JalaliDate = { year, month, day: day.day }
  
  if (!startDate.value || (startDate.value && endDate.value)) {
    // Start new selection
    startDate.value = clickedDate
    endDate.value = null
  } else {
    // We have a start date, check if clicked is before start
    if (compareJalaliDates(clickedDate, startDate.value) < 0) {
      // New start date
      startDate.value = clickedDate
      endDate.value = null
    } else {
      // Set end date
      endDate.value = clickedDate
      emitChange()
      isOpen.value = false
    }
  }
}

function handleDayHover(day: CalendarDay, year: number, month: number) {
  if (day.isDisabled || day.day === 0) {
    hoverDate.value = null
    return
  }
  hoverDate.value = { year, month, day: day.day }
}

function emitChange() {
  if (!startDate.value || !endDate.value) return
  
  const start = toGregorian(startDate.value)
  const end = toGregorian(endDate.value)
  
  emit('update:modelValue', [start, end])
  emit('change', [start, end], displayValue.value)
}

function clear() {
  startDate.value = null
  endDate.value = null
  emit('update:modelValue', [null, null])
  emit('change', [null, null], '')
}

function formatNum(n: number) {
  return props.persianNumerals ? toPersianNumerals(n) : String(n)
}

// Click outside
function handleClickOutside(event: MouseEvent) {
  const target = event.target as HTMLElement
  if (!target.closest('.jalali-range-picker')) {
    isOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<template>
  <div class="jalali-range-picker relative" dir="rtl">
    <!-- Input Trigger -->
    <div
      class="relative flex items-center"
      @click="!disabled && !readonly && (isOpen = !isOpen)"
    >
      <input
        ref="inputRef"
        type="text"
        :value="displayValue"
        :placeholder="placeholder"
        :disabled="disabled"
        readonly
        class="w-full px-3 py-2 pe-10 border border-gray-300 rounded-lg bg-white text-right
               focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
               disabled:bg-gray-100 disabled:cursor-not-allowed
               cursor-pointer
               dark:bg-gray-800 dark:border-gray-600 dark:text-white"
      />
      <div class="absolute start-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
        <button
          v-if="startDate && !disabled && !readonly"
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
        class="absolute z-50 mt-2 bg-white rounded-xl shadow-xl border border-gray-200
               dark:bg-gray-800 dark:border-gray-700
               w-[600px] overflow-hidden"
      >
        <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-gray-200 dark:divide-gray-700">
          
          <!-- Month 1 -->
          <div class="p-4 flex-1">
            <div class="flex items-center justify-between mb-4">
              <button
                type="button"
                class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="prevMonth"
              >
                <ChevronRight class="w-5 h-5" />
              </button>
              <span class="font-semibold text-gray-900 dark:text-white">
                {{ JALALI_MONTH_NAMES[viewDate1.month] }} {{ formatNum(viewDate1.year) }}
              </span>
              <div class="w-8"></div> <!-- Spacer for alignment -->
            </div>
            
            <div class="grid grid-cols-7 gap-1 mb-2">
              <div v-for="(name, i) in JALALI_DAY_NAMES_SHORT" :key="i" 
                   class="text-center text-xs text-gray-500 font-medium h-6 flex items-center justify-center">
                {{ name }}
              </div>
            </div>
            
            <div class="grid grid-cols-7 gap-y-1">
               <button
                  v-for="(day, idx) in month1Days"
                  :key="idx"
                  type="button"
                  :disabled="day.isDisabled || day.day === 0"
                  class="h-9 w-full flex items-center justify-center text-sm relative"
                  @click="handleDayClick(day, viewDate1.year, viewDate1.month)"
                  @mouseenter="handleDayHover(day, viewDate1.year, viewDate1.month)"
                >
                  <!-- Background Range Highlight -->
                  <div 
                    v-if="day.isInRange && day.day !== 0"
                    class="absolute inset-y-0 w-full bg-primary-100 dark:bg-primary-900/30"
                    :class="{
                      'rounded-r-lg': day.isRangeStart, // Start of range (RTL: right side)
                      'rounded-l-lg': day.isRangeEnd,   // End of range (RTL: left side)
                      'start-0': !day.isRangeStart,     // Fill logic...
                      'end-0': !day.isRangeEnd
                    }"
                  ></div>

                  <!-- The Number Circle -->
                   <span 
                    class="relative z-10 w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
                    :class="{
                      'text-transparent': day.day === 0,
                      'text-gray-300 cursor-not-allowed': day.isDisabled && day.day !== 0,
                      'bg-primary-600 text-white shadow-sm': day.isSelected || day.isRangeStart || day.isRangeEnd,
                      'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700': !day.isSelected && !day.isRangeStart && !day.isRangeEnd && !day.isDisabled && day.day !== 0,
                      'text-red-500 dark:text-red-400': day.isWeekend && !day.isSelected && !day.isRangeStart && !day.isRangeEnd && !day.isDisabled
                    }"
                   >
                     {{ day.day > 0 ? formatNum(day.day) : '' }}
                   </span>
                </button>
            </div>
          </div>

          <!-- Month 2 -->
          <div class="p-4 flex-1">
             <div class="flex items-center justify-between mb-4">
              <div class="w-8"></div> <!-- Spacer -->
              <span class="font-semibold text-gray-900 dark:text-white">
                {{ JALALI_MONTH_NAMES[viewDate2.month] }} {{ formatNum(viewDate2.year) }}
              </span>
              <button
                type="button"
                class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="nextMonth"
              >
                <ChevronLeft class="w-5 h-5" />
              </button>
            </div>

            <div class="grid grid-cols-7 gap-1 mb-2">
              <div v-for="(name, i) in JALALI_DAY_NAMES_SHORT" :key="i" 
                   class="text-center text-xs text-gray-500 font-medium h-6 flex items-center justify-center">
                {{ name }}
              </div>
            </div>
            
            <div class="grid grid-cols-7 gap-y-1">
               <button
                  v-for="(day, idx) in month2Days"
                  :key="idx"
                  type="button"
                  :disabled="day.isDisabled || day.day === 0"
                  class="h-9 w-full flex items-center justify-center text-sm relative"
                  @click="handleDayClick(day, viewDate2.year, viewDate2.month)"
                  @mouseenter="handleDayHover(day, viewDate2.year, viewDate2.month)"
                >
                  <!-- Range BG -->
                  <div 
                    v-if="day.isInRange && day.day !== 0"
                    class="absolute inset-y-0 w-full bg-primary-100 dark:bg-primary-900/30"
                    :class="{
                      'rounded-r-lg': day.isRangeStart,
                      'rounded-l-lg': day.isRangeEnd,
                    }"
                  ></div>

                   <span 
                    class="relative z-10 w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
                    :class="{
                      'text-transparent': day.day === 0,
                      'text-gray-300 cursor-not-allowed': day.isDisabled && day.day !== 0,
                      'bg-primary-600 text-white shadow-sm': day.isSelected || day.isRangeStart || day.isRangeEnd,
                      'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700': !day.isSelected && !day.isRangeStart && !day.isRangeEnd && !day.isDisabled && day.day !== 0,
                      'text-red-500 dark:text-red-400': day.isWeekend && !day.isSelected && !day.isRangeStart && !day.isRangeEnd && !day.isDisabled
                    }"
                   >
                     {{ day.day > 0 ? formatNum(day.day) : '' }}
                   </span>
                </button>
            </div>
          </div>

        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-800/50 flex justify-end gap-2">
          <button 
            type="button"
            class="px-4 py-2 text-sm text-gray-700 hover:bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
            @click="isOpen = false"
          >
            انصراف
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.jalali-range-picker {
  font-family: inherit;
}
</style>
