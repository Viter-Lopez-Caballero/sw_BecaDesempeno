<script setup>
import { Head } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import { ref } from 'vue';

const contactForm = ref({
    name: '',
    email: '',
    message: ''
});

const submitContact = () => {
    // Placeholder for form submission
    console.log('Form submitted', contactForm.value);
    alert('Mensaje enviado (simulación)');
};
</script>

<template>
    <LandingLayout>
        <Head title="Contacto" />

        <!-- CONTACTO Section -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-16">
                     <h2 class="text-3xl font-bold text-[#002B5C] mb-4">Contacto</h2>
                     <p class="text-lg text-gray-600">¿Tienes dudas? Envíanos un mensaje y te responderemos a la brevedad.</p>
                </div>

                <div class="bg-blue-50 rounded-2xl p-8 md:p-12 shadow-sm border border-blue-100">
                    <form @submit.prevent="submitContact" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                                <input v-model="contactForm.name" type="text" id="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Juan Pérez">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                <input v-model="contactForm.email" type="email" id="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="juan@ejemplo.com">
                            </div>
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Comentarios o Dudas</label>
                            <textarea v-model="contactForm.message" id="message" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Escribe tu mensaje aquí..."></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="bg-[#002B5C] text-white font-bold py-3 px-10 rounded-lg hover:bg-blue-900 transition shadow-md hover:shadow-lg">
                                Enviar Mensaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>
