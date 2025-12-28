<script setup lang="ts">
import { Badge } from '~/components/ui/badge'
import {
  ArrowDown,
  Minus,
  ArrowUp,
  AlertTriangle,
  Flame,
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

const priorityMeta: Record<string, { variant: BadgeVariant; icon: any }> = {
  low: { variant: 'secondary', icon: ArrowDown },
  medium: { variant: 'warning', icon: Minus },
  high: { variant: 'warning', icon: ArrowUp },
  urgent: { variant: 'destructive', icon: AlertTriangle },
  critical: { variant: 'destructive', icon: Flame },
}

const config = computed(() => {
  const normalizedPriority = props.priority?.toLowerCase() || 'medium'
  const meta = priorityMeta[normalizedPriority] ?? priorityMeta.medium!

  return {
    label: t(`priority.${normalizedPriority}`),
    variant: meta.variant,
    icon: meta.icon,
  }
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
  <Badge :variant="config.variant">
    <component :is="config.icon" v-if="showIcon" :class="[iconSize, 'mr-1']" />
    {{ config.label }}
  </Badge>
</template>
