<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import Pagination from '@/Shared/Pagination.vue';
import { ref } from 'vue';
import { mdiClipboardTextOutline } from '@mdi/js';
import { useCan } from '@/composables/usePermissions';

const props = defineProps({
    rubrics: {
        type: Object,
        required: true,
    },
});

const toggleActive = (rubric) => {
    router.post(route('catalogo.rubrics.toggle-active', rubric.id), {}, {
        preserveScroll: true,
        onError: (errors) => {
             // If error occurs, revert the checkbox visually or alert user
             // Since it's a checkbox, the simplest (lazy) way to revert is to force a reload or just alert.
             // We can also rely on the flash message if using a global toast, but let's be explicit.
             if (errors.error) {
                 alert(errors.error); // Show the backend error message
                 // Reload to revert checkbox state visually if needed, though inertia might handle it if preserving state
                 location.reload(); 
             }
        },
        onFinish: () => {
             // Ensure state is synced
        }
    });
};

const deleteItem = (id) => {
    if (confirm('¿Estás seguro de eliminar esta rúbrica?')) {
        router.delete(route('catalogo.rubrics.destroy', id));
    }
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Rúbricas" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Rúbricas</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiClipboardTextOutline"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Catálogo</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Rúbricas</span>
                    </div>
                </div>
                <Link :href="route('catalogo.rubrics.create')" class="px-4 py-2.5 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                        <path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/>
                    </svg>
                    Agregar
                </Link>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#1B396A] text-white uppercase text-xs font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-4 tracking-wider">#</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Título</th>
                                <th scope="col" class="px-6 py-4 tracking-wider">Activa</th>
                                <th scope="col" class="px-6 py-4 text-center tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="(rubric, index) in rubrics.data" :key="rubric.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ rubrics.from + index }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ rubric.title }}</td>
                                <td class="px-6 py-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" :checked="rubric.is_active" @change="toggleActive(rubric)" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1B396A]"></div>
                                    </label>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link :href="route('catalogo.rubrics.edit', rubric.id)" class="p-2 text-[#1B396A] border border-[#1B396A] rounded-full hover:bg-[#1B396A] hover:text-white transition group" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                <path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/>
                                            </svg>
                                        </Link>
                                        <button @click="deleteItem(rubric.id)" class="p-2 text-red-600 border border-red-600 rounded-full hover:bg-red-600 hover:text-white transition group" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
                                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="rubrics.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No se encontraron registros
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                 <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                     <Pagination :links="rubrics.links" :total="rubrics.total" :from="rubrics.from" :to="rubrics.to" />
                </div>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
