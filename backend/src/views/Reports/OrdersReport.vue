<template>
  <div>
    <Bar v-if="!loading" :data="chartData" :height="240" />

    <Spinner v-else class="" text="" />
  </div>
</template>

<script setup>
import axiosClient from "../../axios";
import Bar from "../../components/core/Charts/Bar.vue";
import Spinner from "../../components/core/Spinner.vue";
import { ref, watch } from "vue";
import { useRoute } from "vue-router";

const chartData = ref([]);
const loading = ref(true);
const route = useRoute();

watch(
  route,
  (val) => {
    getData();
  },
  { immediate: true }
);

function getData() {
  axiosClient
    .get("/report/orders", { params: { d: route.params.date } })
    .then(({ data }) => {
      chartData.value = data;
      loading.value = false;
    });
}
</script>
