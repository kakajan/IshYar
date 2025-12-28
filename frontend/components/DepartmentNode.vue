<template>
  <div class="department-node" :class="{ 'ps-4': level > 0 }">
    <div
      class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group"
      :class="{ 'border-s-2 border-primary-500': level > 0 }"
    >
      <!-- Expand/Collapse -->
      <button
        v-if="hasChildren"
        class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-gray-600"
        @click="expanded = !expanded"
      >
        <UIcon
          :name="
            expanded ? 'i-heroicons-chevron-down' : 'i-heroicons-chevron-right'
          "
          class="w-4 h-4 transition-transform"
        />
      </button>
      <div v-else class="w-6" />

      <!-- Icon -->
      <div
        class="w-8 h-8 rounded-lg flex items-center justify-center"
        :class="
          level === 0
            ? 'bg-primary-100 dark:bg-primary-900'
            : 'bg-gray-100 dark:bg-gray-700'
        "
      >
        <UIcon
          :name="
            level === 0
              ? 'i-heroicons-building-office-2'
              : 'i-heroicons-building-library'
          "
          class="w-4 h-4"
          :class="level === 0 ? 'text-primary-500' : 'text-gray-500'"
        />
      </div>

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
          <span class="font-medium truncate">{{ department.name }}</span>
          <UBadge v-if="department.code" size="xs" color="gray">{{
            department.code
          }}</UBadge>
          <UBadge v-if="!department.is_active" size="xs" color="red">
            {{ $t('common.inactive') }}
          </UBadge>
        </div>
        <div v-if="department.head" class="text-sm text-gray-500 truncate">
          {{ department.head.name }}
        </div>
      </div>

      <!-- Actions -->
      <div
        class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1"
      >
        <UButton
          color="gray"
          variant="ghost"
          icon="i-heroicons-pencil"
          size="xs"
          @click="$emit('edit', department)"
        />
        <UButton
          color="red"
          variant="ghost"
          icon="i-heroicons-trash"
          size="xs"
          @click="$emit('delete', department)"
        />
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

<script setup lang="ts">
interface Department {
  id: string
  name: string
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
