<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import DynamicLayout from '@/layouts/DynamicLayout.vue';
import EyeOffIcon from '@/components/icons/EyeIcon.vue';
import EyeIcon from '@/components/icons/EyeOffIcon.vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false
    },
    status: {
        type: String,
        default: null
    },
    user: {
        type: Object,
        required: true
    }
});

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const showCurrentPassword = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
    });
};

const updatePassword = () => {
    passwordForm.put(route('user-password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

<template>
    <DynamicLayout>
        <Head title="Perfil" />

        <div class="space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="border-b border-gray-200 pb-4 mb-6">
                    <h2 class="text-2xl font-semibold text-[#1B396A]">Información del Perfil</h2>
                    <p class="text-sm text-gray-600 mt-1">Actualiza tu información personal</p>
                </div>

                <form @submit.prevent="updateProfile" class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block mb-2 text-base text-[#1B396A] font-medium">
                            Nombre Completo
                        </label>
                        <input
                            id="name"
                            v-model="profileForm.name"
                            type="text"
                            required
                            class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                            placeholder="Tu nombre completo"
                        />
                        <div v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">
                            {{ profileForm.errors.name }}
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block mb-2 text-base text-[#1B396A] font-medium">
                            Correo Electrónico
                        </label>
                        <input
                            id="email"
                            v-model="profileForm.email"
                            type="email"
                            required
                            class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                            placeholder="tu@email.com"
                        />
                        <div v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">
                            {{ profileForm.errors.email }}
                        </div>
                    </div>

                    <!-- Email Verification -->
                    <div v-if="mustVerifyEmail && !user.email_verified_at" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800">
                            Tu correo electrónico no está verificado.
                        </p>
                    </div>

                    <!-- Success Message -->
                    <div v-if="profileForm.recentlySuccessful" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm">
                        Perfil actualizado correctamente.
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="bg-[#002B5C] text-white py-2.5 px-6 rounded font-medium hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-800 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="!profileForm.processing">Guardar Cambios</span>
                            <span v-else class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="border-b border-gray-200 pb-4 mb-6">
                    <h2 class="text-2xl font-semibold text-[#1B396A]">Actualizar Contraseña</h2>
                    <p class="text-sm text-gray-600 mt-1">Asegúrate de usar una contraseña larga y segura</p>
                </div>

                <form @submit.prevent="updatePassword" class="space-y-5">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block mb-2 text-base text-[#1B396A] font-medium">
                            Contraseña Actual
                        </label>
                        <div class="relative">
                            <input
                                id="current_password"
                                v-model="passwordForm.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                class="bg-[#F3F4F6] border-t-0 border-x-0 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-3 p-2.5 pr-10 border-b-2 border-b-gray-300 focus:border-b-[#1B396A]"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition"
                            >
                                <EyeIcon v-if="!showCurrentPassword" size="20" class="text-gray-600" />
                                <EyeOffIcon v-else size="20" class="text-gray-600" />
                            </button>
                        </div>
                        <div v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                            {{ passwordForm.errors.current_password }}
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block mb-2 text-base text-[#1B396A] font-medium">
                            Nueva Contraseña
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="passwordForm.password"
                                :type="showPassword ? 'text' : 'password'"
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
                        <div v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                            {{ passwordForm.errors.password }}
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block mb-2 text-base text-[#1B396A] font-medium">
                            Confirmar Nueva Contraseña
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                v-model="passwordForm.password_confirmation"
                                :type="showPasswordConfirmation ? 'text' : 'password'"
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
                        <div v-if="passwordForm.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                            {{ passwordForm.errors.password_confirmation }}
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div v-if="passwordForm.recentlySuccessful" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm">
                        Contraseña actualizada correctamente.
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="bg-[#002B5C] text-white py-2.5 px-6 rounded font-medium hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-800 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="!passwordForm.processing">Actualizar Contraseña</span>
                            <span v-else class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Actualizando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DynamicLayout>
</template>
