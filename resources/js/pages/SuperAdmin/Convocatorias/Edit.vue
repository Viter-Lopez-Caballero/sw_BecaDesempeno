<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiBullhorn } from '@mdi/js';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    convocatoria: {
        type: Object,
        required: true,
    },
    documentosCatalogo: {
        type: Array,
        default: () => [],
    },
    documentosVinculados: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    id: props.convocatoria.data?.id || props.convocatoria.id,
    nombre: props.convocatoria.data?.nombre || props.convocatoria.nombre,
    descripcion: props.convocatoria.data?.descripcion || props.convocatoria.descripcion,
    estado: props.convocatoria.data?.estado || props.convocatoria.estado,
    archivo: null,
    imagen: null,
    _method: 'PUT',
    // Calendar Fields (Safely access nested props)
    publicacion_inicio: props.convocatoria.data?.calendario?.publicacion_inicio || '',
    registro_inicio: props.convocatoria.data?.calendario?.registro_inicio || '',
    registro_fin: props.convocatoria.data?.calendario?.registro_fin || '',
    evaluacion_inicio: props.convocatoria.data?.calendario?.evaluacion_inicio || '',
    evaluacion_fin: props.convocatoria.data?.calendario?.evaluacion_fin || '',
    resultados_inicio: props.convocatoria.data?.calendario?.resultados_inicio || '',
    resultados_fin: props.convocatoria.data?.calendario?.resultados_fin || '',
});

const documentosForm = useForm({
    documentos: props.documentosVinculados || [],
});

const archivoPreview = ref(null);
const archivoNombre = ref(props.convocatoria.data?.archivo_nombre || null);
const archivoActual = ref(props.convocatoria.data?.archivo_path || null);
const archivoUrl = ref(props.convocatoria.data?.archivo_path ? `/storage/${props.convocatoria.data.archivo_path}` : null);
const archivoTipo = ref(props.convocatoria.data?.archivo_tipo || null);

const imagenPreview = ref(null);
const imagenNombre = ref(null);
const imagenActual = ref(props.convocatoria.data?.imagen_path || null);
const imagenUrl = ref(props.convocatoria.data?.imagen_path ? `/storage/${props.convocatoria.data.imagen_path}` : null);

const estadosOptions = [
    { id: 'pendiente', nombre: 'Pendiente' },
    { id: 'activa', nombre: 'Activa' },
    { id: 'cerrada', nombre: 'Cerrada' }
];

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.archivo = file;
        archivoNombre.value = file.name;
        archivoTipo.value = file.type;
        
        // Limpiar URL anterior si existe
        if (archivoUrl.value && archivoUrl.value.startsWith('blob:')) {
            URL.revokeObjectURL(archivoUrl.value);
        }
        
        // Crear URL del blob para preview instantánea
        archivoUrl.value = URL.createObjectURL(file);
        archivoPreview.value = archivoUrl.value;
    }
};

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.imagen = file;
        imagenNombre.value = file.name;
        
        // Limpiar URL anterior si existe
        if (imagenUrl.value && imagenUrl.value.startsWith('blob:')) {
            URL.revokeObjectURL(imagenUrl.value);
        }
        
        // Crear URL del blob para preview instantánea
        imagenUrl.value = URL.createObjectURL(file);
        imagenPreview.value = imagenUrl.value;
    }
};

const removeFile = () => {
    // Limpiar URL de blob si existe
    if (archivoUrl.value && archivoUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(archivoUrl.value);
    }
    
    form.archivo = null;
    archivoPreview.value = null;
    
    // Restaurar archivo original si existe
    if (archivoActual.value) {
        archivoUrl.value = `/storage/${archivoActual.value}`;
        archivoNombre.value = props.convocatoria.data?.archivo_nombre || null;
        archivoTipo.value = props.convocatoria.data?.archivo_tipo || null;
    } else {
        archivoUrl.value = null;
        archivoNombre.value = null;
        archivoTipo.value = null;
    }
};

const removeImage = () => {
    // Limpiar URL de blob si existe
    if (imagenUrl.value && imagenUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(imagenUrl.value);
    }
    
    form.imagen = null;
    imagenPreview.value = null;
    
    // Restaurar imagen original si existe
    if (imagenActual.value) {
        imagenUrl.value = `/storage/${imagenActual.value}`;
        imagenNombre.value = null; 
    } else {
        imagenUrl.value = null;
        imagenNombre.value = null;
    }
};

const submit = () => {
    form.post(route(`${props.routeName}update`, { convocatoria: form.id }));
};

const toggleDocumento = (docId) => {
    const index = documentosForm.documentos.indexOf(docId);
    if (index > -1) {
        documentosForm.documentos.splice(index, 1);
    } else {
        documentosForm.documentos.push(docId);
    }
};

const guardarDocumentos = () => {
    documentosForm.put(route('convocatorias.updateDocumentos', form.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="title" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <Link :href="route(`${routeName}index`)" class="flex items-center gap-2 hover:underline">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                <path :d="mdiBullhorn"/>
                            </svg>
                            <span class="text-gray-700 font-medium">Convocatorias</span>
                        </Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Editar Convocatoria</span>
                    </div>
                </div>
                <Link :href="route(`${routeName}index`)" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Nombre: <span class="text-red-500">*</span></label>
                            <input v-model="form.nombre" type="text" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Nombre de la convocatoria" />
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, introduce el nombre de la convocatoria</span>
                            </div>
                            <p v-if="form.errors.nombre" class="mt-1 text-sm text-red-600">{{ form.errors.nombre }}</p>
                        </div>
                        
                        <!-- Estado -->
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Estado: <span class="text-red-500">*</span></label>
                            <VueSelect
                                v-model="form.estado"
                                :options="estadosOptions"
                                :reduce="option => option.id"
                                label="nombre"
                                placeholder="Selecciona un estado..."
                                :searchable="false"
                                :clearable="false"
                                class="vue-select-custom"
                            />
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, selecciona el estado de la convocatoria</span>
                            </div>
                            <p v-if="form.errors.estado" class="mt-1 text-sm text-red-600">{{ form.errors.estado }}</p>
                        </div>

                        <!-- Descripción -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Descripción:</label>
                            <textarea v-model="form.descripcion" rows="4" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Descripción de la convocatoria"></textarea>
                            <div class="flex items-center justify-between mt-1">
                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, introduce la descripción de la convocatoria</span>
                                </div>
                                <span class="text-gray-400 text-sm">{{ form.descripcion?.length || 0 }}/500</span>
                            </div>
                            <p v-if="form.errors.descripcion" class="mt-1 text-sm text-red-600">{{ form.errors.descripcion }}</p>
                        </div>

                        <!-- Imagen de Convocatoria -->
                        <div class="col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                Imagen de Portada:
                            </label>
                            
                            <!-- Visor de imagen cuando hay una seleccionada -->
                            <div v-if="imagenUrl" class="border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-50 flex flex-col items-center p-4">
                                <img :src="imagenUrl" :alt="imagenNombre" class="max-h-64 object-contain shadow-md rounded-lg mb-4" />
                                <div class="flex items-center gap-3">
                                    <span v-if="imagenNombre" class="text-sm font-semibold text-gray-700">{{ imagenNombre }}</span>
                                    <div class="flex items-center gap-2">
                                        <label for="imagen-convocatoria-change" class="px-3 py-1 bg-[#1B396A] text-white text-xs rounded hover:bg-[#0f2347] transition cursor-pointer">
                                            Cambiar Imagen
                                        </label>
                                        <button v-if="form.imagen" type="button" @click="removeImage" 
                                            class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                            Revertir
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Drop zone cuando NO hay imagen -->
                            <div v-else class="flex items-center justify-center w-full">
                                <label for="imagen-convocatoria" class="flex flex-col items-center justify-center w-full h-32 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100 hover:border-[#1B396A] transition-all">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold text-[#1B396A]">Sube una imagen</span> o arrastra aquí
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG o GIF up to 2MB</p>
                                    </div>
                                    <input id="imagen-convocatoria" type="file" class="hidden" @change="handleImageChange" accept="image/*" />
                                </label>
                            </div>
                            
                            <!-- Input para cambiar imagen (oculto) -->
                            <input id="imagen-convocatoria-change" type="file" class="hidden" @change="handleImageChange" accept="image/*" />

                            <p v-if="form.errors.imagen" class="mt-1 text-sm text-red-600">{{ form.errors.imagen }}</p>
                        </div>

                        <!-- Archivo de Convocatoria -->
                        <div class="col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                Archivo de Convocatoria:
                            </label>
                            
                            <!-- Visor de archivo si existe uno (actual o nuevo) -->
                            <div v-if="archivoUrl" class="border-2 border-gray-300 rounded-lg overflow-hidden">
                                <!-- Header del archivo -->
                                <div class="bg-gray-100 border-b border-gray-300 p-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Nombre del Archivo: {{ archivoNombre }}</p>
                                        <p class="text-xs text-gray-600">Tamaño: {{ form.archivo ? (form.archivo.size / 1024 / 1024).toFixed(2) + ' MB' : '2.5 MB' }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label for="archivo-convocatoria-change" class="px-4 py-2 bg-[#1B396A] text-white text-sm rounded hover:bg-[#0f2347] transition cursor-pointer">
                                            Cambiar Archivo
                                        </label>
                                        <button v-if="form.archivo" type="button" @click="removeFile" 
                                            class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Visor integrado -->
                                <div class="bg-gray-100" style="height: 500px;">
                                    <iframe v-if="archivoTipo?.includes('pdf')" 
                                        :src="archivoUrl" 
                                        class="w-full h-full border-0"
                                        title="Visor de archivo">
                                    </iframe>
                                    <div v-else-if="archivoTipo?.startsWith('image/')" class="h-full flex items-center justify-center bg-gray-900">
                                        <img :src="archivoUrl" :alt="archivoNombre" class="max-h-full max-w-full object-contain" />
                                    </div>
                                    <div v-else class="h-full flex items-center justify-center bg-gray-50 p-6">
                                        <div class="text-center">
                                            <svg class="w-16 h-16 mx-auto text-[#1B396A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-900 font-bold mb-2 text-lg">Archivo Seleccionado</p>
                                            <p class="text-gray-600 font-medium mb-2 break-all">{{ archivoNombre }}</p>
                                            <p class="text-sm text-gray-500">Vista previa no disponible para este tipo de archivo.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Drop zone cuando NO hay archivo -->
                            <div v-else class="flex items-center justify-center w-full">
                                <label for="archivo-convocatoria" class="flex flex-col items-center justify-center w-full h-48 bg-gradient-to-br from-[#F3F4F6] to-[#E5E7EB] border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gradient-to-br hover:from-[#EFF6FF] hover:to-[#DBEAFE] hover:border-[#1B396A] transition-all">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-[#1B396A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-700">
                                            <span class="font-semibold text-[#1B396A]">Arrastra archivos aquí</span> o haz clic para seleccionar
                                        </p>
                                        <p class="text-xs text-gray-500">Tamaño máximo: 30MB Admite: JPG, PNG, PDF</p>
                                    </div>
                                    <input id="archivo-convocatoria" type="file" class="hidden" @change="handleFileChange" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />

                        <!-- Documentos Requeridos Section -->
                        <div v-if="documentosCatalogo && documentosCatalogo.length >0">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Documentos Requeridos:</label>
                            <div class="bg-[#F3F4F6] rounded-lg p-4 border border-gray-200">
                                <p class="text-sm text-gray-600 mb-4">Selecciona los documentos que los docentes deberán subir al solicitar esta convocatoria:</p>
                                
                                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                                    <div v-for="doc in documentosCatalogo" :key="doc.id" class="flex items-center gap-3 p-3 bg-white rounded-lg hover:bg-gray-50 transition border border-gray-100">
                                        <input 
                                            :id="`doc-${doc.id}`" 
                                            type="checkbox" 
                                            :checked="documentosForm.documentos.includes(doc.id)" 
                                            @change="toggleDocumento(doc.id)" 
                                            class="w-4 h-4 text-[#1B396A] bg-gray-100 border-gray-300 rounded focus:ring-[#1B396A] focus:ring-2" 
                                        />
                                        <label :for="`doc-${doc.id}`" class="flex-1 cursor-pointer">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-gray-900">{{ doc.nombre }}</span>
                                                <span v-if="doc.es_obligatorio" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Obligatorio
                                                </span>
                                                <span class="text-xs font-medium uppercase px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                                    {{ doc.tipo_archivo }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">{{ doc.descripcion || 'Sin descripción' }}</p>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                                    <span class="text-sm text-gray-600">
                                        <strong>{{ documentosForm.documentos.length }}</strong> de <strong>{{ documentosCatalogo.length }}</strong> documentos seleccionados
                                    </span>
                                    <button 
                                        @click.prevent="guardarDocumentos" 
                                        :disabled="documentosForm.processing" 
                                        type="button" 
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition disabled:opacity-50"
                                    >
                                        {{ documentosForm.processing ? 'Guardando...' : 'Guardar Documentos' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                                </label>
                            </div>

                            <!-- Inputs ocultos para cambiar archivo -->
                            <input id="archivo-convocatoria" type="file" class="hidden" @change="handleFileChange" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                            <input id="archivo-convocatoria-change" type="file" class="hidden" @change="handleFileChange" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />

                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Sube el documento oficial de la convocatoria</span>
                            </div>
                            <p v-if="form.errors.archivo" class="mt-1 text-sm text-red-600">{{ form.errors.archivo }}</p>
                        </div>
                    </div>

                    <!-- Calendario Section -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1B396A">
                                <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/>
                            </svg>
                            <h2 class="text-xl font-bold text-gray-900">Calendario de la Convocatoria</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Publicación -->
                            <div class="space-y-4">
                                <h3 class="font-semibold text-[#1B396A] border-b border-gray-200 pb-1">Publicación</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Fecha de Publicación</label>
                                        <input v-model="form.publicacion_inicio" type="date" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-[#1B396A] focus:border-[#1B396A] block w-full p-2.5" />
                                        <p v-if="form.errors.publicacion_inicio" class="mt-1 text-xs text-red-600">{{ form.errors.publicacion_inicio }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Registro -->
                            <div class="space-y-4">
                                <h3 class="font-semibold text-[#1B396A] border-b border-gray-200 pb-1">Registro de Solicitudes</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Inicio</label>
                                        <input v-model="form.registro_inicio" type="date" :min="form.publicacion_inicio" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-[#1B396A] focus:border-[#1B396A] block w-full p-2.5" />
                                        <p v-if="form.errors.registro_inicio" class="mt-1 text-xs text-red-600">{{ form.errors.registro_inicio }}</p>
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Fin</label>
                                        <input v-model="form.registro_fin" type="date" :min="form.registro_inicio" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-[#1B396A] focus:border-[#1B396A] block w-full p-2.5" />
                                        <p v-if="form.errors.registro_fin" class="mt-1 text-xs text-red-600">{{ form.errors.registro_fin }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Evaluación -->
                            <div class="space-y-4">
                                <h3 class="font-semibold text-[#1B396A] border-b border-gray-200 pb-1">Evaluación</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Inicio</label>
                                        <input v-model="form.evaluacion_inicio" type="date" :min="form.registro_fin" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-[#1B396A] focus:border-[#1B396A] block w-full p-2.5" />
                                        <p v-if="form.errors.evaluacion_inicio" class="mt-1 text-xs text-red-600">{{ form.errors.evaluacion_inicio }}</p>
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Fin</label>
                                        <input v-model="form.evaluacion_fin" type="date" :min="form.evaluacion_inicio" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-[#1B396A] focus:border-[#1B396A] block w-full p-2.5" />
                                        <p v-if="form.errors.evaluacion_fin" class="mt-1 text-xs text-red-600">{{ form.errors.evaluacion_fin }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Resultados -->
                            <div class="space-y-4">
                                <h3 class="font-semibold text-[#1B396A] border-b border-gray-200 pb-1">Resultados</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Inicio</label>
                                        <input v-model="form.resultados_inicio" type="date" :min="form.evaluacion_fin" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-[#1B396A] focus:border-[#1B396A] block w-full p-2.5" />
                                        <p v-if="form.errors.resultados_inicio" class="mt-1 text-xs text-red-600">{{ form.errors.resultados_inicio }}</p>
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Fin</label>
                                        <input v-model="form.resultados_fin" type="date" :min="form.resultados_inicio" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-[#1B396A] focus:border-[#1B396A] block w-full p-2.5" />
                                        <p v-if="form.errors.resultados_fin" class="mt-1 text-xs text-red-600">{{ form.errors.resultados_fin }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route(`${routeName}index`)" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </Link>
                        <button :disabled="form.processing" type="submit" class="px-6 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl disabled:opacity-75 flex items-center gap-2 font-medium">
                            <span v-if="!form.processing">Actualizar</span>
                            <span v-else>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>

<style scoped>
.vue-select-custom :deep(.vs__dropdown-toggle) {
    background: linear-gradient(to bottom right, #F3F4F6, #E5E7EB);
    border: none;
    border-bottom: 2px solid #D1D5DB;
    border-radius: 0.5rem;
    padding: 0.625rem 0.75rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
}

.vue-select-custom :deep(.vs__dropdown-toggle):hover {
    border-bottom-color: rgba(27, 57, 106, 0.5);
}

.vue-select-custom :deep(.vs--open .vs__dropdown-toggle) {
    background: linear-gradient(to bottom right, #EFF6FF, #DBEAFE);
    border-bottom-color: #1B396A;
}

.vue-select-custom :deep(.vs__search) {
    margin: 0;
    padding: 0;
    border: none;
    font-size: 0.875rem;
    color: #111827;
}

.vue-select-custom :deep(.vs__search::placeholder) {
    color: #9CA3AF;
}

.vue-select-custom :deep(.vs__selected) {
    margin: 0;
    padding: 0;
    border: none;
    color: #111827;
    font-size: 0.875rem;
}

.vue-select-custom :deep(.vs__actions) {
    padding: 0 4px 0 6px;
}

.vue-select-custom :deep(.vs__clear),
.vue-select-custom :deep(.vs__open-indicator) {
    fill: #1B396A;
    transition: transform 0.2s;
}

.vue-select-custom :deep(.vs__open-indicator) {
    transform: scale(0.70);
}

.vue-select-custom :deep(.vs--open .vs__open-indicator) {
    transform: rotate(180deg) scale(0.70);
}

.vue-select-custom :deep(.vs__dropdown-menu) {
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    margin-top: 4px;
}

.vue-select-custom :deep(.vs__dropdown-option) {
    padding: 0.625rem 0.75rem;
    color: #374151;
    font-size: 0.875rem;
    transition: all 0.15s;
}

.vue-select-custom :deep(.vs__dropdown-option--highlight) {
    background: #1B396A;
    color: white;
}

.vue-select-custom :deep(.vs__no-options) {
    padding: 0.75rem;
    color: #6B7280;
    font-size: 0.875rem;
    text-align: center;
}
</style>
