<script setup lang="ts">
import { SelectValue, type SelectValueProps, useForwardProps } from 'reka-ui'

const props = defineProps<SelectValueProps>()
const forwardedProps = useForwardProps(props)
</script>

<template>
  <SelectValue v-bind="forwardedProps">
    <slot />
  </SelectValue>
</template>
