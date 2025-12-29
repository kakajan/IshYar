<script setup lang="ts">
import { Card, CardContent } from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Badge } from '~/components/ui/badge'
import { Checkbox } from '~/components/ui/checkbox'
import { Label } from '~/components/ui/label'
import { Textarea } from '~/components/ui/textarea'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '~/components/ui/dialog'
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from '~/components/ui/select'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
} from '~/components/ui/dropdown-menu'
import UserSelect from '~/components/tasks/UserSelect.vue'
import LabelPicker from '~/components/tasks/LabelPicker.vue'
import {
  Plus,
  Search,
  ClipboardList,
  MoreVertical,
  Pencil,
  Eye,
  Trash2,
  Loader2,
} from 'lucide-vue-next'

definePageMeta({
  layout: 'default',
})

const { $api } = useNuxtApp()
const { t } = useI18n()
const toast = useToast()

// State
const tasks = ref<Task[]>([])
const isLoading = ref(true)
const isCreateModalOpen = ref(false)
const selectedStatus = ref<string>('all')
const selectedPriority = ref<string>('all')
const searchQuery = ref('')

// Filters (computed for i18n reactivity)
const statusOptions = computed(() => [
  { label: t('common.all'), value: 'all' },
  { label: t('status.pending'), value: 'pending' },
  { label: t('status.in_progress'), value: 'in_progress' },
  { label: t('status.completed'), value: 'completed' },
  { label: t('status.on_hold'), value: 'on_hold' },
])

const priorityOptions = computed(() => [
  { label: t('common.all'), value: 'all' },
  { label: t('priority.low'), value: 'low' },
  { label: t('priority.medium'), value: 'medium' },
  { label: t('priority.high'), value: 'high' },
  { label: t('priority.urgent'), value: 'urgent' },
])

// Fetch tasks
const fetchTasks = async () => {
  isLoading.value = true
  try {
    const response = (await $api('/tasks', {
      query: {
        status:
          selectedStatus.value === 'all'
            ? undefined
            : selectedStatus.value || undefined,
        priority:
          selectedPriority.value === 'all'
            ? undefined
            : selectedPriority.value || undefined,
        search: searchQuery.value || undefined,
      },
    })) as { data: { data: Task[] } }
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
  assignee_ids: [] as string[],
  label_ids: [],
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
    assignee_ids: [],
    label_ids: [],
  })
}

// Colors
type BadgeVariant =
  | 'default'
  | 'secondary'
  | 'destructive'
  | 'outline'
  | 'success'
  | 'warning'
  | 'info'

const priorityVariant = (priority: string): BadgeVariant => {
  const variants: Record<string, BadgeVariant> = {
    low: 'secondary',
    medium: 'warning',
    high: 'warning',
    urgent: 'destructive',
  }
  return variants[priority] || 'secondary'
}

const statusVariant = (status: string): BadgeVariant => {
  const variants: Record<string, BadgeVariant> = {
    pending: 'secondary',
    in_progress: 'info',
    completed: 'success',
    on_hold: 'warning',
    cancelled: 'destructive',
  }
  return variants[status] || 'secondary'
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
  assignees?: { id: string, name: string, avatar_url?: string }[]
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-foreground">
          {{ $t('tasks.title') }}
        </h1>
        <p class="mt-1 text-muted-foreground">
          {{ $t('tasks.description') }}
        </p>
      </div>
      
      <div class="flex items-center gap-3">
        <!-- View Switcher -->
        <div class="view-switcher-container">
          <NuxtLink 
            to="/tasks/kanban" 
            class="view-switcher-btn"
            :class="{ 'active': $route.path === '/tasks/kanban' }"
          >
            <Icon name="heroicons:view-columns" class="w-5 h-5" />
          </NuxtLink>
          <button 
            class="view-switcher-btn"
            :class="{ 'active': $route.path === '/tasks' || $route.path === '/tasks/' }"
          >
            <Icon name="heroicons:list-bullet" class="w-5 h-5" />
          </button>
        </div>
        
        <!-- Refresh Button -->
        <Button variant="outline" size="sm" @click="fetchTasks" :disabled="isLoading">
          <Icon 
            name="heroicons:arrow-path" 
            class="w-4 h-4" 
            :class="{ 'animate-spin': isLoading }" 
          />
        </Button>
        
        <Button @click="isCreateModalOpen = true">
          <Plus class="w-4 h-4 mr-2" />
          {{ $t('tasks.create') }}
        </Button>
      </div>
    </div>

    <!-- Filters -->
    <Card>
      <CardContent class="p-4">
        <div class="flex flex-wrap gap-4">
          <div class="relative w-64">
            <Search
              class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"
            />
            <Input
              v-model="searchQuery"
              :placeholder="$t('common.search')"
              class="pl-9"
            />
          </div>
          <Select v-model="selectedStatus">
            <SelectTrigger class="w-40">
              <SelectValue :placeholder="$t('common.status')" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="opt in statusOptions"
                :key="opt.value"
                :value="opt.value"
              >
                {{ opt.label }}
              </SelectItem>
            </SelectContent>
          </Select>
          <Select v-model="selectedPriority">
            <SelectTrigger class="w-40">
              <SelectValue :placeholder="$t('tasks.priority.label')" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="opt in priorityOptions"
                :key="opt.value"
                :value="opt.value"
              >
                {{ opt.label }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
      </CardContent>
    </Card>

    <!-- Tasks list -->
    <Card>
      <CardContent class="p-4">
        <div v-if="isLoading" class="flex justify-center py-8">
          <Loader2 class="w-8 h-8 animate-spin text-primary" />
        </div>

        <div v-else-if="tasks.length === 0" class="text-center py-12">
          <ClipboardList class="w-12 h-12 text-muted-foreground mx-auto" />
          <h3 class="mt-4 text-lg font-medium text-foreground">
            {{ $t('tasks.no_tasks') }}
          </h3>
          <p class="mt-2 text-muted-foreground">
            {{ $t('tasks.get_started') }}
          </p>
          <Button class="mt-4" @click="isCreateModalOpen = true">
            {{ $t('tasks.create') }}
          </Button>
        </div>

        <div v-else class="divide-y divide-border">
          <div
            v-for="task in tasks"
            :key="task.id"
            class="py-4 first:pt-0 last:pb-0 flex items-center justify-between hover:bg-muted/50 -mx-4 px-4 rounded-lg transition-colors cursor-pointer"
            @click="navigateTo(`/tasks/${task.id}`)"
          >
            <div class="flex items-center gap-4 flex-1">
              <Checkbox :checked="task.status === 'completed'" />
              <div class="flex-1 min-w-0">
                <p class="font-medium text-foreground truncate">
                  {{ task.title }}
                </p>
                <div
                  class="flex items-center gap-3 mt-1 text-sm text-muted-foreground"
                >
                  <div class="flex -space-x-2 overflow-hidden" v-if="task.assignees && task.assignees.length">
                    <div 
                      v-for="user in task.assignees.slice(0, 3)" 
                      :key="user.id"
                      class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-slate-900 bg-gray-200 flex items-center justify-center text-[10px]"
                    >
                      <img v-if="user.avatar_url" :src="user.avatar_url" class="h-full w-full rounded-full object-cover" />
                       <span v-else>{{ user.name.charAt(0) }}</span>
                    </div>
                    <div v-if="task.assignees.length > 3" class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-slate-900 bg-gray-100 flex items-center justify-center text-[8px]">
                      +{{ task.assignees.length - 3 }}
                    </div>
                  </div>
                  <span v-else-if="task.assignee">{{ task.assignee.name }}</span>
                  <span v-if="task.due_date"
                    >{{ $t('tasks.due_date') }}: {{ task.due_date }}</span
                  >
                </div>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <!-- Progress -->
              <div v-if="task.progress > 0" class="w-20">
                <div class="h-2 bg-muted rounded-full">
                  <div
                    class="h-full bg-primary rounded-full transition-all"
                    :style="{ width: `${task.progress}%` }"
                  />
                </div>
              </div>

              <Badge :variant="priorityVariant(task.priority)">
                {{ task.priority }}
              </Badge>
              <Badge :variant="statusVariant(task.status)">
                {{ task.status.replace('_', ' ') }}
              </Badge>

              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <Button variant="ghost" size="sm" @click.stop>
                    <MoreVertical class="w-4 h-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                  <DropdownMenuItem
                    @select="navigateTo(`/tasks/${task.id}?edit=true`)"
                  >
                    <Pencil class="w-4 h-4 mr-2" />
                    {{ $t('common.edit') }}
                  </DropdownMenuItem>
                  <DropdownMenuItem @select="navigateTo(`/tasks/${task.id}`)">
                    <Eye class="w-4 h-4 mr-2" />
                    {{ $t('tasks.view') }}
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem class="text-destructive">
                    <Trash2 class="w-4 h-4 mr-2" />
                    {{ $t('common.delete') }}
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Create Task Modal -->
    <Dialog v-model:open="isCreateModalOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ $t('tasks.create') }}</DialogTitle>
        </DialogHeader>

        <form class="space-y-4" @submit.prevent="createTask">
          <div class="space-y-2">
            <Label for="title">{{ $t('tasks.form.title') }} *</Label>
            <Input
              id="title"
              v-model="newTask.title"
              :placeholder="$t('tasks.form.title_placeholder')"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="description">{{ $t('tasks.form.description') }}</Label>
            <Textarea
              id="description"
              v-model="newTask.description"
              :placeholder="$t('tasks.form.description_placeholder')"
              :rows="3"
            />
            <div class="space-y-2">
            <Label>{{ $t('tasks.form.assignee') }}</Label>
            <UserSelect v-model="newTask.assignee_ids" :multiple="true" :placeholder="$t('tasks.form.select_assignee')" />
          </div>

          <div class="space-y-2">
            <Label>{{ $t('tasks.form.labels') }}</Label>
            <LabelPicker v-model="newTask.label_ids" />
          </div>
        </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label>{{ $t('tasks.form.type') }}</Label>
              <Select v-model="newTask.type">
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="situational">{{
                    $t('tasks.situational')
                  }}</SelectItem>
                  <SelectItem value="routine">{{
                    $t('tasks.routine')
                  }}</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="space-y-2">
              <Label>{{ $t('tasks.priority.label') }}</Label>
              <Select v-model="newTask.priority">
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="low">{{ $t('priority.low') }}</SelectItem>
                  <SelectItem value="medium">{{
                    $t('priority.medium')
                  }}</SelectItem>
                  <SelectItem value="high">{{
                    $t('priority.high')
                  }}</SelectItem>
                  <SelectItem value="urgent">{{
                    $t('priority.urgent')
                  }}</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div class="space-y-2">
            <Label for="due_date">{{ $t('tasks.due_date') }}</Label>
            <Input id="due_date" v-model="newTask.due_date" type="date" />
          </div>

          <DialogFooter>
            <Button
              variant="outline"
              type="button"
              @click="isCreateModalOpen = false"
            >
              {{ $t('common.cancel') }}
            </Button>
            <Button type="submit">{{ $t('common.create') }}</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>

<style scoped>
/* View switcher custom styles */
.view-switcher-container {
  display: flex;
  align-items: center;
  gap: 2px;
  background: hsl(var(--muted));
  border-radius: 0.5rem;
  padding: 2px;
}

.view-switcher-btn {
  padding: 6px 10px;
  border-radius: 0.375rem;
  transition: all 150ms ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.view-switcher-btn:hover {
  background: hsl(var(--background));
}

.view-switcher-btn.active {
  background: hsl(var(--background));
  box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}
</style>
