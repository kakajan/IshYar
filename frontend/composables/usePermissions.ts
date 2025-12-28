import { useAuthStore } from '~/stores/auth'

interface Permission {
  name: string
}

interface Role {
  name: string
  permissions?: Permission[]
}

/**
 * Composable for user permission and role checking
 */
export const usePermissions = () => {
  const authStore = useAuthStore()

  /**
   * Check if user has a specific permission
   * @param permission - Permission name
   * @returns True if user has the permission
   */
  const can = (permission: string): boolean => {
    if (!authStore.user) return false
    
    // Check direct permissions
    const permissions = authStore.user.permissions || []
    if (permissions.some((p) => p.name === permission)) {
      return true
    }

    // Check permissions through roles
    const roles = (authStore.user.roles || []) as Role[]
    for (const role of roles) {
      const rolePermissions = role.permissions || []
      if (rolePermissions.some((p) => p.name === permission)) {
        return true
      }
    }

    return false
  }

  /**
   * Check if user has any of the specified permissions
   * @param permissions - Array of permission names
   * @returns True if user has at least one permission
   */
  const canAny = (permissions: string[]): boolean => {
    return permissions.some((permission) => can(permission))
  }

  /**
   * Check if user has all specified permissions
   * @param permissions - Array of permission names
   * @returns True if user has all permissions
   */
  const canAll = (permissions: string[]): boolean => {
    return permissions.every((permission) => can(permission))
  }

  /**
   * Check if user has a specific role
   * @param role - Role name
   * @returns True if user has the role
   */
  const hasRole = (role: string): boolean => {
    if (!authStore.user) return false
    const roles = authStore.user.roles || []
    return roles.some((r: any) => r.name === role)
  }

  /**
   * Check if user has any of the specified roles
   * @param roles - Array of role names
   * @returns True if user has at least one role
   */
  const hasAnyRole = (roles: string[]): boolean => {
    return roles.some((role) => hasRole(role))
  }

  /**
   * Check if user is an admin
   * @returns True if user has admin role
   */
  const isAdmin = computed(() => hasRole('admin') || hasRole('super-admin'))

  /**
   * Check if user is a manager
   * @returns True if user has manager role
   */
  const isManager = computed(() => hasRole('manager') || isAdmin.value)

  return {
    can,
    canAny,
    canAll,
    hasRole,
    hasAnyRole,
    isAdmin,
    isManager,
  }
}
