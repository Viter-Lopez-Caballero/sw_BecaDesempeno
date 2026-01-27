<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Áreas Prioritarias" />

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Áreas Prioritarias</h1>
                        <p class="text-gray-600 mt-2">Gestión de áreas prioritarias de investigación y desarrollo.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Agregar Área
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activa</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Inteligencia Artificial</h3>
                    <p class="text-sm text-gray-600 mb-4">Desarrollo de sistemas inteligentes y machine learning.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">15 Proyectos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Ver más →</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activa</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Energías Renovables</h3>
                    <p class="text-sm text-gray-600 mb-4">Investigación en energía solar, eólica y sustentable.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">23 Proyectos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Ver más →</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activa</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Ciberseguridad</h3>
                    <p class="text-sm text-gray-600 mb-4">Protección de infraestructura digital y datos.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">18 Proyectos</span>
                        <button class="text-[#1B396A] hover:text-[#002B5C] font-medium">Ver más →</button>
                    </div>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
