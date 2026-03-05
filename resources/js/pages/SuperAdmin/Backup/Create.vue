<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { ref } from 'vue';
import { mdiDatabaseOutline, mdiLock, mdiLockOff } from '@mdi/js';
import { alertaCargando, alertaExito, alertaError } from '@/utils/alerts.js';
import { useCan } from '@/composables/usePermissions';

const canCreate = useCan('backup.create');

const descLength = ref(0);
const showInfo   = ref(true);

const form = useForm({
    name:         '',
    description:  '',
    encrypted: false,
});

const submit = () => {
    form.post(route('superadmin.backup.store'), {
        onStart:   () => alertaCargando('Procesando...', 'Ejecutando respaldo de la base de datos, esto puede tomar unos segundos.'),
        onSuccess: () => alertaExito('Respaldo completado', 'El respaldo se ha generado exitosamente.'),
        onError:   (errors) => {
            const msg = Object.values(errors)[0] ?? 'Ocurrió un error al ejecutar el respaldo.';
            alertaError('Error', msg);
        },
    });
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Respaldo Manual" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Respaldo Manual</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiDatabaseOutline"/>
                        </svg>
                        <Link :href="route('superadmin.backup.index')" class="text-gray-700 font-medium hover:underline">Respaldos</Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Respaldo Manual</span>
                    </div>
                </div>
                <Link :href="route('superadmin.backup.index')" class="w-full md:w-auto justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <!-- Form card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium">Nombre del Respaldo: <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Ej: Respaldo pre-actualización"
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                :class="{ 'border-b-red-500': form.errors.name }"
                            />
                            <div v-if="!form.errors.name" class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, introduce el nombre del respaldo</span>
                            </div>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Descripción -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block mb-2 text-base text-[#1B396A] font-medium">Descripción:</label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                maxlength="255"
                                placeholder="Describe el motivo o contexto de este respaldo..."
                                @input="descLength = form.description.length"
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A] resize-none"
                                :class="{ 'border-b-red-500': form.errors.description }"
                            ></textarea>
                            <div class="flex items-center justify-between mt-1">
                                <div v-if="!form.errors.description" class="flex items-center gap-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Describe el motivo o contexto de este respaldo</span>
                                </div>
                                <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                                <span class="text-gray-400 text-sm">{{ descLength }}/255</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cifrado del Archivo -->
                    <div class="flex items-center justify-between py-4 px-5 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="form.encrypted ? 'bg-[#1B396A]' : 'bg-gray-200'">
                                <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ fill: form.encrypted ? 'white' : '#6B7280' }">
                                    <path :d="form.encrypted ? mdiLock : mdiLockOff"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Cifrado del Archivo</p>
                                <p class="text-xs text-gray-500">{{ form.encrypted ? 'El archivo será cifrado' : 'Sin cifrado (SQL estándar)' }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="form.encrypted = !form.encrypted"
                            class="relative inline-flex w-14 h-7 items-center rounded-full transition-colors focus:outline-none"
                            :class="form.encrypted ? 'bg-[#1B396A]' : 'bg-gray-300'"
                        >
                            <span
                                class="inline-block w-5 h-5 bg-white rounded-full shadow-md transform transition-transform"
                                :class="form.encrypted ? 'translate-x-8' : 'translate-x-1'"
                            />
                        </button>
                    </div>

                    <!-- Info note -->
                    <div v-if="showInfo" class="flex items-start gap-4 px-5 py-4 bg-white border border-gray-200 border-l-4 border-l-[#1B396A] rounded-lg shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1B396A" class="flex-shrink-0 mt-0.5">
                            <path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#1B396A] mb-0.5">Instrucciones</p>
                            <p class="text-sm text-gray-800 font-medium">El respaldo incluye la <strong>base de datos completa</strong>. El proceso puede tardar unos momentos dependiendo del tamaño de los datos.</p>
                        </div>
                        <button type="button" @click="showInfo = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
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
                            v-if="canCreate"
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 cursor-pointer bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl disabled:opacity-75 flex items-center gap-2 font-medium"
                        >
                            <span v-if="!form.processing">Ejecutar Respaldo Ahora</span>
                            <span v-else>Ejecutando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
