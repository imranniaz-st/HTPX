<template>
  <div class="card p-4 mb-4">
    <h3 class="text-sm font-semibold text-gray-600">{{ title }}</h3>
    <p class="text-2xl font-bold mt-1">{{ value }}</p>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number],
  icon: String,
})
</script>
