<script setup lang="ts">
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardFooter,
} from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import {
  Building2,
  Building,
  Briefcase,
  Users,
  ClipboardList,
  Clock,
  RefreshCw,
  CheckCircle,
  Pencil,
} from 'lucide-vue-next'

definePageMeta({
  layout: 'default',
})

const { $api } = useNuxtApp()
const { t: $t } = useI18n()

interface OrganizationStats {
  total_users: number
  active_users: number
  departments_count: number
  positions_count: number
  tasks?: {
    total: number
    pending: number
    in_progress: number
    completed: number
  }
}

const organization = ref<{ name: string; slug: string } | null>(null)
const stats = ref<OrganizationStats>({
  total_users: 0,
  active_users: 0,
  departments_count: 0,
  positions_count: 0,
})
const showEditModal = ref(false)

// Fetch organization data
onMounted(async () => {
  try {
    const [orgRes, statsRes] = (await Promise.all([
      $api('/organization'),
      $api('/organization/statistics'),
    ])) as [
      { data: { name: string; slug: string } },
      { data: OrganizationStats }
    ]
    organization.value = orgRes.data
    stats.value = statsRes.data
  } catch (error) {
    console.error('Failed to fetch organization data:', error)
  }
})
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-foreground">
          {{ $t('organization.title') }}
        </h1>
        <p class="text-muted-foreground">
          {{ $t('organization.description') }}
        </p>
      </div>
    </div>

    <!-- Organization Info Card -->
    <Card v-if="organization">
      <CardHeader class="flex flex-row items-center gap-4">
        <div
          class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center"
        >
          <Building2 class="w-8 h-8 text-primary" />
        </div>
        <div>
          <CardTitle class="text-xl">{{ organization.name }}</CardTitle>
          <p class="text-sm text-muted-foreground">{{ organization.slug }}</p>
        </div>
      </CardHeader>

      <CardContent>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="text-center p-4 bg-muted rounded-lg">
            <div class="text-3xl font-bold text-primary">
              {{ stats.total_users }}
            </div>
            <div class="text-sm text-muted-foreground">
              {{ $t('organization.total_users') }}
            </div>
          </div>
          <div class="text-center p-4 bg-muted rounded-lg">
            <div class="text-3xl font-bold text-green-500">
              {{ stats.active_users }}
            </div>
            <div class="text-sm text-muted-foreground">
              {{ $t('organization.active_users') }}
            </div>
          </div>
          <div class="text-center p-4 bg-muted rounded-lg">
            <div class="text-3xl font-bold text-blue-500">
              {{ stats.departments_count }}
            </div>
            <div class="text-sm text-muted-foreground">
              {{ $t('organization.departments') }}
            </div>
          </div>
          <div class="text-center p-4 bg-muted rounded-lg">
            <div class="text-3xl font-bold text-purple-500">
              {{ stats.positions_count }}
            </div>
            <div class="text-sm text-muted-foreground">
              {{ $t('organization.positions') }}
            </div>
          </div>
        </div>
      </CardContent>

      <CardFooter class="justify-end">
        <Button variant="outline" @click="showEditModal = true">
          <Pencil class="w-4 h-4 mr-2" />
          {{ $t('common.edit') }}
        </Button>
      </CardFooter>
    </Card>

    <!-- Task Statistics -->
    <Card>
      <CardHeader>
        <CardTitle>{{ $t('organization.task_stats') }}</CardTitle>
      </CardHeader>

      <CardContent>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="flex items-center gap-3 p-3 bg-muted rounded-lg">
            <div
              class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center"
            >
              <ClipboardList class="w-5 h-5 text-white" />
            </div>
            <div>
              <div class="font-semibold">{{ stats.tasks?.total || 0 }}</div>
              <div class="text-xs text-muted-foreground">
                {{ $t('tasks.total') }}
              </div>
            </div>
          </div>
          <div
            class="flex items-center gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg"
          >
            <div
              class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center"
            >
              <Clock class="w-5 h-5 text-white" />
            </div>
            <div>
              <div class="font-semibold">{{ stats.tasks?.pending || 0 }}</div>
              <div class="text-xs text-muted-foreground">
                {{ $t('tasks.pending') }}
              </div>
            </div>
          </div>
          <div
            class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg"
          >
            <div
              class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center"
            >
              <RefreshCw class="w-5 h-5 text-white" />
            </div>
            <div>
              <div class="font-semibold">
                {{ stats.tasks?.in_progress || 0 }}
              </div>
              <div class="text-xs text-muted-foreground">
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
              <CheckCircle class="w-5 h-5 text-white" />
            </div>
            <div>
              <div class="font-semibold">{{ stats.tasks?.completed || 0 }}</div>
              <div class="text-xs text-muted-foreground">
                {{ $t('tasks.completed') }}
              </div>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <NuxtLink to="/departments">
        <Card class="hover:border-primary transition-colors cursor-pointer">
          <CardContent class="p-6">
            <div class="flex items-center gap-4">
              <div
                class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center"
              >
                <Building class="w-6 h-6 text-blue-500" />
              </div>
              <div>
                <h4 class="font-semibold">{{ $t('nav.departments') }}</h4>
                <p class="text-sm text-muted-foreground">
                  {{ $t('organization.manage_departments') }}
                </p>
              </div>
            </div>
          </CardContent>
        </Card>
      </NuxtLink>

      <NuxtLink to="/positions">
        <Card class="hover:border-primary transition-colors cursor-pointer">
          <CardContent class="p-6">
            <div class="flex items-center gap-4">
              <div
                class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center"
              >
                <Briefcase class="w-6 h-6 text-purple-500" />
              </div>
              <div>
                <h4 class="font-semibold">{{ $t('nav.positions') }}</h4>
                <p class="text-sm text-muted-foreground">
                  {{ $t('organization.manage_positions') }}
                </p>
              </div>
            </div>
          </CardContent>
        </Card>
      </NuxtLink>

      <NuxtLink to="/users">
        <Card class="hover:border-primary transition-colors cursor-pointer">
          <CardContent class="p-6">
            <div class="flex items-center gap-4">
              <div
                class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center"
              >
                <Users class="w-6 h-6 text-green-500" />
              </div>
              <div>
                <h4 class="font-semibold">{{ $t('nav.users') }}</h4>
                <p class="text-sm text-muted-foreground">
                  {{ $t('organization.manage_users') }}
                </p>
              </div>
            </div>
          </CardContent>
        </Card>
      </NuxtLink>
    </div>
  </div>
</template>
