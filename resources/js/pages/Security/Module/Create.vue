<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiPlus, mdiClose, mdiCheck } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm } from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import HeadLogo from "@/Components/HeadLogo.vue";

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
});
const form = useForm({ name: null, description: null, key: null, });
const saveForm = () => {
    form.post(route(`${props.routeName}store`));
};
</script>

<template>
    <HeadLogo :title="title" />
    <LayoutAuthenticated>
        <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main :route-back="`${routeName}index`">
            <div class="md:block hidden">
                <BaseButton :icon="mdiClose" color="lightDark" :route-name="`${routeName}index`" />
            </div>
        </SectionTitleLineWithButton>

        <CardBox isForm>
            <div class="grid grid-cols-1 md:grid-cols-2 space-x-2 mb-6 md:mb-0">
                <FormField label="Nombre del módulo:" required help="Nombre del módulo" :error="form.errors.name">
                    <FormControl v-model="form.name" placeholder="Nombre del módulo" />
                </FormField>

                <FormField label="Clave del Módulo:" required :error="form.errors.key">
                    <FormControl v-model="form.key" placeholder="Clave del Módulo" />
                </FormField>
            </div>

            <FormField label="Descripción:" required help="" :error="form.errors.description">
                <FormControl v-model="form.description" placeholder="Descripción" type="textarea" height="h-24"
                    maxlength="255" />
            </FormField>
            <template #footer>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="lightDark" label="Cancelar" />
                    <BaseButton @click="saveForm" :icon="mdiCheck" color="success" label="Guardar"
                        :processing="form.processing" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutAuthenticated>
</template>
