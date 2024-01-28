<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Customers</h1>
  </div>

  <CustomerModal v-model="showModal" :customer="customerModel" @close="onModalClose" />
  <CustomersTable @clickEdit="editCustomer" />
</template>

<script setup>
import CustomersTable from "./CustomersTable.vue";
import CustomerModal from "./CustomerModal.vue";
import { ref } from "vue";
import store from "../../store";

const showModal = ref(false);
const DEFAULT_CUSTOMER = {};
const customerModel = ref({ ...DEFAULT_CUSTOMER });

function showCustomerModal() {
  showModal.value = true;
}

function onModalClose() {
  customerModel.value = { ...DEFAULT_CUSTOMER };
}

function editCustomer(customer) {
  store.dispatch("getCustomer", customer.id).then(({ data }) => {
    customerModel.value = data;
    showCustomerModal();
  });
}
</script>
