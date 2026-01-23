<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';

const props = defineProps({
    permissions: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Permisos" />

        <div class="flex items-center justify-center min-h-[calc(100vh-200px)]">
            <h1 class="text-4xl font-bold text-[#1B396A]">Permisos</h1>
        </div>
    </LayoutAuthenticated>
</template>
