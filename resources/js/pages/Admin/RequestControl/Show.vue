<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import Pagination from '@/Shared/Pagination.vue';
import { mdiFileDocumentMultiple } from '@mdi/js';

const props = defineProps({
    institution: Object,
    solicitudes: Object,
});
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Detalles de Campus" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Solicitudes por Campus</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <Link :href="route('solicitudes.index')" class="text-gray-700 font-medium hover:text-[#1B396A]">Control de Solicitudes</Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">{{ institution.nombre }}</span>
                    </div>
                </div>
                 <Link :href="route('solicitudes.index')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </Link>
            </div>

            <!-- Institution Summary -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Campus</p>
                        <p class="text-2xl font-bold text-[#1B396A]">{{ institution.nombre }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Estado</p>
                        <p class="text-lg font-semibold text-gray-800">{{ institution.estado?.nombre }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Table -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                     <table class="w-full text-sm text-left">
                        <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-4 tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Docente</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Convocatoria</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="solicitud in solicitudes.data" :key="solicitud.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ solicitud.id }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ solicitud.user?.name }}</td>
                                <td class="px-6 py-4 text-gray-800">{{ solicitud.convocatoria?.name }}</td>
                                <td class="px-6 py-4 text-gray-800">{{ new Date(solicitud.created_at).toLocaleDateString() }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full"
                                           :class="solicitud.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ solicitud.status === 'approved' ? 'Aprobada' : 'Rechazada' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="solicitudes.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No hay solicitudes aprobadas o rechazadas en este campus.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                 <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                     <Pagination :links="solicitudes.meta.links" :total="solicitudes.meta.total" :from="solicitudes.meta.from" :to="solicitudes.meta.to" />
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
