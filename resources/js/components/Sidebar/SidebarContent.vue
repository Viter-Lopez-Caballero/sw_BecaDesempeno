<template>
    <div class="space-y-1">
        <!-- Render Sidebar Item Recursively -->
        <SidebarItem
            v-for="(item, index) in items"
            :key="index"
            :item="item"
        />
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
