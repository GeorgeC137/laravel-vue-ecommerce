<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Dashboard</h1>
    <div class="flex items-center">
      <label class="mr-2">Change Date Period</label>
      <CustomInput
        v-model="chosenDate"
        type="select"
        @change="onDatePikerChange"
        :select-options="dateOptions"
      />
    </div>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
    <!-- Active Customers  -->
    <div
      class="bg-white animate-fade-in-down py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
    >
      <label class="font-semibold text-lg block mb-2" for="">Active Customers</label>
      <template v-if="!loading.customersCount">
        <span class="text-3xl font-semibold">{{ customersCount }}</span>
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Active Customers  -->

    <!-- Active Products  -->
    <div
      class="bg-white animate-fade-in-down py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
      style="animation-delay: 0.1s"
    >
      <label class="font-semibold text-lg block mb-2" for="">Active Products</label>
      <template v-if="!loading.productsCount">
        <span class="text-3xl font-semibold">{{ productsCount }}</span>
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Active Products  -->

    <!-- Paid Orders  -->
    <div
      class="bg-white animate-fade-in-down py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
      style="animation-delay: 0.2s"
    >
      <label class="font-semibold text-lg block mb-2" for="">Paid Orders</label>
      <template v-if="!loading.paidOrders">
        <span class="text-3xl font-semibold">{{ paidOrders }}</span>
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Paid Orders  -->

    <!-- Total Income  -->
    <div
      class="bg-white animate-fade-in-down py-6 px-5 rounded-lg shadow flex flex-col items-center"
      style="animation-delay: 0.3s"
    >
      <label class="font-semibold text-lg block mb-2" for="">Total Income</label>
      <template v-if="!loading.totalIncome">
        <span class="text-3xl font-semibold">{{ totalIncome }}</span>
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Total Income  -->
  </div>

  <div
    class="grid grid-cols-1 grid-rows-1 md:grid-rows-2 md:grid-flow-col md:grid-cols-3 gap-3"
  >
    <!-- Orders  -->
    <div
      class="bg-white col-span-1 md:col-span-2 row-span-1 md:row-span-2 py-6 px-5 rounded-lg shadow"
    >
      <label class="font-semibold text-lg block mb-2" for="">Latest Orders</label>
      <template v-if="!loading.latestOrders">
        <div
          v-for="order in latestOrders"
          :key="order.id"
          class="py-2 px-2 hover:bg-gray-50"
        >
          <p>
            <router-link
              :to="{ name: 'app.orders.view', params: { id: order.id } }"
              class="text-indigo-700 font-semibold"
            >
              Order #{{ order.id }}
            </router-link>
            created {{ order.created_at }}. {{ order.items }} items
          </p>
          <p class="flex justify-between">
            <span>{{ order.first_name }} {{ order.last_name }}</span>
            <span>{{ $filters.currencyUSD(order.total_price) }}</span>
          </p>
        </div>
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Orders  -->

    <!-- Doughnut  -->
    <div
      class="bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
    >
      <label class="font-semibold text-lg block mb-2" for="">Orders By Country</label>
      <template v-if="!loading.ordersByCountry">
        <Doughnut :width="140" :height="200" :data="ordersByCountry" />
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Doughnut  -->

    <!-- Customers  -->
    <div class="bg-white py-6 px-5 rounded-lg shadow">
      <label class="font-semibold text-lg block mb-2" for="">Latest Customers</label>
      <template v-if="!loading.latestCustomers">
        <router-link
          :to="{ name: 'app.customers.view', params: { id: customer.id } }"
          v-for="customer in latestCustomers"
          :key="customer.id"
          class="flex mb-2"
        >
          <div
            class="w-12 h-12 bg-gray-200 flex items-center justify-center rounded-full mr-2"
          >
            <UserIcon class="w-5" />
          </div>
          <div>
            <h3>{{ customer.first_name }} {{ customer.last_name }}</h3>
            <p>{{ customer.email }}</p>
          </div>
        </router-link>
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Customers  -->
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axiosClient from "../axios";
import Doughnut from "../components/core/Charts/Doughnut.vue";
import Spinner from "../components/core/Spinner.vue";
import { UserIcon } from "@heroicons/vue/24/outline";
import CustomInput from "../components/core/CustomInput.vue";
import store from "../store";

const customersCount = ref(0);
const productsCount = ref(0);
const paidOrders = ref(0);
const totalIncome = ref(0);
const ordersByCountry = ref([]);
const latestCustomers = ref([]);
const latestOrders = ref([]);
const loading = ref({
  customersCount: true,
  productsCount: true,
  paidOrders: true,
  totalIncome: true,
  ordersByCountry: true,
  latestCustomers: true,
  latestOrders: true,
});

const dateOptions = computed(() => store.state.dateOptions);

const chosenDate = ref("all");

function updateDashboard() {
  const d = chosenDate.value;
  loading.value = {
    customersCount: true,
    productsCount: true,
    paidOrders: true,
    totalIncome: true,
    ordersByCountry: true,
    latestCustomers: true,
    latestOrders: true,
  };

  axiosClient.get("/dashboard/customers-count", { params: { d } }).then(({ data }) => {
    customersCount.value = data;
    loading.value.customersCount = false;
  });

  axiosClient
    .get("/dashboard/latest-customers", { params: { d } })
    .then(({ data: customers }) => {
      latestCustomers.value = customers;
      loading.value.latestCustomers = false;
    });

  axiosClient
    .get("/dashboard/latest-orders", { params: { d } })
    .then(({ data: orders }) => {
      latestOrders.value = orders.data;
      loading.value.latestOrders = false;
    });

  axiosClient.get("/dashboard/products-count", { params: { d } }).then(({ data }) => {
    productsCount.value = data;
    loading.value.productsCount = false;
  });

  axiosClient.get("/dashboard/orders-count", { params: { d } }).then(({ data }) => {
    paidOrders.value = data;
    loading.value.paidOrders = false;
  });

  axiosClient.get("/dashboard/income-amount", { params: { d } }).then(({ data }) => {
    totalIncome.value = new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "KSH",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(data);
    loading.value.totalIncome = false;
  });

  axiosClient
    .get("/dashboard/orders-by-country", { params: { d } })
    .then(({ data: countries }) => {
      const chartData = {
        labels: [],
        datasets: [
          {
            backgroundColor: ["#41B883", "#E46651", "#d5f00a", "#00D8FF", "#DD1B16"],
            data: [],
          },
        ],
      };
      countries.forEach((c) => {
        chartData.labels.push(c.name);
        chartData.datasets[0].data.push(c.count);
      });
      ordersByCountry.value = chartData;
      loading.value.ordersByCountry = false;
    });
}

function onDatePikerChange() {
  updateDashboard();
}

onMounted(() => {
  updateDashboard();
});
</script>
