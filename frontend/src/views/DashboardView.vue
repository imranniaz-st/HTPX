<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <StatCard title="Total Servers" :value="stats.total_servers" />
      <StatCard title="Online" :value="stats.online_servers" class="text-green-600" />
      <StatCard title="Offline" :value="stats.offline_servers" class="text-red-600" />
      <StatCard
        title="Critical Alerts"
        :value="stats.critical_alerts"
        class="text-red-600"
      />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="card">
        <h2 class="text-lg font-semibold mb-4">Recent Alerts</h2>
        <div class="space-y-2">
          <div v-for="alert in recentAlerts" :key="alert.id" class="flex justify-between p-2 hover:bg-gray-50 rounded">
            <div>
              <p class="text-sm font-medium">{{ alert.title }}</p>
              <p class="text-xs text-gray-500">{{ alert.server.name }}</p>
            </div>
            <span :class="`badge badge-${alert.severity}`">{{ alert.severity }}</span>
          </div>
        </div>
      </div>

      <div class="card">
        <h2 class="text-lg font-semibold mb-4">System Metrics</h2>
        <div class="space-y-4">
          <div>
            <div class="flex justify-between mb-1">
              <span class="text-sm">Avg CPU Usage</span>
              <span class="text-sm font-semibold">{{ stats.avg_cpu_usage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-blue-600 h-2 rounded-full"
                :style="{ width: stats.avg_cpu_usage + '%' }"
              ></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between mb-1">
              <span class="text-sm">Avg Memory Usage</span>
              <span class="text-sm font-semibold">{{ stats.avg_memory_usage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-purple-600 h-2 rounded-full"
                :style="{ width: stats.avg_memory_usage + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { dashboardAPI, alertAPI } from '@/services/api-client'
import StatCard from '@/components/StatCard.vue'

const stats = ref({
  total_servers: 0,
  online_servers: 0,
  offline_servers: 0,
  critical_alerts: 0,
  avg_cpu_usage: 0,
  avg_memory_usage: 0,
})

const recentAlerts = ref([])

onMounted(async () => {
  try {
    const statsResponse = await dashboardAPI.getStats()
    stats.value = statsResponse.data

    const alertsResponse = await alertAPI.getAll({ is_resolved: false })
    recentAlerts.value = alertsResponse.data.data.slice(0, 5)
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
  }
})
</script>
