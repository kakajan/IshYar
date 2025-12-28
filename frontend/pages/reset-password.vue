<script setup lang="ts">
definePageMeta({
  layout: 'auth',
})

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { t } = useI18n()

const token = computed(() => (route.query.token as string) || '')

const form = reactive({
  email: (route.query.email as string) || '',
  password: '',
  password_confirmation: '',
})

const isLoading = ref(false)

const handleSubmit = async () => {
  if (form.password !== form.password_confirmation) {
    toast.add({
      title: 'Error',
      description: 'Passwords do not match',
      color: 'error',
    })
    return
  }

  if (form.password.length < 8) {
    toast.add({
      title: 'Error',
      description: 'Password must be at least 8 characters',
      color: 'error',
    })
    return
  }

  isLoading.value = true

  try {
    const { $api } = useNuxtApp()
    await $api('/auth/reset-password', {
      method: 'POST',
      body: {
        token: token.value,
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation,
      },
    })

    toast.add({
      title: 'Success',
      description: 'Your password has been reset. Please sign in.',
      color: 'success',
    })

    router.push('/login')
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to reset password',
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

      <!-- Invalid Token Warning -->
      <UCard v-if="!token" class="glass text-center">
        <div class="py-6">
          <div
            class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <UIcon
              name="i-heroicons-exclamation-triangle"
              class="w-8 h-8 text-red-600"
            />
          </div>
          <h2 class="text-xl font-semibold mb-2">Invalid reset link</h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            This password reset link is invalid or has expired.
          </p>
          <NuxtLink to="/forgot-password">
            <UButton color="primary" block> Request new link </UButton>
          </NuxtLink>
        </div>
      </UCard>

      <!-- Reset Form -->
      <UCard v-else class="glass">
        <template #header>
          <h2 class="text-xl font-semibold text-center">Reset your password</h2>
        </template>

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <UFormField :label="t('auth.email')" name="email">
            <UInput
              v-model="form.email"
              type="email"
              placeholder="you@company.com"
              required
              size="lg"
              :readonly="!!route.query.email"
            />
          </UFormField>

          <UFormField label="New Password" name="password">
            <UInput
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              required
              size="lg"
              autofocus
            />
          </UFormField>

          <UFormField label="Confirm Password" name="password_confirmation">
            <UInput
              v-model="form.password_confirmation"
              type="password"
              placeholder="••••••••"
              required
              size="lg"
            />
          </UFormField>

          <UButton type="submit" block size="lg" :loading="isLoading">
            Reset password
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
