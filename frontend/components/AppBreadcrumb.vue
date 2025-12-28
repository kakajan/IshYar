<script setup lang="ts">
const route = useRoute()
const { t } = useI18n()

interface Crumb {
  label: string
  to?: string
}

const breadcrumbs = computed<Crumb[]>(() => {
  const pathParts = route.path.split('/').filter(Boolean)
  const crumbs: Crumb[] = []

  // Build breadcrumb trail
  let currentPath = ''
  for (const part of pathParts) {
    currentPath += `/${part}`

    // Skip dynamic segments in display but show actual value
    if (part.startsWith('[') && part.endsWith(']')) {
      const paramName = part.slice(1, -1)
      const value = route.params[paramName] as string
      crumbs.push({
        label: value || part,
        to: currentPath.replace(`[${paramName}]`, value),
      })
    } else {
      // Try to get translation, fallback to capitalized part
      const translationKey = `nav.${part}`
      const translated = t(translationKey)
      const label =
        translated !== translationKey ? translated : capitalize(part)

      crumbs.push({
        label,
        to: currentPath,
      })
    }
  }

  // Last item shouldn't be a link
  if (crumbs.length > 0) {
    crumbs[crumbs.length - 1].to = undefined
  }

  return crumbs
})

const capitalize = (str: string) => {
  return str.replace(/-/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase())
}
</script>

<template>
  <nav
    v-if="breadcrumbs.length > 0"
    aria-label="Breadcrumb"
    class="flex items-center text-sm"
  >
    <ol class="flex items-center gap-1">
      <!-- Home icon -->
      <li>
        <NuxtLink
          to="/dashboard"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
        >
          <UIcon name="i-heroicons-home" class="w-4 h-4" />
        </NuxtLink>
      </li>

      <template v-for="(crumb, index) in breadcrumbs" :key="index">
        <li class="flex items-center gap-1">
          <UIcon
            name="i-heroicons-chevron-right"
            class="w-4 h-4 text-gray-400"
          />
          <NuxtLink
            v-if="crumb.to"
            :to="crumb.to"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            {{ crumb.label }}
          </NuxtLink>
          <span v-else class="text-gray-900 dark:text-white font-medium">
            {{ crumb.label }}
          </span>
        </li>
      </template>
    </ol>
  </nav>
</template>
