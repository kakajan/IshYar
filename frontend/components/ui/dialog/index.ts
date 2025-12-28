export { default as Dialog } from './Dialog.vue'

// Re-export reka-ui components for more granular usage
export {
  DialogRoot,
  DialogTrigger,
  DialogPortal,
  DialogOverlay,
  DialogContent,
  DialogTitle,
  DialogDescription,
  DialogClose,
} from 'reka-ui'

// Create simplified wrapper components
export const DialogHeader = {
  name: 'DialogHeader',
  template: '<div class="flex flex-col space-y-1.5 text-center sm:text-left"><slot /></div>'
}

export const DialogFooter = {
  name: 'DialogFooter',
  template: '<div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2"><slot /></div>'
}
