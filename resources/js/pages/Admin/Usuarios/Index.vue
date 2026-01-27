<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
</script>

<template>
    <AdminLayout>
        <Head title="Gestión de Usuarios - Admin" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
                        <p class="text-gray-600 mt-2">Administrar usuarios del sistema</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Nuevo Usuario
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <input 
                        type="search" 
                        placeholder="Buscar usuario..." 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full md:w-96"
                    />
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Usuario Ejemplo</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">usuario@ejemplo.com</td>
                                <td class="px-6 py-4 text-sm text-gray-900">Docente</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-[#1B396A] hover:text-[#002B5C] mr-3">Editar</button>
                                    <button class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
