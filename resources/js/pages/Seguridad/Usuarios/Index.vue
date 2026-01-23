<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';

const props = defineProps({
    users: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Usuarios" />

        <div class="flex items-center justify-center min-h-[calc(100vh-200px)]">
            <h1 class="text-4xl font-bold text-[#1B396A]">Usuarios</h1>
        </div>
    </LayoutAuthenticated>
</template>
