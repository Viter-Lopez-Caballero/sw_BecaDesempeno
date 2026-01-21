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
                     <h2 class="text-3xl md:text-4xl font-bold text-[#000000] mb-4">Contacto</h2>
                     <p class="text-lg text-gray-500">¿Tienes dudas sobre el programa? Estamos aquí para ayudarte.</p>
                </div>

                <div class="bg-white rounded-3xl p-8 md:p-12 shadow-lg border border-blue-100 max-w-3xl mx-auto">
                    <form @submit.prevent="submitContact" class="space-y-8">

                        <!-- Título -->
                        <div class="text-center">
                            <h2 class="text-2xl md:text-2xl font-extrabold text-[#000000]">
                                Envíanos un mensaje
                            </h2>
                        </div>

                        <!-- Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="relative">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Nombre completo
                                </label>
                                <input
                                    v-model="contactForm.name"
                                    type="text"
                                    id="name"
                                    placeholder="Juan Pérez"
                                    class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                                        focus:border-[#1B396A] focus:ring-[#1B396A]
                                        transition"
                                >
                            </div>

                            <div class="relative">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Correo electrónico
                                </label>
                                <input
                                    v-model="contactForm.email"
                                    type="email"
                                    id="email"
                                    placeholder="juan@ejemplo.com"
                                    class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                                        focus:border-[#1B396A] focus:ring-[#1B396A]
                                        transition"
                                >
                            </div>
                        </div>

                        <!-- Textarea -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">
                                Comentarios o dudas
                            </label>
                            <textarea
                                v-model="contactForm.message"
                                id="message"
                                rows="5"
                                placeholder="Escribe tu mensaje aquí..."
                                class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                                    focus:border-[#1B396A] focus:ring-[#1B396A]
                                    transition resize-none"
                            ></textarea>
                        </div>

                        <!-- Botón -->
                        <div class="text-center">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center
                                    bg-[#1B396A] text-white font-bold
                                    px-12 py-3 rounded-xl
                                    hover:bg-blue-900 transition
                                    shadow-lg hover:shadow-xl
                                    transform hover:-translate-y-0.5"
                            >
                                Enviar mensaje
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </section>
    </LandingLayout>
</template>
