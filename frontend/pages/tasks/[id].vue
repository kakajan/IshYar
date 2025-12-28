<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  layout: 'default',
})

const route = useRoute()
const router = useRouter()
const { t, locale } = useI18n()
const toast = useToast()

const taskId = computed(() => route.params.id as string)

const isLoading = ref(true)
const task = ref<any>(null)
const comments = ref<any[]>([])
const newComment = ref('')
const isSubmittingComment = ref(false)

// Status options
const statusOptions = [
  { label: 'Pending', value: 'pending', color: 'warning' },
  { label: 'In Progress', value: 'in_progress', color: 'info' },
  { label: 'Completed', value: 'completed', color: 'success' },
  { label: 'Cancelled', value: 'cancelled', color: 'error' },
]

const priorityColors: Record<string, string> = {
  low: 'gray',
  medium: 'warning',
  high: 'orange',
  urgent: 'error',
}

const fetchTask = async () => {
  isLoading.value = true
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: any }>(`/tasks/${taskId.value}`)
    task.value = response.data
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to load task',
      color: 'error',
    })
    router.push('/tasks')
  } finally {
    isLoading.value = false
  }
}

const fetchComments = async () => {
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: any[] }>(`/tasks/${taskId.value}/comments`)
    comments.value = response.data
  } catch (error) {
    console.error('Failed to load comments', error)
  }
}

const handleStart = async () => {
  try {
    const { $api } = useNuxtApp()
    await $api(`/tasks/${taskId.value}/start`, { method: 'POST' })
    toast.add({
      title: 'Success',
      description: 'Task started',
      color: 'success',
    })
    fetchTask()
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to start task',
      color: 'error',
    })
  }
}

const handleComplete = async () => {
  try {
    const { $api } = useNuxtApp()
    await $api(`/tasks/${taskId.value}/complete`, { method: 'POST' })
    toast.add({
      title: 'Success',
      description: 'Task completed',
      color: 'success',
    })
    fetchTask()
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to complete task',
      color: 'error',
    })
  }
}

const submitComment = async () => {
  if (!newComment.value.trim()) return
  isSubmittingComment.value = true
  try {
    const { $api } = useNuxtApp()
    await $api(`/tasks/${taskId.value}/comments`, {
      method: 'POST',
      body: { content: newComment.value },
    })
    newComment.value = ''
    fetchComments()
  } catch (error: any) {
    toast.add({
      title: 'Error',
      description: error.data?.message || 'Failed to add comment',
      color: 'error',
    })
  } finally {
    isSubmittingComment.value = false
  }
}

const formatDate = (dateStr: string) => {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString(locale.value === 'fa' ? 'fa-IR' : 'en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const formatDateTime = (dateStr: string) => {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleString(locale.value === 'fa' ? 'fa-IR' : 'en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(() => {
  fetchTask()
  fetchComments()
})
</script>

<template>
  <div class="p-6">
    <!-- Back button -->
    <NuxtLink to="/tasks" class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mb-6">
      <UIcon name="i-heroicons-arrow-left" class="mr-2" />
      {{ t('common.back') }} to Tasks
    </NuxtLink>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center py-12">
      <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-500" />
    </div>

    <!-- Task Content -->
    <div v-else-if="task" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <UCard>
          <template #header>
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1">
                <h1 class="text-2xl font-bold">{{ task.title }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                  Created {{ formatDateTime(task.created_at) }}
                </p>
              </div>
              <div class="flex gap-2">
                <UBadge :color="priorityColors[task.priority] || 'gray'" variant="subtle" size="lg">
                  {{ task.priority }}
                </UBadge>
                <UBadge :color="statusOptions.find(s => s.value === task.status)?.color || 'gray'" size="lg">
                  {{ task.status?.replace('_', ' ') }}
                </UBadge>
              </div>
            </div>
          </template>

          <div class="prose dark:prose-invert max-w-none">
            <p v-if="task.description">{{ task.description }}</p>
            <p v-else class="text-gray-400 italic">No description provided.</p>
          </div>

          <template #footer>
            <div class="flex gap-2">
              <UButton
                v-if="task.status === 'pending'"
                icon="i-heroicons-play"
                @click="handleStart"
              >
                Start Task
              </UButton>
              <UButton
                v-if="task.status === 'in_progress'"
                icon="i-heroicons-check"
                color="success"
                @click="handleComplete"
              >
                Mark Complete
              </UButton>
              <NuxtLink :to="`/tasks?edit=${task.id}`">
                <UButton variant="outline" icon="i-heroicons-pencil-square">
                  Edit
                </UButton>
              </NuxtLink>
            </div>
          </template>
        </UCard>

        <!-- Comments Section -->
        <UCard>
          <template #header>
            <h2 class="text-lg font-semibold flex items-center gap-2">
              <UIcon name="i-heroicons-chat-bubble-left-right" />
              Comments ({{ comments.length }})
            </h2>
          </template>

          <!-- Comment Form -->
          <form class="mb-6" @submit.prevent="submitComment">
            <UTextarea
              v-model="newComment"
              placeholder="Add a comment..."
              rows="3"
              class="mb-2"
            />
            <UButton type="submit" :loading="isSubmittingComment" :disabled="!newComment.trim()">
              Post Comment
            </UButton>
          </form>

          <!-- Comment List -->
          <div v-if="comments.length" class="space-y-4">
            <div
              v-for="comment in comments"
              :key="comment.id"
              class="flex gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
            >
              <UAvatar :alt="comment.user?.name || 'User'" size="sm" />
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <span class="font-medium">{{ comment.user?.name || 'Unknown' }}</span>
                  <span class="text-sm text-gray-500">{{ formatDateTime(comment.created_at) }}</span>
                </div>
                <p class="text-gray-700 dark:text-gray-300">{{ comment.content }}</p>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-6 text-gray-500">
            No comments yet. Be the first to add one!
          </div>
        </UCard>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Details Card -->
        <UCard>
          <template #header>
            <h2 class="text-lg font-semibold">Details</h2>
          </template>

          <dl class="space-y-4">
            <div>
              <dt class="text-sm text-gray-500 dark:text-gray-400">Assignee</dt>
              <dd class="flex items-center gap-2 mt-1">
                <UAvatar :alt="task.assignee?.name || 'Unassigned'" size="xs" />
                <span>{{ task.assignee?.name || 'Unassigned' }}</span>
              </dd>
            </div>

            <div>
              <dt class="text-sm text-gray-500 dark:text-gray-400">Reporter</dt>
              <dd class="flex items-center gap-2 mt-1">
                <UAvatar :alt="task.reporter?.name || 'Unknown'" size="xs" />
                <span>{{ task.reporter?.name || 'Unknown' }}</span>
              </dd>
            </div>

            <div>
              <dt class="text-sm text-gray-500 dark:text-gray-400">Due Date</dt>
              <dd class="mt-1">
                <span
                  v-if="task.due_date"
                  :class="{ 'text-red-600': new Date(task.due_date) < new Date() && task.status !== 'completed' }"
                >
                  {{ formatDate(task.due_date) }}
                </span>
                <span v-else class="text-gray-400">Not set</span>
              </dd>
            </div>

            <div v-if="task.started_at">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Started</dt>
              <dd class="mt-1">{{ formatDateTime(task.started_at) }}</dd>
            </div>

            <div v-if="task.completed_at">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Completed</dt>
              <dd class="mt-1">{{ formatDateTime(task.completed_at) }}</dd>
            </div>

            <div v-if="task.department">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Department</dt>
              <dd class="mt-1">{{ task.department.name }}</dd>
            </div>
          </dl>
        </UCard>

        <!-- Activity / Metadata -->
        <UCard>
          <template #header>
            <h2 class="text-lg font-semibold">Activity</h2>
          </template>

          <div class="text-sm space-y-2 text-gray-600 dark:text-gray-400">
            <p>
              <span class="font-medium">Created:</span>
              {{ formatDateTime(task.created_at) }}
            </p>
            <p>
              <span class="font-medium">Updated:</span>
              {{ formatDateTime(task.updated_at) }}
            </p>
          </div>
        </UCard>
      </div>
    </div>
  </div>
</template>
