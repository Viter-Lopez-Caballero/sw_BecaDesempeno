<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
});

const form = useForm({
    name: '',
    key: '',
    description: '',
});

const submit = () => {
    form.post(route(`${props.routeName}store`));
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="title" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                     <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                        <span class="text-[#1B396A] font-semibold flex items-center gap-1">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Seguridad
                        </span>
                        <span>&gt;</span>
                        <Link :href="route(`${routeName}index`)" class="flex items-center gap-1 text-[#1B396A] font-semibold hover:underline">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a1 1 0 01-.15.53l-1.861 2.893A1 1 0 004 6.115V11a1 1 0 001.5 1A1 1 0 004 11V6.115l-1.861-2.893A1 1 0 004 4.101V3a1 1 0 011-1M16 2a1 1 0 011 1v2.101a1 1 0 01-.15.53l-1.861 2.893A1 1 0 0016 6.115V11a1 1 0 001.5 1A1 1 0 0016 11V6.115l-1.861-2.893A1 1 0 0016 4.101V3a1 1 0 011-1" clip-rule="evenodd" />
                                <path d="M5 2a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H6a1 1 0 01-1-1V2z" />
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" clip-rule="evenodd" />
                            </svg>
                            Módulos
                        </Link>
                        <span>&gt;</span>
                        <span class="text-gray-500">Agregar Módulo</span>
                    </div>
                </div>
                <Link :href="route(`${routeName}index`)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Regresar
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre: <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Nombre del módulo" />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        
                         <!-- Clave -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Clave: <span class="text-red-500">*</span></label>
                            <input v-model="form.key" type="text" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Clave del módulo" />
                             <p v-if="form.errors.key" class="mt-1 text-sm text-red-600">{{ form.errors.key }}</p>
                        </div>

                         <!-- Description -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción: <span class="text-red-500">*</span></label>
                            <textarea v-model="form.description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Descripción del módulo"></textarea>
                            <div class="flex justify-end mt-1">
                                <span class="text-gray-400 text-sm">/255</span>
                            </div>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route(`${routeName}index`)" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </Link>
                        <button :disabled="form.processing" type="submit" class="px-6 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition shadow-lg disabled:opacity-75 flex items-center gap-2 font-medium">
                            <span v-if="!form.processing">Guardar</span>
                             <span v-else>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>