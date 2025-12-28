<script setup lang="ts">
const { t } = useI18n()

interface Props {
  status: string
  /** Type of status badge */
  type?: 'task' | 'user' | 'general'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'general',
})

// Status key to color and icon mappings
const statusMeta: Record<string, { color: string; icon?: string }> = {
  pending: { color: 'warning', icon: 'i-heroicons-clock' },
  in_progress: { color: 'info', icon: 'i-heroicons-play' },
  completed: { color: 'success', icon: 'i-heroicons-check' },
  on_hold: { color: 'warning', icon: 'i-heroicons-pause' },
  cancelled: { color: 'error', icon: 'i-heroicons-x-mark' },
  overdue: { color: 'error', icon: 'i-heroicons-exclamation-triangle' },
  active: { color: 'success', icon: 'i-heroicons-check-circle' },
  inactive: { color: 'neutral', icon: 'i-heroicons-minus-circle' },
  suspended: { color: 'error', icon: 'i-heroicons-no-symbol' },
  online: { color: 'success' },
  offline: { color: 'neutral' },
  away: { color: 'warning' },
  enabled: { color: 'success' },
  disabled: { color: 'error' },
  published: { color: 'success' },
  draft: { color: 'warning' },
  archived: { color: 'neutral' },
}

const statusConfig = computed(() => {
  const normalizedStatus =
    props.status?.toLowerCase().replace(/\s+/g, '_') || 'unknown'
  const meta = statusMeta[normalizedStatus] || { color: 'neutral' }

  // Try to get translation, fallback to original status
  const translationKey = `status.${normalizedStatus}`
  const translatedLabel = t(translationKey)
  const label =
    translatedLabel !== translationKey ? translatedLabel : props.status

  return {
    label,
    color: meta.color,
    icon: meta.icon,
  }
})
</script>

<template>
  <UBadge :color="statusConfig.color as any" variant="subtle">
    <UIcon
      v-if="statusConfig.icon"
      :name="statusConfig.icon"
      class="w-3 h-3 mr-1"
    />
    {{ statusConfig.label }}
  </UBadge>
</template>
