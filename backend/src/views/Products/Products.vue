<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Products</h1>
    <button
      type="submit"
      @click="showProductModal"
      class="flex justify-center py-2 px-4 border border-transparent font-medium rounded-md text-white text-sm bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Add new Product
    </button>
  </div>

  <ProductModal v-model="showModal" :product="productModel" />
  <ProductsTable />
</template>

<script setup>
import ProductsTable from "./ProductsTable.vue";
import ProductModal from "./ProductModal.vue";
import { ref } from "vue";

const showModal = ref(false);
const productModel = ref({
  id: "",
  title: "",
  image: "",
  description: "",
  price: "",
});

function showProductModal() {
  showModal.value = true;
}
</script>
