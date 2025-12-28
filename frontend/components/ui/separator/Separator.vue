<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { Separator as SeparatorPrimitive } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
  orientation?: 'horizontal' | 'vertical'
  decorative?: boolean
}>()
</script>

<template>
  <SeparatorPrimitive
    :orientation="orientation"
    :decorative="decorative"
    :class="
      cn(
        'shrink-0 bg-border',
        orientation === 'vertical' ? 'h-full w-px' : 'h-px w-full',
        props.class
      )
    "
  />
</template>
