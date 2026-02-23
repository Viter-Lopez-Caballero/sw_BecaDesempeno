<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/Shared/Pagination.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { useCan } from '@/composables/usePermissions';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { mdiSecurity } from '@mdi/js';
import { alertaExito, alertaPregunta } from '@/utils/alerts.js';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    registrationLink: {
        type: String,
        required: true,
    }
});

const search = ref(props.filters.search || '');
const rows = ref(props.filters.rows || 10);
const sortField = ref(props.filters.sort_field || '');
const sortDirection = ref(props.filters.sort_direction || 'asc');

const rowOptions = [
    { label: '5 Registros', value: 5 },
    { label: '10 Registros', value: 10 },
    { label: '25 Registros', value: 25 },
    { label: '50 Registros', value: 50 },
];

const copyRegistrationLink = () => {
    const fullUrl = window.location.origin + '/register/evaluator';
    
    const tempInput = document.createElement('input');
    tempInput.value = fullUrl;
    document.body.appendChild(tempInput);
    tempInput.select();
    
    try {
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        alertaExito('¡Enlace copiado!', 'El enlace de registro ha sido copiado al portapapeles');
    } catch (err) {
        document.body.removeChild(tempInput);
        console.error('Error al copiar:', err);
    }
};

const onSearch = debounce((value) => {
    router.get(route('admin.evaluators.index'), {
        search: value, 
        rows: rows.value,
        sort_field: sortField.value,
        sort_direction: sortDirection.value
    }, { preserveState: true, replace: true });
}, 500);

const onRowsChange = () => {
    router.get(route('admin.evaluators.index'), { 
        search: search.value, 
        rows: rows.value,
        sort_field: sortField.value,
        sort_direction: sortDirection.value
    }, { preserveState: true, replace: true });
};

watch(search, (value) => {
    onSearch(value);
});

const cleanFilters = () => {
    search.value = '';
    rows.value = 10;
    sortField.value = '';
    sortDirection.value = 'asc';
    router.get(route('admin.evaluators.index'), {}, { preserveState: true, replace: true });
};

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    router.get(route('admin.evaluators.index'), {
        search: search.value,
        rows: rows.value,
        sort_field: sortField.value,
        sort_direction: sortDirection.value
    }, { preserveState: true, replace: true });
};

const deleteUser = async (id, name) => {
    const confirmed = await alertaPregunta(
        '¿Eliminar evaluador?',
        `Se eliminará a ${name}`
    );
    
    if (confirmed) {
        router.delete(route('admin.evaluators.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                alertaExito('¡Eliminado!', 'Evaluador eliminado correctamente');
            }
        });
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Gestión de Evaluadores" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Evaluadores</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiSecurity"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Seguridad</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#1B396A">
                            <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Evaluadores</span>
                    </div>
                </div>
                <div class="flex w-full md:w-auto">
                    <button @click="copyRegistrationLink" class="w-full md:w-auto justify-center px-4 py-2.5 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition flex items-center gap-2 font-medium cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                            <path d="M360-240q-33 0-56.5-23.5T280-320v-480q0-33 23.5-56.5T360-880h360q33 0 56.5 23.5T800-800v480q0 33-23.5 56.5T720-240H360Zm0-80h360v-480H360v480ZM200-80q-33 0-56.5-23.5T120-160v-560h80v560h440v80H200Zm160-240v-480 480Z"/>
                        </svg>
                        Copiar Enlace de Registro
                    </button>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#374151">
                            <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-800">Filtro de Búsqueda</h2>
                    </div>
                    <button @click="cleanFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                            <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
                <div class="text-sm text-gray-500 mb-4">Buscar y filtrar evaluadores</div>
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="relative w-full md:flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1B396A">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Buscar por nombre o email..." class="pl-10 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition" />
                    </div>
                    <div class="w-full md:w-52 flex-shrink-0">
                        <VueSelect
                            v-model="rows"
                            :options="rowOptions"
                            :reduce="option => option.value"
                            :searchable="false"
                            :clearable="false"
                            placeholder="Registros"
                            class="vue-select-custom"
                            @option:selected="onRowsChange"
                        />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-4 tracking-wider">
                                    <button @click="sortBy('id')" class="flex items-center gap-1 hover:text-gray-200">
                                        ID
                                        <svg v-if="sortField === 'id'" xmlns="http://www.w3.org/2000/svg" :class="sortDirection === 'asc' ? '' : 'rotate-180'" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                                            <path d="M480-528 296-344l-56-56 240-240 240 240-56 56-184-184Z"/>
                                        </svg>
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-4 tracking-wider">
                                    <button @click="sortBy('name')" class="flex items-center gap-1 hover:text-gray-200">
                                        Nombre
                                        <svg v-if="sortField === 'name'" xmlns="http://www.w3.org/2000/svg" :class="sortDirection === 'asc' ? '' : 'rotate-180'" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                                            <path d="M480-528 296-344l-56-56 240-240 240 240-56 56-184-184Z"/>
                                        </svg>
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-4 tracking-wider">
                                    <button @click="sortBy('email')" class="flex items-center gap-1 hover:text-gray-200">
                                        Email
                                        <svg v-if="sortField === 'email'" xmlns="http://www.w3.org/2000/svg" :class="sortDirection === 'asc' ? '' : 'rotate-180'" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                                            <path d="M480-528 296-344l-56-56 240-240 240 240-56 56-184-184Z"/>
                                        </svg>
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Rol</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="(user, index) in users.data" :key="user.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ (users.meta.current_page - 1) * users.meta.per_page + index + 1 }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ user.name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ user.email }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                        {{ user.role || 'Evaluador' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button v-if="useCan('users.delete')" @click="deleteUser(user.id, user.name)" class="p-2 text-red-600 border border-red-600 rounded-full hover:bg-red-600 hover:text-white transition group cursor-pointer" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!users.data || users.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No se encontraron evaluadores
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                 <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                     <Pagination :links="users.meta.links" :total="users.meta.total" :from="users.meta.from" :to="users.meta.to" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
:deep(.vue-select-custom .vs__dropdown-toggle) {
    background: linear-gradient(to bottom, #ffffff 0%, #f9fafb 100%);
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem;
    min-height: 42px;
}

:deep(.vue-select-custom .vs__selected) {
    color: #374151;
    font-weight: 500;
}

:deep(.vue-select-custom .vs__search::placeholder) {
    color: #9ca3af;
}

:deep(.vue-select-custom .vs__dropdown-menu) {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

:deep(.vue-select-custom .vs__dropdown-option) {
    padding: 0.75rem 1rem;
    color: #374151;
}

:deep(.vue-select-custom .vs__dropdown-option--highlight) {
    background: #1B396A;
    color: white;
}

:deep(.vue-select-custom .vs__open-indicator) {
    fill: #1B396A;
    transform: scale(0.85);
}

:deep(.vue-select-custom .vs__actions) {
    padding-right: 4px;
}
</style>
