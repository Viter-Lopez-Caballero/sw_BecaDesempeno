<template>
    <LayoutAuthenticated>
        <Head title="Plantillas de Documentos" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">Plantillas de Documentos</h1>
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
                        <span class="text-gray-900 font-semibold">Plantillas</span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                    <Link 
                        v-if="useCan('templates.create')"
                        :href="route('catalog.templates.create', { type: activeTab })"
                        class="w-full md:w-auto justify-center px-4 py-2.5 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition flex items-center gap-2 font-medium cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                            <path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/>
                        </svg>
                        Agregar
                    </Link>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="flex">
                    <button 
                        @click="activeTab = 'recognition'"
                        :class="[
                            'flex-1 px-6 py-4 text-base font-medium transition-all flex items-center justify-center gap-2 border-r border-gray-200 cursor-pointer',
                            activeTab === 'recognition'
                                ? 'bg-[#1B396A] text-white'
                                : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]"
                    >
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor">
                            <path :d="mdiStar"/>
                        </svg>
                        Reconocimientos (Evaluadores)
                    </button>
                    <button 
                        @click="activeTab = 'acceptance'"
                        :class="[
                            'flex-1 px-6 py-4 text-base font-medium transition-all flex items-center justify-center gap-2 cursor-pointer',
                            activeTab === 'acceptance'
                                ? 'bg-[#1B396A] text-white'
                                : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]"
                    >
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor">
                            <path :d="mdiSchool"/>
                        </svg>
                        Cartas de Aceptación (Docentes)
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
                    <button @click="resetFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                            <path d="M400-240v-80h240v80H400Zm-158 0L15-467l57-57 170 170 366-366 57 57-423 423Zm318-160v-80h240v80H560Zm160-160v-80h240v80H720Z"/>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
                <div class="text-sm text-gray-500 mb-4">Buscar plantillas por nombre o archivo</div>
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="relative w-full md:flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1B396A">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Buscar..." class="pl-10 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition" />
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
                                <th scope="col" class="px-6 py-4 tracking-wider w-16 text-center">#</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Archivo</th>
                                <th scope="col" class="px-6 py-4 tracking-wider text-center">Activa</th>
                                <th scope="col" class="px-6 py-4 tracking-wider text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                             <tr v-if="filteredTemplates.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium">No hay plantillas registradas</p>
                                    <p class="text-sm mt-1">Sube una nueva plantilla para comenzar.</p>
                                </td>
                            </tr>
                            <template v-for="(template, index) in filteredTemplates" :key="template.id">
                                <tr 
                                    class="hover:bg-gray-50 transition"
                                    :class="{'bg-blue-50': expandedRows[template.id]}"
                                >
                                    <td class="px-6 py-4 text-center text-gray-500 font-bold">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">
                                        {{ template.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                            </svg>
                                            <span class="text-gray-600 truncate max-w-[200px]" :title="template.file_name">
                                                {{ template.file_name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                :checked="template.is_active" 
                                                @change="toggleActive(template)" 
                                                class="sr-only peer" 
                                                :disabled="!useCan('templates.edit')"
                                            />
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#1B396A]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1B396A]"></div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button 
                                                @click="togglePreview(template.id)"
                                                class="p-2 border rounded-full transition group cursor-pointer flex items-center justify-center"
                                                :class="expandedRows[template.id] ? 'bg-[#1B396A] text-white border-[#1B396A]' : 'text-[#1B396A] border-[#1B396A] hover:bg-[#1B396A] hover:text-white'"
                                                title="Vista previa"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                    <path v-if="!expandedRows[template.id]" d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z"/>
                                                    <path v-else d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L66-81l57-57 3.5 3.5q8-4 16.5-7t17-3q51 0 91.5 28t62.5 70.5q4 8 7 16.5t3 16.5l209 209 57-57Zm-473-473 58 58q-18 38-11 77.5t37 72.5l58 58q-26-9-46.5-27t-32.5-42q-12-24-15-51.5t7-55.5Zm303 303-31 31q-27 12-58 13.5T480-280q-112 0-207.5-51T128-500q20-41 49.5-74t66.5-58l56 56q-25 15-46 32t-38 38q38 67 101.5 106.5T480-360q21 0 41.5-2t40.5-8Z"/>
                                                </svg>
                                            </button>
                                            <button 
                                                v-if="useCan('templates.destroy')"
                                                @click="deleteTemplate(template)"
                                                class="p-2 text-red-600 border border-red-600 rounded-full hover:bg-red-600 hover:text-white transition group cursor-pointer"
                                                title="Eliminar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Inline Preview Row -->
                                <tr v-if="expandedRows[template.id]">
                                    <td colspan="5" class="px-6 py-6 bg-gray-50 border-b border-gray-200">
                                        <div class="flex flex-col gap-4 animate-fadeIn">
                                            <div class="flex justify-between items-center">
                                                <h3 class="font-bold text-gray-800 text-lg">Vista Previa: {{ template.name }}</h3>
                                            </div>
                                            <div class="w-full h-[600px] border border-gray-300 rounded-lg overflow-hidden bg-white relative">
                                                <div class="absolute inset-0 flex items-center justify-center text-gray-400 z-0">
                                                    Cargando vista previa...
                                                </div>
                                                <iframe 
                                                    :src="route('catalog.templates.stream', template.id)" 
                                                    class="w-full h-full relative z-10" 
                                                    frameborder="0"
                                                ></iframe>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </LayoutAuthenticated>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { ref, computed, watch } from 'vue';
import { mdiBookOpenPageVariant, mdiFileDocumentMultiple, mdiStar, mdiSchool } from '@mdi/js';
import { alertaPregunta, alertaExito, alertaError } from '@/utils/alerts.js';
import { useCan } from '@/composables/usePermissions';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
    templates: Array,
});

const activeTab = ref('recognition'); // recognition | acceptance
const expandedRows = ref({});
const search = ref('');
const rows = ref(10);

const rowOptions = [
    { label: '5 Registros', value: 5 },
    { label: '10 Registros', value: 10 },
    { label: '25 Registros', value: 25 },
    { label: '50 Registros', value: 50 },
];

const resetFilters = () => {
    search.value = '';
    rows.value = 10;
};

const filteredTemplates = computed(() => {
    let filtered = props.templates.filter(t => t.type === activeTab.value);
    
    if (search.value) {
        const query = search.value.toLowerCase();
        filtered = filtered.filter(t => 
            t.name.toLowerCase().includes(query) || 
            t.file_name.toLowerCase().includes(query)
        );
    }
    
    // Default sort by latest (assuming API returns latest first, but good to ensure)
    // The previous implementation utilized props.templates which came from Template::latest()->get()
    
    return filtered.slice(0, rows.value);
});

const toggleActive = (template) => {
  router.post(route('catalog.templates.toggle-active', template.id), {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
          const msg = template.is_active 
            ? 'Plantilla desactivada correctamente.' 
            : 'Plantilla activada correctamente.';
          alertaExito('¡Actualizado!', msg);
      }
  });
};

const deleteTemplate = async (template) => {
    const confirmed = await alertaPregunta(
        '¿Estás seguro?',
        'Esta acción eliminará la plantilla permanentemente.'
    );

    if (confirmed) {
        router.delete(route('catalog.templates.destroy', template.id), {
            onSuccess: () => {
                alertaExito('¡Eliminado!', 'La plantilla ha sido eliminada.');
            }
        });
    }
};

const togglePreview = (id) => {
    if (expandedRows.value[id]) {
        expandedRows.value[id] = false;
    } else {
        // Close others? Optional: expandedRows.value = {}; 
        expandedRows.value[id] = true;
    }
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
```
