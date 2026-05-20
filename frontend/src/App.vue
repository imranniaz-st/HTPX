<template>
  <div class="min-h-screen bg-gray-50">
    <div v-if="isAuthenticated" class="flex">
      <!-- Sidebar (desktop) -->
      <aside class="hidden md:flex flex-col w-64 bg-white border-r">
        <div class="px-6 py-6 border-b">
          <router-link to="/dashboard" class="text-2xl font-bold text-blue-600">
            Server Manager
          </router-link>
        </div>

        <nav class="px-4 py-6 flex-1 space-y-1">
          <router-link to="/dashboard" class="block px-3 py-2 rounded text-sm hover:bg-gray-100">Dashboard</router-link>
          <router-link to="/servers" class="block px-3 py-2 rounded text-sm hover:bg-gray-100">Servers</router-link>
          <router-link to="/alerts" class="block px-3 py-2 rounded text-sm hover:bg-gray-100">Alerts</router-link>
          <router-link v-if="authStore.isAdmin" to="/admin" class="block px-3 py-2 rounded text-sm hover:bg-gray-100">Admin</router-link>
        </nav>

        <div class="px-4 py-4 border-t">
          <div class="text-sm text-gray-700 mb-2">{{ authStore.user?.name }}</div>
          <button @click="logout" class="w-full text-left px-3 py-2 rounded text-sm text-red-600 hover:bg-red-50">Logout</button>
        </div>
      </aside>

      <!-- Mobile top bar -->
      <header class="md:hidden w-full bg-white border-b">
        <div class="flex items-center justify-between px-4 py-3">
          <router-link to="/dashboard" class="text-lg font-bold text-blue-600">Server Manager</router-link>
          <div class="flex items-center space-x-3">
            <router-link to="/servers" class="text-sm">Servers</router-link>
            <button @click="logout" class="text-sm text-red-600">Logout</button>
          </div>
        </div>
      </header>

      <div class="flex-1">
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <router-view />
        </main>
      </div>
    </div>

    <div v-else>
      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const isAuthenticated = computed(() => authStore.isAuthenticated)

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
