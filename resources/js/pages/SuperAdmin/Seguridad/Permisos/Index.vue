<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';

const props = defineProps({
    permisos: {
        type: Object,
        required: false,
    },
});

const permisos = [
    { id: 1, modulo: 'Dashboard', nombre: 'dashboard.view', descripcion: 'Ver dashboard' },
    { id: 2, modulo: 'Dashboard', nombre: 'dashboard.stats', descripcion: 'Ver estadísticas' },
    { id: 3, modulo: 'Usuarios', nombre: 'users.create', descripcion: 'Crear usuarios' },
    { id: 4, modulo: 'Usuarios', nombre: 'users.edit', descripcion: 'Editar usuarios' },
    { id: 5, modulo: 'Usuarios', nombre: 'users.delete', descripcion: 'Eliminar usuarios' },
    { id: 6, modulo: 'Usuarios', nombre: 'users.view', descripcion: 'Ver usuarios' },
    { id: 7, modulo: 'Solicitudes', nombre: 'requests.create', descripcion: 'Crear solicitudes' },
    { id: 8, modulo: 'Solicitudes', nombre: 'requests.approve', descripcion: 'Aprobar solicitudes' },
];
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Permisos" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Permisos del Sistema</h1>
                        <p class="text-gray-600 mt-2">Gestión de permisos y accesos a funcionalidades.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Nuevo Permiso
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Permisos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">48</p>
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
                            <p class="text-sm text-gray-600 font-medium">Por Módulo</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">6</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Asignados</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">142</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" placeholder="Buscar permisos..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1B396A] focus:border-transparent" />
                    </div>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1B396A] focus:border-transparent">
                        <option value="">Todos los módulos</option>
                        <option value="dashboard">Dashboard</option>
                        <option value="users">Usuarios</option>
                        <option value="requests">Solicitudes</option>
                    </select>
                </div>
            </div>

            <!-- Permissions Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Módulo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="permiso in permisos" :key="permiso.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">{{ permiso.modulo }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ permiso.nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ permiso.descripcion }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-[#1B396A] hover:text-[#002B5C] mr-3">Editar</button>
                                <button class="text-red-600 hover:text-red-900">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
