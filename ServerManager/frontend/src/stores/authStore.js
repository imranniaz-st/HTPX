import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authAPI } from '@/services/api-client'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  const login = async (email, password) => {
    const response = await authAPI.login(email, password)
    user.value = response.data.user
    token.value = response.data.token
    localStorage.setItem('token', token.value)
    return response.data
  }

  const register = async (name, email, password, passwordConfirmation) => {
    const response = await authAPI.register(name, email, password, passwordConfirmation)
    user.value = response.data.user
    token.value = response.data.token
    localStorage.setItem('token', token.value)
    return response.data
  }

  const logout = async () => {
    try {
      await authAPI.logout()
    } catch (error) {
      console.error('Logout error:', error)
    }
    user.value = null
    token.value = null
    localStorage.removeItem('token')
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    login,
    register,
    logout,
  }
})
