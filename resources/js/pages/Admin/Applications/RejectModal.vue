<template>
    <DialogModal :show="show" @close="close" maxWidth="lg">
        <template #title>
            <div class="flex items-center gap-2 text-red-600 font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Rechazar Solicitud
            </div>
        </template>

        <template #content>
            <p class="text-sm text-gray-700 mb-4 font-bold">
                Escribe una descripción del por qué rechazaste esta solicitud:
            </p>

            <div class="mb-4">
                <textarea 
                    v-model="form.comentario" 
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                    rows="6" 
                    placeholder="Escribe una descripción..."
                    maxlength="855"
                    required
                ></textarea>
                <div class="text-right text-xs text-gray-400 mt-1">
                    {{ form.comentario.length }}/855
                </div>
                <InputError :message="form.errors.comentario" class="mt-2" />
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="close">
                Cancelar
            </SecondaryButton>
            
            <PrimaryButton 
                class="ml-3 bg-red-600 hover:bg-red-700 focus:ring-red-500"
                :disabled="form.processing"
                @click="submit"
            >
                Enviar
            </PrimaryButton>
        </template>
    </DialogModal>
</template>

<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import { alertaCargando, cerrarAlerta, alertaExito, alertaError } from '@/utils/alerts.js';

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
        form.comentario = 'Estimado docente: Le informamos que tras revisar su expediente, su solicitud no cumple con los criterios y lineamientos establecidos en las bases vigentes de la presente convocatoria, por lo cual no ha sido aprobada. Agradecemos su participación.';
        form.clearErrors();
    }
});

const close = () => {
    emit('close');
};

const submit = () => {
    if (!props.applicationId) return;

    // Validación client-side
    form.clearErrors();
    if (!form.comentario || form.comentario.trim() === '') {
        form.errors.comentario = 'El motivo del rechazo es obligatorio';
        return;
    }

    alertaCargando('Rechazando', 'Por favor espera...');
    form.post(route('admin.applications.verdict', props.applicationId), {
        onSuccess: () => {
            cerrarAlerta();
            alertaExito('Rechazada', 'La solicitud fue rechazada correctamente.');
            close();
        },
        onError: () => {
            cerrarAlerta();
            alertaError('Error', 'No se pudo rechazar la solicitud. Por favor verifica los datos.');
        },
    });
};
</script>
