# IshYar Technical Architecture

> **Document Type**: System Architecture & Technical Guidelines  
> **Version**: 1.0.0  
> **Last Updated**: February 1, 2026

---

## System Overview

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              CLIENT LAYER                                        │
├─────────────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐                  │
│  │   Web Browser   │  │   Mobile PWA    │  │   Desktop PWA   │                  │
│  │   (Chrome, etc) │  │   (iOS/Android) │  │   (Windows/Mac) │                  │
│  └────────┬────────┘  └────────┬────────┘  └────────┬────────┘                  │
│           │                    │                    │                            │
│           └────────────────────┼────────────────────┘                            │
│                                │                                                 │
│  ┌─────────────────────────────▼─────────────────────────────┐                  │
│  │                    Nuxt 4 SPA Application                  │                  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │                  │
│  │  │   Vue 3     │ │   Pinia     │ │  Vue Router │          │                  │
│  │  │ Composition │ │   Stores    │ │  Navigation │          │                  │
│  │  └─────────────┘ └─────────────┘ └─────────────┘          │                  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │                  │
│  │  │ Shadcn Vue  │ │  VeeValidate│ │  ApexCharts │          │                  │
│  │  │ Components  │ │  + Zod      │ │   Charts    │          │                  │
│  │  └─────────────┘ └─────────────┘ └─────────────┘          │                  │
│  └─────────────────────────────┬─────────────────────────────┘                  │
└────────────────────────────────┼────────────────────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │     CDN (Cloudflare)    │
                    │    Static + API Proxy   │
                    └────────────┬────────────┘
                                 │
┌────────────────────────────────┼────────────────────────────────────────────────┐
│                          API GATEWAY LAYER                                       │
├────────────────────────────────┼────────────────────────────────────────────────┤
│                    ┌───────────▼───────────┐                                    │
│                    │    Load Balancer      │                                    │
│                    │   (nginx / Traefik)   │                                    │
│                    └───────────┬───────────┘                                    │
│                                │                                                 │
│              ┌─────────────────┼─────────────────┐                              │
│              │                 │                 │                              │
│    ┌─────────▼────────┐ ┌──────▼──────┐ ┌───────▼───────┐                      │
│    │   REST API       │ │  WebSocket  │ │   Filament    │                      │
│    │  /api/v1/*       │ │   Server    │ │  Admin Panel  │                      │
│    │  (Laravel)       │ │  (Reverb)   │ │  /admin/*     │                      │
│    └─────────┬────────┘ └──────┬──────┘ └───────┬───────┘                      │
└──────────────┼─────────────────┼────────────────┼───────────────────────────────┘
               │                 │                │
               └─────────────────┼────────────────┘
                                 │
┌────────────────────────────────┼────────────────────────────────────────────────┐
│                        APPLICATION LAYER                                         │
├────────────────────────────────┼────────────────────────────────────────────────┤
│                    ┌───────────▼───────────┐                                    │
│                    │    Laravel 12+        │                                    │
│                    │    Application        │                                    │
│                    └───────────┬───────────┘                                    │
│                                │                                                 │
│    ┌─────────────┬─────────────┼─────────────┬─────────────┐                   │
│    │             │             │             │             │                   │
│  ┌─▼───────┐ ┌───▼────┐ ┌──────▼─────┐ ┌─────▼────┐ ┌──────▼─────┐            │
│  │ Actions │ │Services│ │   Models   │ │  Events  │ │    Jobs    │            │
│  │         │ │        │ │ (Eloquent) │ │ Listeners│ │   Queues   │            │
│  └─────────┘ └────────┘ └────────────┘ └──────────┘ └────────────┘            │
└────────────────────────────────┬────────────────────────────────────────────────┘
                                 │
┌────────────────────────────────┼────────────────────────────────────────────────┐
│                        DATA LAYER                                                │
├────────────────────────────────┼────────────────────────────────────────────────┤
│    ┌─────────────┬─────────────┼─────────────┬─────────────┐                   │
│    │             │             │             │             │                   │
│  ┌─▼───────────┐ ┌─▼─────────┐ ┌─▼──────────┐ ┌─▼──────────┐                   │
│  │ PostgreSQL  │ │   Redis   │ │Meilisearch │ │    S3      │                   │
│  │   16+       │ │     7+    │ │            │ │  Storage   │                   │
│  │  Primary DB │ │Cache/Queue│ │Full-Text   │ │   Files    │                   │
│  └─────────────┘ └───────────┘ └────────────┘ └────────────┘                   │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 1. Frontend Architecture

### 1.1 Directory Structure

```
frontend/
├── app/
│   └── app.vue                    # Root component
├── assets/
│   └── css/
│       ├── main.css              # Global styles
│       └── tokens.css            # Design tokens
├── components/
│   ├── ui/                       # Shadcn Vue components
│   │   ├── button/
│   │   ├── card/
│   │   └── ...
│   ├── tasks/                    # Feature components
│   │   ├── TaskCard.vue
│   │   ├── TaskList.vue
│   │   ├── TaskForm.vue
│   │   └── TaskKanban.vue
│   ├── crm/                      # CRM components
│   │   ├── ContactCard.vue
│   │   ├── DealCard.vue
│   │   └── PipelineBoard.vue
│   └── common/                   # Shared components
│       ├── AppHeader.vue
│       ├── AppSidebar.vue
│       ├── ConfirmDialog.vue
│       └── EmptyState.vue
├── composables/
│   ├── useApi.ts                 # API client
│   ├── useAuth.ts                # Authentication
│   ├── useWebSocket.ts           # Real-time
│   ├── useForm.ts                # Form handling
│   ├── useNotification.ts        # Toast notifications
│   ├── useTheme.ts               # Theme management
│   ├── useDirection.ts           # RTL support
│   └── usePagination.ts          # Pagination logic
├── layouts/
│   ├── default.vue               # Main layout
│   ├── auth.vue                  # Auth pages
│   └── empty.vue                 # Blank layout
├── middleware/
│   ├── auth.ts                   # Auth guard
│   └── guest.ts                  # Guest only
├── pages/
│   ├── index.vue                 # Dashboard
│   ├── login.vue
│   ├── tasks/
│   │   ├── index.vue             # Task list
│   │   ├── [id].vue              # Task detail
│   │   └── kanban.vue            # Kanban board
│   ├── crm/
│   │   ├── contacts/
│   │   ├── deals/
│   │   └── pipeline.vue
│   └── settings/
├── plugins/
│   ├── api.ts                    # API plugin
│   └── toast.ts                  # Toast plugin
├── schemas/
│   ├── auth.ts                   # Zod schemas
│   ├── task.ts
│   └── crm.ts
├── stores/
│   ├── auth.ts                   # Auth store
│   ├── task.ts                   # Task store
│   ├── notification.ts           # Notification store
│   └── ui.ts                     # UI state
├── types/
│   ├── api.ts                    # API types
│   ├── models.ts                 # Data models
│   └── index.ts                  # Re-exports
└── utils/
    ├── format.ts                 # Formatters
    ├── validation.ts             # Validators
    └── helpers.ts                # Utilities
```

### 1.2 API Client Pattern

```typescript
// composables/useApi.ts
import { ofetch, type FetchOptions } from 'ofetch'

interface ApiResponse<T> {
  data: T
  meta?: {
    current_page?: number
    total?: number
    per_page?: number
  }
}

interface ApiError {
  type: string
  title: string
  status: number
  detail: string
  errors?: Record<string, string[]>
}

export function useApi() {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()
  const toast = useToast()
  
  const api = ofetch.create({
    baseURL: config.public.apiBase,
    
    async onRequest({ options }) {
      // Add auth token
      if (authStore.token) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${authStore.token}`,
        }
      }
      
      // Add locale header
      const { locale } = useI18n()
      options.headers = {
        ...options.headers,
        'Accept-Language': locale.value,
      }
    },
    
    async onResponseError({ response }) {
      const error = response._data as ApiError
      
      // Handle 401 - Token expired
      if (response.status === 401) {
        const refreshed = await authStore.refreshToken()
        if (!refreshed) {
          await authStore.logout()
          navigateTo('/login')
        }
        return
      }
      
      // Handle 422 - Validation errors
      if (response.status === 422) {
        // Errors will be handled by form
        throw error
      }
      
      // Handle other errors
      toast.error(error.detail || 'An error occurred')
      throw error
    },
  })
  
  return {
    get: <T>(url: string, options?: FetchOptions) => 
      api<ApiResponse<T>>(url, { ...options, method: 'GET' }),
      
    post: <T>(url: string, body?: any, options?: FetchOptions) =>
      api<ApiResponse<T>>(url, { ...options, method: 'POST', body }),
      
    put: <T>(url: string, body?: any, options?: FetchOptions) =>
      api<ApiResponse<T>>(url, { ...options, method: 'PUT', body }),
      
    delete: <T>(url: string, options?: FetchOptions) =>
      api<ApiResponse<T>>(url, { ...options, method: 'DELETE' }),
  }
}
```

### 1.3 Store Pattern (Pinia)

```typescript
// stores/task.ts
import { defineStore } from 'pinia'
import type { Task, TaskFilters, CreateTaskInput } from '~/types'

export const useTaskStore = defineStore('task', () => {
  // State
  const tasks = ref<Task[]>([])
  const currentTask = ref<Task | null>(null)
  const loading = ref(false)
  const filters = ref<TaskFilters>({})
  const pagination = ref({
    page: 1,
    perPage: 20,
    total: 0,
  })
  
  // Composables
  const api = useApi()
  const { subscribe, publish } = useWebSocket()
  
  // Getters
  const tasksByStatus = computed(() => {
    const grouped: Record<string, Task[]> = {}
    for (const task of tasks.value) {
      const status = task.status
      if (!grouped[status]) grouped[status] = []
      grouped[status].push(task)
    }
    return grouped
  })
  
  const overdueCount = computed(() =>
    tasks.value.filter(t => 
      t.status !== 'completed' && 
      new Date(t.due_date) < new Date()
    ).length
  )
  
  // Actions
  async function fetchTasks(params?: TaskFilters) {
    loading.value = true
    try {
      const { data, meta } = await api.get<Task[]>('/tasks', {
        query: { ...filters.value, ...params }
      })
      tasks.value = data
      if (meta) {
        pagination.value = {
          page: meta.current_page!,
          perPage: meta.per_page!,
          total: meta.total!,
        }
      }
    } finally {
      loading.value = false
    }
  }
  
  async function createTask(input: CreateTaskInput) {
    const { data } = await api.post<Task>('/tasks', input)
    tasks.value.unshift(data)
    return data
  }
  
  async function updateTask(id: string, input: Partial<Task>) {
    // Optimistic update
    const index = tasks.value.findIndex(t => t.id === id)
    const oldTask = tasks.value[index]
    if (index !== -1) {
      tasks.value[index] = { ...oldTask, ...input }
    }
    
    try {
      const { data } = await api.put<Task>(`/tasks/${id}`, input)
      if (index !== -1) {
        tasks.value[index] = data
      }
      return data
    } catch (error) {
      // Rollback on error
      if (index !== -1) {
        tasks.value[index] = oldTask
      }
      throw error
    }
  }
  
  async function deleteTask(id: string) {
    await api.delete(`/tasks/${id}`)
    tasks.value = tasks.value.filter(t => t.id !== id)
  }
  
  async function moveTask(taskId: string, newStatus: string) {
    return updateTask(taskId, { status: newStatus })
  }
  
  // Real-time subscriptions
  function initRealtime(userId: string) {
    subscribe(`private-user.${userId}`, {
      'task.created': (task: Task) => {
        if (!tasks.value.find(t => t.id === task.id)) {
          tasks.value.unshift(task)
        }
      },
      'task.updated': (task: Task) => {
        const index = tasks.value.findIndex(t => t.id === task.id)
        if (index !== -1) {
          tasks.value[index] = task
        }
      },
      'task.deleted': (taskId: string) => {
        tasks.value = tasks.value.filter(t => t.id !== taskId)
      },
    })
  }
  
  return {
    // State
    tasks,
    currentTask,
    loading,
    filters,
    pagination,
    
    // Getters
    tasksByStatus,
    overdueCount,
    
    // Actions
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
    moveTask,
    initRealtime,
  }
})
```

### 1.4 Form Pattern (VeeValidate + Zod)

```typescript
// schemas/task.ts
import { z } from 'zod'

export const createTaskSchema = z.object({
  title: z.string()
    .min(3, 'Title must be at least 3 characters')
    .max(255, 'Title must be less than 255 characters'),
  description: z.string().optional(),
  priority: z.enum(['low', 'medium', 'high', 'critical']),
  status: z.enum(['not_started', 'in_progress', 'pending_review', 'completed'])
    .default('not_started'),
  due_date: z.string().datetime().optional(),
  assignees: z.array(z.string().uuid()).min(1, 'At least one assignee required'),
  labels: z.array(z.string().uuid()).optional(),
})

export type CreateTaskInput = z.infer<typeof createTaskSchema>
```

```vue
<!-- components/tasks/TaskForm.vue -->
<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { createTaskSchema, type CreateTaskInput } from '~/schemas/task'

const props = defineProps<{
  task?: Task
}>()

const emit = defineEmits<{
  submit: [data: CreateTaskInput]
  cancel: []
}>()

const taskStore = useTaskStore()
const toast = useToast()

const { handleSubmit, errors, isSubmitting, setErrors } = useForm<CreateTaskInput>({
  validationSchema: toTypedSchema(createTaskSchema),
  initialValues: props.task ?? {
    title: '',
    priority: 'medium',
    status: 'not_started',
    assignees: [],
  },
})

const onSubmit = handleSubmit(async (values) => {
  try {
    if (props.task) {
      await taskStore.updateTask(props.task.id, values)
      toast.success('Task updated successfully')
    } else {
      await taskStore.createTask(values)
      toast.success('Task created successfully')
    }
    emit('submit', values)
  } catch (error: any) {
    // Handle validation errors from API
    if (error.status === 422 && error.errors) {
      setErrors(error.errors)
    }
  }
})
</script>

<template>
  <form @submit="onSubmit" class="space-y-6">
    <FormField name="title" v-slot="{ field }">
      <FormItem>
        <FormLabel>Title</FormLabel>
        <FormControl>
          <Input v-bind="field" placeholder="Task title" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    
    <FormField name="description" v-slot="{ field }">
      <FormItem>
        <FormLabel>Description</FormLabel>
        <FormControl>
          <Textarea v-bind="field" placeholder="Task description" rows="4" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    
    <div class="grid grid-cols-2 gap-4">
      <FormField name="priority" v-slot="{ field }">
        <FormItem>
          <FormLabel>Priority</FormLabel>
          <Select v-bind="field">
            <SelectTrigger>
              <SelectValue placeholder="Select priority" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="low">Low</SelectItem>
              <SelectItem value="medium">Medium</SelectItem>
              <SelectItem value="high">High</SelectItem>
              <SelectItem value="critical">Critical</SelectItem>
            </SelectContent>
          </Select>
          <FormMessage />
        </FormItem>
      </FormField>
      
      <FormField name="due_date" v-slot="{ field }">
        <FormItem>
          <FormLabel>Due Date</FormLabel>
          <FormControl>
            <DatePicker v-bind="field" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>
    
    <FormField name="assignees" v-slot="{ field }">
      <FormItem>
        <FormLabel>Assignees</FormLabel>
        <FormControl>
          <UserSelect v-bind="field" multiple />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    
    <div class="flex justify-end gap-3">
      <Button type="button" variant="outline" @click="emit('cancel')">
        Cancel
      </Button>
      <Button type="submit" :disabled="isSubmitting">
        <Loader2 v-if="isSubmitting" class="w-4 h-4 mr-2 animate-spin" />
        {{ task ? 'Update' : 'Create' }} Task
      </Button>
    </div>
  </form>
</template>
```

---

## 2. Backend Architecture

### 2.1 Directory Structure

```
backend/
├── app/
│   ├── Actions/                   # Single-purpose action classes
│   │   ├── Task/
│   │   │   ├── CreateTaskAction.php
│   │   │   ├── UpdateTaskAction.php
│   │   │   ├── AssignTaskAction.php
│   │   │   └── CompleteTaskAction.php
│   │   └── User/
│   ├── Contracts/                 # Interfaces
│   │   ├── NotificationChannel.php
│   │   └── SearchableModel.php
│   ├── Data/                      # Spatie Laravel Data DTOs
│   │   ├── TaskData.php
│   │   ├── UserData.php
│   │   └── ResponseData.php
│   ├── Events/                    # Domain events
│   │   ├── TaskCreated.php
│   │   ├── TaskCompleted.php
│   │   └── TaskAssigned.php
│   ├── Exceptions/
│   │   └── Handler.php
│   ├── Filament/                  # Admin panel
│   │   ├── Resources/
│   │   └── Pages/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── V1/
│   │   │           ├── AuthController.php
│   │   │           ├── TaskController.php
│   │   │           ├── UserController.php
│   │   │           └── CRM/
│   │   ├── Middleware/
│   │   │   ├── ForceJsonResponse.php
│   │   │   └── LocaleMiddleware.php
│   │   ├── Requests/
│   │   │   ├── CreateTaskRequest.php
│   │   │   └── UpdateTaskRequest.php
│   │   └── Resources/
│   │       ├── TaskResource.php
│   │       └── UserResource.php
│   ├── Jobs/
│   │   ├── SendNotificationJob.php
│   │   ├── ProcessWebhookJob.php
│   │   └── GenerateReportJob.php
│   ├── Listeners/
│   │   ├── SendTaskNotification.php
│   │   └── UpdateAnalytics.php
│   ├── Models/
│   │   ├── Concerns/              # Traits
│   │   │   ├── HasTranslations.php
│   │   │   └── HasAuditLog.php
│   │   ├── Task.php
│   │   ├── User.php
│   │   └── Organization.php
│   ├── Notifications/
│   │   ├── TaskAssignedNotification.php
│   │   └── TaskCompletedNotification.php
│   ├── Observers/
│   │   └── TaskObserver.php
│   ├── Policies/
│   │   ├── TaskPolicy.php
│   │   └── UserPolicy.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── EventServiceProvider.php
│   ├── Rules/
│   │   └── TranslatableValue.php
│   ├── Services/
│   │   ├── NotificationService.php
│   │   ├── AnalyticsService.php
│   │   └── SearchService.php
│   └── ValueObjects/
│       └── TranslatableString.php
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php
│   ├── channels.php
│   └── console.php
└── tests/
    ├── Feature/
    └── Unit/
```

### 2.2 Action Class Pattern

```php
<?php
// app/Actions/Task/CreateTaskAction.php

namespace App\Actions\Task;

use App\Data\CreateTaskData;
use App\Events\TaskCreated;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class CreateTaskAction
{
    public function __construct(
        private readonly AssignTaskAction $assignAction,
    ) {}
    
    public function execute(CreateTaskData $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task = Task::create([
                'title' => $data->title,
                'description' => $data->description,
                'priority' => $data->priority,
                'status' => $data->status ?? 'not_started',
                'due_date' => $data->dueDate,
                'organization_id' => auth()->user()->organization_id,
                'created_by' => auth()->id(),
            ]);
            
            // Assign users
            if ($data->assignees) {
                $this->assignAction->execute($task, $data->assignees);
            }
            
            // Attach labels
            if ($data->labels) {
                $task->labels()->attach($data->labels);
            }
            
            // Fire event for listeners
            event(new TaskCreated($task));
            
            return $task->load(['assignees', 'labels', 'creator']);
        });
    }
}
```

### 2.3 Data Transfer Objects (Spatie Laravel Data)

```php
<?php
// app/Data/TaskData.php

namespace App\Data;

use App\Models\Task;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class TaskData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $priority,
        public readonly string $status,
        public readonly int $progress,
        public readonly ?string $dueDate,
        public readonly ?UserData $creator,
        /** @var UserData[] */
        public readonly array $assignees,
        /** @var LabelData[] */
        public readonly array $labels,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}
    
    public static function fromModel(Task $task): self
    {
        return new self(
            id: $task->id,
            title: $task->title,
            description: $task->description,
            priority: $task->priority,
            status: $task->status,
            progress: $task->progress,
            dueDate: $task->due_date?->toIso8601String(),
            creator: $task->creator ? UserData::from($task->creator) : null,
            assignees: UserData::collect($task->assignees)->all(),
            labels: LabelData::collect($task->labels)->all(),
            createdAt: $task->created_at->toIso8601String(),
            updatedAt: $task->updated_at->toIso8601String(),
        );
    }
}
```

### 2.4 API Controller Pattern

```php
<?php
// app/Http/Controllers/Api/V1/TaskController.php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Data\CreateTaskData;
use App\Data\TaskData;
use App\Data\UpdateTaskData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }
    
    public function index(Request $request): TaskCollection
    {
        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('priority'),
                AllowedFilter::scope('assigned_to'),
                AllowedFilter::scope('overdue'),
                AllowedFilter::scope('due_between'),
            ])
            ->allowedSorts(['created_at', 'due_date', 'priority', 'status'])
            ->allowedIncludes(['assignees', 'labels', 'creator'])
            ->defaultSort('-created_at')
            ->where('organization_id', auth()->user()->organization_id)
            ->paginate($request->input('per_page', 20));
        
        return new TaskCollection($tasks);
    }
    
    public function store(
        CreateTaskRequest $request,
        CreateTaskAction $action
    ): JsonResponse {
        $data = CreateTaskData::from($request->validated());
        $task = $action->execute($data);
        
        return response()->json([
            'data' => TaskData::from($task),
            'meta' => ['message' => __('tasks.created')],
        ], 201);
    }
    
    public function show(Task $task): JsonResponse
    {
        $task->load(['assignees', 'labels', 'creator', 'subtasks', 'comments']);
        
        return response()->json([
            'data' => TaskData::from($task),
        ]);
    }
    
    public function update(
        UpdateTaskRequest $request,
        Task $task,
        UpdateTaskAction $action
    ): JsonResponse {
        $data = UpdateTaskData::from($request->validated());
        $task = $action->execute($task, $data);
        
        return response()->json([
            'data' => TaskData::from($task),
            'meta' => ['message' => __('tasks.updated')],
        ]);
    }
    
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        
        return response()->json(null, 204);
    }
}
```

### 2.5 Broadcasting Events

```php
<?php
// app/Events/TaskCreated.php

namespace App\Events;

use App\Data\TaskData;
use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Task $task
    ) {}

    public function broadcastOn(): array
    {
        // Broadcast to assignees
        $channels = $this->task->assignees->map(
            fn ($user) => new PrivateChannel("user.{$user->id}")
        )->all();
        
        // Also broadcast to department channel
        $channels[] = new PrivateChannel(
            "department.{$this->task->department_id}"
        );
        
        return $channels;
    }
    
    public function broadcastAs(): string
    {
        return 'task.created';
    }
    
    public function broadcastWith(): array
    {
        return [
            'task' => TaskData::from($this->task->load(['assignees', 'labels'])),
        ];
    }
}
```

---

## 3. Database Optimization

### 3.1 Index Strategy

```sql
-- Performance-critical indexes

-- Tasks: Frequently filtered
CREATE INDEX idx_tasks_organization_status 
  ON tasks (organization_id, status) 
  WHERE deleted_at IS NULL;

CREATE INDEX idx_tasks_assignee_status 
  ON task_assignees (user_id, task_id);

CREATE INDEX idx_tasks_due_date 
  ON tasks (due_date) 
  WHERE status != 'completed' AND deleted_at IS NULL;

CREATE INDEX idx_tasks_priority_status 
  ON tasks (priority, status, organization_id) 
  WHERE deleted_at IS NULL;

-- Full-text search (if not using Meilisearch)
CREATE INDEX idx_tasks_search 
  ON tasks USING GIN (to_tsvector('english', title || ' ' || COALESCE(description, '')));

-- CRM: Contact lookups
CREATE INDEX idx_contacts_company 
  ON crm_contacts (company_id);

CREATE INDEX idx_contacts_owner 
  ON crm_contacts (owner_id);

CREATE INDEX idx_contacts_email 
  ON crm_contacts (email) 
  WHERE email IS NOT NULL;

-- CRM: Deal pipeline
CREATE INDEX idx_deals_pipeline_stage 
  ON crm_deals (pipeline_id, stage_id, status);

CREATE INDEX idx_deals_close_date 
  ON crm_deals (expected_close_date) 
  WHERE status = 'open';
```

### 3.2 Query Optimization Patterns

```php
<?php
// Efficient task loading with selected columns

// ❌ Bad: N+1 and full column load
$tasks = Task::all();
foreach ($tasks as $task) {
    echo $task->assignees->pluck('name');
}

// ✅ Good: Eager loading with column selection
$tasks = Task::query()
    ->select(['id', 'title', 'status', 'priority', 'due_date', 'progress'])
    ->with([
        'assignees:id,name,avatar',
        'labels:id,name,color'
    ])
    ->where('organization_id', $orgId)
    ->where('status', '!=', 'completed')
    ->orderBy('due_date')
    ->cursorPaginate(50);
```

---

## 4. Caching Strategy

### 4.1 Cache Layers

```php
<?php
// app/Services/CacheService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    // User-specific cache (permissions, preferences)
    public function userCache(int $userId): UserCacheManager
    {
        return new UserCacheManager($userId);
    }
    
    // Organization-specific cache (settings, departments)
    public function orgCache(int $orgId): OrgCacheManager
    {
        return new OrgCacheManager($orgId);
    }
    
    // Dashboard aggregations
    public function dashboard(int $orgId, string $key, callable $callback, int $ttl = 300)
    {
        return Cache::tags(['dashboard', "org:{$orgId}"])
            ->remember("dashboard:{$orgId}:{$key}", $ttl, $callback);
    }
    
    // Invalidate on data changes
    public function invalidateDashboard(int $orgId): void
    {
        Cache::tags(['dashboard', "org:{$orgId}"])->flush();
    }
}
```

### 4.2 Model Caching

```php
<?php
// app/Models/Concerns/CachesAttributes.php

trait CachesAttributes
{
    public static function bootCachesAttributes(): void
    {
        static::saved(fn ($model) => $model->forgetCache());
        static::deleted(fn ($model) => $model->forgetCache());
    }
    
    public function getCached(string $key, callable $callback, int $ttl = 3600)
    {
        $cacheKey = $this->getCacheKey($key);
        
        return Cache::remember($cacheKey, $ttl, $callback);
    }
    
    public function forgetCache(): void
    {
        Cache::forget($this->getCacheKey('*'));
    }
    
    protected function getCacheKey(string $suffix): string
    {
        return sprintf(
            '%s:%s:%s',
            $this->getTable(),
            $this->getKey(),
            $suffix
        );
    }
}
```

---

## 5. Security Architecture

### 5.1 Authentication Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                        Authentication Flow                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│   1. Login Request                                                   │
│   ┌────────────┐      POST /auth/login         ┌────────────┐       │
│   │   Client   │  ────────────────────────────>│   Server   │       │
│   │            │      {email, password}        │            │       │
│   └────────────┘                               └─────┬──────┘       │
│                                                      │               │
│   2. Validation & Token Generation                   │               │
│      • Validate credentials                          │               │
│      • Check rate limits                             │               │
│      • Generate JWT access token (15 min)            │               │
│      • Generate refresh token (7 days)               │               │
│                                                      │               │
│   3. Response                                        │               │
│   ┌────────────┐      {access_token, user}     ┌────┴───────┐       │
│   │   Client   │  <────────────────────────────│   Server   │       │
│   │            │      Set-Cookie: refresh_token│            │       │
│   └─────┬──────┘      (HTTP-only, Secure)      └────────────┘       │
│         │                                                            │
│   4. Store token in memory (not localStorage)                        │
│         │                                                            │
│   5. API Request with Bearer Token                                   │
│   ┌─────▼──────┐      Authorization: Bearer    ┌────────────┐       │
│   │   Client   │  ────────────────────────────>│   Server   │       │
│   │            │                               │            │       │
│   └────────────┘                               └────────────┘       │
│                                                                      │
│   6. Token Refresh (when access token expires)                       │
│   ┌────────────┐      POST /auth/refresh       ┌────────────┐       │
│   │   Client   │  ────────────────────────────>│   Server   │       │
│   │            │      Cookie: refresh_token    │            │       │
│   └────────────┘                               └────────────┘       │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### 5.2 Authorization Matrix

```php
<?php
// app/Policies/TaskPolicy.php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    // View any task in organization
    public function viewAny(User $user): bool
    {
        return $user->can('tasks.view');
    }
    
    // View specific task
    public function view(User $user, Task $task): bool
    {
        // Same organization
        if ($task->organization_id !== $user->organization_id) {
            return false;
        }
        
        // Owner/creator can view
        if ($task->created_by === $user->id) {
            return true;
        }
        
        // Assignee can view
        if ($task->assignees->contains($user->id)) {
            return true;
        }
        
        // Department head can view department tasks
        if ($user->is_department_head && 
            $task->department_id === $user->department_id) {
            return true;
        }
        
        // General permission
        return $user->can('tasks.view');
    }
    
    // Create task
    public function create(User $user): bool
    {
        return $user->can('tasks.create');
    }
    
    // Update task
    public function update(User $user, Task $task): bool
    {
        // Creator can update
        if ($task->created_by === $user->id) {
            return true;
        }
        
        // Manager of assignees can update
        if ($user->can('tasks.manage') && 
            $this->isManagerOfAssignees($user, $task)) {
            return true;
        }
        
        return $user->can('tasks.edit');
    }
    
    // Delete task
    public function delete(User $user, Task $task): bool
    {
        // Only creator or admin can delete
        return $task->created_by === $user->id || 
               $user->can('tasks.delete');
    }
}
```

---

## 6. Testing Strategy

### 6.1 Backend Testing

```php
<?php
// tests/Feature/TaskControllerTest.php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;
    
    private User $user;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }
    
    public function test_can_list_tasks(): void
    {
        Task::factory()->count(5)->create([
            'organization_id' => $this->user->organization_id,
        ]);
        
        $response = $this->getJson('/api/v1/tasks');
        
        $response
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'status', 'priority'],
                ],
                'meta' => ['current_page', 'total'],
            ]);
    }
    
    public function test_can_create_task(): void
    {
        $payload = [
            'title' => 'Test Task',
            'priority' => 'high',
            'assignees' => [$this->user->id],
        ];
        
        $response = $this->postJson('/api/v1/tasks', $payload);
        
        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Test Task')
            ->assertJsonPath('data.priority', 'high');
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'priority' => 'high',
        ]);
    }
    
    public function test_validation_errors_returned_properly(): void
    {
        $response = $this->postJson('/api/v1/tasks', []);
        
        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'type',
                'title',
                'status',
                'errors' => ['title', 'assignees'],
            ]);
    }
    
    public function test_cannot_access_other_organization_tasks(): void
    {
        $otherOrgTask = Task::factory()->create();
        
        $response = $this->getJson("/api/v1/tasks/{$otherOrgTask->id}");
        
        $response->assertForbidden();
    }
}
```

### 6.2 Frontend Testing

```typescript
// tests/components/TaskCard.test.ts
import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import TaskCard from '~/components/tasks/TaskCard.vue'

describe('TaskCard', () => {
  const mockTask = {
    id: '1',
    title: 'Test Task',
    status: 'in_progress',
    priority: 'high',
    progress: 50,
    due_date: '2026-02-15',
    assignees: [
      { id: '1', name: 'John Doe', avatar: null },
    ],
    labels: [
      { id: '1', name: 'Bug', color: '#ef4444' },
    ],
  }
  
  it('renders task title', () => {
    const wrapper = mount(TaskCard, {
      props: { task: mockTask },
    })
    
    expect(wrapper.text()).toContain('Test Task')
  })
  
  it('shows priority indicator', () => {
    const wrapper = mount(TaskCard, {
      props: { task: mockTask },
    })
    
    const priorityBar = wrapper.find('[data-testid="priority-indicator"]')
    expect(priorityBar.classes()).toContain('bg-priority-high')
  })
  
  it('emits click event when clicked', async () => {
    const wrapper = mount(TaskCard, {
      props: { task: mockTask },
    })
    
    await wrapper.trigger('click')
    
    expect(wrapper.emitted('click')).toBeTruthy()
    expect(wrapper.emitted('click')![0]).toEqual([mockTask])
  })
  
  it('shows overdue styling when past due', () => {
    const overdueTask = {
      ...mockTask,
      due_date: '2025-01-01',
    }
    
    const wrapper = mount(TaskCard, {
      props: { task: overdueTask },
    })
    
    const dueDate = wrapper.find('[data-testid="due-date"]')
    expect(dueDate.classes()).toContain('text-error-500')
  })
})
```

---

## 7. Deployment Configuration

### 7.1 Docker Compose (Development)

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build:
      context: ./backend
      dockerfile: Dockerfile
    volumes:
      - ./backend:/var/www/html
    depends_on:
      - postgres
      - redis
    environment:
      - APP_ENV=local
      - DB_HOST=postgres
      - REDIS_HOST=redis
    ports:
      - "8000:8000"

  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile
    volumes:
      - ./frontend:/app
      - /app/node_modules
    ports:
      - "3000:3000"
    environment:
      - NUXT_PUBLIC_API_BASE=http://localhost:8000/api/v1

  postgres:
    image: postgres:16-alpine
    volumes:
      - postgres_data:/var/lib/postgresql/data
    environment:
      - POSTGRES_DB=ishyar
      - POSTGRES_USER=ishyar
      - POSTGRES_PASSWORD=secret
    ports:
      - "5432:5432"

  redis:
    image: redis:7-alpine
    volumes:
      - redis_data:/data
    ports:
      - "6379:6379"

  meilisearch:
    image: getmeili/meilisearch:v1.6
    volumes:
      - meilisearch_data:/meili_data
    environment:
      - MEILI_MASTER_KEY=masterkey
    ports:
      - "7700:7700"

  reverb:
    build:
      context: ./backend
      dockerfile: Dockerfile
    command: php artisan reverb:start
    depends_on:
      - redis
    ports:
      - "8080:8080"

volumes:
  postgres_data:
  redis_data:
  meilisearch_data:
```

### 7.2 Production Nginx Config

```nginx
# nginx.conf
server {
    listen 80;
    server_name ishyar.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name ishyar.com;
    
    ssl_certificate /etc/letsencrypt/live/ishyar.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/ishyar.com/privkey.pem;
    
    # Frontend (SPA)
    location / {
        root /var/www/frontend/dist;
        try_files $uri $uri/ /index.html;
        
        # Cache static assets
        location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
        }
    }
    
    # API
    location /api {
        proxy_pass http://app:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # WebSocket
    location /app {
        proxy_pass http://reverb:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
    }
    
    # Admin panel
    location /admin {
        proxy_pass http://app:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

---

*This architecture document provides the technical foundation for IshYar development. All implementations should follow these patterns and guidelines.*
