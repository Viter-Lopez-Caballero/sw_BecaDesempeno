<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import TextInput from '@/components/TextInput.vue';
import InputLabel from '@/components/InputLabel.vue';
import InputError from '@/components/InputError.vue';
import { mdiArrowLeft, mdiFileDocumentOutline, mdiCloudUpload, mdiCheckBold, mdiEye, mdiEyeOff, mdiRefresh } from '@mdi/js';
import { ref, onMounted } from 'vue';

const props = defineProps({
    announcement: Object,
    catalog_documents: {
        type: Array,
        default: () => [],
    },
    previous_documents: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    announcement_id: props.announcement.id,
    position_type: '', 
    files: [], 
    file_types: [], 
    reused_documents: {}, 
});

// State for display
// Map docId -> { type: 'new'|'reused'|'empty', file: File|null, originalDoc: Object|null, showPreview: boolean }
const documentState = ref({}); 

const initializeState = () => {
    if (props.catalog_documents) {
        props.catalog_documents.forEach(doc => {
            if (!documentState.value[doc.id]) {
                 documentState.value[doc.id] = { type: 'empty', file: null, originalDoc: null, showPreview: false, errorMessage: '' };
            }
        });
    }

    if (props.previous_documents && props.previous_documents.length > 0) {
        props.previous_documents.forEach(prevDoc => {
            const catalogDoc = props.catalog_documents.find(d => d.name === prevDoc.name);
            if (catalogDoc) {
                documentState.value[catalogDoc.id] = {
                    type: 'reused',
                    file: null,
                    originalDoc: prevDoc,
                    showPreview: false,
                    errorMessage: ''
                };
            }
        });
    }
};

// Initialize immediately
initializeState();

// Also update on mount or when props change to be safe (though setup runs once)
onMounted(() => {
    initializeState();
});

const handleFileUpload = (event, docId) => {
    const file = event.target.files[0];
    if (file) {
        processFile(file, docId, event.target);
    }
};

const handleDrop = (event, docId) => {
    const file = event.dataTransfer.files[0];
    if (file) {
        // Validate type (must be PDF)
        if (file.type !== 'application/pdf') {
             if (!documentState.value[docId]) {
                 documentState.value[docId] = { type: 'empty', file: null, originalDoc: null, showPreview: false, errorMessage: '' };
             }
             documentState.value[docId].errorMessage = 'Solo se permiten archivos PDF.';
             return;
        }
        processFile(file, docId, null);
    }
};

const processFile = (file, docId, inputElement) => {
    // Validate size (10MB)
    if (file.size > 10 * 1024 * 1024) {
             // Ensure state exists to set error
             if (!documentState.value[docId]) {
                 documentState.value[docId] = { type: 'empty', file: null, originalDoc: null, showPreview: false, errorMessage: '' };
             }
             documentState.value[docId].errorMessage = 'El archivo pesa más de 10MB.';
             // Reset input if exists
             if (inputElement) inputElement.value = '';
             return;
    }

    documentState.value[docId] = {
        type: 'new',
        file: file,
        originalDoc: null,
        showPreview: false, // Reset preview on new upload
        errorMessage: ''
    };
};

const togglePreview = (docId) => {
    if (documentState.value[docId]) {
        documentState.value[docId].showPreview = !documentState.value[docId].showPreview;
    }
};

const getPreviewUrl = (state) => {
    if (state.type === 'new' && state.file) {
        return URL.createObjectURL(state.file);
    } else if (state.type === 'reused' && state.originalDoc) {
        return route('teacher.documents.stream', state.originalDoc.id);
    }
    return null;
};

const submit = () => {
    form.announcement_id = props.announcement.id; 
    form.files = [];
    form.file_types = [];
    form.reused_documents = {};

    let hasErrors = false;
    let fileIndexToDocId = {}; // Map form.files index -> docId

    // Reset document errors
    props.catalog_documents.forEach(doc => {
        if(documentState.value[doc.id]) {
            documentState.value[doc.id].errorMessage = '';
        }
    });
    
    // Clear form errors
    form.clearErrors();

    props.catalog_documents.forEach(doc => {
        const state = documentState.value[doc.id];
        
        // Safety check if state is still missing for some reason
        if (!state) return;

        if (doc.is_required && state.type === 'empty') {
            state.errorMessage = 'Este documento es obligatorio.';
            hasErrors = true;
        }

        if (state.type === 'new') {
            // Track index for error mapping
            const currentIndex = form.files.length;
            fileIndexToDocId[currentIndex] = doc.id;

            form.files.push(state.file);
            form.file_types.push(doc.name);
        } else if (state.type === 'reused') {
            form.reused_documents[doc.name] = state.originalDoc.id;
        }
    });

    if (!form.position_type) {
        form.setError('position_type', 'El tipo de plaza es obligatorio.');
        hasErrors = true;
    }

    if (hasErrors) {
        return;
    }

    form.post(route('teacher.applications.store'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Validation Errors:', errors);
            
            // Map backend errors to inline errors
            Object.keys(errors).forEach(key => {
                if (key.startsWith('files.')) {
                    const index = parseInt(key.split('.')[1]);
                    const docId = fileIndexToDocId[index];
                    if (docId && documentState.value[docId]) {
                        // Translate specific message if needed, or just show it
                        // The default message is "The files.X field must not be greater than..."
                        // We can set a generic message or try to use the one provided
                        if (errors[key].includes('greater than')) {
                             documentState.value[docId].errorMessage = 'El archivo pesa más de 10MB.';
                        } else {
                             documentState.value[docId].errorMessage = errors[key];
                        }
                    }
                }
            });
        },
    });
};
</script>

<template>
    <TeacherLayout>
        <Head title="Solicitar Beca" />
        
        <div class="space-y-6">
            <!-- Header -->
             <div class="flex items-center justify-between">
                <div>
                     <Link :href="route('teacher.announcements.index')" class="flex items-center text-gray-500 hover:text-[#1B396A] transition mb-1 text-sm font-medium">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 mr-1" fill="currentColor"><path :d="mdiArrowLeft"/></svg>
                        Volver a Convocatorias
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900">Solicitud de Beca</h1>
                    <p class="text-gray-600 mt-1">Convocatoria: <span class="font-semibold text-[#1B396A]">{{ announcement.name }}</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Form (Left) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- 1. General Information Card -->
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Información General</h2>
                        
                        <div>
                            <label for="position_type" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Tipo de Plaza <span class="text-red-500">*</span></label>
                            <input 
                                id="position_type"
                                v-model="form.position_type"
                                type="text"
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                :class="{ 'border-b-red-500': form.errors.position_type }"
                                placeholder="Ej. Titular A, Asociado B..."
                            />
                            <div v-if="!form.errors.position_type" class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Ingresa el tipo de plaza que ocupas actualmente</span>
                            </div>
                            <p v-if="form.errors.position_type" class="mt-1 text-sm text-red-600">{{ form.errors.position_type }}</p>
                        </div>
                    </div>

                    <!-- 2. Documentation Card -->
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Documentación Requerida</h2>
                        <p class="text-sm text-gray-600 mb-6">
                            Sube los documentos solicitados. Si participaste anteriormente, hemos cargado tus documentos previos automáticamente.
                        </p>

                        <!-- Dynamic Documents List -->
                        <div v-if="catalog_documents && catalog_documents.length > 0" class="space-y-8">
                            <div v-for="doc in catalog_documents" :key="doc.id" class="border-b border-gray-100 pb-8 last:border-0 last:pb-0">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-lg font-bold text-gray-800">{{ doc.name }}</h3>
                                            <span v-if="doc.is_required" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Obligatorio</span>
                                            <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Opcional</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">{{ doc.description || 'Sin descripción' }}</p>
                                         <!-- Download Template Link -->
                                        <a v-if="doc.template_url" :href="doc.template_url" target="_blank" class="text-xs font-bold text-[#1B396A] hover:underline flex items-center gap-1 mt-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/></svg>
                                            Descargar Formato
                                        </a>
                                    </div>
                                    <!-- Status Badge -->
                                    <div v-if="documentState[doc.id]?.type !== 'empty'" class="flex items-center gap-1 text-green-600 font-medium text-sm">
                                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path :d="mdiCheckBold"/></svg>
                                        Listo
                                    </div>
                                </div>

                                <!-- FILLED STATE: File Preview / Info -->
                                <div v-if="documentState[doc.id] && documentState[doc.id].type !== 'empty'" class="border-2 border-gray-300 rounded-lg overflow-hidden">
                                    <!-- Header del archivo -->
                                    <div class="bg-gray-100 border-b border-gray-300 p-3 flex items-center justify-between flex-wrap gap-2">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 truncate max-w-xs">
                                                {{ documentState[doc.id].type === 'new' ? documentState[doc.id].file.name : (documentState[doc.id].originalDoc.name || doc.name) }}
                                            </p>
                                            <p v-if="documentState[doc.id].type === 'new'" class="text-xs text-gray-600">
                                                Tamaño: {{ (documentState[doc.id].file.size / 1024 / 1024).toFixed(2) }} MB
                                            </p>
                                            <p v-else class="text-xs text-blue-600 font-medium">
                                                Recuperado de solicitud anterior
                                            </p>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex items-center gap-2">
                                             <!-- Toggle Preview Button -->
                                             <button 
                                                type="button" 
                                                @click="togglePreview(doc.id)"
                                                class="p-2 text-gray-500 hover:text-[#1B396A] hover:bg-white rounded-full transition"
                                                :title="documentState[doc.id].showPreview ? 'Ocultar Vista Previa' : 'Ver Vista Previa'"
                                             >
                                                <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path :d="documentState[doc.id].showPreview ? mdiEyeOff : mdiEye"/></svg>
                                             </button>

                                             <label :for="'doc-upload-' + doc.id" class="cursor-pointer px-4 py-2 bg-[#1B396A] text-white text-sm rounded hover:bg-[#0f2347] transition shadow-sm font-medium">
                                                Cambiar Archivo
                                                <input :id="'doc-upload-' + doc.id" type="file" class="hidden" @change="(e) => handleFileUpload(e, doc.id)" accept=".pdf" />
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Visor integrado (Collapsible) -->
                                    <div v-if="documentState[doc.id].showPreview" class="bg-gray-100 h-[500px] border-t border-gray-200">
                                        <!-- PDF Preview -->
                                        <iframe 
                                            v-if="getPreviewUrl(documentState[doc.id])" 
                                            :src="getPreviewUrl(documentState[doc.id])" 
                                            class="w-full h-full border-0"
                                            title="Visor de archivo"
                                        ></iframe>
                                        
                                        <!-- Fallback -->
                                        <div v-else class="h-full flex items-center justify-center bg-gray-50 p-6">
                                            <div class="text-center">
                                                <svg class="w-16 h-16 mx-auto text-[#1B396A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-gray-900 font-bold mb-2 text-lg">Documento Cargado</p>
                                                <p class="text-gray-600 font-medium mb-2">Vista previa no disponible</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- EMPTY STATE: Drop Zone -->
                                <div v-else class="flex items-center justify-center w-full">
                                    <label 
                                        :for="'doc-upload-' + doc.id" 
                                        class="flex flex-col items-center justify-center w-full h-48 bg-gradient-to-br from-[#F3F4F6] to-[#E5E7EB] border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gradient-to-br hover:from-[#EFF6FF] hover:to-[#DBEAFE] hover:border-[#1B396A] transition-all relative"
                                        @dragover.prevent
                                        @drop.prevent="(e) => handleDrop(e, doc.id)"
                                    >
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-3 text-[#1B396A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-700">
                                                <span class="font-semibold text-[#1B396A]">Arrastra archivos aquí</span> o haz clic para seleccionar
                                            </p>
                                            <p class="text-xs text-gray-500">Formato PDF (Máx. 10MB)</p>
                                        </div>
                                        <input :id="'doc-upload-' + doc.id" type="file" class="hidden" @change="(e) => handleFileUpload(e, doc.id)" accept=".pdf" />
                                    </label>
                                </div>

                                <!-- Error Message -->
                                <p v-if="documentState[doc.id]?.errorMessage" class="mt-2 text-sm text-red-600 font-medium flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ documentState[doc.id].errorMessage }}
                                </p>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                             <p>No hay documentos configurados.</p>
                        </div>
                    </div>
                </div>

                <!-- Summary / Actions (Right Checkbox) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Resumen</h3>
                        
                        <!-- Progress -->
                        <div class="space-y-3 mb-6">
                             <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Documentos listos:</span>
                                <span class="font-bold text-gray-900">
                                    {{ Object.values(documentState).filter(s => s.type !== 'empty').length }} / {{ catalog_documents.filter(d => d.is_required).length }} (Req)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-[#1B396A] h-2.5 rounded-full transition-all duration-500" :style="{ width: catalog_documents.length > 0 ? (Object.values(documentState).filter(s => s.type !== 'empty').length / catalog_documents.filter(d => d.is_required).length * 100) + '%' : '0%' }"></div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                            <p class="text-xs text-blue-800">
                                <strong>Nota:</strong> Al enviar esta solicitud, confirmas que la información proporcionada es verídica y aceptas los términos y condiciones de la convocatoria.
                            </p>
                        </div>

                        <button @click="submit" :disabled="form.processing"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#1B396A] hover:bg-[#0f2347] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1B396A] disabled:opacity-50 disabled:cursor-not-allowed transition">
                            <span v-if="form.processing">Enviando...</span>
                            <span v-else>Enviar Solicitud</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
