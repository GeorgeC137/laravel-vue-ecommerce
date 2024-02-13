<template>
  <div>
    <Line v-if="!loading" :data="chartData" :height="240" />

    <Spinner v-else class="" text="" />
  </div>
</template>

<script setup>
import axiosClient from "../../axios";
import Line from "../../components/core/Charts/Line.vue";
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
    .get("/report/customers", { params: { d: route.params.date } })
    .then(({ data }) => {
      chartData.value = data;
      loading.value = false;
    });
}
</script>
