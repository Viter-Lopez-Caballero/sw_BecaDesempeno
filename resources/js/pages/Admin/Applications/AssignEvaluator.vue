<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { ref, computed, watch } from 'vue';
import { 
    mdiAccountPlus, 
    mdiChevronRight, 
    mdiDelete,
    mdiAccountSchool
} from '@mdi/js';
import { alertaExito, alertaError, alertaCargando, cerrarAlerta } from '@/utils/alerts.js';

const props = defineProps({
    application: Object,
    evaluators: Array,
});

const selectedEvaluatorIds = ref([]);

// Computed to get full objects for the table based on IDs
const selectedEvaluatorsObjects = computed(() => {
    return props.evaluators.filter(e => selectedEvaluatorIds.value.includes(e.id));
});

const availableEvaluators = computed(() => {
    // Filter out already selected evaluators from the dropdown options if desired, 
    // or keep them but they are handled by v-model. 
    // Usually better to keep them selectable or highlight them.
    // For now returning all.
    return props.evaluators;
});

const form = useForm({
    application_id: props.application.id,
    evaluator_ids: [],
});

const unwrap = (obj) => {
    return obj && obj.data ? obj.data : obj;
};

const getUser = () => unwrap(props.application.user);
const getConvocatoria = () => unwrap(props.application.announcement);

// Update form when selection changes
watch(selectedEvaluatorIds, (newVal) => {
    form.evaluator_ids = newVal;
});

const removeEvaluator = (id) => {
    selectedEvaluatorIds.value = selectedEvaluatorIds.value.filter(val => val !== id);
};

const submit = () => {
    if (selectedEvaluatorIds.value.length === 0) return;
    // Limpiar errores previos
    form.clearErrors();
    alertaCargando('Asignando', 'Por favor espera...');
    form.post(route('admin.applications.assign'), {
        preserveScroll: true,
        onSuccess: () => {
            cerrarAlerta();
            alertaExito('Asignación Completada', 'Los evaluadores han sido asignados correctamente.');
        },
        onError: () => {
            cerrarAlerta();
            alertaError('Error', 'No se pudo completar la asignación. Por favor verifica los datos.');
        }
    });
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Pendiente',
        'approved': 'Aprobada',
        'rejected': 'Rechazada',
    };
    return labels[status] || status;
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
};

const filterEvaluators = (options, search) => {
    return options.filter(option => {
        const label = option.name.toLowerCase();
        const institution = (option.institution?.name || '').toLowerCase();
        const query = search.toLowerCase();
        return label.includes(query) || institution.includes(query);
    });
};
</script>

<template>
    <Head title="Asignar Evaluador" />

    <AdminLayout>
        <div class="p-6 space-y-6 max-w-7xl mx-auto">
            <!-- Header with Breadcrumbs -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">Asignar Evaluador</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiAccountPlus"/>
                        </svg>
                        <Link :href="route('admin.applications.index')" class="text-gray-700 font-medium hover:text-[#1B396A]">Solicitudes</Link>
                         <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Asignar Evaluador</span>
                    </div>
                </div>
                 <Link :href="route('admin.applications.index')" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium bg-white cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <!-- Solicitud Info Card -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
                 <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-100 pb-4 mb-6 gap-2 md:gap-4">
                     <!-- Badge Estado -->
                     <span 
                        class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide w-fit self-start md:self-auto md:order-last"
                        :class="{
                            'bg-yellow-100 text-yellow-800': application.status === 'pending',
                            'bg-green-100 text-green-800': application.status === 'approved',
                            'bg-red-100 text-red-800': application.status === 'rejected',
                        }"
                    >
                        {{ getStatusLabel(application.status) }}
                    </span>
                    <h2 class="text-lg font-bold text-gray-900 md:order-first">Información General</h2>
                 </div>
                 
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Profesor</h3>
                        <p class="text-md font-medium text-gray-900">{{ getUser()?.name || 'Completar datos' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Institución</h3>
                        <p class="text-md font-medium text-gray-900">{{ application.campus || getUser()?.institucion?.name || 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Convocatoria</h3>
                        <p class="text-md font-medium text-gray-900">{{ getConvocatoria()?.name || 'General' }}</p>
                    </div>

                    <div>
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Área de Procedencia</h3>
                        <p class="text-md font-medium text-gray-900">{{ getUser()?.priority_area?.name || 'No registrada' }}</p>
                    </div>

                    <div>
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Subárea</h3>
                        <p class="text-md font-medium text-gray-900">{{ getUser()?.sub_area?.name || 'No registrada' }}</p>
                    </div>

                     <div>
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Fecha de Solicitud</h3>
                        <p class="text-md font-medium text-gray-900">{{ formatDate(application.created_at) }}</p>
                    </div>

                    <div>
                        <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Tipo de Plaza</h3>
                        <p class="text-md font-medium text-gray-900">{{ application.position_full_type || 'No especificado' }}</p>
                    </div>
                </div>
            </div>

            <!-- Assignment Card -->
             <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="border-b border-gray-100 pb-4 mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Seleccionar Evaluador</h2>
                    <p class="text-sm text-gray-500">Busque y seleccione los evaluadores que revisarán esta solicitud.</p>
                </div>
                
                <form @submit.prevent="submit">
                    <div class="space-y-8">
                        <!-- Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Buscar Evaluador</label>
                            <VueSelect
                                v-model="selectedEvaluatorIds"
                                :options="availableEvaluators"
                                :reduce="evaluator => evaluator.id"
                                :filter="filterEvaluators"
                                label="name"
                                multiple
                                placeholder="Escribe para buscar..."
                                class="vue-select-custom"
                            >
                                <template #option="{ name, institution }">
                                    <div class="flex flex-col py-1">
                                        <span class="font-medium text-gray-900 text-sm">{{ name }}</span>
                                        <span class="text-xs text-gray-500">{{ institution?.name || 'Sin institución' }}</span>
                                    </div>
                                </template>
                            </VueSelect>
                        </div>

                        <!-- Table of Selected Evaluators -->
                        <div class="overflow-hidden border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#1B396A]">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider w-16">
                                            #
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                            Institución
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider w-24">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(evaluator, index) in selectedEvaluatorsObjects" :key="evaluator.id" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ evaluator.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ evaluator.institution?.name || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                type="button" 
                                                @click="removeEvaluator(evaluator.id)"
                                                class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition"
                                                title="Eliminar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path :d="mdiDelete"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="selectedEvaluatorsObjects.length === 0">
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 bg-gray-50">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg viewBox="0 0 24 24" class="w-8 h-8 text-gray-300" fill="currentColor"><path :d="mdiAccountSchool"/></svg>
                                                <span class="italic">No hay evaluadores seleccionados. Añade uno desde el buscador.</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                             <Link :href="route('admin.applications.index')" class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-center transition font-semibold text-sm uppercase tracking-wider shadow-sm cursor-pointer">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="px-6 py-2.5 bg-[#1B396A] text-white rounded-lg font-semibold uppercase text-sm tracking-wider hover:bg-[#152d47] transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 cursor-pointer"
                                :disabled="form.processing || selectedEvaluatorIds.length === 0"
                            >
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="mdiAccountPlus"/></svg>
                                Asignar Evaluador(es)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.vue-select-custom :deep(.vs__dropdown-toggle) {
    background: #F3F4F6;
    border: none;
    border-bottom: 2px solid #D1D5DB;
    border-radius: 0.5rem;
    padding: 6px;
    min-height: 45px;
    transition: all 0.2s;
}

.vue-select-custom :deep(.vs__dropdown-toggle):hover {
    border-bottom-color: rgba(27, 57, 106, 0.5);
}

.vue-select-custom :deep(.vs--open .vs__dropdown-toggle) {
    background: #EFF6FF;
    border-bottom-color: #1B396A;
}

.vue-select-custom :deep(.vs__search) {
    margin: 0;
    padding: 0;
    border: none;
    font-size: 0.875rem;
    color: #111827;
}

.vue-select-custom :deep(.vs__search::placeholder) {
    color: #9CA3AF;
}

.vue-select-custom :deep(.vs__selected) {
    background-color: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    border-radius: 4px;
    padding: 2px 8px;
    margin: 2px;
    font-size: 0.85rem;
    font-weight: 500;
}

.vue-select-custom :deep(.vs__actions) {
    padding-right: 8px;
}

.vue-select-custom :deep(.vs__clear),
.vue-select-custom :deep(.vs__open-indicator) {
    fill: #1B396A;
}

.vue-select-custom :deep(.vs__dropdown-menu) {
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    margin-top: 4px;
}

.vue-select-custom :deep(.vs__dropdown-option) {
    padding: 0.625rem 0.75rem;
    color: #374151;
    font-size: 0.875rem;
}

.vue-select-custom :deep(.vs__dropdown-option--highlight) {
    background: #1B396A;
    color: white;
}

.vue-select-custom :deep(.vs__dropdown-option--highlight) * {
    color: white !important;
}
</style>
