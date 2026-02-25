<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import EvaluatorLayout from '@/layouts/EvaluatorLayout.vue';
import { alertaPregunta, alertaAdvertencia } from '@/utils/alerts.js';
import { ref, computed } from 'vue';
import RejectModal from './RejectModal.vue';
import { 
    mdiHome, 
    mdiFileDocumentMultiple, 
    mdiCheckCircle, 
    mdiCloseCircle,
    mdiArrowLeft,
    mdiEye,
    mdiEyeOff,
    mdiDownload,
    mdiFileDocumentOutline,
    mdiChevronRight
} from '@mdi/js';

const props = defineProps({
    evaluation: Object,
    application: Object,
    rubric: Object,
    teacher: Object,
});

const form = useForm({
    status: '',
    score: 0,
    answers: {}, // { question_id: { option_id, score } }
    comment: '',
});

const rejectModalOpen = ref(false);

// Logic for Document Preview (copied/adapted from Admin)
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
    // Assuming a download route exists or using the stream with download header?
    // If no specific download route, we might use stream. 
    // Checking previous code: Admin had 'catalog.admin.documents.download'.
    // Evaluator might not have a dedicated download route in the previous code snippet, 
    // but usually stream can be used or we can fallback to stream.
    // Let's use stream for now or check if a download route exists.
    // Given the previous code didn't have a download button, we'll assume stream is fine or add a download route later.
    // Actually, let's use the stream URL for download as a fallback.
    return route('evaluator.documents.stream', doc.id); 
};

const getFileIcon = (type) => {
    // Simple mapping or return standard icon
    if (type === 'pdf') return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'; 
    return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z';
};

// Normalizar la lista de documentos
const documentsList = computed(() => {
    const docs = props.application.documents;
    if (!docs) return [];
    return Array.isArray(docs) ? docs : (docs.data || []);
});

// Computed Properties for Progress tracking
const totalQuestions = computed(() => {
    return props.rubric && props.rubric.questions ? props.rubric.questions.length : 0;
});

const answeredCount = computed(() => {
    return Object.keys(form.answers).filter(key => form.answers[key] !== null && form.answers[key].option_id).length;
});

const progressPercentage = computed(() => {
    if (totalQuestions.value === 0) return 0;
    return (answeredCount.value / totalQuestions.value) * 100;
});

// Calculate total score based on selected options
const currentScore = computed(() => {
    let total = 0;
    for (const key in form.answers) {
        if (form.answers[key]) {
            total += form.answers[key].score;
        }
    }
    return total;
});

// Update score in form when computed changes
const updateFormScore = () => {
    form.score = currentScore.value;
};

const handleOptionSelect = (questionId, option) => {
    form.answers[questionId] = {
        option_id: option.id,
        score: option.score
    };
    updateFormScore();
};

const isReadOnly = computed(() => props.evaluation.status !== 'pending');

// LocalStorage Draft Management
import { watch, onMounted } from 'vue';

const draftKey = computed(() => `evaluator_draft_${props.evaluation.id}`);

const saveDraft = (showMessage = false) => {
    if (isReadOnly.value) return;
    localStorage.setItem(draftKey.value, JSON.stringify(form.answers));
    if (showMessage) {
        // Usa una alerta pequeña de éxito si es manual
        alertaExito('Borrador guardado', 'Tus respuestas han sido guardadas temporalmente de forma exitosa.');
    }
};

onMounted(() => {
    if (isReadOnly.value) return;
    
    // Attempt hydration
    const savedDraft = localStorage.getItem(draftKey.value);
    if (savedDraft) {
        try {
            const parsedAnswers = JSON.parse(savedDraft);
            if (Object.keys(parsedAnswers).length > 0) {
                form.answers = parsedAnswers;
                updateFormScore();
                // User requested NO text/alert for auto-recovery: Just do it silently.
            }
        } catch (e) {
            console.error('Error parsing evaluation draft from local storage:', e);
        }
    }
});

// Autosave silently whenever an answer is selected
watch(() => form.answers, () => {
    if (!isReadOnly.value) {
        localStorage.setItem(draftKey.value, JSON.stringify(form.answers));
    }
}, { deep: true });

const submitEvaluation = async (status) => {
    // Validation: Ensure all rubric questions are answered
    if (props.rubric && props.rubric.questions) {
        const total = props.rubric.questions.length;
        const answered = answeredCount.value;
        
        if (answered < total) {
            alertaAdvertencia('Rúbrica incompleta', 'Debes evaluar todos los criterios de la rúbrica antes de finalizar.');
            return;
        }
    }

    form.status = status;
    form.score = currentScore.value; // Ensure score is up to date

    if (status === 'approved') {
        const confirmed = await alertaPregunta(
            '¿Aceptar Solicitud?',
            `La solicitud será aprobada con una puntuación de ${currentScore.value} puntos.`
        );
        if (confirmed) {
            form.put(route('evaluator.evaluation.update', props.evaluation.id), {
                onSuccess: () => {
                    localStorage.removeItem(draftKey.value);
                }
            });
        }
    } else if (status === 'rejected') {
        rejectModalOpen.value = true;
    }
};

const onRejectionSuccess = () => {
    localStorage.removeItem(draftKey.value);
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
        <Head title="Evaluar Solicitud" />

        <div class="space-y-6">
            <!-- Header with Breadcrumbs -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">Evaluación de Solicitud</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <Link :href="route('evaluator.dashboard')" class="text-gray-700 font-medium hover:text-[#1B396A]">Inicio</Link>
                         <svg viewBox="0 0 24 24" class="w-4 h-4 text-gray-400" style="fill: currentColor">
                            <path :d="mdiChevronRight"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Evaluación</span>
                    </div>
                </div>
                 <Link :href="route('evaluator.dashboard')" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium bg-white cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <!-- Read-Only Banner -->
            <div v-if="isReadOnly" class="p-4 rounded-lg flex items-center gap-3 mb-6" 
                :class="{
                    'bg-green-50 border border-green-200 text-green-800': evaluation.status === 'approved',
                    'bg-red-50 border border-red-200 text-red-800': evaluation.status === 'rejected',
                    'bg-gray-100 border border-gray-300 text-gray-800': evaluation.status === 'expired'
                }"
            >
                <div class="p-2 rounded-full bg-white bg-opacity-50">
                    <svg v-if="evaluation.status === 'approved'" viewBox="0 0 24 24" class="w-6 h-6 fill-current"><path :d="mdiCheckCircle"/></svg>
                    <svg v-else-if="evaluation.status === 'rejected'" viewBox="0 0 24 24" class="w-6 h-6 fill-current"><path :d="mdiCloseCircle"/></svg>
                    <svg v-else viewBox="0 0 24 24" class="w-6 h-6 fill-current"><path :d="mdiClockOutline"/></svg>
                </div>
                <div class="font-medium">
                    <span v-if="evaluation.status === 'approved'">Esta solicitud ya fue aprobada.</span>
                    <span v-else-if="evaluation.status === 'rejected'">Esta solicitud ya fue rechazada.</span>
                    <span v-else>El tiempo de evaluación ha expirado.</span>
                </div>
            </div>

            <!-- Content Container -->
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
                            <p class="text-md font-medium text-gray-900">{{ application.position_full_type || 'No especificado' }}</p>
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

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Column Left: Rubric -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-[#1B396A] px-6 py-4 border-b border-gray-200 flex justify-between items-center text-white">
                                <div>
                                    <h2 class="font-bold text-lg">Rúbrica de Evaluación</h2>
                                    <p class="text-blue-100 text-sm">Selecciona una opción para cada criterio</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs opacity-80 uppercase tracking-widest">Puntuación Total</p>
                                    <p class="text-3xl font-bold">{{ currentScore }} <span class="text-lg font-normal">pts</span></p>
                                </div>
                            </div>
                            
                            <div class="p-6 space-y-8">
                                <div v-if="!rubric" class="text-center py-8 text-gray-500">
                                    No hay una rúbrica activa asignada. Contacte al administrador.
                                </div>

                                <div v-else v-for="(question, qIndex) in rubric.questions" :key="question.id" class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                                    <div class="mb-3">
                                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1 block">Criterio {{ qIndex + 1 }}</span>
                                        <h3 class="text-lg font-medium text-gray-900">{{ question.text }}</h3>
                                    </div>
                                    
                                    <div class="mt-3 space-y-3">
                                        <div v-for="option in question.options" :key="option.id" 
                                            class="relative flex items-center p-4 cursor-pointer rounded-lg border transition-all duration-200 hover:bg-gray-50"
                                            :class="{ 
                                                'border-blue-500 bg-blue-50 ring-1 ring-blue-500': form.answers[question.id]?.option_id === option.id,
                                                'border-gray-200': form.answers[question.id]?.option_id !== option.id
                                            }"
                                            @click="!isReadOnly && handleOptionSelect(question.id, option)"
                                        >
                                            <div class="flex items-center h-5">
                                                <input 
                                                    type="radio" 
                                                    :name="'question_' + question.id" 
                                                    :value="option.id"
                                                    :checked="form.answers[question.id]?.option_id === option.id"
                                                    :disabled="isReadOnly"
                                                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 disabled:opacity-50"
                                                />
                                            </div>
                                            <div class="ml-3 flex-1 flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-900" 
                                                    :class="{'text-blue-900': form.answers[question.id]?.option_id === option.id}"
                                                >
                                                    {{ option.text }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                    :class="{'bg-blue-100 text-blue-800': form.answers[question.id]?.option_id === option.id}"
                                                >
                                                    {{ option.score }} pts
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column Right: Progress Summary & Actions -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 sticky top-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Progreso de Evaluación</h3>
                            
                            <!-- Progress Bar -->
                            <div class="space-y-3 mb-6" v-if="rubric">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Criterios evaluados:</span>
                                    <span class="font-bold text-gray-900">
                                        {{ answeredCount }} / {{ totalQuestions }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-[#1B396A] h-2.5 rounded-full transition-all duration-500" :style="{ width: progressPercentage + '%' }"></div>
                                </div>
                            </div>
                            
                            <!-- Guardado Manual (Solo si está pending) -->
                            <div v-if="!isReadOnly" class="mb-6">
                                <button 
                                    type="button" 
                                    @click="saveDraft(true)"
                                    class="w-full flex justify-center py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition gap-2 items-center"
                                >
                                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    Guardar Borrador
                                </button>
                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    Tus selecciones se guardan automáticamente, pero puedes asegurar el guardado manualmente.
                                </p>
                            </div>

                            <hr class="my-6 border-gray-200" v-if="!isReadOnly">

                            <!-- Submit Actions -->
                            <div v-if="!isReadOnly" class="flex flex-col gap-3">
                                <p v-if="!rubric" class="text-red-500 text-sm mb-2 text-center">No se puede evaluar sin rúbrica.</p>
                                
                                <button 
                                    type="button" 
                                    @click="submitEvaluation('rejected')"
                                    :disabled="form.processing"
                                    class="w-full px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 transition-colors flex items-center justify-center gap-2 uppercase tracking-wide cursor-pointer"
                                > 
                                    <svg viewBox="0 0 24 24" class="w-5 h-5" style="fill: currentColor"><path :d="mdiCloseCircle"/></svg>
                                    Rechazar Solicitud
                                </button>

                                <button 
                                    type="button" 
                                    @click="submitEvaluation('approved')"
                                    :disabled="form.processing || !rubric"
                                    class="w-full px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#1B396A] hover:bg-[#152d47] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1B396A] disabled:opacity-50 transition-colors flex items-center justify-center gap-2 uppercase tracking-wide cursor-pointer"
                                >
                                    <svg viewBox="0 0 24 24" class="w-5 h-5" style="fill: currentColor"><path :d="mdiCheckCircle"/></svg>
                                    Aceptar Solicitud
                                </button>
                            </div>
                            <!-- Si ya terminó -->
                            <div v-else class="text-center py-4 text-green-700 bg-green-50 rounded-lg text-sm font-medium border border-green-200">
                                Evaluación Finalizada.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <RejectModal 
                :show="rejectModalOpen" 
                :evaluationId="evaluation.id"
                :score="currentScore"
                :answers="form.answers"
                @close="rejectModalOpen = false"
                @success="onRejectionSuccess"
            />
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
