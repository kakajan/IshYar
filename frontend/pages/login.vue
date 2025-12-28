<script setup lang="ts">
definePageMeta({
  layout: 'auth',
})

const authStore = useAuthStore()
const toast = useToast()

const form = reactive({
  email: '',
  password: '',
})

const isLoading = ref(false)

const handleSubmit = async () => {
  isLoading.value = true

  const result = await authStore.login(form.email, form.password)

  if (result.success) {
    toast.add({
      title: 'Welcome back!',
      description: 'You have successfully logged in.',
      color: 'success',
    })
    navigateTo('/dashboard')
  } else {
    toast.add({
      title: 'Login failed',
      description: result.error,
      color: 'error',
    })
  }

  isLoading.value = false
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

      <!-- Login Card -->
      <UCard class="glass">
        <template #header>
          <h2 class="text-xl font-semibold text-center">
            Sign in to your account
          </h2>
        </template>

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <UFormField label="Email" name="email">
            <UInput
              v-model="form.email"
              type="email"
              placeholder="you@company.com"
              required
              autofocus
              size="lg"
            />
          </UFormField>

          <UFormField label="Password" name="password">
            <UInput
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              required
              size="lg"
            />
          </UFormField>

          <div class="flex items-center justify-between">
            <UCheckbox label="Remember me" />
            <NuxtLink
              to="/forgot-password"
              class="text-sm text-primary-600 hover:text-primary-500"
            >
              Forgot password?
            </NuxtLink>
          </div>

          <UButton type="submit" block size="lg" :loading="isLoading">
            Sign in
          </UButton>
        </form>

        <template #footer>
          <p class="text-center text-sm text-gray-600 dark:text-gray-400">
            Don't have an account?
            <NuxtLink
              to="/register"
              class="text-primary-600 hover:text-primary-500 font-medium"
            >
              Sign up
            </NuxtLink>
          </p>
        </template>
      </UCard>
    </div>
  </div>
</template>
