<template>
  <div class="space-y-6">
    <div v-if="loading" class="text-center py-8">
      <span class="spinner"></span>
    </div>

    <div v-else class="space-y-6">
      <!-- Header -->
      <div class="card">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold">{{ server.name }}</h1>
            <p class="text-gray-600 mt-1">{{ server.description }}</p>
          </div>
          <div class="flex space-x-2">
            <span :class="`badge badge-${server.status === 'online' ? 'success' : 'danger'}`">
              {{ server.status }}
            </span>
            <router-link
              :to="`/servers/${server.id}/edit`"
              class="btn btn-secondary btn-small"
            >
              Edit
            </router-link>
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
          <div>
            <p class="text-xs text-gray-500">IP Address</p>
            <p class="font-mono font-semibold">{{ server.ip_address }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500">Hostname</p>
            <p class="font-mono">{{ server.hostname }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500">OS Type</p>
            <p class="font-semibold">{{ server.os_type }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500">Last Heartbeat</p>
            <p class="text-sm">{{ server.last_heartbeat ? formatDate(server.last_heartbeat) : 'Never' }}</p>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="card">
        <div class="flex space-x-4 border-b border-gray-200">
          <button
            v-for="tab in tabs"
            :key="tab"
            @click="activeTab = tab"
            :class="[
              'px-4 py-2 font-medium text-sm border-b-2',
              activeTab === tab
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            ]"
          >
            {{ tab }}
          </button>
        </div>

        <!-- Metrics Tab -->
        <div v-if="activeTab === 'Metrics'" class="p-4">
          <div v-if="currentMetric" class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="metric-card">
              <p class="text-sm text-gray-600">CPU Usage</p>
              <p class="text-2xl font-bold">{{ currentMetric.cpu_usage }}%</p>
            </div>
            <div class="metric-card">
              <p class="text-sm text-gray-600">Memory</p>
              <p class="text-2xl font-bold">{{ memoryPercentage }}%</p>
              <p class="text-xs text-gray-500">{{ currentMetric.memory_usage }}MB / {{ currentMetric.memory_total }}MB</p>
            </div>
            <div class="metric-card">
              <p class="text-sm text-gray-600">Disk</p>
              <p class="text-2xl font-bold">{{ diskPercentage }}%</p>
              <p class="text-xs text-gray-500">{{ formatBytes(currentMetric.disk_usage) }} / {{ formatBytes(currentMetric.disk_total) }}</p>
            </div>
            <div class="metric-card">
              <p class="text-sm text-gray-600">Load Average</p>
              <p class="text-2xl font-bold">{{ currentMetric.load_average }}</p>
            </div>
          </div>
          <p v-else class="text-gray-500">No metrics available</p>
        </div>

        <!-- Firewall Tab -->
        <div v-if="activeTab === 'Firewall'" class="p-4">
          <div class="flex justify-between mb-4">
            <h3 class="text-lg font-semibold">Firewall Rules</h3>
            <button class="btn btn-primary btn-small">+ Add Rule</button>
          </div>
          <table class="table w-full text-sm">
            <thead>
              <tr>
                <th>Name</th>
                <th>Direction</th>
                <th>Action</th>
                <th>Protocol</th>
                <th>Port</th>
                <th>Enabled</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="rule in firewallRules" :key="rule.id">
                <td>{{ rule.name }}</td>
                <td>{{ rule.direction }}</td>
                <td>
                  <span :class="`badge badge-${rule.action === 'allow' ? 'success' : 'danger'}`">
                    {{ rule.action }}
                  </span>
                </td>
                <td>{{ rule.protocol }}</td>
                <td>{{ rule.port || '-' }}</td>
                <td>
                  <input type="checkbox" :checked="rule.is_enabled" />
                </td>
                <td>
                  <button class="btn btn-danger btn-small text-xs">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Logs Tab -->
        <div v-if="activeTab === 'Logs'" class="p-4">
          <div class="space-y-4">
            <div class="flex justify-between">
              <div class="flex space-x-2">
                <button class="btn btn-secondary btn-small">📥 Download</button>
                <button class="btn btn-danger btn-small">🗑️ Clear Old</button>
              </div>
              <router-link :to="`/servers/${server.id}/logs`" class="btn btn-primary btn-small">
                View All Logs
              </router-link>
            </div>

            <table class="table w-full text-sm">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Type</th>
                  <th>Level</th>
                  <th>Message</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="log in recentLogs" :key="log.id">
                  <td class="text-xs">{{ formatDate(log.timestamp) }}</td>
                  <td><span class="badge">{{ log.type }}</span></td>
                  <td><span class="badge" :class="`badge-${log.level === 'error' ? 'danger' : 'info'}`">{{ log.level }}</span></td>
                  <td class="truncate">{{ log.message || log.title || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Alerts Tab -->
        <div v-if="activeTab === 'Alerts'" class="p-4">
          <div class="space-y-3">
            <button class="btn btn-primary">+ Create Alert Rule</button>
            <table class="table w-full text-sm">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Metric</th>
                  <th>Threshold</th>
                  <th>Severity</th>
                  <th>Enabled</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="alert in alertRules" :key="alert.id">
                  <td>{{ alert.name }}</td>
                  <td>{{ alert.metric_type }}</td>
                  <td>{{ alert.operator }} {{ alert.threshold }}</td>
                  <td>{{ alert.severity }}</td>
                  <td><input type="checkbox" :checked="alert.is_enabled" /></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { serverAPI, logAPI } from '@/services/api-client'
import { formatDistanceToNow } from 'date-fns'

const route = useRoute()
const serverId = route.params.id

const server = ref({})
const loading = ref(false)
const activeTab = ref('Metrics')
const currentMetric = ref(null)
const firewallRules = ref([])
const recentLogs = ref([])
const alertRules = ref([])

const tabs = ['Metrics', 'Firewall', 'Logs', 'Alerts']

const memoryPercentage = computed(() => {
  if (!currentMetric.value || currentMetric.value.memory_total === 0) return 0
  return Math.round((currentMetric.value.memory_usage / currentMetric.value.memory_total) * 100)
})

const diskPercentage = computed(() => {
  if (!currentMetric.value || currentMetric.value.disk_total === 0) return 0
  return Math.round((currentMetric.value.disk_usage / currentMetric.value.disk_total) * 100)
})

const formatDate = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const fetchServerDetails = async () => {
  loading.value = true
  try {
    const response = await serverAPI.getById(serverId)
    server.value = response.data
    currentMetric.value = response.data.metrics?.[0]
    firewallRules.value = response.data.firewall_rules || []
    alertRules.value = response.data.alert_rules || []

    // Fetch logs
    const logsResponse = await logAPI.getLogs(serverId, { days: 7 })
    recentLogs.value = logsResponse.data.data?.slice(0, 10) || []
  } catch (error) {
    console.error('Failed to load server details:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchServerDetails()
})
</script>
