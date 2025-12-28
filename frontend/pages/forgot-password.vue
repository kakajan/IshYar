<script setup lang="ts">
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Label } from '~/components/ui/label'
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
  CardFooter,
} from '~/components/ui/card'
import { MailOpen } from 'lucide-vue-next'

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
  <div class="min-h-screen flex items-center justify-center bg-background px-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold">IshYar</h1>
        <p class="mt-2 text-muted-foreground">Enterprise WorkSuite</p>
      </div>

      <!-- Success State -->
      <Card v-if="isSubmitted" class="text-center">
        <CardContent class="py-8">
          <div
            class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <MailOpen class="w-8 h-8 text-green-600" />
          </div>
          <h2 class="text-xl font-semibold mb-2">
            {{ t('auth_pages.check_email') }}
          </h2>
          <p class="text-muted-foreground mb-6">
            {{ t('auth_pages.reset_link_sent') }}<br />
            <strong>{{ form.email }}</strong>
          </p>
          <NuxtLink to="/login">
            <Button variant="outline" class="w-full">
              {{ t('auth_pages.back_to_login') }}
            </Button>
          </NuxtLink>
        </CardContent>
      </Card>

      <!-- Form -->
      <Card v-else>
        <CardHeader>
          <CardTitle class="text-xl text-center">
            {{ t('auth_pages.forgot_password_title') }}
          </CardTitle>
        </CardHeader>

        <CardContent>
          <p class="text-sm text-muted-foreground text-center mb-6">
            {{ t('auth_pages.forgot_password_desc') }}
          </p>

          <form class="space-y-6" @submit.prevent="handleSubmit">
            <div class="space-y-2">
              <Label for="email">{{ t('auth.email') }}</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="you@company.com"
                required
                autofocus
              />
            </div>

            <Button type="submit" class="w-full" :loading="isLoading">
              {{ t('auth_pages.send_reset_link') }}
            </Button>
          </form>
        </CardContent>

        <CardFooter class="justify-center">
          <p class="text-sm text-muted-foreground">
            {{ t('auth_pages.remember_password') }}
            <NuxtLink
              to="/login"
              class="text-primary hover:underline font-medium"
            >
              {{ t('auth.sign_in') }}
            </NuxtLink>
          </p>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
