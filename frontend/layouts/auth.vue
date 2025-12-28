<script setup lang="ts">
import { Toaster } from '~/components/ui/toast'

const { toasts, dismiss } = useToast()
</script>

<template>
  <div class="min-h-screen bg-background">
    <slot />
    <Toaster :toasts="toasts" @dismiss="dismiss" />
  </div>
</template>
