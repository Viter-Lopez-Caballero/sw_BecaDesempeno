<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
</script>

<template>
    <LandingLayout>

        <Head title="Convocatoria" />

        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-[#1B396A] via-[#2B4A7E] to-[#3B5C92] py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-4xl md:text-4xl font-bold text-white mb-4">Información de Convocatoria</h1>
                <p class="text-lg md:text-lg text-gray-100 max-w-3xl mx-auto">Detalles y requisitos para participar en
                    el proceso actual para los Institutos Tecnológicos Federales y Centros</p>
            </div>
        </section>

        <!-- CONVOCATORIA Section -->
        <section class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- LADO IZQUIERDO: ¿A quién va dirigido? y Bases -->
                    <div class="space-y-8">
                        <!-- ¿A quién va dirigido? -->
                        <div
                            class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#1B396A]">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-[#1B396A]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-xl">¿A quién va dirigido?</h3>
                            </div>
                        </div>
                        <p class="text-base text-gray-700 leading-relaxed">
                            Personal docente de tiempo completo (40 hrs) adscrito a los Institutos y Centros Federales
                            del TecNM que imparten el 80% del año lectivo 2024 y cumplan con el 80% durante el primer
                            semestre de 2025.
                        </p>

                        <!-- Bases de Participación -->
                        <div
                            class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#10A558]">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-[#10A558]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-xl">Bases de Participación</h3>
                            </div>
                        </div>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <span
                                        class="flex-shrink-0 rounded-full bg-white h-8 w-8 text-[#10A558] flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1.</span>
                                    <span class="text-gray-700 text-sm">Haber cubierto la carga horaria reglamentaria
                                        frente a grupo durante los dos semestres de 2024 y en el primer semestre de
                                        2026.</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="flex-shrink-0 rounded-full bg-white h-8 w-8 text-[#10A558] flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2.</span>
                                    <span class="text-gray-700 text-sm">Personal docente titular en subdirección,
                                        jefatura de departamento o área administrativa (50% docencia, 50% funciones
                                        directivas).</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="flex-shrink-0 rounded-full bg-white h-8 w-8 text-[#10A558] flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3.</span>
                                    <span class="text-gray-700 text-sm">Personal docente titular en Docencia e
                                        Investigación con mínimo 20 horas-semana en los últimos dos semestres de 2024 y
                                        primer semestre de 2026.</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="flex-shrink-0 rounded-full bg-white h-8 w-8 text-[#10A558] flex items-center justify-center text-sm font-bold mr-3 mt-0.5">4.</span>
                                    <span class="text-gray-700 text-sm">Personal docente que haya disfrutado licencia
                                        sin goce de sueldo según Artículo 12 de los Lineamientos del Programa.</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="flex-shrink-0 rounded-full bg-white h-8 w-8 text-[#10A558] flex items-center justify-center text-sm font-bold mr-3 mt-0.5">5.</span>
                                    <span class="text-gray-700 text-sm">Personal con nombramiento de tiempo parcial no
                                        podrá participar.</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="flex-shrink-0 rounded-full bg-white h-8 w-8 text-[#10A558] flex items-center justify-center text-sm font-bold mr-3 mt-0.5">6.</span>
                                    <span class="text-gray-700 text-sm">Integrar expediente virtual completo según
                                        período establecido.</span>
                                </li>
                            </ul>

                            <div class="bg-white p-6 rounded-2xl shadow-md">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">¿Listo para participar?</h3>
                            <p class="text-gray-600 mb-6 text-sm">
                                Si cumples con los requisitos, inicia tu trámite hoy mismo.
                            </p>
                            <Link
                                :href="route('login')"
                                class="mx-auto block w-full sm:w-64 text-center
                                    bg-[#1B396A] text-white font-bold
                                    px-8 py-3 rounded-xl hover:bg-[#1B396A]
                                    transition shadow-lg transform hover:-translate-y-1"
                            >
                                Iniciar en el Sistema
                            </Link>
                        </div>
                    </div>

                    <!-- LADO DERECHO: Card Convocatoria y Calendario -->
                    <div class="space-y-8">
                        <!-- Card Principal de Convocatoria 2025 -->
                        <div
                            class="bg-gradient-to-br from-[#2d4a7c] via-[#3a5a8c] to-[#4a6a9c] text-white rounded-3xl p-8 shadow-md">
                            <h2 class="text-3xl font-bold mb-4 text-center">Convocatoria 2026</h2>
                            <p class="text-base leading-relaxed mb-8 text-blue-50">
                                El Tecnológico Nacional de México convoca a su personal docente a participar en el
                                Programa de Estímulos al Desempeño del Personal Docente correspondiente al ejercicio
                                2026.
                            </p>
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    class="cursor-pointer px-6 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 hover:bg-gray-100 font-medium transition shadow-lg transform hover:-translate-y-1">
                                    Descargar Convocatoria PDF
                                </button>
                            </div>
                        </div>

                        <!-- Calendario de Actividades -->
                        <div class="bg-white p-8 rounded-2xl shadow-md">
                            <h3 class="text-2xl font-bold text-gray-900 mb-8">Calendario de Actividades</h3>

                            <!-- Fase 1 -->
                            <div class="mb-10">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-12 w-12 rounded-full bg-[#1B396A] flex items-center justify-center shadow-md">
                                            <span class="text-white font-bold text-sm">1</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Fase 1</p>
                                        <h4 class="text-lg font-bold text-gray-900 mb-1">Publicación de Convocatoria
                                        </h4>
                                        <p class="text-sm text-gray-600">Difusión oficial de las bases y lineamientos
                                            del programa.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Fase 2 -->
                            <div class="mb-10 relative">
                                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 -mt-16"></div>
                                <div class="flex items-start relative z-10">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-12 w-12 rounded-full bg-[#10A558] flex items-center justify-center shadow-md">
                                            <span class="text-white font-bold text-sm">2</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Fase 2</p>
                                        <h4 class="text-lg font-bold text-gray-900 mb-1">Registro y Carga de Documentos
                                        </h4>
                                        <p class="text-sm text-gray-600">Período para registro en el sistema y carga de
                                            evidencias.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Fase 3 -->
                            <div class="mb-10 relative">
                                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 -mt-14"></div>
                                <div class="flex items-start relative z-10">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-12 w-12 rounded-full bg-[#E9C81F] flex items-center justify-center shadow-md">
                                            <span class="text-white font-bold text-sm">3</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Fase 3</p>
                                        <h4 class="text-lg font-bold text-gray-900 mb-1">Evaluación y Dictaminación</h4>
                                        <p class="text-sm text-gray-600">Comisiones evaluadoras revisan y valoran las
                                            solicitudes.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Fase 4 -->
                            <div class="mb-5 relative">
                                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 -mt-10"></div>
                                <div class="flex items-start relative z-10">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-12 w-12 rounded-full bg-[#1B396A] flex items-center justify-center shadow-md">
                                            <span class="text-white font-bold text-sm">4</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Fase 4</p>
                                        <h4 class="text-lg font-bold text-gray-900 mb-1">Publicación de Resultados</h4>
                                        <p class="text-sm text-gray-600">Anuncio oficial de docentes beneficiados con el
                                            estímulo.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>
