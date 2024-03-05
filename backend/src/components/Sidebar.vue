<template>
  <div class="w-[200px] bg-blue-700 text-white py-4 px-2 transition-all">
    <router-link
      :to="{ name: 'app.dashboard' }"
      class="flex items-center hover:bg-black/30 rounded p-2 transition-colors"
    >
      <span class="mr-2 text-gray-300">
        <HomeIcon class="w-5" />
      </span>

      <span class="text-xs">Dashboard</span>
    </router-link>
    <router-link
      :to="{ name: 'app.products' }"
      class="flex items-center hover:bg-black/30 rounded p-2 transition-colors"
    >
      <span class="mr-2 text-gray-300">
        <ViewColumnsIcon class="w-5" />
      </span>

      <span class="text-xs">Products</span>
    </router-link>
    <router-link
      :to="{ name: 'app.categories' }"
      class="flex items-center hover:bg-black/30 rounded p-2 transition-colors"
    >
      <span class="mr-2 text-gray-300">
        <ViewColumnsIcon class="w-5" />
      </span>

      <span class="text-xs">Categories</span>
    </router-link>
    <router-link
      :to="{ name: 'app.orders' }"
      class="flex items-center hover:bg-black/30 rounded p-2 transition-colors"
    >
      <span class="mr-2 text-gray-300">
        <ViewColumnsIcon class="w-5" />
      </span>

      <span class="text-xs">Orders</span>
    </router-link>
    <router-link
      :to="{ name: 'app.users' }"
      class="flex items-center hover:bg-black/30 rounded p-2 transition-colors"
    >
      <span class="mr-2 text-gray-300">
        <UsersIcon class="w-5" />
      </span>

      <span class="text-xs">Users</span>
    </router-link>
    <router-link
      :to="{ name: 'app.customers' }"
      class="flex items-center hover:bg-black/30 rounded p-2 transition-colors"
    >
      <span class="mr-2 text-gray-300">
        <UserGroupIcon class="w-5" />
      </span>

      <span class="text-xs">Customers</span>
    </router-link>
    <router-link
      :to="{ name: 'reports.orders' }"
      class="flex items-center hover:bg-black/30 rounded p-2 transition-colors"
    >
      <span class="mr-2 text-gray-300">
        <ChartBarIcon class="w-5" />
      </span>

      <span class="text-xs">Reports</span>
    </router-link>
  </div>
</template>

<script setup>
import {
  HomeIcon,
  UsersIcon,
  UserGroupIcon,
  ChartBarIcon,
  ViewColumnsIcon,
} from "@heroicons/vue/24/outline";
</script>
