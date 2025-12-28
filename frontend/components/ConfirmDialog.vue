<script setup lang="ts">
const { isOpen, options, handleConfirm, handleCancel } = useConfirm()
</script>

<template>
  <UModal v-model:open="isOpen" @close="handleCancel">
    <template #content>
      <UCard>
        <div class="flex gap-4">
          <!-- Icon -->
          <div
            :class="[
              'flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center',
              options.confirmColor === 'error' &&
                'bg-red-100 dark:bg-red-900/30',
              options.confirmColor === 'warning' &&
                'bg-yellow-100 dark:bg-yellow-900/30',
              options.confirmColor === 'success' &&
                'bg-green-100 dark:bg-green-900/30',
              options.confirmColor === 'primary' &&
                'bg-primary-100 dark:bg-primary-900/30',
            ]"
          >
            <UIcon
              :name="options.icon || 'i-heroicons-question-mark-circle'"
              :class="[
                'w-6 h-6',
                options.confirmColor === 'error' && 'text-red-600',
                options.confirmColor === 'warning' && 'text-yellow-600',
                options.confirmColor === 'success' && 'text-green-600',
                options.confirmColor === 'primary' && 'text-primary-600',
              ]"
            />
          </div>

          <!-- Content -->
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ options.title }}
            </h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
              {{ options.message }}
            </p>
          </div>
        </div>

        <template #footer>
          <div class="flex justify-end gap-3">
            <UButton variant="outline" @click="handleCancel">
              {{ options.cancelText }}
            </UButton>
            <UButton :color="options.confirmColor" @click="handleConfirm">
              {{ options.confirmText }}
            </UButton>
          </div>
        </template>
      </UCard>
    </template>
  </UModal>
</template>
