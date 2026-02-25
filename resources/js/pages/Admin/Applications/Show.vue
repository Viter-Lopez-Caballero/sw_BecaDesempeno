<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { usePage, router, Head, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { alertaPregunta, alertaExito } from '@/utils/alerts.js';
import RejectModal from './RejectModal.vue';
import { 
    mdiEye, 
    mdiEyeOff, 
    mdiDownload, 
    mdiFileDocumentMultiple,
    mdiCheckCircle,
    mdiCloseCircle,
    mdiClockOutline,
    mdiChevronRight
} from '@mdi/js';

const props = defineProps({
    application: Object,
});

const page = usePage();
const rejectModalOpen = ref(false);
const processing = ref(false);
const documentsState = ref({});
const expandedComments = ref({});

const toggleComment = (id) => {
    expandedComments.value[id] = !expandedComments.value[id];
};

// Watch for flash messages
watch(() => page.props.flash?.success, (successMessage) => {
    if (successMessage) {
        alertaExito('¡Éxito!', successMessage);
    }
}, { immediate: true });

const unwrap = (obj) => {
    return obj && obj.data ? obj.data : obj;
};

const getUser = () => unwrap(props.application.user);
const getConvocatoria = () => unwrap(props.application.announcement);
const getDocuments = () => {
    const docs = props.application.documents;
    const list = Array.isArray(docs) ? docs : (docs?.data || []);
    return list.map(d => unwrap(d));
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Pendiente',
        approved: 'Aprobado',
        rejected: 'Rechazado',
        expired: 'Expirada',
    };
    return labels[status] || status;
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
};

const approveRequest = async () => {
    const confirmed = await alertaPregunta(
        '¿Aprobar solicitud?',
        'Esta acción finalizará el proceso y notificará al docente.'
    );
    if (confirmed) {
        processing.value = true;
        router.post(route('admin.applications.verdict', props.application.id), {
            status: 'approved'
        }, {
            preserveScroll: true,
            onSuccess: () => alertaExito('¡Aprobada!', 'La solicitud fue aprobada correctamente.'),
            onFinish: () => processing.value = false
        });
    }
};

const rejectRequest = () => {
    rejectModalOpen.value = true;
};

const togglePreview = (docId) => {
    // Handle both direct ID and data wrapper
    const id = docId;
    if (!documentsState.value[id]) {
        documentsState.value[id] = { showPreview: false };
    }
    documentsState.value[id].showPreview = !documentsState.value[id].showPreview;
};

const getPreviewUrl = (doc) => {
    const id = doc.id || doc.data?.id;
    return route('catalog.admin.documents.stream', id);
};

const getDownloadUrl = (doc) => {
    const id = doc.id || doc.data?.id;
    return route('catalog.admin.documents.download', id);
};

const getFileIcon = (type) => {
    return 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'; 
};
</script>

<template>
    <Head title="Detalles de Solicitud" />

    <AdminLayout>
        <div class="p-6 space-y-6 max-w-7xl mx-auto">
            <!-- Header with Breadcrumbs -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-bold text-gray-900">Detalles de Solicitud</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <Link :href="route('admin.applications.index')" class="text-gray-700 font-medium hover:text-[#1B396A]">Solicitudes</Link>
                         <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Detalles</span>
                    </div>
                </div>
                 <Link :href="route('admin.applications.index')" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium bg-white cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <!-- Status Banner (Minimalist Redesign) -->
            <transition enter-active-class="transition duration-300 ease-out" enter-from-class="transform -translate-y-4 opacity-0" enter-to-class="transform translate-y-0 opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div 
                    v-if="application.status === 'approved' || application.status === 'rejected'"
                    class="relative px-5 py-4 rounded-lg bg-white shadow-sm border border-gray-100 border-l-4 mb-6 flex items-center gap-4 group"
                    :class="{
                        'border-l-green-500': application.status === 'approved',
                        'border-l-red-500': application.status === 'rejected'
                    }"
                >
                    <div 
                        class="flex-shrink-0"
                        :class="{
                            'text-green-500': application.status === 'approved',
                            'text-red-500': application.status === 'rejected'
                        }"
                    >
                        <svg v-if="application.status === 'approved'" viewBox="0 0 24 24" class="w-8 h-8" fill="currentColor"><path :d="mdiCheckCircle"/></svg>
                        <svg v-else viewBox="0 0 24 24" class="w-8 h-8" fill="currentColor"><path :d="mdiCloseCircle"/></svg>
                    </div>
                    <div>
                        <h3 
                            class="text-lg font-bold text-gray-900"
                        >
                            Solicitud {{ application.status === 'approved' ? 'Aprobada' : 'Rechazada' }}
                        </h3>
                        <p class="text-sm text-gray-600 font-medium">
                            Actualizada el {{ formatDate(application.updated_at) }}
                        </p>
                    </div>
                </div>
            </transition>

            <!-- Comentario de Rechazo (Minimalist Redesign) -->
            <div v-if="application.status === 'rejected' && application.admin_comment" class="relative px-6 py-5 rounded-lg bg-white shadow-sm border border-gray-100 border-l-4 border-l-red-500 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 text-red-500 mt-1">
                        <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2m0 14H6l-2 2V4h16v12z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-red-400 mb-1 block">Motivo del Rechazo</span>
                        <p class="text-gray-800 font-bold leading-relaxed">"{{ application.admin_comment }}"</p>
                    </div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="space-y-6">
                 <!-- Main Info Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
                     <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-100 pb-4 mb-6 gap-2 md:gap-4">
                        <h2 class="text-lg font-bold text-gray-900 md:order-first">Información General</h2>
                     </div>
                     
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Profesor</h3>
                            <p class="text-md font-medium text-gray-900">{{ getUser()?.name || 'Completar datos' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Institución</h3>
                            <p class="text-md font-medium text-gray-900">{{ application.campus || getUser()?.institucion?.name || 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Convocatoria</h3>
                            <p class="text-md font-medium text-gray-900">{{ getConvocatoria()?.name || 'General' }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Área de Procedencia</h3>
                            <p class="text-md font-medium text-gray-900">{{ getUser()?.priority_area?.name || 'No registrada' }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Subárea</h3>
                            <p class="text-md font-medium text-gray-900">{{ getUser()?.sub_area?.name || 'No registrada' }}</p>
                        </div>

                         <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Fecha de Solicitud</h3>
                            <p class="text-md font-medium text-gray-900">{{ formatDate(application.created_at) }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Tipo de Plaza</h3>
                            <p class="text-md font-medium text-gray-900">{{ application.position_full_type || 'No especificado' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Documentation -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Documentación</h2>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div v-if="getDocuments().length === 0" class="text-gray-500 italic text-center py-4 bg-gray-50 rounded-lg">
                            No hay documentos adjuntos.
                        </div>

                        <div v-for="doc in getDocuments()" :key="doc.id" 
                            class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition"
                            :class="{ 'bg-blue-50/30': documentsState[doc.id]?.showPreview }"
                        >
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4 w-full sm:w-auto min-w-0">
                                    <div class="text-gray-700 flex-shrink-0 bg-gray-100 p-2 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="getFileIcon(doc.file_type)" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-medium text-gray-900 truncate block" :title="doc.name || doc.nombre">{{ doc.name || doc.nombre }}</span>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center gap-3 w-full sm:w-auto justify-end flex-shrink-0">
                                    <button 
                                        v-if="doc.file_type === 'pdf'" 
                                        @click="togglePreview(doc.id)" 
                                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500"
                                        :class="documentsState[doc.id]?.showPreview 
                                            ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' 
                                            : 'bg-white text-blue-600 border border-blue-200 hover:bg-blue-50'"
                                    >
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="documentsState[doc.id]?.showPreview ? mdiEyeOff : mdiEye"/></svg>
                                        {{ documentsState[doc.id]?.showPreview ? 'Ocultar' : 'Ver' }}
                                    </button>
                                    
                                    <a :href="getDownloadUrl(doc)" 
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

                <!-- Evaluadores -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Evaluadores Asignados</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-[#1B396A] text-white">
                                <tr>
                                <th class="px-6 py-3 text-xs uppercase font-semibold w-12 whitespace-nowrap">#</th>
                                    <th class="px-6 py-3 text-xs uppercase font-semibold whitespace-nowrap">Nombre</th>
                                    <th class="px-6 py-3 text-xs uppercase font-semibold whitespace-nowrap">Comentarios</th>
                                    <th class="px-6 py-3 text-xs uppercase font-semibold text-right whitespace-nowrap">Estado</th>
                                    <th class="px-6 py-3 text-xs uppercase font-semibold text-center whitespace-nowrap">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(ev, index) in application.evaluations" :key="ev.id">
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ ev.evaluator?.name }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-normal break-words max-w-xs">
                                        <div v-if="ev.comment" class="text-gray-800">
                                            <span v-if="!expandedComments[ev.id] && ev.comment.length > 100">
                                                {{ ev.comment.substring(0, 100) }}...
                                                <br>
                                                <button @click="toggleComment(ev.id)" class="text-[#1B396A] hover:text-[#152d47] font-semibold text-xs mt-1 cursor-pointer inline-flex items-center gap-1 focus:outline-none">
                                                    Ver más
                                                </button>
                                            </span>
                                            <span v-else>
                                                {{ ev.comment }}
                                                <br>
                                                <button v-if="ev.comment.length > 100" @click="toggleComment(ev.id)" class="text-[#1B396A] hover:text-[#152d47] font-semibold text-xs mt-1 cursor-pointer inline-flex items-center gap-1 focus:outline-none">
                                                    Ver menos
                                                </button>
                                            </span>
                                        </div>
                                        <span v-else class="text-gray-400 italic">Sin comentarios</span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <span 
                                            class="px-2.5 py-1 rounded-full text-xs font-bold text-white shadow-sm"
                                            :class="{
                                                'bg-gray-400': ev.status === 'pending',
                                                'bg-green-500': ev.status === 'approved',
                                                'bg-red-500': ev.status === 'rejected',
                                                'bg-gray-700': ev.status === 'expired',
                                            }"
                                        >
                                            {{ getStatusLabel(ev.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <Link v-if="ev.status !== 'pending' && ev.status !== 'expired'" :href="route('admin.applications.evaluation.show', { application: application.id, evaluation: ev.id })" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-semibold transition"
                                            title="Ver Respuestas de la Rúbrica"
                                        >
                                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="mdiEye"/></svg>
                                            Respuestas
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!application.evaluations || application.evaluations.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                        No se han asignado evaluadores aún.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Admin Verdict Buttons -->
                 <div class="flex justify-between items-center pt-4" v-if="application.status === 'pending'">
                    <div class="w-full flex gap-4">
                        <button 
                            @click="approveRequest"
                            class="flex-1 bg-[#1B396A] text-white py-2.5 rounded-lg text-sm font-semibold uppercase tracking-wider hover:bg-[#152d47] transition shadow-md cursor-pointer"
                            :disabled="processing"
                        >
                            Aprobar
                        </button>
                        
                        <button 
                             @click="rejectRequest"
                            class="flex-1 bg-[#d32f2f] text-white py-2.5 rounded-lg text-sm font-semibold uppercase tracking-wider hover:bg-[#b71c1c] transition shadow-md cursor-pointer"
                            :disabled="processing"
                        >
                            Rechazar
                        </button>
                        
                        <Link
                            :href="route('admin.applications.index')"
                            class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-50 transition shadow-sm text-center cursor-pointer"
                        >
                            Cancelar
                        </Link>
                    </div>
                </div>
                <!-- If already decided, show status (Optional: removed to avoid duplication with top banner, or kept simple) -->
                 <div v-else class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200">
                    <p class="text-gray-600 font-medium">Esta solicitud ya ha sido completada con estado: 
                        <span class="font-bold" :class="{'text-red-600': application.status === 'rejected', 'text-green-600': application.status === 'approved'}">
                            {{ getStatusLabel(application.status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

            <!-- Reject Modal -->
            <RejectModal
                :show="rejectModalOpen"
                :applicationId="application.id"
                @close="rejectModalOpen = false"
            />
    </AdminLayout>
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
