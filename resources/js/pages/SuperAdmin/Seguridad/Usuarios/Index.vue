<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import Pagination from '@/Shared/Pagination.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { useCan } from '@/composables/usePermissions';

const props = defineProps({
    usuarios: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const search = ref(props.filters.search);
const rows = ref(props.filters.rows || 10);

const onSearch = debounce((value) => {
    router.get(route(`${props.routeName}index`), { search: value, rows: rows.value }, { preserveState: true, replace: true });
}, 500);

const onRowsChange = () => {
    router.get(route(`${props.routeName}index`), { search: search.value, rows: rows.value }, { preserveState: true, replace: true });
};

watch(search, (value) => {
    onSearch(value);
});

const cleanFilters = () => {
    search.value = '';
    rows.value = 10;
    router.get(route(`${props.routeName}index`), {}, { preserveState: true, replace: true });
};

const deleteUser = (id) => {
    if (confirm('¿Estás seguro de eliminar este usuario?')) {
        router.delete(route(`${props.routeName}destroy`, id));
    }
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="title" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                     <!-- Design shows "Usuarios" heading with + breadcrumb implicitly -->
                    <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                     <!-- Breadcrumb if needed, image doesn't show it explicitly but standard is good -->
                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                         <!-- .. -->
                    </div>
                </div>
                <Link v-if="useCan('users.create')" :href="route(`${routeName}create`)" class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition flex items-center gap-2 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Agregar
                </Link>
            </div>

            <!-- Filter Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <!-- Similar filter layout as other modules -->
                <div class="flex items-center justify-between mb-4">
                     <div class="flex items-center gap-2">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Filtro de Búsqueda</h2>
                    </div>
                     <button @click="cleanFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
                <div class="text-sm text-gray-500 mb-4">Buscar y filtrar usuarios</div>
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                     <div class="w-full md:w-auto">
                        <select v-model="rows" @change="onRowsChange" class="rounded-lg border-gray-300 text-gray-700 text-sm focus:border-[#1B396A] focus:ring-[#1B396A] w-full md:w-48 shadow-sm">
                            <option :value="10">10 Registros</option>
                            <option :value="25">25 Registros</option>
                            <option :value="50">50 Registros</option>
                        </select>
                    </div>
                    <div class="relative w-full md:w-3/4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-[#1B396A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Buscar usuario..." class="pl-10 w-full rounded-full border-gray-300 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 shadow-sm" />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-4 tracking-wider">
                                     <div class="flex items-center gap-1 cursor-pointer">
                                        Nombre
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 tracking-wider">
                                     <div class="flex items-center gap-1 cursor-pointer">
                                        Correo Electrónico
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 tracking-wider">
                                     <div class="flex items-center gap-1 cursor-pointer">
                                        Rol
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th v-if="useCan('users.edit') || useCan('users.delete')" scope="col" class="px-6 py-4 text-center tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="user in usuarios.data" :key="user.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ user.name }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ user.email }}</td>
                                <td class="px-6 py-4 text-gray-900 font-medium capitalize">{{ user.role }}</td>
                                <td v-if="useCan('users.edit') || useCan('users.delete')" class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link v-if="useCan('users.edit')" :href="route(`${routeName}edit`, user.id)" class="p-2 text-[#1B396A] border border-[#1B396A] rounded-full hover:bg-[#1B396A] hover:text-white transition group" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <button v-if="useCan('users.delete')" @click="deleteUser(user.id)" class="p-2 text-red-500 border border-red-500 rounded-full hover:bg-red-500 hover:text-white transition group" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="usuarios.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    No se encontraron registros
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                 <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                     <Pagination :links="usuarios.meta.links" :total="usuarios.meta.total" :from="usuarios.meta.from" :to="usuarios.meta.to" />
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>