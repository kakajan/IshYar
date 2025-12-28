<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardFooter,
} from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Label } from '~/components/ui/label'
import { Badge } from '~/components/ui/badge'
import { Checkbox } from '~/components/ui/checkbox'
import { Textarea } from '~/components/ui/textarea'
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
import {
  Table,
  TableHeader,
  TableBody,
  TableRow,
  TableHead,
  TableCell,
} from '~/components/ui/table'
import { Plus, Pencil, Trash2, Loader2 } from 'lucide-vue-next'

interface Position {
  id: string | number
  title: string
  code?: string
  description?: string
  department?: { id: string | number; name: string }
  department_id?: number | null
  is_active: boolean
  users_count?: number
}

definePageMeta({
  layout: 'default',
})

const { t } = useI18n()
const toast = useToast()

const isLoading = ref(true)
const positions = ref<Position[]>([])
const meta = ref<{ current_page: number; last_page: number; total: number }>({
  current_page: 1,
  last_page: 1,
  total: 0,
})

// Create position modal
const isCreateModalOpen = ref(false)
const isCreating = ref(false)
const createForm = reactive({
  title: '',
  code: '',
  description: '',
  department_id: null as number | null,
  is_active: true,
})

// Edit position modal
const isEditModalOpen = ref(false)
const isEditing = ref(false)
const editingPosition = ref<Position | null>(null)
const editForm = reactive({
  title: '',
  code: '',
  description: '',
  department_id: null as number | null,
  is_active: true,
})

// Delete confirmation
const isDeleteModalOpen = ref(false)
const isDeleting = ref(false)
const deletingPosition = ref<Position | null>(null)

// Departments for select
const departments = ref<{ id: number; name: string }[]>([])

const fetchPositions = async (page = 1) => {
  isLoading.value = true
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{
      data: Position[]
      meta: { current_page: number; last_page: number; total: number }
    }>(`/positions?page=${page}`)
    positions.value = response.data
    meta.value = response.meta
  } catch (error) {
    toast.add({
      title: t('common.error'),
      description: t('messages.load_error'),
      color: 'error',
    })
  } finally {
    isLoading.value = false
  }
}

const fetchDepartments = async () => {
  try {
    const { $api } = useNuxtApp()
    const response = await $api<{ data: { id: number; name: string }[] }>(
      '/departments'
    )
    departments.value = response.data
  } catch (error) {
    console.error('Failed to load departments', error)
  }
}

const openCreateModal = () => {
  createForm.title = ''
  createForm.code = ''
  createForm.description = ''
  createForm.department_id = null
  createForm.is_active = true
  isCreateModalOpen.value = true
}

const handleCreate = async () => {
  isCreating.value = true
  try {
    const { $api } = useNuxtApp()
    await $api('/positions', {
      method: 'POST',
      body: createForm,
    })
    toast.add({
      title: t('common.success'),
      description: t('messages.create_success'),
      color: 'success',
    })
    isCreateModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || t('messages.create_error'),
      color: 'error',
    })
  } finally {
    isCreating.value = false
  }
}

const openEditModal = (position: Position) => {
  editingPosition.value = position
  editForm.title = position.title
  editForm.code = position.code || ''
  editForm.description = position.description || ''
  editForm.department_id = position.department_id || null
  editForm.is_active = position.is_active
  isEditModalOpen.value = true
}

const handleEdit = async () => {
  if (!editingPosition.value) return
  isEditing.value = true
  try {
    const { $api } = useNuxtApp()
    await $api(`/positions/${editingPosition.value.id}`, {
      method: 'PUT',
      body: editForm,
    })
    toast.add({
      title: t('common.success'),
      description: t('messages.update_success'),
      color: 'success',
    })
    isEditModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || t('messages.update_error'),
      color: 'error',
    })
  } finally {
    isEditing.value = false
  }
}

const openDeleteModal = (position: Position) => {
  deletingPosition.value = position
  isDeleteModalOpen.value = true
}

const handleDelete = async () => {
  if (!deletingPosition.value) return
  isDeleting.value = true
  try {
    const { $api } = useNuxtApp()
    await $api(`/positions/${deletingPosition.value.id}`, {
      method: 'DELETE',
    })
    toast.add({
      title: t('common.success'),
      description: t('messages.delete_success'),
      color: 'success',
    })
    isDeleteModalOpen.value = false
    fetchPositions(meta.value.current_page)
  } catch (error: any) {
    toast.add({
      title: t('common.error'),
      description: error.data?.message || t('messages.delete_error'),
      color: 'error',
    })
  } finally {
    isDeleting.value = false
  }
}

onMounted(() => {
  fetchPositions()
  fetchDepartments()
})
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-foreground">
          {{ t('positions.title') }}
        </h1>
        <p class="text-muted-foreground">{{ t('positions.description') }}</p>
      </div>
      <Button @click="openCreateModal">
        <Plus class="w-4 h-4 mr-2" />
        {{ t('positions.create') }}
      </Button>
    </div>

    <!-- Positions Table -->
    <Card>
      <CardContent class="p-0">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>{{ t('positions.position_title') }}</TableHead>
              <TableHead>{{ t('positions.code') }}</TableHead>
              <TableHead>{{ t('positions.department') }}</TableHead>
              <TableHead>{{ t('common.status') }}</TableHead>
              <TableHead>{{ t('positions.users_count') }}</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-if="isLoading">
              <TableCell colspan="6" class="text-center py-8">
                <Loader2 class="w-6 h-6 animate-spin mx-auto text-primary" />
              </TableCell>
            </TableRow>
            <TableRow v-else-if="positions.length === 0">
              <TableCell
                colspan="6"
                class="text-center py-8 text-muted-foreground"
              >
                {{ t('positions.no_positions') }}
              </TableCell>
            </TableRow>
            <TableRow v-for="position in positions" :key="position.id">
              <TableCell class="font-medium">{{ position.title }}</TableCell>
              <TableCell>{{ position.code || '—' }}</TableCell>
              <TableCell>{{ position.department?.name || '—' }}</TableCell>
              <TableCell>
                <Badge
                  :variant="position.is_active ? 'success' : 'destructive'"
                >
                  {{
                    position.is_active
                      ? t('common.active')
                      : t('common.inactive')
                  }}
                </Badge>
              </TableCell>
              <TableCell>
                <Badge variant="secondary">{{
                  position.users_count || 0
                }}</Badge>
              </TableCell>
              <TableCell>
                <div class="flex gap-2 justify-end">
                  <Button
                    variant="ghost"
                    size="sm"
                    @click="openEditModal(position)"
                  >
                    <Pencil class="w-4 h-4" />
                  </Button>
                  <Button
                    variant="ghost"
                    size="sm"
                    class="text-destructive"
                    @click="openDeleteModal(position)"
                  >
                    <Trash2 class="w-4 h-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>

      <CardFooter class="flex items-center justify-between border-t p-4">
        <p class="text-sm text-muted-foreground">
          {{ t('common.showing') }} {{ meta.current_page }}
          {{ t('common.of') }} {{ meta.last_page }} ({{ meta.total }}
          {{ t('tasks.total') }})
        </p>
        <div class="flex gap-2">
          <Button
            variant="outline"
            size="sm"
            :disabled="meta.current_page <= 1"
            @click="fetchPositions(meta.current_page - 1)"
          >
            {{ t('common.previous') }}
          </Button>
          <Button
            variant="outline"
            size="sm"
            :disabled="meta.current_page >= meta.last_page"
            @click="fetchPositions(meta.current_page + 1)"
          >
            {{ t('common.next') }}
          </Button>
        </div>
      </CardFooter>
    </Card>

    <!-- Create Modal -->
    <Dialog v-model:open="isCreateModalOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ t('positions.create') }}</DialogTitle>
        </DialogHeader>

        <form class="space-y-4" @submit.prevent="handleCreate">
          <div class="space-y-2">
            <Label for="create-title"
              >{{ t('positions.position_title') }} *</Label
            >
            <Input
              id="create-title"
              v-model="createForm.title"
              :placeholder="t('positions.title_placeholder')"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="create-code">{{ t('positions.code') }}</Label>
            <Input
              id="create-code"
              v-model="createForm.code"
              :placeholder="t('positions.code_placeholder')"
            />
          </div>

          <div class="space-y-2">
            <Label>{{ t('positions.department') }}</Label>
            <Select v-model="createForm.department_id">
              <SelectTrigger>
                <SelectValue :placeholder="t('positions.select_department')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem :value="null">{{ t('common.none') }}</SelectItem>
                <SelectItem
                  v-for="dept in departments"
                  :key="dept.id"
                  :value="dept.id"
                >
                  {{ dept.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="space-y-2">
            <Label for="create-description">{{
              t('departments.description')
            }}</Label>
            <Textarea
              id="create-description"
              v-model="createForm.description"
              :rows="3"
            />
          </div>

          <div class="flex items-center space-x-2">
            <Checkbox
              id="create-active"
              v-model:checked="createForm.is_active"
            />
            <Label for="create-active" class="font-normal">{{
              t('common.active')
            }}</Label>
          </div>

          <DialogFooter>
            <Button
              variant="outline"
              type="button"
              @click="isCreateModalOpen = false"
            >
              {{ t('common.cancel') }}
            </Button>
            <Button type="submit" :disabled="isCreating">
              <Loader2 v-if="isCreating" class="w-4 h-4 mr-2 animate-spin" />
              {{ t('common.create') }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Edit Modal -->
    <Dialog v-model:open="isEditModalOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ t('positions.edit') }}</DialogTitle>
        </DialogHeader>

        <form class="space-y-4" @submit.prevent="handleEdit">
          <div class="space-y-2">
            <Label for="edit-title"
              >{{ t('positions.position_title') }} *</Label
            >
            <Input
              id="edit-title"
              v-model="editForm.title"
              :placeholder="t('positions.title_placeholder')"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="edit-code">{{ t('positions.code') }}</Label>
            <Input
              id="edit-code"
              v-model="editForm.code"
              :placeholder="t('positions.code_placeholder')"
            />
          </div>

          <div class="space-y-2">
            <Label>{{ t('positions.department') }}</Label>
            <Select v-model="editForm.department_id">
              <SelectTrigger>
                <SelectValue :placeholder="t('positions.select_department')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem :value="null">{{ t('common.none') }}</SelectItem>
                <SelectItem
                  v-for="dept in departments"
                  :key="dept.id"
                  :value="dept.id"
                >
                  {{ dept.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="space-y-2">
            <Label for="edit-description">{{
              t('departments.description')
            }}</Label>
            <Textarea
              id="edit-description"
              v-model="editForm.description"
              :rows="3"
            />
          </div>

          <div class="flex items-center space-x-2">
            <Checkbox id="edit-active" v-model:checked="editForm.is_active" />
            <Label for="edit-active" class="font-normal">{{
              t('common.active')
            }}</Label>
          </div>

          <DialogFooter>
            <Button
              variant="outline"
              type="button"
              @click="isEditModalOpen = false"
            >
              {{ t('common.cancel') }}
            </Button>
            <Button type="submit" :disabled="isEditing">
              <Loader2 v-if="isEditing" class="w-4 h-4 mr-2 animate-spin" />
              {{ t('common.save') }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Modal -->
    <Dialog v-model:open="isDeleteModalOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle class="text-destructive">{{
            t('confirm.delete_title')
          }}</DialogTitle>
        </DialogHeader>

        <p class="text-muted-foreground">{{ t('positions.confirm_delete') }}</p>

        <DialogFooter>
          <Button variant="outline" @click="isDeleteModalOpen = false">
            {{ t('common.cancel') }}
          </Button>
          <Button
            variant="destructive"
            :disabled="isDeleting"
            @click="handleDelete"
          >
            <Loader2 v-if="isDeleting" class="w-4 h-4 mr-2 animate-spin" />
            {{ t('common.delete') }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
