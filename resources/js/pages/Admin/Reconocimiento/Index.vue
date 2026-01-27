<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
</script>

<template>
    <AdminLayout>
        <Head title="Reconocimientos - Admin" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h1 class="text-2xl font-bold text-gray-900">Reconocimientos</h1>
                <p class="text-gray-600 mt-2">Gestión de reconocimientos y distinciones</p>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-gray-600">Módulo de reconocimientos en desarrollo...</p>
            </div>
        </div>
    </AdminLayout>
</template>
