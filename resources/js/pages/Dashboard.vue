<script setup>
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <LayoutAuthenticated>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Bienvenido al Sistema de Gestión de Becas!</h2>
                    <p class="text-gray-600">Has iniciado sesión correctamente como Super Administrador.</p>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
