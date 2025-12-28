<script setup lang="ts">
// Redirect to dashboard
onMounted(() => {
  navigateTo('/dashboard')
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <div
        class="animate-spin w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full mx-auto"
      />
      <p class="mt-4 text-gray-600">Loading...</p>
    </div>
  </div>
</template>
