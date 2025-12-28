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
import { Badge } from '~/components/ui/badge'
import { Avatar, AvatarFallback } from '~/components/ui/avatar'
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from '~/components/ui/select'
import { Loader2, User } from 'lucide-vue-next'

definePageMeta({
  layout: 'default',
})

const authStore = useAuthStore()
const { add: addToast } = useToast()
const { t } = useI18n()

const profileForm = reactive({
  name: '',
  email: '',
  phone: '',
  timezone: 'UTC',
  locale: 'en',
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const isSavingProfile = ref(false)
const isSavingPassword = ref(false)

// Populate form with current user data
onMounted(() => {
  if (authStore.user) {
    profileForm.name = authStore.user.name || ''
    profileForm.email = authStore.user.email || ''
    profileForm.phone = authStore.user.phone || ''
    profileForm.timezone = authStore.user.timezone || 'UTC'
    profileForm.locale = authStore.user.locale || 'en'
  }
})

const timezoneOptions = [
  { value: 'UTC', label: 'UTC' },
  { value: 'Asia/Tehran', label: 'Asia/Tehran (Iran)' },
  { value: 'Asia/Ashgabat', label: 'Asia/Ashgabat (Turkmenistan)' },
  { value: 'Europe/London', label: 'Europe/London' },
  { value: 'America/New_York', label: 'America/New_York' },
  { value: 'America/Los_Angeles', label: 'America/Los_Angeles' },
]

const localeOptions = [
  { value: 'en', label: 'English' },
  { value: 'fa', label: 'فارسی (Persian)' },
]

const getInitials = (name: string | undefined) => {
  if (!name) return 'U'
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const saveProfile = async () => {
  isSavingProfile.value = true
  try {
    const { $api } = useNuxtApp()
    await $api('/auth/profile', {
      method: 'PUT',
      body: profileForm,
    })
    await authStore.fetchUser()
    addToast({
      title: t('common.success'),
      description: t('profile.update_success'),
      variant: 'default',
    })
  } catch (error: any) {
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('messages.failed_profile'),
      variant: 'destructive',
    })
  } finally {
    isSavingProfile.value = false
  }
}

const changePassword = async () => {
  isSavingPassword.value = true
  try {
    const { $api } = useNuxtApp()
    await $api('/auth/password', {
      method: 'PUT',
      body: passwordForm,
    })
    addToast({
      title: t('common.success'),
      description: t('profile.password_success'),
      variant: 'default',
    })
    // Reset password form
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (error: any) {
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('messages.failed_password'),
      variant: 'destructive',
    })
  } finally {
    isSavingPassword.value = false
  }
}
</script>

<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-foreground">
        {{ t('profile.title') }}
      </h1>
      <p class="mt-1 text-muted-foreground">
        {{ t('profile.subtitle') }}
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Profile Information -->
    <Card>
      <CardHeader>
        <CardTitle>{{ t('profile.personal_info') }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form class="space-y-4" @submit.prevent="saveProfile">
          <div class="flex items-center gap-6 mb-6">
            <Avatar class="h-16 w-16">
              <AvatarFallback class="text-lg">{{
                getInitials(authStore.user?.name)
              }}</AvatarFallback>
            </Avatar>
            <div>
              <h3 class="font-medium">{{ authStore.user?.name }}</h3>
              <p class="text-sm text-muted-foreground">
                {{ authStore.user?.email }}
              </p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="profile-name">{{ t('users.name') }}</Label>
              <Input id="profile-name" v-model="profileForm.name" />
            </div>

            <div class="space-y-2">
              <Label for="profile-email">{{ t('users.email') }}</Label>
              <Input
                id="profile-email"
                v-model="profileForm.email"
                type="email"
                disabled
              />
            </div>

            <div class="space-y-2">
              <Label for="profile-phone">{{ t('users.phone') }}</Label>
              <Input
                id="profile-phone"
                v-model="profileForm.phone"
                type="tel"
              />
            </div>

            <div class="space-y-2">
              <Label>{{ t('profile.timezone') }}</Label>
              <Select v-model="profileForm.timezone">
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="tz in timezoneOptions"
                    :key="tz.value"
                    :value="tz.value"
                  >
                    {{ tz.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="space-y-2">
              <Label>{{ t('profile.language') }}</Label>
              <Select v-model="profileForm.locale">
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="loc in localeOptions"
                    :key="loc.value"
                    :value="loc.value"
                  >
                    {{ loc.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div class="flex justify-end pt-4">
            <Button type="submit" :disabled="isSavingProfile">
              <Loader2
                v-if="isSavingProfile"
                class="w-4 h-4 mr-2 animate-spin"
              />
              {{ t('common.save') }}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Change Password -->
    <Card>
      <CardHeader>
        <CardTitle>{{ t('profile.change_password') }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form class="space-y-4" @submit.prevent="changePassword">
          <div class="space-y-2">
            <Label for="current-password">{{
              t('profile.current_password')
            }}</Label>
            <Input
              id="current-password"
              v-model="passwordForm.current_password"
              type="password"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="new-password">{{ t('profile.new_password') }}</Label>
              <Input
                id="new-password"
                v-model="passwordForm.password"
                type="password"
              />
            </div>

            <div class="space-y-2">
              <Label for="confirm-password">{{
                t('profile.confirm_password')
              }}</Label>
              <Input
                id="confirm-password"
                v-model="passwordForm.password_confirmation"
                type="password"
              />
            </div>
          </div>

          <div class="flex justify-end pt-4">
            <Button
              type="submit"
              variant="secondary"
              :disabled="isSavingPassword"
            >
              <Loader2
                v-if="isSavingPassword"
                class="w-4 h-4 mr-2 animate-spin"
              />
              {{ t('profile.change_password') }}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <!-- Account Info -->
    <Card>
      <CardHeader>
        <CardTitle>{{ t('profile.account_info') }}</CardTitle>
      </CardHeader>
      <CardContent>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-muted-foreground">{{
              t('users.department')
            }}</span>
            <p class="font-medium">
              {{ authStore.user?.department?.name || '—' }}
            </p>
          </div>
          <div>
            <span class="text-muted-foreground">{{ t('users.position') }}</span>
            <p class="font-medium">
              {{ authStore.user?.position?.name || '—' }}
            </p>
          </div>
          <div>
            <span class="text-muted-foreground">{{ t('users.roles') }}</span>
            <div class="flex gap-1 mt-1 flex-wrap">
              <Badge
                v-for="role in authStore.user?.roles"
                :key="role.name"
                variant="secondary"
              >
                {{ role.name }}
              </Badge>
              <span
                v-if="!authStore.user?.roles?.length"
                class="text-muted-foreground"
                >—</span
              >
            </div>
          </div>
          <div>
            <span class="text-muted-foreground">{{
              t('profile.member_since')
            }}</span>
            <p class="font-medium">{{ authStore.user?.created_at || '—' }}</p>
          </div>
        </div>
      </CardContent>
    </Card>
    </div>
  </div>
</template>
