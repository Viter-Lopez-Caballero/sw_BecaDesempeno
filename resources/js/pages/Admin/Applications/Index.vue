<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '@/Shared/Pagination.vue';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { mdiFileDocumentMultiple, mdiAccountPlus, mdiAccountMultiple, mdiEye } from '@mdi/js';
import debounce from 'lodash/debounce';

const props = defineProps({
    applications: Object,
    evaluators: Array, // May be unused now in index, but kept if controller sends it
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const rows = ref(props.filters?.rows || 10);

const rowOptions = [
    { label: '5 Registros', value: 5 },
    { label: '10 Registros', value: 10 },
    { label: '25 Registros', value: 25 },
    { label: '50 Registros', value: 50 },
];

const onRowsChange = () => {
    router.get(route('admin.applications.index'), {
        search: search.value,
        status: status.value,
        rows: rows.value,
    }, { preserveState: true, replace: true, preserveScroll: true });
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Pendiente',
        'approved': 'Aprobada',
        'rejected': 'Rechazada',
    };
    return labels[status] || status;
};

const cleanFilters = () => {
    search.value = '';
    status.value = '';
    rows.value = 10;
    router.get(route('admin.applications.index'), {}, { preserveState: true, replace: true });
};

watch([search, status], debounce(() => {
    router.get(route('admin.applications.index'), {
        search: search.value,
        status: status.value,
        rows: rows.value,
    }, { preserveState: true, replace: true });
}, 300));
</script>

<template>
    <Head title="Solicitudes" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Solicitudes</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Solicitudes</span>
                    </div>
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
                             <path d="M400-240v-80h240v80H400Zm-158 0L15-467l57-57 170 170 366-366 57 57-423 423Zm318-160v-80h240v80H560Zm160-160v-80h240v80H720Z"/>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
                <div class="text-sm text-gray-500 mb-4">Buscar y filtrar solicitudes</div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <!-- Search Input -->
                    <div class="relative w-full">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1B396A">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                        </div>
                         <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Buscar por Docente, Institución..." 
                            class="pl-10 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition" 
                        />
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="w-full">
                         <select 
                            v-model="status" 
                            class="w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition"
                        >
                            <option value="">Todos los estados</option>
                            <option value="pending">Pendiente</option>
                            <option value="approved">Aprobada</option>
                            <option value="rejected">Rechazada</option>
                        </select>
                    </div>
                    <!-- Rows -->
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
                    <table class="w-full text-left">
                        <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-4 whitespace-nowrap">ID</th>
                                <th class="px-6 py-4 whitespace-nowrap">Docente</th>
                                <th class="px-6 py-4 whitespace-nowrap">Institución</th>
                                <th class="px-6 py-4 whitespace-nowrap">Evaluador(es)</th>
                                <th class="px-6 py-4 whitespace-nowrap">Estado</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="(application, index) in applications.data" :key="application.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ applications.meta.from + index }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    <div class="font-semibold text-gray-800">{{ application.user?.name }}</div>
                                </td>
                                 <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ application.campus }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template v-if="application.evaluations && application.evaluations.length > 0">
                                        <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                            <svg style="width:16px;height:16px" viewBox="0 0 24 24" class="text-gray-400">
                                                <path fill="currentColor" :d="mdiAccountMultiple" />
                                            </svg>
                                            {{ application.evaluations.length }} Evaluadores
                                        </span>
                                    </template>
                                    <template v-else-if="application.status === 'approved' || application.status === 'rejected'">
                                        <span class="text-xs text-gray-400 italic">
                                            Sin evaluadores
                                        </span>
                                    </template>
                                    <Link 
                                        v-else 
                                        :href="route('admin.applications.assign_view', application.id)"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded-full transition-colors cursor-pointer"
                                    >
                                        <svg style="width:14px;height:14px" viewBox="0 0 24 24">
                                            <path fill="currentColor" :d="mdiAccountPlus" />
                                        </svg>
                                        Asignar
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span 
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-md bg-white text-[13px] font-bold shadow-sm"
                                            :class="{
                                                'text-green-700': application.status === 'approved',
                                                'text-red-700': application.status === 'rejected',
                                                'text-yellow-700': application.status === 'pending'
                                            }"
                                        >
                                            <span 
                                                class="w-2.5 h-2.5 rounded-full"
                                                :class="{
                                                    'bg-green-500': application.status === 'approved',
                                                    'bg-red-500': application.status === 'rejected',
                                                    'bg-yellow-500': application.status === 'pending'
                                                }"
                                            ></span>
                                            {{ getStatusLabel(application.status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link 
                                            :href="route('admin.applications.show', application.id)"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 transition text-xs font-medium uppercase gap-1 cursor-pointer"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Ver Detalles
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="applications.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron solicitudes.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                 <!-- Pagination and Total -->
                <div class="border-t border-gray-100 bg-gray-50 px-6 py-4" v-if="applications.meta?.links">
                     <Pagination :links="applications.meta.links" :total="applications.meta.total" />
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
