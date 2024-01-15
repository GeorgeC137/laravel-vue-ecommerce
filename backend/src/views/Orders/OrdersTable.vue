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
            @click="sortOrders"
            class="border-b-2 p-2 text-left"
            field="created_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Date</TableHeaderCell
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
            field="number_of_items"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Items</TableHeaderCell
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
          <td class="border-b p-2">{{ order.created_at }}</td>
          <td class="border-b p-2">
            <span>{{ order.status }}</span>
          </td>
          <td
            class="border-b p-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px]"
          >
            {{ order.total_price }}
          </td>
          <td class="border-b p-2">{{ order.number_of_items }}</td>
          <td class="border-b p-2">
            <Menu as="div" class="relative inline-block text-left">
              <div>
                <MenuButton
                  class="inline-flex items-center justify-center rounded-full w-10 h-10 bg-black bg-opacity-0 text-sm font-medium text-white hover:bg-opacity-5 focus:bg-opacity-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                >
                  <EllipsisVerticalIcon
                    class="h-5 w-5 text-indigo-500"
                    aria-hidden="true"
                  />
                </MenuButton>
              </div>

              <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
              >
                <MenuItems
                  class="absolute z-10 right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="viewOrder(order)"
                      >
                        <PencilIcon
                          :active="active"
                          class="mr-2 h-5 w-5 text-indigo-400"
                          aria-hidden="true"
                        />
                        Edit
                      </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="deleteOrder(order)"
                      >
                        <TrashIcon
                          :active="active"
                          class="mr-2 h-5 w-5 text-indigo-400"
                          aria-hidden="true"
                        />
                        Delete
                      </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
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
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { PencilIcon, TrashIcon, EllipsisVerticalIcon } from "@heroicons/vue/24/outline";
import store from "../../store";
import Spinner from "../../components/core/Spinner.vue";
import TableHeaderCell from "../../components/core/Table/TableHeaderCell.vue";
import { ORDERS_PER_PAGE } from "../../constants";

const perPage = ref(ORDERS_PER_PAGE);
const search = ref("");
const orders = computed(() => store.state.orders);
const sortField = ref("updated_at");
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
