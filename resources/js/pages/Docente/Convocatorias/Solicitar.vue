<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DocenteLayout from '@/layouts/DocenteLayout.vue';
import { mdiArrowLeft, mdiFileDocumentOutline, mdiCloudUpload, mdiCheckBold } from '@mdi/js';
import { ref } from 'vue';

const props = defineProps({
    convocatoria: Object,
});

// Required documents list
const documents = [
    {
        id: 'carta_exclusividad',
        name: 'Carta de Exclusividad Laboral',
        url: 'https://edd.tecnm.mx/formatos/2025/CARTA_EXCLUSIVIDAD_EDD2025.docx',
        description: 'Documento firmado declarando exclusividad con TecNM.',
        type: 'docx'
    },
    {
        id: 'liberacion_academica',
        name: 'Liberación de Actividades Académicas',
        url: 'https://edd.tecnm.mx/formatos/2025/Formato_Liberacion_de_Actividades_Academicas_2025.docx',
        description: 'Constancia de cumplimiento de actividades académicas.',
        type: 'docx'
    },
    {
        id: 'constancia_docente',
        name: 'Constancia Actividades Frente a Grupo',
        url: 'https://edd.tecnm.mx/formatos/2025/Formato_Constancia_de_Liberacion_de_Actividades_Docentes_2025.docx',
        description: 'Certificación de cumplimiento frente a grupo.',
        type: 'docx'
    },
    // Add other generic requirements if needed
    {
        id: 'cedula',
        name: 'Cédula Profesional',
        url: null, // No template
        description: 'Copia digital de su Cédula Profesional.',
        type: 'pdf'
    }
];

const form = useForm({
    convocatoria_id: props.convocatoria.id,
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
    form.convocatoria_id = props.convocatoria.id; 
    form.files = [];
    form.file_types = [];

    // Order matters? Controller iterates arrays.
    // We should iterate through our known documents and check if they are uploaded.
    
    // Validate all required?
    // Let's assume all listed are required.
    let missing = [];
    documents.forEach(doc => {
        if (uploadedFiles.value[doc.id]) {
            form.files.push(uploadedFiles.value[doc.id]);
            form.file_types.push(doc.name); // Send the descriptive name
        } else {
            missing.push(doc.name);
        }
    });

    if (missing.length > 0) {
        alert('Por favor sube los siguientes documentos obligatorios:\n' + missing.join('\n'));
        return;
    }

    form.post(route('docente.solicitudes.store'), {
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
    <DocenteLayout>
        <Head title="Solicitar Beca" />
        
        <div class="space-y-6">
            <!-- Header -->
             <div class="flex items-center justify-between">
                <div>
                     <Link :href="route('docente.convocatorias.index')" class="flex items-center text-gray-500 hover:text-[#1B396A] transition mb-1 text-sm font-medium">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 mr-1" fill="currentColor"><path :d="mdiArrowLeft"/></svg>
                        Volver a Convocatorias
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900">Solicitud de Beca</h1>
                    <p class="text-gray-600 mt-1">Convocatoria: <span class="font-semibold text-[#1B396A]">{{ convocatoria.nombre }}</span></p>
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

                        <div class="space-y-6">
                            <div v-for="doc in documents" :key="doc.id" class="flex flex-col sm:flex-row gap-4 p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
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
                                            <h4 class="font-semibold text-gray-900">{{ doc.name }}</h4>
                                            <p class="text-xs text-gray-500 mb-2">{{ doc.description }}</p>
                                            
                                            <!-- Download Template Link -->
                                            <a v-if="doc.url" :href="doc.url" target="_blank" class="text-xs font-medium text-[#1B396A] hover:underline flex items-center gap-1 mb-3">
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
                    </div>
                </div>

                <!-- Summary / Actions (Right Checkbox) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Resumen</h3>
                        <div class="space-y-3 mb-6">
                             <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Documentos subidos:</span>
                                <span class="font-bold text-gray-900">{{ Object.keys(uploadedFiles).length }} / {{ documents.length }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-[#1B396A] h-2.5 rounded-full transition-all duration-500" :style="{ width: (Object.keys(uploadedFiles).length / documents.length * 100) + '%' }"></div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                            <p class="text-xs text-blue-800">
                                <strong>Nota:</strong> Al enviar esta solicitud, confirmas que la información proporcionada es verídica y aceptas los términos y condiciones de la convocatoria <strong>{{ convocatoria.nombre }}</strong>.
                            </p>
                        </div>

                        <button @click="submit" :disabled="form.processing || Object.keys(uploadedFiles).length < documents.length"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#1B396A] hover:bg-[#0f2347] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1B396A] disabled:opacity-50 disabled:cursor-not-allowed transition">
                            <span v-if="form.processing">Enviando...</span>
                            <span v-else>Enviar Solicitud</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </DocenteLayout>
</template>
