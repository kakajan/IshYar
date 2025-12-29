<script setup lang="ts">
/**
 * Jalali Calendar Component
 * 
 * Full-month calendar view with slots for customization
 */

import { ref, computed, watch } from 'vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import type { JalaliDate, CalendarDay } from '~/types/jalali.d'
import { JALALI_MONTH_NAMES, JALALI_DAY_NAMES } from '~/types/jalali.d'
import {
  toJalali,
  toGregorian,
  getJalaliToday,
  getDaysInJalaliMonth,
  getJalaliDayOfWeek,
  areJalaliDatesEqual,
  isJalaliToday,
} from '~/utils/jalali/jalali-converter'
import { toPersianNumerals, formatCurrency } from '~/utils/jalali/persian-numerals'

interface Props {
  modelValue?: Date | string | null
  events?: any[] // Simplified event support
  persianNumerals?: boolean
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: () => new Date(),
  events: () => [],
  persianNumerals: true,
  loading: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: Date]
  'change-month': [year: number, month: number]
  'start-date-change': [date: Date] // When view changes
  'date-click': [date: JalaliDate, nativeDate: Date]
}>()

// State
const today = getJalaliToday()
const currentViewDate = ref<JalaliDate>(today)

// Initialize view from modelValue
watch(() => props.modelValue, (val) => {
  if (val) {
    const jDate = toJalali(val)
    currentViewDate.value = { year: jDate.year, month: jDate.month, day: jDate.day } // Keep day but usually view ignores day
  }
}, { immediate: true })

// Computed Days
const days = computed(() => {
  const year = currentViewDate.value.year
  const month = currentViewDate.value.month
  const daysInMonth = getDaysInJalaliMonth(year, month)
  const firstDay: JalaliDate = { year, month, day: 1 }
  const firstDayOfWeek = getJalaliDayOfWeek(firstDay)
  
  const result: CalendarDay[] = []
  
  // Previous month padding
  // We can calculate previous month days to fill or just leave empty/dimmed
  // Let's fill with previous month days for a full grid look
  const prevMonth = month === 1 ? 12 : month - 1
  const prevYear = month === 1 ? year - 1 : year
  const daysInPrevMonth = getDaysInJalaliMonth(prevYear, prevMonth)
  
  for (let i = 0; i < firstDayOfWeek; i++) {
     const dayNum = daysInPrevMonth - (firstDayOfWeek - 1) + i
     const jDate: JalaliDate = { year: prevYear, month: prevMonth, day: dayNum }
     result.push({
       day: dayNum,
       dayOfWeek: i,
       isToday: areJalaliDatesEqual(jDate, today), // Unlikely but possible
       isHoliday: i === 6, // Friday is default holiday/weekend
       isWeekend: i === 6,
       isDisabled: true, // Prev month
       gregorian: toGregorian(jDate).toISOString(),
       // Custom prop to indicate other month
       isOtherMonth: true,
     } as CalendarDay & { isOtherMonth?: boolean })
  }
  
  // Current month
  for (let day = 1; day <= daysInMonth; day++) {
    const jDate: JalaliDate = { year, month, day }
    const dayOfWeek = getJalaliDayOfWeek(jDate)
    const gregorian = toGregorian(jDate)
    
    result.push({
      day,
      dayOfWeek,
      isToday: areJalaliDatesEqual(jDate, today),
      isHoliday: dayOfWeek === 6,
      isWeekend: dayOfWeek === 6,
      isDisabled: false,
      gregorian: gregorian.toISOString(),
      isOtherMonth: false,
    } as CalendarDay & { isOtherMonth?: boolean })
  }
  
  // Next month padding
  // Grid usually has 35 or 42 cells (5 or 6 rows)
  const totalCells = result.length > 35 ? 42 : 35
  const remainingCells = totalCells - result.length
  
  const nextMonth = month === 12 ? 1 : month + 1
  const nextYear = month === 12 ? year + 1 : year
  
  for (let i = 1; i <= remainingCells; i++) {
    const jDate: JalaliDate = { year: nextYear, month: nextMonth, day: i }
    const dayOfWeek = getJalaliDayOfWeek(jDate)
    
    result.push({
      day: i,
      dayOfWeek,
      isToday: areJalaliDatesEqual(jDate, today),
      isHoliday: dayOfWeek === 6,
      isWeekend: dayOfWeek === 6,
      isDisabled: true,
      gregorian: toGregorian(jDate).toISOString(),
      isOtherMonth: true,
    } as CalendarDay & { isOtherMonth?: boolean })
  }
  
  return result
})

// Formatting
function formatNum(n: number) {
  return props.persianNumerals ? toPersianNumerals(n) : String(n)
}

// Navigation
function prevMonth() {
  let { year, month } = currentViewDate.value
  month--
  if (month < 1) {
    month = 12
    year--
  }
  currentViewDate.value = { ...currentViewDate.value, year, month }
  emit('change-month', year, month)
}

function nextMonth() {
  let { year, month } = currentViewDate.value
  month++
  if (month > 12) {
    month = 1
    year++
  }
  currentViewDate.value = { ...currentViewDate.value, year, month }
  emit('change-month', year, month)
}

function goToday() {
  currentViewDate.value = { ...today }
  emit('change-month', today.year, today.month)
}

function handleDayClick(day: CalendarDay) {
  const date = toGregorian({
     year: currentViewDate.value.year, // Note: this might be wrong for prev/next month cells
     // Actually use the day's property if I added year/month to it, OR recalculate
     // For simplicity, let's just emit current view's year/month with the day IF it's not other month
     // Or parse gregorian string
     month: currentViewDate.value.month,
     day: day.day
  })
  
  // Correct way: parse gregorian
  const nativeDate = new Date(day.gregorian)
  const jDateRes = toJalali(nativeDate)
  
  emit('date-click', jDateRes, nativeDate)
}
</script>

<template>
  <div class="jalali-calendar bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
    <!-- Header -->
    <div class="p-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-4">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
          {{ JALALI_MONTH_NAMES[currentViewDate.month] }}
          <span class="text-gray-500 dark:text-gray-400 font-normal">
            {{ formatNum(currentViewDate.year) }}
          </span>
        </h2>
        
        <div class="flex items-center gap-1">
          <button 
            type="button"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors"
            @click="prevMonth"
          >
            <ChevronRight class="w-5 h-5" />
          </button>
          
          <button 
            type="button"
            class="px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors"
            @click="goToday"
          >
            امروز
          </button>
          
          <button 
            type="button"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors"
            @click="nextMonth"
          >
            <ChevronLeft class="w-5 h-5" />
          </button>
        </div>
      </div>

      <div class="flex items-center gap-2">
         <slot name="header-actions" />
      </div>
    </div>

    <!-- Weekday Header -->
    <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
      <div 
        v-for="(name, i) in JALALI_DAY_NAMES" 
        :key="i"
        class="py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-400"
      >
        {{ name }}
      </div>
    </div>

    <!-- Days Grid -->
    <div class="flex-1 grid grid-cols-7 auto-rows-fr bg-gray-200 dark:bg-gray-700 gap-px border-b border-gray-200 dark:border-gray-700">
      <div 
        v-for="(day, idx) in days" 
        :key="idx"
        class="bg-white dark:bg-gray-800 relative min-h-[100px] p-2 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50 flex flex-col group"
        :class="{
          'bg-gray-50/50 dark:bg-gray-900/50': day.isOtherMonth,
          'bg-primary-50/30 dark:bg-primary-900/10': day.isToday
        }"
        @click="handleDayClick(day)"
      >
        <!-- Day Number -->
        <div class="flex items-center justify-between mb-1">
           <span 
            class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full"
            :class="{
              'bg-primary-600 text-white shadow-sm': day.isToday,
              'text-gray-900 dark:text-white': !day.isToday && !day.isOtherMonth,
              'text-gray-400 dark:text-gray-500': day.isOtherMonth,
              'text-red-500 dark:text-red-400': day.isWeekend && !day.isToday && !day.isOtherMonth
            }"
           >
             {{ formatNum(day.day) }}
           </span>
           
           <slot name="day-header" :day="day" />
        </div>

        <!-- Content Slot -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
           <slot name="day-content" :day="day">
              <!-- Default event listing if events prop provided -->
           </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.jalali-calendar {
  font-family: inherit;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 2px;
}
</style>
