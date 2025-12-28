// Auth middleware to protect routes
export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  // Initialize auth on first load
  if (import.meta.client && !authStore.token) {
    await authStore.init()
  }

  // If we already have a token but user isn't loaded yet, fetch it once.
  if (import.meta.client && authStore.token && !authStore.user && !authStore.isLoading) {
    await authStore.fetchUser()
  }

  // Public routes that don't require auth
  const publicRoutes = ['/login', '/register', '/forgot-password', '/reset-password']
  
  if (publicRoutes.includes(to.path)) {
    // Redirect to dashboard if already authenticated
    if (authStore.isAuthenticated) {
      return navigateTo('/dashboard')
    }
    return
  }

  // Require authentication for all other routes
  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }
})
