<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Check, ChevronsUpDown, Plus, Search } from 'lucide-vue-next'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandItem,
  CommandList,
} from '~/components/ui/command'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '~/components/ui/popover'
import { Button } from '~/components/ui/button'
import { Badge } from '~/components/ui/badge'
import { Input } from '~/components/ui/input'
import { cn } from '~/lib/utils'

interface Label {
  id: string
  name: string
  color: string
}

const props = defineProps<{
  modelValue?: string[]
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string[]): void
}>()

const { $api } = useNuxtApp()
const { t } = useI18n()
const open = ref(false)
const labels = ref<Label[]>([])
const isLoading = ref(false)
const searchQuery = ref('')
const isCreating = ref(false)
const newLabelName = ref('')
const newLabelColor = ref('#6366f1')

const fetchLabels = async () => {
  isLoading.value = true
  try {
    const response = await $api<{ data: Label[] }>('/labels')
    labels.value = response.data
  } catch (error) {
    console.error('Failed to fetch labels', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchLabels)

const selectedLabels = computed(() => 
  labels.value.filter(l => props.modelValue?.includes(l.id))
)

const handleSelect = (labelId: string) => {
  const current = props.modelValue || []
  const newValue = current.includes(labelId)
    ? current.filter(id => id !== labelId)
    : [...current, labelId]
  emit('update:modelValue', newValue)
}

const createLabel = async () => {
  if (!newLabelName.value.trim()) return

  isCreating.value = true
  try {
    const response = await $api<{ data: Label }>('/labels', {
      method: 'POST',
      body: {
        name: newLabelName.value,
        color: newLabelColor.value
      }
    })
    labels.value.push(response.data)
    handleSelect(response.data.id)
    newLabelName.value = ''
    // Reset search
    searchQuery.value = ''
  } catch (error) {
    console.error('Failed to create label', error)
  } finally {
    isCreating.value = false
  }
}

const filteredLabels = computed(() => {
  if (!searchQuery.value) return labels.value
  return labels.value.filter(l => 
    l.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})
</script>

<template>
  <Popover v-model:open="open">
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        role="combobox"
        :aria-expanded="open"
        class="w-full justify-between h-auto min-h-10 py-2"
      >
        <div v-if="selectedLabels.length" class="flex flex-wrap gap-1">
          <Badge
            v-for="label in selectedLabels"
            :key="label.id"
            variant="outline"
            :style="{ borderColor: label.color, color: label.color }"
          >
            {{ label.name }}
          </Badge>
        </div>
        <span v-else class="text-muted-foreground">{{ t('tasks.form.select_labels') }}</span>
        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-[300px] p-0" align="start">
      <Command v-model:search-term="searchQuery">
        <div class="flex items-center border-b px-3" cmdk-input-wrapper>
          <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
          <input
            v-model="searchQuery"
            class="flex h-11 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50"
            :placeholder="t('tasks.form.search_labels')"
          >
        </div>
        <CommandList>
          <CommandEmpty class="pt-2 pb-2 px-2">
             <div class="text-sm text-muted-foreground mb-2">{{ t('tasks.form.no_labels_found') }}</div>
             <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded-full border" :style="{ backgroundColor: newLabelColor }" />
                <Input 
                  v-model="newLabelName" 
                  :placeholder="t('tasks.form.create_new_label')" 
                  class="h-8 text-xs"
                  @keydown.enter.prevent="createLabel"
                />
                <Button size="sm" variant="ghost" class="h-8 w-8 p-0" :disabled="!newLabelName" @click="createLabel">
                  <Plus class="h-4 w-4" />
                </Button>
             </div>
          </CommandEmpty>
          <CommandGroup>
            <CommandItem
              v-for="label in filteredLabels"
              :key="label.id"
              :value="label.name"
              @select="handleSelect(label.id)"
            >
              <Check
                :class="cn(
                  'mr-2 h-4 w-4',
                  modelValue?.includes(label.id) ? 'opacity-100' : 'opacity-0'
                )"
              />
              <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full" :style="{ backgroundColor: label.color }" />
                <span>{{ label.name }}</span>
              </div>
            </CommandItem>
          </CommandGroup>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>
