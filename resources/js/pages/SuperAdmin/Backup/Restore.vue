<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { ref, computed } from 'vue';
import { mdiDatabase, mdiDatabaseRefresh, mdiDatabaseOutline, mdiAlertCircle } from '@mdi/js';
import { alertaExito, alertaError } from '@/utils/alerts.js';
import Swal from 'sweetalert2';
import { useCan } from '@/composables/usePermissions';

const hasRestorePermission = useCan('backup.restore');

const props = defineProps({
    backupList: { type: Array, required: true },
});

const selectedBackup = ref(null);

const form = useForm({
    backup_id: null,
});

const confirmKeyword = ref('');
const KEYWORD = 'RESTAURAR';

const canRestore = computed(() => selectedBackup.value !== null && confirmKeyword.value === KEYWORD);

const submit = async () => {
    if (!selectedBackup.value) {
        alertaError('Selección requerida', 'Por favor selecciona un respaldo para restaurar.');
        return;
    }

    // Manual confirm with typed keyword
    const { value: text } = await Swal.fire({
        title: '⚠️ Confirmar Restauración',
        html: `
            <div class="text-left space-y-3">
                <p class="text-gray-700 text-sm">Esta acción <strong>sobrescribirá toda la base de datos</strong> con el contenido del respaldo seleccionado. <strong>Esta acción no se puede deshacer.</strong></p>
                <p class="text-gray-700 text-sm">Para confirmar, escribe <strong class="text-red-600">${KEYWORD}</strong> en el campo de abajo:</p>
                <input id="swal-confirm-input" class="swal2-input" placeholder="${KEYWORD}" style="font-family:monospace" />
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar',
        focusConfirm: false,
        preConfirm: () => {
            const input = Swal.getPopup().querySelector('#swal-confirm-input');
            if (input.value !== KEYWORD) {
                Swal.showValidationMessage(`Debes escribir exactamente: ${KEYWORD}`);
            }
            return input.value;
        },
        allowOutsideClick: () => !Swal.isLoading(),
    });

    if (text !== KEYWORD) return;

    form.backup_id = selectedBackup.value.id;
    form.post(route('superadmin.backup.do-restore'), {
        onSuccess: () => alertaExito('Restauración completada', 'La base de datos fue restaurada exitosamente.'),
        onError:   (errors) => {
            const msg = Object.values(errors)[0] ?? 'Ocurrió un error durante la restauración.';
            alertaError('Error en la restauración', msg);
        },
    });
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Restauración de Base de Datos" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Restauración de Base de Datos</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill:#1B396A">
                            <path :d="mdiDatabaseOutline"/>
                        </svg>
                        <Link :href="route('superadmin.backup.index')" class="text-gray-700 font-medium hover:underline">Respaldos</Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Restauración de Base de Datos</span>
                    </div>
                </div>
                <Link :href="route('superadmin.backup.index')" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <!-- Warning card -->
            <div class="flex items-start gap-4 px-5 py-4 bg-white border border-gray-200 border-l-4 border-l-[#C9A800] rounded-lg shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#C9A800" class="flex-shrink-0 mt-0.5">
                    <path d="m40-120 440-760 440 760H40Zm138-80h604L480-720 178-200Zm302-40q17 0 28.5-11.5T520-280q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280q0 17 11.5 28.5T480-240Zm-40-120h80v-200h-80v200Zm40-100Z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#C9A800] mb-0.5">Advertencia</p>
                    <p class="text-sm text-gray-800 font-medium">
                        <strong>Operación irreversible</strong> — La restauración <strong>reemplaza completamente</strong> la base de datos actual con el contenido del respaldo seleccionado.
                        Todos los datos ingresados después de la fecha del respaldo se perderán permanentemente.
                        Se recomienda <strong>crear un respaldo manual</strong> antes de proceder.
                    </p>
                </div>
            </div>

            <!-- No backups notice -->
            <div v-if="!backupList || backupList.length === 0"
                 class="bg-white rounded-lg shadow-md border border-gray-200 p-12 text-center">
                <svg viewBox="0 0 24 24" class="w-14 h-14 mx-auto mb-4 opacity-30" style="fill:#6B7280">
                    <path :d="mdiDatabase"/>
                </svg>
                <p class="text-gray-600 font-semibold text-lg">No hay respaldos disponibles</p>
                <p class="text-gray-400 text-sm mt-1">Crea al menos un respaldo antes de intentar restaurar.</p>
                <Link :href="route('superadmin.backup.create')" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-[#1B396A] text-white text-sm font-semibold rounded-lg hover:bg-[#0f2347] transition">
                    Crear Respaldo Manual
                </Link>
            </div>

            <!-- Form card -->
            <div v-else class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Selector -->
                    <div>
                        <label class="block mb-2 text-base text-[#1B396A] font-medium">
                            Respaldo a Restaurar: <span class="text-red-500">*</span>
                        </label>
                        <VueSelect
                            v-model="selectedBackup"
                            :options="backupList"
                            label="label"
                            placeholder="Selecciona un respaldo..."
                            class="vue-select-form"
                        >
                            <template #option="option">
                                <div class="flex items-center justify-between gap-2 py-0.5">
                                    <span class="font-medium text-gray-800">{{ option.name }}</span>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ option.date }} · {{ option.size }}</span>
                                </div>
                            </template>
                            <template #selected-option="option">
                                <span class="font-medium text-gray-800">{{ option.name }}</span>
                                <span class="ml-2 text-xs text-gray-400">{{ option.date }}</span>
                            </template>
                        </VueSelect>
                    </div>

                    <!-- Selected backup info -->
                    <div v-if="selectedBackup" class="p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-2 text-sm">
                        <div class="flex flex-wrap gap-x-6 gap-y-2">
                            <div>
                                <span class="text-gray-500">Nombre:</span>
                                <span class="ml-2 font-semibold text-gray-800">{{ selectedBackup.name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Fecha:</span>
                                <span class="ml-2 font-semibold text-gray-800">{{ selectedBackup.date }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Tamaño:</span>
                                <span class="ml-2 font-semibold text-gray-800">{{ selectedBackup.size }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Tipo:</span>
                                <span class="ml-2 font-semibold text-gray-800">{{ selectedBackup.type }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm input -->
                    <div>
                        <label class="block mb-2 text-base text-[#1B396A] font-medium">Confirmación de Seguridad:</label>
                        <p class="text-xs text-gray-500 mb-1">Escribe <span class="font-mono font-bold text-red-600">{{ KEYWORD }}</span> para habilitar el botón de restauración</p>
                        <input
                            v-model="confirmKeyword"
                            type="text"
                            :placeholder="KEYWORD"
                            spellcheck="false"
                            autocomplete="off"
                            class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 font-mono tracking-widest"
                            :class="{ 'border-b-green-500': confirmKeyword === KEYWORD, 'border-b-red-500': confirmKeyword.length > 0 && confirmKeyword !== KEYWORD, 'border-b-gray-300': confirmKeyword.length === 0 }"
                        />
                        <div v-if="confirmKeyword.length === 0" class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Escribe exactamente la palabra de confirmación para continuar</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route('superadmin.backup.index')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </Link>
                        <button
                            v-if="hasRestorePermission"
                            type="button"
                            @click="submit"
                            :disabled="!canRestore || form.processing"
                            class="px-6 py-2 cursor-pointer font-medium rounded-lg transition shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            :class="canRestore ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-gray-200 text-gray-400'"
                        >
                            {{ form.processing ? 'Restaurando...' : 'Restaurar Base de Datos' }}
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
</style>
