<script setup lang="ts">
import { Button } from '~/components/ui/button'
import { Avatar } from '~/components/ui/avatar'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
} from '~/components/ui/dropdown-menu'
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
  X,
} from 'lucide-vue-next'
import { useWindowSize } from '@vueuse/core'

const authStore = useAuthStore()
const colorMode = useColorMode()
const route = useRoute()
const { t, locale } = useI18n()
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

const rtlLocales = ['fa', 'ar', 'he', 'ur']
const isRtl = computed(() => rtlLocales.includes(locale.value))
const slideFromClass = computed(() =>
  isRtl.value ? 'translate-x-full' : '-translate-x-full'
)

// Mobile-first: closed by default on mobile, open on desktop
const isSidebarOpen = ref(false)
const { width } = useWindowSize()
const isDesktop = computed(() => width.value >= 1024)

watchEffect(() => {
  if (isDesktop.value) {
    isSidebarOpen.value = true
  }
})

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = () => {
  if (!isDesktop.value) isSidebarOpen.value = false
}

const handleProfileSelect = () => {
  navigateTo('/profile')
  closeSidebar()
}

const handleSettingsSelect = () => {
  navigateTo('/settings')
  closeSidebar()
}

const toggleColorMode = () => {
  colorMode.preference.value =
    colorMode.value.value === 'dark' ? 'light' : 'dark'
}
</script>

<template>
  <div class="min-h-screen bg-background">
    <!-- Mobile overlay -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isSidebarOpen && !isDesktop"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        @click="closeSidebar"
      />
    </Transition>

    <!-- Sidebar -->
    <Transition
      enter-active-class="transition-transform duration-300"
      :enter-from-class="slideFromClass"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-300"
      leave-from-class="translate-x-0"
      :leave-to-class="slideFromClass"
    >
      <aside
        v-if="isSidebarOpen || isDesktop"
        :class="[
          'fixed inset-y-0 start-0 z-50 flex flex-col bg-card border-e transition-all duration-300',
          'w-64 lg:w-64',
        ]"
      >
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 border-b">
          <NuxtLink
            to="/dashboard"
            class="flex items-center gap-3"
            @click="closeSidebar"
          >
            <div
              class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center"
            >
              <span class="text-primary-foreground font-bold">I</span>
            </div>
            <span class="font-semibold text-lg">IshYar</span>
          </NuxtLink>
          <Button
            variant="ghost"
            size="icon"
            class="lg:hidden"
            @click="closeSidebar"
          >
            <X class="h-5 w-5" />
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
            @click="closeSidebar"
          >
            <component :is="item.icon" class="h-5 w-5 shrink-0" />
            <span>{{ item.name }}</span>
          </NuxtLink>
        </nav>

        <!-- User section -->
        <div class="p-4 border-t">
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="ghost" class="w-full justify-start gap-3">
                <Avatar :alt="authStore.user?.name" size="sm" />
                <span class="truncate">
                  {{ authStore.user?.name }}
                </span>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-56">
              <DropdownMenuItem @select="handleProfileSelect">
                <User class="w-4 h-4 mr-2" />
                {{ t('nav.profile') }}
              </DropdownMenuItem>
              <DropdownMenuItem @select="handleSettingsSelect">
                <Settings class="w-4 h-4 mr-2" />
                {{ t('nav.settings') }}
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem @select="authStore.logout()">
                <LogOut class="w-4 h-4 mr-2" />
                {{ t('nav.logout') }}
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </aside>
    </Transition>

    <!-- Main content -->
    <div :class="['min-h-screen transition-all duration-300', 'lg:ps-64']">
      <!-- Top bar -->
      <header
        class="sticky top-0 z-40 h-16 bg-card border-b flex items-center justify-between px-4 lg:px-6"
      >
        <div class="flex items-center gap-4">
          <!-- Mobile hamburger menu -->
          <Button
            variant="ghost"
            size="icon"
            class="lg:hidden"
            @click="toggleSidebar"
          >
            <Menu class="h-5 w-5" />
          </Button>
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
      <main class="p-4 lg:p-6">
        <slot />
      </main>
    </div>

    <!-- Toast notifications -->
    <Toaster :toasts="toasts" @dismiss="dismiss" />
  </div>
</template>
