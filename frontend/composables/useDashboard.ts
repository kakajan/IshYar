/**
 * Composable for dashboard data from API
 */
export const useDashboard = () => {
  const { $api } = useNuxtApp()
  
  interface DashboardStats {
    total_tasks: number
    completed_tasks: number
    in_progress_tasks: number
    overdue_tasks: number
    pending_tasks: number
    today_tasks: number
    completion_rate: number
    tasks_by_priority: Record<string, number>
    tasks_by_status: Record<string, number>
    recent_tasks: any[]
    upcoming_tasks: any[]
  }
  
  const stats = ref<DashboardStats | null>(null)
  const isLoading = ref(false)
  const error = ref<Error | null>(null)
  
  const fetchDashboard = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: DashboardStats }>('/dashboard')
      stats.value = response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      console.error('Failed to fetch dashboard:', err)
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchTeamStats = async () => {
    try {
      return await $api<{ data: any }>('/dashboard/team')
    } catch (err) {
      console.error('Failed to fetch team stats:', err)
      return null
    }
  }
  
  return {
    stats,
    isLoading,
    error,
    fetchDashboard,
    fetchTeamStats,
  }
}
