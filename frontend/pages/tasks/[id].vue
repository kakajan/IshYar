<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
  CardFooter,
} from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Textarea } from '~/components/ui/textarea'
import { Input } from '~/components/ui/input'
import { Avatar, AvatarFallback } from '~/components/ui/avatar'
import {
  ArrowLeft,
  Loader2,
  Play,
  Check,
  MessageSquare,
  Calendar,
  User,
  Tags,

  ListTodo,
  Plus,
  Trash2,
  Circle,
  CheckCircle2
} from 'lucide-vue-next'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '~/components/ui/dropdown-menu'
import { Progress } from '~/components/ui/progress'
import UserSelect from '~/components/tasks/UserSelect.vue'
import LabelPicker from '~/components/tasks/LabelPicker.vue'
import StatusBadgePicker from '~/components/tasks/StatusBadgePicker.vue'
import { useDebounceFn } from '@vueuse/core'

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
const isSaving = ref(false)
const lastSaved = ref<Date | null>(null)

// Status options
const statusOptions = computed(() => [
  { label: t('status.pending'), value: 'pending', variant: 'warning' },
  { label: t('status.in_progress'), value: 'in_progress', variant: 'info' },
  { label: t('status.completed'), value: 'completed', variant: 'success' },
  { label: t('status.cancelled'), value: 'cancelled', variant: 'destructive' },
])

const priorityColors: Record<string, string> = {
  low: '#94a3b8',
  medium: '#3b82f6',
  high: '#f59e0b',
  urgent: '#ef4444',
  critical: '#ef4444',
}

const priorityOptions = computed(() => [
  { label: t('tasks.priority.low'), value: 'low' },
  { label: t('tasks.priority.medium'), value: 'medium' },
  { label: t('tasks.priority.high'), value: 'high' },
  { label: t('tasks.priority.urgent'), value: 'urgent' },
])

const checklistItem = ref('')

const checklistProgress = computed(() => {
    if (!task.value?.checklist || !task.value.checklist.length) return 0
    const completed = task.value.checklist.filter((i: any) => i.done).length
    return Math.round((completed / task.value.checklist.length) * 100)
})

const priorityColor = computed(() => {
    if(!task.value?.priority) return priorityColors.medium
    return priorityColors[task.value.priority] || priorityColors.medium
})

const updateTask = useDebounceFn(async (data: any) => {
  isSaving.value = true
  try {
    const { $api } = useNuxtApp()
    await $api(`/tasks/${taskId.value}`, {
      method: 'PUT',
      body: data
    })
    lastSaved.value = new Date()
    // Show success feedback briefly
    setTimeout(() => {
      isSaving.value = false
    }, 500)
  } catch (error: any) {
    isSaving.value = false
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('common.error_occurred'),
      variant: 'destructive',
    })
  }
}, 1000)

// Watchers for auto-save
watch(() => task.value?.title, (newVal, oldVal) => {
  if (oldVal !== undefined && newVal !== oldVal) updateTask({ title: newVal })
})
watch(() => task.value?.description, (newVal, oldVal) => {
  if (oldVal !== undefined && newVal !== oldVal) updateTask({ description: newVal })
})

// Priority is handled explicitly by the handler to avoid immediate jumpy UI if we used watcher alone
watch(() => task.value?.priority, (newVal, oldVal) => {
   // Optional: side effects if needed
})
watch(() => task.value?.due_date, (newVal, oldVal) => {
  if (oldVal !== undefined && newVal !== oldVal) updateTask({ due_date: newVal })
})

// Special handlers for complex components
const handleAssigneesChange = (val: string | string[]) => {
    const userIds = Array.isArray(val) ? val : [val];
    task.value.assignee_ids = userIds;
    // Update local object immediately to reflect UI
    updateTask({ assignee_ids: userIds })
}

const handleLabelsChange = async (labelIds: string[]) => {
    // Optimistic update
    task.value.label_ids = labelIds
    
    // Call the specific endpoint for labels sync
    try {
        const { $api } = useNuxtApp()
        await $api(`/tasks/${taskId.value}/labels`, {
            method: 'PUT',
             body: { label_ids: labelIds }
        })
    } catch (error) {
        // Revert on error would be ideal, but for now just toast
        console.error(error)
    }
}

const handleStatusChange = async (newStatus: string) => {
    task.value.status = newStatus
    updateTask({ status: newStatus })
}

const getInitials = (name: string | undefined) => {
  if (!name) return 'U'
  return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2)
}

const fetchTask = async () => {
  isLoading.value = true
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: any }>(`/tasks/${taskId.value}`)
    task.value = response.data
    // Map labels to IDs for picker
    if (task.value.labels) {
        task.value.label_ids = task.value.labels.map((l: any) => l.id)
    }
    // Map assignees to IDs
    if (task.value.assignees) {
        task.value.assignee_ids = task.value.assignees.map((u: any) => u.id)
    } else if (task.value.assignee) {
         task.value.assignee_ids = [task.value.assignee.id]
    } else {
        task.value.assignee_ids = []
    }
    
    // Ensure checklist is an array
    if (!task.value.checklist) {
        task.value.checklist = []
    }
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
    handleStatusChange('in_progress')
    const { $api } = useNuxtApp()
    await $api(`/tasks/${taskId.value}/start`, { method: 'POST' })
}

const handleComplete = async () => {
    handleStatusChange('completed')
     const { $api } = useNuxtApp()
    await $api(`/tasks/${taskId.value}/complete`, { method: 'POST' })
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

const handlePriorityChange = (priority: string) => {
    task.value.priority = priority
    updateTask({ priority })
}

const addChecklistItem = () => {
    if (!checklistItem.value.trim()) return
    
    const newItem = {
        id: crypto.randomUUID(),
        text: checklistItem.value,
        done: false
    }
    
    const newChecklist = [...(task.value.checklist || []), newItem]
    task.value.checklist = newChecklist
    checklistItem.value = ''
    
    // Auto update progress
    updateChecklistProgress()
}

const toggleChecklistItem = (index: number) => {
    task.value.checklist[index].done = !task.value.checklist[index].done
    updateChecklistProgress()
}

const removeChecklistItem = (index: number) => {
    const newChecklist = [...task.value.checklist]
    newChecklist.splice(index, 1)
    task.value.checklist = newChecklist
    updateChecklistProgress()
}

const updateChecklistProgress = () => {
    let progress = 0
    if (task.value.checklist.length > 0) {
        const completed = task.value.checklist.filter((i: any) => i.done).length
        progress = Math.round((completed / task.value.checklist.length) * 100)
    }
    
    task.value.progress = progress
    updateTask({ 
        checklist: task.value.checklist,
        progress: progress
    })
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
    <!-- Header with Back button and Save indicator -->
    <div class="flex items-center justify-between mb-6">
      <NuxtLink
        to="/tasks"
        class="inline-flex items-center text-muted-foreground hover:text-foreground"
      >
        <ArrowLeft class="w-4 h-4 mr-2" />
        {{ t('task_detail.back_to_tasks') }}
      </NuxtLink>
      
      <!-- Auto-save indicator -->
      <div class="flex items-center gap-2 text-sm">
        <Transition name="fade" mode="out-in">
          <div v-if="isSaving" class="flex items-center gap-2 text-muted-foreground">
            <Loader2 class="w-4 h-4 animate-spin" />
            <span>{{ t('common.saving') }}...</span>
          </div>
          <div v-else-if="lastSaved" class="flex items-center gap-2 text-green-600">
            <Check class="w-4 h-4" />
            <span>{{ t('common.saved') }}</span>
          </div>
        </Transition>
      </div>
    </div>

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
              <div class="flex-1 space-y-2">
                <!-- Editable Title -->
                <Input 
                   v-model="task.title" 
                   class="text-2xl font-semibold h-auto px-2 py-1 -ml-2 border-transparent hover:border-input focus:border-input bg-transparent"
                />
                
                <p class="text-muted-foreground text-sm flex items-center gap-2">
                  <span>{{ t('task_detail.created_at') }} {{ formatDateTime(task.created_at) }}</span>
                  <span>•</span>
                  <span>{{ task.reporter?.name }}</span>
                </p>
              </div>
              
              <div class="flex flex-col items-end gap-2">
                 <!-- Priority Badge (Editable) -->
                 <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <div 
                            class="flex items-center gap-2 px-3 py-1 bg-muted rounded-full cursor-pointer hover:bg-muted/80 transition-colors"
                            :title="t('tasks.priority.label')"
                        >
                            <div class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: priorityColor }" />
                            <span class="text-sm font-medium capitalize">{{ t(`tasks.priority.${task.priority || 'medium'}`) }}</span>
                        </div>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem 
                            v-for="opt in priorityOptions" 
                            :key="opt.value"
                            @click="handlePriorityChange(opt.value)"
                            class="flex items-center gap-2 cursor-pointer"
                        >
                             <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: priorityColors[opt.value] }" />
                             <span>{{ opt.label }}</span>
                             <Check v-if="task.priority === opt.value" class="w-4 h-4 ml-auto" />
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                 </DropdownMenu>

                 <!-- Status Badge Picker -->
                 <StatusBadgePicker 
                    :model-value="task.status" 
                    :options="statusOptions"
                    @update:model-value="handleStatusChange"
                 />
                 
                 <!-- Editable Due Date (Simple native picker for now) -->
                 <div class="flex items-center gap-1 text-sm text-muted-foreground">
                    <Calendar class="w-3 h-3" />
                    <input 
                        v-model="task.due_date" 
                        type="date" 
                        class="bg-transparent border-none p-0 h-auto focus:ring-0 text-xs w-24 text-right" 
                    />
                 </div>
              </div>
            </div>
          </CardHeader>

          <CardContent>
             <!-- Editable Description -->
            <div class="prose dark:prose-invert max-w-none">
              <Textarea 
                v-model="task.description" 
                :placeholder="t('task_detail.no_description')"
                class="min-h-[150px] resize-y"
              />
            </div>


            <!-- Verifiable Progress (Checklist) -->
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-medium flex items-center gap-2">
                        <ListTodo class="w-4 h-4 text-muted-foreground" />
                        {{ t('task_detail.checklist') }}
                    </h3>
                    <span class="text-xs text-muted-foreground font-medium" v-if="task.checklist?.length">
                        {{ checklistProgress }}%
                    </span>
                </div>
                
                <div class="space-y-4">
                    <!-- Progress Bar -->
                     <Progress v-if="task.checklist?.length" :model-value="checklistProgress" class="h-2" />

                    <!-- List Items -->
                    <div class="space-y-2">
                        <div 
                            v-for="(item, index) in task.checklist" 
                            :key="item.id || index"
                            class="group flex items-start gap-3 p-2 rounded-md hover:bg-muted/50 transition-colors"
                        >
                            <button 
                                @click="toggleChecklistItem(index as number)"
                                class="mt-0.5 text-muted-foreground hover:text-primary transition-colors focus:outline-none"
                            >
                                <CheckCircle2 v-if="item.done" class="w-5 h-5 text-green-500" />
                                <Circle v-else class="w-5 h-5" />
                            </button>
                            
                            <span 
                                class="flex-1 text-sm leading-relaxed pt-0.5"
                                :class="{ 'line-through text-muted-foreground': item.done }"
                            >
                                {{ item.text }}
                            </span>
                            
                            <button 
                                @click="removeChecklistItem(index as number)"
                                class="text-muted-foreground hover:text-destructive opacity-0 group-hover:opacity-100 transition-opacity p-1"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <!-- Add Item Input -->
                    <div class="flex items-center gap-2 mt-2">
                         <Plus class="w-4 h-4 text-muted-foreground" />
                         <input 
                            v-model="checklistItem"
                            @keydown.enter.prevent="addChecklistItem"
                            :placeholder="t('task_detail.add_checklist_item')"
                            class="flex-1 bg-transparent border-none focus:ring-0 text-sm h-9 p-0 placeholder:text-muted-foreground"
                         />
                         <Button 
                            v-if="checklistItem" 
                            size="sm" 
                            variant="ghost" 
                            @click="addChecklistItem"
                            class="h-7 px-2"
                        >
                            {{ t('common.add') }}
                         </Button>
                    </div>
                </div>
            </div>
          </CardContent>

          <CardFooter class="border-t pt-4 flex justify-between items-center">
            <div class="flex gap-2 text-xs text-muted-foreground">
               <span v-if="task.updated_at">{{ t('common.updated') }}: {{ formatDateTime(task.updated_at) }}</span>
            </div>
            
            <div class="flex gap-2">
              <Button v-if="task.status === 'pending'" @click="handleStart" size="sm">
                <Play class="w-4 h-4 mr-2" />
                {{ t('task_detail.start_task') }}
              </Button>
              <Button
                v-if="task.status === 'in_progress'"
                variant="default"
                class="bg-green-600 hover:bg-green-700"
                @click="handleComplete"
                size="sm"
              >
                <Check class="w-4 h-4 mr-2" />
                {{ t('task_detail.mark_complete') }}
              </Button>
            </div>
          </CardFooter>
        </Card>

        <!-- Comments Section -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2 text-lg">
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
                size="sm"
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
            <CardTitle class="text-lg">{{ t('task_detail.details') }}</CardTitle>
          </CardHeader>
          <CardContent>
            <dl class="space-y-6">
              <!-- Assignee -->
              <div>
                <dt class="text-sm font-medium text-muted-foreground mb-2 flex items-center gap-1">
                    <User class="w-3 h-3" />
                    {{ t('task_detail.assignee') }}
                </dt>
                <dd>
                   <UserSelect 
                      :model-value="task.assignee_ids || []" 
                      @update:model-value="(val: any) => handleAssigneesChange(val)"
                      :multiple="true"
                      :placeholder="t('task_detail.unassigned')" 
                   />
                </dd>
              </div>

              <!-- Labels -->
              <div>
                <dt class="text-sm font-medium text-muted-foreground mb-2 flex items-center gap-1">
                    <Tags class="w-3 h-3" />
                    {{ t('tasks.form.labels') }}
                </dt>
                <dd>
                    <LabelPicker 
                        :model-value="task.label_ids"
                        @update:model-value="handleLabelsChange"
                    />
                </dd>
              </div>

              <div v-if="task.department">
                <dt class="text-sm font-medium text-muted-foreground mb-1">
                  {{ t('users.department') }}
                </dt>
                <dd class="text-sm">{{ task.department.name }}</dd>
              </div>
            </dl>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
