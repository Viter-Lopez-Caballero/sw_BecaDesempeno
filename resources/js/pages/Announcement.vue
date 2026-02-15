<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    announcement: Object, // Puede venir como Resource (con .data) o directo
});

const showModal = ref(false);
const currentPdfUrl = ref('');
const currentPdfTitle = ref('');

const openPdfModal = (url, title) => {
    if (!url) return;
    currentPdfUrl.value = url;
    currentPdfTitle.value = title;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    currentPdfUrl.value = '';
    currentPdfTitle.value = '';
};

// Acceder a los datos correctamente (handling Resource wrapper)
const announcementData = computed(() => {
    return props.announcement?.data || props.announcement;
});

// Helper para parsear fecha "YYYY-MM-DD", "YYYY-MM-DD HH:MM:SS" o ISO "YYYY-MM-DDTHH:mm:ss"
const parseDateLocal = (dateString) => {
    if (!dateString) return null;
    // Tomamos los primeros 10 caracteres (YYYY-MM-DD)
    const datePart = dateString.substring(0, 10);
    const [year, month, day] = datePart.split('-').map(Number);
    
    if (!year || !month || !day) return null;

    return new Date(year, month - 1, day);
};

const etapas = computed(() => {
    const announcement = announcementData.value;
    const stages = [
        {
            id: 1,
            titulo: 'Publicación de Convocatoria',
            dateStartKey: 'publication_start',
            color: '#1B396A'
        },
        {
            id: 2,
            titulo: 'Registro y Carga de Documentos',
            dateStartKey: 'registration_start',
            dateEndKey: 'registration_end',
            color: '#10A558'
        },
        {
            id: 3,
            titulo: 'Evaluación y Dictaminación',
            dateStartKey: 'evaluation_start',
            color: '#E9C81F'
        },
        {
            id: 4,
            titulo: 'Publicación de Resultados',
            dateStartKey: 'results_start',
            dateEndKey: 'results_end',
            color: '#1B396A'
        }
    ];

    if (!announcement || !announcement.calendar) {
        return stages.map(s => ({
            ...s,
            fechas: 'Por definir'
        }));
    }

    return stages.map(stage => {
        const start = parseDateLocal(announcement.calendar[stage.dateStartKey]);
        let fechasStr = start ? start.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' }) : 'Por definir';
        
        if (stage.dateEndKey && announcement.calendar[stage.dateEndKey]) {
            const end = parseDateLocal(announcement.calendar[stage.dateEndKey]);
            if (end) {
                fechasStr += ` - ${end.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })}`;
            }
        } else if (start) {
             fechasStr += ` ${start.getFullYear()}`;
        }

        return {
            ...stage,
            fechas: fechasStr
        };
    });
});
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

                <!-- Case: No active convocatoria -->
                <div v-if="!announcementData" class="text-center py-12">
                    <h2 class="text-2xl font-bold text-gray-700">No hay convocatorias activas en este momento.</h2>
                    <p class="text-gray-500 mt-2">Por favor, vuelve más tarde.</p>
                </div>

                <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-12">
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
                                Iniciar Sesión
                            </Link>
                        </div>
                    </div>

                    <!-- LADO DERECHO: Card Convocatoria y Calendario -->
                    <div class="space-y-8">
                        <!-- Card Principal de Convocatoria -->
                        <div
                            class="bg-gradient-to-br from-[#2d4a7c] via-[#3a5a8c] to-[#4a6a9c] text-white rounded-3xl p-8 shadow-md">
                            <h2 class="text-3xl font-bold mb-4 text-center">{{ announcementData.name }}</h2>
                            <p class="text-base leading-relaxed mb-8 text-blue-50 line-clamp-4">
                                {{ announcementData.description || 'Sin descripción disponible.' }}
                            </p>
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    v-if="announcementData.file_url && announcementData.status !== 'pendiente'"
                                    @click="openPdfModal(announcementData.file_url, announcementData.name)"
                                    class="cursor-pointer px-6 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 hover:bg-gray-100 font-medium transition shadow-lg transform hover:-translate-y-1">
                                    Ver Convocatoria PDF
                                </button>
                                <span v-else-if="announcementData.status === 'pendiente'" class="text-sm bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg border border-yellow-200">
                                    Convocatoria Próximamente
                                </span>
                                <span v-else class="text-sm bg-white/20 px-4 py-2 rounded-lg">Sin PDF disponible</span>
                            </div>
                        </div>

                        <!-- Calendario de Actividades -->
                        <div class="bg-white p-8 rounded-2xl shadow-md">
                            <h3 class="text-2xl font-bold text-gray-900 mb-8">Calendario de Actividades</h3>

                            <div v-for="(etapa, index) in etapas" :key="etapa.id" class="relative" :class="{ 'mb-10': index < etapas.length - 1 }">
                                <!-- Connector Line -->
                                <div v-if="index < etapas.length - 1" class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 -mt-10" style="height: calc(100% + 40px); top: 20px;"></div>
                                
                                <div class="flex items-start relative z-10">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-12 w-12 rounded-full flex items-center justify-center shadow-md text-white font-bold text-sm"
                                            :style="{ backgroundColor: etapa.color }"
                                        >
                                            {{ etapa.id }}
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Fase {{ etapa.id }}</p>
                                        <h4 class="text-lg font-bold text-gray-900 mb-1">{{ etapa.titulo }}</h4>
                                        <p class="text-sm text-gray-500 capitalize">{{ etapa.fechas }}</p>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PDF Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                    
                    <!-- Modal Content -->
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[90vh] flex flex-col">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">{{ currentPdfTitle }}</h2>
                            <button @click="closeModal" class="text-gray-400 hover:text-gray-800 transition-colors cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- PDF Viewer -->
                        <div class="flex-1 overflow-hidden">
                            <iframe :src="currentPdfUrl" class="w-full h-full" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </LandingLayout>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}
</style>
