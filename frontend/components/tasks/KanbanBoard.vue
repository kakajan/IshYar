<script setup lang="ts">
import type { Task, KanbanData, TaskFilters } from '~/composables/useTasks'

const props = defineProps<{
  data: KanbanData | null
  filters?: TaskFilters
  isLoading?: boolean
}>()

const emit = defineEmits<{
  (e: 'task-click', task: Task): void
  (e: 'task-move', task: Task, newStatus: string): void
  (e: 'filter-change', filters: TaskFilters): void
  (e: 'refresh'): void
  (e: 'label-drop', label: any, task: Task): void
  (e: 'create-task', title: string, columnId: string): void
  (e: 'quick-action', action: string, task: Task): void
  (e: 'label-remove', label: any, task: Task): void
}>()

const { t } = useI18n()

// Handle task move between columns
const handleTaskMove = async (task: Task, newStatus: string) => {
  emit('task-move', task, newStatus)
}

// Handle quick actions on cards
const handleQuickAction = (action: string, task: Task) => {
  if (action === 'complete') {
    emit('task-move', task, 'completed')
  } else if (action === 'edit') {
    emit('task-click', task)
  } else {
    emit('quick-action', action, task)
  }
}

// Drag to scroll logic
const containerRef = ref<HTMLElement | null>(null)
const isDragging = ref(false)
const startX = ref(0)
const scrollLeft = ref(0)

const onMouseDown = (e: MouseEvent) => {
  if (!containerRef.value) return
  
  // Don't start drag scroll if clicking on a card or interactive element
  const target = e.target as HTMLElement
  if (target.closest('.kanban-card') || target.closest('button') || target.closest('a') || target.closest('.draggable-label') || target.closest('.quick-add-input')) {
    return
  }

  isDragging.value = true
  startX.value = e.pageX - containerRef.value.offsetLeft
  scrollLeft.value = containerRef.value.scrollLeft
}

const onMouseLeave = () => {
  isDragging.value = false
}

const onMouseUp = () => {
  isDragging.value = false
}

const onMouseMove = (e: MouseEvent) => {
  if (!isDragging.value || !containerRef.value) return
  e.preventDefault()
  const x = e.pageX - containerRef.value.offsetLeft
  const walk = (x - startX.value) * 1.5 // Scroll speed multiplier
  containerRef.value.scrollLeft = scrollLeft.value - walk
}
</script>

<template>
  <div class="kanban-board">
    <!-- Loading overlay -->
    <!-- Loading Progress Bar (YouTube style) -->
    <div v-if="isLoading" class="loading-progress-bar">
      <div class="progress-indeterminate"></div>
    </div>

    <!-- Board content -->
    <div 
      ref="containerRef"
      class="board-container" 
      :class="{ 'is-loading': isLoading, 'is-dragging': isDragging }"
      @mousedown="onMouseDown"
      @mouseleave="onMouseLeave"
      @mouseup="onMouseUp"
      @mousemove="onMouseMove"
    >
      <div class="columns-wrapper">
        <KanbanColumn
          v-for="column in data?.columns"
          :key="column.id"
          :column="column"
          @task-click="emit('task-click', $event)"
          @task-move="handleTaskMove"
          @quick-action="handleQuickAction"
          @label-drop="(label, task) => emit('label-drop', label, task)"
          @create-task="(title, colId) => emit('create-task', title, colId)"
          @label-remove="(label, task) => emit('label-remove', label, task)"
        />
      </div>

      <!-- Empty state -->
      <div v-if="!data?.columns?.length && !isLoading" class="empty-board">
        <div class="empty-illustration">
          <Icon name="heroicons:clipboard-document-list" class="w-16 h-16 text-slate-300" />
        </div>
        <h3>{{ t('kanban.no_tasks_title') }}</h3>
        <p>{{ t('kanban.no_tasks_description') }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.kanban-board {
  position: relative;
  height: 100%;
  overflow: hidden;
  flex: 1;
  min-width: 0;
}

.loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}

.loading-spinner {
  color: var(--primary, #6366f1);
}

.board-container {
  height: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  padding: 20px;
  transition: opacity 200ms ease;
  cursor: grab;
  /* User select none to prevent text selection while dragging */
  user-select: none; 
}

.board-container:active {
  cursor: grabbing;
}

/* Restored Styles */
.columns-wrapper {
  display: flex;
  gap: var(--kanban-column-gap, 16px);
  height: 100%;
  padding-bottom: 20px;
}

.empty-board {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  text-align: center;
  color: var(--text-secondary, #64748b);
  pointer-events: none; /* Let clicks pass through if empty */
}

.empty-illustration {
  margin-bottom: 20px;
  padding: 24px;
  background: var(--bg-secondary, #f1f5f9);
  border-radius: 50%;
}

.empty-board h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary, #1e293b);
  margin: 0 0 8px;
}

.empty-board p {
  font-size: 0.9rem;
  margin: 0;
  max-width: 300px;
}

/* Custom horizontal scrollbar */
.board-container::-webkit-scrollbar {
  height: 8px;
}

.board-container::-webkit-scrollbar-track {
  background: var(--bg-secondary, #f1f5f9);
  border-radius: 4px;
}

.board-container::-webkit-scrollbar-thumb {
  background: var(--border, #e2e8f0);
  border-radius: 4px;
}

.board-container::-webkit-scrollbar-thumb:hover {
  background: var(--text-tertiary, #94a3b8);
}

/* Loading Progress Bar */
.loading-progress-bar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: transparent;
  overflow: hidden;
  z-index: 10000;
}

.progress-indeterminate {
  background-color: var(--primary, #6366f1);
  height: 100%;
  animation: indeterminate 1.5s infinite linear;
  transform-origin: 0% 50%;
}

@keyframes indeterminate {
  0% {
    transform:  translateX(0) scaleX(0);
  }
  40% {
    transform:  translateX(0) scaleX(0.4);
  }
  100% {
    transform:  translateX(100%) scaleX(0.5);
  }
}

/* Dark mode */
:root.dark .progress-indeterminate {
  background-color: var(--primary, #818cf8);
}

:root.dark .empty-illustration {
  background: #334155;
}
</style>
