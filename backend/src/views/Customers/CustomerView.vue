<template>
  <div v-if="customer.id" class="animate-fade-in-down">
    <form @submit.prevent="onSubmit">
      <div class="bg-white pb-4 pt-5 px-4">
        <h1 class="text-2xl font-semibold pb-2">
          {{ title }}
        </h1>
        <CustomInput
          class="mb-2"
          v-model="customer.first_name"
          label="First Name"
          :errors="errors.first_name"
        />
        <CustomInput
          class="mb-2"
          v-model="customer.last_name"
          label="Last Name"
          :errors="errors.last_name"
        />
        <CustomInput
          class="mb-2"
          v-model="customer.email"
          label="Email"
          :errors="errors.email"
        />
        <CustomInput
          type="number"
          class="mb-2"
          v-model="customer.phone"
          label="Phone"
          :errors="errors.phone"
        />
        <CustomInput
          type="checkbox"
          class="mb-2"
          v-model="customer.status"
          label="Active"
          :errors="errors.status"
        />

        <div class="gap-6 grid grid-cols-1 md:grid-cols-2">
          <div>
            <h2 class="text-2xl font-semibold pb-2 border-b mt-6 border-gray-300">
              Billing Address
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
              <CustomInput
                v-model="customer.billingAddress.address1"
                label="Address 1"
                :errors="errors['billingAddress.address1']"
              />
              <CustomInput
                v-model="customer.billingAddress.address2"
                label="Address 2"
                :errors="errors['billingAddress.address2']"
              />
              <CustomInput
                v-model="customer.billingAddress.city"
                label="City"
                :errors="errors['billingAddress.city']"
              />
              <CustomInput
                v-model="customer.billingAddress.zip_code"
                label="Zip Code"
                :errors="errors['billingAddress.zip_code']"
              />
              <CustomInput
                type="select"
                :select-options="countries"
                v-model="customer.billingAddress.country_code"
                label="Country"
                :errors="errors['billingAddress.country_code']"
              />
              <CustomInput
                v-if="billingCountry && !billingCountry.states"
                v-model="customer.billingAddress.state"
                label="State"
                :errors="errors['billingAddress.state']"
              />
              <CustomInput
                v-else
                type="select"
                :select-options="billingStateOptions"
                v-model="customer.billingAddress.state"
                label="State"
                :errors="errors['billingAddress.state']"
              />
            </div>
          </div>

          <div>
            <h2 class="text-2xl font-semibold pb-2 border-b mt-6 border-gray-300">
              Shipping Address
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
              <CustomInput
                v-model="customer.shippingAddress.address1"
                label="Address 1"
                :errors="errors['shippingAddress.address1']"
              />
              <CustomInput
                v-model="customer.shippingAddress.address2"
                label="Address 2"
                :errors="errors['shippingAddress.address2']"
              />
              <CustomInput
                v-model="customer.shippingAddress.city"
                label="City"
                :errors="errors['shippingAddress.city']"
              />
              <CustomInput
                v-model="customer.shippingAddress.zip_code"
                label="Zip Code"
                :errors="errors['shippingAddress.zip_code']"
              />
              <CustomInput
                type="select"
                :select-options="countries"
                v-model="customer.shippingAddress.country_code"
                label="Country"
                :errors="errors['shippingAddress.country_code']"
              />
              <CustomInput
                v-if="shippingCountry && !shippingCountry.states"
                v-model="customer.shippingAddress.state"
                label="State"
                :errors="errors['shippingAddress.state']"
              />
              <CustomInput
                v-else
                type="select"
                :select-options="shippingStateOptions"
                v-model="customer.shippingAddress.state"
                label="State"
                :errors="errors['shippingAddress.state']"
              />
            </div>
          </div>
        </div>
      </div>
      <footer class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button
          type="submit"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-indigo-500 text-base font-medium text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
        >
          Submit
        </button>
        <router-link
          :to="{ name: 'app.customers' }"
          type="button"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-red-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          ref="cancelButtonRef"
        >
          Cancel
        </router-link>
      </footer>
    </form>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from "vue-router";
import store from "../../store";
import { onMounted, ref, computed } from "vue";
import CustomInput from "../../components/core/CustomInput.vue";

const loading = ref(false);
const title = ref("");
const route = useRoute();
const router = useRouter();
const customer = ref({
  billingAddress: {},
  shippingAddress: {},
});
const errors = ref({
  first_name: [],
  last_name: [],
  email: [],
  phone: [],
  status: [],
  "billingAddress.address1": [],
  "billingAddress.address2": [],
  "billingAddress.city": [],
  "billingAddress.state": [],
  "billingAddress.zip_code": [],
  "billingAddress.country_code": [],
  "shippingAddress.address1": [],
  "shippingAddress.address2": [],
  "shippingAddress.city": [],
  "shippingAddress.state": [],
  "shippingAddress.zip_code": [],
  "shippingAddress.country_code": [],
});
const countries = computed(() =>
  store.state.countries.map((c) => ({ key: c.code, text: c.name }))
);
const billingCountry = computed(() =>
  store.state.countries.find((c) => c.code === customer.value.billingAddress.country_code)
);
const billingStateOptions = computed(() => {
  if (!billingCountry.value || !billingCountry.value.states) return [];

  return Object.entries(billingCountry.value.states).map((c) => ({
    key: c[0],
    text: c[1],
  }));
});
const shippingCountry = computed(() =>
  store.state.countries.find(
    (c) => c.code === customer.value.shippingAddress.country_code
  )
);
const shippingStateOptions = computed(() => {
  if (!shippingCountry.value || !shippingCountry.value.states) return [];

  return Object.entries(shippingCountry.value.states).map((c) => ({
    key: c[0],
    text: c[1],
  }));
});

onMounted(() => {
  store.dispatch("getCustomer", route.params.id).then(({ data }) => {
    title.value = `Update customer: "${data.first_name} ${data.last_name}"`;
    customer.value = data;
  });
});

function onSubmit() {
  loading.value = true;
  customer.value.status = !!customer.value.status;
  if (customer.value.id) {
    store
      .dispatch("updateCustomer", customer.value)
      .then((response) => {
        loading.value = false;
        if (response.status === 200) {
          store.commit("showToast", "Customer has been successfully updated");
          store.dispatch("getCustomers");
          router.push({
            name: "app.customers",
          });
        }
      })
      .catch((err) => {
        errors.value = err.response.data.errors;
      });
  } else {
    store.dispatch("createCustomer", customer.value).then((response) => {
      loading.value = false;
      if (response.status === 201) {
        store.commit("showToast", "Customer has been successfully created");
        store.dispatch("getCustomers");
        router.push({
          name: "app.customers",
        });
      }
    });
  }
}
</script>
