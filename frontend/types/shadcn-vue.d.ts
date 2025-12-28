// Type augmentation for Shadcn Vue / Radix Vue components
// NOTE: We use Shadcn Vue (https://www.shadcn-vue.com/), NOT Nuxt UI
// This helps with strict TypeScript checking in templates

import type { DefineComponent } from 'vue'

// Extend Vue template types to be more permissive with slot scopes
declare module 'vue' {
  interface ComponentCustomProperties {
    // Allow dynamic property access on slot-scoped variables
    [key: string]: any
  }
}

export {}
