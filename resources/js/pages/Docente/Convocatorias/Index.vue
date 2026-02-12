<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DocenteLayout from '@/layouts/DocenteLayout.vue';
import { mdiBullhorn } from '@mdi/js';

const props = defineProps({
    convocatorias: Object,
    has_active_solicitud: Boolean,
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
const getFase = (convocatoria) => {
    // 1. Estado Cerrada
    if (convocatoria.estado !== 'activa') {
        return { 
            nombre: 'Cerrada', 
            color: 'bg-red-100 text-red-800', 
            canRegister: false 
        };
    }

    // 2. Estado Activa
    let canRegister = false;
    
    if (convocatoria.calendario) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const regInicio = parseDateLocal(convocatoria.calendario.registro_inicio);
        const regFin = parseDateLocal(convocatoria.calendario.registro_fin);
        
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
    <DocenteLayout>
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

            <!-- Grid de Convocatorias con diseño estilo Landing -->
            <div v-if="convocatorias.data && convocatorias.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
                    <!-- Imagen / Header visual -->
                    <div class="h-48 bg-gradient-to-br overflow-hidden relative" :class="convocatoria.estado === 'activa' ? 'from-gray-700 via-gray-600 to-gray-500' : 'from-gray-400 via-gray-300 to-gray-200'">
                        <img 
                            :src="convocatoria.imagen_url || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&h=400&fit=crop'" 
                            alt="Imagen de convocatoria" 
                            :class="[
                                'w-full h-full object-cover transition-transform duration-500',
                                convocatoria.estado === 'activa' ? 'group-hover:scale-110' : 'opacity-60'
                            ]"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t" :class="convocatoria.estado === 'activa' ? 'from-black/40' : 'from-black/20'"></div>
                    </div>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex justify-between items-center mb-4 gap-2">
                             <span 
                                :class="getFase(convocatoria).color"
                                class="text-xs font-bold px-3 py-1.5 rounded-full shadow-md whitespace-nowrap"
                            >
                                {{ getFase(convocatoria).nombre }}
                            </span>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-md">
                                {{ new Date(convocatoria.created_at).getFullYear() }}
                            </span>
                        </div>

                        <h3 
                            :class="[
                                'text-xl font-bold mb-3',
                                convocatoria.estado === 'activa' 
                                    ? 'text-gray-900 group-hover:text-[#1B396A] transition-colors' 
                                    : 'text-gray-800'
                            ]"
                        >
                            {{ convocatoria.nombre }}
                        </h3>
                        
                        <p class="text-gray-600 mb-4 text-sm line-clamp-3 leading-relaxed">
                            {{ convocatoria.descripcion || 'Sin descripción disponible.' }}
                        </p>

                        <!-- Document Link -->
                        <div v-if="convocatoria.archivo_url" class="mb-4">
                            <a 
                                :href="convocatoria.archivo_url" 
                                target="_blank" 
                                class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors group/pdf"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor" class="mr-1 group-hover/pdf:scale-110 transition-transform">
                                    <path d="M240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/>
                                </svg>
                                Más sobre la convocatoria
                            </a>
                        </div>
                        
                        <div class="mt-auto pt-4 border-t border-gray-100">
                             <!-- Estado de la solicitud (si ya tiene una) -->
                             <div v-if="has_active_solicitud" class="w-full text-center py-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 font-medium">
                                Ya tienes una solicitud en curso
                             </div>
                             
                             <!-- Botón Solicitar -->
                             <Link 
                                v-else-if="getFase(convocatoria).canRegister" 
                                :href="route('docente.convocatorias.solicitar', convocatoria.id)"
                                class="w-full flex items-center justify-center gap-2 py-3 bg-[#1B396A] text-white rounded-xl font-bold hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                             >
                                <span>Solicitar Beca</span>
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                    <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/>
                                </svg>
                            </Link>

                             <!-- Botón Deshabilitado (Fuera de fecha) -->
                             <button 
                                v-else 
                                disabled
                                class="w-full py-3 bg-gray-100 text-gray-400 border border-gray-300 rounded-xl font-medium cursor-not-allowed flex items-center justify-center gap-2"
                             >
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                    <path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm0-80h480v-400H240v400Zm240-120q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280Zm-80-360h160v-80q0-33-23.5-56.5T480-800q-33 0-56.5 23.5T400-720v80ZM240-160v-400 400Z"/>
                                </svg>
                                {{ convocatoria.estado === 'activa' ? 'Registro Cerrado' : 'Convocatoria Inactiva' }}
                             </button>
                        </div>
                    </div>
                </div>
            </div>

             <div v-else class="col-span-full py-16 flex flex-col items-center justify-center text-gray-500 bg-white rounded-2xl shadow-sm border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" height="64px" viewBox="0 -960 960 960" width="64px" fill="#D1D5DB" class="mb-4">
                    <path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                </svg>
                <p class="text-xl font-bold text-gray-700">No hay convocatorias activas</p>
                <p class="text-gray-500 mt-2">Mantente al pendiente para futuras oportunidades.</p>
            </div>
        </div>
    </DocenteLayout>
</template>
