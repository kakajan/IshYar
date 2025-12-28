<script setup lang="ts">
const { t } = useI18n()

type BadgeColor =
  | 'error'
  | 'primary'
  | 'secondary'
  | 'success'
  | 'info'
  | 'warning'
  | 'neutral'

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

const priorityMeta: Record<string, { color: BadgeColor; icon: string }> = {
  low: { color: 'neutral', icon: 'i-heroicons-arrow-down' },
  medium: { color: 'warning', icon: 'i-heroicons-minus' },
  high: { color: 'warning', icon: 'i-heroicons-arrow-up' },
  urgent: { color: 'error', icon: 'i-heroicons-exclamation-triangle' },
  critical: { color: 'error', icon: 'i-heroicons-fire' },
}

const config = computed(() => {
  const normalizedPriority = props.priority?.toLowerCase() || 'medium'
  const meta = priorityMeta[normalizedPriority] ?? priorityMeta.medium!

  return {
    label: t(`priority.${normalizedPriority}`),
    color: meta.color,
    icon: meta.icon,
  }
})

const badgeColor = computed<BadgeColor>(() => config.value.color)

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
  <UBadge :color="badgeColor" variant="subtle" :size="size">
    <UIcon v-if="showIcon" :name="config.icon" :class="[iconSize, 'mr-1']" />
    {{ config.label }}
  </UBadge>
</template>
