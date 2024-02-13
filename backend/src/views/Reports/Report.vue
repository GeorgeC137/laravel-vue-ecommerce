<template>
  <div>
    <div class="grid grid-cols-3 gap-3">
      <div class="col-span-2 grid grid-cols-2 gap-3">
        <router-link
          :to="{ name: 'reports.customers', params: route.params }"
          class="bg-white py-2 px-3 text-center text-gray-700 rounded-md"
          active-class="text-indigo-600 bg-indigo-50"
        >
          Customers Report
        </router-link>
        <router-link
          :to="{ name: 'reports.orders', params: route.params }"
          class="bg-white py-2 px-3 text-center text-gray-700 rounded-md"
          active-class="text-indigo-600 bg-indigo-50"
        >
          Orders Report
        </router-link>
      </div>
      <div>
        <CustomInput
          v-model="chosenDate"
          type="select"
          @change="onDatePikerChange"
          :select-options="dateOptions"
        />
      </div>
    </div>
  </div>

  <div class="bg-white p-3 rounded-md mt-3 shadow-md">
    <router-view />
  </div>
</template>

<script setup>
import store from "../../store";
import { computed, ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import CustomInput from "../../components/core/CustomInput.vue";

const chosenDate = ref("all");
const router = useRouter();
const route = useRoute();
const dateOptions = computed(() => store.state.dateOptions);

function onDatePikerChange() {
  router.push({
    name: route.name,
    params: { date: chosenDate.value },
  });
}
</script>
