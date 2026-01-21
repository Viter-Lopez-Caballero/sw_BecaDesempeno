<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import { CheckCircle, Clock, AlertCircle, Calendar } from 'lucide-vue-next';
</script>

<template>
    <LandingLayout>
        <Head title="Inicio" />

        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-[#000000] mb-4">Programa de Estímulos al Desempeño del Personal Docente</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Tecnológico Nacional de México - Reconociendo la excelencia académica y el compromiso docente de nuestra comunidad educativa.</p>
            </div>

            <div class="mb-20">
                <h2 class="text-2xl font-semibold text-center mb-10 text-gray-700">Etapas de la Convocatoria Activa</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 relative">
                        <div class="flex items-center absolute top-4 left-4">
                            <CheckCircle class="w-6 h-6 text-green-500" />
                        </div>
                        <div class="text-green-500 font-bold text-xl mb-2 text-center">01</div>
                        <h3 class="font-bold text-lg mb-2 text-center">Publicación</h3>
                        <p class="text-sm text-gray-500 text-center">Publicación de la Convocatoria.</p>
                        <div class="flex items-center justify-center mt-2 text-sm text-gray-500">
                             <Calendar class="w-4 h-4 mr-1" />
                             <span>12 Dic - 12 Ene 2025.</span>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500 relative">
                        <div class="flex items-center absolute top-4 left-4">
                            <Clock class="w-6 h-6 text-yellow-500" />
                        </div>
                        <div class="text-yellow-500 font-bold text-xl mb-2 text-center">02</div>
                        <h3 class="font-bold text-lg mb-2 text-center">Registro</h3>
                        <p class="text-sm text-gray-500 text-center">Periodo para Inscribirse.</p>
                        <div class="flex items-center justify-center mt-2 text-sm text-gray-500">
                             <Calendar class="w-4 h-4 mr-1" />
                             <span>20 Dic - 05 Ene 2025.</span>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-gray-400 relative">
                        <div class="flex items-center absolute top-4 left-4">
                            <AlertCircle class="w-6 h-6 text-gray-400" />
                        </div>
                        <div class="text-gray-400 font-bold text-xl mb-2 text-center">03</div>
                        <h3 class="font-bold text-lg mb-2 text-center">Evaluación</h3>
                        <p class="text-sm text-gray-500 text-center">Periodo de Revisión de Solicitudes.</p>
                        <div class="flex items-center justify-center mt-2 text-sm text-gray-500">
                             <Calendar class="w-4 h-4 mr-1" />
                             <span>06 Ene - 11 Ene 2026.</span>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-gray-400 relative">
                        <div class="flex items-center absolute top-4 left-4">
                            <AlertCircle class="w-6 h-6 text-gray-400" />
                        </div>
                        <div class="text-gray-400 font-bold text-xl mb-2 text-center">04</div>
                        <h3 class="font-bold text-lg mb-2 text-center">Resultados</h3>
                        <p class="text-sm text-gray-500 text-center">Publicación de Resultados.</p>
                         <div class="flex items-center justify-center mt-2 text-sm text-gray-500">
                             <Calendar class="w-4 h-4 mr-1" />
                             <span>12 Ene - 13 Ene 2026.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-center mb-10 text-gray-700">Convocatorias</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col">
                        <div class="h-48 bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-300 text-6xl">IMG</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Activa</span>
                                <span class="text-gray-500 text-sm font-medium">2026</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Beca de Excelencia Académica</h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-3">Apoyo dirigido a estudiantes con promedio superior a 9.5 en el ciclo anterior.</p>
                            
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-semibold mb-4 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Información sobre la convocatoria
                            </a>
                            
                            <button class="mt-auto w-full bg-[#002B5C] text-white py-2 rounded-lg hover:bg-blue-900 transition font-medium">
                                Ver más
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col opacity-90">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-6xl">IMG</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                             <div class="flex justify-between items-center mb-3">
                                <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-0.5 rounded">Cerrada</span>
                                <span class="text-gray-500 text-sm font-medium">2025</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Apoyo a Manutención</h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-3">Subsidio para estudiantes de bajos recursos económicos.</p>
                            
                            <a href="#" class="text-gray-500 hover:text-gray-700 text-sm font-semibold mb-4 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Información sobre la convocatoria
                            </a>
                            
                            <button class="mt-auto w-full border border-gray-300 text-gray-600 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                                Ver más
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col opacity-90">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-6xl">IMG</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                             <div class="flex justify-between items-center mb-3">
                                <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-0.5 rounded">Cerrada</span>
                                <span class="text-gray-500 text-sm font-medium">2025</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Apoyo a Manutención</h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-3">Subsidio para estudiantes de bajos recursos económicos.</p>
                            
                            <a href="#" class="text-gray-500 hover:text-gray-700 text-sm font-semibold mb-4 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Información sobre la convocatoria
                            </a>
                            
                            <button class="mt-auto w-full border border-gray-300 text-gray-600 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                                Ver más
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col opacity-90">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-6xl">IMG</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                             <div class="flex justify-between items-center mb-3">
                                <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-0.5 rounded">Cerrada</span>
                                <span class="text-gray-500 text-sm font-medium">2025</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Apoyo a Manutención</h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-3">Subsidio para estudiantes de bajos recursos económicos.</p>
                            
                            <a href="#" class="text-gray-500 hover:text-gray-700 text-sm font-semibold mb-4 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Información sobre la convocatoria
                            </a>
                            
                            <button class="mt-auto w-full border border-gray-300 text-gray-600 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                                Ver más
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col opacity-90">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-6xl">IMG</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                             <div class="flex justify-between items-center mb-3">
                                <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-0.5 rounded">Cerrada</span>
                                <span class="text-gray-500 text-sm font-medium">2025</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Apoyo a Manutención</h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-3">Subsidio para estudiantes de bajos recursos económicos.</p>
                            
                            <a href="#" class="text-gray-500 hover:text-gray-700 text-sm font-semibold mb-4 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Información sobre la convocatoria
                            </a>
                            
                            <button class="mt-auto w-full border border-gray-300 text-gray-600 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                                Ver más
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col opacity-90">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-6xl">IMG</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                             <div class="flex justify-between items-center mb-3">
                                <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-0.5 rounded">Cerrada</span>
                                <span class="text-gray-500 text-sm font-medium">2025</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Apoyo a Manutención</h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-3">Subsidio para estudiantes de bajos recursos económicos.</p>
                            
                            <a href="#" class="text-gray-500 hover:text-gray-700 text-sm font-semibold mb-4 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Información sobre la convocatoria
                            </a>
                            
                            <button class="mt-auto w-full border border-gray-300 text-gray-600 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                                Ver más
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>
