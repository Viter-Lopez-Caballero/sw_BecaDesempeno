<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
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
        // Validate size (10MB)
        if (file.size > 10 * 1024 * 1024) {
             // Ensure state exists to set error
             if (!documentState.value[docId]) {
                 documentState.value[docId] = { type: 'empty', file: null, originalDoc: null, showPreview: false, errorMessage: '' };
             }
             documentState.value[docId].errorMessage = 'El archivo pesa más de 10MB.';
             // Reset input
             event.target.value = '';
             return;
        }

        documentState.value[docId] = {
            type: 'new',
            file: file,
            originalDoc: null,
            showPreview: false, // Reset preview on new upload
            errorMessage: ''
        };
    }
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
                        <div v-if="catalog_documents && catalog_documents.length > 0" class="space-y-6">
                            <div v-for="doc in catalog_documents" :key="doc.id" class="p-4 border border-gray-100 rounded-lg transition" :class="documentState[doc.id]?.type !== 'empty' ? 'bg-blue-50/50 border-blue-100' : 'hover:bg-gray-50'">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <!-- Icon / Status -->
                                    <div class="flex-shrink-0 pt-1">
                                        <div v-if="documentState[doc.id]?.type !== 'empty'" class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 shadow-sm">
                                            <svg viewBox="0 0 24 24" class="w-6 h-6" fill="currentColor"><path :d="mdiCheckBold"/></svg>
                                        </div>
                                        <div v-else class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                             <svg viewBox="0 0 24 24" class="w-6 h-6" fill="currentColor"><path :d="mdiFileDocumentOutline"/></svg>
                                        </div>
                                    </div>

                                    <!-- Info & Actions -->
                                    <div class="flex-1">
                                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="font-semibold text-gray-900">{{ doc.name }}</h4>
                                                    <span v-if="doc.is_required" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Obligatorio
                                                    </span>
                                                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        Opcional
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mb-2">{{ doc.description || 'Sin descripción' }}</p>
                                                
                                                <!-- Status Message -->
                                                <div v-if="documentState[doc.id]?.type === 'reused'" class="text-xs font-medium text-blue-600 flex items-center gap-1 mb-2">
                                                     <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                     Documento recuperado de solicitud anterior
                                                </div>
                                                <div v-if="documentState[doc.id]?.type === 'new'" class="text-xs font-medium text-green-600 flex items-center gap-1 mb-2">
                                                 <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                 Archivo nuevo seleccionado: {{ documentState[doc.id].file.name }}
                                                </div>
                                                
                                                <!-- Download Template Link -->
                                                <a v-if="doc.template_url" :href="doc.template_url" target="_blank" class="text-xs font-bold text-[#1B396A] hover:underline flex items-center gap-1 mb-1">
                                                    Descargar Formato
                                                </a>
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex flex-col gap-2 items-end">
                                                
                                                <!-- View Button (Toggle) -->
                                                <button 
                                                    v-if="documentState[doc.id] && documentState[doc.id].type !== 'empty'"
                                                    @click="togglePreview(doc.id)"
                                                    type="button"
                                                    class="text-xs flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition font-medium w-full justify-center"
                                                >
                                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="documentState[doc.id]?.showPreview ? mdiEyeOff : mdiEye"/></svg>
                                                    {{ documentState[doc.id]?.showPreview ? 'Ocultar PDF' : 'Ver PDF' }}
                                                </button>

                                                <!-- Upload / Change Button -->
                                                <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition w-full justify-center whitespace-nowrap">
                                                    <span v-if="documentState[doc.id]?.type !== 'empty'" class="flex items-center gap-1">
                                                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-gray-500" fill="currentColor"><path :d="mdiRefresh"/></svg>
                                                        Cambiar
                                                    </span>
                                                    <span v-else class="flex items-center gap-1">
                                                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-gray-500" fill="currentColor"><path :d="mdiCloudUpload"/></svg>
                                                        Subir PDF
                                                    </span>
                                                    <input type="file" class="hidden" @change="(e) => handleFileUpload(e, doc.id)" accept=".pdf" />
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Error Message -->
                                        <div v-if="documentState[doc.id]?.errorMessage" class="text-sm text-red-600 font-medium flex items-center gap-1 justify-end mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            {{ documentState[doc.id].errorMessage }}
                                        </div>
                                    </div>
                                </div>
                                <!-- INLINE PREVIEW -->
                                <div v-if="documentState[doc.id]?.showPreview && documentState[doc.id]?.type !== 'empty'" class="mt-4 border-t pt-4 w-full">
                                    <div class="w-full h-[600px] bg-gray-100 rounded-lg overflow-hidden border border-gray-300">
                                        <iframe :src="getPreviewUrl(documentState[doc.id])" class="w-full h-full" frameborder="0"></iframe>
                                    </div>
                                </div>
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
