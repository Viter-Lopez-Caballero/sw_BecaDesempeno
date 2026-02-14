<script setup>
import { Head } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import { ref, onMounted } from 'vue';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import axios from 'axios';
import { alertaExito, alertaError, alertaCargando, cerrarAlerta } from '@/utils/alerts.js';

const contactForm = ref({
    name: '',
    email: '',
    institucion_id: '',
    message: ''
});

const instituciones = ref([]);
const loading = ref(false);
const errors = ref({});

const clearError = (field) => {
    if (errors.value[field]) {
        delete errors.value[field];
    }
};

onMounted(async () => {
    try {
        const response = await axios.get('/api/institutions');
        instituciones.value = response.data;
    } catch (error) {
        console.error('Error al cargar instituciones:', error);
        alertaError('Error', 'No se pudieron cargar las instituciones');
    }
});

const submitContact = async () => {
    errors.value = {};
    
    // Validación básica
    if (!contactForm.value.name) {
        errors.value.name = 'El nombre es obligatorio';
        return;
    }
    if (!contactForm.value.email) {
        errors.value.email = 'El correo electrónico es obligatorio';
        return;
    }
    if (!contactForm.value.institucion_id) {
        errors.value.institucion_id = 'Debes seleccionar una institución';
        return;
    }
    if (!contactForm.value.message) {
        errors.value.message = 'El mensaje es obligatorio';
        return;
    }
    if (contactForm.value.message.length > 1000) {
        errors.value.message = 'El mensaje no puede exceder 1000 caracteres';
        return;
    }

    loading.value = true;
    alertaCargando('Enviando mensaje', 'Por favor espera...');

    try {
        const response = await axios.post('/api/contacto', contactForm.value);
        
        cerrarAlerta();
        alertaExito('¡Mensaje enviado!', response.data.message);
        
        // Limpiar formulario
        contactForm.value = {
            name: '',
            email: '',
            institucion_id: '',
            message: ''
        };
    } catch (error) {
        cerrarAlerta();
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {};
            alertaError('Error de validación', 'Por favor verifica los datos ingresados');
        } else {
            alertaError('Error', error.response?.data?.message || 'Error al enviar el mensaje');
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <LandingLayout>
        <Head title="Contacto" />

        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-[#1B396A] via-[#2B4A7E] to-[#3B5C92] py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-4xl md:text-4xl font-bold text-white mb-4">Contacto</h1>
                <p class="text-lg md:text-lg text-gray-100 max-w-3xl mx-auto">¿Tienes dudas sobre el programa? Estamos aquí para ayudarte.</p>
            </div>
        </section>

        <!-- CONTACTO Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                    <form @submit.prevent="submitContact" class="space-y-6">

                        <!-- Título -->
                        <div class="text-center">
                            <h2 class="text-2xl font-bold text-[#1B396A]">
                                Envíanos un mensaje
                            </h2>
                            <p class="text-sm text-gray-600 mt-2">Completa el formulario y nos pondremos en contacto contigo</p>
                        </div>

                        <!-- Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <label for="name" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    Nombre completo: <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="contactForm.name"
                                    type="text"
                                    id="name"
                                    class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                    :class="{ 'border-b-red-500': errors.name }"
                                    placeholder="Tu Nombre Completo"
                                    @input="clearError('name')"
                                />
                                <div v-if="!errors.name" class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, introduce tu nombre completo</span>
                                </div>
                                <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                                    {{ errors.name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    Correo electrónico: <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="contactForm.email"
                                    type="email"
                                    id="email"
                                    class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                    :class="{ 'border-b-red-500': errors.email }"
                                    @input="clearError('email')"
                                    placeholder="admin@ejemplo.com"
                                />
                                <div v-if="!errors.email" class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, introduce tu correo electrónico</span>
                                </div>
                                    <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                                        {{ errors.email }}
                                    </div>
                            </div>
                        </div>

                        <!-- Institución -->
                        <div>
                            <label for="institucion_id" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                Nombre de la Institución de Procedencia: <span class="text-red-500">*</span>
                            </label>
                            <VueSelect
                                v-model="contactForm.institucion_id"
                                :options="instituciones"
                                :reduce="option => option.id"
                                label="name"
                                placeholder="Buscar o seleccionar una institución..."
                                :searchable="true"
                                :clearable="true"
                                @input="clearError('institucion_id')"
                                :class="['vue-select-custom', { 'vue-select-error': errors.institucion_id }]"
                            >
                                <template #no-options="{ search, searching }">
                                    <template v-if="searching">
                                        No se encontraron resultados para <em>{{ search }}</em>.
                                    </template>
                                    <em v-else>Comienza a escribir para buscar...</em>
                                </template>
                            </VueSelect>
                            <div v-if="!errors.institucion_id" class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, selecciona tu institución</span>
                            </div>
                            <div v-if="errors.institucion_id" class="mt-1 text-sm text-red-600">
                                {{ errors.institucion_id }}
                            </div>
                        </div>

                        <!-- Mensaje -->
                        <div>
                            <label for="message" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                Comentarios o dudas: <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="contactForm.message"
                                id="message"
                                rows="5"
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                :class="{ 'border-b-red-500': errors.message }"
                                @input="clearError('message')"
                                placeholder="Escribe tu mensaje aquí..."
                                maxlength="1000"
                            ></textarea>
                            <div class="flex items-center justify-between mt-1">
                                <div v-if="!errors.message" class="flex items-center gap-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, escribe tu mensaje</span>
                                </div>
                                <div v-if="errors.message" class="text-sm text-red-600">
                                    {{ errors.message }}
                                </div>
                                <span class="text-gray-400 text-sm">{{ contactForm.message?.length || 0 }}/1000</span>
                            </div>
                        </div>

                        <!-- Botón -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                            <button
                                type="submit"
                                :disabled="loading"
                                class="px-6 py-2 bg-[#1B396A] cursor-pointer text-white rounded-lg hover:bg-[#0f2347] transition shadow-lg hover:shadow-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                                <span v-if="!loading">Enviar mensaje</span>
                                <span v-else>Enviando...</span>
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </section>
    </LandingLayout>
</template>

<style scoped>
.vue-select-custom :deep(.vs__dropdown-toggle) {
    background: linear-gradient(to bottom right, #F3F4F6, #E5E7EB);
    border: none;
    border-bottom: 2px solid #D1D5DB;
    border-radius: 0.5rem;
    padding: 0.625rem 0.75rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
}

.vue-select-custom :deep(.vs__dropdown-toggle):hover {
    border-bottom-color: rgba(27, 57, 106, 0.5);
}

.vue-select-custom :deep(.vs--open .vs__dropdown-toggle) {
    background: linear-gradient(to bottom right, #EFF6FF, #DBEAFE);
    border-bottom-color: #1B396A;
}

/* Error state - mayor especificidad */
.vue-select-error :deep(.vs__dropdown-toggle),
.vue-select-error :deep(.vs--open .vs__dropdown-toggle),
.vue-select-error :deep(.vs__dropdown-toggle):hover {
    border-bottom-color: #EF4444 !important;
}

.vue-select-custom :deep(.vs__search) {
    margin: 0;
    padding: 0;
    border: none;
    font-size: 0.875rem;
    color: #111827;
}

.vue-select-custom :deep(.vs__search::placeholder) {
    color: #9CA3AF;
}

.vue-select-custom :deep(.vs__selected) {
    margin: 0;
    padding: 0;
    border: none;
    color: #111827;
    font-size: 0.875rem;
}

.vue-select-custom :deep(.vs__actions) {
    padding: 0 4px 0 6px;
}

.vue-select-custom :deep(.vs__clear),
.vue-select-custom :deep(.vs__open-indicator) {
    fill: #1B396A;
    transition: transform 0.2s;
}

.vue-select-custom :deep(.vs__open-indicator) {
    transform: scale(0.70);
}

.vue-select-custom :deep(.vs--open .vs__open-indicator) {
    transform: rotate(180deg) scale(0.70);
}

.vue-select-custom :deep(.vs__dropdown-menu) {
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    margin-top: 4px;
}

.vue-select-custom :deep(.vs__dropdown-option) {
    padding: 0.625rem 0.75rem;
    color: #374151;
    font-size: 0.875rem;
    transition: all 0.15s;
}

.vue-select-custom :deep(.vs__dropdown-option--highlight) {
    background: #1B396A;
    color: white;
}

.vue-select-custom :deep(.vs__no-options) {
    padding: 0.75rem;
    color: #6B7280;
    font-size: 0.875rem;
    text-align: center;
}
</style>
