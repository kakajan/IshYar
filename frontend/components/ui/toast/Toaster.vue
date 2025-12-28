<script setup lang="ts">
import { X } from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import type { Toast } from '~/composables/useToast'

const props = defineProps<{
  toasts: Toast[]
}>()

const emit = defineEmits<{
  (e: 'dismiss', id: string): void
}>()

const variantClasses: Record<string, string> = {
  default: 'border bg-background text-foreground',
  success:
    'border-green-500/50 bg-green-50 text-green-900 dark:bg-green-950 dark:text-green-100',
  error:
    'border-red-500/50 bg-red-50 text-red-900 dark:bg-red-950 dark:text-red-100',
  destructive:
    'border-red-500/50 bg-red-50 text-red-900 dark:bg-red-950 dark:text-red-100',
  warning:
    'border-yellow-500/50 bg-yellow-50 text-yellow-900 dark:bg-yellow-950 dark:text-yellow-100',
}
</script>

<template>
  <Teleport to="body">
    <div
      class="fixed top-0 right-0 z-[100] flex max-h-screen w-full flex-col-reverse gap-2 p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]"
    >
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="
            cn(
              'group pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-md border p-6 pr-8 shadow-lg transition-all',
              variantClasses[toast.variant || 'default']
            )
          "
        >
          <div class="grid gap-1">
            <div v-if="toast.title" class="text-sm font-semibold">
              {{ toast.title }}
            </div>
            <div v-if="toast.description" class="text-sm opacity-90">
              {{ toast.description }}
            </div>
          </div>
          <button
            class="absolute right-2 top-2 rounded-md p-1 text-foreground/50 opacity-0 transition-opacity hover:text-foreground focus:opacity-100 focus:outline-none focus:ring-2 group-hover:opacity-100"
            @click="emit('dismiss', toast.id)"
          >
            <X class="h-4 w-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
