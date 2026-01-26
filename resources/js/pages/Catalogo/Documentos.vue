<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Documentos" />

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Documentos</h1>
                        <p class="text-gray-600 mt-2">Repositorio de documentos y formatos oficiales.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Subir Documento
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6 text-center hover:shadow-md transition cursor-pointer">
                    <div class="inline-flex p-4 bg-red-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Formatos PDF</h3>
                    <p class="text-3xl font-bold text-gray-900 my-2">156</p>
                    <p class="text-sm text-gray-500">documentos</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 text-center hover:shadow-md transition cursor-pointer">
                    <div class="inline-flex p-4 bg-blue-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Word</h3>
                    <p class="text-3xl font-bold text-gray-900 my-2">89</p>
                    <p class="text-sm text-gray-500">documentos</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 text-center hover:shadow-md transition cursor-pointer">
                    <div class="inline-flex p-4 bg-green-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Excel</h3>
                    <p class="text-3xl font-bold text-gray-900 my-2">45</p>
                    <p class="text-sm text-gray-500">documentos</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 text-center hover:shadow-md transition cursor-pointer">
                    <div class="inline-flex p-4 bg-gray-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Otros</h3>
                    <p class="text-3xl font-bold text-gray-900 my-2">23</p>
                    <p class="text-sm text-gray-500">documentos</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <input 
                        type="search" 
                        placeholder="Buscar documentos..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="p-4 hover:bg-gray-50 transition cursor-pointer flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Formato de Solicitud de Beca.pdf</h4>
                                <p class="text-sm text-gray-500">Actualizado: 15 Ene 2026 • 2.3 MB</p>
                            </div>
                        </div>
                        <button class="px-4 py-2 text-sm text-[#1B396A] hover:text-[#002B5C] font-medium">
                            Descargar
                        </button>
                    </div>
                    <div class="p-4 hover:bg-gray-50 transition cursor-pointer flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Manual de Usuario.docx</h4>
                                <p class="text-sm text-gray-500">Actualizado: 10 Ene 2026 • 1.8 MB</p>
                            </div>
                        </div>
                        <button class="px-4 py-2 text-sm text-[#1B396A] hover:text-[#002B5C] font-medium">
                            Descargar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
