<template>
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
                enter-active-class="transition-opacity duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-300"
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
                    class="w-10"
                />
            </Transition>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 custom-scrollbar">
            <SidebarContent :items="menu" />
        </nav>

        <!-- User Profile -->
        <div class="relative p-4 border-t border-[#2d4a6f]">
            <button
                @click="profileMenuOpen = !profileMenuOpen"
                class="w-full flex items-center space-x-3 hover:bg-white/5 p-2 rounded transition-colors duration-200 cursor-pointer"
            >
                <div class="w-10 h-10 rounded-full bg-[#152d47] flex items-center justify-center text-white font-semibold flex-shrink-0">
                    {{ userInitials }}
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <p class="text-sm font-medium truncate">{{ userName }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ userEmail }}</p>
                </div>
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': profileMenuOpen }">
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
                    class="absolute bottom-full left-4 mb-2 bg-white rounded-xl shadow-xl z-50 w-56"
                >
                    <!-- User Info Header -->
                    <div class="px-4 py-4 bg-gradient-to-r from-[#1e3a5f] to-[#2d4a6f] flex rounded-t-lg items-center gap-3">
                        <img
                            src="/img/LogoTecNMCerrado.png"
                            alt="TecNM"
                            class="w-10 p-2"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ userName }}</p>
                            <p class="text-xs text-gray-300 truncate">{{ userEmail }}</p>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-2">
                        <Link
                            :href="route('profile.edit')"
                            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150"
                            @click="profileMenuOpen = false"
                        >
                            <svg viewBox="0 0 24 24" class="w-4 h-4 mr-3 text-[#1e3a5f]">
                                <path fill="currentColor" :d="mdiAccount"/>
                            </svg>
                            <span class="font-medium">Perfil</span>
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-red-50 transition-colors duration-150 border-t border-gray-100"
                            @click="profileMenuOpen = false"
                        >
                            <svg viewBox="0 0 24 24" class="w-4 h-4 mr-3 text-red-600">
                                <path fill="currentColor" :d="mdiLogout"/>
                            </svg>
                            <span class="font-medium">Cerrar sesión</span>
                        </Link>
                    </div>
                </div>
            </Transition>
        </div>
    </aside>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, inject, ref } from 'vue';
import { mdiAccount, mdiLogout } from "@mdi/js";
import SidebarContent from './SidebarContent.vue';

defineProps({
    menu: {
        type: Array,
        required: true,
        default: () => []
    }
});

const page = usePage();
const sidebarOpen = inject('sidebarOpen', ref(false));
const sidebarCollapsed = inject('sidebarCollapsed', ref(false));
const isMobile = inject('isMobile', ref(false));
const profileMenuOpen = ref(false);

const userName = computed(() => page.props.auth?.user?.name || 'Usuario');
const userEmail = computed(() => page.props.auth?.user?.email || '');
const userInitials = computed(() => {
    const name = userName.value;
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});
</script>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 3px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }
.custom-scrollbar { scrollbar-width: thin; scrollbar-color: rgba(255, 255, 255, 0.2) transparent; }
</style>
