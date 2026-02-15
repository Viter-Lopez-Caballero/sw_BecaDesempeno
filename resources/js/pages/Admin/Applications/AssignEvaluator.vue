<template>
    <Head title="Asignar Evaluador" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                     <nav class="flex text-sm text-gray-500 mb-1">
                        <Link :href="route('admin.applications.index')" class="hover:text-[#1B396A] font-medium">Solicitudes</Link>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-900 font-semibold">Asignar Evaluador</span>
                    </nav>
                </div>
            </div>

            <!-- Solicitud Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Información de la solicitud</h2>
                    <span 
                        class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                        :class="{
                            'bg-green-100 text-green-700 border border-green-200': application.status === 'approved',
                            'bg-red-100 text-red-700 border border-red-200': application.status === 'rejected',
                            'bg-yellow-100 text-yellow-800 border border-yellow-200': application.status === 'pending'
                        }"
                    >
                        {{ getStatusLabel(application.status) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div>
                        <label class="text-sm text-gray-500 font-medium block mb-1">Profesor</label>
                        <p class="text-base text-gray-900 font-semibold">{{ application.user?.name || application.user?.data?.name || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 font-medium block mb-1">Campus</label>
                        <p class="text-base text-gray-900 font-medium">{{ application.user?.institution?.name || application.user?.data?.institution?.name || 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="text-sm text-gray-500 font-medium block mb-1">Convocatoria</label>
                    <p class="text-base text-gray-900 font-medium">{{ application.announcement?.name || application.announcement?.data?.name || 'No especificada' }}</p>
                </div>
            </div>

            <!-- Assignment Card -->
             <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Seleccionar Evaluador</h2>
                <p class="text-sm text-gray-500 mb-6">Asigne un evaluador para revisar esta solicitud</p>
                
                <form @submit.prevent="submit">
                    <div class="space-y-6">
                        <!-- Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Evaluador *</label>
                            <VueSelect
                                v-model="selectedEvaluatorIds"
                                :options="availableEvaluators"
                                :reduce="evaluator => evaluator.id"
                                label="name"
                                multiple
                                placeholder="Seleccione un evaluador"
                                class="vue-select-custom"
                            />
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
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(evaluator, index) in selectedEvaluatorsObjects" :key="evaluator.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ evaluator.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                type="button" 
                                                @click="removeEvaluator(evaluator.id)"
                                                class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition"
                                                title="Eliminar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="selectedEvaluatorsObjects.length === 0">
                                        <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                            No hay evaluadores seleccionados.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between pt-4 gap-4">
                             <Link :href="route('admin.applications.index')" class="w-full sm:w-auto px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-center transition font-bold uppercase text-xs tracking-wider">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="w-full sm:w-auto px-10 py-2.5 bg-[#1B396A] text-white rounded-lg font-bold uppercase text-xs tracking-wider hover:bg-[#152d47] transition shadow-md disabled:opacity-50"
                                :disabled="form.processing || selectedEvaluatorIds.length === 0"
                            >
                                Asignar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { ref, computed, watch } from 'vue';

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
    return props.evaluators; // Or filter out selected if we want to hide them from dropdown
});

const form = useForm({
    application_id: props.application.id,
    evaluator_ids: [],
});

// Update form when selection changes
watch(selectedEvaluatorIds, (newVal) => {
    form.evaluator_ids = newVal;
});

const removeEvaluator = (id) => {
    selectedEvaluatorIds.value = selectedEvaluatorIds.value.filter(val => val !== id);
};

const submit = () => {
    form.post(route('admin.applications.assign'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success logic usually redirects or shows message
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
    return new Date(dateString).toLocaleDateString();
};
</script>

<style scoped>
:deep(.vue-select-custom .vs__dropdown-toggle) {
    padding: 6px;
    border-color: #d1d5db;
    border-radius: 0.5rem;
    min-height: 42px;
}
:deep(.vue-select-custom .vs__selected) {
    background-color: transparent; /* Since we show them in table, maybe hide in input? Or keep. */
    color: #374151;
}
</style>
