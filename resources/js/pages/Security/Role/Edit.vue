<template>
    <HeadLogo :title="title" />
    <LayoutAuthenticated>
        <SectionTitleLineWithButton :icon="mdiPencil" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>

        <DataForm :permissions="permissions" :modules="modules" :form="form">
            <template #actions>
                <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="lightDark" label="Cancelar" />
                <BaseButton @click="saveForm" :icon="mdiCheck" color="success" label="Guardar" />
                <BaseButton color="danger" :icon="mdiTrashCan" @click="deleteForm" label="Eliminar" />
            </template>
        </DataForm>
    </LayoutAuthenticated>
</template>
<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPencil, mdiTrashCan, mdiClose, mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm } from "@inertiajs/vue3";
import DataForm from "./DataForm.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import { messageConfirm } from "@/Hooks/useErrorsForm";

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    role: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Object,
        default: () => ({})
    },
    modules: {
        type: Object,
        required: true,
        default: () => ({})
    },
    routeName: {
        type: String,
        required: true
    },
});
const form = useForm({
    id: props.role.data.id,
    name: props.role.data.name,
    description: props.role.data.description,
    guard_name: props.role.data.guard_name,
    permissions: props.role.data.permissions.map(permission => permission.id),
});

const saveForm = () => form.put(route(`${props.routeName}update`, props.role.data.id))

const deleteForm = () => {
    messageConfirm().then((res) => {
        if (res.isConfirmed) {
            form.delete(route("roles.destroy", props.role.data.id));
        }
    });
};
</script>
