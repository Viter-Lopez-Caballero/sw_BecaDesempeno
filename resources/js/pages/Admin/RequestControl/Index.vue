<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import Pagination from '@/Shared/Pagination.vue';
import { ref } from 'vue';
import { mdiFileDocumentMultiple } from '@mdi/js';

const props = defineProps({
    institutions: Object,
});

const viewDetails = (id) => {
    router.get(route('solicitudes.show', id));
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Control de Solicitudes" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Control de Solicitudes</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiFileDocumentMultiple"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Módulos</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Resumen por Campus</span>
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
                                <th scope="col" class="px-6 py-4 tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Campus</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Aprobadas</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Rechazadas</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="institution in institutions.data" :key="institution.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ institution.id }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ institution.estado }}</td>
                                <td class="px-6 py-4 text-gray-800 font-semibold">{{ institution.nombre }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-800">
                                        {{ institution.approved_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-800">
                                        {{ institution.rejected_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <button @click="viewDetails(institution.id)" class="text-[#1B396A] hover:text-[#0f2347] font-medium flex items-center gap-1 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver Detalles
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="institutions.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No se encontraron registros de actividad.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                     <Pagination :links="institutions.meta.links" :total="institutions.meta.total" :from="institutions.meta.from" :to="institutions.meta.to" />
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
