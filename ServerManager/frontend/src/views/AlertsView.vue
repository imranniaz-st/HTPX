<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold">Alerts</h1>

    <div class="card">
      <div class="flex space-x-4 mb-4">
        <button
          @click="filterResolved = null"
          :class="[
            'btn btn-small',
            filterResolved === null ? 'btn-primary' : 'btn-secondary',
          ]"
        >
          All
        </button>
        <button
          @click="filterResolved = false"
          :class="[
            'btn btn-small',
            filterResolved === false ? 'btn-primary' : 'btn-secondary',
          ]"
        >
          Active
        </button>
        <button
          @click="filterResolved = true"
          :class="[
            'btn btn-small',
            filterResolved === true ? 'btn-primary' : 'btn-secondary',
          ]"
        >
          Resolved
        </button>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th>Server</th>
            <th>Title</th>
            <th>Severity</th>
            <th>Message</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="alert in filteredAlerts" :key="alert.id">
            <td>{{ alert.server?.name }}</td>
            <td>{{ alert.title }}</td>
            <td>
              <span :class="`badge badge-${alert.severity}`">{{ alert.severity }}</span>
            </td>
            <td class="text-sm">{{ alert.message }}</td>
            <td class="text-sm">{{ formatDate(alert.created_at) }}</td>
            <td>
              <button
                v-if="!alert.is_resolved"
                @click="resolveAlert(alert.id)"
                class="btn btn-small btn-primary"
              >
                Resolve
              </button>
              <span v-else class="text-gray-500 text-sm">Resolved</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAlertStore } from '@/stores/alertStore'
import { formatDistanceToNow } from 'date-fns'

const alertStore = useAlertStore()
const filterResolved = ref(null)

const filteredAlerts = computed(() => {
  if (filterResolved.value === null) {
    return alertStore.alerts
  }
  return alertStore.alerts.filter((a) => a.is_resolved === filterResolved.value)
})

const formatDate = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const resolveAlert = async (id) => {
  await alertStore.resolveAlert(id)
}

onMounted(() => {
  alertStore.fetchAlerts()
})
</script>
