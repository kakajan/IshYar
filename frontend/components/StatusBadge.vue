<script setup lang="ts">
import { Badge } from '~/components/ui/badge'
import {
  Clock,
  Play,
  Check,
  Pause,
  X,
  AlertTriangle,
  CheckCircle,
  MinusCircle,
  Ban,
} from 'lucide-vue-next'

const { t } = useI18n()

type BadgeVariant =
  | 'default'
  | 'secondary'
  | 'destructive'
  | 'outline'
  | 'success'
  | 'warning'
  | 'info'

interface Props {
  status: string
  /** Type of status badge */
  type?: 'task' | 'user' | 'general'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'general',
})

// Status key to variant and icon mappings
const statusMeta: Record<string, { variant: BadgeVariant; icon?: any }> = {
  pending: { variant: 'warning', icon: Clock },
  in_progress: { variant: 'info', icon: Play },
  completed: { variant: 'success', icon: Check },
  on_hold: { variant: 'warning', icon: Pause },
  cancelled: { variant: 'destructive', icon: X },
  overdue: { variant: 'destructive', icon: AlertTriangle },
  active: { variant: 'success', icon: CheckCircle },
  inactive: { variant: 'secondary', icon: MinusCircle },
  suspended: { variant: 'destructive', icon: Ban },
  online: { variant: 'success' },
  offline: { variant: 'secondary' },
  away: { variant: 'warning' },
  enabled: { variant: 'success' },
  disabled: { variant: 'destructive' },
  published: { variant: 'success' },
  draft: { variant: 'warning' },
  archived: { variant: 'secondary' },
}

const statusConfig = computed(() => {
  const normalizedStatus =
    props.status?.toLowerCase().replace(/\s+/g, '_') || 'unknown'
  const meta = statusMeta[normalizedStatus] || {
    variant: 'secondary' as BadgeVariant,
  }

  // Try to get translation, fallback to original status
  const translationKey = `status.${normalizedStatus}`
  const translatedLabel = t(translationKey)
  const label =
    translatedLabel !== translationKey ? translatedLabel : props.status

  return {
    label,
    variant: meta.variant,
    icon: meta.icon,
  }
})
</script>

<template>
  <Badge :variant="statusConfig.variant">
    <component
      :is="statusConfig.icon"
      v-if="statusConfig.icon"
      class="w-3 h-3 mr-1"
    />
    {{ statusConfig.label }}
  </Badge>
</template>
