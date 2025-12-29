<script setup lang="ts">
import type { Task, TaskFilters } from '~/composables/useTasks'

definePageMeta({
  layout: 'default',
  // middleware: 'auth', // Handled globally by auth.global.ts
})

const { t } = useI18n()
const router = useRouter()
const { fetchKanban, moveTask, createTask, updateTask, kanbanData, isLoading, error } = useTasks()

// Filters state
const filters = ref<TaskFilters>({})
const showFilters = ref(false)


// Fetch kanban data on mount
onMounted(() => {
  loadKanban()
})

// Watch for filter changes
watch(filters, () => {
  loadKanban()
}, { deep: true })

const loadKanban = async () => {
  await fetchKanban(filters.value)
}

// Handle task click - navigate to detail
const handleTaskClick = (task: Task) => {
  router.push(`/tasks/${task.id}`)
}

// Handle task move between columns
const handleTaskMove = async (task: Task, newStatus: string) => {
  try {
    await moveTask(task.id, newStatus)
    // Refresh to get updated data
    await loadKanban()
  } catch (err) {
    console.error('Failed to move task:', err)
    // TODO: Show error toast
  }
}





const handleCreateTask = async (title: string, columnId: string) => {
  try {
    const newTask = await createTask({
      title,
      type: 'situational', // Default type for quick-add tasks
      status: columnId as any, // Cast if necessary, or ensure Types match
      priority: 'medium', // Default
    })
    
    // Optimistic update - add task to column immediately
    if (kanbanData.value && newTask) {
      // Use the actual status from the created task, or fallback to columnId
      const statusToCheck = newTask.status || columnId
      const column = kanbanData.value.columns.find(col => col.id === statusToCheck)
      
      if (column) {
        // Add the new task to the beginning of the column
        column.tasks.unshift(newTask)
        column.count++
      }
    }
    
    // Refresh board in background to sync with server
    // loadKanban()

    const { add: addToast } = useToast()
    addToast({
      title: t('common.success'),
      description: t('tasks.created'),
      variant: 'default'
    })
  } catch (err) {
    console.error('Failed to create task:', err)
    const { add: addToast } = useToast()
    addToast({
      title: t('common.error'),
      description: t('tasks.create_failed'),
      variant: 'destructive'
    })
  }
}

// Filter options
const priorityOptions = [
  { value: '', label: t('common.all') },
  { value: 'low', label: t('tasks.priority.low') },
  { value: 'medium', label: t('tasks.priority.medium') },
  { value: 'high', label: t('tasks.priority.high') },
  { value: 'urgent', label: t('tasks.priority.urgent') },
]

// Clear all filters
const clearFilters = () => {
  filters.value = {}
}

// Check if any filters are active
const hasActiveFilters = computed(() => {
  return Object.values(filters.value).some(v => v !== undefined && v !== '' && v !== null)
})

const handleLabelDrop = async (label: any, task: Task) => {
  // Check if task already has this label (using labels array or label_ids)
  const hasLabel = task.labels?.some(l => l.id === label.id) || task.label_ids?.includes(label.id)
  if (hasLabel) {
    // Don't show any notification, just silently ignore
    return
  }

  // Optimistic update - update the local state immediately
  if (kanbanData.value) {
    for (const column of kanbanData.value.columns) {
      const foundTask = column.tasks.find(t => t.id === task.id)
      if (foundTask) {
        // Add to labels array (with full object)
        if (!foundTask.labels) foundTask.labels = []
        foundTask.labels.push({ id: label.id, name: label.name, color: label.color })
        // Also update label_ids if it exists
        if (!foundTask.label_ids) foundTask.label_ids = []
        foundTask.label_ids.push(label.id)
        break
      }
    }
  }
  
  try {
     // Get current label IDs from task (if available) or empty array
     const currentLabelIds = task.label_ids || []
     const newLabelIds = [...currentLabelIds, label.id]
     
     // Update task with new label_ids array
     await updateTask(task.id, { label_ids: newLabelIds } as any)
       
     const { add: addToast } = useToast()
     addToast({
       title: t('common.success'),
       description: t('tasks.label_added'),
       variant: 'default'
     })
  } catch (e) {
     console.error('Failed to add label:', e)
     // Revert optimistic update on error
     if (kanbanData.value) {
       for (const column of kanbanData.value.columns) {
         const foundTask = column.tasks.find(t => t.id === task.id)
         if (foundTask) {
           // Remove from labels array
           if (foundTask.labels) {
             foundTask.labels = foundTask.labels.filter(l => l.id !== label.id)
           }
           // Remove from label_ids
           if (foundTask.label_ids) {
             foundTask.label_ids = foundTask.label_ids.filter(id => id !== label.id)
           }
           break
         }
       }
     }
     
     const { add: addToast } = useToast()
     addToast({
       title: t('common.error'),
       description: t('tasks.label_add_failed'),
       variant: 'destructive'
     })
  }
}

// Labels for drag & drop
const labels = ref<any[]>([])
const fetchLabels = async () => {
  try {
    const { $api } = useNuxtApp()
    const res = await $api<{ data: any[] }>('/labels')
    labels.value = res.data
  } catch (e) {
    console.error(e)
  }
}

const handleQuickAction = async (action: string, task: Task & { assignee_ids?: string[]; label_to_remove?: string }) => {
  if (action === 'assign') {
    try {
      const ids = task.assignee_ids
      if (!ids || ids.length === 0) return
      
      // Send array of assignee IDs to backend
      await updateTask(task.id, { assignee_ids: ids } as any)
      
      // Refresh board
      await loadKanban()

      const { add: addToast } = useToast()
      addToast({
        title: t('common.success'),
        description: t('tasks.assigned'),
        variant: 'default'
      })
    } catch (e) {
      console.error(e)
    }
  } else if (action === 'complete') {
    try {
      await updateTask(task.id, { status: 'completed' })
      // Refresh board
      await loadKanban()
      
      const { add: addToast } = useToast()
      addToast({
        title: t('common.success'),
        description: t('tasks.completed'),
        variant: 'default'
      })
    } catch (e) {
      console.error(e)
    }
  } else if (action === 'update-priority') {
    try {
      if (!task.priority) return
      
      // Optimistic update
      if (kanbanData.value) {
        for (const column of kanbanData.value.columns) {
          const foundTask = column.tasks.find(t => t.id === task.id)
          if (foundTask) {
            foundTask.priority = task.priority as any
            break
          }
        }
      }

      await updateTask(task.id, { priority: task.priority } as any)
      
      const { add: addToast } = useToast()
      addToast({
        title: t('common.success'),
        description: t('messages.update_success'),
        variant: 'default'
      })
    } catch (e) {
      console.error('Failed to update priority:', e)
      await loadKanban() 
    }
  }
}

const handleLabelRemove = async (label: any, task: Task) => {
  try {
    const labelIdToRemove = label.id
    if (!labelIdToRemove) return

    // Remove label from current label_ids
    const currentLabelIds = task.label_ids || task.labels?.map(l => l.id) || []
    const newLabelIds = currentLabelIds.filter(id => id !== labelIdToRemove)

    // Optimistic update - remove from UI immediately
    if (kanbanData.value) {
      for (const column of kanbanData.value.columns) {
        const foundTask = column.tasks.find(t => t.id === task.id)
        if (foundTask) {
          if (foundTask.labels) {
            foundTask.labels = foundTask.labels.filter(l => l.id !== labelIdToRemove)
          }
          if (foundTask.label_ids) {
            foundTask.label_ids = foundTask.label_ids.filter(id => id !== labelIdToRemove)
          }
          break
        }
      }
    }

    // Update backend
    await updateTask(task.id, { label_ids: newLabelIds } as any)

    const { add: addToast } = useToast()
    addToast({
      title: t('common.success'),
      description: t('tasks.label_removed'),
      variant: 'default'
    })
  } catch (e) {
    console.error('Failed to remove label:', e)
    // Revert optimistic update on error
    await loadKanban()
    
    const { add: addToast } = useToast()
    addToast({
      title: t('common.error'),
      description: t('tasks.label_remove_failed'),
      variant: 'destructive'
    })
  }
}

onMounted(() => {
  fetchLabels()
})

const onLabelDragStart = (evt: DragEvent, label: any) => {
  if (evt.dataTransfer) {
    evt.dataTransfer.setData('application/json', JSON.stringify({ type: 'label', ...label }))
    evt.dataTransfer.effectAllowed = 'copy'
  }
}

// Labels bar drag to scroll
const labelsBarRef = ref<HTMLElement | null>(null)
const isLabelsDragging = ref(false)
const labelsStartX = ref(0)
const labelsScrollLeft = ref(0)

const onLabelsMouseDown = (e: MouseEvent) => {
  if (!labelsBarRef.value) return
  isLabelsDragging.value = true
  labelsStartX.value = e.pageX - labelsBarRef.value.offsetLeft
  labelsScrollLeft.value = labelsBarRef.value.scrollLeft
}

const onLabelsMouseLeave = () => {
  isLabelsDragging.value = false
}

const onLabelsMouseUp = () => {
  isLabelsDragging.value = false
}

const onLabelsMouseMove = (e: MouseEvent) => {
  if (!isLabelsDragging.value || !labelsBarRef.value) return
  e.preventDefault()
  const x = e.pageX - labelsBarRef.value.offsetLeft
  const walk = (x - labelsStartX.value) * 1.5
  labelsBarRef.value.scrollLeft = labelsScrollLeft.value - walk
}
</script>

<template>
  <div class="kanban-page">
    <!-- Page Header -->
    <header class="page-header">
      <div class="header-left">
        <h1 class="page-title">
          <Icon name="heroicons:view-columns" class="w-7 h-7" />
          {{ t('kanban.title') }}
        </h1>
        <p class="page-subtitle">{{ t('kanban.subtitle') }}</p>
      </div>

      <div class="header-actions">
        <!-- Filter toggle -->
        <button 
          class="btn btn-secondary" 
          :class="{ 'active': showFilters }"
          @click="showFilters = !showFilters"
        >
          <Icon name="heroicons:funnel" class="w-5 h-5" />
          {{ t('common.filters') }}
          <span v-if="hasActiveFilters" class="filter-badge" />
        </button>

        <!-- Refresh button -->
        <button class="btn btn-secondary" @click="loadKanban" :disabled="isLoading">
          <Icon name="heroicons:arrow-path" class="w-5 h-5" :class="{ 'animate-spin': isLoading }" />
        </button>

        <!-- View switcher -->
        <div class="view-switcher">
          <NuxtLink to="/tasks" class="view-btn">
            <Icon name="heroicons:list-bullet" class="w-5 h-5" />
          </NuxtLink>
          <NuxtLink to="/tasks/kanban" class="view-btn active">
            <Icon name="heroicons:view-columns" class="w-5 h-5" />
          </NuxtLink>
        </div>
      </div>
    </header>

    <!-- Filters panel -->
    <Transition name="slide-down">
      <div v-if="showFilters" class="filters-panel">
        <div class="filters-grid">
          <!-- Priority filter -->
          <div class="filter-group">
            <label class="filter-label">{{ t('tasks.priority.label') }}</label>
            <select v-model="filters.priority" class="filter-select">
              <option v-for="opt in priorityOptions" :key="opt.value" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>
          </div>

          <!-- Assignee filter (TODO: add user dropdown) -->
          <div class="filter-group">
            <label class="filter-label">{{ t('tasks.assignee') }}</label>
            <input 
              v-model="filters.assignee_id" 
              type="text" 
              class="filter-input"
              :placeholder="t('common.search')"
            />
          </div>

          <!-- Subject/Tag filter -->
          <div class="filter-group">
            <label class="filter-label">{{ t('tasks.subject') }}</label>
            <input 
              v-model="filters.subject" 
              type="text" 
              class="filter-input"
              :placeholder="t('common.search')"
            />
          </div>
        </div>

        <!-- Clear filters -->
        <button 
          v-if="hasActiveFilters" 
          class="clear-filters-btn"
          @click="clearFilters"
        >
          <Icon name="heroicons:x-mark" class="w-4 h-4" />
          {{ t('common.clear_filters') }}
        </button>
      </div>
    </Transition>

    <!-- Error state -->
    <div v-if="error" class="error-banner">
      <Icon name="heroicons:exclamation-circle" class="w-5 h-5" />
      <span>{{ error.message }}</span>
      <button @click="loadKanban" class="retry-btn">{{ t('common.retry') }}</button>
    </div>

    <!-- Kanban Board with Label Bar -->
    <div class="kanban-wrapper">
      <!-- Label Bar (Horizontal) -->
      <div 
        class="labels-bar"
        ref="labelsBarRef"
        @mousedown="onLabelsMouseDown"
        @mouseleave="onLabelsMouseLeave"
        @mouseup="onLabelsMouseUp"
        @mousemove="onLabelsMouseMove"
      >
        <div class="labels-track">
          <div class="labels-header">
             <Icon name="heroicons:tag" class="w-4 h-4" />
             <span class="text-sm font-semibold">{{ t('tasks.form.labels') }}</span>
          </div>
          <div class="separator" />
          <div class="labels-list">
            <div 
              v-for="label in labels" 
              :key="label.id"
              class="draggable-label"
              :style="{ backgroundColor: label.color + '20', color: label.color, borderColor: label.color }"
              draggable="true"
              @dragstart="onLabelDragStart($event, label)"
            >
              {{ label.name }}
            </div>
          </div>
        </div>
      </div>

      <KanbanBoard
        :data="kanbanData"
        :filters="filters"
        :is-loading="isLoading"
        @task-click="handleTaskClick"
        @task-move="handleTaskMove"
        @refresh="loadKanban"
        @label-drop="handleLabelDrop"
        @create-task="handleCreateTask"
        @quick-action="handleQuickAction"
        @label-remove="handleLabelRemove"
      />
    </div>
  </div>
</template>

<style scoped>
/* ... existing styles ... */
.kanban-wrapper {
  display: flex;
  flex-direction: column; /* Changed from default (row) or missing to column */
  flex: 1;
  overflow: hidden;
}

.labels-bar {
  width: 100%;
  background: white;
  border-bottom: 1px solid var(--border, #e2e8f0);
  padding: 12px 24px;
  overflow-x: auto;
  flex-shrink: 0;
  cursor: grab;
  user-select: none;
}

.labels-bar:active {
  cursor: grabbing;
}

.labels-track {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: min-content;
}

.labels-header {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--text-secondary, #64748b);
  white-space: nowrap;
}

.separator {
  width: 1px;
  height: 24px;
  background: var(--border, #e2e8f0);
}

.labels-list {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 8px;
}

.draggable-label {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: grab;
  border: 1px solid transparent;
  transition: all 150ms ease;
  white-space: nowrap;
}

.draggable-label:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.draggable-label:active {
  cursor: grabbing;
}

/* Hide scrollbar for labels bar but allow functionality */
.labels-bar::-webkit-scrollbar {
  height: 0px;
  background: transparent;
}

/* ... existing styles ... */
.kanban-page {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 64px);
  background: var(--bg-primary, #f8fafc);
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  background: white;
  border-bottom: 1px solid var(--border, #e2e8f0);
  flex-shrink: 0;
}

.header-left {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.page-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary, #1e293b);
  margin: 0;
}

.page-subtitle {
  font-size: 0.9rem;
  color: var(--text-secondary, #64748b);
  margin: 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 150ms ease;
  border: none;
}

.btn-primary {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

.btn-secondary {
  background: var(--bg-secondary, #f1f5f9);
  color: var(--text-secondary, #64748b);
}

.btn-secondary:hover {
  background: var(--border, #e2e8f0);
  color: var(--text-primary, #1e293b);
}

.btn-secondary.active {
  background: var(--primary, #6366f1);
  color: white;
}

.filter-badge {
  width: 8px;
  height: 8px;
  background: #ef4444;
  border-radius: 50%;
}

.view-switcher {
  display: flex;
  gap: 2px;
  background: var(--bg-secondary, #f1f5f9);
  border-radius: 10px;
  padding: 3px;
}

.view-btn {
  padding: 6px 10px;
  border-radius: 7px;
  color: var(--text-secondary, #64748b);
  transition: all 150ms ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.view-btn.active,
.view-btn:hover {
  background: white;
  color: var(--primary, #6366f1);
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.filters-panel {
  padding: 16px 24px;
  background: white;
  border-bottom: 1px solid var(--border, #e2e8f0);
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.filter-label {
  font-size: 0.8rem;
  font-weight: 500;
  color: var(--text-secondary, #64748b);
}

.filter-select,
.filter-input {
  padding: 10px 12px;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 8px;
  font-size: 0.9rem;
  background: white;
  transition: all 150ms ease;
}

.filter-select:focus,
.filter-input:focus {
  outline: none;
  border-color: var(--primary, #6366f1);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.clear-filters-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 12px;
  padding: 8px 12px;
  background: none;
  border: none;
  color: #ef4444;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
}

.clear-filters-btn:hover {
  text-decoration: underline;
}

.error-banner {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 24px;
  background: #fef2f2;
  color: #b91c1c;
  font-size: 0.9rem;
}

.retry-btn {
  margin-left: auto;
  padding: 6px 12px;
  background: #b91c1c;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.85rem;
  cursor: pointer;
}

/* Slide down animation */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 200ms ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Dark mode */
:root.dark .kanban-page {
  --bg-primary: #0f172a;
}

:root.dark .page-header,
:root.dark .filters-panel {
  background: #1e293b;
  border-color: #334155;
}
</style>
