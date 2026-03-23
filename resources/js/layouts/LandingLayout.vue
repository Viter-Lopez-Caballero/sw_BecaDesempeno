<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const page = usePage();
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="font-sans text-gray-800 bg-gray-50 min-h-screen flex flex-col">
        <nav class="bg-[#1B396A] text-white fixed w-full z-50 shadow-md">
            <!-- Desktop Menu -->
            <div class="hidden xl:flex items-center justify-between w-full px-6 xl:px-10 h-24">
                <!-- Logos -->
                <div class="flex items-center gap-3 shrink-0">
                    <Link :href="route('inicio')" class="inline-flex items-center" aria-label="Ir a inicio desde logo TecNM">
                        <img src="/img/LogoTecNMCompleto.png" alt="TecNM" class="h-12 lg:h-14 w-auto" />
                    </Link>
                    <Link :href="route('inicio')" class="inline-flex items-center" aria-label="Ir a inicio desde logo CENIDET">
                        <img src="/img/LogoCenidetCompleto.png" alt="Cenidet" class="h-12 lg:h-14 w-auto" />
                    </Link>
                </div>

                <!-- Nav links -->
                <div class="flex items-center gap-4 lg:gap-6">
                    <Link :href="route('inicio')" :class="{'border-b-2 border-white': route().current('inicio')}" class="text-sm lg:text-base hover:text-blue-300 transition font-medium py-1 whitespace-nowrap">INICIO</Link>
                    <Link :href="route('announcement.show')" :class="{'border-b-2 border-white': route().current('announcement.show')}" class="text-sm lg:text-base hover:text-blue-300 transition font-medium py-1 whitespace-nowrap">CONVOCATORIA</Link>
                    <Link :href="route('documents.index')" :class="{'border-b-2 border-white': route().current('documents.index')}" class="text-sm lg:text-base hover:text-blue-300 transition font-medium py-1 whitespace-nowrap">DOCUMENTOS</Link>
                    <Link :href="route('recognitions.search')" :class="{'border-b-2 border-white': route().current('recognitions.search')}" class="text-sm lg:text-base hover:text-blue-300 transition font-medium py-1 whitespace-nowrap">RECONOCIMIENTOS</Link>
                    <Link :href="route('contact')" :class="{'border-b-2 border-white': route().current('contact')}" class="text-sm lg:text-base hover:text-blue-300 transition font-medium py-1 whitespace-nowrap">CONTACTO</Link>
                </div>

                <!-- Auth buttons -->
                <div class="flex items-center gap-3 pl-4 border-l border-blue-500/40 shrink-0">
                    <Link :href="route('login')" class="text-sm lg:text-base text-white hover:text-blue-300 transition font-medium whitespace-nowrap">INICIAR SESIÓN</Link>
                    <Link :href="route('register')" class="text-sm lg:text-base text-white hover:text-blue-300 transition font-medium whitespace-nowrap">REGISTRARSE</Link>
                </div>
            </div>

            <!-- Mobile & Tablet -->
            <div class="xl:hidden flex items-center justify-between px-4 h-16">
                <!-- Logos -->
                <div class="flex items-center gap-2">
                    <Link :href="route('inicio')" class="inline-flex items-center" aria-label="Ir a inicio desde logo TecNM">
                        <img src="/img/LogoTecNMCompleto.png" alt="TecNM" class="h-9 w-auto" />
                    </Link>
                    <Link :href="route('inicio')" class="inline-flex items-center" aria-label="Ir a inicio desde logo CENIDET">
                        <img src="/img/LogoCenidetCompleto.png" alt="Cenidet" class="h-9 w-auto" />
                    </Link>
                </div>

                <!-- Hamburger -->
                <button
                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                    class="inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-800 focus:outline-none transition duration-150 ease-in-out"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path
                            :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Mobile & Tablet Dropdown Menu -->
            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="xl:hidden bg-[#162e55]"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <Link :href="route('inicio')" :class="{'bg-blue-800 border-l-4 border-white': route().current('inicio')}" class="block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition">INICIO</Link>
                    <Link :href="route('announcement.show')" :class="{'bg-blue-800 border-l-4 border-white': route().current('announcement.show')}" class="block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition">CONVOCATORIA</Link>
                    <Link :href="route('documents.index')" :class="{'bg-blue-800 border-l-4 border-white': route().current('documents.index')}" class="block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition">DOCUMENTOS</Link>
                    <Link :href="route('recognitions.search')" :class="{'bg-blue-800 border-l-4 border-white': route().current('recognitions.search')}" class="block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition">RECONOCIMIENTOS</Link>
                    <Link :href="route('contact')" :class="{'bg-blue-800 border-l-4 border-white': route().current('contact')}" class="block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition">CONTACTO</Link>
                </div>
                <div class="pt-3 pb-3 border-t border-blue-800 space-y-1">
                    <Link :href="route('login')" class="block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 transition">INICIAR SESIÓN</Link>
                    <Link :href="route('register')" class="block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 transition">REGISTRARSE</Link>
                </div>
            </div>
        </nav>

        <main class="flex-grow pt-16 xl:pt-24">
            <slot />
        </main>

        <footer class="bg-[#1B396A] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Contenido principal del footer -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    <!-- Columna Izquierda: Dirección y Redes Sociales -->
                    <div class="space-y-6">
                        <!-- Dirección -->
                        <div>
                            <h3 class="text-lg font-bold mb-3">Dirección</h3>
                            <p class="text-sm leading-relaxed">
                                Av. Universidad 1200, Col. Xoco, Ciudad de México,<br> 
                                Alcaldía Benito Juárez C.P. 03330
                            </p>
                        </div>
                    </div>

                    <!-- Columna Centro: Contacto -->
                    <div class="space-y-6">
                        <!-- Contacto -->
                        <div>
                            <h3 class="text-lg font-bold mb-3">Contacto</h3>
                            <p class="text-sm">
                                Email: <a href="mailto:contacto@tecnm.mx " class="hover:text-blue-300 transition-colors underline">contacto@tecnm.mx </a>
                            </p>
                            <p class="text-sm mt-2">
                                Conmutador: 55 36002500
                            </p>
                        </div>
                    </div>

                    <!-- Columna Derecha: Mapa -->
                    <div>
                        <div>
                            <h3 class="text-lg font-bold mb-3">Síguenos En</h3>
                            <div class="py-4 rounded-lg">
                                <div class="flex space-x-4">
                                    <a href="https://x.com/TecNM_MX" target="_blank" rel="noopener noreferrer" class="text-white hover:text-blue-300 transition-colors">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                    </a>
                                    <a href="https://www.facebook.com/SomosTecNM/" target="_blank" rel="noopener noreferrer" class="text-white hover:text-blue-400 transition-colors">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </a>
                                    <a href="https://www.youtube.com/user/SNESTMX" target="_blank" rel="noopener noreferrer" class="text-white hover:text-red-500 transition-colors">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                    </a>
                                </div>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-white/20 my-6"></div>
                <div class="text-center text-sm pt-6">
                    <p>&copy; Copyright 2026 <a href="https://www.tecnm.mx/" target="_blank" rel="noopener noreferrer" class="hover:text-blue-300 transition-colors underline">TecNM</a> - Programa de Estímulos al Desempeño del Personal Docente</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}
</style>
