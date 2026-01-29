<template>
    <div v-if="isVisible">
        <!-- Group with Subitems -->
        <div v-if="item.items && item.items.length > 0">
            <!-- Expanded / Desktop View -->
            <div v-if="!collapsed || isMobile">
                <button
                    @click="toggleGroup"
                    :class="[
                        'w-full flex items-center justify-between px-6 py-3 transition-all duration-200 relative',
                        'before:absolute before:left-0 before:top-1/2 before:-translate-y-1/2 before:h-[80%] before:w-[4px] before:rounded-r-lg before:transition-colors',
                        isExpanded || isActive
                            ? 'bg-white/10 text-white before:bg-white'
                            : 'text-white/80 hover:bg-white/5 hover:text-white before:bg-transparent'
                    ]"
                >
                    <div class="flex items-center">
                        <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                            <path fill="currentColor" :d="item.icon || mdiViewModule"/>
                        </svg>
                        <span class="ml-3 text-sm font-medium">{{ item.label }}</span>
                    </div>
                    <svg
                        viewBox="0 0 24 24"
                        class="w-4 h-4 transition-transform duration-200"
                        :class="{ 'rotate-90': isExpanded }"
                    >
                        <path fill="currentColor" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </button>

                <!-- Recursive Submenu -->
                <div v-show="isExpanded" class="bg-[#152d47] py-1">
                    <SidebarItem
                        v-for="(subItem, index) in item.items"
                        :key="index"
                        :item="subItem"
                        :is-child="true"
                    />
                </div>
            </div>

            <!-- Collapsed Icon View -->
            <div v-else class="relative group">
                 <Link
                    v-if="item.items.length > 0"
                    :href="route(item.items[0].route)" 
                    :class="[
                        'flex items-center py-3 px-4 justify-center transition-all duration-200 relative',
                        'before:absolute before:left-0 before:top-1/2 before:-translate-y-1/2 before:h-[80%] before:w-[4px] before:rounded-r-lg before:transition-colors',
                        isActive
                            ? 'bg-white/10 text-white before:bg-white'
                            : 'text-white/80 hover:bg-white/5 hover:text-white before:bg-transparent'
                    ]"
                    :title="item.label"
                >
                    <svg viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
                        <path fill="currentColor" :d="item.icon || mdiViewModule"/>
                    </svg>
                </Link>
            </div>
        </div>

        <!-- Single Link Item -->
        <Link
            v-else
            :href="resolveRoute(item.route)"
            :class="[
                'flex items-center py-3 transition-all duration-200 relative',
                 'before:absolute before:left-0 before:top-1/2 before:-translate-y-1/2 before:h-[80%] before:w-[4px] before:rounded-r-lg before:transition-colors',
                isChild 
                    ? 'px-4 py-2.5 pl-12 text-gray-300 hover:bg-[#1e3a5f] hover:text-white' 
                    : (collapsed && !isMobile ? 'px-4 justify-center' : 'px-6'),
                !isChild && (isActive 
                    ? 'bg-white/10 text-white before:bg-white' 
                    : 'text-white/80 hover:bg-white/5 hover:text-white before:bg-transparent'),
                isChild && isActive ? 'bg-[#1e3a5f] text-white font-medium' : ''
            ]"
            :title="collapsed && !isMobile ? item.label : ''"
        >
            <svg v-if="item.icon" viewBox="0 0 24 24" :class="[isChild ? 'w-4 h-4' : 'w-5 h-5', 'flex-shrink-0']">
                <path fill="currentColor" :d="item.icon"/>
            </svg>
            <span v-show="!collapsed || isMobile || isChild" class="ml-3 text-sm font-medium">{{ item.label }}</span>
        </Link>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, inject, ref, onMounted } from 'vue';
import { mdiViewModule } from "@mdi/js";

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    isChild: {
        type: Boolean,
        default: false
    }
});

const page = usePage();
const collapsed = inject('sidebarCollapsed', ref(false));
const isMobile = inject('isMobile', ref(false));

const isExpanded = ref(false);

const resolveRoute = (routeName) => {
    try {
        return route(routeName);
    } catch (e) {
        return '#';
    }
};

const hasPermission = (permission) => {
    if (!permission) return true;
    return page.props.auth?.can?.[permission] || false;
};

// Check if item or any of its children matches current route
const isActive = computed(() => {
    if (props.item.items) {
        return props.item.items.some(sub => {
            try {
                return route().current(sub.route) || route().current(`${sub.route}.*`);
            } catch { return false; }
        });
    }
    
    try {
        return route().current(props.item.route) || route().current(`${props.item.route}.*`);
    } catch { return false; }
});

// Determine visibility based on permission vs children permissions
const isVisible = computed(() => {
    // If it's a group
    if (props.item.items && props.item.items.length > 0) {
        // Show if ANY child is visible
        return props.item.items.some(sub => hasPermission(sub.permission));
    }
    // If single item
    return hasPermission(props.item.permission);
});

const toggleGroup = () => {
    isExpanded.value = !isExpanded.value;
};

onMounted(() => {
    if (isActive.value) {
        isExpanded.value = true;
    }
});
</script>
