<script setup lang="ts">
import type { Task, KanbanColumn as IKanbanColumn } from '~/composables/useTasks'
import draggable from 'vuedraggable'

const props = defineProps<{
  column: IKanbanColumn
}>()

const emit = defineEmits<{
  (e: 'task-click', task: Task): void
  (e: 'task-move', task: Task, newStatus: string): void
  (e: 'quick-action', action: string, task: Task): void
  (e: 'label-drop', label: any, task: Task): void
  (e: 'create-task', title: string, columnId: string): void
  (e: 'label-remove', label: any, task: Task): void
}>()

const { t } = useI18n()

// WIP limit warning
const isOverWipLimit = computed(() => {
  return props.column.wip_limit && props.column.count > props.column.wip_limit
})

// Focus mode state
const isFocused = ref(false)
const newTaskTitle = ref('')

// Handle drag end
const onDragEnd = (evt: any) => {
  if (evt.added) {
    emit('task-move', evt.added.element, props.column.id)
  }
}

const toggleFocus = () => {
  isFocused.value = !isFocused.value
}

const onQuickAdd = () => {
  if (!newTaskTitle.value.trim()) return
  
  emit('create-task', newTaskTitle.value, props.column.id)
  newTaskTitle.value = ''
}
</script>

<template>
  <div class="kanban-column-wrapper">
    <div v-if="isFocused" class="column-placeholder"></div>
    <Teleport to="body" :disabled="!isFocused">
      <div 
        class="kanban-column"
        :class="{ 'is-focused': isFocused }"
        v-bind="$attrs"
      >
    <!-- Column Header -->
    <div 
      class="column-header"
      :style="{ '--column-color': column.color }"
      @dblclick="toggleFocus"
    >
      <div class="header-left">
        <div class="color-indicator" :style="{ backgroundColor: column.color }" />
        <h3 class="column-title">{{ t(column.title) }}</h3>
        <span 
          class="task-count" 
          :class="{ 'over-limit': isOverWipLimit }"
        >
          {{ column.count }}
          <template v-if="column.wip_limit">/ {{ column.wip_limit }}</template>
        </span>
      </div>

      <!-- Header Actions -->
      <div class="header-actions">
        <button 
          class="header-btn"
          :title="isFocused ? t('common.close') : t('kanban.focus_column')"
          @click.stop="toggleFocus"
        >
          <Icon 
            :name="isFocused ? 'heroicons:x-mark' : 'heroicons:arrows-pointing-out'" 
            class="w-4 h-4" 
          />
        </button>
      </div>
    </div>

    <!-- WIP Limit Warning -->
    <div v-if="isOverWipLimit" class="wip-warning">
      <Icon name="heroicons:exclamation-triangle" class="w-4 h-4" />
      <span>{{ t('kanban.wip_limit_exceeded') }}</span>
    </div>


    <!-- Column Content -->
    <div class="column-content">
      <draggable
        :list="column.tasks"
        group="kanban-tasks"
        item-key="id"
        class="tasks-list"
        ghost-class="ghost-card"
        chosen-class="chosen-card"
        drag-class="drag-card"
        :animation="200"
        @change="onDragEnd"
      >
        <template #item="{ element }">
          <KanbanCard
            :task="element"
            @click="emit('task-click', element)"
            @quick-action="(action, task) => emit('quick-action', action, task)"
            @label-drop="(label, task) => emit('label-drop', label, task)"
            @label-remove="(label, task) => emit('label-remove', label, task)"
          />
        </template>
      </draggable>

      <!-- Empty state -->
      <div v-if="column.tasks.length === 0" class="empty-column">
        <Icon name="heroicons:inbox" class="w-8 h-8 text-slate-300" />
        <span>{{ t('kanban.no_tasks') }}</span>
      </div>
    </div>

    <!-- Quick Add Input (fixed at bottom in focused mode) -->
    <div v-if="isFocused" class="quick-add-section">
      <div class="quick-add-input-wrapper">
        <input
          v-model="newTaskTitle"
          type="text"
          class="quick-add-input"
          :placeholder="t('tasks.quick_add_placeholder')"
          @keydown.enter.prevent="onQuickAdd"
          autoFocus
        />
        <button 
          class="quick-add-btn"
          @click="onQuickAdd"
        >
          <Icon name="heroicons:plus" class="w-5 h-5" />
        </button>
      </div>
      <p class="quick-add-hint">{{ t('tasks.quick_add_hint') }}</p>
    </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.kanban-column-wrapper {
  height: 100%;
  display: flex;
}

.column-placeholder {
  width: var(--kanban-column-width, 320px);
  min-width: var(--kanban-column-width, 320px);
  height: 100%;
  border-radius: 16px;
  background: transparent;
}
.kanban-column {
  display: flex;
  flex-direction: column;
  width: var(--kanban-column-width, 320px);
  min-width: var(--kanban-column-width, 320px);
  max-height: calc(100vh - 200px);
  background: var(--column-bg, #f8fafc);
  border-radius: 16px;
  transition: all var(--kanban-transition-speed, 200ms) ease;
}

/* Focused Mode */
.kanban-column.is-focused {
  position: fixed;
  inset: 0;
  width: 100vw;
  height: 100vh;
  max-height: 100vh;
  z-index: 9999;
  margin: 0;
  border-radius: 0;
  background: var(--bg-primary, #f8fafc);
}

.kanban-column.is-focused .column-header {
  border-radius: 0;
  padding: 20px 32px;
}

.kanban-column.is-focused .column-content {
  padding: 32px;
  max-width: 800px;
  margin: 0 auto;
  width: 100%;
}

.column-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  border-radius: 16px 16px 0 0;
  background: linear-gradient(135deg, var(--column-color) 0%, color-mix(in srgb, var(--column-color) 80%, black) 100%);
  cursor: pointer; /* To suggest dblclick works */
}

.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-actions {
  display: flex;
  align-items: center;
}

.header-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 6px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  cursor: pointer;
  transition: all 150ms ease;
  backdrop-filter: blur(4px);
}

.header-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.05);
}

.color-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  box-shadow: 0 0 8px currentColor;
}

.column-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: white;
  margin: 0;
  text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.task-count {
  font-size: 0.8rem;
  font-weight: 600;
  color: rgba(255,255,255,0.9);
  background: rgba(255,255,255,0.2);
  padding: 2px 8px;
  border-radius: 12px;
  backdrop-filter: blur(4px);
}

.task-count.over-limit {
  background: #ef4444;
  animation: pulse 1s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.wip-warning {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #fef3c7;
  color: #b45309;
  font-size: 0.8rem;
  font-weight: 500;
}

.column-content {
  flex: 1;
  overflow-y: auto;
  padding: 12px;
}

/* Quick Add Section */
.quick-add-section {
  margin-bottom: 24px;
}

/* Fixed at bottom in fullscreen mode */
.kanban-column.is-focused .quick-add-section {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  margin: 0;
  padding: 16px 32px;
  padding-bottom: calc(16px + env(safe-area-inset-bottom));
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-top: 1px solid var(--border, #e2e8f0);
  box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
  z-index: 100;
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Add padding to content in focused mode to prevent overlap */
.kanban-column.is-focused .column-content {
  padding-bottom: 140px; /* Space for fixed input */
}

.quick-add-input-wrapper {
  display: flex;
  gap: 8px;
  background: white;
  padding: 4px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  border: 1px solid var(--border, #e2e8f0);
  max-width: 800px;
  margin: 0 auto;
}

.quick-add-input {
  flex: 1;
  border: none;
  padding: 10px 16px;
  font-size: 1rem;
  outline: none;
  background: transparent;
}

.quick-add-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #10b981;
  color: white;
  border: none;
  cursor: pointer;
  transition: all 150ms ease;
}

.quick-add-btn:hover {
  background: #059669;
  transform: scale(1.05);
}

.quick-add-hint {
  font-size: 0.8rem;
  color: var(--text-tertiary, #94a3b8);
  margin: 6px 0 0 8px;
  text-align: center;
  max-width: 800px;
  margin: 6px auto 0;
}

.tasks-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-height: 100px;
}

.empty-column {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 32px 16px;
  color: var(--text-tertiary, #94a3b8);
  font-size: 0.85rem;
}

/* Drag states */
.ghost-card {
  opacity: 0.5;
  background: var(--primary, #6366f1) !important;
  border: 2px dashed var(--primary, #6366f1);
}

.chosen-card {
  transform: scale(1.02);
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
}

.drag-card {
  transform: rotate(3deg);
}


/* Custom scrollbar */
.column-content::-webkit-scrollbar {
  width: 6px;
}

.column-content::-webkit-scrollbar-track {
  background: transparent;
}

.column-content::-webkit-scrollbar-thumb {
  background: var(--border, #e2e8f0);
  border-radius: 3px;
}

.column-content::-webkit-scrollbar-thumb:hover {
  background: var(--text-tertiary, #94a3b8);
}

/* Dark mode */
:root.dark .kanban-column {
  --column-bg: #1e293b;
}

:root.dark .kanban-column.is-focused {
  background: #0f172a;
}

:root.dark .quick-add-input-wrapper {
  background: #1e293b;
  border-color: #334155;
}

:root.dark .quick-add-input {
  color: white;
}

/* Dark mode for fixed quick-add in fullscreen */
:root.dark .kanban-column.is-focused .quick-add-section {
  background: rgba(15, 23, 42, 0.95);
  border-top-color: #334155;
}
</style>
