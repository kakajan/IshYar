<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'

interface Position {
  id: string | number
  title: string
  code?: string
  description?: string
  department?: { id: string | number; name: string }
  department_id?: number | null
  is_active: boolean
  users_count?: number
}

definePageMeta({
  layout: 'default',
})

const { t, locale } = useI18n()
const toast = useToast()
const authStore = useAuthStore()

const isLoading = ref(true)
const positions = ref<Position[]>([])
const meta = ref<{ current_page: number; last_page: number; total: number }>({
  current_page: 1,
  last_page: 1,
  total: 0,
})

// Create position modal
const isCreateModalOpen = ref(false)
const isCreating = ref(false)
const createForm = reactive({
  title: '',
  code: '',
  description: '',
  department_id: null as number | null,
  is_active: true,
})

// Edit position modal
const isEditModalOpen = ref(false)
const isEditing = ref(false)
const editingPosition = ref<any>(null)
const editForm = reactive({
  title: '',
  code: '',
  description: '',
  department_id: null as number | null,
  is_active: true,
})

// Delete confirmation
const isDeleteModalOpen = ref(false)
const isDeleting = ref(false)
const deletingPosition = ref<any>(null)

// Departments for select
const departments = ref<any[]>([])

const columns = computed(() => [
  { key: 'title', label: t('positions.position_title') },
  { key: 'code', label: t('positions.code') },
  { key: 'department', label: t('positions.department') },
  { key: 'is_active', label: t('common.status') },
  { key: 'users_count', label: t('positions.users_count') },
  { key: 'actions', label: '' },
])

// Typed columns for UTable compatibility
const columnsTyped = computed(() => columns.value as any)

// Helper to type-cast row data
const getPos = (row: unknown): Position => row as Position

// Typed rows for UTable compatibility
const tableRows = computed(() => positions.value as any[])

const fetchPositions = async (page = 1) => {
  isLoading.value = true
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: any[]; meta: any }>(
      `/positions?page=${page}`
    )
    positions.value = response.data
    meta.value = response.meta
  } catch (error) {
    toast.add({
      title: t('common.error'),
      description: t('messages.load_error'),
      color: 'error',
    })
  } finally {
    isLoading.value = false
  }
}

const fetchDepartments = async () => {
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: any[] }>('/departments')
    departments.value = response.data
  } catch (error) {
    console.error('Failed to load departments', error)
  }
}

const departmentOptions = computed(() =>
  departments.value.map((d) => ({ label: d.name, value: d.id }))
)

const openCreateModal = () => {
  createForm.title = ''
  createForm.code = ''
  createForm.description = ''
  createForm.department_id = null
  createForm.is_active = true
  isCreateModalOpen.value = true
}

const handleCreate = async () => {
  isCreating.value = true
  try {
    const { $api } = useNuxtApp()
    await $api('/positions', {
      method: 'POST',
      body: createForm,
    })
    toast.add({
      title: t('common.success'),
      description: t('messages.create_success'),
      color: 'success',
    })
    isCreateModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || t('messages.create_error'),
      color: 'error',
    })
  } finally {
    isCreating.value = false
  }
}

const openEditModal = (position: any) => {
  editingPosition.value = position
  editForm.title = position.title
  editForm.code = position.code
  editForm.description = position.description || ''
  editForm.department_id = position.department_id
  editForm.is_active = position.is_active
  isEditModalOpen.value = true
}

const handleEdit = async () => {
  if (!editingPosition.value) return
  isEditing.value = true
  try {
    const { $api } = useNuxtApp()
    await $api(`/positions/${editingPosition.value.id}`, {
      method: 'PUT',
      body: editForm,
    })
    toast.add({
      title: t('common.success'),
      description: t('messages.update_success'),
      color: 'success',
    })
    isEditModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || t('messages.update_error'),
      color: 'error',
    })
  } finally {
    isEditing.value = false
  }
}

const openDeleteModal = (position: any) => {
  deletingPosition.value = position
  isDeleteModalOpen.value = true
}

const handleDelete = async () => {
  if (!deletingPosition.value) return
  isDeleting.value = true
  try {
    const { $api } = useNuxtApp()
    await $api(`/positions/${deletingPosition.value.id}`, {
      method: 'DELETE',
    })
    toast.add({
      title: t('common.success'),
      description: t('messages.delete_success'),
      color: 'success',
    })
    isDeleteModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || t('messages.delete_error'),
      color: 'error',
    })
  } finally {
    isDeleting.value = false
  }
}

onMounted(() => {
  fetchPositions()
  fetchDepartments()
})
</script>

<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold">{{ t('positions.title') }}</h1>
        <p class="text-gray-500 dark:text-gray-400">
          {{ t('positions.description') }}
        </p>
      </div>
      <UButton icon="i-heroicons-plus" @click="openCreateModal">
        {{ t('positions.create') }}
      </UButton>
    </div>

    <!-- Positions Table -->
    <UCard>
      <UTable :columns="columnsTyped" :rows="tableRows" :loading="isLoading">
        <template #department-data="slotProps">
          {{ getPos(slotProps.row).department?.name || '—' }}
        </template>

        <template #is_active-data="slotProps">
          <UBadge
            :color="getPos(slotProps.row).is_active ? 'success' : 'error'"
            variant="subtle"
          >
            {{
              getPos(slotProps.row).is_active
                ? t('common.active')
                : t('common.inactive')
            }}
          </UBadge>
        </template>

        <template #users_count-data="slotProps">
          <UBadge color="neutral" variant="subtle">
            {{ getPos(slotProps.row).users_count || 0 }}
          </UBadge>
        </template>

        <template #actions-data="slotProps">
          <div class="flex gap-2 justify-end">
            <UButton
              variant="ghost"
              icon="i-heroicons-pencil-square"
              size="xs"
              @click="openEditModal(getPos(slotProps.row))"
            />
            <UButton
              variant="ghost"
              icon="i-heroicons-trash"
              color="error"
              size="xs"
              @click="openDeleteModal(getPos(slotProps.row))"
            />
          </div>
        </template>
      </UTable>

      <!-- Pagination -->
      <template #footer>
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500">
            {{ t('common.showing') }} {{ meta.current_page }}
            {{ t('common.of') }} {{ meta.last_page }} ({{ meta.total }}
            {{ t('tasks.total') }})
          </p>
          <UPagination
            v-model="meta.current_page"
            :page-count="10"
            :total="meta.total"
            @update:model-value="fetchPositions"
          />
        </div>
      </template>
    </UCard>

    <!-- Create Modal -->
    <UModal v-model:open="isCreateModalOpen">
      <template #content>
        <UCard>
          <template #header>
            <h3 class="text-lg font-semibold">{{ t('positions.create') }}</h3>
          </template>

          <form class="space-y-4" @submit.prevent="handleCreate">
            <UFormField
              :label="t('positions.position_title')"
              name="title"
              required
            >
              <UInput
                v-model="createForm.title"
                :placeholder="t('positions.title_placeholder')"
              />
            </UFormField>

            <UFormField :label="t('positions.code')" name="code">
              <UInput
                v-model="createForm.code"
                :placeholder="t('positions.code_placeholder')"
              />
            </UFormField>

            <UFormField :label="t('positions.department')" name="department_id">
              <USelect
                v-model="createForm.department_id"
                :items="departmentOptions"
                :placeholder="t('positions.select_department')"
              />
            </UFormField>

            <UFormField
              :label="t('departments.description')"
              name="description"
            >
              <UTextarea v-model="createForm.description" :rows="3" />
            </UFormField>

            <UFormField>
              <UCheckbox
                v-model="createForm.is_active"
                :label="t('common.active')"
              />
            </UFormField>
          </form>

          <template #footer>
            <div class="flex justify-end gap-2">
              <UButton variant="outline" @click="isCreateModalOpen = false">
                {{ t('common.cancel') }}
              </UButton>
              <UButton :loading="isCreating" @click="handleCreate">
                {{ t('common.create') }}
              </UButton>
            </div>
          </template>
        </UCard>
      </template>
    </UModal>

    <!-- Edit Modal -->
    <UModal v-model:open="isEditModalOpen">
      <template #content>
        <UCard>
          <template #header>
            <h3 class="text-lg font-semibold">{{ t('positions.edit') }}</h3>
          </template>

          <form class="space-y-4" @submit.prevent="handleEdit">
            <UFormField
              :label="t('positions.position_title')"
              name="title"
              required
            >
              <UInput
                v-model="editForm.title"
                :placeholder="t('positions.title_placeholder')"
              />
            </UFormField>

            <UFormField :label="t('positions.code')" name="code">
              <UInput
                v-model="editForm.code"
                :placeholder="t('positions.code_placeholder')"
              />
            </UFormField>

            <UFormField :label="t('positions.department')" name="department_id">
              <USelect
                v-model="editForm.department_id"
                :items="departmentOptions"
                :placeholder="t('positions.select_department')"
              />
            </UFormField>

            <UFormField
              :label="t('departments.description')"
              name="description"
            >
              <UTextarea v-model="editForm.description" :rows="3" />
            </UFormField>

            <UFormField>
              <UCheckbox
                v-model="editForm.is_active"
                :label="t('common.active')"
              />
            </UFormField>
          </form>

          <template #footer>
            <div class="flex justify-end gap-2">
              <UButton variant="outline" @click="isEditModalOpen = false">
                {{ t('common.cancel') }}
              </UButton>
              <UButton :loading="isEditing" @click="handleEdit">
                {{ t('common.save') }}
              </UButton>
            </div>
          </template>
        </UCard>
      </template>
    </UModal>

    <!-- Delete Confirmation Modal -->
    <UModal v-model:open="isDeleteModalOpen">
      <template #content>
        <UCard>
          <template #header>
            <h3 class="text-lg font-semibold text-red-600">
              {{ t('confirm.delete_title') }}
            </h3>
          </template>

          <p>
            {{ t('positions.confirm_delete') }}
          </p>

          <template #footer>
            <div class="flex justify-end gap-2">
              <UButton variant="outline" @click="isDeleteModalOpen = false">
                {{ t('common.cancel') }}
              </UButton>
              <UButton
                color="error"
                :loading="isDeleting"
                @click="handleDelete"
              >
                {{ t('common.delete') }}
              </UButton>
            </div>
          </template>
        </UCard>
      </template>
    </UModal>
  </div>
</template>
