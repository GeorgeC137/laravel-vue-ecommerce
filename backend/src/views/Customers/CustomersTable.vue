<template>
  <div class="bg-white shadow p-4 rounded-lg animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          @change="getCustomers()"
          v-model="perPage"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="30">30</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ml-3">Found {{ customers.total }} customers</span>
      </div>
      <div>
        <input
          v-model="search"
          @change="getCustomers()"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          placeholder="Type to Search Customers"
        />
      </div>
    </div>

    <table class="table-auto w-full">
      <thead>
        <tr>
          <TableHeaderCell
            @click="sortCustomers"
            class="border-b-2 p-2 text-left"
            field="user_id"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >ID</TableHeaderCell
          >
          <TableHeaderCell
          @click="sortCustomers"
            class="border-b-2 p-2 text-left"
            :sort-field="sortField"
            field="first_name"
            :sort-direction="sortDirection"
            >Name</TableHeaderCell
          >
          <TableHeaderCell
            class="border-b-2 p-2 text-left"
            field="email"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Email</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortCustomers"
            class="border-b-2 p-2 text-left"
            field="phone"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Phone</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortCustomers"
            class="border-b-2 p-2 text-left"
            field="status"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Status</TableHeaderCell
          >
          <TableHeaderCell
            @click="sortCustomers"
            class="border-b-2 p-2 text-left"
            field="created_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            >Register Date</TableHeaderCell
          >
          <TableHeaderCell field="actions" class="border-b-2 p-2 text-left"
            >Actions</TableHeaderCell
          >
        </tr>
      </thead>

      <tbody v-if="customers.loading || !customers.data.length">
        <tr>
          <td colspan="7">
            <Spinner class="my-4" v-if="customers.loading" />
            <p v-else class="text-gray-700 py-8 text-center">There are no customers</p>
          </td>
        </tr>
      </tbody>

      <tbody v-else>
        <tr v-for="(customer, ind) in customers.data">
          <td class="border-b p-2">{{ customer.id }}</td>
          <td class="border-b p-2">
            <td class="border-b p-2">{{ customer.first_name }} {{ customer.last_name }}</td>
          </td>
          <td
            class="border-b p-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px]"
          >
            {{ customer.email }}
          </td>
          <td class="border-b p-2">{{ customer.phone }}</td>
          <td class="border-b p-2">{{ customer.status }}</td>
          <td class="border-b p-2">{{ customer.created_at }}</td>
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
                      <router-link
                        :to="{ name: 'app.customers.view', params: { id: customer.id } }"
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                      >
                        <PencilIcon
                          :active="active"
                          class="mr-2 h-5 w-5 text-indigo-400"
                          aria-hidden="true"
                        />
                        Edit
                      </router-link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="deleteCustomer(customer)"
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
      v-if="!customers.loading && customers.data.length"
      class="flex justify-between items-center mt-5"
    >
      <span> Showing from {{ customers.from }} to {{ customers.to }} </span>

      <nav
        v-if="customers.total > customers.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-md -space-x-px"
        aria-label="Pagination"
      >
        <a
          href="#"
          @click="getCurrentPage($event, link)"
          v-html="link.label"
          :disabled="!link.url"
          v-for="(link, ind) in customers.links"
          :key="ind"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
            link.active
              ? 'z-10 bg-indigo-50 border-indigo-600 text-indigo-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
            ind === 0 ? 'rounded-l-md' : '',
            ind === customers.links.length - 1 ? 'rounded-r-md' : '',
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
import { CUSTOMERS_PER_PAGE } from "../../constants";

const perPage = ref(CUSTOMERS_PER_PAGE);
const search = ref("");
const customers = computed(() => store.state.customers);
const sortField = ref("updated_at");
const sortDirection = ref("desc");

onMounted(() => {
  getCustomers();
});

function getCustomers() {
  store.dispatch("getCustomers", {
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
  store.dispatch("getCustomers", { url: link.url });
}

function sortCustomers(field) {
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

  getCustomers();
}

function deleteCustomer(customer) {
  if (!confirm("Are you sure you want to delete this customer?")) {
    return;
  }

  store.dispatch("deleteCustomer", customer.id).then(() => {
    // TODO show Notification
    store.dispatch("getCustomers");
  });
}
</script>
