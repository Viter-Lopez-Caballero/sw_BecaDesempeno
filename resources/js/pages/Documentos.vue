<script setup>
import { Head } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
</script>

<template>
    <LandingLayout>
        <Head title="Documentos" />

        <!-- DOCUMENTOS Section -->
        <section class="py-16 bg-gray-50">
             <div class="max-w-4xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-12">
                     <h2 class="text-3xl font-bold text-[#002B5C] mb-4">Documentos de Trabajo 2026</h2>
                     <div class="h-1 w-24 bg-blue-500 mx-auto rounded"></div>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        <a href="#" class="block p-4 hover:bg-blue-50 transition flex items-center group">
                            <div class="bg-red-100 p-2 rounded text-red-600 mr-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">Reglas de Operación 2026</h4>
                                <p class="text-xs text-gray-500">PDF - 2.4 MB</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                         <a href="#" class="block p-4 hover:bg-blue-50 transition flex items-center group">
                            <div class="bg-red-100 p-2 rounded text-red-600 mr-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">Guía de Registro para Estudiantes</h4>
                                <p class="text-xs text-gray-500">PDF - 1.1 MB</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                         <a href="#" class="block p-4 hover:bg-blue-50 transition flex items-center group">
                            <div class="bg-blue-100 p-2 rounded text-blue-600 mr-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">Formato de Solicitud (Ejemplo)</h4>
                                <p class="text-xs text-gray-500">DOCX - 500 KB</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                </div>
             </div>
        </section>
    </LandingLayout>
</template>
