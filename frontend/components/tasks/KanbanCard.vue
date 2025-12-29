<script setup lang="ts">
import type { Task } from '~/composables/useTasks'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

const props = defineProps<{
  task: Task
  isDragging?: boolean
}>()

const emit = defineEmits<{
  (e: 'click', task: Task): void
  (e: 'quick-action', action: string, task: Task): void
  (e: 'label-drop', label: any, task: Task): void
  (e: 'label-remove', label: any, task: Task): void
}>()

const { t } = useI18n()

// Priority colors
const priorityColors: Record<string, string> = {
  low: '#94a3b8',
  medium: '#3b82f6',
  high: '#f59e0b',
  urgent: '#ef4444',
  critical: '#ef4444',
}

// Format relative date
const formatDueDate = (date?: string) => {
  if (!date) return null
  const d = new Date(date)
  const now = new Date()
  const diff = Math.ceil((d.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))
  
  if (diff < 0) return { text: t('tasks.overdue', { days: Math.abs(diff) }), color: 'text-red-500' }
  if (diff === 0) return { text: t('tasks.due_today'), color: 'text-amber-500' }
  if (diff === 1) return { text: t('tasks.due_tomorrow'), color: 'text-amber-400' }
  if (diff <= 7) return { text: t('tasks.due_in_days', { days: diff }), color: 'text-blue-500' }
  return { text: d.toLocaleDateString(), color: 'text-slate-500' }
}

const dueInfo = computed(() => formatDueDate(props.task.due_date))
const priorityColor = computed(() => priorityColors[props.task.priority] || priorityColors.medium)

const onDrop = (evt: DragEvent) => {
  const data = evt.dataTransfer?.getData('application/json')
  if (data) {
    try {
      const payload = JSON.parse(data)
      if (payload.type === 'label') {
        emit('label-drop', payload, props.task)
      }
    } catch (e) {
      console.error('Invalid drop data', e)
    }
  }
}
</script>

<template>
  <div
    class="kanban-card"
    :class="{ 'is-dragging': isDragging }"
    @click="emit('click', task)"
    @dragover.prevent
    @drop.prevent="onDrop"
  >
    <!-- Priority accent bar (Clickable) -->
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <div 
          class="priority-accent"
          :style="{ backgroundColor: priorityColor }"
          role="button"
          :title="t('tasks.priority.label')"
          @click.stop
        />
      </DropdownMenuTrigger>
      <DropdownMenuContent align="start" side="bottom" :side-offset="8" class="w-40 z-[9999]" @click.stop>
        <DropdownMenuItem 
          v-for="(color, priority) in priorityColors" 
          :key="priority"
          class="flex items-center gap-2 cursor-pointer"
          @click="emit('quick-action', 'update-priority', { ...task, priority: priority as any })"
        >
          <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: color }" />
          <span class="capitalize">{{ t(`tasks.priority.${priority}`) }}</span>
          <Icon v-if="task.priority === priority" name="heroicons:check" class="w-4 h-4 ml-auto" />
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>

    <!-- Card content -->
    <div class="card-content">
      <!-- Header with title -->
      <div class="card-header">
        <h4 class="task-title">{{ task.title }}</h4>
        
        <div class="header-actions-wrapper">
          <!-- Add/Edit Assignees Button (Always visible) -->
          <UserSelect
            :model-value="task.assignee_ids || task.assignees?.map(u => u.id) || (task.assignee_id ? [task.assignee_id] : [])"
            :multiple="true"
            :confirm-selection="true"
            @update:model-value="(val) => emit('quick-action', 'assign', { ...task, assignee_ids: Array.isArray(val) ? val : [val] })"
          >
            <template #trigger>
               <button class="action-btn add-user-btn" :title="t('tasks.assign_user')" @click.stop>
                  <Icon name="heroicons:plus" class="w-3.5 h-3.5" />
               </button>
            </template>
          </UserSelect>
          
          <!-- Complete Button (only for in_progress tasks) -->
          <button
            v-if="task.status === 'in_progress'"
            class="action-btn complete-icon-btn"
            :title="t('tasks.complete')"
            @click.stop="emit('quick-action', 'complete', task)"
          >
            <Icon name="heroicons:check-circle-20-solid" mode="svg" class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Description preview -->
      <p v-if="task.description" class="task-description">
        {{ task.description }}
      </p>

      <!-- ... (rest of the file) -->

      <!-- Progress ring -->
      <div v-if="task.progress > 0" class="progress-section">
        <div class="progress-ring-container">
          <svg class="progress-ring" width="32" height="32">
            <circle
              class="progress-ring-bg"
              cx="16"
              cy="16"
              r="12"
              fill="none"
              stroke-width="3"
            />
            <circle
              class="progress-ring-fill"
              cx="16"
              cy="16"
              r="12"
              fill="none"
              stroke-width="3"
              :stroke-dasharray="`${task.progress * 0.754} 75.4`"
              :style="{ stroke: priorityColor }"
            />
          </svg>
          <span class="progress-text">{{ task.progress }}%</span>
        </div>
      </div>

      <!-- Footer with meta info -->
      <div class="card-footer">
        <!-- Row 1: Assignees and meta -->
        <div class="footer-row">
          <!-- Assignees list -->
          <div class="assignees-list">
            <template v-if="task.assignees && task.assignees.length">
               <div v-for="user in task.assignees" :key="user.id" class="assignee" :title="user.name">
                 <img v-if="user.avatar" :src="user.avatar" class="avatar">
                 <div v-else class="avatar-placeholder text-[10px] w-6 h-6">{{ user.name.charAt(0).toUpperCase() }}</div>
               </div>
            </template>
            <template v-else-if="task.assignee">
               <div class="assignee" :title="task.assignee.name">
                 <img v-if="task.assignee.avatar" :src="task.assignee.avatar" class="avatar">
                 <div v-else class="avatar-placeholder">{{ task.assignee.name.charAt(0).toUpperCase() }}</div>
               </div>
            </template>
          </div>

          <!-- Due date badge -->
          <div v-if="dueInfo" class="due-date" :class="dueInfo.color">
            <Icon name="heroicons:calendar" class="w-3.5 h-3.5" />
            <span>{{ dueInfo.text }}</span>
          </div>

          <!-- Revision indicator -->
          <div v-if="task.revision_count && task.revision_count > 0" class="revision-badge">
            <Icon name="heroicons:arrow-path" class="w-3.5 h-3.5" />
            <span>{{ task.revision_count }}</span>
          </div>
        </div>

        <!-- Row 2: Labels/Tags -->
        <div v-if="task.labels && task.labels.length > 0" class="labels-row">
          <span 
            v-for="label in task.labels" 
            :key="label.id" 
            class="label-tag"
            :style="{ borderColor: label.color, color: label.color }"
          >
            {{ label.name }}
            <button 
              class="label-remove-btn"
              :title="t('common.delete')"
              @click.stop="emit('label-remove', label, task)"
            >
              <Icon name="heroicons:x-mark" class="w-3 h-3" />
            </button>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.kanban-card {
  position: relative;
  background: var(--card-bg, white);
  border-radius: var(--kanban-card-radius, 12px);
  box-shadow: var(--kanban-card-shadow, 0 2px 8px rgba(0,0,0,0.08));
  cursor: grab;
  transition: all var(--kanban-transition-speed, 200ms) ease;
  overflow: hidden;
}

.kanban-card:hover {
  box-shadow: var(--kanban-card-shadow-hover, 0 8px 24px rgba(0,0,0,0.15));
  transform: translateY(-2px);
}

.kanban-card.is-dragging {
  transform: scale(var(--kanban-drag-scale, 1.02)) rotate(2deg);
  box-shadow: 0 12px 32px rgba(0,0,0,0.2);
  cursor: grabbing;
  opacity: 0.9;
}

.priority-accent {
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  border-radius: 12px 0 0 12px;
}

.card-content {
  padding: 12px 12px 12px 16px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 8px;
}

.task-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-primary, #1e293b);
  line-height: 1.4;
  margin: 0;
  flex: 1;
}

.header-actions-wrapper {
  display: flex;
  align-items: center;
  gap: 4px;
}

.quick-actions {
  display: flex;
  gap: 4px;
}

.action-btn {
  padding: 4px;
  border-radius: 6px;
  color: var(--text-secondary, #64748b);
  background: var(--bg-secondary, #f1f5f9);
  border: 1px solid transparent;
  cursor: pointer;
  transition: all 150ms ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn:hover {
  background: #e2e8f0 !important; /* Light gray for hover in light mode */
  color: black !important;
  border-color: #cbd5e1 !important;
}

.action-btn:hover :deep(svg) {
  fill: currentColor !important;
  color: currentColor !important;
}

.action-btn:hover :deep(path) {
  fill: currentColor !important;
  stroke: currentColor !important;
}

/* Dark mode hover - keep primary/white for better contrast in dark mode */
:root.dark .action-btn:hover {
  background: var(--primary, #6366f1) !important;
  color: white !important;
  border-color: var(--primary, #6366f1) !important;
}

.expand-btn {
  /* Force visibility */
  opacity: 1 !important;
  visibility: visible !important;
  display: flex !important;
}

.task-description {
  font-size: 0.8rem;
  color: var(--text-secondary, #64748b);
  margin: 8px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.progress-section {
  margin: 10px 0;
}

.progress-ring-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.progress-ring {
  transform: rotate(-90deg);
}

.progress-ring-bg {
  stroke: var(--bg-secondary, #e2e8f0);
}

.progress-ring-fill {
  stroke-linecap: round;
  transition: stroke-dasharray 300ms ease;
}

.progress-text {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-secondary, #64748b);
}

.card-footer {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 10px;
}

.footer-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.assignee {
  flex-shrink: 0;
}

.assignees-list {
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Push add button to end (works for both LTR and RTL) */
.footer-row > :last-child {
  margin-inline-start: auto;
}

.labels-row {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.label-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.7rem;
  padding: 2px 8px;
  border-radius: 10px;
  background: var(--bg-secondary, #f1f5f9);
  color: var(--text-secondary, #64748b);
  border: 1px solid var(--border, #e2e8f0);
}

.label-remove-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 16px;
  height: 16px;
  min-width: 16px;
  min-height: 16px;
  border-radius: 50%;
  background: transparent;
  border: none;
  cursor: pointer;
  opacity: 0.6;
  transition: all 150ms ease;
  padding: 0;
  margin: 0;
  flex-shrink: 0;
  line-height: 0; /* Important for vertical centering */
}

.label-remove-btn:hover {
  opacity: 1;
  background: rgba(0, 0, 0, 0.1);
}

.label-remove-btn svg {
  width: 10px;
  height: 10px;
  display: block;
}

.add-user-btn {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--bg-secondary, #f1f5f9);
  color: var(--text-secondary, #64748b);
  border: 1px dashed var(--border, #cbd5e1);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 150ms ease;
}

.add-user-btn:hover {
  background: white;
  color: var(--primary, #6366f1);
  border-color: var(--primary, #6366f1);
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.complete-icon-btn {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #10b981 !important;
  color: white !important;
  border: 1px solid #10b981 !important;
}

.complete-icon-btn:hover {
  background: #059669 !important;
  color: white !important;
  border-color: #059669 !important;
  transform: scale(1.1);
}

.complete-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: 12px;
  background: #10b981;
  color: white;
  border: none;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 150ms ease;
  margin-left: auto;
}

.complete-btn:hover {
  background: #059669;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  font-size: 0.7rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
}

.due-date {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

.revision-badge {
  display: flex;
  align-items: center;
  gap: 2px;
  font-size: 0.7rem;
  color: #f59e0b;
  background: #fef3c7;
  padding: 2px 6px;
  border-radius: 10px;
}

.tags {
  display: flex;
  gap: 4px;
  margin-left: auto;
}

.tag {
  font-size: 0.65rem;
  padding: 2px 6px;
  border-radius: 6px;
  background: var(--bg-secondary, #f1f5f9);
  color: var(--text-secondary, #64748b);
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 150ms ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Dark mode support */
:root.dark .kanban-card {
  --card-bg: #1e293b;
  --text-primary: #f1f5f9;
  --text-secondary: #94a3b8;
  --bg-secondary: #334155;
}

:root.dark .add-user-btn {
  background: #334155;
  border-color: #475569;
}
</style>
