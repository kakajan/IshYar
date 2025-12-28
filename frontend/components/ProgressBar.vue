<script setup lang="ts">
interface Props {
  /** Current value */
  value: number
  /** Maximum value */
  max?: number
  /** Show percentage text */
  showText?: boolean
  /** Size variant */
  size?: 'xs' | 'sm' | 'md' | 'lg'
  /** Color variant */
  color?: 'primary' | 'success' | 'warning' | 'error' | 'info'
}

const props = withDefaults(defineProps<Props>(), {
  max: 100,
  showText: false,
  size: 'md',
  color: 'primary',
})

const percentage = computed(() => {
  if (props.max === 0) return 0
  return Math.min(100, Math.max(0, (props.value / props.max) * 100))
})

const sizeClasses: Record<string, string> = {
  xs: 'h-1',
  sm: 'h-2',
  md: 'h-3',
  lg: 'h-4',
}

const colorClasses: Record<string, string> = {
  primary: 'bg-primary-500',
  success: 'bg-green-500',
  warning: 'bg-yellow-500',
  error: 'bg-red-500',
  info: 'bg-blue-500',
}
</script>

<template>
  <div class="w-full">
    <div v-if="showText" class="flex justify-between text-sm mb-1">
      <span class="text-gray-600 dark:text-gray-400">
        <slot name="label">Progress</slot>
      </span>
      <span class="text-gray-900 dark:text-white font-medium">
        {{ Math.round(percentage) }}%
      </span>
    </div>
    <div
      :class="[
        'w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden',
        sizeClasses[size],
      ]"
    >
      <div
        :class="[
          'h-full rounded-full transition-all duration-300 ease-out',
          colorClasses[color],
        ]"
        :style="{ width: `${percentage}%` }"
      />
    </div>
  </div>
</template>
