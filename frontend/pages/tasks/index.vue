<script setup lang="ts">
const { $api } = useNuxtApp()
const { t } = useI18n()
const toast = useToast()

// State
const tasks = ref<Task[]>([])
const isLoading = ref(true)
const isCreateModalOpen = ref(false)
const selectedStatus = ref<string>('')
const selectedPriority = ref<string>('')
const searchQuery = ref('')

// Filters (computed for i18n reactivity)
const statusOptions = computed(() => [
  { label: t('common.all'), value: '' },
  { label: t('status.pending'), value: 'pending' },
  { label: t('status.in_progress'), value: 'in_progress' },
  { label: t('status.completed'), value: 'completed' },
  { label: t('status.on_hold'), value: 'on_hold' },
])

const priorityOptions = computed(() => [
  { label: t('common.all'), value: '' },
  { label: t('priority.low'), value: 'low' },
  { label: t('priority.medium'), value: 'medium' },
  { label: t('priority.high'), value: 'high' },
  { label: t('priority.urgent'), value: 'urgent' },
])

// Fetch tasks
const fetchTasks = async () => {
  isLoading.value = true
  try {
    const response = await $api('/tasks', {
      query: {
        status: selectedStatus.value || undefined,
        priority: selectedPriority.value || undefined,
        search: searchQuery.value || undefined,
      },
    })
    tasks.value = response.data.data
  } catch (error) {
    toast.add({
      title: t('common.error'),
      description: t('tasks.fetch_error'),
      color: 'error',
    })
  } finally {
    isLoading.value = false
  }
}

// Create task form
const newTask = reactive({
  title: '',
  description: '',
  type: 'situational',
  priority: 'medium',
  due_date: '',
})

const createTask = async () => {
  try {
    await $api('/tasks', {
      method: 'POST',
      body: newTask,
    })
    toast.add({
      title: t('common.success'),
      description: t('messages.create_success'),
      color: 'success',
    })
    isCreateModalOpen.value = false
    resetForm()
    fetchTasks()
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || t('messages.create_error'),
      color: 'error',
    })
  }
}

const resetForm = () => {
  Object.assign(newTask, {
    title: '',
    description: '',
    type: 'situational',
    priority: 'medium',
    due_date: '',
  })
}

// Colors
const priorityColor = (priority: string) => {
  const colors: Record<string, string> = {
    low: 'gray',
    medium: 'yellow',
    high: 'orange',
    urgent: 'red',
  }
  return colors[priority] || 'gray'
}

const statusColor = (status: string) => {
  const colors: Record<string, string> = {
    pending: 'gray',
    in_progress: 'blue',
    completed: 'green',
    on_hold: 'yellow',
    cancelled: 'red',
  }
  return colors[status] || 'gray'
}

// Watch filters
watch([selectedStatus, selectedPriority], () => {
  fetchTasks()
})

// Debounced search
let searchTimeout: NodeJS.Timeout
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(fetchTasks, 300)
})

// Initial fetch
onMounted(fetchTasks)

// Types
interface Task {
  id: string
  title: string
  description?: string
  type: 'routine' | 'situational'
  status: string
  priority: string
  due_date?: string
  progress: number
  creator?: { name: string }
  assignee?: { name: string }
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('tasks.title') }}</h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">
          {{ $t('tasks.description') }}
        </p>
      </div>
      <UButton icon="i-heroicons-plus" @click="isCreateModalOpen = true">
        {{ $t('tasks.create') }}
      </UButton>
    </div>

    <!-- Filters -->
    <UCard>
      <div class="flex flex-wrap gap-4">
        <UInput
          v-model="searchQuery"
          :placeholder="$t('common.search')"
          icon="i-heroicons-magnifying-glass"
          class="w-64"
        />
        <USelectMenu
          v-model="selectedStatus"
          :options="statusOptions"
          :placeholder="$t('common.status')"
          class="w-40"
        />
        <USelectMenu
          v-model="selectedPriority"
          :options="priorityOptions"
          :placeholder="$t('tasks.priority.label')"
          class="w-40"
        />
      </div>
    </UCard>

    <!-- Tasks list -->
    <UCard>
      <div v-if="isLoading" class="flex justify-center py-8">
        <div
          class="animate-spin w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full"
        />
      </div>

      <div v-else-if="tasks.length === 0" class="text-center py-12">
        <UIcon
          name="i-heroicons-clipboard-document-list"
          class="w-12 h-12 text-gray-400 mx-auto"
        />
        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
          {{ $t('tasks.no_tasks') }}
        </h3>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          {{ $t('tasks.get_started') }}
        </p>
        <UButton class="mt-4" @click="isCreateModalOpen = true">
          {{ $t('tasks.create') }}
        </UButton>
      </div>

      <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
        <div
          v-for="task in tasks"
          :key="task.id"
          class="py-4 first:pt-0 last:pb-0 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/50 -mx-4 px-4 rounded-lg transition-colors cursor-pointer"
        >
          <div class="flex items-center gap-4 flex-1">
            <UCheckbox :model-value="task.status === 'completed'" />
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-900 dark:text-white truncate">
                {{ task.title }}
              </p>
              <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                <span v-if="task.assignee">{{ task.assignee.name }}</span>
                <span v-if="task.due_date">Due {{ task.due_date }}</span>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <!-- Progress -->
            <div v-if="task.progress > 0" class="w-20">
              <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full">
                <div
                  class="h-full bg-primary-500 rounded-full transition-all"
                  :style="{ width: `${task.progress}%` }"
                />
              </div>
            </div>

            <UBadge
              :color="priorityColor(task.priority)"
              variant="subtle"
              size="sm"
            >
              {{ task.priority }}
            </UBadge>
            <UBadge
              :color="statusColor(task.status)"
              variant="subtle"
              size="sm"
            >
              {{ task.status.replace('_', ' ') }}
            </UBadge>

            <UDropdownMenu
              :items="[
                [
                  { label: $t('common.edit'), icon: 'i-heroicons-pencil' },
                  { label: $t('tasks.view'), icon: 'i-heroicons-eye' },
                ],
                [{ label: $t('common.delete'), icon: 'i-heroicons-trash', color: 'red' }],
              ]"
            >
              <UButton
                variant="ghost"
                icon="i-heroicons-ellipsis-vertical"
                size="sm"
              />
            </UDropdownMenu>
          </div>
        </div>
      </div>
    </UCard>

    <!-- Create Task Modal -->
    <UModal v-model:open="isCreateModalOpen">
      <template #header>
        <h3 class="text-lg font-semibold">{{ $t('tasks.create') }}</h3>
      </template>

      <form class="space-y-4 p-4" @submit.prevent="createTask">
        <UFormField :label="$t('tasks.form.title')" name="title" required>
          <UInput v-model="newTask.title" :placeholder="$t('tasks.form.title_placeholder')" />
        </UFormField>

        <UFormField :label="$t('tasks.form.description')" name="description">
          <UTextarea
            v-model="newTask.description"
            :placeholder="$t('tasks.form.description_placeholder')"
            rows="3"
          />
        </UFormField>

        <div class="grid grid-cols-2 gap-4">
          <UFormField :label="$t('tasks.form.type')" name="type">
            <USelectMenu
              v-model="newTask.type"
              :options="[
                { label: $t('tasks.situational'), value: 'situational' },
                { label: $t('tasks.routine'), value: 'routine' },
              ]"
            />
          </UFormField>

          <UFormField :label="$t('tasks.priority.label')" name="priority">
            <USelectMenu
              v-model="newTask.priority"
              :options="[
                { label: $t('priority.low'), value: 'low' },
                { label: $t('priority.medium'), value: 'medium' },
                { label: $t('priority.high'), value: 'high' },
                { label: $t('priority.urgent'), value: 'urgent' },
              ]"
            />
          </UFormField>
        </div>

        <UFormField :label="$t('tasks.due_date')" name="due_date">
          <UInput v-model="newTask.due_date" type="date" />
        </UFormField>

        <div class="flex justify-end gap-3 pt-4">
          <UButton variant="ghost" @click="isCreateModalOpen = false">
            {{ $t('common.cancel') }}
          </UButton>
          <UButton type="submit"> {{ $t('common.create') }} </UButton>
        </div>
      </form>
    </UModal>
  </div>
</template>
