<script setup>
import { Head } from '@inertiajs/vue3';
import DocenteLayout from '@/layouts/DocenteLayout.vue';
</script>

<template>
    <DocenteLayout>
        <Head title="Convocatorias" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Convocatorias</h1>
                        <p class="text-gray-600 mt-2">Administración de convocatorias para becas y estímulos.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Nueva Convocatoria
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Todas</option>
                            <option>Activas</option>
                            <option>Próximas</option>
                            <option>Cerradas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>2026</option>
                            <option>2025</option>
                            <option>2024</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Todos</option>
                            <option>Estímulos</option>
                            <option>Becas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <input 
                            type="search" 
                            placeholder="Buscar convocatoria..." 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                </div>
            </div>

            <!-- Convocatorias Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Convocatoria Activa -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 border-t-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                Activa
                            </span>
                            <span class="text-sm font-medium text-gray-500">2026</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Programa de Estímulos al Desempeño Docente</h3>
                        <p class="text-gray-600 text-sm mb-4">Convocatoria para reconocer la excelencia académica y el compromiso docente.</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Registro: 20 Dic - 05 Ene 2026</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>45 Solicitudes</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button class="flex-1 px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition text-sm font-medium">
                                Ver Detalles
                            </button>
                            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                Editar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Convocatoria Próxima -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 border-t-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                Próxima
                            </span>
                            <span class="text-sm font-medium text-gray-500">2026</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Becas de Investigación Científica</h3>
                        <p class="text-gray-600 text-sm mb-4">Apoyo para proyectos de investigación científica y tecnológica.</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Inicia: 01 Feb 2026</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Duración: 30 días</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button class="flex-1 px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition text-sm font-medium">
                                Ver Detalles
                            </button>
                            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                Editar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Convocatoria Cerrada -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 border-t-4 border-gray-400 opacity-75">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                                Cerrada
                            </span>
                            <span class="text-sm font-medium text-gray-500">2025</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Estímulos Docentes 2025</h3>
                        <p class="text-gray-600 text-sm mb-4">Convocatoria cerrada. Resultados publicados.</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Cerrado: 15 Dic 2025</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>120 Solicitudes</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                Ver Resultados
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DocenteLayout>
</template>
