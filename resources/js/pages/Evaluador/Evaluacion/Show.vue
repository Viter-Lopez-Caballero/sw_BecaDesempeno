<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import EvaluadorLayout from '@/layouts/EvaluadorLayout.vue';
import Swal from 'sweetalert2';
import { ref, computed } from 'vue';
import { 
    mdiHome, 
    mdiFileDocumentOutline, 
    mdiCheckCircle, 
    mdiCloseCircle,
    mdiArrowLeft,
    mdiEye
} from '@mdi/js';

const props = defineProps({
    evaluacion: Object,
    solicitud: Object,
    rubric: Object,
    docente: Object,
});

const form = useForm({
    status: '',
    score: 0,
    respuestas: {}, // { question_id: { option_id, score } }
    comentario: '',
});

const selectedDocument = ref(null);

const documentUrl = computed(() => {
    if (!selectedDocument.value) return null;
    return route('evaluador.documentos.stream', selectedDocument.value.id);
});

// Normalize documents list to handle API Resource wrapping (data property) or direct array
const documentsList = computed(() => {
    const docs = props.solicitud.documentos;
    if (!docs) return [];
    return Array.isArray(docs) ? docs : (docs.data || []);
});

const selectDocument = (doc) => {
    selectedDocument.value = doc;
};

// Calculate total score based on selected options
const currentScore = computed(() => {
    let total = 0;
    for (const key in form.respuestas) {
        if (form.respuestas[key]) {
            total += form.respuestas[key].score;
        }
    }
    return total;
});

// Update score in form when computed changes
const updateFormScore = () => {
    form.score = currentScore.value;
};

const handleOptionSelect = (questionId, option) => {
    form.respuestas[questionId] = {
        option_id: option.id,
        score: option.score
    };
    updateFormScore();
};

const submitEvaluation = (status) => {
    // Validation: Ensure all rubric questions are answered
    if (props.rubric && props.rubric.questions) {
        const totalQuestions = props.rubric.questions.length;
        const answeredCount = Object.keys(form.respuestas).length;
        
        if (answeredCount < totalQuestions) {
            Swal.fire({
                icon: 'warning',
                title: 'Rúbrica incompleta',
                text: 'Debes evaluar todos los criterios de la rúbrica antes de finalizar.',
                confirmButtonColor: '#1B396A',
                confirmButtonText: 'Entendido'
            });
            return;
        }
    }

    form.status = status;
    form.score = currentScore.value; // Ensure score is up to date

    if (status === 'approved') {
        Swal.fire({
            title: '¿Aceptar Solicitud?',
            text: `La solicitud será aprobada con una puntuación de ${currentScore.value} puntos.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B396A',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.put(route('evaluador.evaluacion.update', props.evaluacion.id), {
                    onSuccess: () => {
                        Swal.fire('¡Aceptada!', 'La evaluación ha sido registrada.', 'success');
                    }
                });
            }
        });
    } else if (status === 'rejected') {
        Swal.fire({
            title: 'Rechazar Solicitud',
            input: 'textarea',
            inputLabel: 'Motivo del rechazo',
            inputPlaceholder: 'Escribe aquí por qué rechazas la solicitud...',
            inputAttributes: {
                'aria-label': 'Motivo del rechazo'
            },
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Rechazar',
            cancelButtonText: 'Cancelar',
            preConfirm: (comment) => {
                if (!comment) {
                    Swal.showValidationMessage('Debes ingresar un motivo');
                }
                return comment;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.comentario = result.value;
                form.put(route('evaluador.evaluacion.update', props.evaluacion.id), {
                    onSuccess: () => {
                        Swal.fire('Rechazada', 'La solicitud ha sido rechazada correctamente.', 'success');
                    }
                });
            }
        });
    }
};
</script>

<template>
    <EvaluadorLayout>
        <Head title="Evaluar Solicitud" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">Evaluación de Solicitud</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                        <Link :href="route('evaluador.inicio')" class="hover:text-blue-600 flex items-center gap-1">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" style="fill: currentColor"><path :d="mdiHome"/></svg>
                            Inicio
                        </Link>
                        <span>/</span>
                        <span class="font-semibold text-gray-800">Evaluar</span>
                    </div>
                </div>
                <Link :href="route('evaluador.inicio')" class="w-full md:w-auto justify-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" style="fill: currentColor"><path :d="mdiArrowLeft"/></svg>
                    Volver
                </Link>
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
                            <p class="font-medium text-gray-900 text-base leading-tight">{{ docente.name }}</p>
                        </div>
                        
                        <!-- Convocatoria -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Convocatoria</p>
                            <p class="font-medium text-gray-900">{{ solicitud.convocatoria?.nombre }}</p>
                        </div>

                        <!-- Campus -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Campus</p>
                            <p class="font-medium text-gray-900">{{ docente.institucion?.name || 'No registrada' }}</p>
                        </div>

                        <!-- Área -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Área</p>
                            <p class="font-medium text-gray-900">{{ docente.priority_area?.name || 'N/A' }}</p>
                        </div>

                        <!-- Sub Área -->
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider">Sub Área</p>
                            <p class="font-medium text-gray-900">{{ docente.sub_area?.name || 'N/A' }}</p>
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

                <!-- Rubric Evaluation (Full Width) -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
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
                                        'border-blue-500 bg-blue-50 ring-1 ring-blue-500': form.respuestas[question.id]?.option_id === option.id,
                                        'border-gray-200': form.respuestas[question.id]?.option_id !== option.id
                                    }"
                                    @click="handleOptionSelect(question.id, option)"
                                >
                                    <div class="flex items-center h-5">
                                        <input 
                                            type="radio" 
                                            :name="'question_' + question.id" 
                                            :value="option.id"
                                            :checked="form.respuestas[question.id]?.option_id === option.id"
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div class="ml-3 flex-1 flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900" 
                                            :class="{'text-blue-900': form.respuestas[question.id]?.option_id === option.id}"
                                        >
                                            {{ option.text }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                            :class="{'bg-blue-100 text-blue-800': form.respuestas[question.id]?.option_id === option.id}"
                                        >
                                            {{ option.score }} pts
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row justify-end items-center gap-3">
                        <p v-if="!rubric" class="text-red-500 text-sm mr-auto">No se puede evaluar sin rúbrica.</p>
                        
                        <button 
                            type="button" 
                            @click="submitEvaluation('rejected')"
                            :disabled="form.processing"
                            class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 transition-colors flex items-center justify-center gap-2"
                        > 
                            <svg viewBox="0 0 24 24" class="w-5 h-5" style="fill: currentColor"><path :d="mdiCloseCircle"/></svg>
                            Rechazar Solicitud
                        </button>

                        <button 
                            type="button" 
                            @click="submitEvaluation('approved')"
                            :disabled="form.processing || !rubric"
                            class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#1B396A] hover:bg-[#2c4c85] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg viewBox="0 0 24 24" class="w-5 h-5" style="fill: currentColor"><path :d="mdiCheckCircle"/></svg>
                            Aceptar Solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </EvaluadorLayout>
</template>
