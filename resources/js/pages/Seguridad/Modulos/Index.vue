<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';

const props = defineProps({
    modulos: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Módulos" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Módulos del Sistema</h1>
                        <p class="text-gray-600 mt-2">Gestión de módulos y funcionalidades del sistema.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Nuevo Módulo
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Módulos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">12</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Activos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">10</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-gray-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Inactivos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">2</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-full">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Dashboard</h3>
                    <p class="text-sm text-gray-600 mb-4">Panel principal del sistema</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">15 permisos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Editar →</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Usuarios</h3>
                    <p class="text-sm text-gray-600 mb-4">Gestión de usuarios del sistema</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">12 permisos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Editar →</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Solicitudes</h3>
                    <p class="text-sm text-gray-600 mb-4">Control de solicitudes de becas</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">18 permisos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Editar →</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Convocatorias</h3>
                    <p class="text-sm text-gray-600 mb-4">Administración de convocatorias</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">10 permisos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Editar →</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Seguridad</h3>
                    <p class="text-sm text-gray-600 mb-4">Módulo de seguridad y permisos</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">20 permisos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Editar →</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Catálogo</h3>
                    <p class="text-sm text-gray-600 mb-4">Catálogos del sistema</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">8 permisos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Editar →</button>
                    </div>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
