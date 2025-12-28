<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { AvatarFallback, AvatarImage, AvatarRoot } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
  src?: string
  alt?: string
  fallback?: string
  size?: 'sm' | 'md' | 'lg'
}>()

const sizeClasses = {
  sm: 'h-8 w-8 text-xs',
  md: 'h-10 w-10 text-sm',
  lg: 'h-12 w-12 text-base',
}
</script>

<template>
  <AvatarRoot
    :class="
      cn(
        'relative flex shrink-0 overflow-hidden rounded-full',
        sizeClasses[size || 'md'],
        props.class
      )
    "
  >
    <AvatarImage
      v-if="src"
      :src="src"
      :alt="alt"
      class="aspect-square h-full w-full"
    />
    <AvatarFallback
      class="flex h-full w-full items-center justify-center rounded-full bg-muted font-medium"
    >
      {{ fallback || alt?.charAt(0)?.toUpperCase() || '?' }}
    </AvatarFallback>
  </AvatarRoot>
</template>
