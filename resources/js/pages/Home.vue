<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import Pagination from '@/Shared/Pagination.vue';
import { CheckCircle, Clock, AlertCircle, Calendar, ClipboardCheck, Award, FileText } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    announcements: Object,
    timelineAnnouncement: Object,
    canLogin: Boolean,
    canRegister: Boolean,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// Helper para parsear fecha "YYYY-MM-DD", "YYYY-MM-DD HH:MM:SS" o ISO "YYYY-MM-DDTHH:mm:ss"
const parseDateLocal = (dateString) => {
    if (!dateString) return null;
    // Tomamos los primeros 10 caracteres (YYYY-MM-DD)
    const datePart = dateString.substring(0, 10);
    const [year, month, day] = datePart.split('-').map(Number);
    
    if (!year || !month || !day) return null; // Validación básica
    
    return new Date(year, month - 1, day);
};

// Helper para determinar la fase actual de una convocatoria basada en su calendario
const getFase = (convocatoria) => {
    // 1. Estado Cerrada
    if (convocatoria.status !== 'activa') {
        return { 
            nombre: 'Cerrada', 
            color: 'bg-red-100 text-red-800', 
            canRegister: false 
        };
    }

    // 2. Estado Activa
    // Calculamos si está en periodo de registro para habilitar el botón
    let canRegister = false;
    
    if (convocatoria.calendar) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Usar parseDateLocal para evitar desfases de zona horaria (UTC vs Local)
        const regInicio = parseDateLocal(convocatoria.calendar.registration_start);
        const regFin = parseDateLocal(convocatoria.calendar.registration_end);
        
        // Comparación inclusiva
        if (regInicio && regFin && today >= regInicio && today <= regFin) {
            canRegister = true;
        }
    }

    // Retornamos siempre "Activa" si el estado es activa
    return { 
        nombre: 'Activa', 
        color: 'bg-green-100 text-green-800', 
        canRegister: canRegister 
    };
};

const etapas = computed(() => {
    // Usamos la convocatoria específica para el timeline (Activa o Pendiente)
    const convocatoria = props.timelineAnnouncement ? (props.timelineAnnouncement.data || props.timelineAnnouncement) : null;
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const stages = [
        {
            id: 1,
            numero: '01',
            titulo: 'Publicación',
            descripcion: 'Publicación de la Convocatoria.',
            icono: 'CheckCircle',
            dateStartKey: 'publication_start',
            color: '#1B396A'
        },
        {
            id: 2,
            numero: '02',
            titulo: 'Registro',
            descripcion: 'Periodo para Inscribirse.',
            icono: 'Clock',
            dateStartKey: 'registration_start',
            dateEndKey: 'registration_end',
            color: '#10A558'
        },
        {
            id: 3,
            numero: '03',
            titulo: 'Evaluación',
            descripcion: 'Periodo de Revisión.',
            icono: 'ClipboardCheck',
            dateStartKey: 'evaluation_start',
            color: '#E9C81F'
        },
        {
            id: 4,
            numero: '04',
            titulo: 'Resultados',
            descripcion: 'Publicación de Resultados.',
            icono: 'Award',
            dateStartKey: 'results_start',
            color: '#2B6CB0' // Azul medio — igual que en AnnouncementCalendar
        }
    ];

    if (!convocatoria || !convocatoria.calendar) {
        return stages.map(s => ({ 
            ...s, 
            fechas: 'Por definir', 
            isActive: false,
            isFuture: true
        }));
    }

    return stages.map(stage => {
        // Usar parseDateLocal y acceder a calendar (inglés)
        const start = parseDateLocal(convocatoria.calendar[stage.dateStartKey]);
        
        const isPastOrCurrent = start ? today >= start : false;
        
        let fechasStr = start ? start.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 'Por definir';
        
        if (stage.dateEndKey) {
            const end = parseDateLocal(convocatoria.calendar[stage.dateEndKey]);
            if (end) {
                fechasStr += ` - ${end.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' })}`;
            }
        }

        return {
            ...stage,
            fechas: fechasStr,
            isActive: isPastOrCurrent, // Controls color vs opacity
            isFuture: !isPastOrCurrent
        };
    });
});

const getIconComponent = (iconName) => {
    const icons = {
        CheckCircle,
        Clock,
        ClipboardCheck,
        Award,
        FileText
    };
    return icons[iconName];
};
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
                            El Programa de Estímulos al Desempeño del Personal Docente, tiene como propósito, impulsar y reconocer a los profesores de los Institutos Tecnológicos y Centros Especializados del Tecnológico Nacional de México, por la excelencia en el desempeño, la dedicación y la permanencia en las actividades de la docencia.
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
                <h2 class="text-3xl font-bold text-center mb-12 text-white">Etapas de la Convocatoria</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div 
                        v-for="etapa in etapas" 
                        :key="etapa.id"
                        class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-lg border-l-4 hover:shadow-xl transition-all duration-300"
                        :class="[
                             etapa.isActive ? `border-[${etapa.color}]` : 'border-gray-300 opacity-60 bg-gray-50',
                        ]"
                        :style="{ borderLeftColor: etapa.isActive ? etapa.color : '#D1D5DB' }"
                    >
                        <div class="flex-shrink-0">
                            <component 
                                :is="getIconComponent(etapa.icono)" 
                                class="w-8 h-8" 
                                :style="{ color: etapa.isActive ? etapa.color : '#9CA3AF' }"
                            />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span 
                                    class="font-bold text-2xl" 
                                    :style="{ color: etapa.isActive ? etapa.color : '#9CA3AF' }"
                                >
                                    {{ etapa.numero }}
                                </span>
                            </div>
                            <h3 class="font-bold text-base mb-2" :class="etapa.isActive ? 'text-gray-900' : 'text-gray-500'">{{ etapa.titulo }}</h3>
                            <p class="text-xs mb-3" :class="etapa.isActive ? 'text-gray-600' : 'text-gray-400'">{{ etapa.descripcion }}</p>
                             <div class="flex items-center text-xs" :class="etapa.isActive ? 'text-gray-500' : 'text-gray-400'">
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
                    Consulta las convocatorias disponibles y su estado actual.
                </p>
                
                <div v-if="announcements.data && announcements.data.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div 
                        v-for="convocatoria in announcements.data" 
                        :key="convocatoria.id"
                        :class="[
                            'rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 flex flex-col',
                            convocatoria.status === 'activa' 
                                ? 'bg-white hover:scale-105 group' 
                                : 'bg-white shadow-md border border-gray-200 hover:shadow-lg opacity-80'
                        ]"
                    >
                        <div class="h-56 bg-gradient-to-br overflow-hidden relative" :class="convocatoria.status === 'activa' ? 'from-gray-700 via-gray-600 to-gray-500' : 'from-gray-400 via-gray-300 to-gray-200'">
                            <img 
                                :src="convocatoria.image_url || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&h=400&fit=crop'" 
                                alt="Imagen de convocatoria" 
                                :class="[
                                    'w-full h-full object-cover transition-transform duration-500',
                                    convocatoria.status === 'activa' ? 'group-hover:scale-110' : 'opacity-60'
                                ]"
                                @error="$event.target.src = 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&h=400&fit=crop'"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t" :class="convocatoria.status === 'activa' ? 'from-black/40' : 'from-black/20'"></div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex justify-between items-center mb-4">
                                <span 
                                    :class="getFase(convocatoria).color"
                                    class="text-xs font-bold px-3 py-1.5 rounded-full shadow-md"
                                >
                                    {{ getFase(convocatoria).nombre }}
                                </span>
                                <span class="text-sm font-semibold bg-gray-100 px-3 py-1 rounded-full" :class="convocatoria.status === 'activa' ? 'text-gray-600' : 'text-gray-500'">
                                    {{ new Date(convocatoria.created_at).getFullYear() }}
                                </span>
                            </div>
                            <h3 
                                :class="[
                                    'text-xl font-bold mb-3',
                                    convocatoria.status === 'activa' 
                                        ? 'text-gray-900 group-hover:text-[#2c5282] transition-colors' 
                                        : 'text-gray-800'
                                ]"
                            >
                                {{ convocatoria.name }}
                            </h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-3 leading-relaxed">
                                {{ convocatoria.description || 'Sin descripción disponible.' }}
                            </p>

                            <!-- Document Link -->
                            <div v-if="convocatoria.file_url" class="mb-4">
                                <a 
                                    :href="convocatoria.file_url" 
                                    target="_blank" 
                                    class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors group/pdf"
                                >
                                    <FileText class="w-4 h-4 mr-1 group-hover/pdf:scale-110 transition-transform"/>
                                    Más sobre la convocatoria
                                </a>
                            </div>
                            
                            <div class="mt-auto">
                                <!-- Lógica de Botones según Estado y Login -->
                                <Link 
                                    v-if="!user && convocatoria.status === 'activa'" 
                                    :href="route('login')"
                                    class="w-full block py-3 rounded-xl transition-all font-semibold cursor-pointer text-center bg-white border-2 border-[#2c5282] text-[#2c5282] hover:bg-[#2c5282] hover:text-white shadow-md hover:shadow-lg"
                                >
                                    Iniciar Sesión para Solicitar
                                </Link>

                                <Link 
                                    v-else-if="getFase(convocatoria).canRegister"
                                    :href="route('teacher.dashboard')"
                                    class="w-full block py-3 rounded-xl transition-all font-semibold cursor-pointer text-center bg-gradient-to-r from-[#2c5282] to-[#3d5a80] text-white hover:from-[#1e3a5f] hover:to-[#2c5282] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                >
                                    Ir a Panel Docente
                                </Link>
                                <div v-else class="w-full py-3 rounded-xl font-semibold text-center border-2 border-gray-300 text-gray-500 bg-gray-50 cursor-not-allowed">
                                    {{ convocatoria.status === 'activa' ? 'Ver Detalles' : 'Convocatoria Cerrada' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="text-center py-12">
                    <p class="text-gray-500 text-lg">No hay convocatorias disponibles en este momento.</p>
                </div>
                
                <!-- Paginación -->
                <div class="mt-8">
                    <Pagination 
                        :links="announcements.meta.links" 
                        :total="announcements.meta.total"
                        :from="announcements.meta.from"
                        :to="announcements.meta.to"
                        :show-total="false"
                    />
                </div>
            </div>
        </section>



    </LandingLayout>
</template>
