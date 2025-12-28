<script setup lang="ts">
const { t } = useI18n()

// Mock notifications - in production these would come from an API/websocket
const notifications = ref([
  {
    id: '1',
    type: 'task',
    title: 'New task assigned',
    message: 'You have been assigned to "Review quarterly report"',
    read: false,
    created_at: new Date(Date.now() - 1000 * 60 * 5).toISOString(), // 5 mins ago
  },
  {
    id: '2',
    type: 'comment',
    title: 'New comment',
    message: 'John commented on "Client proposal draft"',
    read: false,
    created_at: new Date(Date.now() - 1000 * 60 * 30).toISOString(), // 30 mins ago
  },
  {
    id: '3',
    type: 'system',
    title: 'System update',
    message: 'IshYar has been updated to version 1.0.1',
    read: true,
    created_at: new Date(Date.now() - 1000 * 60 * 60 * 2).toISOString(), // 2 hours ago
  },
])

const unreadCount = computed(() => notifications.value.filter((n) => !n.read).length)

const iconMap: Record<string, string> = {
  task: 'i-heroicons-clipboard-document-list',
  comment: 'i-heroicons-chat-bubble-left-ellipsis',
  system: 'i-heroicons-cog-6-tooth',
  mention: 'i-heroicons-at-symbol',
  deadline: 'i-heroicons-clock',
}

const colorMap: Record<string, string> = {
  task: 'text-blue-500',
  comment: 'text-green-500',
  system: 'text-gray-500',
  mention: 'text-purple-500',
  deadline: 'text-orange-500',
}

const markAsRead = (notification: (typeof notifications.value)[0]) => {
  notification.read = true
  // In production: API call to mark as read
}

const markAllAsRead = () => {
  notifications.value.forEach((n) => (n.read = true))
  // In production: API call to mark all as read
}

const formatTime = (dateStr: string) => {
  const date = new Date(dateStr)
  const now = new Date()
  const diffMs = now.getTime() - date.getTime()
  const diffMins = Math.floor(diffMs / 60000)
  const diffHours = Math.floor(diffMins / 60)
  const diffDays = Math.floor(diffHours / 24)

  if (diffMins < 1) return 'Just now'
  if (diffMins < 60) return `${diffMins}m ago`
  if (diffHours < 24) return `${diffHours}h ago`
  if (diffDays < 7) return `${diffDays}d ago`
  return date.toLocaleDateString()
}
</script>

<template>
  <UPopover>
    <UButton variant="ghost" icon="i-heroicons-bell" class="relative">
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-medium text-white"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </UButton>

    <template #content>
      <div class="w-80">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <h3 class="font-semibold">Notifications</h3>
          <UButton
            v-if="unreadCount > 0"
            variant="ghost"
            size="xs"
            @click="markAllAsRead"
          >
            Mark all read
          </UButton>
        </div>

        <!-- Notifications list -->
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            :class="[
              'flex gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors',
              !notification.read && 'bg-blue-50/50 dark:bg-blue-900/10',
            ]"
            @click="markAsRead(notification)"
          >
            <div
              :class="[
                'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700',
                colorMap[notification.type],
              ]"
            >
              <UIcon :name="iconMap[notification.type] || 'i-heroicons-bell'" class="w-4 h-4" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2">
                <p :class="['text-sm font-medium truncate', !notification.read && 'text-gray-900 dark:text-white']">
                  {{ notification.title }}
                </p>
                <span
                  v-if="!notification.read"
                  class="flex-shrink-0 w-2 h-2 rounded-full bg-blue-500"
                />
              </div>
              <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                {{ notification.message }}
              </p>
              <p class="text-xs text-gray-400 mt-1">
                {{ formatTime(notification.created_at) }}
              </p>
            </div>
          </div>

          <!-- Empty state -->
          <div
            v-if="notifications.length === 0"
            class="px-4 py-8 text-center text-gray-500"
          >
            <UIcon name="i-heroicons-bell-slash" class="w-8 h-8 mx-auto mb-2 text-gray-400" />
            <p>No notifications yet</p>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
          <NuxtLink
            to="/settings"
            class="text-sm text-primary-600 hover:text-primary-500"
          >
            Manage notification settings
          </NuxtLink>
        </div>
      </div>
    </template>
  </UPopover>
</template>
