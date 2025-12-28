<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-foreground">
          {{ $t('users.title') }}
        </h1>
        <p class="text-muted-foreground">
          {{ $t('users.description') }}
        </p>
      </div>
    </div>

    <!-- Filters -->
    <Card>
      <CardContent class="p-4">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px] relative">
            <Search
              class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"
            />
            <Input
              v-model="search"
              :placeholder="$t('users.search_placeholder')"
              class="pl-9"
            />
          </div>
          <Select v-model="filters.department_id">
            <SelectTrigger class="w-48">
              <SelectValue :placeholder="$t('users.filter_department')" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">{{
                $t('users.all_departments')
              }}</SelectItem>
              <SelectItem
                v-for="dept in departments"
                :key="dept.id"
                :value="dept.id"
              >
                {{ dept.name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <Select v-model="filters.is_active">
            <SelectTrigger class="w-36">
              <SelectValue :placeholder="$t('users.filter_status')" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">{{ $t('users.all_status') }}</SelectItem>
              <SelectItem value="true">{{ $t('common.active') }}</SelectItem>
              <SelectItem value="false">{{ $t('common.inactive') }}</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </CardContent>
    </Card>

    <!-- Users List -->
    <Card>
      <CardContent class="p-0">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>{{ $t('users.name') }}</TableHead>
              <TableHead>{{ $t('users.department') }}</TableHead>
              <TableHead>{{ $t('users.position') }}</TableHead>
              <TableHead>{{ $t('users.roles') }}</TableHead>
              <TableHead>{{ $t('common.status') }}</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-if="loading">
              <TableCell colspan="6" class="text-center py-8">
                <div class="flex items-center justify-center gap-2">
                  <Loader2 class="w-4 h-4 animate-spin" />
                  {{ $t('common.loading') }}
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-else-if="users.length === 0">
              <TableCell
                colspan="6"
                class="text-center py-8 text-muted-foreground"
              >
                {{ $t('users.no_users') }}
              </TableCell>
            </TableRow>
            <TableRow v-for="user in users" :key="user.id">
              <TableCell>
                <div class="flex items-center gap-3">
                  <Avatar>
                    <AvatarFallback>{{ user.name.charAt(0) }}</AvatarFallback>
                  </Avatar>
                  <div>
                    <div class="font-medium">{{ user.name }}</div>
                    <div class="text-sm text-muted-foreground">
                      {{ user.email }}
                    </div>
                  </div>
                </div>
              </TableCell>
              <TableCell>
                <span v-if="user.department">{{ user.department?.name }}</span>
                <span v-else class="text-muted-foreground">—</span>
              </TableCell>
              <TableCell>
                <span v-if="user.position">{{ user.position?.name }}</span>
                <span v-else class="text-muted-foreground">—</span>
              </TableCell>
              <TableCell>
                <div class="flex gap-1 flex-wrap">
                  <Badge
                    v-for="role in user.roles"
                    :key="role.id"
                    :variant="getRoleVariant(role.name)"
                  >
                    {{ role.name }}
                  </Badge>
                </div>
              </TableCell>
              <TableCell>
                <Badge :variant="user.is_active ? 'success' : 'destructive'">
                  {{
                    user.is_active ? $t('common.active') : $t('common.inactive')
                  }}
                </Badge>
              </TableCell>
              <TableCell>
                <Button variant="ghost" size="sm" @click="viewUser(user)">
                  <Eye class="w-4 h-4" />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Pagination -->
        <div class="flex justify-between items-center p-4 border-t">
          <div class="text-sm text-muted-foreground">
            {{ $t('common.showing') }} {{ users.length }} {{ $t('common.of') }}
            {{ total }}
          </div>
          <div class="flex gap-2">
            <Button
              variant="outline"
              size="sm"
              :disabled="page <= 1"
              @click="page--"
            >
              {{ $t('common.previous') }}
            </Button>
            <Button
              variant="outline"
              size="sm"
              :disabled="page >= Math.ceil(total / perPage)"
              @click="page++"
            >
              {{ $t('common.next') }}
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- User Detail Modal -->
    <Dialog v-model:open="showUserModal">
      <DialogContent v-if="selectedUser" class="max-w-lg">
        <DialogHeader>
          <div class="flex items-center gap-4">
            <Avatar size="lg">
              <AvatarFallback>{{ selectedUser.name.charAt(0) }}</AvatarFallback>
            </Avatar>
            <div>
              <DialogTitle>{{ selectedUser.name }}</DialogTitle>
              <p class="text-sm text-muted-foreground">
                {{ selectedUser.email }}
              </p>
            </div>
          </div>
        </DialogHeader>

        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <div class="text-sm text-muted-foreground">
                {{ $t('users.department') }}
              </div>
              <div class="font-medium">
                {{ selectedUser.department?.name || '—' }}
              </div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">
                {{ $t('users.position') }}
              </div>
              <div class="font-medium">
                {{ selectedUser.position?.name || '—' }}
              </div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">
                {{ $t('users.manager') }}
              </div>
              <div class="font-medium">
                {{ selectedUser.manager?.name || '—' }}
              </div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">
                {{ $t('users.phone') }}
              </div>
              <div class="font-medium">{{ selectedUser.phone || '—' }}</div>
            </div>
          </div>

          <Separator />

          <div>
            <div class="text-sm text-muted-foreground mb-2">
              {{ $t('users.roles') }}
            </div>
            <div class="flex gap-2">
              <Badge
                v-for="role in selectedUser.roles"
                :key="role.id"
                :variant="getRoleVariant(role.name)"
              >
                {{ role.name }}
              </Badge>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button
            variant="outline"
            class="w-full"
            @click="showUserModal = false"
          >
            {{ $t('common.close') }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { Card, CardContent } from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Badge } from '~/components/ui/badge'
import { Avatar, AvatarFallback } from '~/components/ui/avatar'
import {
  Table,
  TableHeader,
  TableBody,
  TableRow,
  TableHead,
  TableCell,
} from '~/components/ui/table'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '~/components/ui/dialog'
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from '~/components/ui/select'
import { Separator } from '~/components/ui/separator'
import { Search, Eye, Loader2 } from 'lucide-vue-next'

interface User {
  id: string
  name: string
  email: string
  phone?: string
  department?: { id: string; name: string }
  position?: { id: string; name: string }
  manager?: { id: string; name: string }
  roles: { id: string; name: string }[]
  is_active: boolean
}

definePageMeta({
  layout: 'default',
})

const { $api } = useNuxtApp()
const { t: $t } = useI18n()

const users = ref<User[]>([])
const departments = ref<{ id: string; name: string }[]>([])
const loading = ref(false)
const search = ref('')
const page = ref(1)
const perPage = ref(15)
const total = ref(0)
const showUserModal = ref(false)
const selectedUser = ref<User | null>(null)

const filters = ref({
  department_id: 'all' as string,
  is_active: 'all' as string,
})

type BadgeVariant =
  | 'default'
  | 'secondary'
  | 'destructive'
  | 'outline'
  | 'success'
  | 'warning'
  | 'info'

const getRoleVariant = (role: string): BadgeVariant => {
  const variants: Record<string, BadgeVariant> = {
    'super-admin': 'destructive',
    admin: 'warning',
    manager: 'info',
    employee: 'success',
  }
  return variants[role] || 'secondary'
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (search.value) params.append('search', search.value)
    if (filters.value.department_id && filters.value.department_id !== 'all')
      params.append('department_id', filters.value.department_id)
    if (filters.value.is_active && filters.value.is_active !== 'all')
      params.append('is_active', filters.value.is_active)
    params.append('page', String(page.value))
    params.append('per_page', String(perPage.value))

    const response = (await $api(`/users?${params.toString()}`)) as {
      data: User[]
      meta?: { total?: number }
    }
    users.value = response.data
    total.value = response.meta?.total || response.data.length
  } catch (error) {
    console.error('Failed to fetch users:', error)
  } finally {
    loading.value = false
  }
}

const fetchDepartments = async () => {
  try {
    const response = (await $api('/departments')) as {
      data: { id: string; name: string }[]
    }
    departments.value = response.data
  } catch (error) {
    console.error('Failed to fetch departments:', error)
  }
}

const viewUser = async (user: User) => {
  try {
    const response = (await $api(`/users/${user.id}`)) as { data: User }
    selectedUser.value = response.data
    showUserModal.value = true
  } catch (error) {
    console.error('Failed to fetch user details:', error)
  }
}

// Watch for filter changes
watch([search, filters, page], fetchUsers, { deep: true })

onMounted(async () => {
  await Promise.all([fetchUsers(), fetchDepartments()])
})
</script>
