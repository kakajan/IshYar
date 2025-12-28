<script setup lang="ts">
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
  CardFooter,
} from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Badge } from '~/components/ui/badge'
import { Textarea } from '~/components/ui/textarea'
import { Avatar, AvatarFallback } from '~/components/ui/avatar'
import {
  ArrowLeft,
  Loader2,
  Play,
  Check,
  Pencil,
  MessageSquare,
} from 'lucide-vue-next'
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  layout: 'default',
})

const route = useRoute()
const router = useRouter()
const { t, locale } = useI18n()
const { add: addToast } = useToast()

const taskId = computed(() => route.params.id as string)

const isLoading = ref(true)
const task = ref<any>(null)
const comments = ref<any[]>([])
const newComment = ref('')
const isSubmittingComment = ref(false)

// Status options
const statusOptions = [
  { label: 'pending', value: 'pending', variant: 'warning' as const },
  { label: 'in_progress', value: 'in_progress', variant: 'info' as const },
  { label: 'completed', value: 'completed', variant: 'success' as const },
  { label: 'cancelled', value: 'cancelled', variant: 'destructive' as const },
]

type BadgeVariant =
  | 'default'
  | 'secondary'
  | 'destructive'
  | 'outline'
  | 'success'
  | 'warning'
  | 'info'

const priorityVariants: Record<string, BadgeVariant> = {
  low: 'secondary',
  medium: 'warning',
  high: 'warning',
  urgent: 'destructive',
}

const getStatusVariant = (status: string): BadgeVariant => {
  const found = statusOptions.find((s) => s.value === status)
  return found?.variant || 'secondary'
}

const getInitials = (name: string | undefined) => {
  if (!name) return 'U'
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const fetchTask = async () => {
  isLoading.value = true
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: any }>(`/tasks/${taskId.value}`)
    task.value = response.data
  } catch (error: any) {
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('task_detail.load_failed'),
      variant: 'destructive',
    })
    router.push('/tasks')
  } finally {
    isLoading.value = false
  }
}

const fetchComments = async () => {
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: any[] }>(
      `/tasks/${taskId.value}/comments`
    )
    comments.value = response.data
  } catch (error) {
    console.error('Failed to load comments', error)
  }
}

const handleStart = async () => {
  try {
    const { $api } = useNuxtApp()
    await $api(`/tasks/${taskId.value}/start`, { method: 'POST' })
    addToast({
      title: t('common.success'),
      description: t('task_detail.started'),
      variant: 'default',
    })
    fetchTask()
  } catch (error: any) {
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('task_detail.start_failed'),
      variant: 'destructive',
    })
  }
}

const handleComplete = async () => {
  try {
    const { $api } = useNuxtApp()
    await $api(`/tasks/${taskId.value}/complete`, { method: 'POST' })
    addToast({
      title: t('common.success'),
      description: t('task_detail.completed'),
      variant: 'default',
    })
    fetchTask()
  } catch (error: any) {
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('task_detail.complete_failed'),
      variant: 'destructive',
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
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('task_detail.comment_failed'),
      variant: 'destructive',
    })
  } finally {
    isSubmittingComment.value = false
  }
}

const formatDate = (dateStr: string) => {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString(
    locale.value === 'fa' ? 'fa-IR' : 'en-US',
    {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    }
  )
}

const formatDateTime = (dateStr: string) => {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleString(
    locale.value === 'fa' ? 'fa-IR' : 'en-US',
    {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }
  )
}

onMounted(() => {
  fetchTask()
  fetchComments()
})
</script>

<template>
  <div class="p-6">
    <!-- Back button -->
    <NuxtLink
      to="/tasks"
      class="inline-flex items-center text-muted-foreground hover:text-foreground mb-6"
    >
      <ArrowLeft class="w-4 h-4 mr-2" />
      {{ t('task_detail.back_to_tasks') }}
    </NuxtLink>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center py-12">
      <Loader2 class="w-8 h-8 animate-spin text-primary" />
    </div>

    <!-- Task Content -->
    <div v-else-if="task" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <Card>
          <CardHeader>
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1">
                <CardTitle class="text-2xl">{{ task.title }}</CardTitle>
                <p class="text-muted-foreground mt-1">
                  {{ t('task_detail.created_at') }}
                  {{ formatDateTime(task.created_at) }}
                </p>
              </div>
              <div class="flex gap-2">
                <Badge
                  :variant="priorityVariants[task.priority] || 'secondary'"
                >
                  {{ task.priority }}
                </Badge>
                <Badge :variant="getStatusVariant(task.status)">
                  {{ task.status?.replace('_', ' ') }}
                </Badge>
              </div>
            </div>
          </CardHeader>

          <CardContent>
            <div class="prose dark:prose-invert max-w-none">
              <p v-if="task.description">{{ task.description }}</p>
              <p v-else class="text-muted-foreground italic">
                {{ t('task_detail.no_description') }}
              </p>
            </div>
          </CardContent>

          <CardFooter class="border-t pt-4">
            <div class="flex gap-2">
              <Button v-if="task.status === 'pending'" @click="handleStart">
                <Play class="w-4 h-4 mr-2" />
                {{ t('task_detail.start_task') }}
              </Button>
              <Button
                v-if="task.status === 'in_progress'"
                variant="default"
                class="bg-green-600 hover:bg-green-700"
                @click="handleComplete"
              >
                <Check class="w-4 h-4 mr-2" />
                {{ t('task_detail.mark_complete') }}
              </Button>
              <NuxtLink :to="`/tasks?edit=${task.id}`">
                <Button variant="outline">
                  <Pencil class="w-4 h-4 mr-2" />
                  {{ t('common.edit') }}
                </Button>
              </NuxtLink>
            </div>
          </CardFooter>
        </Card>

        <!-- Comments Section -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <MessageSquare class="w-5 h-5" />
              {{ t('task_detail.comments') }} ({{ comments.length }})
            </CardTitle>
          </CardHeader>
          <CardContent>
            <!-- Comment Form -->
            <form class="mb-6" @submit.prevent="submitComment">
              <Textarea
                v-model="newComment"
                :placeholder="t('task_detail.add_comment')"
                :rows="3"
                class="mb-2"
              />
              <Button
                type="submit"
                :disabled="isSubmittingComment || !newComment.trim()"
              >
                <Loader2
                  v-if="isSubmittingComment"
                  class="w-4 h-4 mr-2 animate-spin"
                />
                {{ t('task_detail.post_comment') }}
              </Button>
            </form>

            <!-- Comment List -->
            <div v-if="comments.length" class="space-y-4">
              <div
                v-for="comment in comments"
                :key="comment.id"
                class="flex gap-3 p-4 bg-muted rounded-lg"
              >
                <Avatar class="h-8 w-8">
                  <AvatarFallback class="text-xs">{{
                    getInitials(comment.user?.name)
                  }}</AvatarFallback>
                </Avatar>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="font-medium">{{
                      comment.user?.name || 'Unknown'
                    }}</span>
                    <span class="text-sm text-muted-foreground">{{
                      formatDateTime(comment.created_at)
                    }}</span>
                  </div>
                  <p class="text-foreground">{{ comment.content }}</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-6 text-muted-foreground">
              {{ t('task_detail.no_comments') }}
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Details Card -->
        <Card>
          <CardHeader>
            <CardTitle>{{ t('task_detail.details') }}</CardTitle>
          </CardHeader>
          <CardContent>
            <dl class="space-y-4">
              <div>
                <dt class="text-sm text-muted-foreground">
                  {{ t('task_detail.assignee') }}
                </dt>
                <dd class="flex items-center gap-2 mt-1">
                  <Avatar class="h-6 w-6">
                    <AvatarFallback class="text-xs">{{
                      getInitials(task.assignee?.name)
                    }}</AvatarFallback>
                  </Avatar>
                  <span>{{
                    task.assignee?.name || t('task_detail.unassigned')
                  }}</span>
                </dd>
              </div>

              <div>
                <dt class="text-sm text-muted-foreground">
                  {{ t('task_detail.reporter') }}
                </dt>
                <dd class="flex items-center gap-2 mt-1">
                  <Avatar class="h-6 w-6">
                    <AvatarFallback class="text-xs">{{
                      getInitials(task.reporter?.name)
                    }}</AvatarFallback>
                  </Avatar>
                  <span>{{
                    task.reporter?.name || t('task_detail.unknown')
                  }}</span>
                </dd>
              </div>

              <div>
                <dt class="text-sm text-muted-foreground">
                  {{ t('task_detail.due_date') }}
                </dt>
                <dd class="mt-1">
                  <span
                    v-if="task.due_date"
                    :class="{
                      'text-destructive':
                        new Date(task.due_date) < new Date() &&
                        task.status !== 'completed',
                    }"
                  >
                    {{ formatDate(task.due_date) }}
                  </span>
                  <span v-else class="text-muted-foreground">{{
                    t('task_detail.not_set')
                  }}</span>
                </dd>
              </div>

              <div v-if="task.started_at">
                <dt class="text-sm text-muted-foreground">
                  {{ t('task_detail.started') }}
                </dt>
                <dd class="mt-1">{{ formatDateTime(task.started_at) }}</dd>
              </div>

              <div v-if="task.completed_at">
                <dt class="text-sm text-muted-foreground">
                  {{ t('status.completed') }}
                </dt>
                <dd class="mt-1">{{ formatDateTime(task.completed_at) }}</dd>
              </div>

              <div v-if="task.department">
                <dt class="text-sm text-muted-foreground">
                  {{ t('users.department') }}
                </dt>
                <dd class="mt-1">{{ task.department.name }}</dd>
              </div>
            </dl>
          </CardContent>
        </Card>

        <!-- Activity / Metadata -->
        <Card>
          <CardHeader>
            <CardTitle>{{ t('task_detail.activity') }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-sm space-y-2 text-muted-foreground">
              <p>
                <span class="font-medium text-foreground"
                  >{{ t('common.created') }}:</span
                >
                {{ formatDateTime(task.created_at) }}
              </p>
              <p>
                <span class="font-medium text-foreground"
                  >{{ t('common.updated') }}:</span
                >
                {{ formatDateTime(task.updated_at) }}
              </p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>
