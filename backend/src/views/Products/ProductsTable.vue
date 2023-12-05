<template>
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

    <table class="table-auto w-full">
      <thead>
        <tr>
          <TableHeaderCell
            @click="sortProducts"
            class="border-b-2 p-2 text-left"
            field="id"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >ID</TableHeaderCell
          >
          <TableHeaderCell
            class="border-b-2 p-2 text-left"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Image</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortProducts"
            class="border-b-2 p-2 text-left"
            field="title"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Title</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortProducts"
            class="border-b-2 p-2 text-left"
            field="price"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Price</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortProducts"
            class="border-b-2 p-2 text-left"
            field="updated_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Last Updated At</TableHeaderCell
          >
        </tr>
      </thead>

      <tbody v-if="products.loading">
        <tr>
          <td colspan="5">
            <Spinner class="my-4" />
          </td>
        </tr>
      </tbody>

      <tbody v-else>
        <tr v-for="product in products.data">
          <td class="border-b p-2">{{ product.id }}</td>
          <td class="border-b p-2">
            <img :src="product.image ? product.image : 'https://picsum.photos/200' " :alt="product.title" class="w-6" />
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

    <div v-if="!products.loading" class="flex justify-between items-center mt-5">
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import store from "../../store";
import Spinner from "../../components/core/Spinner.vue";
import TableHeaderCell from "../../components/core/Table/TableHeaderCell.vue";
import { PRODUCTS_PER_PAGE } from "../../constants";

const perPage = ref(PRODUCTS_PER_PAGE);
const search = ref("");
const products = computed(() => store.state.products);
const sortField = ref("updated_at");
const sortDirection = ref("desc");

onMounted(() => {
  getProducts();
});

function getProducts() {
  store.dispatch("getProducts", {
    search: search.value,
    perPage: perPage.value,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
  });
}

function getCurrentPage(e, link) {
  e.preventDefault();
  if (!link.url || link.active) {
    return;
  }
  store.dispatch("getProducts", { url: link.url });
}

function sortProducts(field) {
  if (sortField.value === field) {
    if (sortDirection.value === "asc") {
      sortDirection.value = "desc";
    } else {
      sortDirection.value = "asc";
    }
  } else {
    sortField.value = field;
    sortDirection.value = "asc";
  }

  getProducts();
}
</script>
