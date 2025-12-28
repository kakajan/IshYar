<script setup lang="ts">
definePageMeta({
  layout: 'auth',
})

const authStore = useAuthStore()
const toast = useToast()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const isLoading = ref(false)

const handleSubmit = async () => {
  isLoading.value = true

  const result = await authStore.register(
    form.name,
    form.email,
    form.password,
    form.password_confirmation
  )

  if (result.success) {
    toast.add({
      title: 'Welcome!',
      description: 'Your account has been created.',
      color: 'success',
    })
    navigateTo('/dashboard')
  } else {
    toast.add({
      title: 'Registration failed',
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

      <!-- Register Card -->
      <UCard class="glass">
        <template #header>
          <h2 class="text-xl font-semibold text-center">Create your account</h2>
        </template>

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <UFormField label="Name" name="name">
            <UInput
              v-model="form.name"
              placeholder="Your name"
              required
              autofocus
              size="lg"
            />
          </UFormField>

          <UFormField label="Email" name="email">
            <UInput
              v-model="form.email"
              type="email"
              placeholder="you@company.com"
              required
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
            Create account
          </UButton>
        </form>

        <template #footer>
          <p class="text-center text-sm text-gray-600 dark:text-gray-400">
            Already have an account?
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
