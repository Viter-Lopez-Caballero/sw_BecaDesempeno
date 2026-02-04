<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import Pagination from '@/Shared/Pagination.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { 
    mdiFileDocumentMultiple, 
    mdiClockOutline, 
    mdiCheckCircle, 
    mdiCloseCircle,
    mdiHome
} from '@mdi/js';

const props = defineProps({
    stats: Object,
    topInstitutions: Object,
    institutions: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const rows = ref(props.filters?.rows || 10);

const rowOptions = [
    { label: '5 Registros', value: 5 },
    { label: '10 Registros', value: 10 },
    { label: '25 Registros', value: 25 },
    { label: '50 Registros', value: 50 },
];

const onSearch = debounce((value) => {
    router.get(route('admin.inicio'), {
        search: value,
        rows: rows.value,
    }, { preserveState: true, replace: true, preserveScroll: true });
}, 500);

const cleanFilters = () => {
    search.value = '';
    rows.value = 10;
    router.get(route('admin.inicio'), {}, { preserveState: true, replace: true });
};

const onRowsChange = () => {
    router.get(route('admin.inicio'), {
        search: search.value,
        rows: rows.value,
    }, { preserveState: true, replace: true, preserveScroll: true });
};

watch(search, (value) => {
    onSearch(value);
});

const getMaxCount = () => {
    if (!props.topInstitutions?.data || props.topInstitutions.data.length === 0) return 1;
    return Math.max(...props.topInstitutions.data.map(i => i.solicitudes_count));
};

const getProgressWidth = (count) => {
    const max = getMaxCount();
    return (count / max) * 100;
};
</script>

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

<template>
    <LayoutAuthenticated>
        <Head title="Dashboard Admin" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Inicio</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiHome"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Inicio</span>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Solicitudes -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Total Solicitudes</p>
                            <p class="text-3xl font-bold text-gray-900">{{ stats.total }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-full">
                            <svg viewBox="0 0 24 24" class="w-8 h-8" style="fill: #1B396A;">
                                <path :d="mdiFileDocumentMultiple"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pendientes -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Pendientes</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ stats.pending }}</p>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-full">
                            <svg viewBox="0 0 24 24" class="w-8 h-8" style="fill: #ca8a04;">
                                <path :d="mdiClockOutline"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Aprobadas -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Aprobadas</p>
                            <p class="text-3xl font-bold text-green-600">{{ stats.approved }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-full">
                            <svg viewBox="0 0 24 24" class="w-8 h-8" style="fill: #16a34a;">
                                <path :d="mdiCheckCircle"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rechazadas -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Rechazadas</p>
                            <p class="text-3xl font-bold text-red-600">{{ stats.rejected }}</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-full">
                            <svg viewBox="0 0 24 24" class="w-8 h-8" style="fill: #dc2626;">
                                <path :d="mdiCloseCircle"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solicitudes por Campus -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Solicitudes por Campus</h2>
                
                <div class="space-y-4">
                    <div v-for="institution in topInstitutions.data" :key="institution.id" class="flex items-center gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700 mb-2">{{ institution.nombre }}</p>
                            <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                                <div 
                                    class="h-full bg-gradient-to-r from-[#1B396A] to-[#2563eb] rounded-full transition-all duration-300 flex items-center justify-end pr-2"
                                    :style="{ width: getProgressWidth(institution.solicitudes_count) + '%' }"
                                >
                                </div>
                            </div>
                        </div>
                        <div class="text-right min-w-[3rem]">
                            <p class="text-lg font-bold text-[#1B396A]">{{ institution.solicitudes_count }}</p>
                        </div>
                    </div>

                    <div v-if="!topInstitutions.data || topInstitutions.data.length === 0" class="text-center py-8 text-gray-500">
                        No hay datos disponibles
                    </div>
                </div>

                <!-- Pagination for Top Institutions -->
                <div v-if="topInstitutions.meta && topInstitutions.meta.last_page > 1" class="mt-6 pt-4 border-t border-gray-200">
                    <Pagination 
                        :links="topInstitutions.meta.links" 
                        :total="topInstitutions.meta.total" 
                        :from="topInstitutions.meta.from" 
                        :to="topInstitutions.meta.to" 
                    />
                </div>
            </div>

            <!-- Filter and Table Section -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#374151">
                            <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-800">Filtro de Búsqueda</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="cleanFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition">
                            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                                <path d="M400-240v-80h240v80H400Zm-158 0L15-467l57-57 170 170 366-366 57 57-423 423Zm318-160v-80h240v80H560Zm160-160v-80h240v80H720Z"/>
                            </svg>
                            Limpiar Filtros
                        </button>
                        <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 text-sm font-medium transition">
                            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                                <path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/>
                            </svg>
                            Exportar
                        </button>
                    </div>
                </div>
                
                <div class="text-sm text-gray-500 mb-4">Buscar por nombre de campus o ID</div>
                
                <div class="flex flex-col md:flex-row gap-4 items-end mb-6">
                    <div class="relative w-full md:flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1B396A">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                        </div>
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Buscar por ID, nombre, campus, etc." 
                            class="pl-10 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition"
                        />
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

                <!-- Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-4 tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Campus</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Aprobadas</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Rechazadas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="institution in institutions.data" :key="institution.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ institution.id }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ institution.estado }}</td>
                                <td class="px-6 py-4 text-gray-800 font-semibold">{{ institution.nombre }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-800">
                                        {{ institution.approved_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-800">
                                        {{ institution.rejected_count }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!institutions.data || institutions.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No se encontraron registros.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Table -->
                <div v-if="institutions.meta" class="mt-4 pt-4 border-t border-gray-200 bg-gray-50 px-4 py-3 rounded-b-lg">
                    <Pagination 
                        :links="institutions.meta.links" 
                        :total="institutions.meta.total" 
                        :from="institutions.meta.from" 
                        :to="institutions.meta.to" 
                    />
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
