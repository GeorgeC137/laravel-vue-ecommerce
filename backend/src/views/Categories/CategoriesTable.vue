<template>
    <div class="bg-white shadow p-4 rounded-lg animate-fade-in-down">
        <div class="flex justify-between border-b-2 pb-3">
            <div class="flex items-center">
                <span class="ml-3">Found {{ categories.data.length }} categories</span>
            </div>
        </div>

        <table class="table-auto w-full">
            <thead>
                <tr>
                    <TableHeaderCell @click="sortCategories" class="border-b-2 p-2 text-left" field="id" :sort-field="sortField"
                        :sort-direction="sortDirection">ID</TableHeaderCell>
                    <TableHeaderCell @click="sortCategories" class="border-b-2 p-2 text-left" field="name"
                        :sort-field="sortField" :sort-direction="sortDirection">Name</TableHeaderCell>
                    <TableHeaderCell @click="sortCategories" class="border-b-2 p-2 text-left" field="email"
                        :sort-field="sortField" :sort-direction="sortDirection">Email</TableHeaderCell>
                    <TableHeaderCell @click="sortCategories" class="border-b-2 p-2 text-left" field="updated_at"
                        :sort-field="sortField" :sort-direction="sortDirection">Create Date</TableHeaderCell>
                    <TableHeaderCell field="actions" class="border-b-2 p-2 text-left">Actions</TableHeaderCell>
                </tr>
            </thead>

            <tbody v-if="categories.loading || !categories.data.length">
                <tr>
                    <td colspan="5">
                        <Spinner class="my-4" v-if="categories.loading" />
                        <p v-else class="text-gray-700 py-8 text-center">There are no categories</p>
                    </td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr v-for="(category, ind) in categories.data">
                    <td class="border-b p-2">{{ category.id }}</td>
                    <td class="border-b p-2">
                    <td class="border-b p-2">{{ category.name }}</td>
                    </td>
                    <td class="border-b p-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px]">
                        {{ category.email }}
                    </td>
                    <td class="border-b p-2">{{ category.created_at }}</td>
                    <td class="border-b p-2">
                        <Menu as="div" class="relative inline-block text-left">
                            <div>
                                <MenuButton
                                    class="inline-flex items-center justify-center rounded-full w-10 h-10 bg-black bg-opacity-0 text-sm font-medium text-white hover:bg-opacity-5 focus:bg-opacity-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                                    <EllipsisVerticalIcon class="h-5 w-5 text-indigo-500" aria-hidden="true" />
                                </MenuButton>
                            </div>

                            <transition enter-active-class="transition duration-100 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0">
                                <MenuItems
                                    class="absolute z-10 right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="px-1 py-1">
                                        <MenuItem v-slot="{ active }">
                                        <button :class="[
                                            active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]" @click="editCategory(category)">
                                            <PencilIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400"
                                                aria-hidden="true" />
                                            Edit
                                        </button>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active }">
                                        <button :class="[
                                            active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]" @click="deleteCategory(category)">
                                            <TrashIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400"
                                                aria-hidden="true" />
                                            Delete
                                        </button>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="!categories.loading && categories.data.length" class="flex justify-between items-center mt-5">
            <span> Showing from {{ categories.from }} to {{ categories.to }} </span>

            <nav v-if="categories.total > categories.limit"
                class="relative z-0 inline-flex justify-center rounded-md shadow-md -space-x-px" aria-label="Pagination">
                <a href="#" @click="getCurrentPage($event, link)" v-html="link.label" :disabled="!link.url"
                    v-for="(link, ind) in categories.links" :key="ind"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap" :class="[
                        link.active
                            ? 'z-10 bg-indigo-50 border-indigo-600 text-indigo-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        ind === 0 ? 'rounded-l-md' : '',
                        ind === categories.links.length - 1 ? 'rounded-r-md' : '',
                        !link.url ? 'bg-gray-100 text-gray-700' : '',
                    ]" aria-current="page"></a>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { PencilIcon, TrashIcon, EllipsisVerticalIcon } from "@heroicons/vue/24/outline";
import store from "../../store";
import Spinner from "../../components/core/Spinner.vue";
import TableHeaderCell from "../../components/core/Table/TableHeaderCell.vue";

const categories = computed(() => store.state.categories);
const sortField = ref("updated_at");
const sortDirection = ref("desc");

const emit = defineEmits(["clickEdit"]);

onMounted(() => {
    getCategories();
});

function getCategories() {
    store.dispatch("getCategories", {
        sort_field: sortField.value,
        sort_direction: sortDirection.value,
    });
}

function getCurrentPage(e, link) {
    e.preventDefault();
    if (!link.url || link.active) {
        return;
    }
    store.dispatch("getCategories", { url: link.url });
}

function sortCategories(field) {
    if (sortField.value === field) {
        if (sortDirection.value === "asc") {
            sortDirection.value = "desc";
        } else {
            sortDirection.value = "asc";
        }
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }

    getCategories();
}

function editCategory(category) {
    emit("clickEdit", category);
}

function deleteCategory(category) {
    if (!confirm("Are you sure you want to delete this category?")) {
        return;
    }

    store.dispatch("deleteCategory", category.id).then(() => {
        store.commit('showToast', 'Category has been successfully deleted');
        store.dispatch("getCategories");
    });
}
</script>
