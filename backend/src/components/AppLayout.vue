<template>
  <div v-if="currentUser.id" class="flex min-h-full bg-gray-200">
    <!-- Sidebar  Start -->
    <Sidebar :class="{ '-ml-[200px]': !sideBarOpened }" />
    <!-- Sidebar End -->

    <div class="flex-1">
      <!-- Header Start -->
      <Navbar @toggle-sidebar="toggleSidebar" @logout="logout" />
      <!-- Header End -->

      <!-- Content Start  -->
      <main class="p-6">
        <router-view></router-view>
      </main>
      <!-- Content Start  -->
    </div>
  </div>

  <div v-else class="flex items-center justify-center min-h-full bg-gray-200">
    <Spinner />
  </div>

  <Toast />
</template>

<script setup>
import Sidebar from "../components/Sidebar.vue";
import Navbar from "../components/Navbar.vue";
import Spinner from "./core/Spinner.vue";
import Toast from "./core/Toast.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import store from "../store";
import { useRouter } from "vue-router";

const sideBarOpened = ref(true);
const router = useRouter();
const currentUser = computed(() => store.state.user.data);

onMounted(() => {
  store.dispatch("getUser");
  handleSideBarOpened();
  window.addEventListener("resize", handleSideBarOpened);
});

onUnmounted(() => {
  window.removeEventListener("resize", handleSideBarOpened);
});

function handleSideBarOpened() {
  sideBarOpened.value = window.outerWidth > 768;
}

function toggleSidebar() {
  sideBarOpened.value = !sideBarOpened.value;
}

function logout() {
  store.dispatch("logout").then(() => {
    router.push({
      name: "Login",
    });
  });
}
</script>
