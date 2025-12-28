<script setup lang="ts">
import { Button } from '~/components/ui/button'
import { Avatar } from '~/components/ui/avatar'
import { DropdownMenu } from '~/components/ui/dropdown-menu'
import { Toaster } from '~/components/ui/toast'
import {
  Home,
  ClipboardList,
  Building2,
  Library,
  Briefcase,
  Users,
  Settings,
  Menu,
  Sun,
  Moon,
  User,
  LogOut,
} from 'lucide-vue-next'

const authStore = useAuthStore()
const colorMode = useColorMode()
const route = useRoute()
const { t } = useI18n()
const { toasts, dismiss } = useToast()

// Navigation items with Lucide icons
const navigation = computed(() => [
  { name: t('nav.dashboard'), to: '/dashboard', icon: Home },
  { name: t('nav.tasks'), to: '/tasks', icon: ClipboardList },
  { name: t('nav.organization'), to: '/organization', icon: Building2 },
  { name: t('nav.departments'), to: '/departments', icon: Library },
  { name: t('nav.positions'), to: '/positions', icon: Briefcase },
  { name: t('nav.users'), to: '/users', icon: Users },
  { name: t('nav.settings'), to: '/settings', icon: Settings },
])

const isSidebarOpen = ref(true)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const toggleColorMode = () => {
  colorMode.preference.value =
    colorMode.value.value === 'dark' ? 'light' : 'dark'
}

const userMenuItems = computed(() => [
  [
    {
      label: t('nav.profile'),
      icon: User,
      click: () => navigateTo('/profile'),
    },
    {
      label: t('nav.settings'),
      icon: Settings,
      click: () => navigateTo('/settings'),
    },
  ],
  [{ label: t('nav.logout'), icon: LogOut, click: () => authStore.logout() }],
])
</script>

<template>
  <div class="min-h-screen bg-background">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 start-0 z-50 flex flex-col bg-card border-e transition-all duration-300',
        isSidebarOpen ? 'w-64' : 'w-20',
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center justify-between h-16 px-4 border-b">
        <NuxtLink to="/dashboard" class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center"
          >
            <span class="text-primary-foreground font-bold">I</span>
          </div>
          <span v-if="isSidebarOpen" class="font-semibold text-lg">IshYar</span>
        </NuxtLink>
        <Button variant="ghost" size="icon" @click="toggleSidebar">
          <Menu class="h-5 w-5" />
        </Button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <NuxtLink
          v-for="item in navigation"
          :key="item.name"
          :to="item.to"
          :class="[
            'flex items-center gap-3 px-3 py-2 rounded-lg transition-colors',
            route.path.startsWith(item.to)
              ? 'bg-primary/10 text-primary'
              : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground',
          ]"
        >
          <component :is="item.icon" class="h-5 w-5 shrink-0" />
          <span v-if="isSidebarOpen">{{ item.name }}</span>
        </NuxtLink>
      </nav>

      <!-- User section -->
      <div class="p-4 border-t">
        <DropdownMenu :items="userMenuItems">
          <Button variant="ghost" class="w-full justify-start gap-3">
            <Avatar :alt="authStore.user?.name" size="sm" />
            <span v-if="isSidebarOpen" class="truncate">
              {{ authStore.user?.name }}
            </span>
          </Button>
        </DropdownMenu>
      </div>
    </aside>

    <!-- Main content -->
    <div
      :class="[
        'min-h-screen transition-all duration-300',
        isSidebarOpen ? 'ps-64' : 'ps-20',
      ]"
    >
      <!-- Top bar -->
      <header
        class="sticky top-0 z-40 h-16 bg-card border-b flex items-center justify-between px-6"
      >
        <div class="flex items-center gap-4">
          <div class="text-sm text-muted-foreground">
            {{ route.meta?.title || '' }}
          </div>
        </div>

        <div class="flex items-center gap-2">
          <!-- Theme toggle -->
          <Button variant="ghost" size="icon" @click="toggleColorMode">
            <Sun v-if="colorMode.value.value === 'dark'" class="h-5 w-5" />
            <Moon v-else class="h-5 w-5" />
          </Button>

          <!-- Language Switcher -->
          <LanguageSwitcher />
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>

    <!-- Toast notifications -->
    <Toaster :toasts="toasts" @dismiss="dismiss" />
  </div>
</template>
