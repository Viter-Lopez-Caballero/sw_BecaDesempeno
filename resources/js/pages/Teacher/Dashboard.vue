<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import Pagination from '@/Shared/Pagination.vue';
import { mdiHome } from '@mdi/js';
import { ref, watch, onMounted } from 'vue';
import { alertaExito } from '@/utils/alerts';

const props = defineProps({
    applications: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(route('teacher.dashboard'), { search: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

onMounted(() => {
    const page = usePage();
    if (page.props.flash?.success) {
        alertaExito('¡Éxito!', page.props.flash.success);
    }
});
</script>

<template>
    <TeacherLayout>
        <Head title="Inicio" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mis Solicitudes</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiHome"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Inicio</span>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#374151">
                            <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-800">Filtro de Búsqueda</h2>
                    </div>
                </div>
                <div class="text-sm text-gray-500 mb-4">Buscar solicitudes</div>
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="relative w-full md:flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1B396A">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                        </div>
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Buscar por ID o convocatoria..."
                            class="pl-10 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition" 
                        />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left uppercase">
                        <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-4 tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Convocatoria</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Fecha de Postulación</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="(application, index) in applications.data" :key="application.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 leading-none">
                                    {{ (applications.meta?.from || 1) + index }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ application.announcement?.name }}</td>
                                <td class="px-6 py-4 text-gray-600 capitalize">
                                    {{ new Date(application.created_at).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full"
                                           :class="{
                                               'bg-green-100 text-green-800': application.status === 'approved',
                                               'bg-gray-100 text-gray-600': application.status === 'rejected',
                                               'bg-yellow-100 text-yellow-800': application.status === 'pending'
                                           }">
                                        {{ 
                                            application.status === 'approved' ? 'Aceptada' : 
                                            application.status === 'rejected' ? 'No Aprobada' : 'Pendiente' 
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <Link :href="route('teacher.applications.show', application.id)" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 transition text-xs font-medium uppercase gap-1 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Ver Detalles
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="applications.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No has realizado ninguna solicitud de beca aún.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                 <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50" v-if="applications.meta">
                     <Pagination :links="applications.meta.links" :total="applications.meta.total" :from="applications.meta.from" :to="applications.meta.to" />
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
