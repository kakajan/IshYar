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
    title: '',
    message: '',
    confirmText: '',
    cancelText: '',
    confirmColor: 'primary',
    icon: 'i-heroicons-question-mark-circle',
  },
  resolve: null,
})

export const useConfirm = () => {
  const { t } = useI18n()

  const confirm = (options: ConfirmOptions): Promise<boolean> => {
    return new Promise((resolve) => {
      state.options = {
        title: options.title || t('confirm.title'),
        message: options.message,
        confirmText: options.confirmText || t('common.confirm'),
        cancelText: options.cancelText || t('common.cancel'),
        confirmColor: options.confirmColor || 'primary',
        icon: options.icon || 'i-heroicons-question-mark-circle',
      }
      state.resolve = resolve
      state.isOpen = true
    })
  }

  const confirmDelete = (itemName?: string): Promise<boolean> => {
    return confirm({
      title: t('confirm.delete_title'),
      message: itemName
        ? t('confirm.delete_message').replace('this item', `"${itemName}"`)
        : t('confirm.delete_message'),
      confirmText: t('common.delete'),
      cancelText: t('common.cancel'),
      confirmColor: 'error',
      icon: 'i-heroicons-trash',
    })
  }

  const confirmUnsavedChanges = (): Promise<boolean> => {
    return confirm({
      title: t('confirm.unsaved_title'),
      message: t('confirm.unsaved_message'),
      confirmText: t('confirm.leave'),
      cancelText: t('confirm.stay'),
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
