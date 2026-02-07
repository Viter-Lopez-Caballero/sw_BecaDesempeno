<template>
    <Head title="Detalles de Solicitud" />

    <AdminLayout>
        <div class="p-6 space-y-6 max-w-7xl mx-auto">
            <!-- Header with Breadcrumbs -->
            <div class="flex items-center justify-between">
                <div>
                     <nav class="flex text-sm text-gray-500 mb-1">
                        <Link :href="route('admin.solicitudes.index')" class="hover:text-[#1B396A]">Solicitudes</Link>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-900 font-medium">Detalles</span>
                    </nav>
                    <h1 class="text-2xl font-bold text-gray-900">Detalles de Solicitud</h1>
                </div>
            </div>

            <!-- Content Card -->
            <div class="space-y-6">
                 <!-- Main Info Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
                     <div class="absolute top-6 right-6">
                         <!-- Badge Estado -->
                         <span 
                            class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                            :class="{
                                'bg-yellow-100 text-yellow-800': solicitud.status === 'pending',
                                'bg-green-100 text-green-800': solicitud.status === 'approved',
                                'bg-red-100 text-red-800': solicitud.status === 'rejected',
                            }"
                        >
                            {{ getStatusLabel(solicitud.status) }}
                        </span>
                     </div>
                     <h2 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Información General</h2>
                     
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Profesor</h3>
                            <p class="text-md font-medium text-gray-900">{{ getUser()?.name || 'Completar datos' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Campus</h3>
                            <p class="text-md font-medium text-gray-900">{{ getUser()?.institucion?.nombre || 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-1">Convocatoria</h3>
                            <p class="text-md font-medium text-gray-900">{{ getConvocatoria()?.nombre || 'General' }}</p>
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
                            <p class="text-md font-medium text-gray-900">{{ formatDate(solicitud.created_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Documentation -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Documentación</h2>
                    <div class="space-y-3">
                        <div 
                            v-for="doc in getDocuments()" 
                            :key="doc.id"
                            class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg hover:shadow-sm transition-shadow"
                        >
                            <div class="flex items-center gap-3">
                                 <div class="bg-white p-2 rounded border border-gray-200">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                 </div>
                                 <span class="text-sm font-medium text-gray-700">{{ doc.name || doc.nombre }}</span>
                            </div>
                            
                             <div class="flex items-center gap-2">
                                <button 
                                    v-if="doc.file_type === 'pdf'" 
                                    @click="openPdfModal(doc)" 
                                    class="flex items-center gap-1 text-[#1B396A] hover:bg-blue-50 px-3 py-1.5 rounded-md transition text-sm font-medium"
                                >
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="mdiEye"/></svg>
                                    Ver
                                </button>
                                <a 
                                    :href="route('admin.documents.download', doc.id || doc.data?.id)" 
                                    class="flex items-center gap-1 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-md transition text-sm font-medium"
                                    title="Descargar"
                                >
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path :d="mdiDownload"/></svg>
                                    Descargar
                                </a>
                            </div>
                        </div>
                        <div v-if="getDocuments().length === 0" class="text-sm text-gray-500 italic">
                            No hay documentos adjuntos.
                        </div>
                    </div>
                </div>

                <!-- Evaluadores -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Evaluadores Asignados</h2>
                    </div>
                    <table class="w-full text-left">
                        <thead class="bg-[#1B396A] text-white">
                            <tr>
                                <th class="px-6 py-3 text-xs uppercase font-semibold w-12">#</th>
                                <th class="px-6 py-3 text-xs uppercase font-semibold">Nombre</th>
                                <th class="px-6 py-3 text-xs uppercase font-semibold">Comentarios</th>
                                <th class="px-6 py-3 text-xs uppercase font-semibold text-right">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(ev, index) in solicitud.evaluaciones" :key="ev.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ ev.evaluador?.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 italic">
                                    {{ ev.comentario || 'Sin comentarios' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span 
                                        class="px-2.5 py-1 rounded-full text-xs font-bold text-white shadow-sm"
                                        :class="{
                                            'bg-gray-400': ev.status === 'pending',
                                            'bg-green-500': ev.status === 'approved',
                                            'bg-red-500': ev.status === 'rejected',
                                        }"
                                    >
                                        {{ getStatusLabel(ev.status) }}
                                    </span>
                                </td>
                            </tr>
                             <tr v-if="!solicitud.evaluaciones || solicitud.evaluaciones.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 italic">
                                    No se han asignado evaluadores aún.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Admin Verdict Buttons -->
                 <div class="flex justify-between items-center pt-4" v-if="solicitud.status === 'pending'">
                    <div class="w-full flex gap-4">
                        <button 
                            @click="approveRequest"
                            class="flex-1 bg-[#1B396A] text-white py-3 rounded-lg font-bold uppercase tracking-wider hover:bg-[#152d47] transition shadow-md"
                            :disabled="processing"
                        >
                            Aprobar
                        </button>
                        
                        <button 
                             @click="rejectRequest"
                            class="flex-1 bg-[#d32f2f] text-white py-3 rounded-lg font-bold uppercase tracking-wider hover:bg-[#b71c1c] transition shadow-md"
                            :disabled="processing"
                        >
                            Rechazar
                        </button>
                        
                        <Link
                            :href="route('admin.solicitudes.index')"
                            class="px-6 py-3 bg-white border border-gray-300 rounded-lg font-bold uppercase tracking-wider text-gray-700 hover:bg-gray-50 transition shadow-sm"
                        >
                            Cancelar
                        </Link>
                    </div>
                </div>
                 <!-- If already decided, show status -->
                 <div v-else class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200">
                    <p class="text-gray-600 font-medium">Esta solicitud ya ha sido completada con estado: <span class="font-bold">{{ getStatusLabel(solicitud.status) }}</span></p>
                    </div>
            </div>
        </div>

            <!-- Reject Modal -->
            <RejectModal
                :show="rejectModalOpen"
                :solicitudId="solicitud.id"
                @close="rejectModalOpen = false"
            />

             <!-- PDF Preview Modal -->
            <Teleport to="body">
                <Transition name="modal">
                    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[90vh] flex flex-col">
                            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">{{ currentPdfTitle }}</h2>
                                <button @click="closeModal" class="text-gray-400 hover:text-gray-800 transition-colors cursor-pointer">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex-1 overflow-hidden bg-gray-100">
                                <iframe :src="currentPdfUrl" class="w-full h-full" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </Transition>
            </Teleport>

    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import RejectModal from './RejectModal.vue';
import { mdiEye, mdiDownload } from '@mdi/js';

const props = defineProps({
    solicitud: Object,
});

const rejectModalOpen = ref(false);
const processing = ref(false);
const showModal = ref(false);
const currentPdfUrl = ref('');
const currentPdfTitle = ref('');

const unwrap = (obj) => {
    return obj && obj.data ? obj.data : obj;
};

const getUser = () => unwrap(props.solicitud.user);
const getConvocatoria = () => unwrap(props.solicitud.convocatoria);
const getDocuments = () => {
    const docs = props.solicitud.documentos;
    const list = Array.isArray(docs) ? docs : (docs?.data || []);
    return list.map(d => unwrap(d));
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Pendiente',
        approved: 'Aprobado',
        rejected: 'Rechazado',
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

const approveRequest = () => {
    if (!confirm('¿Estás seguro de APROBAR esta solicitud final?')) return;
    
    processing.value = true;
    router.post(route('admin.solicitudes.verdict', props.solicitud.id), {
        status: 'approved'
    }, {
        onFinish: () => processing.value = false
    });
};

const rejectRequest = () => {
    rejectModalOpen.value = true;
};

const openPdfModal = (doc) => {
    // Determine stream URL
    const id = doc.id || doc.data?.id;
    if (!id) return;
    currentPdfUrl.value = route('admin.documents.stream', id);
    currentPdfTitle.value = doc.name || doc.nombre || doc.data?.name;
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

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}
</style>
