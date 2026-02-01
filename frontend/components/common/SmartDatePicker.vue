<script setup lang="ts">
/**
 * Smart Date Picker Component
 * 
 * Automatically switches between JalaliDatePicker and native/other date pickers
 * based on the current locale.
 */

import { computed } from 'vue'
import JalaliDatePicker from '~/components/jalali/JalaliDatePicker.vue'

interface Props {
  modelValue?: Date | string | null
  id?: string
  placeholder?: string
  minDate?: Date | string
  maxDate?: Date | string
  disabled?: boolean
  readonly?: boolean
  className?: string
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: null,
  id: undefined,
  placeholder: '',
  minDate: undefined,
  maxDate: undefined,
  disabled: false,
  readonly: false,
  className: '',
})

const emit = defineEmits<{
  'update:modelValue': [value: string | Date | null]
  'change': [value: string | Date | null]
}>()

const { locale } = useI18n()
const isJalali = computed(() => locale.value === 'fa')

// Model value handling for different pickers
// JalaliDatePicker expects Date object or string
// Native input date expects YYYY-MM-DD string

const jalaliModel = computed({
  get: () => props.modelValue,
  set: (val) => {
    const valueToEmit = val === undefined ? null : val
    emit('update:modelValue', valueToEmit)
    emit('change', valueToEmit)
  }
})

const nativeModel = computed({
  get: () => {
    if (!props.modelValue) return ''
    if (props.modelValue instanceof Date) {
      return props.modelValue.toISOString().split('T')[0]
    }
    // Try to parse string if it's not YYYY-MM-DD
    const d = new Date(props.modelValue)
    if (!isNaN(d.getTime())) {
      return d.toISOString().split('T')[0]
    }
    return props.modelValue as string
  },
  set: (val) => {
    const valueToEmit = val === undefined ? null : val
    emit('update:modelValue', valueToEmit)
    emit('change', valueToEmit)
  }
})

const minDateIso = computed(() => {
  if (!props.minDate) return undefined
  return new Date(props.minDate).toISOString().split('T')[0]
})

const maxDateIso = computed(() => {
  if (!props.maxDate) return undefined
  return new Date(props.maxDate).toISOString().split('T')[0]
})
</script>

<template>
  <div class="smart-date-picker">
    <JalaliDatePicker
      v-if="isJalali"
      :id="id"
      v-model="jalaliModel"
      :placeholder="placeholder"
      :min-date="minDate"
      :max-date="maxDate"
      :disabled="disabled"
      :readonly="readonly"
      :class="className"
    />
    
    <input
      v-else
      :id="id"
      v-model="nativeModel"
      type="date"
      :min="minDateIso"
      :max="maxDateIso"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
      :class="className"
    >
  </div>
</template>
