<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { computed, ref } from 'vue';
import { mdiDatabaseOutline, mdiBell, mdiBellOff, mdiCalendarClock } from '@mdi/js';
import { alertaCargando, alertaExito, alertaError } from '@/utils/alerts.js';
import { useCan } from '@/composables/usePermissions';

const canSchedule = useCan('backup.schedule');

const props = defineProps({
    schedule: { type: Object, required: true },
});

const frequencyOptions = [
    { label: 'Diario',   value: 'daily'   },
    { label: 'Semanal',  value: 'weekly'  },
    { label: 'Mensual',  value: 'monthly' },
];

const form = useForm({
    frequency:            props.schedule.frequency    ?? 'daily',
    run_time:             props.schedule.run_time     ?? '02:00',
    email_notifications:  props.schedule.email_notifications ?? false,
    is_active:            props.schedule.is_active    ?? true,
});

const frequencyLabel = computed(() => {
    return frequencyOptions.find(o => o.value === form.frequency)?.label ?? '';
});

const showScheduleInfo = ref(true);
const showNextRunInfo  = ref(true);

const submit = () => {
    alertaCargando('Guardando...', 'Por favor espera.');
    form.post(route('superadmin.backup.update-schedule'), {
        onSuccess: () => alertaExito('Configuración guardada', 'La programación de respaldos ha sido actualizada correctamente.'),
        onError:   (errors) => {
            const msg = Object.values(errors)[0] ?? 'Ocurrió un error al guardar la configuración.';
            alertaError('Error', msg);
        },
    });
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Programación Automática" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Programación Automática</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiDatabaseOutline"/>
                        </svg>
                        <Link :href="route('superadmin.backup.index')" class="text-gray-700 font-medium hover:underline">Respaldos</Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Programación Automática</span>
                    </div>
                </div>
                <Link :href="route('superadmin.backup.index')" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <!-- Current schedule preview -->
            <div v-if="schedule.next_run_at && showScheduleInfo"
                 class="flex items-start gap-4 px-5 py-4 bg-white border border-gray-200 border-l-4 border-l-[#1B396A] rounded-lg shadow-sm">
                <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0 mt-0.5" style="fill:#1B396A">
                    <path :d="mdiCalendarClock"/>
                </svg>
                <div class="flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#1B396A] mb-0.5">Próximo Respaldo</p>
                    <p class="text-sm text-gray-800 font-medium">
                        Próximo respaldo programado:
                        <strong>{{ schedule.next_run_at }}</strong>
                        — Frecuencia actual: <strong>{{ schedule.frequency_label }}</strong>
                    </p>
                </div>
                <button type="button" @click="showScheduleInfo = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="currentColor">
                        <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                    </svg>
                </button>
            </div>

            <!-- Form card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Frecuencia -->
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium">Frecuencia: <span class="text-red-500">*</span></label>
                            <VueSelect
                                v-model="form.frequency"
                                :options="frequencyOptions"
                                :reduce="o => o.value"
                                :searchable="false"
                                :clearable="false"
                                placeholder="Selecciona frecuencia"
                                class="vue-select-form"
                            />
                            <div v-if="!form.errors.frequency" class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Selecciona con qué frecuencia se ejecutarán los respaldos</span>
                            </div>
                            <p v-if="form.errors.frequency" class="mt-1 text-sm text-red-600">{{ form.errors.frequency }}</p>
                        </div>

                        <!-- Hora de Ejecución -->
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium">Hora de Ejecución: <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.run_time"
                                type="time"
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                :class="{ 'border-b-red-500': form.errors.run_time }"
                            />
                            <div v-if="!form.errors.run_time" class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Hora del día en que se ejecutará el respaldo</span>
                            </div>
                            <p v-if="form.errors.run_time" class="mt-1 text-sm text-red-600">{{ form.errors.run_time }}</p>
                        </div>

                    </div>

                    <!-- Notificaciones por Correo -->
                    <div class="flex items-center justify-between py-4 px-5 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="form.email_notifications ? 'bg-[#1B396A]' : 'bg-gray-200'">
                                <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ fill: form.email_notifications ? 'white' : '#6B7280' }">
                                    <path :d="form.email_notifications ? mdiBell : mdiBellOff"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Notificaciones por Correo</p>
                                <p class="text-xs text-gray-500">{{ form.email_notifications ? 'Se enviará un correo al completar cada respaldo' : 'Sin notificaciones por correo' }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="form.email_notifications = !form.email_notifications"
                            class="relative inline-flex w-14 h-7 items-center rounded-full transition-colors focus:outline-none"
                            :class="form.email_notifications ? 'bg-[#1B396A]' : 'bg-gray-300'"
                        >
                            <span
                                class="inline-block w-5 h-5 bg-white rounded-full shadow-md transform transition-transform"
                                :class="form.email_notifications ? 'translate-x-8' : 'translate-x-1'"
                            />
                        </button>
                    </div>

                    <!-- Activar / Desactivar programación -->
                    <div class="flex items-center justify-between py-4 px-5 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="form.is_active ? 'bg-green-500' : 'bg-gray-200'">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" :fill="form.is_active ? 'white' : '#6B7280'">
                                    <path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm-40-120v-80q-50 0-85-35t-35-85v-40L100-680q-8 30-14 58.5T80-560q0 131 79 227t221 113Zm260-26q69-63 104.5-144T840-560v-20q0-26-2.5-48.5T826-673L560-409v89q0 50-35 85t-85 35v80q88-3 155-45t145-106Z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Programación Activa</p>
                                <p class="text-xs text-gray-500">{{ form.is_active ? 'Los respaldos automáticos están activados' : 'Los respaldos automáticos están desactivados' }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="form.is_active = !form.is_active"
                            class="relative inline-flex w-14 h-7 items-center rounded-full transition-colors focus:outline-none"
                            :class="form.is_active ? 'bg-green-500' : 'bg-gray-300'"
                        >
                            <span
                                class="inline-block w-5 h-5 bg-white rounded-full shadow-md transform transition-transform"
                                :class="form.is_active ? 'translate-x-8' : 'translate-x-1'"
                            />
                        </button>
                    </div>

                    <!-- Next run preview -->
                    <div v-if="showNextRunInfo"
                         class="flex items-start gap-4 px-5 py-4 bg-white border border-gray-200 border-l-4 border-l-green-500 rounded-lg shadow-sm">
                        <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0 mt-0.5" style="fill:#16A34A">
                            <path :d="mdiCalendarClock"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-green-600 mb-0.5">Configuración Actual</p>
                            <p class="text-sm text-gray-800 font-medium">
                                Con la configuración actual, el sistema ejecutará respaldos de manera
                                <strong>{{ frequencyLabel }}</strong> a las <strong>{{ form.run_time }}</strong> hrs.
                            </p>
                        </div>
                        <button type="button" @click="showNextRunInfo = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="currentColor">
                                <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route('superadmin.backup.index')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </Link>
                        <button
                            v-if="canSchedule"
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 cursor-pointer bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl disabled:opacity-75 flex items-center gap-2 font-medium"
                        >
                            <span v-if="!form.processing">Guardar Configuración</span>
                            <span v-else>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>

<style scoped>
:deep(.vue-select-form .vs__dropdown-toggle) {
    background: #F3F4F6;
    border: none;
    border-bottom: 2px solid #D1D5DB;
    border-radius: 0.375rem 0.375rem 0 0;
    padding: 0 8px;
    height: 44px;
    display: flex;
    align-items: center;
    transition: border-color .2s, background .2s;
}
:deep(.vue-select-form .vs__dropdown-toggle:focus-within) {
    border-bottom-color: #1B396A;
    background: white;
}
:deep(.vue-select-form .vs__selected) { color: #1F2937; font-weight: 500; font-size: 0.875rem; }
:deep(.vue-select-form .vs__search::placeholder) { color: #9CA3AF; }
:deep(.vue-select-form .vs__dropdown-menu) {
    border: 1px solid #D1D5DB;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,.1);
}
:deep(.vue-select-form .vs__dropdown-option) { padding: .75rem 1rem; color: #374151; }
:deep(.vue-select-form .vs__dropdown-option--highlight) { background: #1B396A; color: white; }
:deep(.vue-select-form .vs__open-indicator) { fill: #1B396A; transform: scale(.85); }
:deep(.vue-select-form .vs__clear) { fill: #9CA3AF; }
</style>
