// Auth store using Pinia
export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isLoading = ref(false)

  // Initialize from localStorage
  const init = async () => {
    if (!import.meta.client) return

    const storedToken = localStorage.getItem('auth_token')
    if (!storedToken) return

    token.value = storedToken

    // If we have a token but no user yet, fetch profile before route guards run.
    if (!user.value) {
      await fetchUser()
    }
  }

  // Set token
  const setToken = (newToken: string) => {
    token.value = newToken
    if (import.meta.client) {
      localStorage.setItem('auth_token', newToken)
    }
  }

  // Clear auth
  const clearAuth = () => {
    user.value = null
    token.value = null
    if (import.meta.client) {
      localStorage.removeItem('auth_token')
    }
  }

  // Login
  const login = async (email: string, password: string) => {
    isLoading.value = true
    try {
      const { $api } = useNuxtApp()
      const response = await $api('/auth/login', {
        method: 'POST',
        body: { email, password },
      })
      
      setToken(response.data.token)
      user.value = response.data.user
      
      return { success: true }
    } catch (error: any) {
      return { 
        success: false, 
        error: error.data?.message || 'Login failed' 
      }
    } finally {
      isLoading.value = false
    }
  }

  // Register
  const register = async (name: string, email: string, password: string, password_confirmation: string) => {
    isLoading.value = true
    try {
      const { $api } = useNuxtApp()
      const response = await $api('/auth/register', {
        method: 'POST',
        body: { name, email, password, password_confirmation },
      })
      
      setToken(response.data.token)
      user.value = response.data.user
      
      return { success: true }
    } catch (error: any) {
      return { 
        success: false, 
        error: error.data?.message || 'Registration failed',
        errors: error.data?.errors,
      }
    } finally {
      isLoading.value = false
    }
  }

  // Fetch current user
  const fetchUser = async () => {
    if (!token.value) return
    
    isLoading.value = true
    try {
      const { $api } = useNuxtApp()
      const response = await $api('/auth/me')
      user.value = response.data.user
    } catch (error) {
      clearAuth()
    } finally {
      isLoading.value = false
    }
  }

  // Logout
  const logout = async () => {
    try {
      const { $api } = useNuxtApp()
      await $api('/auth/logout', { method: 'POST' })
    } catch (error) {
      // Ignore errors on logout
    } finally {
      clearAuth()
      navigateTo('/login')
    }
  }

  // Refresh token
  const refreshToken = async () => {
    try {
      const { $api } = useNuxtApp()
      const response = await $api('/auth/refresh', { method: 'POST' })
      setToken(response.data.token)
      return true
    } catch (error) {
      clearAuth()
      return false
    }
  }

  return {
    // State
    user,
    token,
    isAuthenticated,
    isLoading,
    // Actions
    init,
    login,
    register,
    logout,
    fetchUser,
    refreshToken,
    clearAuth,
  }
})

// Types
interface User {
  id: string
  name: string
  email: string
  phone?: string
  avatar?: string
  timezone: string
  locale: string
  department?: {
    id: string
    name: string
  }
  position?: {
    id: string
    name: string
  }
  roles?: Array<{ name: string }>
  permissions?: Array<{ name: string }>
  is_active: boolean
  created_at: string
}

// Auto-import for Pinia
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useAuthStore, import.meta.hot))
}
