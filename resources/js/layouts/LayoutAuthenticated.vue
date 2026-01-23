<template>
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <!-- Mobile Overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="sidebarOpen && isMobile"
                @click="sidebarOpen = false"
                class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            ></div>
        </Transition>

        <!-- Sidebar -->
        <aside
            :class="[
                'bg-[#1e3a5f] text-white flex flex-col shadow-lg transition-all duration-300 z-50',
                sidebarCollapsed && !isMobile
                    ? 'w-20'
                    : 'w-64',
                isMobile
                    ? 'fixed inset-y-0 left-0 transform ' + (sidebarOpen ? 'translate-x-0' : '-translate-x-full')
                    : 'relative'
            ]"
        >
            <!-- Logo Section -->
            <div class="p-6 flex items-center justify-center border-b border-[#2d4a6f] min-h-[80px]">
                <Transition
                    mode="out-in"
                    enter-active-class="transition-opacity duration-200"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition-opacity duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <img
                        v-if="!sidebarCollapsed || isMobile"
                        key="full-logo"
                        src="/img/LogoTecNMCompleto.png"
                        alt="TecNM Logo"
                        class="w-full h-auto max-w-[180px]"
                    />
                    <img
                        v-else
                        key="small-logo"
                        src="/img/LogoTecNMCerrado.png"
                        alt="TecNM"
                        class="w-10 h-10"
                    />
                </Transition>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 custom-scrollbar">
                <!-- Inicio -->
                <Link
                    :href="route('inicio.dashboard')"
                    :class="[
                        'flex items-center py-3 transition-all duration-200 group relative',
                        sidebarCollapsed && !isMobile ? 'px-4 justify-center' : 'px-6',
                        isActiveRoute('inicio.dashboard')
                            ? 'bg-white/10 text-white border-l-4 border-white'
                            : 'text-white/80 hover:bg-white/5 hover:text-white border-l-4 border-transparent'
                    ]"
                    :title="sidebarCollapsed && !isMobile ? 'Inicio' : ''"
                >
                    <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                        <path fill="currentColor" :d="mdiHome"/>
                    </svg>
                    <span v-show="!sidebarCollapsed || isMobile" class="ml-3 font-medium">Inicio</span>
                </Link>

                <!-- Control de Solicitudes -->
                <Link
                    :href="route('inicio.dashboard')"
                    :class="[
                        'flex items-center py-3 transition-all duration-200 group relative',
                        sidebarCollapsed && !isMobile ? 'px-4 justify-center' : 'px-6',
                        'text-white/80 hover:bg-white/5 hover:text-white border-l-4 border-transparent'
                    ]"
                    :title="sidebarCollapsed && !isMobile ? 'Control de Solicitudes' : ''"
                >
                    <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                        <path fill="currentColor" :d="mdiFileDocumentMultiple"/>
                    </svg>
                    <span v-show="!sidebarCollapsed || isMobile" class="ml-3 font-medium">Control de Solicitudes</span>
                </Link>

                <!-- Convocatorias -->
                <Link
                    :href="route('inicio.dashboard')"
                    :class="[
                        'flex items-center py-3 transition-all duration-200 group relative',
                        sidebarCollapsed && !isMobile ? 'px-4 justify-center' : 'px-6',
                        'text-white/80 hover:bg-white/5 hover:text-white border-l-4 border-transparent'
                    ]"
                    :title="sidebarCollapsed && !isMobile ? 'Convocatorias' : ''"
                >
                    <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                        <path fill="currentColor" :d="mdiBullhorn"/>
                    </svg>
                    <span v-show="!sidebarCollapsed || isMobile" class="ml-3 font-medium">Convocatorias</span>
                </Link>

                <!-- Seguridad (Collapsible) -->
                <div v-if="!sidebarCollapsed || isMobile" class="mt-1">
                    <button
                        @click="securityExpanded = !securityExpanded"
                        :class="[
                            'w-full flex items-center justify-between px-6 py-3 transition-all duration-200 relative',
                            securityExpanded || isSecurityActive()
                                ? 'bg-white/10 text-white border-l-4 border-white'
                                : 'text-white/80 hover:bg-white/5 hover:text-white border-l-4 border-transparent'
                        ]"
                    >
                        <div class="flex items-center">
                            <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                                <path fill="currentColor" :d="mdiSecurity"/>
                            </svg>
                            <span class="ml-3 font-medium">Seguridad</span>
                        </div>
                        <svg
                            viewBox="0 0 24 24"
                            class="w-4 h-4 transition-transform duration-200"
                        >
                            <path fill="currentColor" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                        </svg>
                    </button>

                    <!-- Submenu -->
                    <div
                        v-show="securityExpanded"
                        class="bg-[#152d47] py-1"
                    >
                        <Link
                            v-for="item in securityItems"
                            :key="item.route"
                            :href="route(item.route)"
                            :class="[
                                'flex items-center px-4 py-2.5 pl-12 transition-colors duration-200',
                                isActiveRoute(item.route)
                                    ? 'bg-[#1e3a5f] text-white font-medium'
                                    : 'text-gray-300 hover:bg-[#1e3a5f] hover:text-white'
                            ]"
                        >
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0">
                                <path fill="currentColor" :d="item.icon"/>
                            </svg>
                            <span class="ml-3 text-sm">{{ item.label }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Seguridad Icon Only (Collapsed) -->
                <Link
                    v-else
                    :href="route('modules.index')"
                    :class="[
                        'flex items-center py-3 px-4 justify-center transition-all duration-200 group relative',
                        isSecurityActive()
                            ? 'bg-white/10 text-white border-l-4 border-white'
                            : 'text-white/80 hover:bg-white/5 hover:text-white border-l-4 border-transparent'
                    ]"
                    title="Seguridad"
                >
                    <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                        <path fill="currentColor" :d="mdiSecurity"/>
                    </svg>
                </Link>

                <!-- Catálogo (Collapsible) -->
                <div v-if="!sidebarCollapsed || isMobile" class="mt-1">
                    <button
                        @click="catalogExpanded = !catalogExpanded"
                        :class="[
                            'w-full flex items-center justify-between px-6 py-3 transition-all duration-200 relative',
                            catalogExpanded || isCatalogActive()
                                ? 'bg-white/10 text-white border-l-4 border-white'
                                : 'text-white/80 hover:bg-white/5 hover:text-white border-l-4 border-transparent'
                        ]"
                    >
                        <div class="flex items-center">
                            <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                                <path fill="currentColor" :d="mdiBookOpenPageVariant"/>
                            </svg>
                            <span class="ml-3 font-medium">Catálogo</span>
                        </div>
                        <svg
                            viewBox="0 0 24 24"
                            class="w-4 h-4 transition-transform duration-200"
                        >
                            <path fill="currentColor" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                        </svg>
                    </button>

                    <!-- Submenu -->
                    <div
                        v-show="catalogExpanded"
                        class="bg-[#152d47] py-1"
                    >
                        <Link
                            v-for="item in catalogItems"
                            :key="item.route"
                            :href="route(item.route)"
                            :class="[
                                'flex items-center px-4 py-2.5 pl-12 transition-colors duration-200',
                                isActiveRoute(item.route)
                                    ? 'bg-[#1e3a5f] text-white font-medium'
                                    : 'text-gray-300 hover:bg-[#1e3a5f] hover:text-white'
                            ]"
                        >
                            <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0">
                                <path fill="currentColor" :d="item.icon"/>
                            </svg>
                            <span class="ml-3 text-sm">{{ item.label }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Catálogo Icon Only (Collapsed) -->
                <Link
                    v-else
                    :href="route('inicio.dashboard')"
                    :class="[
                        'flex items-center py-3 px-4 justify-center transition-all duration-200 group relative mt-1',
                        isCatalogActive()
                            ? 'bg-white/10 text-white border-l-4 border-white'
                            : 'text-white/80 hover:bg-white/5 hover:text-white border-l-4 border-transparent'
                    ]"
                    title="Catálogo"
                >
                    <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                        <path fill="currentColor" :d="mdiBookOpenPageVariant"/>
                    </svg>
                </Link>
            </nav>

            <!-- User Profile -->
            <div class="relative p-4 border-t border-[#2d4a6f]">
                <button
                    @click="profileMenuOpen = !profileMenuOpen"
                    class="w-full flex items-center space-x-3 hover:bg-white/5 p-2 rounded transition-colors duration-200"
                >
                    <div class="w-10 h-10 rounded-full bg-blue-400 flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ userInitials }}
                    </div>
                    <div class="flex-1 min-w-0 text-left">
                        <p class="text-sm font-medium truncate">{{ userName }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ userEmail }}</p>
                    </div>
                    <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': profileMenuOpen }">
                        <path fill="currentColor" d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <Transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <div
                        v-show="profileMenuOpen"
                        class="absolute bottom-full left-4 right-4 mb-2 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-50"
                    >
                        <Link
                            :href="route('profile.edit')"
                            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150"
                            @click="profileMenuOpen = false"
                        >
                            <svg viewBox="0 0 24 24" class="w-5 h-5 mr-3 text-gray-500">
                                <path fill="currentColor" :d="mdiAccount"/>
                            </svg>
                            <span>Perfil</span>
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150 border-t border-gray-100"
                            @click="profileMenuOpen = false"
                        >
                            <svg viewBox="0 0 24 24" class="w-5 h-5 mr-3 text-gray-500">
                                <path fill="currentColor" :d="mdiLogout"/>
                            </svg>
                            <span>Cerrar sesión</span>
                        </Link>
                    </div>
                </Transition>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10 px-4 lg:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Mobile Menu Button -->
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100 transition-colors duration-200 mr-4"
                        >
                            <svg viewBox="0 0 24 24" class="w-6 h-6">
                                <path fill="currentColor" :d="mdiMenu"/>
                            </svg>
                        </button>

                        <!-- Desktop Toggle Button -->
                        <button
                            @click="sidebarCollapsed = !sidebarCollapsed"
                            class="hidden lg:block p-2 rounded-md text-gray-600 hover:bg-gray-100 transition-colors duration-200 mr-4"
                            :title="sidebarCollapsed ? 'Expandir sidebar' : 'Colapsar sidebar'"
                        >
                            <svg viewBox="0 0 24 24" class="w-6 h-6">
                                <path fill="currentColor" :d="mdiMenu"/>
                            </svg>
                        </button>

                        <h1 class="text-xl font-semibold text-gray-800">{{ pageTitle }}</h1>
                    </div>
                    
                    <!-- Spacer for mobile -->
                    <div class="w-10 lg:hidden"></div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    mdiHome,
    mdiSecurity,
    mdiViewModule,
    mdiLockCheckOutline,
    mdiAccountSupervisor,
    mdiAccount,
    mdiFileDocumentMultiple,
    mdiBullhorn,
    mdiBookOpenPageVariant,
    mdiLogout,
    mdiOfficeBuilding,
    mdiLightbulbOnOutline,
    mdiCalendar,
    mdiClipboardTextOutline,
    mdiChevronLeft,
    mdiChevronRight,
    mdiMenu,
} from "@mdi/js";

const page = usePage();
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const securityExpanded = ref(true);
const catalogExpanded = ref(false);
const profileMenuOpen = ref(false);

// Detect mobile
const isMobile = ref(false);

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024;
};

// Check on mount and resize
import { onMounted, onUnmounted } from 'vue';

onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile);
});

const userName = computed(() => page.props.auth?.user?.name || 'Usuario');
const userEmail = computed(() => page.props.auth?.user?.email || '');
const pageTitle = computed(() => page.props.title || 'Dashboard');

const userInitials = computed(() => {
    const name = userName.value;
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

const securityItems = [
    {
        label: "Módulos",
        route: "modules.index",
        icon: mdiViewModule,
    },
    {
        label: "Permisos",
        route: "permissions.index",
        icon: mdiLockCheckOutline,
    },
    {
        label: "Roles",
        route: "roles.index",
        icon: mdiAccountSupervisor,
    },
    {
        label: "Usuarios",
        route: "users.index",
        icon: mdiAccount,
    },
];

const catalogItems = [
    {
        label: "Campus",
        route: "inicio.dashboard",
        icon: mdiOfficeBuilding,
    },
    {
        label: "Áreas Prioritarias",
        route: "inicio.dashboard",
        icon: mdiLightbulbOnOutline,
    },
    {
        label: "Documentos",
        route: "inicio.dashboard",
        icon: mdiFileDocumentMultiple,
    },
    {
        label: "Calendario",
        route: "inicio.dashboard",
        icon: mdiCalendar,
    },
    {
        label: "Rúbrica",
        route: "inicio.dashboard",
        icon: mdiClipboardTextOutline,
    },
];

const isActiveRoute = (routeName) => {
    return route().current(routeName) || route().current(`${routeName}.*`);
};

const isSecurityActive = () => {
    return securityItems.some(item => isActiveRoute(item.route));
};

const isCatalogActive = () => {
    return catalogItems.some(item => isActiveRoute(item.route));
};
</script>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Firefox */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}
</style>
