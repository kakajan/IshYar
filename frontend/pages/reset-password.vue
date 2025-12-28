<script setup lang="ts">
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
  CardFooter,
} from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Label } from '~/components/ui/label'
import { Loader2, AlertTriangle } from 'lucide-vue-next'

definePageMeta({
  layout: 'auth',
})

const route = useRoute()
const router = useRouter()
const { add: addToast } = useToast()
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
    addToast({
      title: t('common.error'),
      description: t('auth_pages.passwords_not_match'),
      variant: 'destructive',
    })
    return
  }

  if (form.password.length < 8) {
    addToast({
      title: t('common.error'),
      description: t('profile.password_min_length'),
      variant: 'destructive',
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

    addToast({
      title: t('common.success'),
      description: t('auth_pages.password_reset_success'),
      variant: 'default',
    })

    router.push('/login')
  } catch (error: any) {
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('messages.update_error'),
      variant: 'destructive',
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
        <h1 class="text-3xl font-bold text-foreground">IshYar</h1>
        <p class="mt-2 text-muted-foreground">
          {{ $t('auth.enterprise_worksuite') }}
        </p>
      </div>

      <!-- Invalid Token Warning -->
      <Card v-if="!token" class="text-center">
        <CardContent class="py-6">
          <div
            class="w-16 h-16 bg-destructive/10 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <AlertTriangle class="w-8 h-8 text-destructive" />
          </div>
          <h2 class="text-xl font-semibold mb-2">
            {{ t('auth_pages.invalid_reset_link') }}
          </h2>
          <p class="text-muted-foreground mb-6">
            {{ t('auth_pages.invalid_reset_link_desc') }}
          </p>
          <NuxtLink to="/forgot-password">
            <Button class="w-full">
              {{ t('auth_pages.request_new_link') }}
            </Button>
          </NuxtLink>
        </CardContent>
      </Card>

      <!-- Reset Form -->
      <Card v-else>
        <CardHeader>
          <CardTitle class="text-center">
            {{ t('auth_pages.reset_password_title') }}
          </CardTitle>
        </CardHeader>
        <CardContent>
          <form class="space-y-6" @submit.prevent="handleSubmit">
            <div class="space-y-2">
              <Label for="email">{{ t('auth.email') }}</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="you@company.com"
                required
                :readonly="!!route.query.email"
              />
            </div>

            <div class="space-y-2">
              <Label for="password">{{ t('profile.new_password') }}</Label>
              <Input
                id="password"
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                required
                autofocus
              />
            </div>

            <div class="space-y-2">
              <Label for="password-confirmation">{{
                t('profile.confirm_password')
              }}</Label>
              <Input
                id="password-confirmation"
                v-model="form.password_confirmation"
                type="password"
                placeholder="••••••••"
                required
              />
            </div>

            <Button type="submit" class="w-full" :disabled="isLoading">
              <Loader2 v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
              {{ t('auth_pages.reset_password_title') }}
            </Button>
          </form>
        </CardContent>
        <CardFooter class="justify-center">
          <p class="text-center text-sm text-muted-foreground">
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
