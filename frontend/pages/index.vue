<script setup lang="ts">
import { Loader2 } from 'lucide-vue-next'

definePageMeta({
  layout: false,
})

// Redirect to dashboard
onMounted(() => {
  navigateTo('/dashboard')
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-background">
    <div class="text-center">
      <Loader2 class="w-8 h-8 animate-spin text-primary mx-auto" />
      <p class="mt-4 text-muted-foreground">{{ $t('common.loading') }}</p>
    </div>
  </div>
</template>
