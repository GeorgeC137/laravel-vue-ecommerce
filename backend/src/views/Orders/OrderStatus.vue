<template>
  <span
    class="text-white p-1 rounded"
    :class="{
      'bg-emerald-500': ['paid', 'complete'].includes(order.status),
      'bg-red-500': order.status === 'cancelled',
      'bg-gray-400': order.status === 'unpaid',
      'bg-orange-400': order.status === 'shipped',
    }"
    >{{ order.status }}</span
  >
</template>

<script setup>
const { order } = defineProps({
  order: Object,
});
</script>
