<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TeacherLayout from '@/layouts/TeacherLayout.vue';
import { mdiBullhorn } from '@mdi/js';

const props = defineProps({
    announcements: Object,
    has_active_application: Boolean,
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

// Helper para determinar la fase actual
const getFase = (announcement) => {
    // 1. Estado Cerrada
    if (announcement.status !== 'activa') {
        return { 
            nombre: 'Cerrada', 
            color: 'bg-red-100 text-red-800', 
            canRegister: false 
        };
    }

    // 2. Estado Activa
    let canRegister = false;
    
    if (announcement.calendar) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const regInicio = parseDateLocal(announcement.calendar.registration_start);
        const regFin = parseDateLocal(announcement.calendar.registration_end);
        
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
</script>

<template>
    <TeacherLayout>
        <Head title="Convocatorias" />
        <div class="space-y-6">
             <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Convocatorias Disponibles</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiBullhorn"/>
                        </svg>
                         <span class="text-gray-900 font-semibold">Convocatorias</span>
                    </div>
                </div>
            </div>

            <!-- Diseño "Featured Card" para convocatoria única/principal -->
            <div v-if="announcements.data && announcements.data.length > 0" class="max-w-5xl mx-auto">
                <div 
                    v-for="announcement in announcements.data" 
                    :key="announcement.id"
                    :class="[
                        'rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 flex flex-col md:flex-row',
                        announcement.status === 'activa' 
                            ? 'bg-white transform hover:-translate-y-1' 
                            : 'bg-white shadow-md border border-gray-200 opacity-90'
                    ]"
                >
                    <!-- Imagen (Izquierda en desktop, Arriba en mobile) -->
                    <div class="md:w-2/5 relative h-64 md:h-auto overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10 md:hidden"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-transparent to-transparent z-10 hidden md:block"></div>
                        
                        <img 
                            :src="announcement.image_url || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&q=80'" 
                            alt="Imagen de convocatoria" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        
                        <!-- Fase Badge (Desktop: Over image top-left) -->
                    </div>
                    
                    <!-- Contenido (Derecha en desktop) -->
                    <div class="p-8 md:w-3/5 flex flex-col justify-center">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-sm font-semibold text-gray-500 uppercase tracking-widest">
                                Convocatoria {{ new Date(announcement.created_at).getFullYear() }}
                            </span>
                            <span 
                                :class="getFase(announcement).color"
                                class="text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-gray-100"
                            >
                                {{ getFase(announcement).nombre }}
                            </span>
                        </div>

                        <h2 class="text-3xl font-bold text-gray-900 mt-0 mb-3 leading-tight">
                            {{ announcement.name }}
                        </h2>
                        <div class="w-20 h-1.5 bg-[#1B396A] rounded-full mb-4"></div>
                        
                        <p class="text-gray-600 outline outline-0 text-lg mb-6 leading-relaxed">
                            {{ announcement.description || 'Sin descripción disponible para esta convocatoria.' }}
                        </p>

                        <!-- Fechas Importantes (Si está activa) -->
                        <div v-if="announcement.calendar && announcement.status === 'activa'" class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-100 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Inicio de Registro</p>
                                <p class="text-gray-800 font-medium font-mono">
                                    {{ parseDateLocal(announcement.calendar.registration_start)?.toLocaleDateString() || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Fin de Registro</p>
                                <p class="text-red-600 font-medium font-mono">
                                    {{ parseDateLocal(announcement.calendar.registration_end)?.toLocaleDateString() || 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 mt-auto items-center">
                             <!-- Document Link -->
                            <a 
                                v-if="announcement.file_url"
                                :href="announcement.file_url" 
                                target="_blank" 
                                class="text-[#1B396A] font-semibold hover:underline flex items-center gap-2 group/link"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover/link:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Bases Completas (PDF)
                            </a>

                             <!-- Botón Solicitar / Estado -->
                             <div class="flex-1 w-full sm:w-auto">
                                <div v-if="has_active_application" class="w-full text-center py-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 font-medium px-4">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        Solicitud en revisión
                                    </span>
                                </div>
                                
                                <Link 
                                    v-else-if="getFase(announcement).canRegister" 
                                    :href="route('teacher.announcements.apply', announcement.id)"
                                    class="w-full flex items-center justify-center gap-2 py-3.5 bg-[#1B396A] text-white rounded-lg font-bold hover:bg-[#152d47] transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 px-6 cursor-pointer"
                                >
                                    <span>Solicitar Beca</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </Link>

                                <button 
                                    v-else 
                                    disabled
                                    class="w-full py-3.5 bg-gray-100 text-gray-400 border border-gray-300 rounded-lg font-medium cursor-not-allowed flex items-center justify-center gap-2"
                                >
                                    {{ announcement.status === 'activa' ? 'Fuera de Periodo' : 'Convocatoria Inactiva' }}
                                </button>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
             <div v-else class="max-w-3xl mx-auto py-16 flex flex-col items-center justify-center text-gray-400 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="bg-gray-50 p-6 rounded-full mb-4">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-xl font-bold text-gray-600">No hay convocatorias activas</p>
                <p class="text-gray-500 mt-2">Mantente al pendiente para futuras oportunidades.</p>
            </div>
        </div>
    </TeacherLayout>
</template>
