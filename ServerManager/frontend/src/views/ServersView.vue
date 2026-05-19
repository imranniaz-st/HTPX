<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold">Servers</h1>
      <router-link
        to="/servers/add"
        class="btn btn-primary"
      >
        + Add Server
      </router-link>
    </div>

    <div v-if="loading" class="text-center py-8">
      <span class="spinner"></span>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="server in servers" :key="server.id" class="card cursor-pointer hover:shadow-lg">
        <div class="flex justify-between items-start mb-3">
          <h3 class="text-lg font-semibold">{{ server.name }}</h3>
          <span :class="`badge badge-${server.status === 'online' ? 'success' : 'danger'}`">
            {{ server.status }}
          </span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <p><strong>IP:</strong> {{ server.ip_address }}</p>
          <p><strong>OS:</strong> {{ server.os_type }}</p>
          <p v-if="server.metrics?.[0]" class="text-xs">
            <strong>CPU:</strong> {{ server.metrics[0].cpu_usage }}% |
            <strong>Memory:</strong> {{ server.metrics[0].memory_usage }}%
          </p>
        </div>
        <div class="mt-4 flex space-x-2">
          <router-link
            :to="`/servers/${server.id}`"
            class="btn btn-primary btn-small flex-1 text-center"
          >
            View
          </router-link>
          <router-link
            :to="`/servers/${server.id}/edit`"
            class="btn btn-secondary btn-small flex-1 text-center"
          >
            Edit
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useServerStore } from '@/stores/serverStore'

const serverStore = useServerStore()
const { servers, loading, fetchServers } = serverStore

onMounted(() => {
  fetchServers()
})
</script>
