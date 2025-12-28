<script setup lang="ts">
interface Props {
  status: string
  /** Type of status badge */
  type?: 'task' | 'user' | 'general'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'general',
})

// Task status mappings
const taskStatusMap: Record<string, { label: string; color: string; icon?: string }> = {
  pending: { label: 'Pending', color: 'warning', icon: 'i-heroicons-clock' },
  in_progress: { label: 'In Progress', color: 'info', icon: 'i-heroicons-play' },
  completed: { label: 'Completed', color: 'success', icon: 'i-heroicons-check' },
  on_hold: { label: 'On Hold', color: 'warning', icon: 'i-heroicons-pause' },
  cancelled: { label: 'Cancelled', color: 'error', icon: 'i-heroicons-x-mark' },
  overdue: { label: 'Overdue', color: 'error', icon: 'i-heroicons-exclamation-triangle' },
}

// User status mappings
const userStatusMap: Record<string, { label: string; color: string; icon?: string }> = {
  active: { label: 'Active', color: 'success', icon: 'i-heroicons-check-circle' },
  inactive: { label: 'Inactive', color: 'neutral', icon: 'i-heroicons-minus-circle' },
  pending: { label: 'Pending', color: 'warning', icon: 'i-heroicons-clock' },
  suspended: { label: 'Suspended', color: 'error', icon: 'i-heroicons-no-symbol' },
  online: { label: 'Online', color: 'success' },
  offline: { label: 'Offline', color: 'neutral' },
  away: { label: 'Away', color: 'warning' },
}

// General status mappings
const generalStatusMap: Record<string, { label: string; color: string }> = {
  active: { label: 'Active', color: 'success' },
  inactive: { label: 'Inactive', color: 'neutral' },
  enabled: { label: 'Enabled', color: 'success' },
  disabled: { label: 'Disabled', color: 'error' },
  published: { label: 'Published', color: 'success' },
  draft: { label: 'Draft', color: 'warning' },
  archived: { label: 'Archived', color: 'neutral' },
}

const statusConfig = computed(() => {
  const normalizedStatus = props.status?.toLowerCase().replace(/\s+/g, '_') || 'unknown'
  
  if (props.type === 'task') {
    return taskStatusMap[normalizedStatus] || { label: props.status, color: 'neutral' }
  }
  if (props.type === 'user') {
    return userStatusMap[normalizedStatus] || { label: props.status, color: 'neutral' }
  }
  return generalStatusMap[normalizedStatus] || { label: props.status, color: 'neutral' }
})
</script>

<template>
  <UBadge :color="statusConfig.color as any" variant="subtle">
    <UIcon v-if="statusConfig.icon" :name="statusConfig.icon" class="w-3 h-3 mr-1" />
    {{ statusConfig.label }}
  </UBadge>
</template>
