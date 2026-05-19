<template>
  <div class="min-h-screen bg-gray-50">
    <nav v-if="isAuthenticated" class="bg-white shadow-md border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/dashboard" class="text-2xl font-bold text-blue-600">
              Server Manager
            </router-link>
            <div class="hidden md:flex ml-8 space-x-1">
              <router-link
                to="/dashboard"
                class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100"
              >
                Dashboard
              </router-link>
              <router-link
                to="/servers"
                class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100"
              >
                Servers
              </router-link>
              <router-link
                to="/alerts"
                class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100"
              >
                Alerts
              </router-link>
              <router-link
                to="/settings"
                class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100"
              >
                Settings
              </router-link>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">{{ authStore.user?.name }}</span>
            <button
              @click="logout"
              class="px-3 py-2 rounded-md text-sm font-medium text-red-600 hover:bg-red-50"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <router-view />
    </main>
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
