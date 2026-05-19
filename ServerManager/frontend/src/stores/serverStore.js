import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { serverAPI } from '@/services/api-client'

export const useServerStore = defineStore('servers', () => {
  const servers = ref([])
  const loading = ref(false)
  const currentServer = ref(null)

  const fetchServers = async () => {
    loading.value = true
    try {
      const response = await serverAPI.getAll()
      servers.value = response.data.data
    } finally {
      loading.value = false
    }
  }

  const fetchServer = async (id) => {
    loading.value = true
    try {
      const response = await serverAPI.getById(id)
      currentServer.value = response.data
      return response.data
    } finally {
      loading.value = false
    }
  }

  const addServer = async (data) => {
    const response = await serverAPI.create(data)
    servers.value.push(response.data)
    return response.data
  }

  const updateServer = async (id, data) => {
    const response = await serverAPI.update(id, data)
    const index = servers.value.findIndex((s) => s.id === id)
    if (index !== -1) {
      servers.value[index] = response.data
    }
    return response.data
  }

  const deleteServer = async (id) => {
    await serverAPI.delete(id)
    servers.value = servers.value.filter((s) => s.id !== id)
  }

  const onlineServers = computed(() => servers.value.filter((s) => s.status === 'online'))
  const offlineServers = computed(() => servers.value.filter((s) => s.status === 'offline'))

  return {
    servers,
    loading,
    currentServer,
    fetchServers,
    fetchServer,
    addServer,
    updateServer,
    deleteServer,
    onlineServers,
    offlineServers,
  }
})
