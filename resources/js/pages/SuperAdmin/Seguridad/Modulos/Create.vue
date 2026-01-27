<script setup>
import CardBox from '@/Components/CardBox.vue';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import { mdiBallotOutline, mdiInformation, mdiPlus, mdiPencil, mdiTrashCan, mdiContentSave, mdiClose  } from "@mdi/js";
import NotificationBar from '@/Components/NotificationBar.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';

import { defineProps } from 'vue';
import { Link, Head, router } from "@inertiajs/vue3";

import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetButton from '@/Components/Button.vue';
import { useForm } from "@inertiajs/vue3";



const props = defineProps({
    name: 'Create',
    titulo: {
        type: String,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
});
const form = useForm({ nombre: "", descripcion: "", key: "", });
const guardar = () => {
    form.post(route("modulo.store"));
};


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
            <div class="mb-6">
                <label for="nombre" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    <span class="text-red-600 mr-1">*</span>Nombre del módulo:
                </label>
                <jet-input id="nombre"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    v-model="form.nombre" required placeholder="Nombre del módulo" />
                <jet-input-error :message="form.errors.nombre" />
            </div>
            <div class="mb-6">
                <label for="descripcion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    <span class="text-red-600 mr-1">*</span>Descripción:
                </label>
                <jet-input id="descripcion"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    v-model="form.descripcion" required placeholder="Descripción" />
                <jet-input-error :message="form.errors.descripcion" />
            </div>
            <div class="">
                <label for="key" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    <span class="text-red-600 mr-1">*</span>Clave del Módulo:
                </label>
                <jet-input id="descripcion"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    v-model="form.key" required placeholder="Clave del Módulo" />
                <jet-input-error :message="form.errors.key" />
            </div>
            <!-- <jet-button @click="guardar"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                :class="{ 'text-white': form.processing }" :disabled="form.processing">
                <span class="animate-spin mr-1" v-show="form.processing">&#9696;</span> Guardar
            </jet-button> -->
            <template #footer>
                <BaseButtons>
                    <BaseButton :href="route(`${routeName}index`)" :icon="mdiClose" color="danger" label="Cancelar" />
                    <BaseButton @click="guardar" :icon="mdiContentSave" type="submit" color="info" label="Guardar"/>
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
