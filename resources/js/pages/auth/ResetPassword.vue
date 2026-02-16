<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import LandingLayout from '@/layouts/LandingLayout.vue';
import EyeOffIcon from '@/components/icons/EyeIcon.vue';
import EyeIcon from '@/components/icons/EyeOffIcon.vue';
import { alertaExito, alertaError, alertaCargando, cerrarAlerta } from '@/utils/alerts.js';

const props = defineProps({
    token: {
        type: String,
        required: true
    },
    email: {
        type: String,
        required: true
    }
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: ''
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const clearError = (field) => {
    if (form.errors[field]) {
        delete form.errors[field];
    }
};

const submit = () => {
    // Limpiar errores previos
    form.clearErrors();
    
    // Validación del lado del cliente
    if (!form.password) {
        form.errors.password = 'La contraseña es obligatoria';
        return;
    }
    if (form.password.length < 8) {
        form.errors.password = 'La contraseña debe tener al menos 8 caracteres';
        return;
    }
    if (!form.password_confirmation) {
        form.errors.password_confirmation = 'Debes confirmar tu contraseña';
        return;
    }
    if (form.password !== form.password_confirmation) {
        form.errors.password_confirmation = 'Las contraseñas no coinciden';
        return;
    }
    
    alertaCargando('Restableciendo contraseña', 'Por favor espera...');
    
    form.post(route('password.update'), {
        onSuccess: () => {
            cerrarAlerta();
            alertaExito('¡Contraseña actualizada!', 'Tu contraseña ha sido restablecida exitosamente');
        },
        onError: () => {
            cerrarAlerta();
            alertaError('Error', 'No se pudo restablecer la contraseña. El enlace puede haber expirado');
        },
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        }
    });
};
</script>

<template>
    <LandingLayout>
        <Head title="Restablecer Contraseña" />

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
                            Restablecer Contraseña
                        </h2>
                        <p class="mt-2 text-sm text-blue-100">
                            Ingresa tu nueva contraseña para continuar
                        </p>
                    </div>

                    <!-- Formulario -->
                    <div class="px-8 py-10">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Email (readonly) -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Correo Electrónico
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    readonly
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 text-gray-600 cursor-not-allowed"
                                />
                            </div>

                            <!-- Nueva Contraseña -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nueva Contraseña
                                </label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        autocomplete="new-password"
                                        placeholder="Mínimo 8 caracteres"
                                        :class="[
                                            'w-full px-4 py-3 pr-12 rounded-lg border transition duration-200',
                                            form.errors.password 
                                                ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
                                                : 'border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]'
                                        ]"
                                        @input="clearError('password')"
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-700 transition"
                                    >
                                        <EyeIcon v-if="!showPassword" size="20" class="text-gray-600" />
                                        <EyeOffIcon v-else size="20" class="text-gray-600" />
                                    </button>
                                </div>
                                <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </p>
                                <p v-else class="mt-2 text-sm text-gray-500">
                                    Debe tener al menos 8 caracteres
                                </p>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Confirmar Contraseña
                                </label>
                                <div class="relative">
                                    <input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        :type="showPasswordConfirmation ? 'text' : 'password'"
                                        autocomplete="new-password"
                                        placeholder="Repite tu contraseña"
                                        :class="[
                                            'w-full px-4 py-3 pr-12 rounded-lg border transition duration-200',
                                            form.errors.password_confirmation 
                                                ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
                                                : 'border-gray-300 focus:border-[#1B396A] focus:ring-[#1B396A]'
                                        ]"
                                        @input="clearError('password_confirmation')"
                                    />
                                    <button
                                        type="button"
                                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-700 transition"
                                    >
                                        <EyeIcon v-if="!showPasswordConfirmation" size="20" class="text-gray-600" />
                                        <EyeOffIcon v-else size="20" class="text-gray-600" />
                                    </button>
                                </div>
                                <p v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.password_confirmation }}
                                </p>
                                <p v-else class="mt-2 text-sm text-gray-500">
                                    Vuelve a escribir tu contraseña
                                </p>
                            </div>

                            <!-- Botón Submit -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-gradient-to-r from-[#1B396A] to-[#0f2347] text-white py-3 px-4 rounded-lg font-semibold hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1B396A] transition duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="!form.processing">
                                    Restablecer Contraseña
                                </span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Restableciendo...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Footer informativo -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        ¿Recordaste tu contraseña?
                        <a :href="route('login')" class="text-[#1B396A] hover:underline font-medium">
                            Iniciar sesión
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>
