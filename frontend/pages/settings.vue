<script setup lang="ts">
definePageMeta({
  layout: 'default',
})

const { t } = useI18n()
const colorMode = useColorMode()

const appearanceOptions = [
  { value: 'system', label: 'System', icon: 'i-heroicons-computer-desktop' },
  { value: 'light', label: 'Light', icon: 'i-heroicons-sun' },
  { value: 'dark', label: 'Dark', icon: 'i-heroicons-moon' },
]

const notificationSettings = reactive({
  email_notifications: true,
  push_notifications: true,
  task_reminders: true,
  weekly_digest: false,
})
</script>

<template>
  <div class="space-y-6 max-w-3xl animate-fade-in">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ t('nav.settings') }}
      </h1>
      <p class="mt-1 text-gray-600 dark:text-gray-400">
        Customize your application preferences
      </p>
    </div>

    <!-- Appearance -->
    <UCard>
      <template #header>
        <div class="flex items-center gap-3">
          <UIcon name="i-heroicons-paint-brush" class="w-5 h-5 text-primary-500" />
          <h2 class="text-lg font-semibold">Appearance</h2>
        </div>
      </template>

      <div class="space-y-4">
        <UFormField label="Theme" name="theme">
          <div class="flex gap-3">
            <button
              v-for="option in appearanceOptions"
              :key="option.value"
              type="button"
              class="flex flex-col items-center gap-2 p-4 rounded-lg border-2 transition-all"
              :class="
                colorMode.preference === option.value
                  ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                  : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
              "
              @click="colorMode.preference = option.value"
            >
              <UIcon :name="option.icon" class="w-6 h-6" />
              <span class="text-sm font-medium">{{ option.label }}</span>
            </button>
          </div>
        </UFormField>
      </div>
    </UCard>

    <!-- Notifications -->
    <UCard>
      <template #header>
        <div class="flex items-center gap-3">
          <UIcon name="i-heroicons-bell" class="w-5 h-5 text-primary-500" />
          <h2 class="text-lg font-semibold">Notifications</h2>
        </div>
      </template>

      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">Email Notifications</p>
            <p class="text-sm text-gray-500">Receive important updates via email</p>
          </div>
          <UToggle v-model="notificationSettings.email_notifications" />
        </div>

        <UDivider />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">Push Notifications</p>
            <p class="text-sm text-gray-500">Get notified in your browser</p>
          </div>
          <UToggle v-model="notificationSettings.push_notifications" />
        </div>

        <UDivider />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">Task Reminders</p>
            <p class="text-sm text-gray-500">Remind me about upcoming deadlines</p>
          </div>
          <UToggle v-model="notificationSettings.task_reminders" />
        </div>

        <UDivider />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">Weekly Digest</p>
            <p class="text-sm text-gray-500">Receive a weekly summary of activities</p>
          </div>
          <UToggle v-model="notificationSettings.weekly_digest" />
        </div>
      </div>
    </UCard>

    <!-- Language & Region -->
    <UCard>
      <template #header>
        <div class="flex items-center gap-3">
          <UIcon name="i-heroicons-language" class="w-5 h-5 text-primary-500" />
          <h2 class="text-lg font-semibold">Language & Region</h2>
        </div>
      </template>

      <p class="text-sm text-gray-500">
        Language and timezone settings are managed in your
        <NuxtLink to="/profile" class="text-primary-500 hover:underline">
          profile settings
        </NuxtLink>.
      </p>
    </UCard>

    <!-- Danger Zone -->
    <UCard>
      <template #header>
        <div class="flex items-center gap-3">
          <UIcon name="i-heroicons-exclamation-triangle" class="w-5 h-5 text-red-500" />
          <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
        </div>
      </template>

      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">Export My Data</p>
            <p class="text-sm text-gray-500">Download all your data in JSON format</p>
          </div>
          <UButton color="gray" variant="outline">
            Export
          </UButton>
        </div>

        <UDivider />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium text-red-600">Delete Account</p>
            <p class="text-sm text-gray-500">Permanently delete your account and all data</p>
          </div>
          <UButton color="red" variant="soft">
            Delete Account
          </UButton>
        </div>
      </div>
    </UCard>
  </div>
</template>
