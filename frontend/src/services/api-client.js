import api from './api'

export const serverAPI = {
  getAll(page = 1) {
    return api.get(`/servers?page=${page}`)
  },

  getById(id) {
    return api.get(`/servers/${id}`)
  },

  create(data) {
    return api.post('/servers', data)
  },

  update(id, data) {
    return api.put(`/servers/${id}`, data)
  },

  delete(id) {
    return api.delete(`/servers/${id}`)
  },

  getMetrics(id, hours = 24) {
    return api.get(`/servers/${id}/metrics/history?hours=${hours}`)
  },

  getLatestMetric(id) {
    return api.get(`/servers/${id}/metrics`)
  },
}

export const firewallAPI = {
  getRules(serverId, page = 1) {
    return api.get(`/servers/${serverId}/firewall-rules?page=${page}`)
  },

  addRule(serverId, data) {
    return api.post(`/servers/${serverId}/firewall-rules`, data)
  },

  updateRule(serverId, ruleId, data) {
    return api.put(`/servers/${serverId}/firewall-rules/${ruleId}`, data)
  },

  deleteRule(serverId, ruleId) {
    return api.delete(`/servers/${serverId}/firewall-rules/${ruleId}`)
  },
}

export const alertAPI = {
  getAll(filters = {}) {
    return api.get('/alerts', { params: filters })
  },

  resolve(id) {
    return api.put(`/alerts/${id}/resolve`)
  },

  getRules(page = 1) {
    return api.get(`/alert-rules?page=${page}`)
  },

  createRule(data) {
    return api.post('/alert-rules', data)
  },

  updateRule(id, data) {
    return api.put(`/alert-rules/${id}`, data)
  },

  deleteRule(id) {
    return api.delete(`/alert-rules/${id}`)
  },
}

export const dashboardAPI = {
  getStats() {
    return api.get('/dashboard/stats')
  },

  getAlertsSummary() {
    return api.get('/dashboard/alerts-summary')
  },
}

export const userAPI = {
  getServerUsers(serverId) {
    return api.get(`/servers/${serverId}/users`)
  },

  changePassword(serverId, username, password) {
    return api.post(`/servers/${serverId}/users/${username}/change-password`, {
      password,
    })
  },

  getProfile() {
    return api.get('/profile')
  },

  updateProfile(data) {
    return api.put('/profile', data)
  },

  changePassword(data) {
    return api.put('/profile/password', data)
  },
}

export const logAPI = {
  getLogs(serverId, filters = {}) {
    return api.get(`/servers/${serverId}/logs`, { params: filters })
  },

  getLog(serverId, logId) {
    return api.get(`/servers/${serverId}/logs/${logId}`)
  },

  createLog(serverId, data) {
    return api.post(`/servers/${serverId}/logs`, data)
  },

  downloadLogs(serverId) {
    return api.get(`/servers/${serverId}/logs/export/csv`, {
      responseType: 'blob',
    })
  },

  clearOldLogs(serverId, days = 30) {
    return api.delete(`/servers/${serverId}/logs/cleanup?days=${days}`)
  },
}

export const authAPI = {
  login(email, password) {
    return api.post('/auth/login', { email, password })
  },

  register(name, email, password, passwordConfirmation) {
    return api.post('/auth/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })
  },

  logout() {
    return api.post('/auth/logout')
  },
}

export const adminAPI = {
  getUsers(page = 1, perPage = 50) {
    return api.get(`/admin/users?page=${page}&per_page=${perPage}`)
  },

  getLogs(page = 1, perPage = 50, serverId = null) {
    const params = new URLSearchParams()
    params.append('page', page)
    params.append('per_page', perPage)
    if (serverId) params.append('server_id', serverId)
    return api.get(`/admin/logs?${params.toString()}`)
  },
}
