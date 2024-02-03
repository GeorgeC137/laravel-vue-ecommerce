<template>
  <h1 class="mb-3 text-4xl">Dashboard</h1>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
    <!-- Active Customers  -->
    <div
      class="bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
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
      class="bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
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
      class="bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
    >
      <label class="font-semibold text-lg block mb-2" for="">Paid Orders</label>
      <template v-if="!loading.paidOrders">
        <span class="text-3xl font-semibold">{{ paidOrders }}</span>
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Paid Orders  -->

    <!-- Total Income  -->
    <div class="bg-white py-6 px-5 rounded-lg shadow flex flex-col items-center">
      <label class="font-semibold text-lg block mb-2" for="">Total Income</label>
      <template v-if="!loading.totalIncome">
        <span class="text-3xl font-semibold">{{ totalIncome }}</span>
      </template>

      <Spinner v-else text="" class="" />
    </div>
    <!-- Total Income  -->
  </div>

  <div class="grid grid-cols-1 grid-rows-2 grid-flow-col md:grid-cols-3 gap-3">
    <!-- Products  -->
    <div
      class="bg-white col-span-2 row-span-2 py-6 px-5 rounded-lg shadow flex flex-col items-center justify-center"
    >
      Products
    </div>
    <!-- Products  -->

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
      <router-link
        to="/"
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
    </div>
    <!-- Customers  -->
  </div>
</template>

<script setup>
import { ref } from "vue";
import axiosClient from "../axios";
import Doughnut from "../components/core/Charts/Doughnut.vue";
import Spinner from "../components/core/Spinner.vue";
import { UserIcon } from "@heroicons/vue/24/outline";

const customersCount = ref(0);
const productsCount = ref(0);
const paidOrders = ref(0);
const totalIncome = ref(0);
const ordersByCountry = ref([]);
const latestCustomers = ref([]);
const loading = ref({
  customersCount: true,
  productsCount: true,
  paidOrders: true,
  totalIncome: true,
  ordersByCountry: true,
  latestCustomers: true,
});

axiosClient.get("/dashboard/customers-count").then(({ data }) => {
  customersCount.value = data;
  loading.value.customersCount = false;
});

axiosClient.get("/dashboard/latest-customers").then(({ data: customers }) => {
  latestCustomers.value = customers;
  loading.value.latestCustomers = false;
});

axiosClient.get("/dashboard/products-count").then(({ data }) => {
  productsCount.value = data;
  loading.value.productsCount = false;
});

axiosClient.get("/dashboard/orders-count").then(({ data }) => {
  paidOrders.value = data;
  loading.value.paidOrders = false;
});

axiosClient.get("/dashboard/income-amount").then(({ data }) => {
  totalIncome.value = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(data);
  loading.value.totalIncome = false;
});

axiosClient.get("/dashboard/orders-by-country").then(({ data: countries }) => {
  const chartData = {
    labels: [],
    datasets: [
      {
        backgroundColor: ["#41B883", "#E46651", "#00D8FF", "#DD1B16"],
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
</script>
