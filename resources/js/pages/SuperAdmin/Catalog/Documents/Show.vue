<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiFileDocumentMultiple, mdiBookOpenPageVariant, mdiEye, mdiEyeOff, mdiDownload } from '@mdi/js';
import { ref } from 'vue';

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
});

const documentsState = ref({});

const togglePreview = (docId) => {
    if (!documentsState.value[docId]) {
        documentsState.value[docId] = { showPreview: false };
    }
    documentsState.value[docId].showPreview = !documentsState.value[docId].showPreview;
};

const getPreviewUrl = (doc) => {
    return route('catalog.documents.streamDocente', doc.id);
};

const getFileIcon = () => {
    return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z';
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="title" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiBookOpenPageVariant"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Catálogo</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <Link :href="route(`${routeName}index`, { tab: 'docentes' })" class="flex items-center gap-2 hover:underline">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                <path :d="mdiFileDocumentMultiple"/>
                            </svg>
                            <span class="text-gray-700 font-medium">Documentos</span>
                        </Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Detalles</span>
                    </div>
                </div>
                <Link :href="route(`${routeName}index`, { tab: 'docentes' })" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <!-- Información del Profesor -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1B396A">
                        <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/>
                    </svg>
                    Información del Profesor
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Profesor</label>
                        <p class="text-base font-semibold text-gray-900">{{ application.teacher?.name || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Correo Electrónico</label>
                        <p class="text-base text-gray-700">{{ application.teacher?.email || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Área</label>
                        <p class="text-base text-gray-700">{{ application.teacher?.department || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Subárea</label>
                        <p class="text-base text-gray-700">{{ application.teacher?.sub_area || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Institución</label>
                        <p class="text-base text-gray-700">{{ application.campus || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                        <p class="text-base text-gray-700">{{ application.teacher?.state || 'N/A' }}</p>
                    </div>
                    <div v-if="application.announcement" class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Convocatoria</label>
                        <p class="text-base text-gray-700">{{ application.announcement.name }}</p>
                    </div>
                </div>
            </div>

            <!-- Documentación -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1B396A">
                        <path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520Z"/>
                    </svg>
                    Documentación
                </h2>

                <div class="grid grid-cols-1 gap-4">
                    <div v-if="!application.documents || application.documents.length === 0"
                        class="text-gray-500 italic text-center py-8 bg-gray-50 rounded-lg">
                        No hay documentos cargados.
                    </div>

                    <div v-for="doc in application.documents" :key="doc.id"
                        class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition"
                        :class="{ 'bg-blue-50/30': documentsState[doc.id]?.showPreview }">

                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <!-- Nombre del documento -->
                            <div class="flex items-center gap-4 w-full sm:w-auto min-w-0">
                                <div class="text-gray-700 flex-shrink-0 bg-gray-100 p-2 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="getFileIcon()" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="font-medium text-gray-900 truncate block" :title="doc.name">{{ doc.name }}</span>
                                    <span class="text-xs text-gray-500 uppercase">{{ doc.file_type }}</span>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="flex items-center gap-3 w-full sm:w-auto justify-end flex-shrink-0">
                                <button
                                    v-if="doc.file_type === 'pdf'"
                                    @click="togglePreview(doc.id)"
                                    class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500"
                                    :class="documentsState[doc.id]?.showPreview
                                        ? 'bg-blue-100 text-blue-700 hover:bg-blue-200'
                                        : 'bg-white text-blue-600 border border-blue-200 hover:bg-blue-50'"
                                >
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor">
                                        <path :d="documentsState[doc.id]?.showPreview ? mdiEyeOff : mdiEye"/>
                                    </svg>
                                    {{ documentsState[doc.id]?.showPreview ? 'Ocultar' : 'Ver' }}
                                </button>

                                <a :href="route('catalog.documents.downloadDocente', doc.id)"
                                    class="flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-1.5 rounded-md transition text-sm font-medium border border-gray-200 bg-white"
                                    title="Descargar archivo"
                                >
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="mdiDownload"/></svg>
                                    Descargar
                                </a>
                            </div>
                        </div>

                        <!-- Inline Preview -->
                        <div v-if="documentsState[doc.id]?.showPreview" class="mt-4 pt-4 border-t border-gray-200 w-full animate-fadeIn">
                            <div class="w-full h-[600px] bg-gray-100 rounded-lg overflow-hidden border border-gray-300 relative">
                                <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                                    Cargando vista previa...
                                </div>
                                <iframe :src="getPreviewUrl(doc)" class="w-full h-full relative z-10" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>

<style scoped>
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
