/**
 * Composable for department operations with API
 */
export interface Department {
  id: string
  name: string
  description?: string
  code?: string
  parent_id?: string
  organization_id: string
  head_id?: string
  settings?: Record<string, any>
  is_active: boolean
  parent?: Department
  head?: {
    id: string
    name: string
    email: string
    avatar?: string
  }
  children?: Department[]
  users_count?: number
  created_at: string
  updated_at: string
}

export const useDepartments = () => {
  const { $api } = useNuxtApp()
  
  const departments = ref<Department[]>([])
  const currentDepartment = ref<Department | null>(null)
  const departmentTree = ref<Department[]>([])
  const isLoading = ref(false)
  const error = ref<Error | null>(null)
  
  const fetchDepartments = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: Department[] }>('/departments')
      departments.value = response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      console.error('Failed to fetch departments:', err)
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchDepartmentTree = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: Department[] }>('/departments/tree')
      departmentTree.value = response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      console.error('Failed to fetch department tree:', err)
    } finally {
      isLoading.value = false
    }
  }
  
  const fetchDepartment = async (id: string) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: Department }>(`/departments/${id}`)
      currentDepartment.value = response.data
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      return null
    } finally {
      isLoading.value = false
    }
  }
  
  const createDepartment = async (data: Partial<Department>) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: Department; message: string }>('/departments', {
        method: 'POST',
        body: data,
      })
      departments.value.push(response.data)
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      throw err
    } finally {
      isLoading.value = false
    }
  }
  
  const updateDepartment = async (id: string, data: Partial<Department>) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await $api<{ data: Department; message: string }>(`/departments/${id}`, {
        method: 'PUT',
        body: data,
      })
      // Update local state
      const index = departments.value.findIndex(d => d.id === id)
      if (index !== -1) {
        departments.value[index] = response.data
      }
      currentDepartment.value = response.data
      return response.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      throw err
    } finally {
      isLoading.value = false
    }
  }
  
  const deleteDepartment = async (id: string) => {
    try {
      await $api(`/departments/${id}`, { method: 'DELETE' })
      departments.value = departments.value.filter(d => d.id !== id)
      return true
    } catch (err) {
      console.error('Failed to delete department:', err)
      return false
    }
  }
  
  // Helper to get flat list from tree
  const flattenTree = (tree: Department[]): Department[] => {
    const result: Department[] = []
    const traverse = (nodes: Department[], depth = 0) => {
      for (const node of nodes) {
        result.push({ ...node, _depth: depth } as any)
        if (node.children?.length) {
          traverse(node.children, depth + 1)
        }
      }
    }
    traverse(tree)
    return result
  }
  
  return {
    departments,
    currentDepartment,
    departmentTree,
    isLoading,
    error,
    fetchDepartments,
    fetchDepartmentTree,
    fetchDepartment,
    createDepartment,
    updateDepartment,
    deleteDepartment,
    flattenTree,
  }
}
