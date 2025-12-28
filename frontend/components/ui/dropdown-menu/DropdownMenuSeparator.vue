<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import {
  DropdownMenuSeparator,
  type DropdownMenuSeparatorProps,
  useForwardProps,
} from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<
  DropdownMenuSeparatorProps & { class?: HTMLAttributes['class'] }
>()

const forwardedProps = useForwardProps(props)
</script>

<template>
  <DropdownMenuSeparator
    v-bind="forwardedProps"
    :class="cn('-mx-1 my-1 h-px bg-muted', props.class)"
  />
</template>
