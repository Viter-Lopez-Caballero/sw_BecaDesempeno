<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import Pagination from '@/Shared/Pagination.vue';
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';
import { useCan } from '@/composables/usePermissions';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { mdiFileDocumentMultiple, mdiBookOpenPageVariant, mdiAccountSchool } from '@mdi/js';
import { alertaPregunta, alertaExito, alertaError } from '@/utils/alerts';

// Modal de visualización de archivo
const showModal = ref(false);
const currentFileUrl = ref('');
const currentFileTitle = ref('');

const openFileModal = (document) => {
    if (document.file_path) {
        currentFileUrl.value = `/storage/${document.file_path}`;
        currentFileTitle.value = document.name;
        showModal.value = true;
    }
};

const closeModal = () => {
    showModal.value = false;
    currentFileUrl.value = '';
    currentFileTitle.value = '';
};
 
const props = defineProps({
    documents: {
        type: Object,
        required: true,
        default: () => ({
            data: [],
            links: [],
            from: 0,
            to: 0,
            total: 0
        })
    },
    applications: {
        type: Object,
        default: () => ({
            data: [],
            meta: {
                links: [],
                from: 0,
                to: 0,
                total: 0
            }
        })
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
        default: () => ({})
    },
    activeTab: {
        type: String,
        default: 'requeridos'
    }
});

const search = ref(props.filters.search);
const rows = ref(props.filters.rows || 10);
const sortField = ref(props.filters.order || 'name');
const sortDirection = ref(props.filters.direction || 'asc');
const sortFieldDocentes = ref(props.filters.order || 'user_name');
const currentTab = ref(props.activeTab);

const rowOptions = [
    { label: '5 Registros', value: 5 },
    { label: '10 Registros', value: 10 },
    { label: '25 Registros', value: 25 },
    { label: '50 Registros', value: 50 },
];

const switchTab = (tab) => {
    currentTab.value = tab;
    search.value = '';
    rows.value = 10;
    
    router.get(route(`${props.routeName}index`), { 
        tab: tab,
        rows: 10
    }, { preserveState: false, replace: true });
};

const onSearch = debounce((value) => {
    router.get(route(`${props.routeName}index`), { 
        tab: currentTab.value,
        search: value,
        rows: rows.value,
        order: sortField.value,
        direction: sortDirection.value
    }, { preserveState: true, replace: true });
}, 500);

const onRowsChange = () => {
    router.get(route(`${props.routeName}index`), {
        tab: currentTab.value,
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
    sortField.value = 'name';
    sortDirection.value = 'asc';
    router.get(route(`${props.routeName}index`), { tab: currentTab.value }, { preserveState: true, replace: true });
};

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    router.get(route(`${props.routeName}index`), {
        tab: currentTab.value,
        search: search.value,
        rows: rows.value,
        order: sortField.value,
        direction: sortDirection.value
    }, { preserveState: true, replace: true });
};

const deleteItem = async (id, name) => {
    const confirmed = await alertaPregunta(
        '¿Estás seguro?',
        `Se eliminará el documento "${name}"`
    );
    
    if (confirmed) {
        router.delete(route(`${props.routeName}destroy`, { document: id }), {
            preserveScroll: true,
            onSuccess: () => alertaExito('¡Eliminado!', 'El documento ha sido eliminado'),
            onError: () => alertaError('Error', 'No se pudo eliminar el documento')
        });
    }
};

const toggleActivo = (id, active) => {
    router.post(route('catalog.documents.toggleActive', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            alertaExito(
                '¡Actualizado!',
                active ? 'Documento desactivado' : 'Documento activado'
            );
        },
        onError: () => alertaError('Error', 'No se pudo actualizar el estado')
    });
};

const viewFile = (document) => {
    if (document.file_path) {
        window.open(`/storage/${document.file_path}`, '_blank');
    }
};

const downloadFile = (id) => {
    window.location.href = route('catalog.documents.download', id);
};

// Ordenamiento para la pestaña de Documentos de Docentes
const sortByDocentes = (field) => {
    if (sortFieldDocentes.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortFieldDocentes.value = field;
        sortDirection.value = 'asc';
    }
    router.get(route(`${props.routeName}index`), {
        tab: currentTab.value,
        search: search.value,
        rows: rows.value,
        order: sortFieldDocentes.value,
        direction: sortDirection.value
    }, { preserveState: true, replace: true });
};

const viewDetails = (id) => {
    router.get(route(`${props.routeName}show`, id));
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="title" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiBookOpenPageVariant"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Catálogo</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Documentos</span>
                    </div>
                </div>
                <Link v-if="useCan('documents.create') && currentTab === 'requeridos'" :href="route(`${routeName}create`)" class="w-full md:w-auto justify-center px-4 py-2.5 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/>
                    </svg>
                    Agregar
                </Link>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="flex">
                    <button 
                        @click="switchTab('requeridos')" 
                        :class="[
                            'flex-1 px-6 py-4 text-base font-medium transition-all flex items-center justify-center gap-2 border-r border-gray-200 cursor-pointer',
                            currentTab === 'requeridos' 
                                ? 'bg-[#1B396A] text-white' 
                                : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                            <path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Z"/>
                        </svg>
                        Documentos requeridos en la convocatoria
                    </button>
                    <button 
                        @click="switchTab('docentes')" 
                        :class="[
                            'flex-1 px-6 py-4 text-base font-medium transition-all flex items-center justify-center gap-2 cursor-pointer',
                            currentTab === 'docentes' 
                                ? 'bg-[#1B396A] text-white' 
                                : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]"
                    >
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor">
                            <path :d="mdiAccountSchool"/>
                        </svg>
                        Documentos del docente
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
                            <path d="M400-240v-80h240v80H400Zm-158 0L15-467l57-57 170 170 366-366 57 57-423 423Zm318-160v-80h240v80H560Zm160-160v-80h240v80H720Z"/>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
                <div class="text-sm text-gray-500 mb-4">Buscar y filtrar documentos</div>
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="relative w-full md:flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1B396A">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                        </div>
                        <input v-model="search" type="text" :placeholder="currentTab === 'requeridos' ? 'Buscar por nombre o descripción...' : 'Buscar por profesor, campus o convocatoria...'" class="pl-10 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition" />
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

            <!-- Main Content -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">

                <!-- Tabla de Documentos Requeridos -->
                <div v-if="currentTab === 'requeridos'">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                                <tr>
                                    <th scope="col" class="px-6 py-4 tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-4 tracking-wider">
                                        <div @click="sortBy('name')" class="flex items-center gap-1 cursor-pointer hover:text-gray-200 transition">
                                            Nombre
                                            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor" :class="{ 'opacity-100': sortField === 'name', 'opacity-50': sortField !== 'name' }">
                                                <path d="M320-440v-287L217-624l-57-56 200-200 200 200-57 56-103-103v287h-80ZM600-80 400-280l57-56 103 103v-287h80v287l103-103 57 56L600-80Z"/>
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 tracking-wider">
                                        <div @click="sortBy('description')" class="flex items-center gap-1 cursor-pointer hover:text-gray-200 transition">
                                            Descripción
                                            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor" :class="{ 'opacity-100': sortField === 'description', 'opacity-50': sortField !== 'description' }">
                                                <path d="M320-440v-287L217-624l-57-56 200-200 200 200-57 56-103-103v287h-80ZM600-80 400-280l57-56 103 103v-287h80v287l103-103 57 56L600-80Z"/>
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center tracking-wider">Archivo</th>
                                    <th scope="col" class="px-6 py-4 text-center tracking-wider">Activo</th>
                                    <th scope="col" class="px-6 py-4 text-center tracking-wider">Acciones</th>
                                </tr>
                            </thead>
            <tbody class="divide-y divide-gray-200">
                                <tr v-for="(document, index) in (documents?.data || [])" :key="document.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ documents.from + index }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-800">{{ document.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ document.description || 'Sin descripción' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button
                                            v-if="document.file_path"
                                            @click="openFileModal(document)"
                                            class="inline-flex items-center gap-2 px-4 py-2 border border-[#1B396A] rounded-lg text-[#1B396A] font-semibold hover:bg-[#1B396A] hover:text-white transition cursor-pointer text-sm"
                                            title="Visualizar archivo"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="currentColor">
                                                <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z"/>
                                            </svg>
                                            Visualizar
                                        </button>
                                        <button
                                            v-else
                                            disabled
                                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-400 font-semibold cursor-not-allowed text-sm"
                                            title="Sin archivo"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="currentColor">
                                                <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z"/>
                                            </svg>
                                            Sin archivo
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" :checked="document.active" @change="toggleActivo(document.id, document.active)" class="sr-only peer" />
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#1B396A]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1B396A]"></div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <Link v-if="useCan('documents.edit')" :href="route(`${routeName}edit`, { document: document.id })" class="p-2 text-[#1B396A] border border-[#1B396A] rounded-full hover:bg-[#1B396A] hover:text-white transition group cursor-pointer" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                    <path d="M200-200h57l391-391-57-57-391 391v57Zm-40 80q-17 0-28.5-11.5T120-160v-97q0-16 6-30.5t17-25.5l505-504q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L313-143q-11 11-25.5 17t-30.5 6h-97Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/>
                                                </svg>
                                            </Link>
                                            <button v-if="useCan('documents.delete')" @click="deleteItem(document.id, document.name)" class="p-2 text-red-600 border border-red-600 rounded-full hover:bg-red-600 hover:text-white transition group cursor-pointer" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!documents?.data || documents.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        No se encontraron registros
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Documentos Requeridos -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <Pagination 
                            :links="documents.links || []" 
                            :from="documents.from || 0" 
                            :to="documents.to || 0" 
                            :total="documents.total || 0" 
                        />
                    </div>
                </div>

                <!-- Tabla de Documentos de Docentes -->
                <div v-else-if="currentTab === 'docentes'">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                                <tr>
                                    <th scope="col" class="px-6 py-4 tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-4 tracking-wider">
                                        <div @click="sortByDocentes('user_name')" class="flex items-center gap-1 cursor-pointer hover:text-gray-200 transition">
                                            Profesor
                                            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor" :class="{ 'opacity-100': sortFieldDocentes === 'user_name', 'opacity-50': sortFieldDocentes !== 'user_name' }">
                                                <path d="M320-440v-287L217-624l-57-56 200-200 200 200-57 56-103-103v287h-80ZM600-80 400-280l57-56 103 103v-287h80v287l103-103 57 56L600-80Z"/>
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 tracking-wider">
                                        <div @click="sortByDocentes('institution_name')" class="flex items-center gap-1 cursor-pointer hover:text-gray-200 transition">
                                            Institución
                                            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor" :class="{ 'opacity-100': sortFieldDocentes === 'institution_name', 'opacity-50': sortFieldDocentes !== 'institution_name' }">
                                                <path d="M320-440v-287L217-624l-57-56 200-200 200 200-57 56-103-103v287h-80ZM600-80 400-280l57-56 103 103v-287h80v287l103-103 57 56L600-80Z"/>
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 tracking-wider">
                                        <div @click="sortByDocentes('announcement_name')" class="flex items-center gap-1 cursor-pointer hover:text-gray-200 transition">
                                            Convocatoria
                                            <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor" :class="{ 'opacity-100': sortFieldDocentes === 'announcement_name', 'opacity-50': sortFieldDocentes !== 'announcement_name' }">
                                                <path d="M320-440v-287L217-624l-57-56 200-200 200 200-57 56-103-103v287h-80ZM600-80 400-280l57-56 103 103v-287h80v287l103-103 57 56L600-80Z"/>
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center tracking-wider">Documentos</th>
                                    <th scope="col" class="px-6 py-4 text-center tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="(application, index) in (applications?.data || [])" :key="application.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ applications.meta.from + index }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="text-sm font-semibold text-gray-800">{{ application.teacher?.name || 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 max-w-[200px] truncate" :title="application.campus">
                                        {{ application.campus }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 max-w-[200px] truncate" :title="application.announcement?.name">
                                        {{ application.announcement?.name || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ application.documents_count || 0 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="viewDetails(application.id)" class="p-2 text-[#1B396A] border border-[#1B396A] rounded-full hover:bg-[#1B396A] hover:text-white transition cursor-pointer" title="Ver Detalles">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                    <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!applications?.data || applications.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        No se encontraron registros
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Documentos de Docentes -->
                    <div v-if="applications?.meta?.links && applications.meta.links.length > 0" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <Pagination 
                            :links="applications.meta.links" 
                            :from="applications.meta.from || 0" 
                            :to="applications.meta.to || 0" 
                            :total="applications.meta.total || 0" 
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Visualizar Archivo -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>

                    <!-- Modal Content -->
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[90vh] flex flex-col">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between p-5 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <h2 class="text-lg font-semibold text-gray-900">{{ currentFileTitle }}</h2>
                            </div>
                            <button @click="closeModal" class="text-gray-400 hover:text-gray-800 transition-colors cursor-pointer p-1 rounded-lg hover:bg-gray-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- File Viewer -->
                        <div class="flex-1 overflow-hidden rounded-b-2xl">
                            <iframe :src="currentFileUrl" class="w-full h-full" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
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

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}
</style>
