import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

let echoInstance: Echo | null = null

export function useWebSocket() {
  const isConnected = ref(false)
  const connectionError = ref<string | null>(null)

  const getEcho = () => {
    if (!import.meta.client) return null
    if (echoInstance) return echoInstance

    const config = useRuntimeConfig()
    const authStore = useAuthStore()

    const apiBase = config.public.apiBase as string
    const authEndpoint = apiBase.replace(/\/api\/v\d+$/i, '') + '/broadcasting/auth'

    ;(window as any).Pusher = Pusher

    echoInstance = new Echo({
      broadcaster: 'reverb',
      key: config.public.reverbAppKey as string,
      wsHost: config.public.reverbHost as string,
      wsPort: Number(config.public.reverbPort),
      wssPort: Number(config.public.reverbPort),
      forceTLS: (config.public.reverbScheme as string) === 'https',
      enabledTransports: ['ws', 'wss'],
      authEndpoint,
      auth: {
        headers: {
          Authorization: authStore.token ? `Bearer ${authStore.token}` : '',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    })

    const connection = (echoInstance as any).connector?.pusher?.connection

    if (connection) {
      connection.bind('connected', () => {
        isConnected.value = true
        connectionError.value = null
      })

      connection.bind('disconnected', () => {
        isConnected.value = false
      })

      connection.bind('error', (error: { error?: string }) => {
        connectionError.value = error?.error || 'Connection error'
      })
    }

    return echoInstance
  }

  const subscribe = (channelName: string, events: Record<string, (payload: any) => void>) => {
    const echo = getEcho()
    if (!echo) return

    const channel = echo.private(channelName)
    for (const [event, handler] of Object.entries(events)) {
      channel.listen(event, handler)
    }

    return channel
  }

  const leave = (channelName: string) => {
    const echo = getEcho()
    if (!echo) return
    echo.leave(`private-${channelName}`)
  }

  const disconnect = () => {
    if (!echoInstance) return
    echoInstance.disconnect()
    echoInstance = null
    isConnected.value = false
  }

  return {
    isConnected,
    connectionError,
    getEcho,
    subscribe,
    leave,
    disconnect,
  }
}
