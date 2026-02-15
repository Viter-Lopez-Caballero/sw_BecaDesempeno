<script setup>
import { Head, Link } from '@inertiajs/vue3';
import EvaluatorLayout from '@/layouts/EvaluatorLayout.vue';
import { ref, computed } from 'vue';
import { 
    mdiHome, 
    mdiFileDocumentOutline, 
    mdiArrowLeft,
    mdiEye,
    mdiCheckCircle,
    mdiCloseCircle,
    mdiMessageText
} from '@mdi/js';

const props = defineProps({
    evaluation: Object,
    application: Object,
    rubric: Object,
    teacher: Object,
});

const selectedDocument = ref(null);

const documentUrl = computed(() => {
    if (!selectedDocument.value) return null;
    return route('evaluator.documents.stream', selectedDocument.value.id);
});

// Normalize documents list
const documentsList = computed(() => {
    const docs = props.application.documents;
    if (!docs) return [];
    return Array.isArray(docs) ? docs : (docs.data || []);
});

const selectDocument = (doc) => {
    selectedDocument.value = doc;
};

// Respuestas guardadas en la evaluación
const respuestas = computed(() => props.evaluation.answers || {});

// Verificar si una opción fue la seleccionada
// Obtener la opción seleccionada para una pregunta
const getSelectedOption = (question) => {
    const respuesta = respuestas.value[question.id];
    if (!respuesta) return null;
    // La respuesta en BD guarda { option_id: ..., score: ... }
    return question.options.find(opt => opt.id === respuesta.option_id);
};

</script>

<template>
    <EvaluatorLayout>
        <Head title="Detalles de Evaluación" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">Detalles de Evaluación</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                        <Link :href="route('evaluator.dashboard')" class="hover:text-blue-600 flex items-center gap-1">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" style="fill: currentColor"><path :d="mdiHome"/></svg>
                            Inicio
                        </Link>
                        <span>/</span>
                        <Link :href="route('evaluator.evaluations.index')" class="hover:text-blue-600">
                            Historial
                        </Link>
                        <span>/</span>
                        <span class="font-semibold text-gray-800">Detalles</span>
                    </div>
                </div>
                <Link :href="route('evaluator.evaluations.index')" class="w-full md:w-auto justify-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" style="fill: currentColor"><path :d="mdiArrowLeft"/></svg>
                    Volver
                </Link>
            </div>

            <!-- Status Banner -->
            <div 
                class="rounded-lg p-4 flex items-center gap-4 shadow-sm border"
                :class="{
                    'bg-green-50 border-green-200': evaluation.status === 'approved',
                    'bg-red-50 border-red-200': evaluation.status === 'rejected'
                }"
            >
                <div 
                    class="p-2 rounded-full"
                    :class="{
                        'bg-green-100 text-green-600': evaluation.status === 'approved',
                        'bg-red-100 text-red-600': evaluation.status === 'rejected'
                    }"
                >
                    <svg v-if="evaluation.status === 'approved'" viewBox="0 0 24 24" class="w-8 h-8" style="fill: currentColor"><path :d="mdiCheckCircle"/></svg>
                    <svg v-else viewBox="0 0 24 24" class="w-8 h-8" style="fill: currentColor"><path :d="mdiCloseCircle"/></svg>
                </div>
                <div>
                    <h3 
                        class="text-lg font-bold"
                        :class="{
                            'text-green-800': evaluation.status === 'approved',
                            'text-red-800': evaluation.status === 'rejected'
                        }"
                    >
                        Solicitud {{ evaluation.status === 'approved' ? 'Aprobada' : 'Rechazada' }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        Evaluada el {{ evaluation.updated_at ? new Date(evaluation.updated_at).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Fecha no disponible' }}
                    </p>
                </div>
            </div>

            <!-- Comentario de Rechazo (si existe) -->
            <div v-if="evaluation.status === 'rejected' && evaluation.comment" class="bg-red-50 rounded-lg shadow-md border border-red-200 p-6">
                <div class="flex items-start gap-3">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 text-red-600 mt-1" style="fill: currentColor"><path :d="mdiMessageText"/></svg>
                    <div>
                        <h3 class="font-bold text-red-800 mb-2">Motivo del Rechazo</h3>
                        <p class="text-red-700 italic">"{{ evaluation.comment }}"</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Applicant Details (Full Width) -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h2 class="font-semibold text-gray-800">Detalles del Solicitante</h2>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 text-sm">
                        <!-- Docente -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Docente</p>
                            <p class="font-medium text-gray-900 text-base leading-tight">{{ teacher.name }}</p>
                        </div>
                        
                        <!-- Convocatoria -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Convocatoria</p>
                            <p class="font-medium text-gray-900">{{ application.announcement?.name }}</p>
                        </div>

                        <!-- Campus -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Campus</p>
                            <p class="font-medium text-gray-900">{{ teacher.institution?.name || 'No registrada' }}</p>
                        </div>

                        <!-- Área -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Área</p>
                            <p class="font-medium text-gray-900">{{ teacher.priority_area?.name || 'N/A' }}</p>
                        </div>

                        <!-- Sub Área -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Sub Área</p>
                            <p class="font-medium text-gray-900">{{ teacher.sub_area?.name || 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Documents List -->
                    <div class="lg:col-span-1 bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden flex flex-col h-[600px]">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-800">Documentación</h2>
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ documentsList.length }} Archivos</span>
                        </div>
                        <div class="overflow-y-auto flex-1">
                            <div v-if="documentsList.length === 0" class="p-8 text-center text-gray-500 text-sm">
                                No hay documentos adjuntos.
                            </div>
                            <div v-else class="divide-y divide-gray-100">
                                <div 
                                    v-for="doc in documentsList" 
                                    :key="doc.id" 
                                    class="p-4 hover:bg-gray-50 transition cursor-pointer group border-l-4"
                                    :class="selectedDocument?.id === doc.id ? 'bg-blue-50 border-blue-600' : 'border-transparent'"
                                    @click="selectDocument(doc)"
                                >
                                    <div class="flex items-start gap-3">
                                        <div class="bg-blue-100 p-2 rounded text-blue-600 mt-1">
                                            <svg viewBox="0 0 24 24" class="w-5 h-5" style="fill: currentColor"><path :d="mdiFileDocumentOutline"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" :title="doc.name">{{ doc.name }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ doc.file_type || 'Archivo' }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex justify-end">
                                         <button 
                                            class="text-xs font-medium px-2 py-1 rounded transition flex items-center gap-1"
                                            :class="selectedDocument?.id === doc.id ? 'bg-blue-600 text-white' : 'text-blue-600 bg-blue-50 hover:bg-blue-100'"
                                        >
                                            <svg viewBox="0 0 24 24" class="w-3 h-3" style="fill: currentColor"><path :d="mdiEye"/></svg>
                                            {{ selectedDocument?.id === doc.id ? 'Visualizando' : 'Visualizar' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Preview -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden h-[600px] flex flex-col">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-800">Vista Previa</h2>
                             <div v-if="selectedDocument" class="flex items-center gap-3">
                                <a :href="documentUrl" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                                    Abrir en nueva pestaña
                                    <svg xmlns="http://www.w3.org/2000/svg" height="14px" viewBox="0 -960 960 960" width="14px" fill="currentColor">
                                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-880h280v80H200v560h560v-280h80v280q0 33-23.5 56.5T760-120H200Zm188-212-56-56 372-372H560v-80h280v280h-80v-144L388-332Z"/>
                                    </svg>
                                </a>
                                <button 
                                    @click="selectedDocument = null" 
                                    class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-3 py-1 rounded transition"
                                >
                                    Cerrar Vista
                                </button>
                             </div>
                        </div>
                         <div class="flex-1 bg-gray-100 relative">
                            <div v-if="!selectedDocument" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-20 h-20 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">Selecciona un documento para visualizar</p>
                            </div>
                            <iframe 
                                v-else 
                                :src="documentUrl" 
                                class="w-full h-full border-0"
                                title="Visor de documento"
                            ></iframe>
                        </div>
                    </div>
                 </div>

                <!-- Rubric Evaluation (READ ONLY) -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-gray-700 px-6 py-4 border-b border-gray-200 flex justify-between items-center text-white">
                        <div>
                            <h2 class="font-bold text-lg">Resultados de Evaluación</h2>
                            <p class="text-gray-300 text-sm">Respuestas registradas</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs opacity-80 uppercase tracking-widest">Puntuación Final</p>
                            <p class="text-3xl font-bold">{{ Math.round(evaluation.score) }} <span class="text-lg font-normal">pts</span></p>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-8">
                        <div v-if="!rubric" class="text-center py-8 text-gray-500">
                            No se pudo cargar la rúbrica original.
                        </div>

                        <div v-else v-for="(question, qIndex) in rubric.questions" :key="question.id" class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                            <div class="mb-3">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Criterio {{ qIndex + 1 }}</span>
                                <h3 class="text-lg font-medium text-gray-900">{{ question.text }}</h3>
                            </div>
                            
                            <div class="mt-3">
                                <div v-if="getSelectedOption(question)" 
                                    class="relative flex items-center p-4 rounded-lg border border-blue-500 bg-blue-50"
                                >
                                    <div class="flex items-center h-5">
                                        <div class="h-4 w-4 rounded-full border border-blue-600 bg-blue-600 flex items-center justify-center">
                                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1 flex justify-between items-center">
                                        <span class="text-sm font-medium text-blue-900">
                                            {{ getSelectedOption(question).text }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ Math.round(getSelectedOption(question).score) }} pts
                                        </span>
                                    </div>
                                </div>
                                <div v-else class="p-4 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 text-sm italic">
                                    No se seleccionó ninguna opción.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EvaluatorLayout>
</template>
