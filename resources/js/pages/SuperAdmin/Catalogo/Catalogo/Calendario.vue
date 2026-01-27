<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Calendario" />

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Calendario Académico</h1>
                        <p class="text-gray-600 mt-2">Fechas importantes y eventos del ciclo escolar 2026.</p>
                    </div>
                    <button class="px-4 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#002B5C] transition">
                        Agregar Evento
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Enero 2026</h2>
                            <div class="flex gap-2">
                                <button class="p-2 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button class="p-2 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-7 gap-2 text-center mb-2">
                            <div class="text-xs font-semibold text-gray-500 py-2">Dom</div>
                            <div class="text-xs font-semibold text-gray-500 py-2">Lun</div>
                            <div class="text-xs font-semibold text-gray-500 py-2">Mar</div>
                            <div class="text-xs font-semibold text-gray-500 py-2">Mié</div>
                            <div class="text-xs font-semibold text-gray-500 py-2">Jue</div>
                            <div class="text-xs font-semibold text-gray-500 py-2">Vie</div>
                            <div class="text-xs font-semibold text-gray-500 py-2">Sáb</div>
                        </div>

                        <div class="grid grid-cols-7 gap-2 text-center">
                            <div class="text-gray-400 p-2">29</div>
                            <div class="text-gray-400 p-2">30</div>
                            <div class="text-gray-400 p-2">31</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">1</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">2</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">3</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">4</div>
                            
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">5</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">6</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">7</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">8</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">9</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">10</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">11</div>
                            
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">12</div>
                            <div class="p-2 rounded-lg bg-blue-100 text-blue-700 font-semibold cursor-pointer">13</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">14</div>
                            <div class="p-2 rounded-lg bg-green-100 text-green-700 font-semibold cursor-pointer relative">
                                15
                                <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-green-600 rounded-full"></span>
                            </div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">16</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">17</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">18</div>
                            
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">19</div>
                            <div class="p-2 rounded-lg bg-yellow-100 text-yellow-700 font-semibold cursor-pointer">20</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">21</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">22</div>
                            <div class="p-2 rounded-lg bg-blue-600 text-white font-semibold cursor-pointer">23</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">24</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">25</div>
                            
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">26</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">27</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">28</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">29</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">30</div>
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">31</div>
                            <div class="text-gray-400 p-2">1</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <h3 class="font-semibold text-gray-900 mb-4">Próximos Eventos</h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Fin de Registro</p>
                                    <p class="text-xs text-gray-500">13 Ene 2026</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Inicio de Evaluación</p>
                                    <p class="text-xs text-gray-500">15 Ene 2026</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg">
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Junta Consejo</p>
                                    <p class="text-xs text-gray-500">20 Ene 2026</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
