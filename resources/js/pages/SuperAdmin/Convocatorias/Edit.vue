<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiBullhorn } from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    convocatoria: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    id: props.convocatoria.id,
    nombre: props.convocatoria.nombre,
    descripcion: props.convocatoria.descripcion,
    anio: props.convocatoria.anio,
    estado: props.convocatoria.estado,
});

const submit = () => {
    form.put(route(`${props.routeName}update`, { convocatoria: form.id }));
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
                        <Link :href="route(`${routeName}index`)" class="flex items-center gap-2 hover:underline">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                <path :d="mdiBullhorn"/>
                            </svg>
                            <span class="text-gray-700 font-medium">Convocatorias</span>
                        </Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Editar Convocatoria</span>
                    </div>
                </div>
                <Link :href="route(`${routeName}index`)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Nombre: <span class="text-red-500">*</span></label>
                            <input v-model="form.nombre" type="text" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Nombre de la convocatoria" />
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, introduce el nombre de la convocatoria</span>
                            </div>
                            <p v-if="form.errors.nombre" class="mt-1 text-sm text-red-600">{{ form.errors.nombre }}</p>
                        </div>
                        
                        <!-- Año -->
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Año: <span class="text-red-500">*</span></label>
                            <input v-model="form.anio" type="number" min="2000" max="2100" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Año" />
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, introduce el año de la convocatoria</span>
                            </div>
                            <p v-if="form.errors.anio" class="mt-1 text-sm text-red-600">{{ form.errors.anio }}</p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Estado: <span class="text-red-500">*</span></label>
                            <select v-model="form.estado" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]">
                                <option value="pendiente">Pendiente</option>
                                <option value="activa">Activa</option>
                                <option value="cerrada">Cerrada</option>
                            </select>
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, selecciona el estado de la convocatoria</span>
                            </div>
                            <p v-if="form.errors.estado" class="mt-1 text-sm text-red-600">{{ form.errors.estado }}</p>
                        </div>

                        <!-- Descripción -->
                        <div class="col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Descripción:</label>
                            <textarea v-model="form.descripcion" rows="4" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Descripción de la convocatoria"></textarea>
                            <div class="flex items-center justify-between mt-1">
                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, introduce la descripción de la convocatoria</span>
                                </div>
                                <span class="text-gray-400 text-sm">{{ form.descripcion?.length || 0 }}/500</span>
                            </div>
                            <p v-if="form.errors.descripcion" class="mt-1 text-sm text-red-600">{{ form.errors.descripcion }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route(`${routeName}index`)" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </Link>
                        <button :disabled="form.processing" type="submit" class="px-6 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl disabled:opacity-75 flex items-center gap-2 font-medium">
                            <span v-if="!form.processing">Actualizar</span>
                            <span v-else>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
