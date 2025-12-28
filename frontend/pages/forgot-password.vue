<script setup lang="ts">
definePageMeta({
  layout: 'auth',
})

const toast = useToast()
const { t } = useI18n()

const form = reactive({
  email: '',
})

const isLoading = ref(false)
const isSubmitted = ref(false)

const handleSubmit = async () => {
  isLoading.value = true

  try {
    const { $api } = useNuxtApp()
    await $api('/auth/forgot-password', {
      method: 'POST',
      body: { email: form.email },
    })
    isSubmitted.value = true
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to send reset link',
      color: 'error',
    })
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4"
  >
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">IshYar</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Enterprise WorkSuite
        </p>
      </div>

      <!-- Success State -->
      <UCard v-if="isSubmitted" class="glass text-center">
        <div class="py-6">
          <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <UIcon name="i-heroicons-envelope-open" class="w-8 h-8 text-green-600" />
          </div>
          <h2 class="text-xl font-semibold mb-2">Check your email</h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            We've sent a password reset link to<br />
            <strong>{{ form.email }}</strong>
          </p>
          <NuxtLink to="/login">
            <UButton variant="outline" block>
              Back to login
            </UButton>
          </NuxtLink>
        </div>
      </UCard>

      <!-- Form -->
      <UCard v-else class="glass">
        <template #header>
          <h2 class="text-xl font-semibold text-center">
            {{ t('auth.forgot_password') }}
          </h2>
        </template>

        <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
          Enter your email address and we'll send you a link to reset your password.
        </p>

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <UFormField :label="t('auth.email')" name="email">
            <UInput
              v-model="form.email"
              type="email"
              placeholder="you@company.com"
              required
              autofocus
              size="lg"
            />
          </UFormField>

          <UButton type="submit" block size="lg" :loading="isLoading">
            Send reset link
          </UButton>
        </form>

        <template #footer>
          <p class="text-center text-sm text-gray-600 dark:text-gray-400">
            Remember your password?
            <NuxtLink
              to="/login"
              class="text-primary-600 hover:text-primary-500 font-medium"
            >
              Sign in
            </NuxtLink>
          </p>
        </template>
      </UCard>
    </div>
  </div>
</template>
