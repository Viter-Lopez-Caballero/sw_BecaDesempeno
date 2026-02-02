<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: String,
    status: String,
});

const form = useForm({
    code: '',
});

const resendForm = useForm({
    email: props.email,
});

const codeInputs = ref([]);
const isResending = ref(false);

// Auto-focus en el primer input
const focusInput = (index) => {
    if (codeInputs.value[index]) {
        codeInputs.value[index].focus();
    }
};

// Manejar input en cada dígito
const handleInput = (index, event) => {
    const value = event.target.value;
    
    // Solo permitir números
    if (!/^\d$/.test(value) && value !== '') {
        event.target.value = '';
        return;
    }

    // Actualizar el código completo
    const digits = codeInputs.value.map(input => input.value).join('');
    form.code = digits;

    // Auto-focus al siguiente input
    if (value && index < 5) {
        focusInput(index + 1);
    }

    // Auto-submit cuando se completen los 6 dígitos
    if (digits.length === 6) {
        submit();
    }
};

// Manejar backspace
const handleKeydown = (index, event) => {
    if (event.key === 'Backspace' && !event.target.value && index > 0) {
        focusInput(index - 1);
    }
};

// Manejar paste
const handlePaste = (event) => {
    event.preventDefault();
    const pastedData = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
    
    pastedData.split('').forEach((digit, index) => {
        if (codeInputs.value[index]) {
            codeInputs.value[index].value = digit;
        }
    });

    form.code = pastedData;
    
    if (pastedData.length === 6) {
        submit();
    }
};

const submit = () => {
    form.post(route('verification.verify'), {
        preserveScroll: true,
        onError: () => {
            // Limpiar inputs en caso de error
            codeInputs.value.forEach(input => input.value = '');
            form.code = '';
            focusInput(0);
        },
    });
};

const resendCode = () => {
    isResending.value = true;
    resendForm.post(route('verification.resend'), {
        preserveScroll: true,
        onFinish: () => {
            isResending.value = false;
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
        <Head title="Verificar Correo Electrónico" />

        <div class="w-full max-w-md">
            <!-- Card Principal -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header con logos -->
                <div class="bg-white border-b border-gray-200 p-6">
                    <div class="flex justify-center items-center gap-6 mb-4">
                        <img src="/img/logo-sep.png" alt="SEP" class="h-16 w-auto">
                        <img src="/img/logo-cenidet.png" alt="CENIDET" class="h-16 w-auto">
                    </div>
                </div>

                <!-- Contenido -->
                <div class="p-8">
                    <!-- Icono de email -->
                    <div class="flex justify-center mb-6">
                        <div class="bg-blue-100 rounded-full p-4">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-2xl font-bold text-gray-900 text-center mb-2">
                        Confirma tu Correo Electrónico
                    </h1>

                    <p class="text-gray-600 text-center text-sm mb-8">
                        Hemos enviado un código de confirmación a tu dirección de email.<br>
                        Por favor revisa tu bandeja de entrada y haz clic en el enlace para activar tu cuenta.
                    </p>

                    <div v-if="email" class="bg-blue-50 rounded-lg p-3 mb-6 text-center">
                        <p class="text-sm text-gray-700">
                            Código enviado a:<br>
                            <span class="font-semibold text-blue-600">{{ email }}</span>
                        </p>
                    </div>

                    <!-- Inputs del código -->
                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 text-center mb-3">
                                Ingresa el código de 6 dígitos
                            </label>
                            
                            <div class="flex justify-center gap-2">
                                <input
                                    v-for="index in 6"
                                    :key="index"
                                    :ref="el => codeInputs[index - 1] = el"
                                    type="text"
                                    maxlength="1"
                                    inputmode="numeric"
                                    pattern="[0-9]"
                                    class="w-12 h-14 text-center text-2xl font-bold border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    :class="{ 'border-red-500': form.errors.code }"
                                    @input="handleInput(index - 1, $event)"
                                    @keydown="handleKeydown(index - 1, $event)"
                                    @paste="handlePaste"
                                />
                            </div>

                            <p v-if="form.errors.code" class="mt-2 text-sm text-red-600 text-center">
                                {{ form.errors.code }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing || form.code.length !== 6"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2"
                        >
                            <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verificar Código
                        </button>
                    </form>

                    <!-- Reenviar código -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 mb-2">
                            ¿No recibiste el código?
                        </p>
                        <button
                            @click="resendCode"
                            :disabled="resendForm.processing || isResending"
                            class="text-blue-600 hover:text-blue-800 font-medium text-sm disabled:text-gray-400 disabled:cursor-not-allowed"
                        >
                            {{ isResending ? 'Reenviando...' : 'Reenviar código' }}
                        </button>
                    </div>

                    <!-- Mensaje de éxito al reenviar -->
                    <div v-if="$page.props.flash?.success" class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3">
                        <p class="text-sm text-green-800 text-center">
                            {{ $page.props.flash.success }}
                        </p>
                    </div>

                    <!-- Advertencia de spam -->
                    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                        <p class="text-xs text-yellow-800">
                            Si no encontrarás el correo, revisa la carpeta de spam o correo no deseado.
                        </p>
                    </div>

                    <!-- Volver al login -->
                    <div class="mt-6 text-center">
                        <a
                            href="/login"
                            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver e Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
