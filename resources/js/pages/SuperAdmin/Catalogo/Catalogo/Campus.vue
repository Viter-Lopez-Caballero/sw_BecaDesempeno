<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Campus" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Campus</h1>
                        <p class="text-gray-600 mt-2">Gestión de campus del Tecnológico Nacional de México.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Agregar Campus
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Campus</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">254</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Activos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">248</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-gray-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Inactivos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">6</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-full">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <h2 class="text-lg font-semibold text-gray-900">Lista de Campus</h2>
                        <div class="flex gap-3">
                            <input 
                                type="search" 
                                placeholder="Buscar campus..." 
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option>Todos los estados</option>
                                <option>Activos</option>
                                <option>Inactivos</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matrícula</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TEC-001</td>
                                <td class="px-6 py-4 text-sm text-gray-900">Instituto Tecnológico de Cuernavaca</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Cuernavaca</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Morelos</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">4,500</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-[#1B396A] hover:text-[#002B5C] mr-3">Ver</button>
                                    <button class="text-gray-600 hover:text-gray-900">Editar</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TEC-002</td>
                                <td class="px-6 py-4 text-sm text-gray-900">Instituto Tecnológico de Tijuana</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Tijuana</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Baja California</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12,300</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-[#1B396A] hover:text-[#002B5C] mr-3">Ver</button>
                                    <button class="text-gray-600 hover:text-gray-900">Editar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
