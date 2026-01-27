<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Rúbrica" />

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Rúbricas de Evaluación</h1>
                        <p class="text-gray-600 mt-2">Criterios de evaluación para solicitudes de becas y estímulos.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Nueva Rúbrica
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                        <h3 class="text-xl font-bold mb-2">Rúbrica de Investigación</h3>
                        <p class="text-blue-100 text-sm">Evaluación de proyectos de investigación científica</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                                <div>
                                    <p class="font-medium text-gray-900">Calidad de la propuesta</p>
                                    <p class="text-sm text-gray-500">Originalidad y viabilidad</p>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 font-semibold rounded-full text-sm">40%</span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                                <div>
                                    <p class="font-medium text-gray-900">Metodología</p>
                                    <p class="text-sm text-gray-500">Enfoque y herramientas</p>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 font-semibold rounded-full text-sm">30%</span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                                <div>
                                    <p class="font-medium text-gray-900">Impacto esperado</p>
                                    <p class="text-sm text-gray-500">Relevancia y beneficios</p>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 font-semibold rounded-full text-sm">20%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">Experiencia del equipo</p>
                                    <p class="text-sm text-gray-500">Trayectoria y capacidad</p>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 font-semibold rounded-full text-sm">10%</span>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-2">
                            <button class="flex-1 px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition text-sm font-medium">
                                Ver Completa
                            </button>
                            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                Editar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
                        <h3 class="text-xl font-bold mb-2">Rúbrica Docente</h3>
                        <p class="text-green-100 text-sm">Evaluación del desempeño docente</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                                <div>
                                    <p class="font-medium text-gray-900">Calidad académica</p>
                                    <p class="text-sm text-gray-500">Preparación y dominio</p>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 font-semibold rounded-full text-sm">35%</span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                                <div>
                                    <p class="font-medium text-gray-900">Evaluaciones estudiantiles</p>
                                    <p class="text-sm text-gray-500">Opinión de alumnos</p>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 font-semibold rounded-full text-sm">25%</span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                                <div>
                                    <p class="font-medium text-gray-900">Productividad académica</p>
                                    <p class="text-sm text-gray-500">Publicaciones y ponencias</p>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 font-semibold rounded-full text-sm">25%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">Actividades complementarias</p>
                                    <p class="text-sm text-gray-500">Tutorías y extensión</p>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 font-semibold rounded-full text-sm">15%</span>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-2">
                            <button class="flex-1 px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition text-sm font-medium">
                                Ver Completa
                            </button>
                            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Escala de Calificación</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-2xl font-bold text-red-700">1</p>
                        <p class="text-sm text-red-600 mt-1">Insuficiente</p>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <p class="text-2xl font-bold text-orange-700">2</p>
                        <p class="text-sm text-orange-600 mt-1">Regular</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-2xl font-bold text-yellow-700">3</p>
                        <p class="text-sm text-yellow-600 mt-1">Bueno</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-2xl font-bold text-blue-700">4</p>
                        <p class="text-sm text-blue-600 mt-1">Muy Bueno</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-2xl font-bold text-green-700">5</p>
                        <p class="text-sm text-green-600 mt-1">Excelente</p>
                    </div>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
