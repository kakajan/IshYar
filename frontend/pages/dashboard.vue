<script setup lang="ts">
const { t } = useI18n()
const authStore = useAuthStore()
const { stats: dashboardStats, isLoading, fetchDashboard } = useDashboard()

// Fetch dashboard data on mount
onMounted(() => {
  fetchDashboard()
})

// Dashboard stats (computed from API or fallback)
const stats = computed(() => {
  const data = dashboardStats.value
  return [
    {
      name: t('dashboard.total_tasks'),
      value: data?.total_tasks?.toString() ?? '—',
      change: data ? `${data.completion_rate}%` : '',
      icon: 'i-heroicons-clipboard-document-list',
    },
    {
      name: t('dashboard.completed_today'),
      value: data?.completed_tasks?.toString() ?? '—',
      change: '',
      icon: 'i-heroicons-check-circle',
    },
    {
      name: t('dashboard.in_progress'),
      value: data?.in_progress_tasks?.toString() ?? '—',
      change: '',
      icon: 'i-heroicons-arrow-path',
    },
    {
      name: t('dashboard.overdue'),
      value: data?.overdue_tasks?.toString() ?? '—',
      change: '',
      icon: 'i-heroicons-exclamation-triangle',
    },
  ]
})

// Recent tasks from API
const recentTasks = computed(() => {
  return dashboardStats.value?.recent_tasks ?? []
})

type BadgeColor =
  | 'error'
  | 'primary'
  | 'secondary'
  | 'success'
  | 'info'
  | 'warning'
  | 'neutral'

const priorityColor = (priority: string): BadgeColor => {
  const colors: Record<string, BadgeColor> = {
    low: 'neutral',
    medium: 'warning',
    high: 'warning',
    urgent: 'error',
  }
  return colors[priority] || 'neutral'
}

const statusColor = (status: string): BadgeColor => {
  const colors: Record<string, BadgeColor> = {
    pending: 'neutral',
    in_progress: 'info',
    completed: 'success',
    on_hold: 'warning',
    cancelled: 'error',
  }
  return colors[status] || 'neutral'
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Welcome header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ $t('dashboard.welcome') }},
        {{ authStore.user?.name?.split(' ')[0] }}! 👋
      </h1>
      <p class="mt-1 text-gray-600 dark:text-gray-400">
        {{ $t('dashboard.subtitle') }}
      </p>
    </div>

    <!-- Loading state -->
    <div
      v-if="isLoading"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
    >
      <UCard v-for="i in 4" :key="i" class="glass">
        <div class="animate-pulse">
          <div
            class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2"
          ></div>
          <div
            class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-2"
          ></div>
          <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
        </div>
      </UCard>
    </div>

    <!-- Stats grid -->
    <div
      v-if="!isLoading"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
    >
      <UCard v-for="stat in stats" :key="stat.name" class="glass">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ stat.name }}
            </p>
            <p
              class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white"
            >
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
                  : 'text-gray-500'
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
          <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
            <UIcon
              :name="stat.icon"
              class="w-6 h-6 text-primary-600 dark:text-primary-400"
            />
          </div>
        </div>
      </UCard>
    </div>

    <!-- Main content grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent tasks -->
      <UCard class="lg:col-span-2">
        <template #header>
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">
              {{ $t('dashboard.recent_tasks') }}
            </h2>
            <NuxtLink to="/tasks">
              <UButton variant="ghost" size="sm">{{
                $t('dashboard.view_all')
              }}</UButton>
            </NuxtLink>
          </div>
        </template>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="task in recentTasks"
            :key="task.id"
            class="py-4 first:pt-0 last:pb-0 flex items-center justify-between"
          >
            <div class="flex items-center gap-4">
              <UCheckbox :model-value="task.status === 'completed'" />
              <div>
                <p class="font-medium text-gray-900 dark:text-white">
                  {{ task.title }}
                </p>
                <p class="text-sm text-gray-500">
                  {{ $t('tasks.due_date') }} {{ task.due_date }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <UBadge
                :color="priorityColor(task.priority)"
                variant="subtle"
                size="sm"
              >
                {{ task.priority }}
              </UBadge>
              <UBadge
                :color="statusColor(task.status)"
                variant="subtle"
                size="sm"
              >
                {{ task.status.replace('_', ' ') }}
              </UBadge>
            </div>
          </div>
        </div>
      </UCard>

      <!-- Quick actions -->
      <UCard>
        <template #header>
          <h2 class="text-lg font-semibold">
            {{ $t('dashboard.quick_actions') }}
          </h2>
        </template>

        <div class="space-y-3">
          <UButton block variant="outline" icon="i-heroicons-plus">
            {{ $t('dashboard.create_task') }}
          </UButton>
          <UButton block variant="outline" icon="i-heroicons-calendar">
            {{ $t('dashboard.schedule_meeting') }}
          </UButton>
          <UButton block variant="outline" icon="i-heroicons-document-text">
            {{ $t('dashboard.generate_report') }}
          </UButton>
        </div>
      </UCard>
    </div>
  </div>
</template>
