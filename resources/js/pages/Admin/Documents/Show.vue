<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiFileDocumentMultiple } from '@mdi/js';

const props = defineProps({
    solicitud: Object,
});

const getFileIcon = (type) => {
    // Return svg path based on type or generic
    return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'; 
};
</script>

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
                        <Link :href="route('catalogo.documentos')" class="text-gray-700 font-medium hover:text-[#1B396A]">Documentos</Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">{{ solicitud.user?.name }}</span>
                    </div>
                </div>
                 <Link :href="route('catalogo.documentos')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </Link>
            </div>
            
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-[#1B396A] mb-4 border-b pb-2">Información del Docente</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre Completo</p>
                            <p class="text-gray-900 font-medium text-lg">{{ solicitud.user?.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Correo Electrónico</p>
                            <p class="text-gray-900">{{ solicitud.user?.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Campus</p>
                            <p class="text-gray-900">{{ solicitud.user?.institucion?.nombre || 'No registrada' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Área Prioritaria</p>
                            <p class="text-gray-900">{{ solicitud.user?.priority_area?.name || 'No asignada' }}</p>
                        </div>
                         <div>
                            <p class="text-sm font-medium text-gray-500">Sub Área</p>
                            <p class="text-gray-900">{{ solicitud.user?.sub_area?.name || 'No asignada' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Solicitud</p>
                            <p class="text-gray-900">{{ new Date(solicitud.created_at).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-[#1B396A] mb-6 border-b pb-2 flex justify-between items-center">
                        Documentos Cargados
                        <span class="text-sm font-normal text-white bg-[#1B396A] px-3 py-1 rounded-full">{{ solicitud.documentos.length }} archivos</span>
                    </h3>

                    <div v-if="solicitud.documentos.length === 0" class="text-center py-12 text-gray-500">
                        No hay documentos cargados para esta solicitud.
                    </div>

                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="doc in solicitud.documentos" :key="doc.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow bg-gray-50">
                            <div class="flex flex-col items-center text-center">
                                <div class="bg-blue-100 p-3 rounded-full mb-3 text-[#1B396A]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="getFileIcon(doc.file_type)" />
                                    </svg>
                                </div>
                                <h4 class="font-medium text-gray-900 mb-1 truncate w-full" :title="doc.name">{{ doc.name }}</h4>
                                <p class="text-xs text-gray-500 mb-4">{{ doc.created_at.split('T')[0] }}</p>
                                
                                <a :href="route('admin.documents.download', doc.id)" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 bg-[#1B396A] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0f2347] transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Descargar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </LayoutAuthenticated>
</template>
