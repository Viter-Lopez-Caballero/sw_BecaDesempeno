<script setup>
import { Head, router } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    recognitions: {
        type: Array,
        default: null,
    },
    searched: {
        type: Boolean,
        default: false,
    },
    filters: {
        type: Object,
        default: () => ({ curp: '', name: '', folio: '' }),
    },
});

const curp  = ref(props.filters.curp  || '');
const name  = ref(props.filters.name  || '');
const folio = ref(props.filters.folio || '');
const loading = ref(false);

const search = () => {
    if (!curp.value && !name.value && !folio.value) return;
    loading.value = true;
    router.get(
        route('recognitions.search'),
        { curp: curp.value, name: name.value, folio: folio.value },
        {
            preserveState: true,
            replace: true,
            onFinish: () => { loading.value = false; },
        }
    );
};

const clearFilters = () => {
    curp.value  = '';
    name.value  = '';
    folio.value = '';
    router.get(route('recognitions.search'), {}, { preserveState: true, replace: true });
};

const typeLabel = (type) => {
    if (!type) return 'Reconocimiento';
    if (type === 'evaluator') return 'Reconocimiento de Evaluador';
    if (type === 'teacher')   return 'Carta de Aceptación';
    return type;
};

const typeColor = (type) => {
    if (type === 'evaluator') return 'bg-[#1B396A]';
    if (type === 'teacher')   return 'bg-[#10A558]';
    return 'bg-gray-500';
};

const typeIcon = (type) => {
    // Returns either 'star' for evaluator or 'file-check' for teacher
    return type === 'teacher' ? 'file' : 'star';
};
</script>

<template>
    <LandingLayout>
        <Head title="Buscador de Reconocimientos" />

        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-[#1B396A] via-[#2B4A7E] to-[#3B5C92] py-14 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
            <div class="absolute top-1/2 left-1/4 w-48 h-48 bg-white/[0.03] rounded-full -translate-y-1/2"></div>

            <div class="relative max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 leading-tight">
                    Buscador de Reconocimientos
                </h1>
                <p class="text-lg text-blue-100 max-w-2xl mx-auto leading-relaxed">
                    Encuentra fácilmente los reconocimientos que has obtenido a través de tu participación
                    en convocatorias organizadas por el TecNM.
                </p>
            </div>
        </section>

        <!-- Search Section -->
        <section class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">

                <!-- Search Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-[#1B396A] to-[#2B4A7E] px-6 py-4 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-5 h-5 fill-white/80">
                            <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/>
                        </svg>
                        <h2 class="text-white font-semibold text-lg">Criterios de Búsqueda</h2>
                        <span class="text-blue-200 text-sm ml-auto">Ingresa al menos un criterio</span>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <!-- CURP -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    CURP
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-5 h-5 fill-[#1B396A]">
                                            <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/>
                                        </svg>
                                    </div>
                                    <input
                                        v-model="curp"
                                        type="text"
                                        placeholder="Ej. ABCD010101HXXXXX01"
                                        maxlength="18"
                                        @input="curp = curp.toUpperCase()"
                                        @keyup.enter="search"
                                        class="pl-10 w-full h-[45px] rounded-xl border border-gray-300 text-gray-700 text-sm focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition uppercase"
                                    />
                                </div>
                            </div>

                            <!-- Nombre Completo -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Nombre Completo
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-5 h-5 fill-[#1B396A]">
                                            <path d="M160-200v-80h640v80H160Zm0-160v-80h640v80H160Zm0-160v-80h640v80H160Zm0-160v-80h640v80H160Z"/>
                                        </svg>
                                    </div>
                                    <input
                                        v-model="name"
                                        type="text"
                                        placeholder="Ej. JUAN PÉREZ GARCÍA"
                                        @input="name = name.toUpperCase()"
                                        @keyup.enter="search"
                                        class="pl-10 w-full h-[45px] rounded-xl border border-gray-300 text-gray-700 text-sm focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition uppercase"
                                    />
                                </div>
                            </div>

                            <!-- Folio -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Folio / Identificador
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-5 h-5 fill-[#1B396A]">
                                            <path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/>
                                        </svg>
                                    </div>
                                    <input
                                        v-model="folio"
                                        type="text"
                                        placeholder="Ej. REC-2025-EV-12-ABCD"
                                        @input="folio = folio.toUpperCase()"
                                        @keyup.enter="search"
                                        class="pl-10 w-full h-[45px] rounded-xl border border-gray-300 text-gray-700 text-sm focus:border-[#1B396A] focus:ring focus:ring-[#1B396A] focus:ring-opacity-20 hover:bg-gray-50 transition uppercase"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-6">
                            <button
                                @click="search"
                                :disabled="loading || (!curp && !name && !folio)"
                                class="flex-1 flex items-center justify-center gap-2 h-[45px] rounded-xl bg-[#1B396A] text-white text-sm font-semibold hover:bg-[#162e55] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transition shadow-sm"
                            >
                                <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-5 h-5 fill-white">
                                    <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                                </svg>
                                <svg v-else class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/>
                                    <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                {{ loading ? 'Buscando...' : 'Buscar Reconocimientos' }}
                            </button>

                            <button
                                v-if="searched"
                                @click="clearFilters"
                                class="sm:w-44 flex items-center justify-center gap-2 h-[45px] rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 cursor-pointer transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                                    <path d="M400-240v-80h240v80H400Zm-158 0L15-467l57-57 170 170 366-366 57 57-423 423Zm318-160v-80h240v80H560Zm160-160v-80h240v80H720Z"/>
                                </svg>
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div v-if="searched" class="mt-8">

                    <!-- No results -->
                    <div v-if="!recognitions || recognitions.length === 0" class="text-center py-16 bg-white rounded-2xl border border-gray-200 shadow-sm">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-2xl mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-8 h-8 fill-gray-400">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Sin resultados</h3>
                        <p class="text-gray-500 text-sm max-w-sm mx-auto">
                            No encontramos reconocimientos activos con los criterios ingresados.
                            Verifica los datos e intenta de nuevo.
                        </p>
                    </div>

                    <!-- Results grid -->
                    <div v-else>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                Resultados encontrados
                                <span class="ml-2 text-sm font-normal text-gray-500">({{ recognitions.length }} reconocimiento{{ recognitions.length !== 1 ? 's' : '' }})</span>
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div
                                v-for="rec in recognitions"
                                :key="rec.id"
                                class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow group"
                            >
                                <!-- Card Top Accent -->
                                <div :class="typeColor(rec.template_type)" class="h-1.5 w-full"></div>

                                <div class="p-5">
                                    <!-- Header row -->
                                    <div class="flex items-start justify-between gap-3 mb-4">
                                        <!-- Icon + Type badge -->
                                        <div class="flex items-center gap-3">
                                            <div :class="typeColor(rec.template_type)" class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center shadow-sm">
                                                <!-- Star icon for evaluator -->
                                                <svg v-if="typeIcon(rec.template_type) === 'star'" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-6 h-6 fill-white">
                                                    <path d="m354-247 126-76 126 77-33-144 111-96-146-13-58-136-58 135-146 13 111 97-33 143ZM233-80l65-281L80-550l288-25 112-265 112 265 288 25-218 189 65 281-247-149L233-80Zm247-350Z"/>
                                                </svg>
                                                <!-- File icon for teacher -->
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-6 h-6 fill-white">
                                                    <path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <span :class="[typeColor(rec.template_type), 'text-white text-xs font-semibold px-2 py-0.5 rounded-full']">
                                                    {{ typeLabel(rec.template_type) }}
                                                </span>
                                                <p class="text-xs text-gray-400 mt-1">{{ rec.date || rec.year }}</p>
                                            </div>
                                        </div>
                                        <!-- Folio badge -->
                                        <div class="flex-shrink-0 text-right">
                                            <span class="text-xs text-gray-400 block">Folio</span>
                                            <span class="text-xs font-mono font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-lg">
                                                {{ rec.folio }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Recognition name -->
                                    <h4 class="text-base font-bold text-gray-800 mb-1 leading-snug">
                                        {{ rec.template_name }}
                                    </h4>

                                    <!-- Participant & Announcement -->
                                    <div class="space-y-1 mb-4">
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-4 h-4 fill-gray-400 flex-shrink-0">
                                                <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/>
                                            </svg>
                                            <span class="font-medium truncate">{{ rec.participant }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-4 h-4 fill-gray-400 flex-shrink-0">
                                                <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z"/>
                                            </svg>
                                            <span class="truncate">{{ rec.announcement }}</span>
                                        </div>
                                    </div>

                                    <!-- Divider -->
                                    <div class="border-t border-gray-100 pt-4">
                                        <a
                                            :href="route('recognitions.public-download', { identifier: rec.folio })"
                                            target="_blank"
                                            class="w-full flex items-center justify-center gap-2 h-10 rounded-xl bg-[#1B396A] text-white text-sm font-semibold hover:bg-[#162e55] transition shadow-sm group-hover:shadow-md"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-5 h-5 fill-white">
                                                <path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/>
                                            </svg>
                                            Descargar Documento
                                        </a>
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
