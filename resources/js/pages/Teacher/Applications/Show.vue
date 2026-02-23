<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { 
    mdiFileDocumentMultiple, 
    mdiEye, 
    mdiEyeOff,
    mdiDownload, 
    mdiCheckCircle, 
    mdiCloseCircle, 
    mdiClockOutline,
    mdiChevronRight
} from '@mdi/js';
import { ref } from 'vue';

const props = defineProps({
    application: Object,
});

// State to track preview visibility for each document
const documentsState = ref({});

const togglePreview = (docId) => {
    if (!documentsState.value[docId]) {
        documentsState.value[docId] = { showPreview: false };
    }
    documentsState.value[docId].showPreview = !documentsState.value[docId].showPreview;
};

const getPreviewUrl = (doc) => {
    return route('teacher.documents.stream', doc.id);
};

const getFileIcon = (type) => {
    return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'; 
};
</script>

<template>
    <TeacherLayout>
        <Head title="Detalles de Solicitud" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">Detalles de la Solicitud</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <Link :href="route('teacher.dashboard')" class="text-gray-700 font-medium hover:text-[#1B396A]">Inicio</Link>
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-gray-400" fill="currentColor">
                            <path :d="mdiChevronRight"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Detalles de Solicitud</span>
                    </div>
                </div>
                 <Link :href="route('teacher.dashboard')" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium bg-white cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>
            
            <!-- Status Banner -->
            <div 
                class="rounded-lg p-4 flex items-center gap-4 shadow-sm border"
                :class="{
                    'bg-green-50 border-green-200': application.status === 'approved',
                    'bg-gray-50 border-gray-200': application.status === 'rejected',
                    'bg-yellow-50 border-yellow-200': application.status === 'pending'
                }"
            >
                <div 
                    class="p-2 rounded-full hidden sm:block"
                    :class="{
                        'bg-green-100 text-green-600': application.status === 'approved',
                        'bg-gray-100 text-gray-600': application.status === 'rejected',
                        'bg-yellow-100 text-yellow-600': application.status === 'pending'
                    }"
                >
                    <svg v-if="application.status === 'approved'" viewBox="0 0 24 24" class="w-8 h-8" style="fill: currentColor"><path :d="mdiCheckCircle"/></svg>
                    <!-- Icon removed for rejected status as requested -->
                    <svg v-else-if="application.status === 'pending'" viewBox="0 0 24 24" class="w-8 h-8" style="fill: currentColor"><path :d="mdiClockOutline"/></svg>
                </div>
                <div>
                    <h3 
                        class="text-lg font-bold"
                        :class="{
                            'text-green-800': application.status === 'approved',
                            'text-gray-800': application.status === 'rejected',
                            'text-yellow-800': application.status === 'pending'
                        }"
                    >
                        {{ 
                            application.status === 'approved' ? 'Solicitud Aprobada' : 
                            application.status === 'rejected' ? 'Solicitud No Aprobada' : 'Solicitud Pendiente de Revisión' 
                        }}
                    </h3>
                    <p class="text-sm text-gray-600" v-if="application.status === 'pending'">
                        Tu solicitud está siendo revisada por el comité. Te notificaremos cuando haya una actualización.
                    </p>
                     <div v-else class="text-sm">
                         <p class="text-gray-600 mb-1">Evaluada el {{ new Date(application.updated_at).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                         <div v-if="application.status === 'rejected' && application.admin_comment" class="bg-gray-50 p-3 rounded-md border border-gray-100 mt-2">
                            <span class="font-bold text-gray-800 block mb-1">Motivo:</span>
                            <p class="text-gray-700">"{{ application.admin_comment }}"</p>
                         </div>
                         
                         <!-- Download Acceptance Letter -->
                         <div v-if="application.status === 'approved'" class="mt-4">
                            <a 
                                :href="route('teacher.documents.downloadAcceptance', application.id)" 
                                target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-sm text-sm"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Descargar Carta de Aceptación
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200 p-8">
                <!-- Info Header -->
                <div class="flex justify-between items-start mb-8 relative">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Información de la Solicitud</h3>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-y-8 gap-x-12 mb-10">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Profesor</p>
                        <p class="text-gray-900 font-medium text-base">{{ application.teacher?.name || application.user?.name || 'Usuario' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Área Prioritaria</p>
                        <p class="text-gray-900 font-medium text-base">{{ application.teacher?.department || application.user?.priority_area?.name || 'No asignado' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Institución</p>
                        <p class="text-gray-900 text-base leading-snug">{{ application.campus || application.user?.institution?.name || 'No registrado' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Tipo de Beca</p>
                        <p class="text-gray-900 text-base">{{ application.announcement?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Tipo de Plaza</p>
                        <p class="text-gray-900 text-base">{{ application.position_type || 'No especificado' }}</p>
                    </div>
                    <div>
                         <p class="text-sm font-medium text-gray-600 mb-1">Fecha de Solicitud</p>
                         <p class="text-gray-900 text-base">{{ new Date(application.created_at).toLocaleDateString() }}</p>
                    </div>
                </div>

                <!-- Documentation -->
                <div class="border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Documentación</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div v-if="!application.documents || application.documents.length === 0" class="text-gray-500 italic">
                            No hay documentos disponibles.
                        </div>

                        <div v-for="doc in application.documents" :key="doc.id" 
                            class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition"
                            :class="{ 'bg-blue-50/30': documentsState[doc.id]?.showPreview }">
                            
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4 w-full sm:w-auto min-w-0">
                                    <div class="text-gray-700 flex-shrink-0 bg-gray-100 p-2 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="getFileIcon(doc.file_type)" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-medium text-gray-900 truncate block" :title="doc.name">{{ doc.name }}</span>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center gap-3 w-full sm:w-auto justify-end flex-shrink-0">
                                    <button 
                                        v-if="doc.file_type === 'pdf'" 
                                        @click="togglePreview(doc.id)" 
                                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 cursor-pointer"
                                        :class="documentsState[doc.id]?.showPreview 
                                            ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' 
                                            : 'bg-white text-blue-600 border border-blue-200 hover:bg-blue-50'"
                                    >
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="documentsState[doc.id]?.showPreview ? mdiEyeOff : mdiEye"/></svg>
                                        {{ documentsState[doc.id]?.showPreview ? 'Ocultar' : 'Ver' }}
                                    </button>
                                    
                                    <a :href="route('teacher.documents.download', doc.id)" 
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
        </div>
    </TeacherLayout>
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
