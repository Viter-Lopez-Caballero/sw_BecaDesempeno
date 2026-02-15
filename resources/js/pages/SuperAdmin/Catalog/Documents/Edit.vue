<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiBookOpenPageVariant, mdiFileDocumentOutline } from '@mdi/js';
import { alertaExito, alertaError, alertaCargando, cerrarAlerta } from '@/utils/alerts.js';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    document: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.document.name,
    description: props.document.description,
    active: props.document.active,
    archivo: null,
});

const archivoPreview = ref(null);
const archivoNombre = ref(props.document.file_name || null);
const archivoActual = ref(props.document.file_path || null);
const archivoUrl = ref(props.document.file_path ? `/storage/${props.document.file_path}` : null);
const archivoTipo = ref(props.document.file_type || null);

const clearError = (field) => {
    if (form.errors[field]) {
        delete form.errors[field];
    }
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.archivo = file;
        archivoNombre.value = file.name;
        archivoTipo.value = file.type;
        archivoActual.value = null; // Marcar que se reemplazará el archivo actual
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                archivoPreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            if (archivoUrl.value && !props.document.file_path) {
                URL.revokeObjectURL(archivoUrl.value);
            }
            archivoUrl.value = URL.createObjectURL(file);
        } else {
            archivoPreview.value = null;
            if (archivoUrl.value && !props.document.file_path) {
                URL.revokeObjectURL(archivoUrl.value);
            }
            archivoUrl.value = null;
        }
    }
};

const removeFile = () => {
    if (archivoUrl.value && !props.document.file_path) {
        URL.revokeObjectURL(archivoUrl.value);
    }
    
    form.archivo = null;
    archivoPreview.value = null;
    archivoNombre.value = null;
    archivoActual.value = null;
    archivoUrl.value = null;
    archivoTipo.value = null;
    
    const fileInput = document.getElementById('archivo');
    if (fileInput) {
        fileInput.value = '';
    }
};

const submit = () => {
    form.clearErrors();
    
    if (!form.name) {
        form.errors.name = 'El nombre del documento es obligatorio';
        return;
    }
    
    alertaCargando('Actualizando...', 'Por favor espera mientras se actualiza el documento');
    form.post(route(`${props.routeName}update`, props.document.id), {
        _method: 'put',
        onSuccess: () => {
            cerrarAlerta();
            alertaExito('¡Actualizado!', 'El documento ha sido actualizado exitosamente');
        },
        onError: () => {
            cerrarAlerta();
            alertaError('Error', 'Hubo un problema al actualizar el documento');
        }
    });
};

const viewCurrentFile = () => {
    if (archivoActual.value) {
        window.open(`/storage/${archivoActual.value}`, '_blank');
    } else if (archivoUrl.value) {
        window.open(archivoUrl.value, '_blank');
    }
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="title" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiBookOpenPageVariant"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Catálogo</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <Link :href="route(`${routeName}index`)" class="flex items-center gap-2 hover:underline">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                <path :d="mdiFileDocumentOutline"/>
                            </svg>
                            <span class="text-gray-700 font-medium">Documentos Requeridos</span>
                        </Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Editar</span>
                    </div>
                </div>
                <Link :href="route(`${routeName}index`)" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <!-- Mensaje de documento fundamental -->
                <div v-if="document.is_fundamental" class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#F59E0B">
                            <path d="m233-120 65-281L80-590l288-25 112-265 112 265 288 25-218 189 65 281-247-149-247 149Z"/>
                        </svg>
                        <p class="text-sm font-medium text-amber-800">Este es un documento fundamental del sistema. Solo puedes actualizar su información, no eliminarlo.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                Nombre del Documento: <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="form.name" 
                                @input="clearError('name')" 
                                type="text" 
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" 
                                :class="{ 'border-b-red-500': form.errors.name }" 
                                placeholder="Ej. Cédula Profesional" 
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Descripción -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Descripción:</label>
                            <textarea 
                                v-model="form.description" 
                                @input="clearError('description')" 
                                rows="3" 
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" 
                                :class="{ 'border-b-red-500': form.errors.description }" 
                                placeholder="Descripción del documento..."
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <!-- Archivo de Plantilla/Ejemplo -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                Archivo de Plantilla/Ejemplo:
                            </label>
                            
                            <!-- Archivo Actual -->
                            <div v-if="archivoActual && !form.archivo" class="mb-4 p-4 border-2 border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-12 h-12 bg-green-600 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="white">
                                                <path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ archivoNombre }}</p>
                                            <p class="text-xs text-gray-500">Archivo actual</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="viewCurrentFile" class="text-blue-600 hover:text-blue-800 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor">
                                                <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z"/>
                                            </svg>
                                        </button>
                                        <button type="button" @click="removeFile" class="text-red-600 hover:text-red-800 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor">
                                                <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Nuevo Archivo -->
                            <div v-if="!archivoNombre" class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#6B7280">
                                            <path d="M440-320v-326L336-542l-56-58 200-200 200 200-56 58-104-104v326h-80ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500"><span class="font-semibold">Click para subir</span> o arrastra el archivo</p>
                                        <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (MAX. 30MB)</p>
                                    </div>
                                    <input id="archivo" type="file" class="hidden" @change="handleFileChange" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" />
                                </label>
                            </div>

                            <!-- Preview del nuevo archivo -->
                            <div v-else-if="form.archivo" class="mt-4 p-4 border-2 border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-12 h-12 bg-[#1B396A] rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="white">
                                                <path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ archivoNombre }}</p>
                                            <p class="text-xs text-gray-500">Nuevo archivo (reemplazará el actual)</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeFile" class="text-red-600 hover:text-red-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor">
                                            <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Vista Previa de Imagen -->
                                <div v-if="archivoPreview" class="mt-4">
                                    <img :src="archivoPreview" alt="Preview" class="max-h-48 rounded-lg mx-auto" />
                                </div>

                                <!-- Vista Previa de PDF -->
                                <div v-if="archivoUrl && archivoTipo === 'application/pdf'" class="mt-4">
                                    <iframe :src="archivoUrl" class="w-full h-96 rounded-lg"></iframe>
                                </div>
                            </div>
                            
                            <p v-if="form.errors.archivo" class="mt-1 text-sm text-red-600">{{ form.errors.archivo }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route(`${routeName}index`)" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition cursor-pointer">
                            Cancelar
                        </Link>
                        <button 
                            :disabled="form.processing" 
                            type="submit" 
                            class="px-6 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl disabled:opacity-75 flex items-center gap-2 font-medium cursor-pointer"
                        >
                            <span v-if="!form.processing">Actualizar</span>
                            <span v-else>Actualizando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
