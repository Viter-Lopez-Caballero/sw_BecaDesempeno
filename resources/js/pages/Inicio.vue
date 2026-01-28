<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import Pagination from '@/Shared/Pagination.vue';
import { CheckCircle, Clock, AlertCircle, Calendar, ClipboardCheck, Award } from 'lucide-vue-next';

defineProps({
    convocatorias: Object,
});
</script>

<template>
    <LandingLayout>
        <Head title="Inicio" />

        <!-- Hero Section con imagen de fondo -->
        <section class="relative bg-gradient-to-br from-[#2c5282] via-[#3d5a80] to-[#4a6fa5] py-24 md:py-32 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <!-- Imagen de fondo -->
            <div class="absolute inset-0 z-0">
                <img 
                    src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=1920&h=1080&fit=crop&q=80" 
                    alt="Equipo de profesionales" 
                    class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-[#2c5282]/80 via-[#3d5a80]/75 to-[#4a6fa5]/70"></div>
            </div>
            
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight drop-shadow-lg">
                        Programa de Estímulos al Desempeño<br />del Personal Docente
                    </h1>
                    <p class="text-lg md:text-xl text-blue-50 max-w-3xl mx-auto leading-relaxed drop-shadow-md">
                        Tecnológico Nacional de México - Reconociendo la excelencia académica y el compromiso docente de nuestra comunidad educativa.
                    </p>
                </div>
            </div>
        </section>

        <!-- Sección Sobre el Programa -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 to-white">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Texto -->
                    <div class="space-y-6">
                        <div>
                            <span class="text-[#2c5282] font-semibold text-sm uppercase tracking-wider">Nuestro Propósito</span>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3 mb-6">
                                ¿Para qué sirve este programa?
                            </h2>
                        </div>
                        <p class="text-lg text-gray-700 leading-relaxed">
                            El Programa de Estímulos al Desempeño del Personal Docente, tiene como propósito, impulsar y reconocer a los profesores de los Institutos Tecnológicos y Centros Especializados del Sistema Nacional de Institutos Tecnológicos, por la calidad en el desempeño, la dedicación y la permanencia en las actividades de la docencia.
                        </p>
                        <div class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#2c5282]">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-[#2c5282]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Reconocimiento a la Excelencia</h3>
                                <p class="text-gray-600 text-sm">Valoramos y premiamos el compromiso y dedicación de nuestros docentes.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Imagen -->
                    <div class="relative">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                            <img 
                                src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop&q=80" 
                                alt="Profesionales colaborando" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <!-- Decoración -->
                        <div class="absolute -bottom-6 -right-6 w-72 h-72 bg-[#2c5282]/10 rounded-full -z-10"></div>
                        <div class="absolute -top-6 -left-6 w-48 h-48 bg-blue-200/30 rounded-full -z-10"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Etapas con fondo tecnológico -->
        <section class="relative py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#2B4A7E] via-[#3d6398] to-[#4e6b91] overflow-hidden">
            <!-- Patrón de fondo tecnológico -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: linear-gradient(#4a90e2 1px, transparent 1px), linear-gradient(90deg, #4a90e2 1px, transparent 1px); background-size: 50px 50px;"></div>
                <div class="absolute top-10 left-10 w-64 h-64 bg-blue-400 rounded-full blur-3xl opacity-20"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-cyan-400 rounded-full blur-3xl opacity-20"></div>
            </div>
            
            <div class="max-w-7xl mx-auto relative z-10">
                <h2 class="text-3xl font-bold text-center mb-12 text-white">Etapas de la Convocatoria Activa</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-lg border-l-4 border-[#10A558] hover:shadow-xl transition-all duration-300">
                        <div class="flex-shrink-0">
                            <CheckCircle class="w-8 h-8 text-[#10A558]" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[#10A558] font-bold text-2xl">01</span>
                            </div>
                            <h3 class="font-bold text-base text-gray-900 mb-2">Publicación</h3>
                            <p class="text-xs text-gray-600 mb-3">Publicación de la Convocatoria.</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <Calendar class="w-3 h-3 mr-1" />
                                <span>12 Dic - 12 Ene 2025</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-lg border-l-4 border-[#E9C81F] hover:shadow-xl transition-all duration-300">
                        <div class="flex-shrink-0">
                            <Clock class="w-8 h-8 text-[#E9C81F]" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[#E9C81F] font-bold text-2xl">02</span>
                            </div>
                            <h3 class="font-bold text-base text-gray-900 mb-2">Registro</h3>
                            <p class="text-xs text-gray-600 mb-3">Periodo para Inscribirse.</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <Calendar class="w-3 h-3 mr-1" />
                                <span>20 Dic - 05 Ene 2025</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-lg border-l-4 border-[#B03A2E] hover:shadow-xl transition-all duration-300">
                        <div class="flex-shrink-0">
                            <ClipboardCheck class="w-8 h-8 text-[#B03A2E]" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[#B03A2E] font-bold text-2xl">03</span>
                            </div>
                            <h3 class="font-bold text-base text-gray-900 mb-2">Evaluación</h3>
                            <p class="text-xs text-gray-600 mb-3">Periodo de Revisión de Solicitudes.</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <Calendar class="w-3 h-3 mr-1" />
                                <span>06 Ene - 11 Ene 2026</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-lg border-l-4 border-[#1B396A] hover:shadow-xl transition-all duration-300">
                        <div class="flex-shrink-0">
                            <Award class="w-8 h-8 text-[#1B396A]" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[#1B396A] font-bold text-2xl">04</span>
                            </div>
                            <h3 class="font-bold text-base text-gray-900 mb-2">Resultados</h3>
                            <p class="text-xs text-gray-600 mb-3">Publicación de Resultados.</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <Calendar class="w-3 h-3 mr-1" />
                                <span>12 Ene - 13 Ene 2026</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Convocatorias con fondo gris claro -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-4 text-gray-900">Convocatorias</h2>
                <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
                    Consulta las convocatorias disponibles y mantente informado sobre los procesos de estímulos al desempeño docente.
                </p>
                
                <div v-if="convocatorias?.data?.length" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div 
                        v-for="convocatoria in convocatorias.data" 
                        :key="convocatoria.id"
                        :class="[
                            'rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 flex flex-col',
                            convocatoria.estado === 'activa' 
                                ? 'bg-white hover:scale-105 group' 
                                : 'bg-white shadow-md border border-gray-200 hover:shadow-lg opacity-80'
                        ]"
                    >
                        <div class="h-56 bg-gradient-to-br overflow-hidden relative" :class="convocatoria.estado === 'activa' ? 'from-gray-700 via-gray-600 to-gray-500' : 'from-gray-400 via-gray-300 to-gray-200'">
                            <img 
                                :src="convocatoria.imagen || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&h=400&fit=crop'" 
                                alt="Imagen de convocatoria" 
                                :class="[
                                    'w-full h-full object-cover transition-transform duration-500',
                                    convocatoria.estado === 'activa' ? 'group-hover:scale-110' : 'opacity-60'
                                ]"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t" :class="convocatoria.estado === 'activa' ? 'from-black/40' : 'from-black/20'"></div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex justify-between items-center mb-4">
                                <span 
                                    :class="[
                                        'text-xs font-bold px-3 py-1.5 rounded-full shadow-md',
                                        convocatoria.estado === 'activa' 
                                            ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' 
                                            : 'bg-gray-200 text-gray-700'
                                    ]"
                                >
                                    {{ convocatoria.estado === 'activa' ? 'Activa' : 'Cerrada' }}
                                </span>
                                <span class="text-sm font-semibold bg-gray-100 px-3 py-1 rounded-full" :class="convocatoria.estado === 'activa' ? 'text-gray-600' : 'text-gray-500'">
                                    {{ convocatoria.anio || new Date().getFullYear() }}
                                </span>
                            </div>
                            <h3 
                                :class="[
                                    'text-xl font-bold mb-3',
                                    convocatoria.estado === 'activa' 
                                        ? 'text-gray-900 group-hover:text-[#2c5282] transition-colors' 
                                        : 'text-gray-800'
                                ]"
                            >
                                {{ convocatoria.titulo || convocatoria.nombre || 'Convocatoria' }}
                            </h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-3 leading-relaxed">
                                {{ convocatoria.descripcion || 'Descripción de la convocatoria.' }}
                            </p>
                            
                            <Link 
                                :href="`/convocatorias/${convocatoria.id}`" 
                                :class="[
                                    'text-sm font-semibold mb-4 inline-flex items-center group/link',
                                    convocatoria.estado === 'activa' 
                                        ? 'text-[#2c5282] hover:text-[#1e3a5f]' 
                                        : 'text-gray-500 hover:text-gray-700'
                                ]"
                            >
                                <svg class="w-4 h-4 mr-2" :class="convocatoria.estado === 'activa' ? 'group-hover/link:translate-x-1 transition-transform' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Información Acerca de la Convocatoria
                            </Link>
                            
                            <Link
                                :href="`/convocatorias/${convocatoria.id}`"
                                :class="[
                                    'mt-auto w-full py-3 rounded-xl transition-all font-semibold cursor-pointer text-center',
                                    convocatoria.estado === 'activa'
                                        ? 'bg-gradient-to-r from-[#2c5282] to-[#3d5a80] text-white hover:from-[#1e3a5f] hover:to-[#2c5282] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5'
                                        : 'border-2 border-gray-300 text-gray-600 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                Ver más
                            </Link>
                        </div>
                    </div>
                </div>
                
                <!-- Mensaje cuando no hay convocatorias -->
                <div v-else class="text-center py-12">
                    <p class="text-gray-500 text-lg">No hay convocatorias disponibles en este momento.</p>
                </div>
                
                <!-- Paginación -->
                <div v-if="convocatorias?.data?.length" class="mt-8">
                    <Pagination 
                        :links="convocatorias.links" 
                        :total="convocatorias.total"
                        :from="convocatorias.from"
                        :to="convocatorias.to"
                        :show-total="false"
                    />
                </div>
            </div>
        </section>
    </LandingLayout>
</template>
