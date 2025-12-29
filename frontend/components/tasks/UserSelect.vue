<script setup lang="ts">
import { Check, ChevronsUpDown, Search } from 'lucide-vue-next'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '~/components/ui/command'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '~/components/ui/popover'
import { Button } from '~/components/ui/button'
import { Avatar, AvatarFallback, AvatarImage } from '~/components/ui/avatar'
import { cn } from '~/lib/utils'

interface User {
  id: string
  name: string
  email: string
  avatar_url?: string
}

const props = withDefaults(defineProps<{
  modelValue?: string | string[]
  placeholder?: string
  multiple?: boolean
  confirmSelection?: boolean
}>(), {
  modelValue: '',
  multiple: false,
  confirmSelection: false
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | string[]): void
}>()

const { $api } = useNuxtApp()
const open = ref(false)
const users = ref<User[]>([])
const isLoading = ref(false)
const searchQuery = ref('')
const tempValue = ref<string | string[]>([])

const fetchUsers = async () => {
  isLoading.value = true
  try {
    const response = await $api<{ data: User[] }>('/users', {
      query: { search: searchQuery.value }
    })
    users.value = response.data
  } catch (error) {
    console.error('Failed to fetch users', error)
  } finally {
    isLoading.value = false
  }
}

// Debounce search
let timeout: NodeJS.Timeout
watch(searchQuery, () => {
  clearTimeout(timeout)
  timeout = setTimeout(fetchUsers, 300)
})

watch(open, (newVal) => {
  if (newVal) {
    if (props.multiple) {
       tempValue.value = Array.isArray(props.modelValue) ? [...props.modelValue] : (props.modelValue ? [props.modelValue] : [])
    } else {
       tempValue.value = props.modelValue
    }
  }
})

onMounted(fetchUsers)

const selectedUsers = computed(() => {
  if (props.multiple) {
     const ids = Array.isArray(props.modelValue) ? props.modelValue : (props.modelValue ? [props.modelValue] : [])
     return users.value.filter(u => ids.includes(u.id))
  }
  return users.value.find(u => u.id === props.modelValue) ? [users.value.find(u => u.id === props.modelValue)!] : []
})

const getInitials = (name: string) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const handleSelect = (userId: string) => {
  if (props.multiple) {
    let current = props.confirmSelection 
      ? (Array.isArray(tempValue.value) ? [...tempValue.value] : [])
      : (Array.isArray(props.modelValue) ? [...props.modelValue] : (props.modelValue ? [props.modelValue] : []))
    
    // Ensure current is string[]
    if (!Array.isArray(current)) current = []

    const index = current.indexOf(userId)
    if (index === -1) {
      current.push(userId)
    } else {
      current.splice(index, 1)
    }
    
    if (props.confirmSelection) {
      tempValue.value = current
    } else {
      emit('update:modelValue', current)
    }
  } else {
    // Single select
    if (props.confirmSelection) {
       tempValue.value = userId === tempValue.value ? '' : userId
    } else {
       emit('update:modelValue', userId === props.modelValue ? '' : userId)
       open.value = false
    }
  }
}

const confirm = () => {
  emit('update:modelValue', tempValue.value)
  open.value = false
}

const isSelected = (userId: string) => {
  const target = props.confirmSelection ? tempValue.value : props.modelValue
  if (props.multiple) {
    return Array.isArray(target) && target.includes(userId)
  }
  return target === userId
}
</script>

<template>
  <Popover v-model:open="open">
    <PopoverTrigger as-child>
      <slot name="trigger">
        <Button
          variant="outline"
          role="combobox"
          :aria-expanded="open"
          class="w-full justify-between h-auto min-h-[40px] py-2 px-3"
        >
          <div v-if="selectedUsers.length" class="flex flex-wrap gap-1">
             <div v-for="user in selectedUsers" :key="user.id" class="flex items-center gap-1 bg-secondary rounded-full px-2 py-0.5 text-xs">
                <Avatar class="h-4 w-4">
                  <AvatarImage :src="user.avatar_url || ''" />
                  <AvatarFallback class="text-[8px]">{{ getInitials(user.name) }}</AvatarFallback>
                </Avatar>
                <span class="truncate max-w-[100px]">{{ user.name }}</span>
             </div>
          </div>
          <span v-else class="text-muted-foreground">{{ placeholder || 'Select user...' }}</span>
          <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
        </Button>
      </slot>
    </PopoverTrigger>
    <PopoverContent class="w-[300px] p-0" align="start">
      <Command v-model:searchTerm="searchQuery" :filter-function="(list: any[]) => list">
        <div class="flex items-center border-b px-3" cmdk-input-wrapper>
          <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
          <input
            v-model="searchQuery"
            class="flex h-11 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50"
            :placeholder="placeholder || 'Search users...'"
          />
        </div>
        <CommandList>
          <CommandEmpty v-if="!isLoading && users.length === 0">No users found.</CommandEmpty>
          <div v-if="isLoading" class="p-4 text-center text-sm text-muted-foreground">Loading...</div>
          <CommandGroup v-else>
            <CommandItem
              v-for="user in users"
              :key="user.id"
              :value="user.name"
              @select="handleSelect(user.id)"
            >
              <Check
                :class="cn(
                  'mr-2 h-4 w-4',
                  isSelected(user.id) ? 'opacity-100' : 'opacity-0'
                )"
              />
              <div class="flex items-center gap-2">
                <Avatar class="h-6 w-6">
                  <AvatarImage :src="user.avatar_url || ''" />
                  <AvatarFallback class="text-[10px]">{{ getInitials(user.name) }}</AvatarFallback>
                </Avatar>
                <div class="flex flex-col">
                  <span>{{ user.name }}</span>
                  <span class="text-xs text-muted-foreground">{{ user.email }}</span>
                </div>
              </div>
            </CommandItem>
          </CommandGroup>
        </CommandList>
        
        <!-- Confirm Footer -->
        <div v-if="confirmSelection" class="p-2 border-t">
          <Button class="w-full" size="sm" @click="confirm">
             Confirm
          </Button>
        </div>
      </Command>
    </PopoverContent>
  </Popover>
</template>
