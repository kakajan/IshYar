<script setup lang="ts">
import { Button } from '~/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from '~/components/ui/dropdown-menu'
import { Globe, Languages } from 'lucide-vue-next'

type LocaleCode = 'en' | 'fa'

const { locale, locales, setLocale } = useI18n()

const currentLocale = computed(() => {
  return locales.value.find(
    (l) => typeof l === 'object' && l.code === locale.value
  )
})

const availableLocales = computed(() => {
  return locales.value.filter(
    (l): l is { code: LocaleCode; name: string; dir?: 'ltr' | 'rtl' } =>
      typeof l === 'object' && 'code' in l
  )
})

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

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost">
        <component
          :is="currentLocale?.code === 'fa' ? Languages : Globe"
          class="w-4 h-4"
        />
        <span class="hidden sm:inline ms-2">{{ currentLocale?.name }}</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuItem
        v-for="l in availableLocales"
        :key="l.code"
        :class="{ 'bg-accent': locale === l.code }"
        @select="switchLocale(l.code)"
      >
        <component
          :is="l.code === 'fa' ? Languages : Globe"
          class="w-4 h-4 mr-2"
        />
        {{ l.name }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
