import { ref } from 'vue'

const toasts = ref([])

export const useToast = () => {
  const addToast = (toast) => {
    const id = Date.now() + Math.random()
    toasts.value.push({
      id,
      type: 'info',
      ...toast
    })
    
    if (toast.duration !== 0) {
      setTimeout(() => {
        removeToast(id)
      }, toast.duration || 3000)
    }
    
    return id
  }
  
  const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }
  
  const success = (message, title = 'Success') => {
    return addToast({ type: 'success', message, title })
  }
  
  const error = (message, title = 'Error') => {
    return addToast({ type: 'error', message, title })
  }
  
  const info = (message, title = 'Info') => {
    return addToast({ type: 'info', message, title })
  }
  
  const warning = (message, title = 'Warning') => {
    return addToast({ type: 'warning', message, title })
  }
  
  return {
    toasts,
    addToast,
    removeToast,
    success,
    error,
    info,
    warning
  }
}

