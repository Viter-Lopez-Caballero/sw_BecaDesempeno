<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiCalendar, mdiCalendarClock } from '@mdi/js';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    calendario: {
        type: Object,
        required: true,
    },
    convocatorias: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    id: props.calendario.id,
    convocatoria_id: props.calendario.convocatoria_id,
    publicacion_inicio: props.calendario.publicacion_inicio,
    publicacion_fin: props.calendario.publicacion_fin,
    registro_inicio: props.calendario.registro_inicio,
    registro_fin: props.calendario.registro_fin,
    evaluacion_inicio: props.calendario.evaluacion_inicio,
    evaluacion_fin: props.calendario.evaluacion_fin,
    resultados_inicio: props.calendario.resultados_inicio,
    resultados_fin: props.calendario.resultados_fin,
});

const submit = () => {
    form.put(route(`${props.routeName}update`, { calendario: form.id }));
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
                            <path :d="mdiCalendar"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Catálogos</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <Link :href="route(`${routeName}index`)" class="flex items-center gap-2 hover:underline">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                <path :d="mdiCalendarClock"/>
                            </svg>
                            <span class="text-gray-700 font-medium">Calendario</span>
                        </Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Editar Calendario</span>
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
                    <!-- Convocatoria -->
                    <div>
                        <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Convocatoria: <span class="text-red-500">*</span></label>
                        <VueSelect 
                            v-model="form.convocatoria_id" 
                            :options="convocatorias" 
                            :reduce="option => option.id"
                            label="nombre"
                            placeholder="Selecciona una convocatoria"
                            class="vue-select-custom"
                        >
                            <template #option="option">
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{ option.nombre }}</span>
                                    <span class="text-xs text-gray-500">Año {{ option.anio }}</span>
                                </div>
                            </template>
                        </VueSelect>
                        <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Selecciona la convocatoria a la que pertenece este calendario</span>
                        </div>
                        <p v-if="form.errors.convocatoria_id" class="mt-1 text-sm text-red-600">{{ form.errors.convocatoria_id }}</p>
                    </div>

                    <!-- Fase 1: Publicación -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="bg-[#1B396A] text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">1</span>
                            Fase de Publicación
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Inicio: <span class="text-red-500">*</span></label>
                                <input v-model="form.publicacion_inicio" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" />
                                <p v-if="form.errors.publicacion_inicio" class="mt-1 text-sm text-red-600">{{ form.errors.publicacion_inicio }}</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Fin: <span class="text-red-500">*</span></label>
                                <input v-model="form.publicacion_fin" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" />
                                <p v-if="form.errors.publicacion_fin" class="mt-1 text-sm text-red-600">{{ form.errors.publicacion_fin }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fase 2: Registro -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="bg-[#10A558] text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">2</span>
                            Fase de Registro
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Inicio: <span class="text-red-500">*</span></label>
                                <input v-model="form.registro_inicio" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" />
                                <p v-if="form.errors.registro_inicio" class="mt-1 text-sm text-red-600">{{ form.errors.registro_inicio }}</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Fin: <span class="text-red-500">*</span></label>
                                <input v-model="form.registro_fin" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" />
                                <p v-if="form.errors.registro_fin" class="mt-1 text-sm text-red-600">{{ form.errors.registro_fin }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fase 3: Evaluación -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="bg-[#E9C81F] text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">3</span>
                            Fase de Evaluación
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Inicio: <span class="text-red-500">*</span></label>
                                <input v-model="form.evaluacion_inicio" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" />
                                <p v-if="form.errors.evaluacion_inicio" class="mt-1 text-sm text-red-600">{{ form.errors.evaluacion_inicio }}</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Fin: <span class="text-red-500">*</span></label>
                                <input v-model="form.evaluacion_fin" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" />
                                <p v-if="form.errors.evaluacion_fin" class="mt-1 text-sm text-red-600">{{ form.errors.evaluacion_fin }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fase 4: Resultados -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="bg-[#1B396A] text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">4</span>
                            Fase de Resultados
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Inicio: <span class="text-red-500">*</span></label>
                                <input v-model="form.resultados_inicio" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" />
                                <p v-if="form.errors.resultados_inicio" class="mt-1 text-sm text-red-600">{{ form.errors.resultados_inicio }}</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Fin: <span class="text-red-500">*</span></label>
                                <input v-model="form.resultados_fin" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" />
                                <p v-if="form.errors.resultados_fin" class="mt-1 text-sm text-red-600">{{ form.errors.resultados_fin }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route(`${routeName}index`)" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </Link>
                        <button :disabled="form.processing" type="submit" class="px-6 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl disabled:opacity-75 flex items-center gap-2 font-medium">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Actualizando...' : 'Actualizar Calendario' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>

<style scoped>
:deep(.vue-select-custom .vs__dropdown-toggle) {
    background: #F3F4F6;
    border: none;
    border-bottom: 2px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.625rem 0.75rem;
    min-height: 42px;
}

:deep(.vue-select-custom .vs__selected) {
    color: #111827;
    font-weight: 500;
}

:deep(.vue-select-custom .vs__search::placeholder) {
    color: #9ca3af;
}

:deep(.vue-select-custom .vs__dropdown-menu) {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

:deep(.vue-select-custom .vs__dropdown-option) {
    padding: 0.75rem 1rem;
    color: #374151;
}

:deep(.vue-select-custom .vs__dropdown-option--highlight) {
    background: #1B396A;
    color: white;
}

:deep(.vue-select-custom .vs__open-indicator) {
    fill: #1B396A;
}

:deep(.vue-select-custom .vs__dropdown-toggle):focus-within {
    border-bottom-color: #1B396A;
}
</style>
