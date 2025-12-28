<script setup lang="ts">
interface Props {
  /** Number of rows to display */
  rows?: number
  /** Whether to show avatar placeholder */
  avatar?: boolean
  /** Whether to show as card */
  card?: boolean
  /** Variant style */
  variant?: 'list' | 'card' | 'table' | 'text'
}

const props = withDefaults(defineProps<Props>(), {
  rows: 3,
  avatar: false,
  card: false,
  variant: 'list',
})
</script>

<template>
  <!-- Text variant -->
  <div v-if="variant === 'text'" class="animate-pulse space-y-2">
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4" />
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full" />
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-5/6" />
  </div>

  <!-- Card variant -->
  <UCard v-else-if="variant === 'card'" class="animate-pulse">
    <div class="space-y-4">
      <div class="flex items-center gap-4">
        <div v-if="avatar" class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700" />
        <div class="flex-1 space-y-2">
          <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3" />
          <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2" />
        </div>
      </div>
      <div class="space-y-2">
        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded" />
        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6" />
      </div>
    </div>
  </UCard>

  <!-- Table variant -->
  <div v-else-if="variant === 'table'" class="animate-pulse">
    <!-- Header -->
    <div class="flex gap-4 p-4 border-b border-gray-200 dark:border-gray-700">
      <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4" />
      <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4" />
      <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4" />
      <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4" />
    </div>
    <!-- Rows -->
    <div
      v-for="i in rows"
      :key="i"
      class="flex gap-4 p-4 border-b border-gray-100 dark:border-gray-800"
    >
      <div v-if="avatar" class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700" />
      <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4" />
      <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4" />
      <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4" />
      <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4" />
    </div>
  </div>

  <!-- List variant (default) -->
  <div v-else class="animate-pulse space-y-4">
    <div
      v-for="i in rows"
      :key="i"
      class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
    >
      <div v-if="avatar" class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700" />
      <div class="flex-1 space-y-2">
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3" />
        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3" />
      </div>
      <div class="h-6 w-16 bg-gray-200 dark:bg-gray-700 rounded" />
    </div>
  </div>
</template>
