<script setup>
import { Head, router } from '@inertiajs/vue3';
import SuperAdminLayout from '@/layouts/SuperAdminLayout.vue';
import SolicitudesChart from '@/components/Dashboard/SolicitudesChart.vue';
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { mdiFileDocumentMultiple, mdiCheckCircle, mdiCloseCircle, mdiMagnify, mdiFilterVariant, mdiHome } from '@mdi/js';

const props = defineProps({
    stats: Object,
    chart: Object,
    filters: Object,
    instituciones: Array,
    estados: Array,
});

const search = ref(props.filters.search || '');
const institucionId = ref(props.filters.institucion_id || null);
const estadoId = ref(props.filters.estado_id || null);

// Chart Data Setup
const chartData = computed(() => ({
    labels: props.chart.labels,
    datasets: [
        {
            label: 'Aprobados',
            backgroundColor: '#10B981', // Green-500
            data: props.chart.approved,
            borderRadius: 4,
            barThickness: 20,
        },
        {
            label: 'Rechazados',
            backgroundColor: '#EF4444', // Red-500
            data: props.chart.rejected,
            borderRadius: 4,
            barThickness: 20,
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                usePointStyle: true,
                padding: 20,
            }
        },
        title: {
            display: false,
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                 autoSkip: false,
                 maxRotation: 45,
                 minRotation: 0
            }
        },
        y: {
            type: 'linear',
            beginAtZero: true,
            grid: {
                borderDash: [5, 5],
                color: '#f3f4f6'
            },
            ticks: {
                precision: 0
            }
        }
    },
    // Customize bar separation if needed, or stick to defaults
    barPercentage: 0.7,
    categoryPercentage: 0.8
};

// Filter Logic
const updateFilters = debounce(() => {
    router.get(route('superadmin.dashboard'), {
        search: search.value,
        institucion_id: institucionId.value,
        estado_id: estadoId.value,
    }, { preserveState: true, replace: true });
}, 500);

const cleanFilters = () => {
    search.value = '';
    institucionId.value = null;
    estadoId.value = null;
    updateFilters();
};

watch([search, institucionId, estadoId], () => {
    updateFilters();
});

</script>

<template>
    <SuperAdminLayout>
        <Head title="Dashboard" />

        <div class="space-y-8">
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total -->
                <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-500 p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Solicitudes Totales</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                        </div>
                        <div class="p-2">
                             <svg viewBox="0 0 24 24" class="w-8 h-8 text-blue-600" style="fill: currentColor"><path :d="mdiFileDocumentMultiple"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Aprobadas -->
                <div class="bg-white rounded-lg shadow-md border-l-4 border-green-500 p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Aprobadas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.approved }}</p>
                        </div>
                        <div class="p-2">
                             <svg viewBox="0 0 24 24" class="w-8 h-8 text-green-600" style="fill: currentColor"><path :d="mdiCheckCircle"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        {{ stats.total > 0 ? Math.round((stats.approved / stats.total) * 100) : 0 }}% del total
                    </div>
                </div>

                <!-- Rechazadas -->
                <div class="bg-white rounded-lg shadow-md border-l-4 border-red-500 p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Rechazadas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.rejected }}</p>
                        </div>
                        <div class="p-2">
                            <svg viewBox="0 0 24 24" class="w-8 h-8 text-red-600" style="fill: currentColor"><path :d="mdiCloseCircle"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        {{ stats.total > 0 ? Math.round((stats.rejected / stats.total) * 100) : 0 }}% del total
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-4">
                     <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#374151">
                            <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-800">Filtros de Búsqueda</h2>
                    </div>
                    
                    <button @click="cleanFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                            <path d="M400-240v-80h240v80H400Zm-158 0L15-467l57-57 170 170 366-366 57 57-423 423Zm318-160v-80h240v80H560Zm160-160v-80h240v80H720Z"/>
                        </svg>
                        Limpiar todo
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <!-- Search -->
                    <div class="w-full">
                         <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Buscar por nombre</label>
                        <div class="relative">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg viewBox="0 0 24 24" class="w-5 h-5 text-gray-400" style="fill: currentColor"><path :d="mdiMagnify"/></svg>
                            </div>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Escribe para buscar..." 
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 transition text-sm"
                            />
                        </div>
                    </div>

                    <!-- Campus Filter -->
                    <div class="w-full">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Filtrar por Campus</label>
                        <VueSelect
                            v-model="institucionId"
                            :options="instituciones"
                            :reduce="option => option.id"
                            label="nombre"
                            :clearable="true"
                            placeholder="Todos los campus"
                            class="vue-select-custom"
                        />
                    </div>

                    <!-- State Filter -->
                    <div class="w-full">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Filtrar por Estado</label>
                         <VueSelect
                            v-model="estadoId"
                            :options="estados"
                            :reduce="option => option.id"
                            label="nombre"
                            :clearable="true"
                            placeholder="Todos los estados"
                            class="vue-select-custom"
                        />
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Resultado de Evaluación de Solicitudes por Campus</h2>
                    <p class="text-sm text-gray-500">Distribución de aprobados y rechazados</p>
                </div>
                
                <div class="w-full relative min-h-[256px] md:min-h-[400px]">
                    <SolicitudesChart :chart-data="chartData" :chart-options="chartOptions" />
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>

<style scoped>
:deep(.vue-select-custom .vs__dropdown-toggle) {
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.25rem 0.5rem;
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
    z-index: 50;
}
:deep(.vue-select-custom .vs__dropdown-option--highlight) {
    background: #1B396A;
    color: white;
}
</style>
