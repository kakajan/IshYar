/**
 * Composable for task operations with API
 */
export interface Task {
  id: string
  title: string
  description?: string
  status: 'pending' | 'in_progress' | 'completed' | 'on_hold' | 'cancelled'
  priority: 'low' | 'medium' | 'high' | 'critical'
  due_date?: string
  start_date?: string
  estimated_time?: number
  actual_time?: number
  progress: number
  assignee_id?: string
  assignee?: {
    id: string
    name: string
    email: string
    avatar?: string
  }
  creator?: {
    id: string
    name: string
    email: string
  }
  department_id?: string
  department?: {
    id: string
    name: string
  }
  parent_id?: string
  tags?: string[]
  created_at: string
  updated_at: string
}

export interface TaskFilters {
  status?: string
  priority?: string
  assignee_id?: string
  department_id?: string
  search?: string
  due_date_from?: string
  due_date_to?: string
  page?: number
  per_page?: number
}

export const useTasks = () => {
  const { $api } = useNuxtApp()
  
  const tasks = ref<Task[]>([])
  const currentTask = ref<Task | null>(null)
  const isLoading = ref(false)
  const error = ref<Error | null>(null)
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  })
  
  const fetchTasks = async (filters: TaskFilters = {}) => {
    isLoading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          params.append(key, String(value))
        }
      })
      
      const response = await $api<{
        data: Task[]
        meta: typeof meta.value
      }>(`/tasks?${params.toString()}`)
      
      tasks.value = response.data
      if (response.meta) {
        meta.value = response.meta
      }
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      console.error('Failed to fetch tasks:', err)
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchMyTasks = async (filters: TaskFilters = {}) => {
    isLoading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          params.append(key, String(value))
        }
      })
      
      const response = await $api<{
        data: Task[]
        meta: typeof meta.value
      }>(`/my/tasks?${params.toString()}`)
      
      tasks.value = response.data
      if (response.meta) {
        meta.value = response.meta
      }
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchTodayTasks = async () => {
    try {
      const response = await $api<{ data: Task[] }>('/my/tasks/today')
      return response.data
    } catch (err) {
      console.error('Failed to fetch today tasks:', err)
      return []
    }
  }
  
  const fetchTask = async (id: string) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: Task }>(`/tasks/${id}`)
      currentTask.value = response.data
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      return null
    } finally {
      isLoading.value = false
    }
  }
  
  const createTask = async (data: Partial<Task>) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: Task; message: string }>('/tasks', {
        method: 'POST',
        body: data,
      })
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      throw err
    } finally {
      isLoading.value = false
    }
  }
  
  const updateTask = async (id: string, data: Partial<Task>) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: Task; message: string }>(`/tasks/${id}`, {
        method: 'PUT',
        body: data,
      })
      currentTask.value = response.data
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      throw err
    } finally {
      isLoading.value = false
    }
  }
  
  const deleteTask = async (id: string) => {
    try {
      await $api(`/tasks/${id}`, { method: 'DELETE' })
      tasks.value = tasks.value.filter(t => t.id !== id)
      return true
    } catch (err) {
      console.error('Failed to delete task:', err)
      return false
    }
  }
  
  const completeTask = async (id: string) => {
    try {
      const response = await $api<{ data: Task }>(`/tasks/${id}/complete`, {
        method: 'POST',
      })
      // Update local state
      const index = tasks.value.findIndex(t => t.id === id)
      if (index !== -1) {
        tasks.value[index] = response.data
      }
      return response.data
    } catch (err) {
      console.error('Failed to complete task:', err)
      throw err
    }
  }
  
  const startTask = async (id: string) => {
    try {
      const response = await $api<{ data: Task }>(`/tasks/${id}/start`, {
        method: 'POST',
      })
      const index = tasks.value.findIndex(t => t.id === id)
      if (index !== -1) {
        tasks.value[index] = response.data
      }
      return response.data
    } catch (err) {
      console.error('Failed to start task:', err)
      throw err
    }
  }
  
  return {
    tasks,
    currentTask,
    isLoading,
    error,
    meta,
    fetchTasks,
    fetchMyTasks,
    fetchTodayTasks,
    fetchTask,
    createTask,
    updateTask,
    deleteTask,
    completeTask,
    startTask,
  }
}
