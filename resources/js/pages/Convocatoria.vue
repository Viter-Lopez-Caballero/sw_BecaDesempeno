<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
</script>

<template>
    <LandingLayout>
        <Head title="Convocatoria" />

        <!-- CONVOCATORIA Section -->
        <section class="bg-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-[#002B5C] mb-4">Información de Convocatoria</h2>
                    <p class="text-lg text-gray-500">Detalles y requisitos para participar en el proceso actual.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <!-- Left: Requirements -->
                    <div>
                        <h3 class="text-xl font-bold text-blue-900 mb-6 bg-blue-50 p-4 rounded-lg inline-block w-full border-l-4 border-blue-900">
                            ¿A quién va dirigido?
                        </h3>
                        <p class="text-gray-700 mb-8 leading-relaxed">
                            A estudiantes inscritos en instituciones públicas de educación superior que cumplan con los criterios de vulnerabilidad económica y excelencia académica establecidos en el reglamento vigente.
                        </p>

                        <h3 class="text-xl font-bold text-blue-900 mb-6 bg-blue-50 p-4 rounded-lg inline-block w-full border-l-4 border-blue-900">
                            Requisitos de Participación
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <span class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</span>
                                <span class="text-gray-700">Ser alumno regular de la institución.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</span>
                                <span class="text-gray-700">Tener un promedio mínimo general de 8.0.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</span>
                                <span class="text-gray-700">No contar con otro beneficio similar.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">4</span>
                                <span class="text-gray-700">Comprobante de ingresos familiares.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">5</span>
                                <span class="text-gray-700">Curp actualizada.</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">6</span>
                                <span class="text-gray-700">Carta de exposición de motivos.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Right: Downloads & Calendar -->
                    <div class="space-y-12">
                        <!-- Download Block -->
                        <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Descargar Convocatoria</h3>
                            <p class="text-gray-600 mb-6 text-sm">Obtén el documento completo con todas las bases.</p>
                            <button class="flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-100 transition shadow-sm font-semibold group">
                                <svg class="w-5 h-5 mr-2 text-red-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Descargar PDF Oficial
                            </button>
                        </div>

                        <!-- Calendar Simplified -->
                        <div>
                             <h3 class="text-xl font-bold text-gray-900 mb-6">Calendario</h3>
                             <div class="space-y-6 relative border-l-2 border-blue-200 ml-3 pl-8 pb-3">
                                <div class="relative">
                                    <span class="absolute -left-[41px] bg-blue-600 h-5 w-5 rounded-full border-4 border-white ring-2 ring-blue-100"></span>
                                    <h4 class="font-bold text-gray-900">Apertura</h4>
                                    <p class="text-sm text-gray-500">15 de Enero 2026</p>
                                </div>
                                <div class="relative">
                                    <span class="absolute -left-[41px] bg-blue-600 h-5 w-5 rounded-full border-4 border-white ring-2 ring-blue-100"></span>
                                    <h4 class="font-bold text-gray-900">Cierre</h4>
                                    <p class="text-sm text-gray-500">28 de Febrero 2026</p>
                                </div>
                                <div class="relative">
                                    <span class="absolute -left-[41px] bg-green-500 h-5 w-5 rounded-full border-4 border-white ring-2 ring-green-100"></span>
                                    <h4 class="font-bold text-gray-900">Resultados</h4>
                                    <p class="text-sm text-gray-500">15 de Marzo 2026</p>
                                </div>
                             </div>
                        </div>

                        <!-- CTA -->
                         <div class="bg-[#002B5C] p-8 rounded-2xl text-center text-white">
                            <h3 class="text-xl font-bold mb-2">¿Listo para participar?</h3>
                            <p class="text-blue-200 mb-6 text-sm">Si cumples con los requisitos, inicia tu trámite hoy mismo.</p>
                            <Link :href="route('login')" class="inline-block bg-white text-[#002B5C] font-bold px-8 py-3 rounded-full hover:bg-blue-50 transition shadow-lg transform hover:-translate-y-1">
                                Iniciar en el Sistema
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>
