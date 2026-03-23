<template>
    <div class="space-y-1">
        <template v-for="(item, index) in items" :key="index">
            <!-- Section Header (no link, just a label) -->
            <div
                v-if="item.type === 'section'"
                :class="[
                    'pt-5 pb-1',
                    collapsed && !isMobile ? 'px-4' : 'px-6'
                ]"
            >
                <span
                    class="block text-[11px] font-semibold uppercase tracking-wider text-white/40 select-none truncate"
                    :class="collapsed && !isMobile ? 'text-center' : ''"
                    :title="item.label"
                >
                    {{ item.label }}
                </span>
            </div>

            <!-- Regular Sidebar Item -->
            <SidebarItem
                v-else
                :item="item"
            />
        </template>
    </div>
</template>

<script setup>
import { inject, ref } from 'vue';
import SidebarItem from './SidebarItem.vue';

const collapsed = inject('sidebarCollapsed', ref(false));
const isMobile = inject('isMobile', ref(false));

defineProps({
    items: {
        type: Array,
        required: true,
        default: () => []
    }
});
</script>
