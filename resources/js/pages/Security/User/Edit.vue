<template>
    <HeadLogo :title="title" />
    <LayoutAuthenticated>
        <SectionTitleLineWithButton :icon="mdiPencil" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>

        <DataForm :form="form" :roles="roles" is-edit>
            <template #actions>
                <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="lightDark" label="Cancelar" />
                <BaseButton @click="saveForm" :icon="mdiCheck" color="success" label="Guardar" />
                <BaseButton color="danger" :icon="mdiTrashCan" @click="deleteForm" label="Eliminar" />
            </template>
        </DataForm>
    </LayoutAuthenticated>
</template>
<script setup>
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPencil, mdiTrashCan, mdiClose, mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import { useForm } from "@inertiajs/vue3";
import DataForm from "./DataForm.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import { messageConfirm } from "@/Hooks/useErrorsForm";

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
    roles: {
        type: Object,
        default: () => ({}),
    },
    user: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    id: props.user.data.id,
    name: props.user.data.name,
    email: props.user.data.email,
    password: null,
    roles: props.user.data.roles.map(
        (role) => role.id
    ),
});

const saveForm = () => {
    form.put(route(`${props.routeName}update`, props.user.data.id));
};

const deleteForm = () => {
    messageConfirm().then((res) => {
        if (res.isConfirmed) {
            form.delete(route(`${props.routeName}destroy`, props.user.data.id));
        }
    });
};
</script>
