<script setup>
import { Head } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    activeAnnouncement: Object
});

const announcementYear = computed(() => {
    if (props.activeAnnouncement && props.activeAnnouncement.created_at) {
        return new Date(props.activeAnnouncement.created_at).getFullYear();
    }
    return 2026;
});

const activeDocument = ref(null);
const currentPdfUrl = ref('');
const currentPdfTitle = ref('');

const toggleDocument = (id, url, title) => {
    if (activeDocument.value === id) {
        closeDocument();
    } else {
        activeDocument.value = id;
        currentPdfUrl.value = url;
        currentPdfTitle.value = title;
    }
};

const closeDocument = () => {
    activeDocument.value = null;
    currentPdfUrl.value = '';
    currentPdfTitle.value = '';
};

const isPdfResource = (url) => /\.pdf($|\?)/i.test(url || '');
const isImageResource = (url) => /\.(png|jpe?g|gif|webp|bmp|svg)($|\?)/i.test(url || '');

const currentPreviewIsPdf = computed(() => isPdfResource(currentPdfUrl.value));
const currentPreviewIsImage = computed(() => isImageResource(currentPdfUrl.value));
</script>

<template>
    <LandingLayout>
        <Head title="Documentos" />

        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-[#1B396A] via-[#2B4A7E] to-[#3B5C92] py-8 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
            <div class="absolute top-1/2 left-1/4 w-48 h-48 bg-white/[0.03] rounded-full -translate-y-1/2"></div>
            <div class="relative max-w-7xl mx-auto text-center">
                <h1 class="text-4xl md:text-4xl font-bold text-white mb-4">Documentos de Trabajo 2026</h1>
                <p class="text-lg md:text-lg text-gray-100 max-w-3xl mx-auto">Descarga los formatos oficiales y documentación necesaria para tu participación en el programa</p>
            </div>
        </section>

        <!-- DOCUMENTOS Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 1. Carta de Exclusividad Laboral -->
                    <a href="https://edd.tecnm.mx/formatos/2025/CARTA_EXCLUSIVIDAD_EDD2025.docx" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#2B579A] hover:shadow-xl transition-all group cursor-pointer">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#2B579A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Carta de Exclusividad Laboral</h3>
                            <p class="text-sm text-gray-600 mb-3">Documento donde el docente manifiesta su compromiso exclusivo con el TecNM, declarando no exceder las 12 horas-semana en otras instituciones y autorizando la revisión con recursos humanos.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">DOCX • 85 KB</span>
                                <svg class="w-5 h-5 text-[#2B579A] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- 2. Formato de Liberación de Actividades Académicas -->
                    <a href="https://edd.tecnm.mx/formatos/2025/Formato_Liberacion_de_Actividades_Academicas_2025.docx" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#2B579A] hover:shadow-xl transition-all group cursor-pointer">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#2B579A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Liberación de Actividades Académicas</h3>
                            <p class="text-sm text-gray-600 mb-3">Constancia de cumplimiento de actividades académicas como asistencia a reuniones, programas de formación docente, asesorías, propuestas de mejora, sinodales de titulación y participación en eventos académicos.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">DOCX • 92 KB</span>
                                <svg class="w-5 h-5 text-[#2B579A] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- 3. Formato de Constancia de Liberación de Actividades Docentes -->
                    <a href="https://edd.tecnm.mx/formatos/2025/Formato_Constancia_de_Liberacion_de_Actividades_Docentes_2025.docx" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#2B579A] hover:shadow-xl transition-all group cursor-pointer">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#2B579A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Constancia Actividades Frente a Grupo</h3>
                            <p class="text-sm text-gray-600 mb-3">Formato que certifica el cumplimiento de actividades docentes frente a grupo: elaboración de dosificación, evaluación de programas, entrega de informes, calificaciones y reportes finales del semestre.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">DOCX • 88 KB</span>
                                <svg class="w-5 h-5 text-[#2B579A] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- 4. Lineamientos del Programa -->
                    <button @click="toggleDocument('doc4', 'https://edd.tecnm.mx/formatos/2025/LINEAMIENTOS_EDD_2025.pdf', 'Lineamientos del Programa')" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#059669] hover:shadow-xl transition-all group text-left w-full cursor-pointer" :class="{'ring-2 ring-offset-2 ring-[#059669]': activeDocument === 'doc4'}">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#059669]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Lineamientos del Programa</h3>
                            <p class="text-sm text-gray-600 mb-3">Documento oficial con la estructura completa del programa: presentación, bases de operación, tipos de personal beneficiado, fuentes de financiamiento, criterios de evaluación y responsabilidades de docentes participantes.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">PDF • 4.8 MB</span>
                                <svg class="w-5 h-5 text-[#059669] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                    
                    <!-- Viewer for Doc 4 -->
                    <div v-if="activeDocument === 'doc4'" class="col-span-1 md:col-span-2 bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-4 transition-all">
                        <div class="flex justify-between items-center p-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="font-bold text-gray-800">{{ currentPdfTitle }}</h3>
                            <button @click="closeDocument" class="text-gray-400 hover:text-gray-600 p-1.5 hover:bg-gray-200 rounded-lg transition cursor-pointer" title="Cerrar vista previa">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="w-full h-[600px] relative bg-gray-100">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400 z-0">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto animate-pulse mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Cargando vista previa...</span>
                                </div>
                            </div>
                            <iframe
                                v-if="currentPreviewIsPdf"
                                :src="currentPdfUrl"
                                class="w-full h-full relative z-10"
                                frameborder="0"
                            ></iframe>
                            <div
                                v-else-if="currentPreviewIsImage"
                                class="w-full h-full relative z-10 overflow-auto flex items-center justify-center p-4"
                            >
                                <img
                                    :src="currentPdfUrl"
                                    :alt="currentPdfTitle"
                                    class="max-w-full max-h-full object-contain mx-auto"
                                />
                            </div>
                            <div v-else class="w-full h-full relative z-10 flex items-center justify-center p-8">
                                <a
                                    :href="currentPdfUrl"
                                    target="_blank"
                                    class="px-4 py-2 rounded-md bg-[#1B396A] text-white font-medium hover:bg-[#152d47]"
                                >
                                    Abrir documento
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. SPD - Solicitud de Personal Docente -->
                    <button @click="toggleDocument('doc5', 'https://edd.tecnm.mx/formatos/2025/FORMATO_SPD_2025.pdf', 'Guía SPD - Integración de Expediente')" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#059669] hover:shadow-xl transition-all group text-left w-full cursor-pointer" :class="{'ring-2 ring-offset-2 ring-[#059669]': activeDocument === 'doc5'}">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#059669]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Guía SPD - Integración de Expediente</h3>
                            <p class="text-sm text-gray-600 mb-3">Guía oficial para integrar el expediente del programa EDD con requisitos, instrucciones de captura, documentos comprobatorios, evidencias y responsabilidades del docente en el portal www.edd.tecnm.mx</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">PDF • 1.2 MB</span>
                                <svg class="w-5 h-5 text-[#059669] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                    
                    <!-- 6. Convocatoria (Dinámica) -->
                    <button v-if="activeAnnouncement && activeAnnouncement.file_url" @click="toggleDocument('doc6', activeAnnouncement.file_url, `Convocatoria ${announcementYear}`)" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#1B396A] hover:shadow-xl transition-all group text-left w-full cursor-pointer" :class="{'ring-2 ring-offset-2 ring-[#1B396A]': activeDocument === 'doc6'}">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#1B396A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Convocatoria {{ announcementYear }}</h3>
                            <p class="text-sm text-gray-600 mb-3">Documento oficial de convocatoria del TecNM para el programa de estímulos {{ announcementYear }}: bases de participación, registro, evaluación, estímulos diferenciados, suspensiones, responsabilidades y contacto oficial.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">PDF • Dinámico</span>
                                <svg class="w-5 h-5 text-[#1B396A] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                    <!-- Fallback 6. Convocatoria Estática -->
                    <button v-else @click="toggleDocument('doc6', 'https://edd.tecnm.mx/formatos/2025/CONVOCATORIA_EDD_2025.pdf', 'Convocatoria 2026')" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#1B396A] hover:shadow-xl transition-all group text-left w-full cursor-pointer" :class="{'ring-2 ring-offset-2 ring-[#1B396A]': activeDocument === 'doc6'}">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#1B396A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Convocatoria 2026 (Estática)</h3>
                            <p class="text-sm text-gray-600 mb-3">Documento oficial de convocatoria del TecNM para el programa de estímulos 2025: bases de participación, registro, evaluación, estímulos diferenciados, suspensiones, responsabilidades y contacto oficial.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">PDF • 2.7 MB</span>
                                <svg class="w-5 h-5 text-[#1B396A] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                    
                    <!-- Viewer for Row 5/6 -->
                    <div v-if="activeDocument === 'doc5' || activeDocument === 'doc6'" class="col-span-1 md:col-span-2 bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-4 transition-all">
                        <div class="flex justify-between items-center p-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="font-bold text-gray-800">{{ currentPdfTitle }}</h3>
                            <button @click="closeDocument" class="text-gray-400 hover:text-gray-600 p-1.5 hover:bg-gray-200 rounded-lg transition cursor-pointer" title="Cerrar vista previa">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="w-full h-[600px] relative bg-gray-100">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400 z-0">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto animate-pulse mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Cargando vista previa...</span>
                                </div>
                            </div>
                            <iframe
                                v-if="currentPreviewIsPdf"
                                :src="currentPdfUrl"
                                class="w-full h-full relative z-10"
                                frameborder="0"
                            ></iframe>
                            <div
                                v-else-if="currentPreviewIsImage"
                                class="w-full h-full relative z-10 overflow-auto flex items-center justify-center p-4"
                            >
                                <img
                                    :src="currentPdfUrl"
                                    :alt="currentPdfTitle"
                                    class="max-w-full max-h-full object-contain mx-auto"
                                />
                            </div>
                            <div v-else class="w-full h-full relative z-10 flex items-center justify-center p-8">
                                <a
                                    :href="currentPdfUrl"
                                    target="_blank"
                                    class="px-4 py-2 rounded-md bg-[#1B396A] text-white font-medium hover:bg-[#152d47]"
                                >
                                    Abrir documento
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 7. Aviso de Privacidad Integral -->
                    <button @click="toggleDocument('doc7', 'https://www.tecnm.mx/menu/proteccion_datos_personales/AVISO_DE_PRIVACIDAD_INTEGRAL_2023.pdf?doc=1', 'Aviso de Privacidad Integral')" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#E9C81F] hover:shadow-xl transition-all group text-left w-full cursor-pointer" :class="{'ring-2 ring-offset-2 ring-[#E9C81F]': activeDocument === 'doc7'}">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#E9C81F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Aviso de Privacidad Integral</h3>
                            <p class="text-sm text-gray-600 mb-3">Documento legal del TecNM sobre protección de datos personales: fundamento legal, finalidades para recabar información, tratamiento, resguardo y derechos de los participantes según la Ley General de Protección de Datos.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">PDF • 380 KB</span>
                                <svg class="w-5 h-5 text-[#E9C81F] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                    
                    <!-- Doc 7 & 8 are handled after card 8 below -->

                    <!-- 8. Aviso de Privacidad Simplificado -->
                    <button @click="toggleDocument('doc8', 'https://www.tecnm.mx/menu/proteccion_datos_personales/AVISO_DE_PRIVACIDAD_SIMPLIFICADO_2023.pdf?doc=1', 'Aviso de Privacidad Simplificado')" class="flex items-start space-x-4 bg-white p-6 rounded-xl shadow-md border-l-4 border-[#E9C81F] hover:shadow-xl transition-all group text-left w-full cursor-pointer" :class="{'ring-2 ring-offset-2 ring-[#E9C81F]': activeDocument === 'doc8'}">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-[#E9C81F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">Aviso de Privacidad Simplificado</h3>
                            <p class="text-sm text-gray-600 mb-3">Versión simplificada del aviso de privacidad con las finalidades principales: gestión de becas, otorgamiento de estímulos, expedición de certificados, trámites de cédulas profesionales y suscripción de convenios del TecNM.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">PDF • 285 KB</span>
                                <svg class="w-5 h-5 text-[#E9C81F] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                    
                    <!-- Viewer for Row 7/8 -->
                    <div v-if="activeDocument === 'doc7' || activeDocument === 'doc8'" class="col-span-1 md:col-span-2 bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-4 transition-all">
                        <div class="flex justify-between items-center p-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="font-bold text-gray-800">{{ currentPdfTitle }}</h3>
                            <button @click="closeDocument" class="text-gray-400 hover:text-gray-600 p-1.5 hover:bg-gray-200 rounded-lg transition cursor-pointer" title="Cerrar vista previa">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="w-full h-[600px] relative bg-gray-100">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400 z-0">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto animate-pulse mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Cargando vista previa...</span>
                                </div>
                            </div>
                            <iframe
                                v-if="currentPreviewIsPdf"
                                :src="currentPdfUrl"
                                class="w-full h-full relative z-10"
                                frameborder="0"
                            ></iframe>
                            <div
                                v-else-if="currentPreviewIsImage"
                                class="w-full h-full relative z-10 overflow-auto flex items-center justify-center p-4"
                            >
                                <img
                                    :src="currentPdfUrl"
                                    :alt="currentPdfTitle"
                                    class="max-w-full max-h-full object-contain mx-auto"
                                />
                            </div>
                            <div v-else class="w-full h-full relative z-10 flex items-center justify-center p-8">
                                <a
                                    :href="currentPdfUrl"
                                    target="_blank"
                                    class="px-4 py-2 rounded-md bg-[#1B396A] text-white font-medium hover:bg-[#152d47]"
                                >
                                    Abrir documento
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>

<style scoped>
</style>
