<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          {{ $t('organization.title') }}
        </h1>
        <p class="text-gray-500 dark:text-gray-400">
          {{ $t('organization.description') }}
        </p>
      </div>
    </div>

    <!-- Organization Info Card -->
    <UCard v-if="organization">
      <template #header>
        <div class="flex items-center gap-4">
          <div
            class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-xl flex items-center justify-center"
          >
            <UIcon
              name="i-heroicons-building-office-2"
              class="w-8 h-8 text-primary-500"
            />
          </div>
          <div>
            <h2 class="text-xl font-semibold">{{ organization.name }}</h2>
            <p class="text-sm text-gray-500">{{ organization.slug }}</p>
          </div>
        </div>
      </template>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
          <div class="text-3xl font-bold text-primary-500">
            {{ stats.total_users }}
          </div>
          <div class="text-sm text-gray-500">
            {{ $t('organization.total_users') }}
          </div>
        </div>
        <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
          <div class="text-3xl font-bold text-green-500">
            {{ stats.active_users }}
          </div>
          <div class="text-sm text-gray-500">
            {{ $t('organization.active_users') }}
          </div>
        </div>
        <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
          <div class="text-3xl font-bold text-blue-500">
            {{ stats.departments_count }}
          </div>
          <div class="text-sm text-gray-500">
            {{ $t('organization.departments') }}
          </div>
        </div>
        <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
          <div class="text-3xl font-bold text-purple-500">
            {{ stats.positions_count }}
          </div>
          <div class="text-sm text-gray-500">
            {{ $t('organization.positions') }}
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end">
          <UButton
            icon="i-heroicons-pencil"
            color="primary"
            variant="soft"
            :label="$t('common.edit')"
            @click="showEditModal = true"
          />
        </div>
      </template>
    </UCard>

    <!-- Task Statistics -->
    <UCard>
      <template #header>
        <h3 class="text-lg font-semibold">
          {{ $t('organization.task_stats') }}
        </h3>
      </template>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div
          class="flex items-center gap-3 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg"
        >
          <div
            class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center"
          >
            <UIcon
              name="i-heroicons-clipboard-document-list"
              class="w-5 h-5 text-white"
            />
          </div>
          <div>
            <div class="font-semibold">{{ stats.tasks?.total || 0 }}</div>
            <div class="text-xs text-gray-500">{{ $t('tasks.total') }}</div>
          </div>
        </div>
        <div
          class="flex items-center gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg"
        >
          <div
            class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center"
          >
            <UIcon name="i-heroicons-clock" class="w-5 h-5 text-white" />
          </div>
          <div>
            <div class="font-semibold">{{ stats.tasks?.pending || 0 }}</div>
            <div class="text-xs text-gray-500">{{ $t('tasks.pending') }}</div>
          </div>
        </div>
        <div
          class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg"
        >
          <div
            class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center"
          >
            <UIcon name="i-heroicons-arrow-path" class="w-5 h-5 text-white" />
          </div>
          <div>
            <div class="font-semibold">{{ stats.tasks?.in_progress || 0 }}</div>
            <div class="text-xs text-gray-500">
              {{ $t('tasks.in_progress') }}
            </div>
          </div>
        </div>
        <div
          class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg"
        >
          <div
            class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center"
          >
            <UIcon name="i-heroicons-check-circle" class="w-5 h-5 text-white" />
          </div>
          <div>
            <div class="font-semibold">{{ stats.tasks?.completed || 0 }}</div>
            <div class="text-xs text-gray-500">{{ $t('tasks.completed') }}</div>
          </div>
        </div>
      </div>
    </UCard>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <NuxtLink to="/departments">
        <UCard
          class="hover:border-primary-500 transition-colors cursor-pointer"
        >
          <div class="flex items-center gap-4">
            <div
              class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center"
            >
              <UIcon
                name="i-heroicons-building-library"
                class="w-6 h-6 text-blue-500"
              />
            </div>
            <div>
              <h4 class="font-semibold">{{ $t('nav.departments') }}</h4>
              <p class="text-sm text-gray-500">
                {{ $t('organization.manage_departments') }}
              </p>
            </div>
          </div>
        </UCard>
      </NuxtLink>

      <NuxtLink to="/positions">
        <UCard
          class="hover:border-primary-500 transition-colors cursor-pointer"
        >
          <div class="flex items-center gap-4">
            <div
              class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center"
            >
              <UIcon
                name="i-heroicons-briefcase"
                class="w-6 h-6 text-purple-500"
              />
            </div>
            <div>
              <h4 class="font-semibold">{{ $t('nav.positions') }}</h4>
              <p class="text-sm text-gray-500">
                {{ $t('organization.manage_positions') }}
              </p>
            </div>
          </div>
        </UCard>
      </NuxtLink>

      <NuxtLink to="/users">
        <UCard
          class="hover:border-primary-500 transition-colors cursor-pointer"
        >
          <div class="flex items-center gap-4">
            <div
              class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center"
            >
              <UIcon name="i-heroicons-users" class="w-6 h-6 text-green-500" />
            </div>
            <div>
              <h4 class="font-semibold">{{ $t('nav.users') }}</h4>
              <p class="text-sm text-gray-500">
                {{ $t('organization.manage_users') }}
              </p>
            </div>
          </div>
        </UCard>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default',
})

const { $api } = useNuxtApp()
const { t: $t } = useI18n()

const organization = ref<any>(null)
const stats = ref<any>({})
const showEditModal = ref(false)

// Fetch organization data
onMounted(async () => {
  try {
    const [orgRes, statsRes] = (await Promise.all([
      $api('/organization'),
      $api('/organization/statistics'),
    ])) as [{ data: any }, { data: any }]
    organization.value = orgRes.data
    stats.value = statsRes.data
  } catch (error) {
    console.error('Failed to fetch organization data:', error)
  }
})
</script>
