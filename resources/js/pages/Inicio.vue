<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import Pagination from '@/Shared/Pagination.vue';
import { CheckCircle, Clock, AlertCircle, Calendar, ClipboardCheck, Award } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
    convocatorias: Object,
});

const etapas = ref([
    {
        id: 1,
        numero: '01',
        titulo: 'Publicación',
        descripcion: 'Publicación de la Convocatoria.',
        fechas: '12 Dic - 12 Ene 2025',
        color: '#1B396A',
        icono: 'CheckCircle',
        isActive: true
    },
    {
        id: 2,
        numero: '02',
        titulo: 'Registro',
        descripcion: 'Periodo para Inscribirse.',
        fechas: '20 Dic - 05 Ene 2025',
        color: '#10A558',
        icono: 'Clock',
        isActive: true
    },
    {
        id: 3,
        numero: '03',
        titulo: 'Evaluación',
        descripcion: 'Periodo de Revisión de Solicitudes.',
        fechas: '06 Ene - 11 Ene 2026',
        color: '#E9C81F',
        icono: 'ClipboardCheck',
        isActive: true
    },
    {
        id: 4,
        numero: '04',
        titulo: 'Resultados',
        descripcion: 'Publicación de Resultados.',
        fechas: '12 Ene - 13 Ene 2026',
        color: '#0F172A',
        icono: 'Award',
        isActive: true
    }
]);

const getIconComponent = (iconName) => {
    const icons = {
        CheckCircle,
        Clock,
        ClipboardCheck,
        Award
    };
    return icons[iconName];
};

const convocatoriasEjemplo = ref([
    {
        id: 1,
        titulo: 'Convocatoria de Estímulos al Desempeño Docente 2026',
        nombre: 'Convocatoria de Estímulos al Desempeño Docente 2026',
        descripcion: 'Programa diseñado para reconocer y estimular la excelencia académica, dedicación y permanencia de los profesores del Tecnológico Nacional de México.',
        anio: 2026,
        estado: 'activa',
        imagen: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&h=400&fit=crop'
    },
    {
        id: 2,
        titulo: 'Convocatoria de Estímulos 2025',
        nombre: 'Convocatoria de Estímulos 2025',
        descripcion: 'Reconocimiento al desempeño docente durante el año 2025. Esta convocatoria ya fue cerrada y se publicaron los resultados.',
        anio: 2025,
        estado: 'cerrada',
        imagen: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop'
    },
    {
        id: 3,
        titulo: 'Convocatoria de Estímulos 2024',
        nombre: 'Convocatoria de Estímulos 2024',
        descripcion: 'Programa de reconocimiento a la excelencia docente 2024. Convocatoria finalizada con resultados publicados.',
        anio: 2024,
        estado: 'cerrada',
        imagen: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop'
    },
    {
        id: 4,
        titulo: 'Convocatoria Especial Investigación 2024',
        nombre: 'Convocatoria Especial Investigación 2024',
        descripcion: 'Estímulos especiales para docentes con proyectos de investigación destacados. Convocatoria cerrada.',
        anio: 2024,
        estado: 'cerrada',
        imagen: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop'
    },
    {
        id: 5,
        titulo: 'Convocatoria de Estímulos 2023',
        nombre: 'Convocatoria de Estímulos 2023',
        descripcion: 'Reconocimiento al desempeño docente del año 2023. Proceso completado con resultados oficiales publicados.',
        anio: 2023,
        estado: 'cerrada',
        imagen: 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&h=400&fit=crop'
    }
]);

// Paginación
const currentPage = ref(1);
const perPage = 3;

const totalPages = computed(() => Math.ceil(convocatoriasEjemplo.value.length / perPage));

const convocatoriasPaginadas = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;
    return convocatoriasEjemplo.value.slice(start, end);
});

const paginationLinks = computed(() => {
    const links = [];
    
    // Link anterior
    links.push({
        url: currentPage.value > 1 ? `?page=${currentPage.value - 1}` : null,
        label: '&laquo; Previous',
        active: false
    });
    
    // Links de páginas
    for (let i = 1; i <= totalPages.value; i++) {
        links.push({
            url: `?page=${i}`,
            label: i.toString(),
            active: i === currentPage.value
        });
    }
    
    // Link siguiente
    links.push({
        url: currentPage.value < totalPages.value ? `?page=${currentPage.value + 1}` : null,
        label: 'Next &raquo;',
        active: false
    });
    
    return links;
});

// Detectar cambios en la URL
if (typeof window !== 'undefined') {
    const urlParams = new URLSearchParams(window.location.search);
    const page = parseInt(urlParams.get('page')) || 1;
    currentPage.value = page;
}
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

        <!-- Sección de Estadísticas -->
        <!-- <section class="py-16 px-4 sm:px-6 lg:px-8 bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#2c5282] to-[#3d5a80] mb-4 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900 mb-2">1,500+</h3>
                        <p class="text-gray-600 text-sm">Docentes Participantes</p>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#10A558] to-[#0d8847] mb-4 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900 mb-2">126</h3>
                        <p class="text-gray-600 text-sm">Instituciones del TecNM</p>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#E9C81F] to-[#d4b41a] mb-4 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900 mb-2">98%</h3>
                        <p class="text-gray-600 text-sm">Tasa de Satisfacción</p>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- Sección Sobre el Programa -->
        <!-- <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 to-white"> -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
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
                        <div class="space-y-4">
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
                            <div class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#10A558]">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-[#10A558]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">Impulso al Desarrollo Profesional</h3>
                                    <p class="text-gray-600 text-sm">Promovemos la formación continua y mejora de competencias docentes.</p>
                                </div>
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
        <section class="relative py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#1B396A] via-[#2B4A7E] to-[#3B5C92] overflow-hidden">
            <!-- Patrón de fondo tecnológico -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: linear-gradient(#5B8DEE 2px, transparent 2px), linear-gradient(90deg, #5B8DEE 2px, transparent 2px); background-size: 40px 40px;"></div>
                <div class="absolute top-10 left-10 w-64 h-64 bg-blue-400 rounded-full blur-3xl opacity-20"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-cyan-400 rounded-full blur-3xl opacity-20"></div>
            </div>
            
            <div class="max-w-7xl mx-auto relative z-10">
                <h2 class="text-3xl font-bold text-center mb-12 text-white">Etapas de la Convocatoria Activa</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div 
                        v-for="etapa in etapas" 
                        :key="etapa.id"
                        class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-lg border-l-4 hover:shadow-xl transition-all duration-300"
                        :class="etapa.isActive ? `border-[${etapa.color}]` : 'border-[#AEAEAE]'"
                        :style="{ borderLeftColor: etapa.isActive ? etapa.color : '#AEAEAE' }"
                    >
                        <div class="flex-shrink-0">
                            <component 
                                :is="getIconComponent(etapa.icono)" 
                                class="w-8 h-8" 
                                :style="{ color: etapa.isActive ? etapa.color : '#AEAEAE' }"
                            />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span 
                                    class="font-bold text-2xl" 
                                    :style="{ color: etapa.isActive ? etapa.color : '#AEAEAE' }"
                                >
                                    {{ etapa.numero }}
                                </span>
                            </div>
                            <h3 class="font-bold text-base mb-2" :class="etapa.isActive ? 'text-gray-900' : 'text-[#AEAEAE]'">{{ etapa.titulo }}</h3>
                            <p class="text-xs mb-3" :class="etapa.isActive ? 'text-gray-600' : 'text-[#AEAEAE]'">{{ etapa.descripcion }}</p>
                            <div class="flex items-center text-xs" :class="etapa.isActive ? 'text-gray-500' : 'text-[#AEAEAE]'">
                                <Calendar class="w-3 h-3 mr-1" />
                                <span>{{ etapa.fechas }}</span>
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
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div 
                        v-for="convocatoria in convocatoriasPaginadas" 
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
                
                <!-- Paginación -->
                <div class="mt-8">
                    <Pagination 
                        :links="paginationLinks" 
                        :total="convocatoriasEjemplo.length"
                        :from="(currentPage - 1) * perPage + 1"
                        :to="Math.min(currentPage * perPage, convocatoriasEjemplo.length)"
                        :show-total="false"
                    />
                </div>
            </div>
        </section>

        <!-- Sección de Beneficios -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <span class="text-[#2c5282] font-semibold text-sm uppercase tracking-wider">Ventajas del Programa</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3">
                        Beneficios para los Participantes
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Beneficio 1 -->
                    <div class="group relative bg-gradient-to-br from-blue-50 to-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-blue-100 hover:border-[#2c5282]">
                        <div class="absolute -top-6 left-8 inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-[#2c5282] to-[#3d5a80] shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3 group-hover:text-[#2c5282] transition-colors">Estímulos Económicos</h3>
                        <p class="text-gray-600 leading-relaxed">Reconocimiento monetario proporcional a tu desempeño y dedicación académica.</p>
                    </div>

                    <!-- Beneficio 2 -->
                    <div class="group relative bg-gradient-to-br from-green-50 to-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-green-100 hover:border-[#10A558]">
                        <div class="absolute -top-6 left-8 inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-[#10A558] to-[#0d8847] shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3 group-hover:text-[#10A558] transition-colors">Reconocimiento Institucional</h3>
                        <p class="text-gray-600 leading-relaxed">Prestigio y valoración oficial por tu contribución al sistema educativo nacional.</p>
                    </div>

                    <!-- Beneficio 3 -->
                    <div class="group relative bg-gradient-to-br from-yellow-50 to-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-yellow-100 hover:border-[#E9C81F]">
                        <div class="absolute -top-6 left-8 inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-[#E9C81F] to-[#d4b41a] shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3 group-hover:text-[#E9C81F] transition-colors">Desarrollo Profesional</h3>
                        <p class="text-gray-600 leading-relaxed">Incentivo para formación continua, investigación y actividades de mejora educativa.</p>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>
