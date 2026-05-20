<template>
  <div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Add Server</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block font-semibold">Name</label>
        <input v-model="form.name" class="input w-full" required />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold">IP Address</label>
          <input v-model="form.ip_address" class="input w-full" required />
        </div>
        <div>
          <label class="block font-semibold">Hostname</label>
          <input v-model="form.hostname" class="input w-full" />
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block font-semibold">OS Type</label>
          <select v-model="form.os_type" class="input w-full">
            <option value="linux">Linux</option>
            <option value="windows">Windows</option>
            <option value="bsd">BSD</option>
          </select>
        </div>
        <div>
          <label class="block font-semibold">SSH Port</label>
          <input type="number" v-model.number="form.ssh_port" class="input w-full" />
        </div>
        <div>
          <label class="block font-semibold">SSH Username</label>
          <input v-model="form.ssh_username" class="input w-full" />
        </div>
      </div>

      <div>
        <label class="block font-semibold">Description</label>
        <textarea v-model="form.description" class="input w-full" rows="4"></textarea>
      </div>

      <div class="flex space-x-2">
        <button type="submit" class="btn btn-primary">Create</button>
        <router-link to="/servers" class="btn btn-secondary">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useServerStore } from '@/stores/serverStore'

const router = useRouter()
const store = useServerStore()

const form = reactive({
  name: '',
  ip_address: '',
  hostname: '',
  os_type: 'linux',
  ssh_port: 22,
  ssh_username: 'ubuntu',
  description: '',
})

const submit = async () => {
  try {
    const created = await store.addServer(form)
    router.push(`/servers/${created.id}`)
  } catch (err) {
    console.error('Failed to create server', err)
    alert('Failed to create server: ' + (err?.response?.data?.message || err.message))
  }
}
</script>

<style scoped>
.input { padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px }
.btn { padding: 6px 10px; border-radius: 4px }
.btn-primary { background: #2563eb; color: white }
.btn-secondary { background: #e5e7eb; }
</style>
