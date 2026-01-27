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
    modules: {
        type: Array,
        required: true,
    },
    permission: {
        type: Object,
        required: true,
    }
});

const form = useForm({
    id: props.permission.id,
    name: props.permission.name,
    guard_name: props.permission.guard_name,
    description: props.permission.description,
    module_key: props.permission.module_key, 
});

const submit = () => {
    form.put(route(`${props.routeName}update`, form.id));
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
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                             </svg>
                            Permisos
                        </Link>
                        <span>&gt;</span>
                        <span class="text-gray-500">Editar Permiso</span>
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
                        <!-- Module Selector -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre: <span class="text-red-500">*</span></label>
                            <select v-model="form.module_key" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]">
                                <option value="" disabled>Seleccione una Opción</option>
                                <option v-for="modulo in modules" :key="modulo.id" :value="modulo.key">{{ modulo.name }}</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Selecciona un módulo disponible</p>
                            <p v-if="form.errors.module_key" class="mt-1 text-sm text-red-600">{{ form.errors.module_key }}</p>
                        </div>
                        
                         <!-- Permission Name (Key) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Clave: <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Clave del permiso" />
                            <p class="text-sm text-gray-500 mt-1">Ejemplos: nombre_modulo.index, nombre_modulo.store, ...</p>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                         <!-- Description -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción: <span class="text-red-500">*</span></label>
                            <textarea v-model="form.description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Descripción"></textarea>
                            <div class="flex justify-between mt-1">
                                <p class="text-sm text-gray-500">Ejemplos: index=Leer Registros, store=Crear Registros...</p>
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
                            <span v-if="!form.processing">Actualizar</span>
                             <span v-else>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>