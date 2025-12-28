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

definePageMeta({
  layout: 'auth',
})

const { t } = useI18n()
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
      title: t('auth.welcome'),
      description: t('auth.account_created'),
      color: 'success',
    })
    navigateTo('/dashboard')
  } else {
    toast.add({
      title: t('auth.registration_failed'),
      description: result.error,
      color: 'error',
    })
  }

  isLoading.value = false
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-background px-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold">IshYar</h1>
        <p class="mt-2 text-muted-foreground">
          {{ $t('auth.enterprise_worksuite') }}
        </p>
      </div>

      <!-- Register Card -->
      <Card>
        <CardHeader>
          <CardTitle class="text-xl text-center">
            {{ $t('auth_pages.create_account') }}
          </CardTitle>
        </CardHeader>

        <CardContent>
          <form class="space-y-6" @submit.prevent="handleSubmit">
            <div class="space-y-2">
              <Label for="name">{{ $t('auth.name') }}</Label>
              <Input
                id="name"
                v-model="form.name"
                :placeholder="$t('auth.name_placeholder')"
                required
                autofocus
              />
            </div>

            <div class="space-y-2">
              <Label for="email">{{ $t('auth.email') }}</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                :placeholder="$t('auth.email_placeholder')"
                required
              />
            </div>

            <div class="space-y-2">
              <Label for="password">{{ $t('auth.password') }}</Label>
              <Input
                id="password"
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                required
              />
            </div>

            <div class="space-y-2">
              <Label for="password_confirmation">{{
                $t('auth.confirm_password')
              }}</Label>
              <Input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                placeholder="••••••••"
                required
              />
            </div>

            <Button type="submit" class="w-full" :loading="isLoading">
              {{ $t('auth_pages.create_account') }}
            </Button>
          </form>
        </CardContent>

        <CardFooter class="justify-center">
          <p class="text-sm text-muted-foreground">
            {{ $t('auth_pages.already_have_account') }}
            <NuxtLink
              to="/login"
              class="text-primary hover:underline font-medium"
            >
              {{ $t('auth.sign_in') }}
            </NuxtLink>
          </p>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
