<script setup>
import { Head, Link } from '@inertiajs/vue3';
import EvaluatorLayout from '@/layouts/EvaluatorLayout.vue';
import { ref, computed } from 'vue';
import { 
    mdiHome, 
    mdiFileDocumentMultiple, 
    mdiCheckCircle, 
    mdiCloseCircle,
    mdiMessageText,
    mdiChevronRight,
    mdiEye,
    mdiEyeOff,
    mdiDownload
} from '@mdi/js';

const props = defineProps({
    evaluation: Object,
    application: Object,
    rubric: Object,
    teacher: Object,
});

// Logic for Document Preview (Standardized)
const documentsState = ref({});

const togglePreview = (docId) => {
    if (!documentsState.value[docId]) {
        documentsState.value[docId] = { showPreview: false };
    }
    documentsState.value[docId].showPreview = !documentsState.value[docId].showPreview;
};

const getPreviewUrl = (doc) => {
    return route('evaluator.documents.stream', doc.id);
};

const getDownloadUrl = (doc) => {
    return route('evaluator.documents.stream', doc.id); 
};

const getFileIcon = (type) => {
    if (type === 'pdf') return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'; 
    return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z';
};

// Normalize documents list
const documentsList = computed(() => {
    const docs = props.application.documents;
    if (!docs) return [];
    return Array.isArray(docs) ? docs : (docs.data || []);
});

// Respuestas guardadas en la evaluación
const respuestas = computed(() => props.evaluation.answers || {});

const getSelectedOption = (question) => {
    const respuesta = respuestas.value[question.id];
    if (!respuesta) return null;
    return question.options.find(opt => opt.id === respuesta.option_id);
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
</script>

<template>
    <EvaluatorLayout>
        <Head title="Detalles de Evaluación" />

        <div class="space-y-6">
            <!-- Header with Breadcrumbs -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">Detalles de Evaluación</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <Link :href="route('evaluator.evaluations.index')" class="text-gray-700 font-medium hover:text-[#1B396A]">Evaluaciones</Link>
                         <svg viewBox="0 0 24 24" class="w-4 h-4 text-gray-400" style="fill: currentColor">
                            <path :d="mdiChevronRight"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Detalles</span>
                    </div>
                </div>
                 <Link :href="route('evaluator.evaluations.index')" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
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
                        Evaluada el {{ evaluation.updated_at ? formatDate(evaluation.updated_at) : 'Fecha no disponible' }}
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
                
                <!-- Información General (Applicant) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
                     <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-100 pb-4 mb-6 gap-2 md:gap-4">
                        <h2 class="text-lg font-bold text-gray-900">Información General</h2>
                     </div>
                     
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Docente</h3>
                            <p class="text-md font-medium text-gray-900">{{ teacher.name || 'Completar datos' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Institución</h3>
                            <p class="text-md font-medium text-gray-900">{{ teacher.institution?.name || 'No registrada' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Convocatoria</h3>
                            <p class="text-md font-medium text-gray-900">{{ application.announcement?.name || 'General' }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Área de Procedencia</h3>
                            <p class="text-md font-medium text-gray-900">{{ teacher.priority_area?.name || 'No registrada' }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Subárea</h3>
                            <p class="text-md font-medium text-gray-900">{{ teacher.sub_area?.name || 'No registrada' }}</p>
                        </div>

                         <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Fecha de Solicitud</h3>
                            <p class="text-md font-medium text-gray-900">{{ formatDate(application.created_at) }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Tipo de Plaza</h3>
                            <p class="text-md font-medium text-gray-900">{{ application.position_type || 'No especificado' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Documentación -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Documentación</h2>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div v-if="documentsList.length === 0" class="text-gray-500 italic text-center py-4 bg-gray-50 rounded-lg">
                            No hay documentos adjuntos.
                        </div>

                        <div v-for="doc in documentsList" :key="doc.id" 
                            class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition"
                            :class="{ 'bg-blue-50/30': documentsState[doc.id]?.showPreview }"
                        >
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4 w-full sm:w-auto min-w-0">
                                    <div class="text-gray-700 flex-shrink-0 bg-gray-100 p-2 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="getFileIcon(doc.file_type)" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-medium text-gray-900 truncate block" :title="doc.name">{{ doc.name }}</span>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center gap-3 w-full sm:w-auto justify-end flex-shrink-0">
                                    <button 
                                        v-if="doc.file_type === 'pdf'" 
                                        @click="togglePreview(doc.id)" 
                                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500"
                                        :class="documentsState[doc.id]?.showPreview 
                                            ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' 
                                            : 'bg-white text-blue-600 border border-blue-200 hover:bg-blue-50'"
                                    >
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="documentsState[doc.id]?.showPreview ? mdiEyeOff : mdiEye"/></svg>
                                        {{ documentsState[doc.id]?.showPreview ? 'Ocultar' : 'Ver' }}
                                    </button>
                                    
                                    <a :href="getDownloadUrl(doc)" 
                                        target="_blank"
                                        class="flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-1.5 rounded-md transition text-sm font-medium border border-gray-200 bg-white"
                                        title="Descargar archivo"
                                    >
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="mdiDownload"/></svg>
                                        Descargar
                                    </a>
                                </div>
                            </div>
                             <!-- Inline Preview -->
                            <div v-if="documentsState[doc.id]?.showPreview" class="mt-4 pt-4 border-t border-gray-200 w-full animate-fadeIn">
                                <div class="w-full h-[600px] bg-gray-100 rounded-lg overflow-hidden border border-gray-300 relative">
                                    <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                                        Cargando vista previa...
                                    </div>
                                    <iframe :src="getPreviewUrl(doc)" class="w-full h-full relative z-10" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rubric Evaluation (READ ONLY) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-[#1B396A] px-6 py-4 border-b border-gray-200 flex justify-between items-center text-white">
                        <div>
                            <h2 class="font-bold text-lg">Resultados de Evaluación</h2>
                            <p class="text-blue-100 text-sm">Respuestas registradas</p>
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
                                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1 block">Criterio {{ qIndex + 1 }}</span>
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

<style scoped>
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
