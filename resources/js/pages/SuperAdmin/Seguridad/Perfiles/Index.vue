<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';

const props = defineProps({
    perfiles: {
        type: Object,
        required: false,
    },
});

const roles = [
    { id: 1, nombre: 'Super Administrador', usuarios: 2, permisos: 48, color: 'red' },
    { id: 2, nombre: 'Administrador', usuarios: 5, permisos: 35, color: 'blue' },
    { id: 3, nombre: 'Coordinador', usuarios: 8, permisos: 20, color: 'green' },
    { id: 4, nombre: 'Evaluador', usuarios: 15, permisos: 12, color: 'yellow' },
    { id: 5, nombre: 'Solicitante', usuarios: 245, permisos: 5, color: 'purple' },
];
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Perfiles" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Roles y Perfiles</h1>
                        <p class="text-gray-600 mt-2">Gestión de roles y asignación de permisos.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Nuevo Rol
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Roles</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">5</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Usuarios Asignados</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">275</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Permisos Totales</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">120</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Activos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">5</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="rol in roles" :key="rol.id" class="bg-white rounded-lg shadow-sm p-6 border-l-4 hover:shadow-md transition" :class="`border-${rol.color}-500`">
                    <div class="flex items-start justify-between mb-4">
                        <div :class="`p-3 bg-${rol.color}-100 rounded-lg`">
                            <svg class="w-6 h-6" :class="`text-${rol.color}-600`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span :class="`px-2 py-1 bg-${rol.color}-100 text-${rol.color}-800 text-xs font-semibold rounded-full`">{{ rol.usuarios }} usuarios</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ rol.nombre }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ rol.permisos }} permisos asignados</p>
                    <div class="flex items-center justify-between">
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium text-sm">Ver Permisos</button>
                        <div class="flex gap-2">
                            <button class="text-gray-600 hover:text-gray-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button class="text-red-600 hover:text-red-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
