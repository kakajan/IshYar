<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Plus, Search, Loader2 } from 'lucide-vue-next'
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '~/components/ui/table'
import { Avatar, AvatarFallback, AvatarImage } from '~/components/ui/avatar'
import { Card, CardContent, CardHeader, CardTitle } from '~/components/ui/card'

definePageMeta({
  layout: 'default',
})

const { t } = useI18n()
const users = ref<any[]>([])
const isLoading = ref(true)
const searchQuery = ref('')
const { $api } = useNuxtApp()

const fetchUsers = async () => {
  isLoading.value = true
  try {
    const response = await $api<{ data: any[] }>('/users', {
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

onMounted(fetchUsers)

const getInitials = (name: string) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">{{ t('nav.users') }}</h1>
        <p class="text-muted-foreground">{{ t('users.subtitle') }}</p>
      </div>
      <Button as-child>
        <NuxtLink to="/users/create">
          <Plus class="mr-2 h-4 w-4" />
          {{ t('users.create_new') }}
        </NuxtLink>
      </Button>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>
          <div class="flex items-center gap-2">
            <Search class="h-4 w-4 text-muted-foreground" />
            <Input
              v-model="searchQuery"
              :placeholder="t('common.search')"
              class="max-w-xs"
            />
          </div>
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div v-if="isLoading" class="flex justify-center py-8">
          <Loader2 class="h-8 w-8 animate-spin text-primary" />
        </div>
        
        <Table v-else>
          <TableHeader>
            <TableRow>
              <TableHead>{{ t('users.name') }}</TableHead>
              <TableHead>{{ t('users.email') }}</TableHead>
              <TableHead>{{ t('users.department') }}</TableHead>
              <TableHead>{{ t('users.role') }}</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="user in users" :key="user.id">
              <TableCell class="flex items-center gap-3">
                <Avatar>
                  <AvatarImage :src="user.avatar_url" />
                  <AvatarFallback>{{ getInitials(user.name) }}</AvatarFallback>
                </Avatar>
                <div class="flex flex-col">
                  <span class="font-medium">{{ user.name }}</span>
                </div>
              </TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>
                {{ user.department?.name || '-' }}
              </TableCell>
              <TableCell>
                {{ user.role || 'Employee' }}
              </TableCell>
            </TableRow>
            <TableRow v-if="users.length === 0">
              <TableCell colspan="4" class="h-24 text-center">
                {{ t('common.no_results') }}
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </div>
</template>
