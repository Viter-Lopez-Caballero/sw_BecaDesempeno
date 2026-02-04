<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import Pagination from '@/Shared/Pagination.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { useCan } from '@/composables/usePermissions';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { mdiBullhorn } from '@mdi/js';

const props = defineProps({
    convocatorias: {
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
const showViewer = ref(false);
const currentFile = ref(null);
const rows = ref(props.filters.rows || 10);
const sortField = ref(props.filters.order || 'id');
const sortDirection = ref(props.filters.direction || 'asc');

const rowOptions = [
    { label: '5 Registros', value: 5 },
    { label: '10 Registros', value: 10 },
    { label: '25 Registros', value: 25 },
    { label: '50 Registros', value: 50 },
];

const onSearch = debounce((value) => {
    router.get(route(`${props.routeName}index`), {
        search: value,
        rows: rows.value,
        order: sortField.value,
        direction: sortDirection.value
    }, { preserveState: true, replace: true });
}, 500);

const onRowsChange = () => {
    router.get(route(`${props.routeName}index`), {
        search: search.value,
        rows: rows.value,
        order: sortField.value,
        direction: sortDirection.value
    }, { preserveState: true, replace: true });
};

watch(search, (value) => {
    onSearch(value);
});

const cleanFilters = () => {
    search.value = '';
    rows.value = 10;
    sortField.value = 'id';
    sortDirection.value = 'asc';
    router.get(route(`${props.routeName}index`), {}, { preserveState: true, replace: true });
};

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    router.get(route(`${props.routeName}index`), {
        search: search.value,
        rows: rows.value,
        order: sortField.value,
        direction: sortDirection.value
    }, { preserveState: true, replace: true });
};

const deleteConvocatoria = (id) => {
    if (confirm('¿Estás seguro de eliminar esta convocatoria?')) {
        router.delete(route(`${props.routeName}destroy`, { convocatoria: id }));
    }
};

const getEstadoBadge = (estado) => {
    const badges = {
        activa: 'bg-green-100 text-green-800',
        cerrada: 'bg-red-100 text-red-800',
        pendiente: 'bg-yellow-100 text-yellow-800'
    };
    return badges[estado] || 'bg-gray-100 text-gray-800';
};

const viewFile = (convocatoria) => {
    if (convocatoria.archivo_path) {
        currentFile.value = {
            url: `/storage/${convocatoria.archivo_path}`,
            nombre: convocatoria.archivo_nombre,
            tipo: convocatoria.archivo_tipo,
            convocatoria: convocatoria.nombre
        };
        showViewer.value = true;
    }
};

const closeViewer = () => {
    showViewer.value = false;
    currentFile.value = null;
};
</script>

<template>
    <LayoutAuthenticated>

        <Head :title="title" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiBullhorn" />
                        </svg>
                        <span class="text-gray-900 font-semibold">Convocatorias</span>
                    </div>
                </div>
                <Link v-if="useCan('convocatorias.create')" :href="route(`${routeName}create`)"
                    class="px-4 py-2.5 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"
                        fill="currentColor">
                        <path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z" />
                    </svg>
                    Agregar
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Convocatorias Activas -->
                <div
                    class="bg-white rounded-lg shadow-md border-l-4 border-yellow-500 p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Convocatorias
                                Activas</p>
                            <p class="text-2xl font-bold text-gray-900">{{convocatorias.data.filter(c => c.estado ===
                                'activa').length }}</p>
                        </div>
                        <div class="p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Convocatorias Finalizadas -->
                <div
                    class="bg-white rounded-lg shadow-md border-l-4 border-green-500 p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Convocatorias
                                Finalizadas</p>
                            <p class="text-2xl font-bold text-gray-800">{{convocatorias.data.filter(c => c.estado ===
                                'cerrada').length }}</p>
                        </div>
                        <div class="p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"
                            fill="#374151">
                            <path
                                d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z" />
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-800">Filtro de Búsqueda</h2>
                    </div>
                    <button @click="cleanFilters"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px"
                            fill="currentColor">
                            <path
                                d="M400-240v-80h240v80H400Zm-158 0L15-467l57-57 170 170 366-366 57 57-423 423Zm318-160v-80h240v80H560Zm160-160v-80h240v80H720Z" />
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
                <div class="text-sm text-gray-500 mb-4">Buscar y filtrar convocatorias</div>
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="relative w-full md:flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"
                                fill="#1B396A">
                                <path
                                    d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z" />
                            </svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Buscar..."
                            class="pl-10 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition" />
                    </div>
                    <div class="w-full md:w-52 flex-shrink-0">
                        <VueSelect v-model="rows" :options="rowOptions" :reduce="option => option.value"
                            :searchable="false" :clearable="false" placeholder="Registros" class="vue-select-custom"
                            @option:selected="onRowsChange" />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-center" style="width: 80px;">
                                    <button @click="sortBy('id')"
                                        class="flex items-center justify-center gap-1 hover:text-gray-200 transition w-full">
                                        <span>ID</span>
                                        <svg v-if="sortField === 'id'" xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4" :class="sortDirection === 'asc' ? 'rotate-0' : 'rotate-180'"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-4 text-left">
                                    <button @click="sortBy('nombre')"
                                        class="flex items-center gap-1 hover:text-gray-200 transition">
                                        <span>Nombre</span>
                                        <svg v-if="sortField === 'nombre'" xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4" :class="sortDirection === 'asc' ? 'rotate-0' : 'rotate-180'"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center" style="width: 120px;">
                                    <button @click="sortBy('anio')"
                                        class="flex items-center justify-center gap-1 hover:text-gray-200 transition w-full">
                                        <span>Año</span>
                                        <svg v-if="sortField === 'anio'" xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4" :class="sortDirection === 'asc' ? 'rotate-0' : 'rotate-180'"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center" style="width: 150px;">
                                    <button @click="sortBy('estado')"
                                        class="flex items-center justify-center gap-1 hover:text-gray-200 transition w-full">
                                        <span>Estado</span>
                                        <svg v-if="sortField === 'estado'" xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4" :class="sortDirection === 'asc' ? 'rotate-0' : 'rotate-180'"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center" style="width: 120px;">Archivo</th>
                                <th scope="col" class="px-6 py-4 text-center" style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="convocatoria in convocatorias.data" :key="convocatoria.id"
                                class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-center text-gray-900 font-medium">{{ convocatoria.id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0" style="fill: #1B396A;">
                                            <path :d="mdiBullhorn" />
                                        </svg>
                                        <span class="text-gray-900 font-medium">{{ convocatoria.nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-900">{{ convocatoria.anio }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span v-if="convocatoria.estado === 'activa'"
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activa
                                    </span>
                                    <span v-else-if="convocatoria.estado === 'cerrada'"
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Cerrada
                                    </span>
                                    <span v-else-if="convocatoria.estado === 'pendiente'"
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendiente
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div v-if="convocatoria.archivo_path" class="flex items-center justify-center gap-2">
                                        <button @click="viewFile(convocatoria)"
                                            class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 font-medium transition"
                                            title="Ver archivo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <a :href="route('convocatorias.download', convocatoria.id)"
                                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium transition"
                                            title="Descargar archivo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <span v-else class="text-gray-400 text-xs">Sin archivo</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link v-if="useCan('convocatorias.edit')"
                                            :href="route(`${routeName}edit`, { convocatoria: convocatoria.id })"
                                            class="text-[#1B396A] hover:text-[#0f2347] transition" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20px"
                                                viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                <path
                                                    d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z" />
                                            </svg>
                                        </Link>
                                        <button v-if="useCan('convocatorias.destroy')"
                                            @click="deleteConvocatoria(convocatoria.id)"
                                            class="text-red-600 hover:text-red-800 transition" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20px"
                                                viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                <path
                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <Pagination :links="convocatorias.meta.links" :total="convocatorias.meta.total"
                        :from="convocatorias.meta.from" :to="convocatorias.meta.to" />
                </div>
            </div>

            <!-- Visor de Archivos -->
            <Transition name="slide-up">
                <div v-if="showViewer" class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-[#1B396A] to-[#2B4A7E] px-6 py-4 flex items-center justify-between">
                        <div class="text-white">
                            <h3 class="text-lg font-bold">{{ currentFile?.convocatoria }}</h3>
                            <p class="text-sm text-blue-100 mt-1">{{ currentFile?.nombre }}</p>
                        </div>
                        <button @click="closeViewer" class="text-white hover:text-gray-200 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-100 rounded-lg overflow-hidden" style="height: 600px;">
                            <iframe v-if="currentFile?.tipo?.includes('pdf')" 
                                :src="currentFile?.url" 
                                class="w-full h-full border-0"
                                title="Visor de archivo">
                            </iframe>
                            <div v-else-if="currentFile?.tipo?.startsWith('image/')" class="h-full flex items-center justify-center bg-gray-900">
                                <img :src="currentFile?.url" :alt="currentFile?.nombre" class="max-h-full max-w-full object-contain" />
                            </div>
                            <div v-else class="h-full flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-600 font-medium mb-2">Vista previa no disponible</p>
                                    <p class="text-sm text-gray-500 mb-4">Este tipo de archivo no se puede visualizar en el navegador</p>
                                    <a :href="route('convocatorias.download', currentFile?.id)" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Descargar Archivo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </LayoutAuthenticated>
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

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s ease;
}

.slide-up-enter-from {
    opacity: 0;
    transform: translateY(20px);
}

.slide-up-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>
