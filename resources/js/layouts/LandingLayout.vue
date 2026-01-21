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

        <footer class="bg-[#1B396A] text-white py-10 text-center text-sm">
            <p>&copy; 2026 Sistema de Gestión de Becas. Todos los derechos reservados.</p>
        </footer>
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}
</style>
