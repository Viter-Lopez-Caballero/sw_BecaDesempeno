<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiBallotOutline, mdiInformation, mdiPlus, mdiPencil, mdiTrashCan, mdiContentSave, mdiClose } from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { defineProps, ref, provide } from 'vue'; // Importar ref
import { Link, Head, router } from "@inertiajs/vue3";
import JetInput from "@/Components/Input.vue";
import JetInputError from "@/Components/InputError.vue";
import JetButton from "@/Components/Button.vue";
import { useForm } from "@inertiajs/vue3";
import DataForm from "./DataForm.vue";

const props = defineProps({
    name: 'Create',
    titulo: { type: String, required: true },
    routeName: { type: String, required: true },
    modulos: { type: Object, required: true },
    permisos: { type: Object, required: true },
    profiles: { type: Object, required: true },
});

const saveStatus = ref(0); // Definir saveStatus

const form = useForm({ curp:'', name: '', email: '', password: '', profiles: [] });

const guardar = () => {
    form.transform(data => ({
        ...data,
        profiles: data.profiles.map(p => p.id)
    }))
    .post(route('usuarios.store'), {
        onError: () => saveStatus.value = 3,
    });
};

provide('form', form);
provide('profiles', props.profiles);

</script>

<template>
    <Head :title="titulo">
        <link rel="shortcut icon" type="image/png" href="/img/TecnmBlanco.png">
    </Head>
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiPlus" :title="titulo" main>
            <a :href="route(`${routeName}index`)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x"
                    viewBox="0 0 16 16">
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </a>
        </SectionTitleLineWithButton>
        
        <CardBox form @submit.prevent="guardar">
            <DataForm />
    
            <template #footer>
                <BaseButtons>
                    <BaseButton :href="route(`${routeName}index`)" :icon="mdiClose" color="danger" label="Cancelar" />
                    <BaseButton @click="guardar" :icon="mdiContentSave" type="submit" color="info" label="Guardar"/>
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
