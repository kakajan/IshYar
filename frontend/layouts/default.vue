<script setup lang="ts">
const authStore = useAuthStore()
const colorMode = useColorMode()
const route = useRoute()
const { t } = useI18n()

// Navigation items
const navigation = computed(() => [
  { name: t('nav.dashboard'), to: '/dashboard', icon: 'i-heroicons-home' },
  {
    name: t('nav.tasks'),
    to: '/tasks',
    icon: 'i-heroicons-clipboard-document-list',
  },
  {
    name: t('nav.organization'),
    to: '/organization',
    icon: 'i-heroicons-building-office',
  },
  {
    name: t('nav.departments'),
    to: '/departments',
    icon: 'i-heroicons-building-library',
  },
  { name: t('nav.users'), to: '/users', icon: 'i-heroicons-users' },
  { name: t('nav.settings'), to: '/settings', icon: 'i-heroicons-cog-6-tooth' },
])

const isSidebarOpen = ref(true)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const toggleColorMode = () => {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}

const userMenuItems = computed(() => [
  [
    { label: t('nav.profile'), icon: 'i-heroicons-user', to: '/profile' },
    {
      label: t('nav.settings'),
      icon: 'i-heroicons-cog-6-tooth',
      to: '/settings',
    },
  ],
  [
    {
      label: t('nav.logout'),
      icon: 'i-heroicons-arrow-right-on-rectangle',
      click: () => authStore.logout(),
    },
  ],
])
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 start-0 z-50 flex flex-col bg-white dark:bg-gray-800 border-e border-gray-200 dark:border-gray-700 transition-all duration-300',
        isSidebarOpen ? 'w-64' : 'w-20',
      ]"
    >
      <!-- Logo -->
      <div
        class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700"
      >
        <NuxtLink to="/dashboard" class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center"
          >
            <span class="text-white font-bold">I</span>
          </div>
          <span v-if="isSidebarOpen" class="font-semibold text-lg">IshYar</span>
        </NuxtLink>
        <UButton
          variant="ghost"
          icon="i-heroicons-bars-3"
          size="sm"
          @click="toggleSidebar"
        />
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
              ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400'
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
          ]"
        >
          <UIcon :name="item.icon" class="w-5 h-5 flex-shrink-0" />
          <span v-if="isSidebarOpen">{{ item.name }}</span>
        </NuxtLink>
      </nav>

      <!-- User section -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <UDropdownMenu :items="userMenuItems">
          <UButton variant="ghost" class="w-full justify-start gap-3">
            <UAvatar :alt="authStore.user?.name" size="sm" />
            <span v-if="isSidebarOpen" class="truncate">
              {{ authStore.user?.name }}
            </span>
          </UButton>
        </UDropdownMenu>
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
        class="sticky top-0 z-40 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6"
      >
        <div class="flex items-center gap-4">
          <!-- Breadcrumb or page title can go here -->
        </div>

        <div class="flex items-center gap-4">
          <!-- Theme toggle -->
          <UButton
            variant="ghost"
            :icon="
              colorMode.value === 'dark'
                ? 'i-heroicons-sun'
                : 'i-heroicons-moon'
            "
            @click="toggleColorMode"
          />

          <!-- Language Switcher -->
          <LanguageSwitcher />

          <!-- Notifications -->
          <UButton variant="ghost" icon="i-heroicons-bell" />
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
