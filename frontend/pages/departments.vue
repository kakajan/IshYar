<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          {{ $t('departments.title') }}
        </h1>
        <p class="text-gray-500 dark:text-gray-400">
          {{ $t('departments.description') }}
        </p>
      </div>
      <UButton
        icon="i-heroicons-plus"
        color="primary"
        :label="$t('departments.create')"
        @click="showCreateModal = true"
      />
    </div>

    <!-- Department Tree -->
    <UCard>
      <template #header>
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold">
            {{ $t('departments.tree_view') }}
          </h3>
          <UButtonGroup>
            <UButton
              :color="viewMode === 'tree' ? 'primary' : 'gray'"
              icon="i-heroicons-rectangle-group"
              @click="viewMode = 'tree'"
            />
            <UButton
              :color="viewMode === 'list' ? 'primary' : 'gray'"
              icon="i-heroicons-list-bullet"
              @click="viewMode = 'list'"
            />
          </UButtonGroup>
        </div>
      </template>

      <!-- Tree View -->
      <div v-if="viewMode === 'tree'" class="space-y-2">
        <DepartmentNode
          v-for="dept in rootDepartments"
          :key="dept.id"
          :department="dept"
          :level="0"
          @edit="editDepartment"
          @delete="confirmDelete"
        />
        <div
          v-if="rootDepartments.length === 0"
          class="text-center py-8 text-gray-500"
        >
          {{ $t('departments.no_departments') }}
        </div>
      </div>

      <!-- List View -->
      <UTable
        v-if="viewMode === 'list'"
        :rows="allDepartments"
        :columns="columns"
      >
        <template #name-data="{ row }">
          <div class="flex items-center gap-2">
            <UIcon
              name="i-heroicons-building-library"
              class="text-primary-500"
            />
            <span class="font-medium">{{ row.name }}</span>
            <UBadge v-if="row.code" size="xs" color="gray">{{
              row.code
            }}</UBadge>
          </div>
        </template>
        <template #head-data="{ row }">
          <span v-if="row.head">{{ row.head.name }}</span>
          <span v-else class="text-gray-400">—</span>
        </template>
        <template #is_active-data="{ row }">
          <UBadge :color="row.is_active ? 'green' : 'red'">
            {{ row.is_active ? $t('common.active') : $t('common.inactive') }}
          </UBadge>
        </template>
        <template #actions-data="{ row }">
          <UDropdown :items="getActions(row)">
            <UButton
              color="gray"
              variant="ghost"
              icon="i-heroicons-ellipsis-vertical"
            />
          </UDropdown>
        </template>
      </UTable>
    </UCard>

    <!-- Create/Edit Modal -->
    <UModal v-model="showCreateModal">
      <UCard>
        <template #header>
          <h3 class="text-lg font-semibold">
            {{
              editingDepartment
                ? $t('departments.edit')
                : $t('departments.create')
            }}
          </h3>
        </template>

        <form class="space-y-4" @submit.prevent="saveDepartment">
          <UFormGroup :label="$t('departments.name')" required>
            <UInput
              v-model="form.name"
              :placeholder="$t('departments.name_placeholder')"
            />
          </UFormGroup>

          <UFormGroup :label="$t('departments.slug')" required>
            <UInput
              v-model="form.slug"
              :placeholder="$t('departments.slug_placeholder')"
            />
          </UFormGroup>

          <UFormGroup :label="$t('departments.code')">
            <UInput
              v-model="form.code"
              :placeholder="$t('departments.code_placeholder')"
            />
          </UFormGroup>

          <UFormGroup :label="$t('departments.parent')">
            <USelect
              v-model="form.parent_id"
              :options="parentOptions"
              :placeholder="$t('departments.select_parent')"
            />
          </UFormGroup>

          <UFormGroup :label="$t('departments.description')">
            <UTextarea v-model="form.description" :rows="3" />
          </UFormGroup>

          <div class="flex items-center gap-2">
            <UCheckbox v-model="form.is_active" :label="$t('common.active')" />
          </div>
        </form>

        <template #footer>
          <div class="flex justify-end gap-3">
            <UButton color="gray" variant="soft" @click="closeModal">
              {{ $t('common.cancel') }}
            </UButton>
            <UButton color="primary" :loading="saving" @click="saveDepartment">
              {{ $t('common.save') }}
            </UButton>
          </div>
        </template>
      </UCard>
    </UModal>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default',
})

const { $api } = useNuxtApp()
const { t: $t } = useI18n()

const viewMode = ref<'tree' | 'list'>('tree')
const allDepartments = ref<any[]>([])
const rootDepartments = ref<any[]>([])
const showCreateModal = ref(false)
const editingDepartment = ref<any>(null)
const saving = ref(false)

const form = ref({
  name: '',
  slug: '',
  code: '',
  parent_id: null as string | null,
  description: '',
  is_active: true,
})

const columns = [
  { key: 'name', label: $t('departments.name') },
  { key: 'code', label: $t('departments.code') },
  { key: 'head', label: $t('departments.head') },
  { key: 'is_active', label: $t('common.status') },
  { key: 'actions', label: '' },
]

const parentOptions = computed(() => [
  { value: null, label: $t('departments.no_parent') },
  ...allDepartments.value.map((d) => ({ value: d.id, label: d.name })),
])

const getActions = (row: any) => [
  [
    {
      label: $t('common.edit'),
      icon: 'i-heroicons-pencil',
      click: () => editDepartment(row),
    },
  ],
  [
    {
      label: $t('common.delete'),
      icon: 'i-heroicons-trash',
      click: () => confirmDelete(row),
    },
  ],
]

const fetchDepartments = async () => {
  try {
    const [allRes, treeRes] = await Promise.all([
      $api('/departments'),
      $api('/departments/tree'),
    ])
    allDepartments.value = allRes.data
    rootDepartments.value = treeRes.data
  } catch (error) {
    console.error('Failed to fetch departments:', error)
  }
}

const editDepartment = (dept: any) => {
  editingDepartment.value = dept
  form.value = {
    name: dept.name,
    slug: dept.slug,
    code: dept.code || '',
    parent_id: dept.parent_id,
    description: dept.description || '',
    is_active: dept.is_active,
  }
  showCreateModal.value = true
}

const saveDepartment = async () => {
  saving.value = true
  try {
    if (editingDepartment.value) {
      await $api(`/departments/${editingDepartment.value.id}`, {
        method: 'PUT',
        body: form.value,
      })
    } else {
      await $api('/departments', {
        method: 'POST',
        body: form.value,
      })
    }
    closeModal()
    await fetchDepartments()
  } catch (error) {
    console.error('Failed to save department:', error)
  } finally {
    saving.value = false
  }
}

const confirmDelete = async (dept: any) => {
  if (confirm($t('departments.confirm_delete'))) {
    try {
      await $api(`/departments/${dept.id}`, { method: 'DELETE' })
      await fetchDepartments()
    } catch (error) {
      console.error('Failed to delete department:', error)
    }
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingDepartment.value = null
  form.value = {
    name: '',
    slug: '',
    code: '',
    parent_id: null,
    description: '',
    is_active: true,
  }
}

onMounted(fetchDepartments)
</script>
