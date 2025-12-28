<template>
  <UDropdown :items="localeItems">
    <UButton
      color="neutral"
      variant="ghost"
      :icon="
        currentLocale?.code === 'fa'
          ? 'i-heroicons-language'
          : 'i-heroicons-globe-alt'
      "
    >
      <span class="hidden sm:inline">{{ currentLocale?.name }}</span>
    </UButton>
  </UDropdown>
</template>

<script setup lang="ts">
type LocaleCode = 'en' | 'fa'

const { locale, locales, setLocale } = useI18n()

const currentLocale = computed(() => {
  return locales.value.find(
    (l) => typeof l === 'object' && l.code === locale.value
  )
})

const localeItems = computed(() => [
  locales.value
    .filter(
      (l): l is { code: LocaleCode; name: string; dir?: 'ltr' | 'rtl' } =>
        typeof l === 'object' && 'code' in l
    )
    .map((l) => ({
      label: l.name,
      icon: l.code === 'fa' ? 'i-heroicons-language' : 'i-heroicons-globe-alt',
      click: () => switchLocale(l.code),
      active: locale.value === l.code,
    })),
])

const switchLocale = (code: LocaleCode) => {
  setLocale(code)

  // Update document direction for RTL support
  const localeConfig = locales.value.find(
    (l) => typeof l === 'object' && l.code === code
  )
  if (
    localeConfig &&
    typeof localeConfig === 'object' &&
    'dir' in localeConfig
  ) {
    document.documentElement.dir = (localeConfig.dir as string) || 'ltr'
    document.documentElement.lang = code

    // Add or remove RTL class
    if (localeConfig.dir === 'rtl') {
      document.documentElement.classList.add('rtl')
    } else {
      document.documentElement.classList.remove('rtl')
    }
  }
}

// Set initial direction on mount
onMounted(() => {
  const localeConfig = locales.value.find(
    (l) => typeof l === 'object' && l.code === locale.value
  )
  if (
    localeConfig &&
    typeof localeConfig === 'object' &&
    'dir' in localeConfig
  ) {
    document.documentElement.dir = (localeConfig.dir as string) || 'ltr'
    document.documentElement.lang = locale.value
    if (localeConfig.dir === 'rtl') {
      document.documentElement.classList.add('rtl')
    }
  }
})
</script>
