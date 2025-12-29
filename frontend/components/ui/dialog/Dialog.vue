<script setup lang="ts">
import {
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle,
  DialogTrigger,
} from 'reka-ui'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { X } from 'lucide-vue-next'

const props = defineProps<{
  open?: boolean
  class?: HTMLAttributes['class']
  title?: string
  description?: string
}>()

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void
}>()
</script>

<template>
  <DialogRoot :open="open" @update:open="emit('update:open', $event)">
    <slot name="trigger">
      <DialogTrigger as-child>
        <slot name="trigger-button" />
      </DialogTrigger>
    </slot>
    <DialogPortal>
      <DialogOverlay
        class="fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"
      />
      <DialogContent
        :class="
          cn(
            'fixed left-1/2 top-1/2 z-50 grid w-full max-w-lg -translate-x-1/2 -translate-y-1/2 gap-4 border bg-background p-6 shadow-lg duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] sm:rounded-lg',
            props.class
          )
        "
      >
        <div
          v-if="title || description"
          class="flex flex-col space-y-1.5 text-center sm:text-left"
        >
          <DialogTitle
            v-if="title"
            class="text-lg font-semibold leading-none tracking-tight"
          >
            {{ title }}
          </DialogTitle>
          <DialogDescription
            v-if="description"
            class="text-sm text-muted-foreground"
          >
            {{ description }}
          </DialogDescription>
        </div>
        <slot />
        <DialogClose
          class="absolute right-4 rtl:right-auto rtl:left-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-accent data-[state=open]:text-muted-foreground"
        >
          <X class="h-4 w-4" />
          <span class="sr-only">Close</span>
        </DialogClose>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>
