<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '~/components/ui/card'
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Badge } from '~/components/ui/badge'
import { Label } from '~/components/ui/label'
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
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
} from '~/components/ui/dropdown-menu'
import {
  Plus,
  LayoutGrid,
  List,
  Building2,
  MoreVertical,
  Pencil,
  Trash2,
} from 'lucide-vue-next'

interface Department {
  id: string
  name: string
  slug: string
  code?: string
  description?: string
  head?: { name: string }
  is_active: boolean
  parent_id?: string | null
  children?: Department[]
}

definePageMeta({
  layout: 'default',
})

const { $api } = useNuxtApp()
const { t: $t } = useI18n()

const viewMode = ref<'tree' | 'list'>('tree')
const allDepartments = ref<Department[]>([])
const rootDepartments = ref<Department[]>([])
const showCreateModal = ref(false)
const editingDepartment = ref<Department | null>(null)
const saving = ref(false)

const form = ref({
  name: '',
  slug: '',
  code: '',
  parent_id: null as string | null,
  description: '',
  is_active: true,
})

const fetchDepartments = async () => {
  try {
    const [allRes, treeRes] = (await Promise.all([
      $api('/departments'),
      $api('/departments/tree'),
    ])) as [{ data: Department[] }, { data: Department[] }]
    allDepartments.value = allRes.data
    rootDepartments.value = treeRes.data
  } catch (error) {
    console.error('Failed to fetch departments:', error)
  }
}

const editDepartment = (dept: Department) => {
  editingDepartment.value = dept
  form.value = {
    name: dept.name,
    slug: dept.slug,
    code: dept.code || '',
    parent_id: dept.parent_id || null,
    description: dept.description || '',
    is_active: dept.is_active,
  }
  showCreateModal.value = true
}

const saveDepartment = async () => {
  saving.value = true
  try {
    if (editingDepartment.value) {
      await $api(`/departments/${editingDepartment.value.id}`, {
        method: 'PUT',
        body: form.value,
      })
    } else {
      await $api('/departments', {
        method: 'POST',
        body: form.value,
      })
    }
    closeModal()
    await fetchDepartments()
  } catch (error) {
    console.error('Failed to save department:', error)
  } finally {
    saving.value = false
  }
}

const confirmDelete = async (dept: Department) => {
  if (confirm($t('departments.confirm_delete'))) {
    try {
      await $api(`/departments/${dept.id}`, { method: 'DELETE' })
      await fetchDepartments()
    } catch (error) {
      console.error('Failed to delete department:', error)
    }
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingDepartment.value = null
  form.value = {
    name: '',
    slug: '',
    code: '',
    parent_id: null,
    description: '',
    is_active: true,
  }
}

onMounted(fetchDepartments)
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-foreground">
          {{ $t('departments.title') }}
        </h1>
        <p class="text-muted-foreground">
          {{ $t('departments.description') }}
        </p>
      </div>
      <Button @click="showCreateModal = true">
        <Plus class="w-4 h-4 mr-2" />
        {{ $t('departments.create') }}
      </Button>
    </div>

    <!-- Department Tree/List -->
    <Card>
      <CardHeader class="flex flex-row items-center justify-between pb-2">
        <CardTitle class="text-lg">
          {{ $t('departments.tree_view') }}
        </CardTitle>
        <div class="flex gap-1">
          <Button
            :variant="viewMode === 'tree' ? 'default' : 'outline'"
            size="sm"
            @click="viewMode = 'tree'"
          >
            <LayoutGrid class="w-4 h-4" />
          </Button>
          <Button
            :variant="viewMode === 'list' ? 'default' : 'outline'"
            size="sm"
            @click="viewMode = 'list'"
          >
            <List class="w-4 h-4" />
          </Button>
        </div>
      </CardHeader>

      <CardContent>
        <!-- Tree View -->
        <div v-if="viewMode === 'tree'" class="space-y-2">
          <DepartmentNode
            v-for="dept in rootDepartments"
            :key="dept.id"
            :department="dept"
            :level="0"
            @edit="editDepartment"
            @delete="confirmDelete"
          />
          <div
            v-if="rootDepartments.length === 0"
            class="text-center py-8 text-muted-foreground"
          >
            {{ $t('departments.no_departments') }}
          </div>
        </div>

        <!-- List View -->
        <Table v-if="viewMode === 'list'">
          <TableHeader>
            <TableRow>
              <TableHead>{{ $t('departments.name') }}</TableHead>
              <TableHead>{{ $t('departments.code') }}</TableHead>
              <TableHead>{{ $t('departments.head') }}</TableHead>
              <TableHead>{{ $t('common.status') }}</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="dept in allDepartments" :key="dept.id">
              <TableCell>
                <div class="flex items-center gap-2">
                  <Building2 class="w-4 h-4 text-primary" />
                  <span class="font-medium">{{ dept.name }}</span>
                  <Badge v-if="dept.code" variant="secondary">{{
                    dept.code
                  }}</Badge>
                </div>
              </TableCell>
              <TableCell>{{ dept.code || '—' }}</TableCell>
              <TableCell>
                <span v-if="dept.head">{{ dept.head.name }}</span>
                <span v-else class="text-muted-foreground">—</span>
              </TableCell>
              <TableCell>
                <Badge :variant="dept.is_active ? 'success' : 'destructive'">
                  {{
                    dept.is_active ? $t('common.active') : $t('common.inactive')
                  }}
                </Badge>
              </TableCell>
              <TableCell>
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="sm">
                      <MoreVertical class="w-4 h-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent>
                    <DropdownMenuItem @select="editDepartment(dept)">
                      <Pencil class="w-4 h-4 mr-2" />
                      {{ $t('common.edit') }}
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                      class="text-destructive"
                      @select="confirmDelete(dept)"
                    >
                      <Trash2 class="w-4 h-4 mr-2" />
                      {{ $t('common.delete') }}
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>

    <!-- Create/Edit Modal -->
    <Dialog v-model:open="showCreateModal">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>
            {{
              editingDepartment
                ? $t('departments.edit')
                : $t('departments.create')
            }}
          </DialogTitle>
        </DialogHeader>

        <form class="space-y-4" @submit.prevent="saveDepartment">
          <div class="space-y-2">
            <Label for="name">{{ $t('departments.name') }} *</Label>
            <Input
              id="name"
              v-model="form.name"
              :placeholder="$t('departments.name_placeholder')"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="slug">{{ $t('departments.slug') }} *</Label>
            <Input
              id="slug"
              v-model="form.slug"
              :placeholder="$t('departments.slug_placeholder')"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="code">{{ $t('departments.code') }}</Label>
            <Input
              id="code"
              v-model="form.code"
              :placeholder="$t('departments.code_placeholder')"
            />
          </div>

          <div class="space-y-2">
            <Label>{{ $t('departments.parent') }}</Label>
            <Select v-model="form.parent_id">
              <SelectTrigger>
                <SelectValue :placeholder="$t('departments.select_parent')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem :value="null">{{
                  $t('departments.no_parent')
                }}</SelectItem>
                <SelectItem
                  v-for="d in allDepartments"
                  :key="d.id"
                  :value="d.id"
                >
                  {{ d.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="space-y-2">
            <Label for="description">{{ $t('departments.description') }}</Label>
            <Textarea id="description" v-model="form.description" :rows="3" />
          </div>

          <div class="flex items-center space-x-2">
            <Checkbox id="is_active" v-model:checked="form.is_active" />
            <Label for="is_active" class="font-normal">{{
              $t('common.active')
            }}</Label>
          </div>

          <DialogFooter>
            <Button variant="outline" type="button" @click="closeModal">
              {{ $t('common.cancel') }}
            </Button>
            <Button type="submit" :loading="saving">
              {{ $t('common.save') }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>
