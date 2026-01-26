<script setup>
import { Head } from '@inertiajs/vue3';
import DynamicLayout from '@/layouts/DynamicLayout.vue';
</script>

<template>
    <DynamicLayout>
        <Head title="Inicio" />

        <div class="flex items-center justify-center min-h-[calc(100vh-200px)]">
            <h1 class="text-4xl font-bold text-[#1B396A]">Dashboard</h1>
        </div>
    </DynamicLayout>
</template>
