<template>
  <div class="max-w-7xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Admin — Users & Logs</h1>

    <section class="mb-8">
      <h2 class="text-lg font-semibold mb-2">Users</h2>
      <table class="w-full table-auto border">
        <thead>
          <tr>
            <th class="p-2">ID</th>
            <th class="p-2">Name</th>
            <th class="p-2">Email</th>
            <th class="p-2">Role</th>
            <th class="p-2">Active</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users.data" :key="u.id">
            <td class="p-2">{{ u.id }}</td>
            <td class="p-2">{{ u.name }}</td>
            <td class="p-2">{{ u.email }}</td>
            <td class="p-2">{{ u.role }}</td>
            <td class="p-2">{{ u.is_active ? 'Yes' : 'No' }}</td>
          </tr>
        </tbody>
      </table>
    </section>

    <section>
      <h2 class="text-lg font-semibold mb-2">Server Logs</h2>
      <div class="mb-4">
        <label class="mr-2">Server ID:</label>
        <input v-model="filterServer" type="text" class="input inline-block" placeholder="optional server id" />
        <button @click="fetchLogs" class="btn btn-primary ml-2">Filter</button>
      </div>

      <table class="w-full table-auto border">
        <thead>
          <tr>
            <th class="p-2">ID</th>
            <th class="p-2">Server ID</th>
            <th class="p-2">Level</th>
            <th class="p-2">Message</th>
            <th class="p-2">Time</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in logs.data" :key="log.id">
            <td class="p-2">{{ log.id }}</td>
            <td class="p-2">{{ log.server_id }}</td>
            <td class="p-2">{{ log.level }}</td>
            <td class="p-2">{{ log.message }}</td>
            <td class="p-2">{{ log.created_at }}</td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { adminAPI } from '@/services/api-client'
import { useAuthStore } from '@/stores/authStore'

export default {
  setup() {
    const auth = useAuthStore()
    const users = ref({ data: [] })
    const logs = ref({ data: [] })
    const filterServer = ref('')

    const fetchUsers = async () => {
      const res = await adminAPI.getUsers(1, 200)
      users.value = res.data
    }

    const fetchLogs = async () => {
      const serverId = filterServer.value || null
      const res = await adminAPI.getLogs(1, 200, serverId)
      logs.value = res.data
    }

    onMounted(async () => {
      if (!auth.isAdmin) {
        // redirect to dashboard if not admin
        window.location.href = '/dashboard'
        return
      }
      await fetchUsers()
      await fetchLogs()
    })

    return { users, logs, filterServer, fetchLogs }
  },
}
</script>

<style scoped>
.input { padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px }
.btn { padding: 6px 10px; border-radius: 4px }
.btn-primary { background: #2563eb; color: white }
</style>
