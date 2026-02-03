<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import LandingLayout from '@/layouts/LandingLayout.vue';
import EyeOffIcon from '@/components/icons/EyeIcon.vue';
import EyeIcon from '@/components/icons/EyeOffIcon.vue';
import { alertaExito, alertaError, alertaCargando, cerrarAlerta } from '@/utils/alerts.js';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: false
    },
    status: {
        type: String,
        default: null
    },
    canRegister: {
        type: Boolean,
        default: false
    }
});

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const showPassword = ref(false);

const submit = () => {
    alertaCargando('Iniciando sesión', 'Por favor espera...');
    
    form.post(route('login'), {
        onSuccess: () => {
            cerrarAlerta();
            alertaExito('¡Bienvenido!', 'Sesión iniciada correctamente');
        },
        onError: () => {
            cerrarAlerta();
            alertaError('Error de autenticación', 'Credenciales incorrectas. Por favor verifica tus datos');
        },
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <LandingLayout>
        <Head title="Iniciar Sesión" />

        <div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
            <div class="max-w-6xl w-full">
                <!-- Card contenedor unificado -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">
                    <!-- Login Form -->
                    <div class="p-10 space-y-7 flex flex-col justify-center">
                        <!-- Title -->
                        <div class="text-center">
                            <h2 class="text-3xl text-[#1B396A] font-semibold">Iniciar sesión</h2>
                        </div>

                        <!-- Status Message -->
                        <div v-if="status" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm">
                            {{ status }}
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit" class="space-y-5">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    Correo Electrónico:
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                    placeholder="admin@example.com"
                                />
                                <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, introduce tu correo electrónico</span>
                                </div>
                                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    Contraseña:
                                </label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        autocomplete="current-password"
                                        class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 pr-10 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                        placeholder="••••••••"
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition"
                                    >
                                        <EyeIcon v-if="!showPassword" size="20" class="text-gray-600" />
                                        <EyeOffIcon v-else size="20" class="text-gray-600" />
                                    </button>
                                </div>
                                <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, introduce tu contraseña</span>
                                </div>
                                <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <!-- Forgot Password Link -->
                            <div class="text-center">
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-sm text-[#1B396A] hover:text-[#0f2347] hover:underline transition"
                                >
                                    ¿Has olvidado tu contraseña?
                                </Link>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-[#002B5C] cursor-pointer text-white py-2.5 px-4 rounded font-medium hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-800 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="!form.processing">Ingresar</span>
                                <span v-else class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Ingresando...
                                </span>
                            </button>

                            <!-- Register Link -->
                            <!-- <div class="text-center pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600">
                                    ¿No tienes una cuenta?
                                    <Link
                                        :href="route('register')"
                                        class="font-medium text-[#1B396A] hover:text-[#0f2347] hover:underline transition"
                                    >
                                        Regístrate
                                    </Link>
                                </p>
                            </div> -->
                        </form>
                    </div>

                    <!-- Visual Section - Image -->
                    <div class="hidden lg:flex relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&h=1000&fit=crop&q=80" 
                            alt="Estudiantes del TecNM" 
                            class="absolute inset-0 w-full h-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1B396A]/90 via-[#1B396A]/40 to-transparent"></div>
                        <div class="relative z-10 flex flex-col justify-end p-10 text-white">
                            <div class="space-y-4">
                                <h3 class="text-3xl font-bold drop-shadow-lg">
                                    Sistema de Estímulos al Desempeño del Personal Docente
                                </h3>
                                <p class="text-lg text-blue-100 leading-relaxed drop-shadow-md">
                                    Tecnológico Nacional de México - Reconociendo la excelencia académica y el compromiso de nuestros docentes.
                                </p>
                                <div v-if="canRegister" class="rounded-xl p-6 mt-6">
                                    <div class="text-center space-y-4">
                                        <div>
                                            <p class="text-xl font-bold text-white mb-2">¿No tienes una cuenta?</p>
                                            <p class="text-sm text-blue-100">Únete a la comunidad docente del TecNM</p>
                                        </div>
                                        <Link
                                            :href="route('register')"
                                            class="inline-block bg-white text-[#1B396A] font-bold px-8 py-3 rounded-lg hover:bg-gray-100 transition shadow-lg transform hover:-translate-y-1 hover:shadow-xl"
                                        >
                                            Regístrate
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>
