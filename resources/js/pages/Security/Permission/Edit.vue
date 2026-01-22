<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPencil, mdiTrashCan, mdiClose, mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm } from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import { messageConfirm } from "@/Hooks/useErrorsForm";

const props = defineProps({
    name: 'Edit',
    title: {
        type: String,
        required: true
    },
    modules: {
        type: Object,
        required: true
    },
    permission: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
});

const form = useForm({ ...props.permission });
const saveForm = () => {
    form.put(route(`${props.routeName}update`, props.permission.id));
};
const deleteForm = () => {
    messageConfirm().then((res) => {
        if (res.isConfirmed) {
            form.delete(route(`${props.routeName}destroy`, props.permission.id));
        }
    });
};

</script>

<template>
    <HeadLogo :title="title" />
    <LayoutAuthenticated>
        <SectionTitleLineWithButton :icon="mdiPencil" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>

        <CardBox isForm>
            <div class="grid grid-cols-1 md:grid-cols-2 space-x-2 mb-6 md:mb-0">
                <FormField label="Selecciona un módulo:" required help="Selecciona un módulo disponible"
                    :error="form.errors.module_key">
                    <FormControl v-model="form.module_key" :options="modules" valueSelect="key" />
                </FormField>
                <FormField label="Nombre del permiso:" help="Ejemplos: nombre_modulo.index, nombre_modulo.store,
                    nombre_modulo.update, nombre_modulo.delete" required :error="form.errors.name">
                    <FormControl v-model="form.name" placeholder="Nombre del permiso" />
                </FormField>
            </div>

            <FormField label="Descripción:" help="Ejemplos: index=Leer Registros, store=Crear Registros,
                    update=Actualizar Registros, delete=Eliminar Registros" required :error="form.errors.description">
                <FormControl v-model="form.description" placeholder="Descripción" type="textarea" height="h-24"
                    maxlength="255" />
            </FormField>

            <template #footer>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="lightDark" label="Cancelar" />
                    <BaseButton @click="saveForm" :icon="mdiCheck" color="success" label="Guardar" />
                    <BaseButton color="danger" :icon="mdiTrashCan" @click="deleteForm" label="Eliminar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutAuthenticated>
</template>
