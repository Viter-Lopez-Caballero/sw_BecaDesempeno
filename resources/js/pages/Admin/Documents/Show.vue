<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiFileDocumentMultiple, mdiEye } from '@mdi/js';
import { ref } from 'vue';

const props = defineProps({
    application: Object,
});

const getFileIcon = (type) => {
    // Return svg path based on type or generic
    return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'; 
};

const showModal = ref(false);
const currentPdfUrl = ref('');
const currentPdfTitle = ref('');

const openPdfModal = (doc) => {
    currentPdfUrl.value = route('admin.documents.stream', doc.id);
    currentPdfTitle.value = doc.name;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    currentPdfUrl.value = '';
    currentPdfTitle.value = '';
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>

<template>
    <LayoutAuthenticated>
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
                        <Link :href="route('admin.documents.index')" class="text-gray-700 font-medium hover:text-[#1B396A]">Documentos</Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-700 font-medium" v-if="application.user?.institution">{{ application.user?.institution?.name }}</span>
                        <svg v-if="application.user?.institution" xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">{{ application.user?.name }}</span>
                    </div>
                </div>
                 <Link :href="route('admin.documents.index')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>
            
                <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-[#1B396A] mb-4 border-b pb-2">Información del Solicitante</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre Completo</p>
                            <p class="text-gray-900 font-medium text-lg">{{ application.user?.name || application.user?.data?.name || 'Usuario' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Correo Electrónico</p>
                            <p class="text-gray-900">{{ application.user?.email || application.user?.data?.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Campus</p>
                            <p class="text-gray-900">{{ application.user?.institution?.name || application.user?.data?.institution?.name || 'No registrada' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Área Prioritaria</p>
                            <p class="text-gray-900">{{ application.user?.priority_area?.name || application.user?.data?.priority_area?.name || 'No asignada' }}</p>
                        </div>
                         <div>
                            <p class="text-sm font-medium text-gray-500">Sub Área</p>
                            <p class="text-gray-900">{{ application.user?.sub_area?.name || application.user?.data?.sub_area?.name || 'No asignada' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Convocatoria</p>
                            <p class="text-gray-900 font-semibold text-[#1B396A]">{{ application.announcement?.name || application.announcement?.data?.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Solicitud</p>
                            <p class="text-gray-900">{{ new Date(application.created_at).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-[#1B396A] mb-6 border-b pb-2 flex justify-between items-center">
                        Documentos Cargados
                        <span class="text-sm font-normal text-white bg-[#1B396A] px-3 py-1 rounded-full">{{ (application.documents?.data || application.documents)?.length || 0 }} archivos</span>
                    </h3>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                                <tr>
                                    <th scope="col" class="px-6 py-4 tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-4 tracking-wider">Documento</th>
                                    <th scope="col" class="px-6 py-4 tracking-wider">Fecha de Carga</th>
                                    <th scope="col" class="px-6 py-4 text-center tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="(doc, index) in (application.documents?.data || application.documents)" :key="doc.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-gray-900 font-medium">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 text-gray-900 font-semibold flex items-center gap-2">
                                        <div class="p-1 rounded bg-blue-50 text-[#1B396A]">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" :d="getFileIcon(doc.file_type)" />
                                            </svg>
                                        </div>
                                        {{ doc.name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ new Date(doc.created_at).toLocaleDateString() }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="openPdfModal(doc)" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 transition text-xs font-medium uppercase gap-1" title="Ver Documento">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" :d="mdiEye" />
                                                </svg>
                                                Ver
                                            </button>
                                            <a :href="route('admin.documents.download', doc.id)" 
                                            class="inline-flex items-center px-3 py-1.5 bg-[#1B396A] text-white rounded-md hover:bg-[#0f2347] transition text-xs font-medium uppercase gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                </svg>
                                                Descargar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!application.documents || (application.documents.data && application.documents.data.length === 0) || (!application.documents.data && application.documents.length === 0)">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        No hay documentos cargados para esta solicitud.
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
                </div>

            </div>

             <!-- PDF Preview Modal -->
            <Teleport to="body">
                <Transition name="modal">
                    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl h-[80vh] flex flex-col">
                                <!-- Header -->
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b flex justify-between items-center">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                        Vista Previa: {{ currentPdfTitle }}
                                    </h3>
                                    <button @click="closeModal" type="button" class="text-gray-400 hover:text-gray-500">
                                        <span class="sr-only">Cerrar</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 bg-gray-100 p-4">
                                    <iframe :src="currentPdfUrl" class="w-full h-full rounded-md border border-gray-300" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </Teleport>
    </LayoutAuthenticated>
</template>
