<script setup lang="ts">
interface Props {
  priority: string
  /** Show icon */
  showIcon?: boolean
  /** Size variant */
  size?: 'xs' | 'sm' | 'md'
}

const props = withDefaults(defineProps<Props>(), {
  showIcon: true,
  size: 'sm',
})

const priorityConfig: Record<string, { label: string; color: string; icon: string }> = {
  low: {
    label: 'Low',
    color: 'neutral',
    icon: 'i-heroicons-arrow-down',
  },
  medium: {
    label: 'Medium',
    color: 'warning',
    icon: 'i-heroicons-minus',
  },
  high: {
    label: 'High',
    color: 'orange',
    icon: 'i-heroicons-arrow-up',
  },
  urgent: {
    label: 'Urgent',
    color: 'error',
    icon: 'i-heroicons-exclamation-triangle',
  },
  critical: {
    label: 'Critical',
    color: 'error',
    icon: 'i-heroicons-fire',
  },
}

const config = computed(() => {
  const normalizedPriority = props.priority?.toLowerCase() || 'medium'
  return priorityConfig[normalizedPriority] || priorityConfig.medium
})

const iconSize = computed(() => {
  const sizes: Record<string, string> = {
    xs: 'w-2.5 h-2.5',
    sm: 'w-3 h-3',
    md: 'w-4 h-4',
  }
  return sizes[props.size]
})
</script>

<template>
  <UBadge :color="config.color as any" variant="subtle" :size="size">
    <UIcon v-if="showIcon" :name="config.icon" :class="[iconSize, 'mr-1']" />
    {{ config.label }}
  </UBadge>
</template>
