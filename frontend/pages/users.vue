<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          {{ $t('users.title') }}
        </h1>
        <p class="text-gray-500 dark:text-gray-400">
          {{ $t('users.description') }}
        </p>
      </div>
    </div>

    <!-- Filters -->
    <UCard>
      <div class="flex flex-wrap gap-4">
        <UInput
          v-model="search"
          icon="i-heroicons-magnifying-glass"
          :placeholder="$t('users.search_placeholder')"
          class="flex-1 min-w-[200px]"
        />
        <USelect
          v-model="filters.department_id"
          :options="departmentOptions"
          :placeholder="$t('users.filter_department')"
          class="w-48"
        />
        <USelect
          v-model="filters.is_active"
          :options="statusOptions"
          :placeholder="$t('users.filter_status')"
          class="w-36"
        />
      </div>
    </UCard>

    <!-- Users List -->
    <UCard>
      <UTable :rows="tableRows" :columns="columnsTyped" :loading="loading">
        <template #name-data="slotProps">
          <div class="flex items-center gap-3">
            <UAvatar :alt="getUser(slotProps.row).name" size="sm" />
            <div>
              <div class="font-medium">{{ getUser(slotProps.row).name }}</div>
              <div class="text-sm text-gray-500">
                {{ getUser(slotProps.row).email }}
              </div>
            </div>
          </div>
        </template>
        <template #department-data="slotProps">
          <span v-if="getUser(slotProps.row).department">{{
            getUser(slotProps.row).department?.name
          }}</span>
          <span v-else class="text-gray-400">—</span>
        </template>
        <template #position-data="slotProps">
          <span v-if="getUser(slotProps.row).position">{{
            getUser(slotProps.row).position?.name
          }}</span>
          <span v-else class="text-gray-400">—</span>
        </template>
        <template #roles-data="slotProps">
          <div class="flex gap-1">
            <UBadge
              v-for="role in getUser(slotProps.row).roles"
              :key="role.id"
              size="xs"
              :color="getRoleColor(role.name)"
            >
              {{ role.name }}
            </UBadge>
          </div>
        </template>
        <template #is_active-data="slotProps">
          <UBadge
            :color="getUser(slotProps.row).is_active ? 'success' : 'error'"
            size="sm"
          >
            {{
              getUser(slotProps.row).is_active
                ? $t('common.active')
                : $t('common.inactive')
            }}
          </UBadge>
        </template>
        <template #actions-data="slotProps">
          <UButton
            color="neutral"
            variant="ghost"
            icon="i-heroicons-eye"
            @click="viewUser(getUser(slotProps.row))"
          />
        </template>
      </UTable>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4">
        <div class="text-sm text-gray-500">
          {{ $t('common.showing') }} {{ users.length }} {{ $t('common.of') }}
          {{ total }}
        </div>
        <UPagination v-model="page" :page-count="perPage" :total="total" />
      </div>
    </UCard>

    <!-- User Detail Modal -->
    <UModal v-model="showUserModal">
      <UCard v-if="selectedUser">
        <template #header>
          <div class="flex items-center gap-4">
            <UAvatar :alt="selectedUser.name" size="lg" />
            <div>
              <h3 class="text-lg font-semibold">{{ selectedUser.name }}</h3>
              <p class="text-sm text-gray-500">{{ selectedUser.email }}</p>
            </div>
          </div>
        </template>

        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <div class="text-sm text-gray-500">
                {{ $t('users.department') }}
              </div>
              <div class="font-medium">
                {{ selectedUser.department?.name || '—' }}
              </div>
            </div>
            <div>
              <div class="text-sm text-gray-500">
                {{ $t('users.position') }}
              </div>
              <div class="font-medium">
                {{ selectedUser.position?.name || '—' }}
              </div>
            </div>
            <div>
              <div class="text-sm text-gray-500">{{ $t('users.manager') }}</div>
              <div class="font-medium">
                {{ selectedUser.manager?.name || '—' }}
              </div>
            </div>
            <div>
              <div class="text-sm text-gray-500">{{ $t('users.phone') }}</div>
              <div class="font-medium">{{ selectedUser.phone || '—' }}</div>
            </div>
          </div>

          <UDivider />

          <div>
            <div class="text-sm text-gray-500 mb-2">
              {{ $t('users.roles') }}
            </div>
            <div class="flex gap-2">
              <UBadge
                v-for="role in selectedUser.roles"
                :key="role.id"
                :color="getRoleColor(role.name)"
              >
                {{ role.name }}
              </UBadge>
            </div>
          </div>
        </div>

        <template #footer>
          <UButton block color="neutral" @click="showUserModal = false">
            {{ $t('common.close') }}
          </UButton>
        </template>
      </UCard>
    </UModal>
  </div>
</template>

<script setup lang="ts">
interface User {
  id: string
  name: string
  email: string
  phone?: string
  department?: { id: string; name: string }
  position?: { id: string; name: string }
  manager?: { id: string; name: string }
  roles: { id: string; name: string }[]
  is_active: boolean
}

definePageMeta({
  layout: 'default',
})

const { $api } = useNuxtApp()
const { t: $t } = useI18n()

const users = ref<User[]>([])
const departments = ref<{ id: string; name: string }[]>([])
const loading = ref(false)
const search = ref('')
const page = ref(1)
const perPage = ref(15)
const total = ref(0)
const showUserModal = ref(false)
const selectedUser = ref<any>(null)

const filters = ref({
  department_id: null as string | null,
  is_active: null as boolean | null,
})

const columns = [
  { key: 'name', label: $t('users.name') },
  { key: 'department', label: $t('users.department') },
  { key: 'position', label: $t('users.position') },
  { key: 'roles', label: $t('users.roles') },
  { key: 'is_active', label: $t('common.status') },
  { key: 'actions', label: '' },
]

// Typed columns for UTable compatibility
const columnsTyped = computed(() => columns as any)

// Helper to type-cast row data
const getUser = (row: unknown): User => row as User

// Typed rows for UTable compatibility
const tableRows = computed(() => users.value as any[])

const departmentOptions = computed(() => [
  { value: null, label: $t('users.all_departments') },
  ...departments.value.map((d) => ({ value: d.id, label: d.name })),
])

const statusOptions = [
  { value: null, label: $t('users.all_status') },
  { value: true, label: $t('common.active') },
  { value: false, label: $t('common.inactive') },
]

type BadgeColor =
  | 'error'
  | 'primary'
  | 'secondary'
  | 'success'
  | 'info'
  | 'warning'
  | 'neutral'

const getRoleColor = (role: string): BadgeColor => {
  const colors: Record<string, BadgeColor> = {
    'super-admin': 'error',
    admin: 'warning',
    manager: 'info',
    employee: 'success',
  }
  return colors[role] || 'neutral'
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (search.value) params.append('search', search.value)
    if (filters.value.department_id)
      params.append('department_id', filters.value.department_id)
    if (filters.value.is_active !== null)
      params.append('is_active', String(filters.value.is_active))
    params.append('page', String(page.value))
    params.append('per_page', String(perPage.value))

    const response = (await $api(`/users?${params.toString()}`)) as {
      data: any[]
      meta?: { total?: number }
    }
    users.value = response.data
    total.value = response.meta?.total || response.data.length
  } catch (error) {
    console.error('Failed to fetch users:', error)
  } finally {
    loading.value = false
  }
}

const fetchDepartments = async () => {
  try {
    const response = (await $api('/departments')) as { data: any[] }
    departments.value = response.data
  } catch (error) {
    console.error('Failed to fetch departments:', error)
  }
}

const viewUser = async (user: any) => {
  try {
    const response = (await $api(`/users/${user.id}`)) as { data: any }
    selectedUser.value = response.data
    showUserModal.value = true
  } catch (error) {
    console.error('Failed to fetch user details:', error)
  }
}

// Watch for filter changes
watch([search, filters, page], fetchUsers, { deep: true })

onMounted(async () => {
  await Promise.all([fetchUsers(), fetchDepartments()])
})
</script>
