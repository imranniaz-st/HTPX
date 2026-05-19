<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">Server Logs</h1>
      <div class="flex space-x-2">
        <button
          @click="downloadLogs"
          class="btn btn-secondary"
        >
          Download CSV
        </button>
        <button
          @click="showClearModal = true"
          class="btn btn-danger"
        >
          Clear Old
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="card p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Type</label>
          <select v-model="filters.type" class="input text-sm">
            <option value="">All Types</option>
            <option value="system">System</option>
            <option value="ssh">SSH</option>
            <option value="firewall">Firewall</option>
            <option value="alert">Alert</option>
            <option value="password_change">Password Change</option>
            <option value="reboot">Reboot</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Level</label>
          <select v-model="filters.level" class="input text-sm">
            <option value="">All Levels</option>
            <option value="debug">Debug</option>
            <option value="info">Info</option>
            <option value="warning">Warning</option>
            <option value="error">Error</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Days</label>
          <select v-model="filters.days" class="input text-sm">
            <option value="1">Last 24h</option>
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="">All</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="applyFilters" class="btn btn-primary w-full">
            Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Logs Table -->
    <div v-if="loading" class="text-center py-8">
      <span class="spinner"></span>
    </div>

    <div v-else class="card">
      <table class="table w-full text-sm">
        <thead>
          <tr>
            <th>Time</th>
            <th>Type</th>
            <th>Level</th>
            <th>Title</th>
            <th>Message</th>
            <th>User</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in logs" :key="log.id" :class="getLevelClass(log.level)">
            <td class="text-xs">{{ formatDate(log.timestamp) }}</td>
            <td>
              <span class="badge" :class="`badge-${getTypeBadgeClass(log.type)}`">
                {{ log.type }}
              </span>
            </td>
            <td>
              <span class="badge" :class="`badge-${getLevelBadgeClass(log.level)}`">
                {{ log.level }}
              </span>
            </td>
            <td class="font-medium">{{ log.title || '-' }}</td>
            <td class="truncate max-w-xs">{{ log.message || log.command || '-' }}</td>
            <td class="text-xs">{{ log.user?.name || 'System' }}</td>
            <td>
              <button
                @click="selectedLog = log"
                class="btn btn-primary btn-small text-xs"
              >
                View
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="logs.length === 0" class="text-center py-8 text-gray-500">
        No logs found
      </div>
    </div>

    <!-- Log Detail Modal -->
    <div v-if="selectedLog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">{{ selectedLog.title || 'Log Details' }}</h2>
          <button @click="selectedLog = null" class="text-2xl">×</button>
        </div>

        <div class="space-y-3">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="font-semibold text-sm">Timestamp:</label>
              <p class="text-sm">{{ formatDate(selectedLog.timestamp) }}</p>
            </div>
            <div>
              <label class="font-semibold text-sm">Type:</label>
              <p class="text-sm">{{ selectedLog.type }}</p>
            </div>
            <div>
              <label class="font-semibold text-sm">Level:</label>
              <p class="text-sm">{{ selectedLog.level }}</p>
            </div>
            <div>
              <label class="font-semibold text-sm">User:</label>
              <p class="text-sm">{{ selectedLog.user?.name || 'System' }}</p>
            </div>
          </div>

          <div v-if="selectedLog.message">
            <label class="font-semibold text-sm">Message:</label>
            <p class="text-sm bg-gray-100 p-2 rounded">{{ selectedLog.message }}</p>
          </div>

          <div v-if="selectedLog.command">
            <label class="font-semibold text-sm">Command:</label>
            <pre class="text-xs bg-gray-900 text-green-400 p-2 rounded overflow-x-auto">{{ selectedLog.command }}</pre>
          </div>

          <div v-if="selectedLog.output">
            <label class="font-semibold text-sm">Output:</label>
            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto max-h-32">{{ selectedLog.output }}</pre>
          </div>

          <div v-if="selectedLog.error">
            <label class="font-semibold text-sm">Error:</label>
            <pre class="text-xs bg-red-100 text-red-800 p-2 rounded overflow-x-auto">{{ selectedLog.error }}</pre>
          </div>
        </div>
      </div>
    </div>

    <!-- Clear Logs Modal -->
    <div v-if="showClearModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold mb-4">Clear Old Logs</h2>
        <p class="text-sm text-gray-600 mb-4">
          This will delete logs older than <strong>{{ clearDays }} days</strong>. This action cannot be undone.
        </p>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Days to Keep:</label>
          <input v-model.number="clearDays" type="number" class="input" min="1" />
        </div>
        <div class="flex space-x-2">
          <button @click="showClearModal = false" class="btn btn-secondary flex-1">Cancel</button>
          <button @click="clearOldLogs" class="btn btn-danger flex-1">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { logAPI } from '@/services/api-client'
import { formatDistanceToNow } from 'date-fns'

const route = useRoute()
const serverId = route.params.serverId

const logs = ref([])
const loading = ref(false)
const selectedLog = ref(null)
const showClearModal = ref(false)
const clearDays = ref(30)

const filters = ref({
  type: '',
  level: '',
  days: '7',
})

const formatDate = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const getLevelClass = (level) => {
  return {
    error: 'bg-red-50',
    warning: 'bg-yellow-50',
    info: 'bg-blue-50',
    debug: 'bg-gray-50',
  }[level] || ''
}

const getLevelBadgeClass = (level) => {
  return {
    error: 'danger',
    warning: 'warning',
    info: 'info',
    debug: 'secondary',
  }[level] || 'info'
}

const getTypeBadgeClass = (type) => {
  return {
    error: 'danger',
    alert: 'warning',
    system: 'info',
  }[type] || 'info'
}

const fetchLogs = async () => {
  loading.value = true
  try {
    const response = await logAPI.getLogs(serverId, filters.value)
    logs.value = response.data.data
  } catch (error) {
    console.error('Failed to load logs:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchLogs()
}

const downloadLogs = async () => {
  try {
    const response = await logAPI.downloadLogs(serverId)
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `server-logs-${new Date().toISOString().split('T')[0]}.csv`)
    document.body.appendChild(link)
    link.click()
    link.parentNode.removeChild(link)
  } catch (error) {
    console.error('Failed to download logs:', error)
  }
}

const clearOldLogs = async () => {
  try {
    await logAPI.clearOldLogs(serverId, clearDays.value)
    showClearModal.value = false
    fetchLogs()
  } catch (error) {
    console.error('Failed to clear logs:', error)
  }
}

onMounted(() => {
  fetchLogs()
})
</script>
