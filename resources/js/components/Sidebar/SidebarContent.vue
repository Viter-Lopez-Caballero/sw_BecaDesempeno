<template>
    <div class="space-y-1">
        <template v-for="(item, index) in items" :key="index">
            <!-- Section Header (no link, just a label) -->
            <div
                v-if="item.type === 'section'"
                class="px-6 pt-5 pb-1"
            >
                <span class="text-[11px] font-semibold uppercase tracking-wider text-white/40 select-none">
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
import SidebarItem from './SidebarItem.vue';

defineProps({
    items: {
        type: Array,
        required: true,
        default: () => []
    }
});
</script>
