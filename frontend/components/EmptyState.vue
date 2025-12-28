<script setup lang="ts">
interface Props {
  /** Empty state title */
  title?: string
  /** Empty state description */
  description?: string
  /** Icon name */
  icon?: string
  /** Action button text */
  actionText?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'No data found',
  description: 'There are no items to display at the moment.',
  icon: 'i-heroicons-inbox',
  actionText: '',
})

const emit = defineEmits<{
  action: []
}>()
</script>

<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
      <UIcon :name="icon" class="w-8 h-8 text-gray-400" />
    </div>
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
      {{ title }}
    </h3>
    <p class="text-gray-500 dark:text-gray-400 max-w-sm mb-6">
      {{ description }}
    </p>
    <slot name="action">
      <UButton v-if="actionText" @click="emit('action')">
        {{ actionText }}
      </UButton>
    </slot>
  </div>
</template>
