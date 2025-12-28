<script setup lang="ts">
definePageMeta({
  layout: 'default',
})

const authStore = useAuthStore()
const toast = useToast()
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

const saveProfile = async () => {
  isSavingProfile.value = true
  try {
    const { $api } = useNuxtApp()
    await $api('/auth/profile', {
      method: 'PUT',
      body: profileForm,
    })
    await authStore.fetchUser()
    toast.add({
      title: t('common.success'),
      description: t('profile.update_success'),
      color: 'success',
    })
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || 'Failed to update profile',
      color: 'error',
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
    toast.add({
      title: t('common.success'),
      description: 'Password changed successfully',
      color: 'success',
    })
    // Reset password form
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || 'Failed to change password',
      color: 'error',
    })
  } finally {
    isSavingPassword.value = false
  }
}
</script>

<template>
  <div class="space-y-6 max-w-3xl animate-fade-in">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ t('profile.title') }}
      </h1>
      <p class="mt-1 text-gray-600 dark:text-gray-400">
        Manage your account settings and preferences
      </p>
    </div>

    <!-- Profile Information -->
    <UCard>
      <template #header>
        <h2 class="text-lg font-semibold">{{ t('profile.personal_info') }}</h2>
      </template>

      <form class="space-y-4" @submit.prevent="saveProfile">
        <div class="flex items-center gap-6 mb-6">
          <UAvatar :alt="authStore.user?.name" size="xl" />
          <div>
            <h3 class="font-medium">{{ authStore.user?.name }}</h3>
            <p class="text-sm text-gray-500">{{ authStore.user?.email }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField :label="t('users.name')" name="name">
            <UInput v-model="profileForm.name" />
          </UFormField>

          <UFormField :label="t('users.email')" name="email">
            <UInput v-model="profileForm.email" type="email" disabled />
          </UFormField>

          <UFormField :label="t('users.phone')" name="phone">
            <UInput v-model="profileForm.phone" type="tel" />
          </UFormField>

          <UFormField :label="t('profile.timezone')" name="timezone">
            <USelectMenu
              v-model="profileForm.timezone"
              :options="timezoneOptions"
            />
          </UFormField>

          <UFormField :label="t('profile.language')" name="locale">
            <USelectMenu
              v-model="profileForm.locale"
              :options="localeOptions"
            />
          </UFormField>
        </div>

        <div class="flex justify-end pt-4">
          <UButton type="submit" :loading="isSavingProfile">
            {{ t('common.save') }}
          </UButton>
        </div>
      </form>
    </UCard>

    <!-- Change Password -->
    <UCard>
      <template #header>
        <h2 class="text-lg font-semibold">
          {{ t('profile.change_password') }}
        </h2>
      </template>

      <form class="space-y-4" @submit.prevent="changePassword">
        <UFormField
          :label="t('profile.current_password')"
          name="current_password"
        >
          <UInput v-model="passwordForm.current_password" type="password" />
        </UFormField>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField :label="t('profile.new_password')" name="password">
            <UInput v-model="passwordForm.password" type="password" />
          </UFormField>

          <UFormField
            :label="t('profile.confirm_password')"
            name="password_confirmation"
          >
            <UInput
              v-model="passwordForm.password_confirmation"
              type="password"
            />
          </UFormField>
        </div>

        <div class="flex justify-end pt-4">
          <UButton type="submit" :loading="isSavingPassword" color="gray">
            {{ t('profile.change_password') }}
          </UButton>
        </div>
      </form>
    </UCard>

    <!-- Account Info -->
    <UCard>
      <template #header>
        <h2 class="text-lg font-semibold">Account Information</h2>
      </template>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
          <span class="text-gray-500">Department</span>
          <p class="font-medium">
            {{ authStore.user?.department?.name || '—' }}
          </p>
        </div>
        <div>
          <span class="text-gray-500">Position</span>
          <p class="font-medium">{{ authStore.user?.position?.name || '—' }}</p>
        </div>
        <div>
          <span class="text-gray-500">Roles</span>
          <div class="flex gap-1 mt-1">
            <UBadge
              v-for="role in authStore.user?.roles"
              :key="role.name"
              size="xs"
            >
              {{ role.name }}
            </UBadge>
            <span v-if="!authStore.user?.roles?.length" class="text-gray-400"
              >—</span
            >
          </div>
        </div>
        <div>
          <span class="text-gray-500">Member since</span>
          <p class="font-medium">{{ authStore.user?.created_at || '—' }}</p>
        </div>
      </div>
    </UCard>
  </div>
</template>
