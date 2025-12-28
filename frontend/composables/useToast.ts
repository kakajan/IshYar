export interface Toast {
  id: string
  title?: string
  description?: string
  variant?: 'default' | 'success' | 'error' | 'warning' | 'destructive'
  duration?: number
}

interface ToastOptions {
  title?: string
  description?: string
  color?: 'success' | 'error' | 'warning' | 'info'
  variant?: 'default' | 'success' | 'error' | 'warning' | 'destructive'
  duration?: number
}

const toasts = ref<Toast[]>([])

export const useToast = () => {
  const add = (options: ToastOptions) => {
    const id = Math.random().toString(36).substr(2, 9)
    // Support both 'color' and 'variant' properties
    let variant: Toast['variant'] = 'default'
    if (options.variant) {
      variant = options.variant
    } else if (options.color) {
      variant = options.color === 'info' ? 'default' : (options.color as Toast['variant'])
    }
    
    const toast: Toast = {
      id,
      title: options.title,
      description: options.description,
      variant,
      duration: options.duration ?? 5000,
    }
    
    toasts.value.push(toast)
    
    if (toast.duration && toast.duration > 0) {
      setTimeout(() => {
        dismiss(id)
      }, toast.duration)
    }
    
    return id
  }
  
  const dismiss = (id: string) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }
  
  const dismissAll = () => {
    toasts.value = []
  }
  
  return {
    toasts,
    add,
    dismiss,
    dismissAll,
  }
}
