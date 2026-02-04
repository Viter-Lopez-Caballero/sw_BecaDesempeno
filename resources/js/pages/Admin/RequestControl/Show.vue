<template>
    <Head :title="`Detalles - ${institution.nombre}`" />

    <LayoutAuthenticated>
        <div class="p-6 space-y-6">
            <!-- Header -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h1 class="text-2xl font-bold text-gray-900">{{ institution.nombre }}</h1>
                <p class="text-gray-500">Estado: {{ institution.estado?.nombre }}</p>
                <Link :href="route('solicitudes.index')" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                    &larr; Volver a Instituciones
                </Link>
            </div>

            <!-- Solicitudes Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Solicitudes del Campus</h2>
                </div>
                
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 uppercase text-xs text-gray-500 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Folio</th>
                            <th class="px-6 py-4">Docente</th>
                            <th class="px-6 py-4">Convocatoria</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="solicitud in solicitudes.data" :key="solicitud.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ solicitud.id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ solicitud.user?.name }}
                                <div class="text-xs text-gray-500">{{ solicitud.user?.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ solicitud.convocatoria?.titulo || 'General' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ new Date(solicitud.created_at).toLocaleDateString() }}</td>
                            <td class="px-6 py-4">
                                <span 
                                    class="px-2 py-1 rounded-full text-xs font-bold uppercase"
                                    :class="{
                                        'bg-green-100 text-green-800': solicitud.status === 'approved',
                                        'bg-red-100 text-red-800': solicitud.status === 'rejected',
                                        'bg-yellow-100 text-yellow-800': solicitud.status === 'pending'
                                    }"
                                >
                                    {{ solicitud.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="solicitudes.data.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No hay solicitudes registradas para este campus.
                            </td>
                        </tr>
                    </tbody>
                </table>
                 <!-- Pagination -->
                <div class="border-t border-gray-100 bg-gray-50 px-6 py-4" v-if="solicitudes.meta?.links">
                    <Pagination :links="solicitudes.meta.links" />
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>

<script setup>
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Shared/Pagination.vue';

defineProps({
    institution: Object,
    solicitudes: Object,
});
</script>
