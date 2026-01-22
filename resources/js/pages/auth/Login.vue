<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

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

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar Sesión" />

        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-8 space-y-6">
                <!-- Logos -->
                <div class="flex justify-center items-center space-x-6 mb-6">
                    <img src="/img/LogoTecNMCompleto.png" alt="TecNM" class="h-16 object-contain" />
                    <img src="/img/LogoCenidetCompleto.png" alt="Cenidet" class="h-16 object-contain" />
                </div>

                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-[#002B5C]">Inicio de Sesión</h2>
                    <p class="mt-2 text-sm text-gray-600">Ingresa tus credenciales para acceder</p>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ status }}
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Correo Electrónico
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            autocomplete="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:border-transparent transition duration-200"
                            placeholder="tu-correo@ejemplo.com"
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Contraseña
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:border-transparent transition duration-200"
                            placeholder="••••••••"
                        />
                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Remember & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 text-[#002B5C] focus:ring-[#002B5C] border-gray-300 rounded"
                            />
                            <span class="ml-2 text-sm text-gray-700">Recordarme</span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm font-medium text-[#002B5C] hover:text-blue-800 transition"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-[#002B5C] text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#002B5C] transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="!form.processing">Ingresar</span>
                        <span v-else class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Ingresando...
                        </span>
                    </button>

                    <!-- Register Link -->
                    <div v-if="canRegister" class="text-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            ¿No tienes cuenta?
                            <Link
                                :href="route('register')"
                                class="font-semibold text-[#002B5C] hover:text-blue-800 transition"
                            >
                                Regístrate
                            </Link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
