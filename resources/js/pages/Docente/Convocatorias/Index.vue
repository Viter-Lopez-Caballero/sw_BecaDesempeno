<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DocenteLayout from '@/layouts/DocenteLayout.vue';
import { mdiBullhorn } from '@mdi/js';

const props = defineProps({
    convocatorias: Object,
    has_active_solicitud: Boolean,
});
</script>

<template>
    <DocenteLayout>
        <Head title="Convocatorias" />
        <div class="space-y-6">
             <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Convocatorias Disponibles</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiBullhorn"/>
                        </svg>
                         <span class="text-gray-900 font-semibold">Convocatorias</span>
                    </div>
                </div>
            </div>

            <div v-if="convocatorias.data && convocatorias.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="convocatoria in convocatorias.data" :key="convocatoria.id" class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow flex flex-col">
                    <div class="bg-gray-50 p-4 border-b border-gray-100 flex justify-between items-center">
                         <span class="text-xs font-bold uppercase tracking-wider text-[#1B396A] bg-blue-100 px-2 py-1 rounded-md">
                            {{ convocatoria.nombre }}
                        </span>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-200">
                            Activa
                        </span>
                    </div>
                    <div class="p-6 flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ convocatoria.nombre }}</h3>
                         <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ convocatoria.descripcion }}</p>
                         
                         <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                            <span class="font-semibold">Cierre:</span>
                            <span>{{ convocatoria.fecha_fin ? new Date(convocatoria.fecha_fin).toLocaleDateString() : 'Por definir' }}</span>
                         </div>
                    </div>
                    <div class="p-6 pt-0 mt-auto">
                        <Link v-if="!has_active_solicitud" :href="route('docente.convocatorias.solicitar', convocatoria.id)" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-[#1B396A] text-white rounded-lg font-semibold hover:bg-[#0f2347] transition">
                            Solicitar Beca
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                            </svg>
                        </Link>
                        <div v-else class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg font-semibold cursor-not-allowed border border-gray-300">
                             Solicitud en curso
                        </div>
                    </div>
                </div>
            </div>
             <div v-else class="col-span-full py-12 flex flex-col items-center justify-center text-gray-500 bg-white rounded-lg border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#9CA3AF" class="mb-4">
                    <path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                </svg>
                <p class="text-lg font-medium">No hay convocatorias activas en este momento.</p>
                <p class="text-sm">Mantente al pendiente para futuras oportunidades.</p>
            </div>
        </div>
    </DocenteLayout>
</template>
