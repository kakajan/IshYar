<script setup lang="ts">
import { ref, reactive } from 'vue'
import { Loader2, ArrowLeft } from 'lucide-vue-next'
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Label } from '~/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '~/components/ui/card'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '~/components/ui/select'

definePageMeta({
  layout: 'default',
})

const { t } = useI18n()
const router = useRouter()
const { $api } = useNuxtApp()
const { add: addToast } = useToast()

const isLoading = ref(false)
const departments = ref<any[]>([])

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  department_id: '',
  role: 'employee'
})

// Fetch departments on mount
onMounted(async () => {
    try {
        const res = await $api<{ data: any[] }>('/departments');
        departments.value = res.data;
    } catch (e) {
        console.error(e);
    }
})

const handleSubmit = async () => {
  isLoading.value = true
  try {
    await $api('/users', {
      method: 'POST',
      body: form
    })
    
    addToast({
      title: t('common.success'),
      description: t('users.create_success'),
      variant: 'default',
    })
    
    router.push('/users')
  } catch (error: any) {
    addToast({
      title: t('common.error'),
      description: error.data?.message || t('common.error_occurred'),
      variant: 'destructive',
    })
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto p-6">
    <Button variant="ghost" class="mb-6 pl-0 hover:bg-transparent" @click="router.back()">
      <ArrowLeft class="mr-2 h-4 w-4" />
      {{ t('common.back') }}
    </Button>

    <Card>
      <CardHeader>
        <CardTitle>{{ t('users.create_new') }}</CardTitle>
        <CardDescription>{{ t('users.create_subtitle') }}</CardDescription>
      </CardHeader>
      <form @submit.prevent="handleSubmit">
        <CardContent class="space-y-4">
          <div class="space-y-2">
            <Label for="name">{{ t('users.name') }}</Label>
            <Input id="name" v-model="form.name" required />
          </div>
          
          <div class="space-y-2">
            <Label for="email">{{ t('users.email') }}</Label>
            <Input id="email" type="email" v-model="form.email" required />
          </div>

          <div class="grid grid-cols-2 gap-4">
               <div class="space-y-2">
                <Label for="password">{{ t('users.password') }}</Label>
                <Input id="password" type="password" v-model="form.password" required />
              </div>
              
              <div class="space-y-2">
                <Label for="confirm_password">{{ t('users.confirm_password') }}</Label>
                <Input id="confirm_password" type="password" v-model="form.password_confirmation" required />
              </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label>{{ t('users.department') }}</Label>
                 <Select v-model="form.department_id">
                    <SelectTrigger>
                      <SelectValue :placeholder="t('users.select_department')" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="dept in departments" :key="dept.id" :value="dept.id">
                        {{ dept.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
              </div>
              
              <div class="space-y-2">
                 <Label>{{ t('users.role') }}</Label>
                  <Select v-model="form.role">
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="employee">{{ t('users.roles.employee') }}</SelectItem>
                      <SelectItem value="manager">{{ t('users.roles.manager') }}</SelectItem>
                      <SelectItem value="admin">{{ t('users.roles.admin') }}</SelectItem>
                    </SelectContent>
                  </Select>
              </div>
          </div>

        </CardContent>
        <CardFooter class="justify-end">
          <Button type="submit" :disabled="isLoading">
            <Loader2 v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
            {{ t('common.create') }}
          </Button>
        </CardFooter>
      </form>
    </Card>
  </div>
</template>
