<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Products</h1>
    <button
      type="submit"
      class="flex justify-center py-2 px-4 border border-transparent font-medium rounded-md text-white text-sm bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Add new Product
    </button>
  </div>

  <div class="bg-white shadow p-4 rounded-lg">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          @change="getProducts()"
          v-model="perPage"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="30">30</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div>
        <input
          v-model="search"
          @change="getProducts()"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          placeholder="Type to Search Products"
        />
      </div>
    </div>

    <Spinner v-if="products.loading" />
    <template v-else>
      <table class="table-auto w-full">
        <thead>
          <tr>
            <th class="border-b-2 p-2 text-left">ID</th>
            <th class="border-b-2 p-2 text-left">Image</th>
            <th class="border-b-2 p-2 text-left">Title</th>
            <th class="border-b-2 p-2 text-left">Price</th>
            <th class="border-b-2 p-2 text-left">Last Updated At</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="product in products.data">
            <td class="border-b p-2">{{ product.id }}</td>
            <td class="border-b p-2">
              <img :src="product.image" :alt="product.title" class="w-6" />
            </td>
            <td
              class="border-b p-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px]"
            >
              {{ product.title }}
            </td>
            <td class="border-b p-2">{{ product.price }}</td>
            <td class="border-b p-2">{{ product.updated_at }}</td>
          </tr>
        </tbody>
      </table>

      <div class="flex justify-between items-center mt-5">
        <span> Showing from {{ products.from }} to {{ products.to }} </span>

        <nav
          v-if="products.total > products.limit"
          class="relative z-0 inline-flex justify-center rounded-md shadow-md -space-x-px"
          aria-label="Pagination"
        >
          <a
            href="#"
            @click="getCurrentPage($event, link)"
            v-html="link.label"
            :disabled="!link.url"
            v-for="(link, ind) in products.links"
            :key="ind"
            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
            :class="[
              link.active
                ? 'z-10 bg-indigo-50 border-indigo-600 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              ind === 0 ? 'rounded-l-md' : '',
              ind === products.links.length - 1 ? 'rounded-r-md' : '',
              !link.url ? 'bg-gray-100 text-gray-700' : '',
            ]"
            aria-current="page"
          ></a>
        </nav>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import store from "../store";
import Spinner from "../components/core/Spinner.vue";
import { PRODUCTS_PER_PAGE } from "../constants.js";

const perPage = ref(PRODUCTS_PER_PAGE);
const search = ref("");
const products = computed(() => store.state.products);

onMounted(() => {
  getProducts();
});

function getProducts() {
  store.dispatch("getProducts", {
    search: search.value,
    perPage: perPage.value,
  });
}

function getCurrentPage(e, link) {
  e.preventDefault();
  if (!link.url || link.active) {
    return;
  }
  store.dispatch("getProducts", { url: link.url });
}
</script>
