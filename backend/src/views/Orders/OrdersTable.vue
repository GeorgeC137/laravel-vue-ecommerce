<template>
  <div class="bg-white shadow p-4 rounded-lg animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          @change="getOrders()"
          v-model="perPage"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="30">30</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ml-3">Found {{ orders.total }} orders</span>
      </div>
      <div>
        <input
          v-model="search"
          @change="getOrders()"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          placeholder="Type to Search Orders"
        />
      </div>
    </div>

    <table class="table-auto w-full">
      <thead>
        <tr>
          <TableHeaderCell
            @click="sortOrders"
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
            >Customer</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortOrders"
            class="border-b-2 p-2 text-left"
            field="status"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Status</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortOrders"
            class="border-b-2 p-2 text-left"
            field="total_price"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Total</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortOrders"
            class="border-b-2 p-2 text-left"
            field="created_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Date</TableHeaderCell
          >
          <TableHeaderCell field="actions" class="border-b-2 p-2 text-left"
            >Actions</TableHeaderCell
          >
        </tr>
      </thead>

      <tbody v-if="orders.loading || !orders.data.length">
        <tr>
          <td colspan="6">
            <Spinner class="my-4" v-if="orders.loading" />
            <p v-else class="text-gray-700 py-8 text-center">There are no orders</p>
          </td>
        </tr>
      </tbody>

      <tbody v-else>
        <tr v-for="(order, ind) in orders.data">
          <td class="border-b p-2">{{ order.id }}</td>
          <td class="border-b p-2">
            {{ order.customer.first_name }} {{ order.customer.last_name }}
          </td>
          <td class="border-b p-2">
            <span>{{ order.status }}</span>
          </td>
          <td
            class="border-b p-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px]"
          >
            ${{ order.total_price }}
          </td>
          <td class="border-b p-2">{{ order.created_at }}</td>
          <td class="border-b p-2">
            <router-link
              :to="{ name: 'app.orders.view', params: { id: order.id } }"
              class="w-8 h-8 rounded-full border-indigo-700 text-indigo-700 border flex justify-center items-center hover:text-white hover:bg-indigo-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-4 h-4"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                />
              </svg>
            </router-link>
          </td>
        </tr>
      </tbody>
    </table>

    <div
      v-if="!orders.loading && orders.data.length"
      class="flex justify-between items-center mt-5"
    >
      <span> Showing from {{ orders.from }} to {{ orders.to }} </span>

      <nav
        v-if="orders.total > orders.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-md -space-x-px"
        aria-label="Pagination"
      >
        <a
          href="#"
          @click="getCurrentPage($event, link)"
          v-html="link.label"
          :disabled="!link.url"
          v-for="(link, ind) in orders.links"
          :key="ind"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
            link.active
              ? 'z-10 bg-indigo-50 border-indigo-600 text-indigo-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
            ind === 0 ? 'rounded-l-md' : '',
            ind === orders.links.length - 1 ? 'rounded-r-md' : '',
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
import { ORDERS_PER_PAGE } from "../../constants";

const perPage = ref(ORDERS_PER_PAGE);
const search = ref("");
const orders = computed(() => store.state.orders);
const sortField = ref("created_at");
const sortDirection = ref("desc");

const emit = defineEmits(["clickView"]);

onMounted(() => {
  getOrders();
});

function getOrders() {
  store.dispatch("getOrders", {
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
  store.dispatch("getOrders", { url: link.url });
}

function sortOrders(field) {
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

  getOrders();
}

function viewOrder(order) {
  emit("clickView", order);
}

function deleteOrder(order) {
  if (!confirm("Are you sure you want to delete this order?")) {
    return;
  }

  store.dispatch("deleteOrder", order.id).then(() => {
    // TODO show Notification
    store.dispatch("getOrders");
  });
}
</script>
