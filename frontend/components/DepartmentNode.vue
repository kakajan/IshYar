<script setup lang="ts">
import { Button } from '~/components/ui/button'
import { Badge } from '~/components/ui/badge'
import {
  ChevronDown,
  ChevronRight,
  Building2,
  Building,
  Pencil,
  Trash2,
} from 'lucide-vue-next'

interface Department {
  id: string
  name: string
  slug: string
  code?: string
  is_active: boolean
  head?: { name: string }
  children?: Department[]
}

const props = defineProps<{
  department: Department
  level: number
}>()

defineEmits<{
  edit: [department: Department]
  delete: [department: Department]
}>()

const expanded = ref(true)

const hasChildren = computed(() => {
  return props.department.children && props.department.children.length > 0
})
</script>

<template>
  <div class="department-node" :class="{ 'ps-4': level > 0 }">
    <div
      class="flex items-center gap-2 p-3 rounded-lg hover:bg-muted transition-colors group"
      :class="{ 'border-s-2 border-primary': level > 0 }"
    >
      <!-- Expand/Collapse -->
      <button
        v-if="hasChildren"
        class="w-6 h-6 flex items-center justify-center text-muted-foreground hover:text-foreground"
        @click="expanded = !expanded"
      >
        <component
          :is="expanded ? ChevronDown : ChevronRight"
          class="w-4 h-4 transition-transform"
        />
      </button>
      <div v-else class="w-6" />

      <!-- Icon -->
      <div
        class="w-8 h-8 rounded-lg flex items-center justify-center"
        :class="level === 0 ? 'bg-primary/10' : 'bg-muted'"
      >
        <component
          :is="level === 0 ? Building2 : Building"
          class="w-4 h-4"
          :class="level === 0 ? 'text-primary' : 'text-muted-foreground'"
        />
      </div>

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
          <span class="font-medium truncate">{{ department.name }}</span>
          <Badge v-if="department.code" variant="secondary">
            {{ department.code }}
          </Badge>
          <Badge v-if="!department.is_active" variant="destructive">
            {{ $t('common.inactive') }}
          </Badge>
        </div>
        <div
          v-if="department.head"
          class="text-sm text-muted-foreground truncate"
        >
          {{ department.head.name }}
        </div>
      </div>

      <!-- Actions -->
      <div
        class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1"
      >
        <Button variant="ghost" size="sm" @click="$emit('edit', department)">
          <Pencil class="w-4 h-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          class="text-destructive"
          @click="$emit('delete', department)"
        >
          <Trash2 class="w-4 h-4" />
        </Button>
      </div>
    </div>

    <!-- Children -->
    <div v-if="hasChildren && expanded" class="mt-1">
      <DepartmentNode
        v-for="child in department.children"
        :key="child.id"
        :department="child"
        :level="level + 1"
        @edit="$emit('edit', $event)"
        @delete="$emit('delete', $event)"
      />
    </div>
  </div>
</template>
