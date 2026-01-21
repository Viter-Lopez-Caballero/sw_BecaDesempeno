<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue';
import SectionMain from '@/Components/SectionMain.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <LayoutMain>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Bienvenido al Sistema de Gestión de Becas!</div>
                </div>
            </div>
        </div>
    </LayoutMain>
</template>
