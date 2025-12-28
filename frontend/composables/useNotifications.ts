/**
 * Composable for notification operations with API
 */
export interface Notification {
  id: string
  type: string
  title: string
  body?: string
  data?: Record<string, any>
  action_url?: string
  read_at?: string
  created_at: string
}

export const useNotifications = () => {
  const { $api } = useNuxtApp()
  
  const notifications = ref<Notification[]>([])
  const unreadCount = ref(0)
  const isLoading = ref(false)
  const error = ref<Error | null>(null)
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
  })
  
  const fetchNotifications = async (unreadOnly = false) => {
    isLoading.value = true
    error.value = null
    
    try {
      const params = unreadOnly ? '?unread=1' : ''
      const response = await $api<{
        data: Notification[]
        meta: typeof meta.value
      }>(`/notifications${params}`)
      
      notifications.value = response.data
      if (response.meta) {
        meta.value = response.meta
      }
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      console.error('Failed to fetch notifications:', err)
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchUnreadCount = async () => {
    try {
      const response = await $api<{ data: { count: number } }>('/notifications/unread-count')
      unreadCount.value = response.data.count
    } catch (err) {
      console.error('Failed to fetch unread count:', err)
    }
  }
  
  const markAsRead = async (id: string) => {
    try {
      await $api(`/notifications/${id}/read`, { method: 'POST' })
      // Update local state
      const notification = notifications.value.find(n => n.id === id)
      if (notification) {
        notification.read_at = new Date().toISOString()
      }
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    } catch (err) {
      console.error('Failed to mark notification as read:', err)
    }
  }
  
  const markAllAsRead = async () => {
    try {
      await $api('/notifications/read-all', { method: 'POST' })
      // Update local state
      notifications.value.forEach(n => {
        if (!n.read_at) {
          n.read_at = new Date().toISOString()
        }
      })
      unreadCount.value = 0
    } catch (err) {
      console.error('Failed to mark all as read:', err)
    }
  }
  
  const deleteNotification = async (id: string) => {
    try {
      await $api(`/notifications/${id}`, { method: 'DELETE' })
      notifications.value = notifications.value.filter(n => n.id !== id)
    } catch (err) {
      console.error('Failed to delete notification:', err)
    }
  }
  
  const getPreferences = async () => {
    try {
      const response = await $api<{ data: Record<string, boolean> }>('/notifications/preferences')
      return response.data
    } catch (err) {
      console.error('Failed to fetch notification preferences:', err)
      return null
    }
  }
  
  const updatePreferences = async (preferences: Record<string, boolean>) => {
    try {
      const response = await $api<{ data: Record<string, boolean> }>('/notifications/preferences', {
        method: 'PUT',
        body: preferences,
      })
      return response.data
    } catch (err) {
      console.error('Failed to update notification preferences:', err)
      throw err
    }
  }
  
  // Polling for new notifications (every 30 seconds)
  let pollInterval: ReturnType<typeof setInterval> | null = null
  
  const startPolling = (intervalMs = 30000) => {
    if (pollInterval) return
    pollInterval = setInterval(fetchUnreadCount, intervalMs)
  }
  
  const stopPolling = () => {
    if (pollInterval) {
      clearInterval(pollInterval)
      pollInterval = null
    }
  }
  
  // Clean up on unmount
  onUnmounted(() => {
    stopPolling()
  })
  
  return {
    notifications,
    unreadCount,
    isLoading,
    error,
    meta,
    fetchNotifications,
    fetchUnreadCount,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    getPreferences,
    updatePreferences,
    startPolling,
    stopPolling,
  }
}
