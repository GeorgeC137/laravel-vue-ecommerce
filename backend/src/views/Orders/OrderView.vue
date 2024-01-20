<template>
  <div v-if="order">
    <!-- Order Details -->
    <div>
      <h2 class="text-1xl font-semibold pb-2 border-b border-gray-300">Order Details</h2>
      <table class="table-sm">
        <tbody>
          <tr>
            <td class="font-bold">Order #</td>
            <td>{{ order.id }}</td>
          </tr>
          <tr>
            <td class="font-bold">Order Date</td>
            <td>{{ order.created_at }}</td>
          </tr>
          <tr>
            <td class="font-bold">Status</td>
            <td>
              <span
                class="text-white p-1 rounded"
                :class="{
                  'bg-emerald-500': order.status === 'paid',
                  'bg-gray-400': order.status !== 'paid',
                }"
                >{{ order.status }}</span
              >
            </td>
          </tr>
          <tr>
            <td class="font-bold">SubTotal</td>
            <td>${{ order.total_price }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Order Details -->

    <!-- Customer Details -->
    <div>
      <h2 class="text-1xl font-semibold mt-6 pb-2 border-b border-gray-300">
        Customer Details
      </h2>
      <table class="table-sm">
        <tbody>
          <tr>
            <td class="font-bold">Full Name</td>
            <td>{{ order.customer.first_name }} {{ order.customer.last_name }}</td>
          </tr>
          <tr>
            <td class="font-bold">Email</td>
            <td>{{ order.customer.email }}</td>
          </tr>
          <tr>
            <td class="font-bold">Phone</td>
            <td>{{ order.customer.phone }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Customer Details -->

    <!-- Addresses Details  -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <h2 class="text-1xl font-semibold mt-6 pb-2 border-b border-gray-300">
          Billing Address
        </h2>
        <!-- Billing Address Details -->
        <div>
          {{ order.customer.billingAddress.address1 }},
          {{ order.customer.billingAddress.address2 }} <br />
          {{ order.customer.billingAddress.city }},
          {{ order.customer.billingAddress.zip_code }} <br />
          {{ order.customer.billingAddress.state }},
          {{ order.customer.billingAddress.country }} <br />
        </div>
        <!-- Billing Address Details -->
      </div>
      <div>
        <h2 class="text-1xl font-semibold mt-6 pb-2 border-b border-gray-300">
          Shipping Address
        </h2>
        <!-- Shipping Address Details -->
        <div>
          {{ order.customer.shippingAddress.address1 }},
          {{ order.customer.shippingAddress.address2 }} <br />
          {{ order.customer.shippingAddress.city }},
          {{ order.customer.shippingAddress.zip_code }} <br />
          {{ order.customer.shippingAddress.state }},
          {{ order.customer.shippingAddress.country }} <br />
        </div>
        <!-- Shipping Address Details -->
      </div>
    </div>
    <!-- Addresses Details  -->

    <!-- Order Items -->
    <div>
      <h2 class="text-1xl font-semibold mt-6 border-b pb-2 border-gray-300">
        Order Items
      </h2>
      <div v-for="item in order.items" :key="item.id">
        <div>
          <div class="flex gap-6">
            <a
              href="#"
              class="flex items-center overflow-hidden justify-center w-32 h-32"
            >
              <img :src="item.product.image" :alt="item.title" class="object-cover" />
            </a>
            <div class="flex-1 flex flex-col justify-between pb-3">
              <h3 class="text-ellipsis mb-4">
                {{ item.product.title }}
              </h3>
            </div>
          </div>

          <div class="flex justify-between items-center mt-3">
            <div class="flex items-center">Qty: {{ item.quantity }}</div>
            <div class="text-lg font-semibold mb-4">${{ item.unit_price }}</div>
          </div>
        </div>

        <hr class="my-2" />
      </div>
    </div>
    <!--/ Order Items -->
  </div>
</template>

<script setup>
import { useRoute } from "vue-router";
import store from "../../store";
import { onMounted, ref } from "vue";

const route = useRoute();
const order = ref(null);

onMounted(() => {
  store.dispatch("getOrder", route.params.id).then(({ data }) => {
    order.value = data;
  });
});
</script>
