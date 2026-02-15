<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { mdiArrowLeft, mdiFileDocumentOutline, mdiCloudUpload, mdiCheckBold } from '@mdi/js';
import { ref } from 'vue';

const props = defineProps({
    announcement: Object,
    catalog_documents: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    announcement_id: props.announcement.id,
    files: [], // Array of File objects
    file_types: [], // Array of strings (document names)
});

// Temporary storage for display
const uploadedFiles = ref({}); // Map docId -> File

const handleFileUpload = (event, docId, docName) => {
    const file = event.target.files[0];
    if (file) {
        uploadedFiles.value[docId] = file;
    }
};

const submit = () => {
    // Reconstruct arrays for submission
    form.announcement_id = props.announcement.id; 
    form.files = [];
    form.file_types = [];

    // Order matters? Controller iterates arrays.
    // We should iterate through our known documents and check if they are uploaded.
    
    // Validate all required?
    let missing = [];
    props.catalog_documents.forEach(doc => {
        if (doc.is_required) {
            if (uploadedFiles.value[doc.id]) {
                form.files.push(uploadedFiles.value[doc.id]);
                form.file_types.push(doc.name); // Send the descriptive name
            } else {
                missing.push(doc.name);
            }
        } else {
            // Optional documents
            if (uploadedFiles.value[doc.id]) {
                form.files.push(uploadedFiles.value[doc.id]);
                form.file_types.push(doc.name);
            }
        }
    });

    if (missing.length > 0) {
        alert('Por favor sube los siguientes documentos obligatorios:\n' + missing.join('\n'));
        return;
    }

    form.post(route('teacher.applications.store'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Validation Errors:', errors);
            alert('Error al enviar la solicitud. Por favor revisa los archivos subidos. \nDetalles: ' + JSON.stringify(errors));
        },
        onSuccess: () => {
            // Success handled by controller redirect
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
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Documentación Requerida</h2>
                        <p class="text-sm text-gray-600 mb-6">
                            Descarga los formatos, llénalos correctamente y súbelos en formato PDF. Asegúrate de que sean legibles.
                        </p>

                        <!-- Dynamic Documents List -->
                        <div v-if="catalog_documents && catalog_documents.length > 0" class="space-y-6">
                            <div v-for="doc in catalog_documents" :key="doc.id" class="flex flex-col sm:flex-row gap-4 p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                <!-- Icon / Status -->
                                <div class="flex-shrink-0 pt-1">
                                    <div v-if="uploadedFiles[doc.id]" class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                        <svg viewBox="0 0 24 24" class="w-6 h-6" fill="currentColor"><path :d="mdiCheckBold"/></svg>
                                    </div>
                                    <div v-else class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-[#1B396A]">
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
                                            
                                            <!-- Download Template Link -->
                                            <a v-if="doc.template_url" :href="doc.template_url" target="_blank" class="text-xs font-medium text-[#1B396A] hover:underline flex items-center gap-1 mb-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="14px" viewBox="0 -960 960 960" width="14px" fill="currentColor">
                                                    <path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/>
                                                </svg>
                                                Descargar Formato / Plantilla
                                            </a>
                                            <div v-else class="h-6"></div> <!-- Spacer -->
                                        </div>

                                        <!-- Upload Input -->
                                        <div class="flex-shrink-0">
                                            <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                                                <svg viewBox="0 0 24 24" class="w-5 h-5 mr-2 text-gray-400" fill="currentColor"><path :d="mdiCloudUpload"/></svg>
                                                {{ uploadedFiles[doc.id] ? 'Archivo seleccionado' : 'Subir PDF' }}
                                                <input type="file" class="hidden" @change="(e) => handleFileUpload(e, doc.id, doc.name)" accept=".pdf" />
                                            </label>
                                            <p v-if="uploadedFiles[doc.id]" class="text-xs text-green-600 mt-1 text-center font-medium max-w-[150px] truncate">
                                                {{ uploadedFiles[doc.id].name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#9CA3AF" class="mb-4">
                                <path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                            </svg>
                            <p class="text-lg font-medium">No hay documentos configurados para esta convocatoria</p>
                            <p class="text-sm">Por favor contacta al administrador</p>
                        </div>
                    </div>
                </div>

                <!-- Summary / Actions (Right Checkbox) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Resumen</h3>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Documentos requeridos:</span>
                                <span class="font-bold text-gray-900">{{ catalog_documents.filter(d => d.is_required).length }}</span>
                            </div>
                             <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Documentos subidos:</span>
                                <span class="font-bold text-gray-900">{{ Object.keys(uploadedFiles).length }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-[#1B396A] h-2.5 rounded-full transition-all duration-500" :style="{ width: catalog_documents.length > 0 ? (Object.keys(uploadedFiles).length / catalog_documents.filter(d => d.is_required).length * 100) + '%' : '0%' }"></div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                            <p class="text-xs text-blue-800">
                                <strong>Nota:</strong> Al enviar esta solicitud, confirmas que la información proporcionada es verídica y aceptas los términos y condiciones de la convocatoria <strong>{{ announcement.name }}</strong>.
                            </p>
                        </div>

                        <button @click="submit" :disabled="form.processing || Object.keys(uploadedFiles).length < catalog_documents.filter(d => d.is_required).length"
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
