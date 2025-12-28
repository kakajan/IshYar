/**
 * Composable for confirmation dialogs
 */

interface ConfirmOptions {
  title?: string
  message: string
  confirmText?: string
  cancelText?: string
  confirmColor?: 'primary' | 'error' | 'warning' | 'success'
  icon?: string
}

interface ConfirmState {
  isOpen: boolean
  options: ConfirmOptions
  resolve: ((value: boolean) => void) | null
}

const state = reactive<ConfirmState>({
  isOpen: false,
  options: {
    title: 'Confirm',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    confirmColor: 'primary',
    icon: 'i-heroicons-question-mark-circle',
  },
  resolve: null,
})

export const useConfirm = () => {
  const confirm = (options: ConfirmOptions): Promise<boolean> => {
    return new Promise((resolve) => {
      state.options = {
        title: options.title || 'Confirm',
        message: options.message,
        confirmText: options.confirmText || 'Confirm',
        cancelText: options.cancelText || 'Cancel',
        confirmColor: options.confirmColor || 'primary',
        icon: options.icon || 'i-heroicons-question-mark-circle',
      }
      state.resolve = resolve
      state.isOpen = true
    })
  }

  const confirmDelete = (itemName?: string): Promise<boolean> => {
    return confirm({
      title: 'Delete Confirmation',
      message: itemName
        ? `Are you sure you want to delete "${itemName}"? This action cannot be undone.`
        : 'Are you sure you want to delete this item? This action cannot be undone.',
      confirmText: 'Delete',
      cancelText: 'Cancel',
      confirmColor: 'error',
      icon: 'i-heroicons-trash',
    })
  }

  const confirmUnsavedChanges = (): Promise<boolean> => {
    return confirm({
      title: 'Unsaved Changes',
      message: 'You have unsaved changes. Are you sure you want to leave without saving?',
      confirmText: 'Leave',
      cancelText: 'Stay',
      confirmColor: 'warning',
      icon: 'i-heroicons-exclamation-triangle',
    })
  }

  const handleConfirm = () => {
    state.resolve?.(true)
    state.isOpen = false
    state.resolve = null
  }

  const handleCancel = () => {
    state.resolve?.(false)
    state.isOpen = false
    state.resolve = null
  }

  return {
    // State (for the dialog component)
    isOpen: computed(() => state.isOpen),
    options: computed(() => state.options),
    handleConfirm,
    handleCancel,
    // Actions (for using in components)
    confirm,
    confirmDelete,
    confirmUnsavedChanges,
  }
}
