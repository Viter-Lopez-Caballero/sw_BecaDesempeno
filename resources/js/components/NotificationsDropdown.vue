<template>
    <div class="relative" ref="dropdownRef">
        <!-- Notifications Button -->
        <button
            @click="toggleDropdown"
            class="p-2 rounded-md text-gray-900 hover:bg-gray-200 transition-colors duration-200 relative"
            title="Notificaciones"
        >
            <svg viewBox="0 0 24 24" class="w-6 h-6" style="fill: currentColor;">
                <path :d="mdiBellOutline"/>
            </svg>
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[500px] overflow-hidden flex flex-col"
            >
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-[#1B396A] to-[#2d5a9e] flex items-center justify-between">
                    <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" style="fill: currentColor;">
                            <path :d="mdiBellOutline"/>
                        </svg>
                        Notificaciones
                    </h3>
                    <button
                        v-if="notifications.length > 0 && hasUnread"
                        @click="markAllAsRead"
                        class="text-white text-xs hover:text-gray-200 transition underline"
                    >
                        Marcar todas como leídas
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="overflow-y-auto flex-1">
                    <div v-if="loading" class="p-8 text-center text-gray-500">
                        <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-[#1B396A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Cargando...
                    </div>

                    <div v-else-if="notifications.length === 0" class="p-8 text-center text-gray-500">
                        <svg viewBox="0 0 24 24" class="w-16 h-16 mx-auto mb-3 text-gray-300" style="fill: currentColor;">
                            <path :d="mdiBellOutline"/>
                        </svg>
                        <p class="font-medium">No hay notificaciones</p>
                        <p class="text-sm mt-1">Cuando recibas notificaciones aparecerán aquí</p>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div
                            v-for="notification in notifications"
                            :key="notification.id"
                            class="p-4 hover:bg-gray-50 transition cursor-pointer"
                            :class="{ 'bg-blue-50': !notification.read_at }"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                                            <path :d="mdiFileDocumentMultiple"/>
                                        </svg>
                                        <h4 class="font-semibold text-gray-900 text-sm truncate">
                                            {{ notification.title }}
                                        </h4>
                                        <span v-if="!notification.read_at" class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full"></span>
                                    </div>
                                    
                                    <!-- Notification Content Based on Type -->
                                    <div v-if="notification.type === 'weekly_summary' && notification.data" class="mb-2">
                                        <div class="bg-gradient-to-r from-[#1B396A] to-[#2d5a9e] text-white rounded px-3 py-2 text-sm font-semibold">
                                            Total de Solicitudes: {{ notification.data.total || 0 }}
                                        </div>
                                    </div>

                                    <div v-else-if="notification.type === 'evaluator_assignment' && notification.data" class="mb-2">
                                        <p class="text-sm text-gray-700 mb-1">{{ notification.data.message }}</p>
                                        <div class="bg-gradient-to-r from-[#1B396A] to-[#2d5a9e] text-white rounded px-3 py-2 text-sm font-semibold">
                                            Evaluaciones asignadas: {{ notification.data.count || 0 }}
                                        </div>
                                    </div>

                                    <div v-else-if="notification.type === 'application_verdict' && notification.data" class="mb-2">
                                        <p class="text-sm" :class="notification.data.status === 'approved' ? 'text-green-700' : 'text-red-700'">
                                            {{ notification.data.message }}
                                        </p>
                                    </div>

                                    <div v-else-if="notification.type === 'announcement_stage_change' && notification.data" class="mb-2">
                                        <p class="text-sm text-gray-700">{{ notification.data.message }}</p>
                                        <div class="bg-blue-100 text-blue-800 rounded px-3 py-1 text-xs font-medium inline-block mt-1">
                                            {{ notification.data.stage }}
                                        </div>
                                    </div>

                                    <div v-else-if="notification.type === 'announcement_date_change' && notification.data" class="mb-2">
                                        <p class="text-sm text-gray-700">{{ notification.data.message }}</p>
                                    </div>

                                    <div v-else-if="notification.data && typeof notification.data === 'object'" class="mb-2">
                                        <p class="text-sm text-gray-700">{{ notification.data.message || JSON.stringify(notification.data) }}</p>
                                    </div>

                                    <p class="text-xs text-gray-500">
                                        {{ formatDate(notification.created_at) }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col gap-1 flex-shrink-0">
                                    <button
                                        v-if="!notification.read_at"
                                        @click.stop="markAsRead(notification.id)"
                                        class="p-1.5 text-green-600 border border-green-600 rounded hover:bg-green-600 hover:text-white transition"
                                        title="Marcar como leída"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                                            <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click.stop="deleteNotification(notification.id)"
                                        class="p-1.5 text-red-600 border border-red-600 rounded hover:bg-red-600 hover:text-white transition"
                                        title="Eliminar"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor">
                                            <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { mdiBellOutline, mdiFileDocumentMultiple } from '@mdi/js';
import { alertaExito, alertaError } from '@/utils/alerts.js';

const page = usePage();
const isOpen = ref(false);
const loading = ref(false);
const notifications = ref([]);
const dropdownRef = ref(null);

// Unread count from global props
const unreadCount = computed(() => page.props.unreadNotifications || 0);

// Check if there are unread notifications
const hasUnread = computed(() => notifications.value.some(n => !n.read_at));

// Get notification route based on user role
const getNotificationRoute = (action) => {
    // Use primaryRole or check roles array
    const primaryRole = page.props.auth?.primaryRole;
    const userRoles = page.props.auth?.roles;
    
    let prefix = 'admin';
    
    if (primaryRole === 'Admin' || (userRoles && userRoles.includes && userRoles.includes('Admin'))) {
        prefix = 'admin';
    } else if (primaryRole === 'Evaluador' || (userRoles && userRoles.includes && userRoles.includes('Evaluador'))) {
        prefix = 'evaluator';
    } else if (primaryRole === 'Docente' || (userRoles && userRoles.includes && userRoles.includes('Docente'))) {
        prefix = 'teacher';
    }
    
    return `${prefix}.notifications.${action}`;
};

// Toggle dropdown
const toggleDropdown = async () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        await loadNotifications();
    }
};

// Load notifications
const loadNotifications = async () => {
    loading.value = true;
    try {
        const response = await fetch(route(getNotificationRoute('index')), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        notifications.value = data.notifications?.data || [];
    } catch (error) {
        console.error('Error loading notifications:', error);
    } finally {
        loading.value = false;
    }
};

// Mark as read
const markAsRead = async (id) => {
    try {
        await router.post(route(getNotificationRoute('mark-as-read'), id), {}, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                const notification = notifications.value.find(n => n.id === id);
                if (notification) {
                    notification.read_at = new Date().toISOString();
                }
                alertaExito('Marcada como leída', 'Notificación marcada correctamente');
                // Reload to update unread count
                router.reload({ only: ['unreadNotifications'] });
            },
            onError: () => {
                alertaError('Error', 'No se pudo marcar la notificación');
            }
        });
    } catch (error) {
        console.error('Error marking as read:', error);
    }
};

// Mark all as read
const markAllAsRead = async () => {
    try {
        await router.post(route(getNotificationRoute('mark-all-as-read')), {}, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                notifications.value.forEach(n => {
                    n.read_at = new Date().toISOString();
                });
                alertaExito('Todas marcadas como leídas', 'Notificaciones actualizadas correctamente');
                // Reload to update unread count
                router.reload({ only: ['unreadNotifications'] });
            },
            onError: () => {
                alertaError('Error', 'No se pudieron marcar las notificaciones');
            }
        });
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
};

// Delete notification
const deleteNotification = async (id) => {
    try {
        await router.delete(route(getNotificationRoute('destroy'), id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                notifications.value = notifications.value.filter(n => n.id !== id);
                alertaExito('Eliminada', 'Notificación eliminada correctamente');
                // Reload to update unread count
                router.reload({ only: ['unreadNotifications'] });
            },
            onError: () => {
                alertaError('Error', 'No se pudo eliminar la notificación');
            }
        });
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
};

// Calculate total
const calculateTotal = (data) => {
    if (!data) return 0;
    return Object.values(data).reduce((sum, val) => sum + val, 0);
};

// Format date
const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = Math.floor((now - date) / (1000 * 60 * 60));
    
    if (diffInHours < 1) {
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        return diffInMinutes <= 1 ? 'Hace un momento' : `Hace ${diffInMinutes} minutos`;
    }
    if (diffInHours < 24) {
        return `Hace ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`;
    }
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) {
        return `Hace ${diffInDays} día${diffInDays > 1 ? 's' : ''}`;
    }
    
    return date.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
