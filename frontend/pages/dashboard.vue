<script setup lang="ts">
const { t } = useI18n()
const authStore = useAuthStore()

// Dashboard stats (computed for i18n reactivity)
const stats = computed(() => [
  {
    name: t('dashboard.total_tasks'),
    value: '24',
    change: '+12%',
    icon: 'i-heroicons-clipboard-document-list',
  },
  {
    name: t('dashboard.completed_today'),
    value: '8',
    change: '+23%',
    icon: 'i-heroicons-check-circle',
  },
  {
    name: t('dashboard.in_progress'),
    value: '12',
    change: '-5%',
    icon: 'i-heroicons-arrow-path',
  },
  {
    name: t('dashboard.overdue'),
    value: '4',
    change: '+2%',
    icon: 'i-heroicons-exclamation-triangle',
  },
])

const recentTasks = ref([
  {
    id: '1',
    title: 'Review quarterly report',
    status: 'in_progress',
    priority: 'high',
    due_date: '2025-12-28',
  },
  {
    id: '2',
    title: 'Team meeting preparation',
    status: 'pending',
    priority: 'medium',
    due_date: '2025-12-29',
  },
  {
    id: '3',
    title: 'Client proposal draft',
    status: 'completed',
    priority: 'high',
    due_date: '2025-12-27',
  },
])

const priorityColor = (priority: string) => {
  const colors: Record<string, string> = {
    low: 'gray',
    medium: 'yellow',
    high: 'orange',
    urgent: 'red',
  }
  return colors[priority] || 'gray'
}

const statusColor = (status: string) => {
  const colors: Record<string, string> = {
    pending: 'gray',
    in_progress: 'blue',
    completed: 'green',
    on_hold: 'yellow',
    cancelled: 'red',
  }
  return colors[status] || 'gray'
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Welcome header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ $t('dashboard.welcome') }}, {{ authStore.user?.name?.split(' ')[0] }}! 👋
      </h1>
      <p class="mt-1 text-gray-600 dark:text-gray-400">
        {{ $t('dashboard.subtitle') }}
      </p>
    </div>

    <!-- Stats grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
              class="mt-1 text-sm"
              :class="
                stat.change.startsWith('+') ? 'text-green-600' : 'text-red-600'
              "
            >
              {{ stat.change }} {{ $t('dashboard.from_last_week') }}
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
            <h2 class="text-lg font-semibold">{{ $t('dashboard.recent_tasks') }}</h2>
            <NuxtLink to="/tasks">
              <UButton variant="ghost" size="sm">{{ $t('dashboard.view_all') }}</UButton>
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
                <p class="text-sm text-gray-500">{{ $t('tasks.due_date') }} {{ task.due_date }}</p>
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
          <h2 class="text-lg font-semibold">{{ $t('dashboard.quick_actions') }}</h2>
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
