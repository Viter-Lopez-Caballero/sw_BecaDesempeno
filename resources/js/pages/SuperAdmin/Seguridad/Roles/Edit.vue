<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { ref, computed } from 'vue';
import { mdiSecurity } from '@mdi/js';

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
    permissions: {
        type: Object, // Grouped by module_key
        required: true,
    },
    role: {
        type: Object,
        required: true,
    },
    rolePermissions: {
        type: Array,
        required: true,
    }
});

const form = useForm({
    id: props.role.data.id,
    name: props.role.data.name,
    guard_name: 'web',
    description: props.role.data.description,
    permissions: props.rolePermissions, // Pre-filled with IDs
});

const selectedModuleKey = ref('');
const searchPermission = ref('');

// Computed permissions for the selected module
const modulePermissions = computed(() => {
    if (!selectedModuleKey.value) return [];
    return props.permissions[selectedModuleKey.value] || [];
});

// Filtered permissions based on search
const filteredPermissions = computed(() => {
    if (!searchPermission.value) return modulePermissions.value;
    const search = searchPermission.value.toLowerCase();
    return modulePermissions.value.filter(p => 
        p.name.toLowerCase().includes(search) || 
        p.description.toLowerCase().includes(search)
    );
});

// Total permissions count
const totalPermissionsCount = computed(() => {
    let count = 0;
    Object.keys(props.permissions).forEach(key => {
        count += props.permissions[key].length;
    });
    return count;
});

const selectAll = () => {
    const currentIds = modulePermissions.value.map(p => p.id);
    const newPermissions = [...new Set([...form.permissions, ...currentIds])];
    form.permissions = newPermissions;
};

const selectNone = () => {
    const currentIds = modulePermissions.value.map(p => p.id);
    form.permissions = form.permissions.filter(id => !currentIds.includes(id));
};

const submit = () => {
    form.put(route(`${props.routeName}update`, form.id));
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
                            <path :d="mdiSecurity"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Seguridad</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <Link :href="route(`${routeName}index`)" class="flex items-center gap-2 hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                <path d="M40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm720 0v-120q0-44-24.5-84.5T666-434q51 6 96 20.5t84 35.5q36 20 55 44.5t19 53.5v120H760ZM360-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm400-160q0 66-47 113t-113 47q-11 0-28-2.5t-28-5.5q27-32 41.5-71t14.5-81q0-42-14.5-81T544-792q14-5 28-6.5t28-1.5q66 0 113 47t47 113Z"/>
                            </svg>
                            <span class="text-gray-700 font-medium">Roles</span>
                        </Link>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Editar Rol</span>
                    </div>
                </div>
                <Link :href="route(`${routeName}index`)" class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>
                    </svg>
                    Regresar
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Nombre del Rol: <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Ej: Editor" />
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, introduce el nombre del rol</span>
                            </div>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Descripción: <span class="text-red-500">*</span></label>
                            <input v-model="form.description" type="text" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Descripción del rol" />
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, introduce una breve descripción del rol</span>
                            </div>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Módulos (Izquierda) -->
                            <div>
                                <h3 class="text-base font-medium text-[#1B396A] mb-2">Módulos: <span class="text-red-500">*</span></h3>
                                <p class="text-xs text-gray-500 mb-3">{{ modules.length }} Módulos disponibles</p>
                                <div class="space-y-2">
                                    <div 
                                        v-for="modulo in modules" 
                                        :key="modulo.id"
                                        @click="selectedModuleKey = modulo.key"
                                        class="px-4 py-3 rounded-lg cursor-pointer transition text-sm font-medium"
                                        :class="selectedModuleKey === modulo.key ? 'bg-[#1B396A] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
                                    >
                                        {{ modulo.name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Permisos (Derecha) -->
                            <div class="md:col-span-3">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="text-base font-medium text-[#1B396A]">Permisos: <span class="text-red-500">*</span></h3>
                                        <p class="text-xs text-gray-500 mt-1">{{ totalPermissionsCount }} permisos disponibles</p>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ form.permissions.length }} Seleccionados
                                    </div>
                                </div>

                                <!-- Botones Todos/Ninguno -->
                                <div class="flex items-center gap-3 mb-3">
                                    <button 
                                        type="button" 
                                        @click="selectAll"
                                        :disabled="!selectedModuleKey"
                                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                            <path d="M268-240 42-466l57-56 170 170 56 56-57 56Zm226 0L268-466l56-57 170 170 368-368 56 57-424 424Zm0-226-57-56 198-198 57 56-198 198Z"/>
                                        </svg>
                                        Todos
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectNone"
                                        :disabled="!selectedModuleKey"
                                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                            <path d="M320-280 80-520l240-240 57 56-184 184 184 184-57 56Zm480 80v-160q0-50-35-85t-85-35H433l144 144-57 56-240-240 240-240 57 56-144 144h247q83 0 141.5 58.5T880-360v160h-80Z"/>
                                        </svg>
                                        Ninguno
                                    </button>
                                </div>

                                <!-- Búsqueda -->
                                <div class="relative mb-3">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1B396A">
                                            <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        v-model="searchPermission" 
                                        type="text" 
                                        placeholder="Buscar..." 
                                        :disabled="!selectedModuleKey"
                                        class="pl-10 w-full h-[45px] rounded-lg border border-gray-300 text-gray-700 focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 transition disabled:opacity-50 disabled:cursor-not-allowed" 
                                    />
                                </div>

                                <!-- Lista de permisos -->
                                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 max-h-96 overflow-y-auto">
                                    <div v-if="selectedModuleKey && filteredPermissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <label 
                                            v-for="permission in filteredPermissions" 
                                            :key="permission.id" 
                                            class="flex items-start gap-3 p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-[#1B396A] transition group"
                                            :class="{'ring-2 ring-[#1B396A] border-[#1B396A]': form.permissions.includes(permission.id)}"
                                        >
                                            <input 
                                                type="checkbox" 
                                                :value="permission.id" 
                                                v-model="form.permissions"
                                                class="mt-1 h-4 w-4 text-[#1B396A] border-gray-300 rounded focus:ring-[#1B396A]"
                                            >
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900 group-hover:text-[#1B396A]">{{ permission.id }} {{ permission.name }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ permission.description }}</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div v-else-if="selectedModuleKey && filteredPermissions.length === 0" class="text-center py-10 text-gray-500">
                                        No se encontraron permisos.
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center py-10 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                        </svg>
                                        <p class="text-sm">Selecciona un módulo de la izquierda</p>
                                    </div>
                                </div>
                            </div>
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