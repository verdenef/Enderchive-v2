<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen">
        <div
          class="fixed inset-0 z-[9998]"
          style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px)"
          @click="$emit('close')"
        />
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div
            class="w-full max-w-lg rounded-xl"
            style="background: rgba(26, 26, 46, 0.98); backdrop-filter: blur(12px); border: 2px solid rgba(124, 58, 237, 0.5); box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
            @click.stop
          >
            <div class="flex items-center justify-between p-6 border-b border-[#2b1d3a]">
              <div class="flex items-center gap-2">
                <Icon name="lucide:download" class="w-5 h-5 text-[#22c55e]" />
                <h2>Export Data</h2>
              </div>
              <button
                @click="$emit('close')"
                class="p-2 hover:bg-white/10 rounded transition-colors"
              >
                <Icon name="lucide:x" class="w-5 h-5 text-gray-400 hover:text-white" />
              </button>
            </div>

            <div class="p-6 space-y-6">
              <!-- Format Selection -->
              <div>
                <label class="block text-sm mb-3">Export Format</label>
                <div class="grid grid-cols-3 gap-3">
                  <button
                    v-for="fmt in formats"
                    :key="fmt.id"
                    @click="format = fmt.id"
                    :class="[
                      'p-4 rounded-lg border-2 transition-all',
                      format === fmt.id ? 'border-[#7c3aed]' : 'border-[#2b1d3a] hover:border-[#7c3aed]/50'
                    ]"
                    :style="{
                      background: format === fmt.id ? 'rgba(124, 58, 237, 0.2)' : 'rgba(27, 22, 38, 0.5)'
                    }"
                  >
                    <Icon :name="fmt.icon" class="w-6 h-6 mx-auto mb-2 text-[#8b5cf6]" />
                    <p class="text-xs font-semibold text-white mb-1">{{ fmt.label }}</p>
                    <p class="text-xs text-white/50">{{ fmt.description }}</p>
                  </button>
                </div>
              </div>

              <!-- Data Selection -->
              <div>
                <label class="block text-sm mb-3">Select Data to Export</label>
                <div class="space-y-2">
                  <label
                    v-for="data in dataTypes"
                    :key="data.id"
                    :class="[
                      'flex items-start gap-3 p-3 rounded-lg border transition-all cursor-pointer',
                      selectedData.includes(data.id) ? 'border-[#7c3aed]' : 'border-[#2b1d3a] hover:border-[#7c3aed]'
                    ]"
                    :style="{
                      background: selectedData.includes(data.id) ? 'rgba(124, 58, 237, 0.1)' : 'rgba(27, 22, 38, 0.5)'
                    }"
                  >
                    <input
                      type="checkbox"
                      :checked="selectedData.includes(data.id)"
                      @change="handleToggleData(data.id)"
                      class="mt-1"
                    />
                    <div class="flex-1">
                      <p class="text-sm font-semibold text-white">{{ data.label }}</p>
                      <p class="text-xs text-white/50">{{ data.description }}</p>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-4 justify-end pt-4">
                <GameButton variant="secondary" @click="$emit('close')" :disabled="isExporting">
                  Cancel
                </GameButton>
                <GameButton
                  variant="primary"
                  @click="handleExport"
                  :disabled="isExporting || selectedData.length === 0"
                >
                  <template #icon>
                    <Icon v-if="isExporting" name="lucide:loader-2" class="w-4 h-4 animate-spin" />
                    <Icon v-else name="lucide:download" class="w-4 h-4" />
                  </template>
                  {{ isExporting ? 'Exporting...' : 'Export Data' }}
                </GameButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'
import Icon from '../ui/Icon.vue'
import GameButton from '../buttons/GameButton.vue'
import { useToast } from '../../Composables/useToast'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'export'])

const toast = useToast()
const format = ref('json')
const selectedData = ref(['games', 'achievements', 'playtime', 'reviews'])
const isExporting = ref(false)

const formats = [
  { id: 'pdf', label: 'PDF', icon: 'lucide:file-text', description: 'Formatted document' },
  { id: 'json', label: 'JSON', icon: 'lucide:file-json', description: 'Raw data format' },
  { id: 'csv', label: 'CSV', icon: 'lucide:file-spreadsheet', description: 'Spreadsheet format' }
]

const dataTypes = [
  { id: 'games', label: 'Games Library', description: 'All games in your library' },
  { id: 'achievements', label: 'Achievements', description: 'All unlocked achievements' },
  { id: 'playtime', label: 'Playtime Records', description: 'Playtime history and stats' },
  { id: 'reviews', label: 'Reviews', description: 'All your game reviews' },
  { id: 'friends', label: 'Friends List', description: 'Your friends and connections' },
  { id: 'journal', label: 'Journal Entries', description: 'All journal entries' }
]

const handleToggleData = (dataId) => {
  if (selectedData.value.includes(dataId)) {
    selectedData.value = selectedData.value.filter(id => id !== dataId)
  } else {
    selectedData.value.push(dataId)
  }
}

const generateExportData = () => {
  const exportData = {
    exportedAt: new Date().toISOString(),
    username: 'user',
    data: {}
  }

  selectedData.value.forEach(dataType => {
    switch (dataType) {
      case 'games':
        exportData.data.games = {
          library: [],
          totalGames: 0
        }
        break
      case 'achievements':
        exportData.data.achievements = []
        break
      case 'playtime':
        exportData.data.playtime = {
          totalHours: 0,
          averagePerDay: 0,
          history: []
        }
        break
      case 'reviews':
        exportData.data.reviews = []
        break
      case 'friends':
        exportData.data.friends = []
        break
      case 'journal':
        exportData.data.journal = []
        break
    }
  })

  return exportData
}

const downloadFile = (content, filename, mimeType) => {
  const blob = new Blob([content], { type: mimeType })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

const convertToCSV = (data) => {
  let csv = 'Data Type,Key,Value\n'
  
  Object.entries(data.data).forEach(([dataType, content]) => {
    if (Array.isArray(content)) {
      content.forEach((item, index) => {
        Object.entries(item).forEach(([key, value]) => {
          csv += `${dataType},${key},${JSON.stringify(value)}\n`
        })
      })
    } else if (typeof content === 'object') {
      Object.entries(content).forEach(([key, value]) => {
        if (Array.isArray(value)) {
          value.forEach((item, index) => {
            if (typeof item === 'object') {
              Object.entries(item).forEach(([subKey, subValue]) => {
                csv += `${dataType}.${key},${subKey},${JSON.stringify(subValue)}\n`
              })
            } else {
              csv += `${dataType},${key}[${index}],${JSON.stringify(item)}\n`
            }
          })
        } else {
          csv += `${dataType},${key},${JSON.stringify(value)}\n`
        }
      })
    }
  })
  
  return csv
}

const handleExport = async () => {
  if (selectedData.value.length === 0) {
    toast.error('Error', 'Please select at least one data type to export')
    return
  }

  isExporting.value = true
  
  setTimeout(() => {
    const exportData = generateExportData()
    const timestamp = new Date().toISOString().split('T')[0]
    
    if (format.value === 'json') {
      const jsonContent = JSON.stringify(exportData, null, 2)
      downloadFile(jsonContent, `enderchive-export-${timestamp}.json`, 'application/json')
    } else if (format.value === 'csv') {
      const csvContent = convertToCSV(exportData)
      downloadFile(csvContent, `enderchive-export-${timestamp}.csv`, 'text/csv')
    } else if (format.value === 'pdf') {
      const textContent = `Enderchive Data Export\n\nExported: ${exportData.exportedAt}\nUser: ${exportData.username}\n\n${JSON.stringify(exportData.data, null, 2)}`
      downloadFile(textContent, `enderchive-export-${timestamp}.txt`, 'text/plain')
      toast.info('Info', 'PDF export created as text file. For full PDF support, a PDF library would be needed.')
    }
    
    isExporting.value = false
    emit('export', format.value, selectedData.value)
    emit('close')
    toast.success('Success', 'Data exported successfully!')
  }, 1500)
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>

