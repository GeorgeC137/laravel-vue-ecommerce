<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Categories</h1>
    <button
      type="submit"
      @click="showCategoryModal"
      class="flex justify-center py-2 px-4 border border-transparent font-medium rounded-md text-white text-sm bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Add new Category
    </button>
  </div>

  <CategoryModal v-model="showModal" :category="categoryModel" @close="onModalClose" />
  <CategoriesTable @clickEdit="editCategory" />
</template>

<script setup>
import CategoriesTable from "./CategoriesTable.vue";
import CategoryModal from "./CategoryModal.vue";
import { ref } from "vue";
import store from "../../store";

const showModal = ref(false);
const DEFAULT_EMPTY_OBJECT = {
  id: "",
  name: "",
  email: "",
  password: "",
};
const categoryModel = ref({ ...DEFAULT_EMPTY_OBJECT });

function showCategoryModal() {
  showModal.value = true;
}

function onModalClose() {
  categoryModel.value = { ...DEFAULT_EMPTY_OBJECT };
}

function editCategory(category) {
  store.dispatch("getCategory", category.id).then(({ data }) => {
    categoryModel.value = data;
    showCategoryModal();
  });
}
</script>
