/**
 * Composable for handling async API operations with loading and error states
 */
export const useApiData = <T>() => {
  const data = ref<T | null>(null) as Ref<T | null>
  const error = ref<Error | null>(null)
  const isLoading = ref(false)
  const isSuccess = ref(false)

  const execute = async (
    fn: () => Promise<T>,
    options?: {
      onSuccess?: (data: T) => void
      onError?: (error: Error) => void
    }
  ): Promise<T | null> => {
    isLoading.value = true
    error.value = null
    isSuccess.value = false

    try {
      const result = await fn()
      data.value = result
      isSuccess.value = true
      options?.onSuccess?.(result)
      return result
    } catch (err) {
      const e = err instanceof Error ? err : new Error(String(err))
      error.value = e
      options?.onError?.(e)
      return null
    } finally {
      isLoading.value = false
    }
  }

  const reset = () => {
    data.value = null
    error.value = null
    isLoading.value = false
    isSuccess.value = false
  }

  return {
    data,
    error,
    isLoading,
    isSuccess,
    execute,
    reset,
  }
}

/**
 * Composable for paginated API data
 */
export const usePaginatedData = <T>() => {
  const items = ref<T[]>([]) as Ref<T[]>
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  })
  const isLoading = ref(false)
  const error = ref<Error | null>(null)

  const fetchPage = async (
    fn: (page: number) => Promise<{ data: T[]; meta: typeof meta.value }>
  ) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await fn(meta.value.current_page)
      items.value = response.data
      meta.value = response.meta
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
    } finally {
      isLoading.value = false
    }
  }

  const goToPage = (page: number) => {
    if (page >= 1 && page <= meta.value.last_page) {
      meta.value.current_page = page
    }
  }

  const nextPage = () => {
    if (meta.value.current_page < meta.value.last_page) {
      meta.value.current_page++
    }
  }

  const prevPage = () => {
    if (meta.value.current_page > 1) {
      meta.value.current_page--
    }
  }

  const hasNextPage = computed(() => meta.value.current_page < meta.value.last_page)
  const hasPrevPage = computed(() => meta.value.current_page > 1)

  return {
    items,
    meta,
    isLoading,
    error,
    fetchPage,
    goToPage,
    nextPage,
    prevPage,
    hasNextPage,
    hasPrevPage,
  }
}

/**
 * Composable for form handling with validation
 */
export const useForm = <T extends Record<string, any>>(initialValues: T) => {
  const values = reactive({ ...initialValues }) as T
  const errors = reactive({} as Record<string, string>)
  const isDirty = ref(false)
  const isSubmitting = ref(false)

  const reset = () => {
    Object.assign(values, initialValues)
    Object.keys(errors).forEach((key) => delete errors[key])
    isDirty.value = false
    isSubmitting.value = false
  }

  const setError = (field: keyof T, message: string) => {
    errors[field as string] = message
  }

  const clearError = (field: keyof T) => {
    delete errors[field as string]
  }

  const clearErrors = () => {
    Object.keys(errors).forEach((key) => delete errors[key])
  }

  const setErrors = (serverErrors: Record<string, string[]>) => {
    clearErrors()
    Object.entries(serverErrors).forEach(([field, messages]) => {
      if (field in values && messages.length > 0 && messages[0]) {
        errors[field] = messages[0]
      }
    })
  }

  // Watch for changes to mark as dirty
  watch(
    () => values,
    () => {
      isDirty.value = true
    },
    { deep: true }
  )

  return {
    values,
    errors,
    isDirty,
    isSubmitting,
    reset,
    setError,
    clearError,
    clearErrors,
    setErrors,
  }
}
