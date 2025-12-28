/**
 * Plugin to handle RTL/LTR direction based on current locale
 * Uses env variable as fallback, then checks user settings from API
 */
import type { Ref } from 'vue'

interface LocaleConfig {
  dir: 'ltr' | 'rtl'
  name: string
}

export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig()
  const defaultLocale = (config.public.defaultLocale as string) || 'fa'

  // Define locale configurations
  const localeConfigs: Record<string, LocaleConfig> = {
    en: { dir: 'ltr', name: 'English' },
    fa: { dir: 'rtl', name: 'فارسی' },
  }

  const updateDirection = (localeCode: string) => {
    const localeConfig = localeConfigs[localeCode] ?? localeConfigs['fa']
    if (localeConfig) {
      document.documentElement.setAttribute('dir', localeConfig.dir)
      document.documentElement.setAttribute('lang', localeCode)
    }
  }

  // Set initial direction based on default locale
  updateDirection(defaultLocale)

  // Hook into i18n locale changes after the app is mounted
  nuxtApp.hook('app:mounted', () => {
    const i18n = nuxtApp.$i18n as { locale: Ref<string> } | undefined
    if (i18n?.locale) {
      // Update direction for current locale
      updateDirection(i18n.locale.value)

      // Watch for locale changes
      watch(
        () => i18n.locale.value,
        (newLocale) => {
          updateDirection(newLocale)
        }
      )
    }
  })

  // Provide helper to update direction programmatically
  return {
    provide: {
      updateDirection,
    },
  }
})
