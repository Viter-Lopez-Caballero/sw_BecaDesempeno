<template>
    <Modal :show="show" @close="close" maxWidth="lg">
        <div class="p-6">
            <h2 class="text-xl font-bold text-red-600 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Rechazar Solicitud
            </h2>
            
            <p class="text-sm text-gray-600 mb-4">
                Escribe una descripción del por qué rechazaste esta solicitud:
            </p>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <textarea 
                        v-model="form.comentario" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500" 
                        rows="4" 
                        placeholder="Escribe una descripción..."
                        maxlength="255"
                        required
                    ></textarea>
                    <div class="text-right text-xs text-gray-400 mt-1">
                        {{ form.comentario.length }}/255
                    </div>
                    <InputError :message="form.errors.comentario" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button 
                        type="button" 
                        @click="close"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-[#1e3a5f] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#152d47] transition disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    applicationId: Number,
});

const emit = defineEmits(['close']);

const form = useForm({
    status: 'rejected',
    comentario: '',
});

watch(() => props.show, (val) => {
    if (val) {
        form.reset();
        form.clearErrors();
    }
});

const close = () => {
    emit('close');
};

const submit = () => {
    if (!props.applicationId) return;

    form.post(route('admin.applications.verdict', props.applicationId), {
        onSuccess: () => {
            close();
        },
    });
};
</script>
