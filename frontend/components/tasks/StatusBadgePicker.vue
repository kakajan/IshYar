<script setup lang="ts">
import { computed } from 'vue'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Badge } from '@/components/ui/badge'

const props = defineProps<{
  modelValue: string
  options: { label: string; value: string; variant: string }[]
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const currentOption = computed(() => 
  props.options.find(o => o.value === props.modelValue)
)

const handleSelect = (value: string) => {
  emit('update:modelValue', value)
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Badge
        :variant="(currentOption?.variant as any) || 'secondary'"
        class="cursor-pointer hover:opacity-80 transition-opacity"
      >
        {{ currentOption?.label || modelValue }}
      </Badge>
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuItem
        v-for="option in options"
        :key="option.value"
        @click="handleSelect(option.value)"
      >
        <Badge :variant="option.variant as any" class="mr-2">
          {{ option.label }}
        </Badge>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
