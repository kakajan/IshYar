<script setup lang="ts">
definePageMeta({
  layout: 'default',
})

const { t } = useI18n()
const colorMode = useColorMode()

const appearanceOptions = computed(() => [
  {
    value: 'system',
    label: t('settings.theme_system'),
    icon: 'i-heroicons-computer-desktop',
  },
  { value: 'light', label: t('settings.theme_light'), icon: 'i-heroicons-sun' },
  { value: 'dark', label: t('settings.theme_dark'), icon: 'i-heroicons-moon' },
])

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
        {{ t('settings.subtitle') }}
      </p>
    </div>

    <!-- Appearance -->
    <UCard>
      <template #header>
        <div class="flex items-center gap-3">
          <UIcon
            name="i-heroicons-paint-brush"
            class="w-5 h-5 text-primary-500"
          />
          <h2 class="text-lg font-semibold">{{ t('settings.appearance') }}</h2>
        </div>
      </template>

      <div class="space-y-4">
        <UFormField :label="t('settings.theme')" name="theme">
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
          <h2 class="text-lg font-semibold">
            {{ t('settings.notifications') }}
          </h2>
        </div>
      </template>

      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.email_notifications') }}</p>
            <p class="text-sm text-gray-500">
              {{ t('settings.email_notifications_desc') }}
            </p>
          </div>
          <UToggle v-model="notificationSettings.email_notifications" />
        </div>

        <UDivider />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.push_notifications') }}</p>
            <p class="text-sm text-gray-500">
              {{ t('settings.push_notifications_desc') }}
            </p>
          </div>
          <UToggle v-model="notificationSettings.push_notifications" />
        </div>

        <UDivider />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.task_reminders') }}</p>
            <p class="text-sm text-gray-500">
              {{ t('settings.task_reminders_desc') }}
            </p>
          </div>
          <UToggle v-model="notificationSettings.task_reminders" />
        </div>

        <UDivider />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.weekly_digest') }}</p>
            <p class="text-sm text-gray-500">
              {{ t('settings.weekly_digest_desc') }}
            </p>
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
          <h2 class="text-lg font-semibold">
            {{ t('settings.language_region') }}
          </h2>
        </div>
      </template>

      <p class="text-sm text-gray-500">
        {{ t('settings.language_region_desc') }}
        <NuxtLink to="/profile" class="text-primary-500 hover:underline">
          {{ t('settings.profile_settings') }} </NuxtLink
        >.
      </p>
    </UCard>

    <!-- Danger Zone -->
    <UCard>
      <template #header>
        <div class="flex items-center gap-3">
          <UIcon
            name="i-heroicons-exclamation-triangle"
            class="w-5 h-5 text-red-500"
          />
          <h2 class="text-lg font-semibold text-red-600">
            {{ t('settings.danger_zone') }}
          </h2>
        </div>
      </template>

      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.export_data') }}</p>
            <p class="text-sm text-gray-500">
              {{ t('settings.export_data_desc') }}
            </p>
          </div>
          <UButton color="gray" variant="outline">
            {{ t('settings.export') }}
          </UButton>
        </div>

        <UDivider />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium text-red-600">
              {{ t('settings.delete_account') }}
            </p>
            <p class="text-sm text-gray-500">
              {{ t('settings.delete_account_desc') }}
            </p>
          </div>
          <UButton color="red" variant="soft">
            {{ t('settings.delete_account') }}
          </UButton>
        </div>
      </div>
    </UCard>
  </div>
</template>
