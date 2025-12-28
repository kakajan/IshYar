// API plugin for making authenticated requests with automatic token refresh
export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  let isRefreshing = false
  let refreshPromise: Promise<void> | null = null

  const api = $fetch.create({
    baseURL: config.public.apiBase as string,

    async onRequest({ options }) {
      // Get current locale
      const { locale } = useI18n()
      
      // Add auth token to requests
      const headers: Record<string, string> = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'Accept-Language': locale.value || 'en',
      }
      
      if (authStore.token) {
        headers.Authorization = `Bearer ${authStore.token}`
      }

      options.headers = new Headers({
        ...Object.fromEntries((options.headers as Headers)?.entries?.() || []),
        ...headers,
      })
    },

    async onResponseError({ request, response, options }) {
      // Handle 401 Unauthorized - attempt token refresh
      if (response.status === 401 && authStore.token) {
        // Don't refresh if already on auth routes
        const url = typeof request === 'string' ? request : request.url
        if (url.includes('/auth/login') || url.includes('/auth/refresh')) {
          authStore.clearAuth()
          navigateTo('/login')
          return
        }

        // If already refreshing, wait for it
        if (isRefreshing && refreshPromise) {
          await refreshPromise
          // Retry the original request
          return $fetch(request, {
            ...options,
            headers: new Headers({
              ...Object.fromEntries((options.headers as Headers)?.entries?.() || []),
              Authorization: `Bearer ${authStore.token}`,
            }),
          } as any)
        }

        // Try to refresh the token
        isRefreshing = true
        refreshPromise = (async () => {
          try {
            const refreshResponse = await $fetch<{
              data: { token: string }
            }>(`${config.public.apiBase}/auth/refresh`, {
              method: 'POST',
              headers: {
                Authorization: `Bearer ${authStore.token}`,
              },
            })

            if (refreshResponse.data?.token) {
              authStore.token = refreshResponse.data.token
            }
          } catch (error) {
            // Refresh failed, clear auth and redirect
            authStore.clearAuth()
            navigateTo('/login')
            throw error
          } finally {
            isRefreshing = false
            refreshPromise = null
          }
        })()

        await refreshPromise

        // Retry the original request with new token
        return $fetch(request, {
          ...options,
          headers: new Headers({
            ...Object.fromEntries((options.headers as Headers)?.entries?.() || []),
            Authorization: `Bearer ${authStore.token}`,
          }),
        } as any)
      }

      // Handle other errors
      if (response.status === 403) {
        console.error('Access forbidden:', response._data)
      }

      if (response.status >= 500) {
        console.error('Server error:', response._data)
      }
    },
  })

  return {
    provide: {
      api,
    },
  }
})
