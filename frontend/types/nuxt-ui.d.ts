// Type augmentation for Nuxt UI components
// This helps with strict TypeScript checking in templates

import type { DefineComponent } from 'vue'

declare module '@nuxt/ui' {
  // Allow any row type in table slots
  interface TableSlotProps<T = any> {
    row: T
  }
}

// Extend Vue template types to be more permissive with slot scopes
declare module 'vue' {
  interface ComponentCustomProperties {
    // Allow dynamic property access on slot-scoped variables
    [key: string]: any
  }
}

export {}
