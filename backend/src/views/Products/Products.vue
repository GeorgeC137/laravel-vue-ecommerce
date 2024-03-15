<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Products</h1>
    <router-link
      :to="{ name: 'app.products.create' }"
      type="submit"
      class="flex justify-center py-2 px-4 border border-transparent font-medium rounded-md text-white text-sm bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Add new Product
    </router-link>
  </div>

  <ProductsTable />
</template>

<script setup>
import ProductsTable from "./ProductsTable.vue";
</script>
