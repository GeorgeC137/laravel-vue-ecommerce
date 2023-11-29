<template>
  <GuestLayout title="Set new password">
    <form class="space-y-6" action="#" method="POST">
      <div>
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900"
          >New password</label
        >
        <div class="mt-2">
          <input
            id="password"
            name="password"
            type="password"
            required
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
          />
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label
            for="repeat-password"
            class="block text-sm font-medium leading-6 text-gray-900"
            >Repeat Password</label
          >
          <div class="text-sm">
            <router-link
              :to="{ name: 'Login' }"
              class="font-semibold text-indigo-600 hover:text-indigo-500"
              >Back to Login</router-link
            >
          </div>
        </div>
        <div class="mt-2">
          <input
            id="repeat-password"
            name="repeat-password"
            type="password"
            required
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
          />
        </div>
      </div>

      <div>
        <button
          type="submit"
          class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          Submit
        </button>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from "../components/GuestLayout.vue";
</script>
