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
    user: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    id: props.user.data.id,
    name: props.user.data.name,
    email: props.user.data.email,
    password: '', // Password is optional on update, left blank to keep current
    roles: props.user.data.roles_ids || [], 
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
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Ej: Juan Pérez" />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        
                         <!-- Correo -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                            <input v-model="form.email" type="email" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="correo@ejemplo.com" />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                         <!-- Contraseña -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña (Opcional)</label>
                            <input v-model="form.password" type="password" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Dejar en blanco para mantener actual" />
                            <p class="text-xs text-gray-500 mt-1">Solo llena este campo si deseas cambiar la contraseña.</p>
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                        </div>

                        <!-- Rol -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asignar Rol</label>
                            <select v-model="form.roles[0]" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]">
                                <option :value="null" disabled>Selecciona un rol</option>
                                <option v-for="rol in roles" :key="rol.id" :value="rol.id">{{ rol.name }}</option>
                            </select>
                             <p v-if="form.errors.roles" class="mt-1 text-sm text-red-600">{{ form.errors.roles }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 bg-gray-50 -mx-8 -mb-8 p-4 mt-6 rounded-b-lg">
                        <Link :href="route(`${routeName}index`)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <button :disabled="form.processing" type="submit" class="px-6 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition shadow-lg disabled:opacity-75 flex items-center gap-2">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>