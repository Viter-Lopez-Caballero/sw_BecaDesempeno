<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { ref, watch, computed, onMounted } from 'vue';

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

// Computed permissions for the selected module
const modulePermissions = computed(() => {
    if (!selectedModuleKey.value) return [];
    return props.permissions[selectedModuleKey.value] || [];
});

const submit = () => {
    form.put(route(`${props.routeName}update`, form.id));
};

// Helper: Check if all permissions of current module are selected
const isAllSelected = computed(() => {
    const current = modulePermissions.value;
    if (current.length === 0) return false;
    return current.every(p => form.permissions.includes(p.id));
});

const toggleAllModulePermissions = () => {
    const current = modulePermissions.value;
    if (isAllSelected.value) {
        // Deselect all from this module
        form.permissions = form.permissions.filter(id => !current.find(p => p.id === id));
    } else {
        // Select all from this module
        const newIds = current.map(p => p.id);
        form.permissions = [...new Set([...form.permissions, ...newIds])];
    }
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
                            Roles
                        </Link>
                        <span>&gt;</span>
                        <span class="text-gray-500">Editar</span>
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
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol</label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Ej: Editor" />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <input v-model="form.description" type="text" class="w-full rounded-lg border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]" placeholder="Descripción breve..." />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Asignación de Permisos</h3>
                        
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Module Selector -->
                            <div class="w-full md:w-1/3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selecciona un Módulo</label>
                                <div class="space-y-2 max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-2">
                                    <div 
                                        v-for="modulo in modules" 
                                        :key="modulo.id"
                                        @click="selectedModuleKey = modulo.key"
                                        class="p-3 rounded-md cursor-pointer transition flex items-center justify-between"
                                        :class="selectedModuleKey === modulo.key ? 'bg-[#1B396A] text-white' : 'hover:bg-gray-50 text-gray-700'"
                                    >
                                        <span class="font-medium">{{ modulo.name }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="selectedModuleKey === modulo.key ? 'text-white' : 'text-gray-400'" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Selecciona un módulo para ver sus permisos disponibles.</p>
                            </div>

                            <!-- Permissions List -->
                            <div class="w-full md:w-2/3 bg-gray-50 rounded-lg border border-gray-200 p-4">
                                <div v-if="selectedModuleKey">
                                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                                        <h4 class="font-semibold text-gray-800">Permisos de Módulo</h4>
                                        <button 
                                            type="button" 
                                            @click="toggleAllModulePermissions"
                                            class="text-sm text-[#1B396A] hover:underline font-medium"
                                        >
                                            {{ isAllSelected ? 'Deseleccionar Todos' : 'Seleccionar Todos' }}
                                        </button>
                                    </div>
                                    <div v-if="modulePermissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <label 
                                            v-for="permission in modulePermissions" 
                                            :key="permission.id" 
                                            class="flex items-start gap-3 p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-[#1B396A] transition group"
                                            :class="{'ring-1 ring-[#1B396A] border-[#1B396A]': form.permissions.includes(permission.id)}"
                                        >
                                            <input 
                                                type="checkbox" 
                                                :value="permission.id" 
                                                v-model="form.permissions"
                                                class="mt-1 h-4 w-4 text-[#1B396A] border-gray-300 rounded focus:ring-[#1B396A]"
                                            >
                                            <div>
                                                <div class="font-medium text-gray-900 group-hover:text-[#1B396A]">{{ permission.description }}</div>
                                                <div class="text-xs text-gray-500 font-mono">{{ permission.name }}</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div v-else class="text-center py-10 text-gray-500">
                                        No hay permisos registrados para este módulo.
                                    </div>
                                </div>
                                <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 py-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                    </svg>
                                    <p>Selecciona un módulo de la izquierda</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Summary -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                             <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Has seleccionado <strong>{{ form.permissions.length }}</strong> permisos en total.</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <Link :href="route(`${routeName}index`)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <button :disabled="form.processing" type="submit" class="px-6 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition shadow-lg disabled:opacity-75 flex items-center gap-2">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Actualizar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>