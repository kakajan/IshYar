/**
 * Composable for user operations with API
 */
export interface User {
  id: string
  name: string
  email: string
  avatar?: string
  phone?: string
  organization_id?: string
  department_id?: string
  position_id?: string
  role?: string
  is_active: boolean
  email_verified_at?: string
  settings?: Record<string, any>
  notification_preferences?: Record<string, boolean>
  department?: {
    id: string
    name: string
  }
  position?: {
    id: string
    title: string
  }
  created_at: string
  updated_at: string
}

export const useUsers = () => {
  const { $api } = useNuxtApp()
  
  const users = ref<User[]>([])
  const currentUser = ref<User | null>(null)
  const teamMembers = ref<User[]>([])
  const colleagues = ref<User[]>([])
  const isLoading = ref(false)
  const error = ref<Error | null>(null)
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  })
  
  const fetchUsers = async (filters: { department_id?: string; search?: string; page?: number } = {}) => {
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
        data: User[]
        meta: typeof meta.value
      }>(`/users?${params.toString()}`)
      
      users.value = response.data
      if (response.meta) {
        meta.value = response.meta
      }
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      console.error('Failed to fetch users:', err)
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchUser = async (id: string) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: User }>(`/users/${id}`)
      currentUser.value = response.data
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      return null
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchTeam = async () => {
    try {
      const response = await $api<{ data: User[] }>('/users/team')
      teamMembers.value = response.data
      return response.data
    } catch (err) {
      console.error('Failed to fetch team:', err)
      return []
    }
  }
  
  const fetchColleagues = async () => {
    try {
      const response = await $api<{ data: User[] }>('/users/colleagues')
      colleagues.value = response.data
      return response.data
    } catch (err) {
      console.error('Failed to fetch colleagues:', err)
      return []
    }
  }
  
  // Helper to get user initials
  const getInitials = (name: string) => {
    return name
      .split(' ')
      .map(part => part[0])
      .join('')
      .toUpperCase()
      .slice(0, 2)
  }
  
  return {
    users,
    currentUser,
    teamMembers,
    colleagues,
    isLoading,
    error,
    meta,
    fetchUsers,
    fetchUser,
    fetchTeam,
    fetchColleagues,
    getInitials,
  }
}
