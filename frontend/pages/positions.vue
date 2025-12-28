<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  layout: 'default',
})

const { t, locale } = useI18n()
const toast = useToast()
const authStore = useAuthStore()

const isLoading = ref(true)
const positions = ref<any[]>([])
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

const columns = [
  { key: 'title', label: t('common.title') },
  { key: 'code', label: 'Code' },
  { key: 'department', label: t('common.department') },
  { key: 'is_active', label: t('common.status') },
  { key: 'users_count', label: 'Users' },
  { key: 'actions', label: '' },
]

const fetchPositions = async (page = 1) => {
  isLoading.value = true
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: any[]; meta: any }>(`/positions?page=${page}`)
    positions.value = response.data
    meta.value = response.meta
  } catch (error) {
    toast.add({
      title: 'Error',
      description: 'Failed to load positions',
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
      title: 'Success',
      description: 'Position created successfully',
      color: 'success',
    })
    isCreateModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to create position',
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
      title: 'Success',
      description: 'Position updated successfully',
      color: 'success',
    })
    isEditModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to update position',
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
      title: 'Success',
      description: 'Position deleted successfully',
      color: 'success',
    })
    isDeleteModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to delete position',
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
        <h1 class="text-2xl font-bold">{{ t('common.positions') }}</h1>
        <p class="text-gray-500 dark:text-gray-400">
          Manage organizational positions and roles
        </p>
      </div>
      <UButton icon="i-heroicons-plus" @click="openCreateModal">
        Create Position
      </UButton>
    </div>

    <!-- Positions Table -->
    <UCard>
      <UTable :columns="columns" :rows="positions" :loading="isLoading">
        <template #department-data="{ row }">
          {{ row.department?.name || '—' }}
        </template>

        <template #is_active-data="{ row }">
          <UBadge :color="row.is_active ? 'success' : 'error'" variant="subtle">
            {{ row.is_active ? 'Active' : 'Inactive' }}
          </UBadge>
        </template>

        <template #users_count-data="{ row }">
          <UBadge color="neutral" variant="subtle">
            {{ row.users_count || 0 }}
          </UBadge>
        </template>

        <template #actions-data="{ row }">
          <div class="flex gap-2 justify-end">
            <UButton
              variant="ghost"
              icon="i-heroicons-pencil-square"
              size="xs"
              @click="openEditModal(row)"
            />
            <UButton
              variant="ghost"
              icon="i-heroicons-trash"
              color="error"
              size="xs"
              @click="openDeleteModal(row)"
            />
          </div>
        </template>
      </UTable>

      <!-- Pagination -->
      <template #footer>
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500">
            Showing page {{ meta.current_page }} of {{ meta.last_page }}
            ({{ meta.total }} total)
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
            <h3 class="text-lg font-semibold">Create Position</h3>
          </template>

          <form class="space-y-4" @submit.prevent="handleCreate">
            <UFormField label="Title" name="title" required>
              <UInput v-model="createForm.title" placeholder="e.g., Senior Developer" />
            </UFormField>

            <UFormField label="Code" name="code">
              <UInput v-model="createForm.code" placeholder="e.g., SR-DEV" />
            </UFormField>

            <UFormField label="Department" name="department_id">
              <USelect
                v-model="createForm.department_id"
                :items="departmentOptions"
                placeholder="Select department"
              />
            </UFormField>

            <UFormField label="Description" name="description">
              <UTextarea v-model="createForm.description" rows="3" />
            </UFormField>

            <UFormField>
              <UCheckbox v-model="createForm.is_active" label="Active" />
            </UFormField>
          </form>

          <template #footer>
            <div class="flex justify-end gap-2">
              <UButton variant="outline" @click="isCreateModalOpen = false">
                Cancel
              </UButton>
              <UButton :loading="isCreating" @click="handleCreate">
                Create
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
            <h3 class="text-lg font-semibold">Edit Position</h3>
          </template>

          <form class="space-y-4" @submit.prevent="handleEdit">
            <UFormField label="Title" name="title" required>
              <UInput v-model="editForm.title" placeholder="e.g., Senior Developer" />
            </UFormField>

            <UFormField label="Code" name="code">
              <UInput v-model="editForm.code" placeholder="e.g., SR-DEV" />
            </UFormField>

            <UFormField label="Department" name="department_id">
              <USelect
                v-model="editForm.department_id"
                :items="departmentOptions"
                placeholder="Select department"
              />
            </UFormField>

            <UFormField label="Description" name="description">
              <UTextarea v-model="editForm.description" rows="3" />
            </UFormField>

            <UFormField>
              <UCheckbox v-model="editForm.is_active" label="Active" />
            </UFormField>
          </form>

          <template #footer>
            <div class="flex justify-end gap-2">
              <UButton variant="outline" @click="isEditModalOpen = false">
                Cancel
              </UButton>
              <UButton :loading="isEditing" @click="handleEdit">
                Save
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
            <h3 class="text-lg font-semibold text-red-600">Delete Position</h3>
          </template>

          <p>
            Are you sure you want to delete
            <strong>{{ deletingPosition?.title }}</strong>? This action cannot be undone.
          </p>

          <template #footer>
            <div class="flex justify-end gap-2">
              <UButton variant="outline" @click="isDeleteModalOpen = false">
                Cancel
              </UButton>
              <UButton color="error" :loading="isDeleting" @click="handleDelete">
                Delete
              </UButton>
            </div>
          </template>
        </UCard>
      </template>
    </UModal>
  </div>
</template>
