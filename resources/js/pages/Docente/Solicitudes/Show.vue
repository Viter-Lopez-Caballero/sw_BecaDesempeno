<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DocenteLayout from '@/layouts/DocenteLayout.vue';
import { mdiFileDocumentMultiple, mdiEye, mdiDownload } from '@mdi/js';
import { ref } from 'vue';

const props = defineProps({
    solicitud: Object,
});

const showModal = ref(false);
const currentPdfUrl = ref('');
const currentPdfTitle = ref('');

const openPdfModal = (doc) => {
    // Determine stream URL
    currentPdfUrl.value = route('docente.solicitudes.stream', doc.id);
    currentPdfTitle.value = doc.name;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    currentPdfUrl.value = '';
    currentPdfTitle.value = '';
};

const getFileIcon = (type) => {
    // Return svg path based on type or generic
    return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'; 
};
</script>

<template>
    <DocenteLayout>
        <Head title="Detalles de Solicitud" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detalles de la Solicitud</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <Link :href="route('docente.inicio')" class="text-gray-700 font-medium hover:text-[#1B396A]">Inicio</Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">{{ solicitud.convocatoria?.nombre }}</span>
                    </div>
                </div>
                 <Link :href="route('docente.inicio')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>
            
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200 p-8">
                <!-- Info Header with Status -->
                <div class="flex justify-between items-start mb-8 relative">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Información de la Solicitud</h3>
                        <p class="text-sm text-gray-500">Solicitud #{{ solicitud.id }}</p>
                    </div>
                    <div>
                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold text-white"
                             :class="{
                                'bg-green-500': solicitud.status === 'approved',
                                'bg-red-500': solicitud.status === 'rejected',
                                'bg-yellow-500': solicitud.status === 'pending'
                             }">
                            {{ 
                                solicitud.status === 'approved' ? 'Aprobado' : 
                                solicitud.status === 'rejected' ? 'Rechazada' : 'Pendiente' 
                            }}
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-y-8 gap-x-12 mb-10">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Profesor</p>
                        <p class="text-gray-900 font-medium text-base">{{ solicitud.profesor?.name || solicitud.user?.name || 'Usuario' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Departamento</p>
                        <p class="text-gray-900 font-medium text-base">{{ solicitud.profesor?.departamento || solicitud.user?.priority_area?.name || 'No asignado' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Campus</p>
                        <p class="text-gray-900 text-base leading-snug">{{ solicitud.campus || solicitud.user?.institucion?.nombre || 'No registrado' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Tipo de Beca</p>
                        <p class="text-gray-900 text-base">{{ solicitud.convocatoria?.nombre }}</p>
                    </div>
                    <div>
                         <p class="text-sm font-medium text-gray-600 mb-1">Fecha de Solicitud</p>
                         <p class="text-gray-900 text-base">{{ new Date(solicitud.created_at).toLocaleDateString() }}</p>
                    </div>
                </div>

                <!-- Documentation -->
                <div class="border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Documentación</h3>
                    
                    <div class="space-y-4">
                        <div v-if="!solicitud.documentos || solicitud.documentos.length === 0" class="text-gray-500 italic">
                            No hay documentos disponibles.
                        </div>

                        <div v-for="doc in solicitud.documentos" :key="doc.id" 
                            class="flex items-center justify-between p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4">
                                <div class="text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="getFileIcon(doc.file_type)" />
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">{{ doc.name }}</span>
                            </div>
                            
                            <!-- Actions (Preview or Download) -->
                            <div class="flex items-center gap-2">
                                <button v-if="doc.file_type === 'pdf'" @click="openPdfModal(doc)" class="flex items-center gap-1 text-[#1B396A] hover:bg-blue-50 px-3 py-1.5 rounded-md transition text-sm font-medium">
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="mdiEye"/></svg>
                                    Ver
                                </button>
                                <a :href="route('docente.solicitudes.download', doc.id)" class="flex items-center gap-1 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-md transition text-sm font-medium">
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="mdiDownload"/></svg>
                                    Descargar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>
                    
                    <!-- Modal Content -->
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[90vh] flex flex-col">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">{{ currentPdfTitle }}</h2>
                            <button @click="closeModal" class="text-gray-400 hover:text-gray-800 transition-colors cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- PDF Viewer -->
                        <div class="flex-1 overflow-hidden">
                            <iframe :src="currentPdfUrl" class="w-full h-full" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </DocenteLayout>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}
</style>
