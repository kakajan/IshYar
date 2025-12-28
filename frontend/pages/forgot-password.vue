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
      title: t('common.error'),
      description: error.data?.message || t('messages.load_error'),
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
          <div
            class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <UIcon
              name="i-heroicons-envelope-open"
              class="w-8 h-8 text-green-600"
            />
          </div>
          <h2 class="text-xl font-semibold mb-2">
            {{ t('auth_pages.check_email') }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            {{ t('auth_pages.reset_link_sent') }}<br />
            <strong>{{ form.email }}</strong>
          </p>
          <NuxtLink to="/login">
            <UButton variant="outline" block>
              {{ t('auth_pages.back_to_login') }}
            </UButton>
          </NuxtLink>
        </div>
      </UCard>

      <!-- Form -->
      <UCard v-else class="glass">
        <template #header>
          <h2 class="text-xl font-semibold text-center">
            {{ t('auth_pages.forgot_password_title') }}
          </h2>
        </template>

        <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
          {{ t('auth_pages.forgot_password_desc') }}
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
            {{ t('auth_pages.send_reset_link') }}
          </UButton>
        </form>

        <template #footer>
          <p class="text-center text-sm text-gray-600 dark:text-gray-400">
            {{ t('auth_pages.remember_password') }}
            <NuxtLink
              to="/login"
              class="text-primary-600 hover:text-primary-500 font-medium"
            >
              {{ t('auth.sign_in') }}
            </NuxtLink>
          </p>
        </template>
      </UCard>
    </div>
  </div>
</template>
