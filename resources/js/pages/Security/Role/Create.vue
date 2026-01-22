<template>
    <HeadLogo :title="title" />
    <LayoutAuthenticated>
        <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>

        <DataForm :permissions="permissions" :modules="modules" :form="form">
            <template #actions>
                <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="lightDark" label="Cancelar" />
                <BaseButton @click="saveForm" :icon="mdiCheck" color="success" label="Guardar"
                    :processing="form.processing" />
            </template>
        </DataForm>
    </LayoutAuthenticated>
</template>
<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPlus, mdiClose, mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm } from "@inertiajs/vue3";
import DataForm from "./DataForm.vue";
import HeadLogo from "@/Components/HeadLogo.vue";

const props = defineProps({
    name: 'Create',
    title: {
        type: String,
        required: true
    },
    modules: {
        type: Object,
        required: true
    },
    permissions: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
});
const form = useForm({
    name: null,
    description: null,
    guard_name: 'web',
    permissions: []
});
const saveForm = () => {
    form.post(route(`${props.routeName}store`));
}
</script>
