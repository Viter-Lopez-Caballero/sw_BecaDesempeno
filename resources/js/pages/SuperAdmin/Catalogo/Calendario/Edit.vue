<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiBookOpenPageVariant, mdiCalendar, mdiCalendarClock } from '@mdi/js';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { alertaExito, alertaError, alertaCargando, cerrarAlerta } from '@/utils/alerts.js';

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

const clearError = (field) => {
    if (form.errors[field]) {
        delete form.errors[field];
    }
};

const submit = () => {
    // Limpiar errores previos
    form.clearErrors();
    
    // Validación del lado del cliente
    if (!form.convocatoria_id) {
        form.errors.convocatoria_id = 'Debes seleccionar una convocatoria';
        return;
    }
    if (!form.publicacion_inicio) {
        form.errors.publicacion_inicio = 'La fecha de inicio de publicación es obligatoria';
        return;
    }
    if (!form.publicacion_fin) {
        form.errors.publicacion_fin = 'La fecha de fin de publicación es obligatoria';
        return;
    }
    if (!form.registro_inicio) {
        form.errors.registro_inicio = 'La fecha de inicio de registro es obligatoria';
        return;
    }
    if (!form.registro_fin) {
        form.errors.registro_fin = 'La fecha de fin de registro es obligatoria';
        return;
    }
    if (!form.evaluacion_inicio) {
        form.errors.evaluacion_inicio = 'La fecha de inicio de evaluación es obligatoria';
        return;
    }
    if (!form.evaluacion_fin) {
        form.errors.evaluacion_fin = 'La fecha de fin de evaluación es obligatoria';
        return;
    }
    if (!form.resultados_inicio) {
        form.errors.resultados_inicio = 'La fecha de inicio de resultados es obligatoria';
        return;
    }
    if (!form.resultados_fin) {
        form.errors.resultados_fin = 'La fecha de fin de resultados es obligatoria';
        return;
    }
    
    alertaCargando('Actualizando', 'Por favor espera...');
    
    form.put(route(`${props.routeName}update`, { calendario: form.id }), {
        onSuccess: () => {
            cerrarAlerta();
            alertaExito('¡Éxito!', 'Calendario actualizado correctamente');
        },
        onError: () => {
            cerrarAlerta();
            alertaError('Error', 'Por favor verifica los datos ingresados');
        }
    });
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
                            <path :d="mdiBookOpenPageVariant"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Catálogo</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <Link :href="route(`${routeName}index`)" class="flex items-center gap-2 hover:underline">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                <path :d="mdiCalendar"/>
                            </svg>
                            <span class="text-gray-700 font-medium">Calendario</span>
                        </Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Editar Calendario</span>
                    </div>
                </div>
                <Link :href="route(`${routeName}index`)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium cursor-pointer">
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
                        <label class="block mb-2 text-sm font-medium text-gray-900">Convocatoria <span class="text-red-500">*</span></label>
                        <VueSelect 
                            v-model="form.convocatoria_id" 
                            :options="convocatorias" 
                            :reduce="option => option.id"
                            label="nombre"
                            placeholder="Selecciona una convocatoria"
                            :searchable="true"
                            :clearable="true"
                            :class="['vue-select-custom', { 'vue-select-error': form.errors.convocatoria_id }]"
                            @option:selected="clearError('convocatoria_id')"
                            @option:deselected="clearError('convocatoria_id')"
                        >
                            <template #option="option">
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{ option.nombre }}</span>
                                </div>
                            </template>
                            <template #no-options="{ search, searching }">
                                <template v-if="searching">
                                    No se encontraron resultados para <em>{{ search }}</em>.
                                </template>
                                <em v-else>Comienza a escribir para buscar...</em>
                            </template>
                        </VueSelect>
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
                                <input v-model="form.publicacion_inicio" @input="clearError('publicacion_inicio')" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" :class="{ 'border-b-red-500': form.errors.publicacion_inicio }" />
                                <p v-if="form.errors.publicacion_inicio" class="mt-1 text-sm text-red-600">{{ form.errors.publicacion_inicio }}</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Fin: <span class="text-red-500">*</span></label>
                                <input v-model="form.publicacion_fin" @input="clearError('publicacion_fin')" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" :class="{ 'border-b-red-500': form.errors.publicacion_fin }" />
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
                                <input v-model="form.registro_inicio" @input="clearError('registro_inicio')" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" :class="{ 'border-b-red-500': form.errors.registro_inicio }" />
                                <p v-if="form.errors.registro_inicio" class="mt-1 text-sm text-red-600">{{ form.errors.registro_inicio }}</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Fin: <span class="text-red-500">*</span></label>
                                <input v-model="form.registro_fin" @input="clearError('registro_fin')" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" :class="{ 'border-b-red-500': form.errors.registro_fin }" />
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
                                <input v-model="form.evaluacion_inicio" @input="clearError('evaluacion_inicio')" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" :class="{ 'border-b-red-500': form.errors.evaluacion_inicio }" />
                                <p v-if="form.errors.evaluacion_inicio" class="mt-1 text-sm text-red-600">{{ form.errors.evaluacion_inicio }}</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Fin: <span class="text-red-500">*</span></label>
                                <input v-model="form.evaluacion_fin" @input="clearError('evaluacion_fin')" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" :class="{ 'border-b-red-500': form.errors.evaluacion_fin }" />
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
                                <input v-model="form.resultados_inicio" @input="clearError('resultados_inicio')" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" :class="{ 'border-b-red-500': form.errors.resultados_inicio }" />
                                <p v-if="form.errors.resultados_inicio" class="mt-1 text-sm text-red-600">{{ form.errors.resultados_inicio }}</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Fecha de Fin: <span class="text-red-500">*</span></label>
                                <input v-model="form.resultados_fin" @input="clearError('resultados_fin')" type="date" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" :class="{ 'border-b-red-500': form.errors.resultados_fin }" />
                                <p v-if="form.errors.resultados_fin" class="mt-1 text-sm text-red-600">{{ form.errors.resultados_fin }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route(`${routeName}index`)" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition cursor-pointer">
                            Cancelar
                        </Link>
                        <button :disabled="form.processing" type="submit" class="px-6 py-2.5 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl disabled:opacity-75 flex items-center gap-2 font-medium cursor-pointer">
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
    background: linear-gradient(to bottom right, #F3F4F6, #E5E7EB);
    border: none;
    border-bottom: 2px solid #D1D5DB;
    border-radius: 0.5rem;
    padding: 0.625rem 0.75rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
}

:deep(.vue-select-custom .vs__dropdown-toggle):hover {
    border-bottom-color: rgba(27, 57, 106, 0.5);
}

:deep(.vue-select-custom .vs--open .vs__dropdown-toggle) {
    background: linear-gradient(to bottom right, #EFF6FF, #DBEAFE);
    border-bottom-color: #1B396A;
}

:deep(.vue-select-custom .vs__search) {
    margin: 0;
    padding: 0;
    border: none;
    font-size: 0.875rem;
    color: #111827;
}

:deep(.vue-select-custom .vs__search::placeholder) {
    color: #9CA3AF;
}

:deep(.vue-select-custom .vs__selected) {
    margin: 0;
    padding: 0;
    border: none;
    color: #111827;
    font-size: 0.875rem;
}

:deep(.vue-select-custom .vs__actions) {
    padding: 0 4px 0 6px;
}

:deep(.vue-select-custom .vs__clear),
:deep(.vue-select-custom .vs__open-indicator) {
    fill: #1B396A;
    transition: transform 0.2s;
}

:deep(.vue-select-custom .vs__open-indicator) {
    transform: scale(0.70);
}

:deep(.vue-select-custom .vs--open .vs__open-indicator) {
    transform: rotate(180deg) scale(0.70);
}

:deep(.vue-select-custom .vs__dropdown-menu) {
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    margin-top: 4px;
}

:deep(.vue-select-custom .vs__dropdown-option) {
    padding: 0.625rem 0.75rem;
    color: #374151;
    font-size: 0.875rem;
    transition: all 0.15s;
}

:deep(.vue-select-custom .vs__dropdown-option--highlight) {
    background: #1B396A;
    color: white;
}

:deep(.vue-select-custom .vs__no-options) {
    padding: 0.75rem;
    color: #6B7280;
    font-size: 0.875rem;
    text-align: center;
}

/* Error state */
.vue-select-error :deep(.vs__dropdown-toggle),
.vue-select-error :deep(.vs--open .vs__dropdown-toggle),
.vue-select-error :deep(.vs__dropdown-toggle):hover {
    border-bottom-color: #EF4444 !important;
}
</style>
