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
                    <!-- Columna Izquierda: Dirección, Contacto y Redes Sociales -->
                    <div class="space-y-6">
                        <!-- Dirección -->
                        <div>
                            <h3 class="text-lg font-bold mb-3">Dirección</h3>
                            <p class="text-sm leading-relaxed">
                                Interior Internado Palmira S/N, Col. Palmira,<br />
                                C.P. 62490<br />
                                Cuernavaca, Morelos.
                            </p>
                        </div>

                        <!-- Contacto -->
                        <div>
                            <h3 class="text-lg font-bold mb-3">Contacto</h3>
                            <p class="text-sm">
                                Email: <a href="mailto:contacto@cenidet.tecnm.mx" class="hover:text-blue-300 transition-colors underline">contacto@cenidet.tecnm.mx</a>
                            </p>
                            <p class="text-sm mt-2">
                                Conmutador: 777 362 7770
                            </p>
                        </div>

                        <!-- Redes Sociales -->
                        <div>
                            <h3 class="text-lg font-bold mb-3">Síguenos En</h3>
                            <div class="py-4 rounded-lg">
                                <div class="flex space-x-4">
                                    <a href="https://x.com/CENIDET" target="_blank" rel="noopener noreferrer" class="text-white hover:text-blue-300 transition-colors">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                    </a>
                                    <a href="https://www.instagram.com/cenidet_oficial/" target="_blank" rel="noopener noreferrer" class="text-white hover:text-pink-400 transition-colors">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        </svg>
                                    </a>
                                    <a href="https://www.facebook.com/TecNMCenidet.mx/" target="_blank" rel="noopener noreferrer" class="text-white hover:text-blue-400 transition-colors">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </a>
                                    <a href="https://api.whatsapp.com/send/?phone=527773627770&text&type=phone_number&app_absent=0" target="_blank" rel="noopener noreferrer" class="text-white hover:text-green-400 transition-colors">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                    </a>
                                    <a href="https://www.youtube.com/channel/UCppsS5lV2CKqImeHeLt5zgw" target="_blank" rel="noopener noreferrer" class="text-white hover:text-red-500 transition-colors">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Mapas -->
                    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-lg overflow-hidden shadow-lg h-[350px]">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3771.0488636!2d-99.2225241!3d18.8765331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85cddf3a4772c8c3%3A0xca05f1ffbc24908!2sTecNM%20Cenidet!5e0!3m2!1ses!2smx!4v1737863200000!5m2!1ses!2smx"
                                width="100%" 
                                height="350" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                class="w-full"
                            ></iframe>
                        </div>
                        <div class="rounded-lg overflow-hidden shadow-lg h-[350px]">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3771.0!2d-99.2214021!3d18.8794382!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85cddf3b114d22ad%3A0xae54f8c7010a6b5d!2sCenidet%20campus%20Ing.%20Mecanica!5e0!3m2!1ses!2smx!4v1737863100000!5m2!1ses!2smx"
                                width="100%" 
                                height="350" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                class="w-full"
                            ></iframe>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
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
