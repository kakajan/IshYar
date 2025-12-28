<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardContent } from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Badge } from '~/components/ui/badge'
import { Checkbox } from '~/components/ui/checkbox'
import { Skeleton } from '~/components/ui/skeleton'
import {
  ClipboardList,
  CheckCircle,
  RefreshCw,
  AlertTriangle,
  Plus,
  Calendar,
  FileText,
} from 'lucide-vue-next'

const { t } = useI18n()
const authStore = useAuthStore()
const { stats: dashboardStats, isLoading, fetchDashboard } = useDashboard()

// Fetch dashboard data on mount
onMounted(() => {
  fetchDashboard()
})

// Icon components mapped
const iconMap = {
  'clipboard-list': ClipboardList,
  'check-circle': CheckCircle,
  refresh: RefreshCw,
  alert: AlertTriangle,
}

// Dashboard stats (computed from API or fallback)
const stats = computed(() => {
  const data = dashboardStats.value
  return [
    {
      name: t('dashboard.total_tasks'),
      value: data?.total_tasks?.toString() ?? '—',
      change: data ? `${data.completion_rate}%` : '',
      iconKey: 'clipboard-list',
    },
    {
      name: t('dashboard.completed_today'),
      value: data?.completed_tasks?.toString() ?? '—',
      change: '',
      iconKey: 'check-circle',
    },
    {
      name: t('dashboard.in_progress'),
      value: data?.in_progress_tasks?.toString() ?? '—',
      change: '',
      iconKey: 'refresh',
    },
    {
      name: t('dashboard.overdue'),
      value: data?.overdue_tasks?.toString() ?? '—',
      change: '',
      iconKey: 'alert',
    },
  ]
})

// Recent tasks from API
const recentTasks = computed(() => {
  return dashboardStats.value?.recent_tasks ?? []
})

type BadgeVariant =
  | 'default'
  | 'secondary'
  | 'destructive'
  | 'outline'
  | 'success'
  | 'warning'
  | 'info'

const priorityVariant = (priority: string): BadgeVariant => {
  const variants: Record<string, BadgeVariant> = {
    low: 'secondary',
    medium: 'warning',
    high: 'warning',
    urgent: 'destructive',
  }
  return variants[priority] || 'secondary'
}

const statusVariant = (status: string): BadgeVariant => {
  const variants: Record<string, BadgeVariant> = {
    pending: 'secondary',
    in_progress: 'info',
    completed: 'success',
    on_hold: 'warning',
    cancelled: 'destructive',
  }
  return variants[status] || 'secondary'
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Welcome header -->
    <div>
      <h1 class="text-2xl font-bold text-foreground">
        {{ $t('dashboard.welcome') }},
        {{ authStore.user?.name?.split(' ')[0] }}! 👋
      </h1>
      <p class="mt-1 text-muted-foreground">
        {{ $t('dashboard.subtitle') }}
      </p>
    </div>

    <!-- Loading state -->
    <div
      v-if="isLoading"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
    >
      <Card v-for="i in 4" :key="i">
        <CardContent class="p-6">
          <Skeleton class="h-4 w-1/2 mb-2" />
          <Skeleton class="h-8 w-1/4 mb-2" />
          <Skeleton class="h-3 w-1/3" />
        </CardContent>
      </Card>
    </div>

    <!-- Stats grid -->
    <div
      v-if="!isLoading"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
    >
      <Card v-for="stat in stats" :key="stat.name">
        <CardContent class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-muted-foreground">
                {{ stat.name }}
              </p>
              <p class="mt-1 text-3xl font-semibold text-foreground">
                {{ stat.value }}
              </p>
              <p
                v-if="stat.change"
                class="mt-1 text-sm"
                :class="
                  stat.change.startsWith('+') || stat.change.startsWith('-')
                    ? stat.change.startsWith('+')
                      ? 'text-green-600'
                      : 'text-red-600'
                    : 'text-muted-foreground'
                "
              >
                {{ stat.change }}
                <span
                  v-if="
                    stat.change.startsWith('+') || stat.change.startsWith('-')
                  "
                  >{{ $t('dashboard.from_last_week') }}</span
                >
              </p>
            </div>
            <div class="p-3 bg-primary/10 rounded-lg">
              <component
                :is="iconMap[stat.iconKey as keyof typeof iconMap]"
                class="w-6 h-6 text-primary"
              />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Main content grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent tasks -->
      <Card class="lg:col-span-2">
        <CardHeader class="flex flex-row items-center justify-between pb-2">
          <CardTitle class="text-lg">
            {{ $t('dashboard.recent_tasks') }}
          </CardTitle>
          <NuxtLink to="/tasks">
            <Button variant="ghost" size="sm">
              {{ $t('dashboard.view_all') }}
            </Button>
          </NuxtLink>
        </CardHeader>

        <CardContent>
          <div class="divide-y divide-border">
            <div
              v-for="task in recentTasks"
              :key="task.id"
              class="py-4 first:pt-0 last:pb-0 flex items-center justify-between"
            >
              <div class="flex items-center gap-4">
                <Checkbox :checked="task.status === 'completed'" />
                <div>
                  <p class="font-medium text-foreground">
                    {{ task.title }}
                  </p>
                  <p class="text-sm text-muted-foreground">
                    {{ $t('tasks.due_date') }} {{ task.due_date }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <Badge :variant="priorityVariant(task.priority)">
                  {{ task.priority }}
                </Badge>
                <Badge :variant="statusVariant(task.status)">
                  {{ task.status.replace('_', ' ') }}
                </Badge>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Quick actions -->
      <Card>
        <CardHeader>
          <CardTitle class="text-lg">
            {{ $t('dashboard.quick_actions') }}
          </CardTitle>
        </CardHeader>

        <CardContent class="space-y-3">
          <Button class="w-full justify-start" variant="outline">
            <Plus class="w-4 h-4 mr-2" />
            {{ $t('dashboard.create_task') }}
          </Button>
          <Button class="w-full justify-start" variant="outline">
            <Calendar class="w-4 h-4 mr-2" />
            {{ $t('dashboard.schedule_meeting') }}
          </Button>
          <Button class="w-full justify-start" variant="outline">
            <FileText class="w-4 h-4 mr-2" />
            {{ $t('dashboard.generate_report') }}
          </Button>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
