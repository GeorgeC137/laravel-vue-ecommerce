<template>
  <div class="flex items-center justify-between mb-3">
    <h1 v-if="!loading" class="text-3xl font-semibold">
      {{ product.id ? `Update product: "${product.title}"` : "Create new Product" }}
    </h1>
  </div>
  <div class="bg-white shadow rounded-lg animate-fade-in-down">
    <Spinner
      v-if="loading"
      class="absolute top-0 left-0 bg-white right-0 bottom-0 flex items-center justify-center z-50"
    />
    <form v-else @submit.prevent="onSubmit">
      <div class="grid grid-cols-3">
        <div class="col-span-2 pb-4 pt-5 px-4">
          <CustomInput
            class="mb-2"
            v-model="product.title"
            label="Product Title"
            :errors="errors['title']"
          />
          <CustomInput
            type="richtext"
            v-model="product.description"
            class="mb-2"
            label="Description"
            :errors="errors['description']"
          />
          <CustomInput
            type="number"
            v-model="product.price"
            class="mb-2"
            label="Price"
            :errors="errors['price']"
            prepend="KSH "
          />
          <CustomInput
            type="number"
            v-model="product.quantity"
            class="mb-2"
            label="Quantity"
            :errors="errors['quantity']"
          />
          <CustomInput
            type="checkbox"
            v-model="product.published"
            class="mb-2"
            label="Published"
            :errors="errors['published']"
          />
          <treeselect
            class="mt-2"
            v-model="product.categories"
            :multiple="true"
            :options="options"
          />
        </div>
        <div class="col-span-1 pb-4 pt-5 px-4">
          <ImagePreview
            v-model="product.images"
            v-model:image-positions="product.image_positions"
            v-model:deleted-images="product.deleted_images"
            :images="product.images"
          />
        </div>
      </div>
      <footer
        class="bg-gray-50 px-4 rounded-b-lg py-3 sm:px-6 sm:flex sm:flex-row-reverse"
      >
        <button
          type="submit"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-indigo-500 text-base font-medium text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
        >
          Save
        </button>
        <button
          @click="onSubmit($event, true)"
          type="button"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-indigo-500 text-base font-medium text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
        >
          Save & Close
        </button>
        <router-link
          :to="{ name: 'app.products' }"
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
import { onMounted, ref } from "vue";
import CustomInput from "../../components/core/CustomInput.vue";
import Spinner from "../../components/core/Spinner.vue";
import ImagePreview from "../../components/ImagePreview.vue";
import store from "../../store";
import { useRoute, useRouter } from "vue-router";
import Treeselect from "vue3-treeselect";
import "vue3-treeselect/dist/vue3-treeselect.css";
import axiosClient from "../../axios";

const loading = ref(false);
const route = useRoute();
const router = useRouter();
const options = ref([]);

const product = ref({
  id: null,
  title: null,
  price: null,
  quantity: null,
  published: false,
  images: [],
  deleted_images: [],
  image_positions: {},
  description: "",
  categories: [],
});

const errors = ref({});

onMounted(() => {
  if (route.params.id) {
    loading.value = true;
    store.dispatch("getProduct", route.params.id).then((response) => {
      loading.value = false;
      product.value = response.data;
    });
  }

  axiosClient.get("/categories/tree").then((result) => {
    options.value = result.data;
  });
});

function onSubmit($event, close = false) {
  loading.value = true;
  errors.value = {};
  product.value.quantity = product.value.quantity || 0;
  if (product.value.id) {
    store
      .dispatch("updateProduct", product.value)
      .then((response) => {
        loading.value = false;
        if (response.status === 200) {
          product.value = response.data;
          store.commit("showToast", "Product updated successfully");
          store.dispatch("getProducts");
          if (close) {
            router.push({ name: "app.products" });
          }
        }
      })
      .catch((err) => {
        loading.value = false;
        errors.value = err.response.data.errors;
      });
  } else {
    store
      .dispatch("createProduct", product.value)
      .then((response) => {
        loading.value = false;
        if (response.status === 201) {
          product.value = response.data;
          store.commit("showToast", "Product created successfully");
          store.dispatch("getProducts");
          if (close) {
            router.push({ name: "app.products" });
          } else {
            product.value = response.data;
            router.push({ name: "app.products.edit", params: { id: response.data.id } });
          }
        }
      })
      .catch((err) => {
        loading.value = false;
        errors.value = err.response.data.errors;
      });
  }
}
</script>
