// API plugin for making authenticated requests
export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const api = $fetch.create({
    baseURL: config.public.apiBase,
    
    onRequest({ options }) {
      // Add auth token to requests
      if (authStore.token) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${authStore.token}`,
        }
      }
      
      // Add common headers
      options.headers = {
        ...options.headers,
        Accept: 'application/json',
        'Content-Type': 'application/json',
      }
    },

    onResponseError({ response }) {
      // Handle 401 Unauthorized
      if (response.status === 401) {
        authStore.clearAuth()
        navigateTo('/login')
      }
    },
  })

  return {
    provide: {
      api,
    },
  }
})
