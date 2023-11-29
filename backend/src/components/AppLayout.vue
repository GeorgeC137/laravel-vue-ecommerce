<template>
  <div class="flex min-h-full bg-gray-200">
    <!-- Sidebar  Start -->
    <Sidebar :class="{ '-ml-[200px]': !sideBarOpened }" />
    <!-- Sidebar End -->

    <div class="flex-1">
      <!-- Header Start -->
      <Navbar @toggle-sidebar="toggleSidebar" />
      <!-- Header End -->

      <!-- Content Start  -->
      <main class="p-6">
        <router-view></router-view>
      </main>
      <!-- Content Start  -->
    </div>
  </div>
</template>

<script setup>
import Sidebar from "../components/Sidebar.vue";
import Navbar from "../components/Navbar.vue";
import { ref, onMounted, onUnmounted } from "vue";

const sideBarOpened = ref(true);

onMounted(() => {
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
</script>
