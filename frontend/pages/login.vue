<script setup lang="ts">
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Label } from '~/components/ui/label'
import { Checkbox } from '~/components/ui/checkbox'
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
  email: '',
  password: '',
  remember: false,
})

const isLoading = ref(false)

const handleSubmit = async () => {
  isLoading.value = true

  const result = await authStore.login(form.email, form.password)

  if (result.success) {
    toast.add({
      title: t('auth.welcome_back'),
      description: t('auth.login_success'),
      color: 'success',
    })
    navigateTo('/dashboard')
  } else {
    toast.add({
      title: t('auth.login_failed'),
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

      <!-- Login Card -->
      <Card>
        <CardHeader>
          <CardTitle class="text-xl text-center">
            {{ $t('auth.sign_in_title') }}
          </CardTitle>
        </CardHeader>

        <CardContent>
          <form class="space-y-6" @submit.prevent="handleSubmit">
            <div class="space-y-2">
              <Label for="email">{{ $t('auth.email') }}</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                :placeholder="$t('auth.email_placeholder')"
                required
                autofocus
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

            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <Checkbox id="remember" v-model:checked="form.remember" />
                <Label for="remember" class="text-sm font-normal">
                  {{ $t('auth.remember_me') }}
                </Label>
              </div>
              <NuxtLink
                to="/forgot-password"
                class="text-sm text-primary hover:underline"
              >
                {{ $t('auth.forgot_password') }}
              </NuxtLink>
            </div>

            <Button type="submit" class="w-full" :loading="isLoading">
              {{ $t('auth.sign_in') }}
            </Button>
          </form>
        </CardContent>

        <CardFooter class="justify-center">
          <p class="text-sm text-muted-foreground">
            {{ $t('auth_pages.dont_have_account') }}
            <NuxtLink
              to="/register"
              class="text-primary hover:underline font-medium"
            >
              {{ $t('auth.sign_up') }}
            </NuxtLink>
          </p>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
