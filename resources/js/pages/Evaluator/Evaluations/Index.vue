<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import EvaluatorLayout from '@/layouts/EvaluatorLayout.vue';
import Pagination from '@/Shared/Pagination.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { mdiSchool, mdiCheckCircle, mdiCloseCircle, mdiFileDocumentMultiple } from '@mdi/js';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ approved: 0, rejected: 0, evaluadas: 0, total: 0, pendientes: 0 })
    },
    applications: Object,
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
    router.get(route('evaluator.evaluations.index'), { 
        search: value,
        rows: rows.value
    }, { preserveState: true, replace: true });
}, 500);

const cleanFilters = () => {
    search.value = '';
    rows.value = 10;
    router.get(route('evaluator.evaluations.index'), {}, { preserveState: true, replace: true });
};

const onRowsChange = () => {
    router.get(route('evaluator.evaluations.index'), {
        search: search.value,
        rows: rows.value,
    }, { preserveState: true, replace: true, preserveScroll: true });
};

watch(search, (value) => {
    onSearch(value);
});

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Pendiente',
        approved: 'Aprobada',
        rejected: 'Rechazada',
    };
    return labels[status] || status;
};
</script>

<template>
    <EvaluatorLayout>
        <Head title="Historial de Evaluaciones" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Historial de Evaluaciones</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiSchool"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Evaluaciones</span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Aprobadas -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Aprobadas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats?.aprobadas ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-full">
                        <svg viewBox="0 0 24 24" class="w-8 h-8 text-green-600" style="fill: currentColor"><path :d="mdiCheckCircle"/></svg>
                    </div>
                </div>

                <!-- Rechazadas -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Rechazadas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats?.rechazadas ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-full">
                         <svg viewBox="0 0 24 24" class="w-8 h-8 text-red-600" style="fill: currentColor"><path :d="mdiCloseCircle"/></svg>
                    </div>
                </div>

                <!-- Total Evaluadas -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Completadas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats?.evaluadas ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-full">
                         <svg viewBox="0 0 24 24" class="w-8 h-8 text-blue-600" style="fill: currentColor"><path :d="mdiFileDocumentMultiple"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter and Table Section -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-semibold text-gray-800">Filtro de Búsqueda</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="cleanFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition">
                            Limpiar Filtros
                        </button>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row gap-4 items-end mb-6">
                    <div class="relative w-full md:flex-1">
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Buscar por convocatoria o docente..." 
                            class="pl-4 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition"
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
            </div>

            <!-- Listado de Solicitudes Completadas -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Evaluaciones Completadas</h3>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div v-if="applications.data.length === 0" class="text-center py-8 text-gray-500">
                            No has realizado ninguna evaluación aún.
                        </div>

                        <div v-for="application in applications.data" :key="application.evaluation_id" class="border border-gray-300 rounded-lg p-5 hover:shadow-md transition-shadow bg-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            
                            <!-- Columna Izquierda: Datos Principales -->
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-700 mb-1">{{ application.announcement_name }}</h4>
                                <p class="text-gray-600">
                                    {{ application.teacher_name }}
                                </p>
                            </div>

                            <!-- Columna Central: Metadata -->
                            <div class="flex flex-col gap-2 text-sm text-gray-600 md:px-4 md:border-l md:border-r border-gray-200">
                                 <div class="flex items-center gap-2">
                                    <span>Evaluado el {{ application.evaluation_date }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span>{{ application.documents_count }} Documentos adjuntos</span>
                                </div>
                                <div class="flex items-center gap-2 font-medium">
                                    <span>Puntaje: {{ Math.round(application.score) }} pts</span>
                                </div>
                            </div>

                            <!-- Columna Derecha: Acciones -->
                            <div class="flex flex-col items-end gap-3 min-w-[140px]">
                                <span 
                                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border"
                                    :class="{
                                        'bg-green-100 text-green-800 border-green-200': application.status === 'approved',
                                        'bg-red-100 text-red-800 border-red-200': application.status === 'rejected',
                                        'bg-yellow-100 text-yellow-800 border-yellow-200': application.status === 'pending',
                                    }"
                                >
                                    {{ getStatusLabel(application.status) }}
                                </span>
                                
                                <Link 
                                    :href="route('evaluator.evaluations.show', application.evaluation_id)" 
                                    class="w-full text-center bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2"
                                >
                                    Ver Detalles
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <Pagination :links="applications.links" :total="applications.total" :from="applications.from" :to="applications.to" />
                </div>
            </div>
        </div>
    </EvaluatorLayout>
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
