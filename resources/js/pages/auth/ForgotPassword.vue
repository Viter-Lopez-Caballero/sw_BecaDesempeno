<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import LandingLayout from '@/layouts/LandingLayout.vue';
import { alertaExito, alertaError, alertaCargando, cerrarAlerta } from '@/utils/alerts.js';

defineProps({
    status: {
        type: String,
        default: null
    }
});

const form = useForm({
    email: '',
});

const clearError = (field) => {
    if (form.errors[field]) {
        delete form.errors[field];
    }
};

const submit = () => {
    // Limpiar errores previos
    form.clearErrors();
    
    // Validación del lado del cliente
    if (!form.email) {
        form.errors.email = 'El correo electrónico es obligatorio';
        return;
    }
    if (!form.email.includes('@')) {
        form.errors.email = 'El correo electrónico debe contener un @';
        return;
    }
    
    alertaCargando('Enviando enlace', 'Por favor espera...');
    
    form.post(route('password.email'), {
        onSuccess: () => {
            cerrarAlerta();
            alertaExito('¡Enlace enviado!', 'Revisa tu correo electrónico para restablecer tu contraseña');
        },
        onError: () => {
            cerrarAlerta();
            alertaError('Error', 'No se pudo enviar el enlace. Verifica tu correo electrónico');
        },
    });
};
</script>

<template>
    <LandingLayout>
        <Head title="Recuperar Contraseña" />

        <div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full">
                <!-- Card Principal -->
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Header con logo y color institucional -->
                    <div class="bg-gradient-to-r from-[#1B396A] to-[#0f2347] px-8 py-10 text-center">
                        <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center mb-4 shadow-lg">
                            <img 
                                src="/img/tecnm-logo.png" 
                                alt="TecNM Logo" 
                                class="w-12 h-12 object-contain"
                            />
                        </div>
                        <h2 class="text-2xl font-bold text-white">
                            ¿Olvidaste tu contraseña?
                        </h2>
                        <p class="mt-2 text-sm text-blue-100">
                            No te preocupes, te enviaremos instrucciones para restablecerla
                        </p>
                    </div>

                    <!-- Formulario -->
                    <div class="px-8 py-10">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Correo Electrónico
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="tu.correo@ejemplo.com"
                                    :class="[
                                        'w-full px-4 py-3 rounded-lg border transition duration-200',
                                        form.errors.email 
                                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
                                            : 'border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]'
                                    ]"
                                    @input="clearError('email')"
                                />
                                <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </p>
                                <p v-else class="mt-2 text-sm text-gray-500">
                                    Ingresa el correo electrónico asociado a tu cuenta
                                </p>
                            </div>

                            <!-- Botón Submit -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-gradient-to-r from-[#1B396A] to-[#0f2347] text-white py-3 px-4 rounded-lg font-semibold hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1B396A] transition duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="!form.processing">
                                    Enviar enlace de recuperación
                                </span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Enviando...
                                </span>
                            </button>
                        </form>

                        <!-- Link para volver -->
                        <div class="mt-6 text-center">
                            <Link 
                                :href="route('login')" 
                                class="text-[#1B396A] hover:text-[#0f2347] font-medium text-sm hover:underline transition"
                            >
                                ← Volver al inicio de sesión
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Footer informativo -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Si tienes problemas para recuperar tu contraseña,
                        <a href="/contacto" class="text-[#1B396A] hover:underline font-medium">
                            contáctanos
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>
