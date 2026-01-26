<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import LandingLayout from '@/layouts/LandingLayout.vue';
import EyeIcon from '@/components/icons/EyeIcon.vue';
import EyeOffIcon from '@/components/icons/EyeOffIcon.vue';
import LupaIcon from '@/components/icons/LupaIcon.vue';

const form = useForm({
    curp: '',
    nombre_completo: '',
    email: '',
    password: '',
    password_confirmation: '',
    institucion_id: '',
    area_prioritaria_id: '',
    subarea_prioritaria_id: ''
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const buscarCurp = () => {
    // Lógica para buscar CURP
    console.log('Buscando CURP:', form.curp);
};
</script>

<template>
    <LandingLayout>
        <Head title="Crear Cuenta" />

        <div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl w-full">
                <!-- Register Card -->
                <div class="bg-white rounded-lg shadow-xl p-10 space-y-7">
                    <!-- Title -->
                    <div class="text-center">
                        <h2 class="text-3xl font-semibold text-[#1B396A]">Crear Cuenta</h2>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- CURP -->
                        <div>
                            <label for="curp" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                CURP
                            </label>
                            <div class="flex gap-2">
                                <input
                                    id="curp"
                                    v-model="form.curp"
                                    type="text"
                                    required
                                    autofocus
                                    maxlength="18"
                                    class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                    placeholder="Tu curp"
                                />
                                <button
                                    type="button"
                                    @click="buscarCurp"
                                    class="bg-[#1B396A] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#0f2347] transition whitespace-nowrap flex items-center gap-2"
                                >
                                    <LupaIcon size="16" />
                                    Buscar CURP
                                </button>
                            </div>
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, introduce tu CURP</span>
                            </div>
                            <div v-if="form.errors.curp" class="mt-1 text-sm text-red-600">
                                {{ form.errors.curp }}
                            </div>
                        </div>

                        <!-- Grid de 2 columnas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Nombre Completo -->
                            <div>
                                <label for="nombre_completo" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    Nombre Completo:
                                </label>
                                <input
                                    id="nombre_completo"
                                    v-model="form.nombre_completo"
                                    type="text"
                                    required
                                    class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                    placeholder="Tu nombre"
                                />
                                <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, introduce tu nombre completo</span>
                                </div>
                                <div v-if="form.errors.nombre_completo" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.nombre_completo }}
                                </div>
                            </div>

                            <!-- Correo Electrónico -->
                            <div>
                                <label for="email" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    Correo Electrónico:
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
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
                        </div>

                        <!-- Grid de 2 columnas - Contraseñas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Contraseña -->
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
                                        autocomplete="new-password"
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
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div>
                                <label for="password_confirmation" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    Confirmar Contraseña:
                                </label>
                                <div class="relative">
                                    <input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        :type="showPasswordConfirmation ? 'text' : 'password'"
                                        required
                                        autocomplete="new-password"
                                        class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 pr-10 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                        placeholder="••••••••"
                                    />
                                    <button
                                        type="button"
                                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition"
                                    >
                                        <EyeIcon v-if="!showPasswordConfirmation" size="20" class="text-gray-600" />
                                        <EyeOffIcon v-else size="20" class="text-gray-600" />
                                    </button>
                                </div>
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, confirma tu contraseña</span>
                            </div>
                            </div>
                        </div>

                        <!-- Nombre de la Institución de Procedencia -->
                        <div>
                            <label for="institucion_id" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                Nombre de la Institución de Procedencia:
                            </label>
                            <select
                                id="institucion_id"
                                v-model="form.institucion_id"
                                required
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                            >
                                <option value="" disabled selected>Selecciona una Opción</option>
                                <option value="1">Institución 1</option>
                                <option value="2">Institución 2</option>
                                <option value="3">Institución 3</option>
                            </select>
                            <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Por favor, selecciona tu institución de procedencia</span>
                            </div>
                            <div v-if="form.errors.institucion_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.institucion_id }}
                            </div>
                        </div>

                        <!-- Grid de 2 columnas - Áreas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Área Prioritaria -->
                            <div>
                                <label for="area_prioritaria_id" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    Área Prioritaria:
                                </label>
                                <select
                                    id="area_prioritaria_id"
                                    v-model="form.area_prioritaria_id"
                                    required
                                    class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                >
                                    <option value="" disabled selected>Selecciona una Opción</option>
                                    <option value="1">Área 1</option>
                                    <option value="2">Área 2</option>
                                    <option value="3">Área 3</option>
                                </select>
                                <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, selecciona el área prioritaria</span>
                                </div>
                                <div v-if="form.errors.area_prioritaria_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.area_prioritaria_id }}
                                </div>
                            </div>

                            <!-- SubÁrea Prioritaria -->
                            <div>
                                <label for="subarea_prioritaria_id" class="block mb-2 text-base text-[#1B396A] font-medium text-gray-900">
                                    SubÁrea Prioritaria:
                                </label>
                                <select
                                    id="subarea_prioritaria_id"
                                    v-model="form.subarea_prioritaria_id"
                                    required
                                    class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                >
                                    <option value="" disabled selected>Selecciona una Opción</option>
                                    <option value="1">SubÁrea 1</option>
                                    <option value="2">SubÁrea 2</option>
                                    <option value="3">SubÁrea 3</option>
                                </select>
                                <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Por favor, selecciona la subárea prioritaria</span>
                                </div>
                                <div v-if="form.errors.subarea_prioritaria_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.subarea_prioritaria_id }}
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-[#1B396A] text-white py-2.5 px-4 rounded font-medium hover:bg-[#0f2347] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1B396A] transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed mt-6"
                        >
                            <span v-if="!form.processing">Crear una cuenta</span>
                            <span v-else class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creando cuenta...
                            </span>
                        </button>

                        <!-- Login Link -->
                        <div class="text-center pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                ¿Ya una cuenta?
                                <Link
                                    :href="route('login')"
                                    class="font-medium text-[#1B396A] hover:text-[#0f2347] hover:underline transition"
                                >
                                    Inicia Sesión
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>
