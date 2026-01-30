<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue';
import { mdiClipboardTextOutline } from '@mdi/js';

const props = defineProps({
    title: String,
    routeName: String,
});

const form = useForm({
    title: '',
    is_active: false,
    questions: [],
});

const addQuestion = () => {
    form.questions.push({
        text: '',
        options: []
    });
};

const removeQuestion = (index) => {
    form.questions.splice(index, 1);
};

const addOption = (qIndex) => {
    const question = form.questions[qIndex];
    if (question.options.length >= 5) {
        alert('Máximo 5 respuestas por pregunta.');
        return;
    }
    question.options.push({ text: '', score: 0 });
};

const removeOption = (qIndex, oIndex) => {
    form.questions[qIndex].options.splice(oIndex, 1);
};

// Validar puntajes únicos y rango
const validateScore = (qIndex, oIndex) => {
    const question = form.questions[qIndex];
    const currentScore = question.options[oIndex].score;
    
    if (currentScore < 1 || currentScore > 5) {
        alert('El puntaje debe estar entre 1 y 5.');
        // Optional: Reset to nearest valid value?
        return;
    }

    const duplicate = question.options.some((opt, idx) => idx !== oIndex && opt.score === currentScore);
    if (duplicate) {
        alert('El puntaje debe ser único para esta pregunta.');
    }
};

const submit = () => {
    // Validate all unique scores before submit
    for (const q of form.questions) {
        const scores = q.options.map(o => o.score);
        const uniqueScores = new Set(scores);
        if (scores.length !== uniqueScores.size) {
            alert('Existen puntajes duplicados en una de las preguntas. Por favor corrígelos.');
            return;
        }
    }
    form.post(route('catalogo.rubrics.store'));
};
</script>

<template>
    <LayoutAuthenticated>
        <Head :title="title" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-shrink-0" style="fill: #1B396A;">
                            <path :d="mdiClipboardTextOutline"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Catálogo</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                         <Link :href="route('catalogo.rubrics.index')" class="text-gray-700 font-medium hover:text-[#1B396A] transition">Rúbricas</Link>
                         <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px" fill="#9CA3AF">
                            <path d="m321-80-71-71 329-329-329-329 71-71 400 400L321-80Z"/>
                        </svg>
                        <span class="text-gray-900 font-semibold">Crear</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Título -->
                    <div>
                        <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Título de la Rúbrica: <span class="text-red-500">*</span></label>
                        <input v-model="form.title" type="text" class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Escribe el titulo..." required>
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <!-- Preguntas -->
                    <div v-for="(question, qIndex) in form.questions" :key="qIndex" class="border p-4 rounded-lg bg-gray-50 relative">
                        <div class="mb-4">
                             <label class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">Pregunta {{ qIndex + 1 }}: <span class="text-red-500">*</span></label>
                             <div class="flex gap-2">
                                 <input v-model="question.text" type="text" class="bg-white border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Escribe la pregunta..." required>
                                 <button type="button" @click="removeQuestion(qIndex)" class="p-2 text-red-600 hover:bg-red-50 rounded-full border border-red-200 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                                 </button>
                             </div>
                             <p v-if="form.errors[`questions.${qIndex}.text`]" class="mt-1 text-sm text-red-600">{{ form.errors[`questions.${qIndex}.text`] }}</p>
                        </div>

                        <div class="flex justify-start mb-4">
                            <button type="button" @click="addOption(qIndex)" class="px-3 py-1.5 bg-[#1B396A] text-white rounded text-xs hover:bg-[#0f2347] transition flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="currentColor"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                                Agregar Respuesta
                            </button>
                        </div>

                        <div class="space-y-3 pl-4 border-l-2 border-gray-200">
                            <div v-for="(option, oIndex) in question.options" :key="oIndex" class="flex gap-4 items-start">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Respuesta {{ oIndex + 1 }}:</label>
                                    <input v-model="option.text" type="text" class="bg-white border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" placeholder="Escribe la respuesta..." required>
                                    <p v-if="form.errors[`questions.${qIndex}.options.${oIndex}.text`]" class="text-red-600 text-xs mt-1">{{ form.errors[`questions.${qIndex}.options.${oIndex}.text`] }}</p>
                                </div>
                                <div class="w-24">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Puntaje (1-5):</label>
                                    <input v-model="option.score" type="number" min="1" max="5" class="bg-white border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]" required @input="validateScore(qIndex, oIndex)">
                                    <p v-if="form.errors[`questions.${qIndex}.options.${oIndex}.score`]" class="text-red-600 text-xs mt-1">{{ form.errors[`questions.${qIndex}.options.${oIndex}.score`] }}</p>
                                </div>
                                <button type="button" @click="removeOption(qIndex, oIndex)" class="mt-8 p-1.5 text-red-600 hover:bg-red-50 rounded-full border border-red-200 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center mt-4 border-t pt-4 border-dashed border-gray-300">
                         <button type="button" @click="addQuestion" class="px-6 py-2 bg-white text-[#1B396A] border-2 border-[#1B396A] rounded-lg hover:bg-[#1B396A] hover:text-white transition flex items-center gap-2 text-sm font-bold w-full justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                            Agregar Nueva Pregunta
                        </button>
                    </div>

                    <!-- Footer Buttons -->
                    <div v-if="form.errors.is_active" class="col-span-12 text-red-600 text-sm mb-4 text-right">
                        {{ form.errors.is_active }}
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link :href="route('catalogo.rubrics.index')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#1B396A] text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl disabled:opacity-75 flex items-center gap-2 font-medium">
                            <span v-if="!form.processing">Guardar</span>
                            <span v-else>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </LayoutAuthenticated>
</template>
