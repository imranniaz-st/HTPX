import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { alertAPI } from '@/services/api-client'

export const useAlertStore = defineStore('alerts', () => {
  const alerts = ref([])
  const alertRules = ref([])
  const loading = ref(false)

  const fetchAlerts = async (filters = {}) => {
    loading.value = true
    try {
      const response = await alertAPI.getAll(filters)
      alerts.value = response.data.data || response.data
    } finally {
      loading.value = false
    }
  }

  const resolveAlert = async (id) => {
    const response = await alertAPI.resolve(id)
    const index = alerts.value.findIndex((a) => a.id === id)
    if (index !== -1) {
      alerts.value[index] = response.data
    }
  }

  const fetchAlertRules = async () => {
    const response = await alertAPI.getRules()
    alertRules.value = response.data.data || response.data
  }

  const createAlertRule = async (data) => {
    const response = await alertAPI.createRule(data)
    alertRules.value.push(response.data)
    return response.data
  }

  const deleteAlertRule = async (id) => {
    await alertAPI.deleteRule(id)
    alertRules.value = alertRules.value.filter((r) => r.id !== id)
  }

  const unresolved = computed(() => alerts.value.filter((a) => !a.is_resolved))
  const criticalAlerts = computed(() =>
    alerts.value.filter((a) => a.severity === 'critical' && !a.is_resolved)
  )

  return {
    alerts,
    alertRules,
    loading,
    fetchAlerts,
    resolveAlert,
    fetchAlertRules,
    createAlertRule,
    deleteAlertRule,
    unresolved,
    criticalAlerts,
  }
})
