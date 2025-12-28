/**
 * Composable for managing color mode (light/dark/system)
 */
type ColorMode = 'light' | 'dark' | 'system'

const colorModePreference = ref<ColorMode>('system')
const resolvedColorMode = ref<'light' | 'dark'>('light')

// Initialize on client-side only
if (import.meta.client) {
  // Get stored preference
  const stored = localStorage.getItem('color-mode') as ColorMode | null
  if (stored && ['light', 'dark', 'system'].includes(stored)) {
    colorModePreference.value = stored
  }

  // Watch for system preference changes
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  
  const updateResolvedMode = () => {
    if (colorModePreference.value === 'system') {
      resolvedColorMode.value = mediaQuery.matches ? 'dark' : 'light'
    } else {
      resolvedColorMode.value = colorModePreference.value
    }
    
    // Update document class
    document.documentElement.classList.toggle('dark', resolvedColorMode.value === 'dark')
  }

  // Initial update
  updateResolvedMode()

  // Listen for system preference changes
  mediaQuery.addEventListener('change', updateResolvedMode)

  // Watch for preference changes
  watch(colorModePreference, (newVal) => {
    localStorage.setItem('color-mode', newVal)
    updateResolvedMode()
  })
}

export const useColorMode = () => {
  return {
    preference: computed({
      get: () => colorModePreference.value,
      set: (val: ColorMode) => { colorModePreference.value = val }
    }),
    value: computed(() => resolvedColorMode.value),
    
    // Setter for v-model compatibility
    set(mode: ColorMode) {
      colorModePreference.value = mode
    },
  }
}
