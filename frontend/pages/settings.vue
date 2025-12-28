<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardContent } from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Label } from '~/components/ui/label'
import { Separator } from '~/components/ui/separator'
import { Switch } from '~/components/ui/switch'
import { Monitor, Sun, Moon, Bell, Globe, AlertTriangle } from 'lucide-vue-next'

definePageMeta({
  layout: 'default',
})

const { t } = useI18n()
const colorMode = useColorMode()

type ColorModeOption = 'system' | 'light' | 'dark'

const appearanceOptions: {
  value: ColorModeOption
  label: string
  icon: any
}[] = [
  { value: 'system', label: 'theme_system', icon: Monitor },
  { value: 'light', label: 'theme_light', icon: Sun },
  { value: 'dark', label: 'theme_dark', icon: Moon },
]

const notificationSettings = reactive({
  email_notifications: true,
  push_notifications: true,
  task_reminders: true,
  weekly_digest: false,
})
</script>

<template>
  <div class="space-y-6 max-w-3xl p-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-foreground">
        {{ t('nav.settings') }}
      </h1>
      <p class="mt-1 text-muted-foreground">
        {{ t('settings.subtitle') }}
      </p>
    </div>

    <!-- Appearance -->
    <Card>
      <CardHeader>
        <div class="flex items-center gap-3">
          <Sun class="w-5 h-5 text-primary" />
          <CardTitle>{{ t('settings.appearance') }}</CardTitle>
        </div>
      </CardHeader>
      <CardContent>
        <div class="space-y-4">
          <Label>{{ t('settings.theme') }}</Label>
          <div class="flex gap-3">
            <button
              v-for="option in appearanceOptions"
              :key="option.value"
              type="button"
              class="flex flex-col items-center gap-2 p-4 rounded-lg border-2 transition-all"
              :class="
                colorMode.preference.value === option.value
                  ? 'border-primary bg-primary/10'
                  : 'border-border hover:border-muted-foreground/50'
              "
              @click="colorMode.preference.value = option.value"
            >
              <component :is="option.icon" class="w-6 h-6" />
              <span class="text-sm font-medium">{{
                t(`settings.${option.label}`)
              }}</span>
            </button>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Notifications -->
    <Card>
      <CardHeader>
        <div class="flex items-center gap-3">
          <Bell class="w-5 h-5 text-primary" />
          <CardTitle>{{ t('settings.notifications') }}</CardTitle>
        </div>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.email_notifications') }}</p>
            <p class="text-sm text-muted-foreground">
              {{ t('settings.email_notifications_desc') }}
            </p>
          </div>
          <Switch v-model="notificationSettings.email_notifications" />
        </div>

        <Separator />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.push_notifications') }}</p>
            <p class="text-sm text-muted-foreground">
              {{ t('settings.push_notifications_desc') }}
            </p>
          </div>
          <Switch v-model="notificationSettings.push_notifications" />
        </div>

        <Separator />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.task_reminders') }}</p>
            <p class="text-sm text-muted-foreground">
              {{ t('settings.task_reminders_desc') }}
            </p>
          </div>
          <Switch v-model="notificationSettings.task_reminders" />
        </div>

        <Separator />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.weekly_digest') }}</p>
            <p class="text-sm text-muted-foreground">
              {{ t('settings.weekly_digest_desc') }}
            </p>
          </div>
          <Switch v-model="notificationSettings.weekly_digest" />
        </div>
      </CardContent>
    </Card>

    <!-- Language & Region -->
    <Card>
      <CardHeader>
        <div class="flex items-center gap-3">
          <Globe class="w-5 h-5 text-primary" />
          <CardTitle>{{ t('settings.language_region') }}</CardTitle>
        </div>
      </CardHeader>
      <CardContent>
        <p class="text-sm text-muted-foreground">
          {{ t('settings.language_region_desc') }}
          <NuxtLink to="/profile" class="text-primary hover:underline">
            {{ t('settings.profile_settings') }} </NuxtLink
          >.
        </p>
      </CardContent>
    </Card>

    <!-- Danger Zone -->
    <Card>
      <CardHeader>
        <div class="flex items-center gap-3">
          <AlertTriangle class="w-5 h-5 text-destructive" />
          <CardTitle class="text-destructive">{{
            t('settings.danger_zone')
          }}</CardTitle>
        </div>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ t('settings.export_data') }}</p>
            <p class="text-sm text-muted-foreground">
              {{ t('settings.export_data_desc') }}
            </p>
          </div>
          <Button variant="outline">
            {{ t('settings.export') }}
          </Button>
        </div>

        <Separator />

        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium text-destructive">
              {{ t('settings.delete_account') }}
            </p>
            <p class="text-sm text-muted-foreground">
              {{ t('settings.delete_account_desc') }}
            </p>
          </div>
          <Button variant="destructive">
            {{ t('settings.delete_account') }}
          </Button>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
