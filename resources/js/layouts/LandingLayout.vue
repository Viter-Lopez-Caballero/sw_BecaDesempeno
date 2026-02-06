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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20 relative">
                    <div class="flex items-center md:static">
                        <div class="-mr-2 flex items-center md:hidden mr-4">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-800 focus:outline-none focus:bg-blue-800 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center space-x-3 absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 md:static md:transform-none md:translate-x-0 md:translate-y-0">
                            <img src="/img/LogoTecNMCompleto.png" alt="TecNM" class="block md:hidden h-10 w-auto" />
                            
                            <img src="/img/LogoTecNMCompleto.png" alt="TecNM" class="hidden md:block h-12 w-auto" />
                            
                            <img src="/img/LogoCenidetCompleto.png" alt="Cenidet" class="h-10 md:h-12 w-auto" />
                            
                        </div>
                    </div>

                    <div class="hidden md:flex space-x-8">
                        <Link :href="route('inicio')" :class="{'border-b-2 border-white': route().current('inicio')}" class="hover:text-blue-300 transition font-medium py-1">INICIO</Link>
                        <Link :href="route('convocatoria')" :class="{'border-b-2 border-white': route().current('convocatoria')}" class="hover:text-blue-300 transition font-medium py-1">CONVOCATORIA</Link>
                        <Link :href="route('documentos')" :class="{'border-b-2 border-white': route().current('documentos')}" class="hover:text-blue-300 transition font-medium py-1">DOCUMENTOS</Link>
                        <Link :href="route('contacto')" :class="{'border-b-2 border-white': route().current('contacto')}" class="hover:text-blue-300 transition font-medium py-1">CONTACTO</Link>
                    </div>

                    <div class="hidden md:flex items-center space-x-4">
                        <Link :href="route('login')" class="text-white hover:text-blue-300 transition font-medium">
                            INICIAR SESIÓN
                        </Link>
                        <Link
                            :href="route('register')"
                            class="text-white hover:text-blue-300 transition font-medium"
                        >
                            REGISTRARSE
                        </Link>
                    </div>
                </div>
            </div>

            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="md:hidden bg-[#162e55]"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <Link :href="route('inicio')" :class="{'bg-blue-800 border-l-4 border-white': route().current('inicio')}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition duration-150 ease-in-out">INICIO</Link>
                    <Link :href="route('convocatoria')" :class="{'bg-blue-800 border-l-4 border-white': route().current('convocatoria')}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition duration-150 ease-in-out">CONVOCATORIA</Link>
                    <Link :href="route('documentos')" :class="{'bg-blue-800 border-l-4 border-white': route().current('documentos')}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition duration-150 ease-in-out">DOCUMENTOS</Link>
                    <Link :href="route('contacto')" :class="{'bg-blue-800 border-l-4 border-white': route().current('contacto')}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 hover:border-blue-300 transition duration-150 ease-in-out">CONTACTO</Link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-blue-800">
                    <div class="space-y-1">
                        <Link :href="route('login')" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 transition duration-150 ease-in-out">
                            INICIAR SESIÓN
                        </Link>
                        <Link :href="route('register')" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-blue-100 hover:text-white hover:bg-blue-800 transition duration-150 ease-in-out">
                            REGISTRARSE
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow pt-20">
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
                                Av. Universidad 1200, Col. Xoco, Ciudad de México, Alcaldía Benito Juárez C.P. 03330
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
