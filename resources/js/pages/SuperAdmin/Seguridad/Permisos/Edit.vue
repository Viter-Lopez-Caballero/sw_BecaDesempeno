<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiBallotOutline, mdiInformation, mdiPlus, mdiPencil, mdiTrashCan, mdiContentSave, mdiClose  } from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";

import { defineProps } from 'vue';
import { Link, Head, router } from "@inertiajs/vue3";

import JetInput from "@/Components/Input.vue";
import JetInputError from "@/Components/InputError.vue";
import JetButton from "@/Components/Button.vue";
import { useForm } from "@inertiajs/vue3";

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const props = defineProps({
    name: 'Edit',
    titulo: {
        type: String,
        required: true
    },
    modulos: { type: Object, required: true },
    record: { type: Object, required: true },
    routeName: {
        type: String,
        required: true
    },
});

const form = useForm({ ...props.record });
const guardar = () => {
    form.put(route("permissions.update", props.record.id));
};

const eliminar = () => {
    Swal.fire({
        title: "¿Esta seguro?",
        text: "Esta acción no se puede revertir",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        cancelButtonColor: "#d33",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Si!, eliminar registro!",
    }).then((res) => {
        if (res.isConfirmed) {
            form.delete(route("permissions.destroy", props.record.id));
        }
    });
};


</script>

<template>
    <Head :title="titulo">
        <link rel="shortcut icon" type="image/png" href="/img/TecnmBlanco.png">
    </Head>
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiPencil" :title="titulo" main>
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
                <label for="module_key" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    <span class="text-red-600 mr-1">*</span>Nombre del módulo
                </label>

                <select id="module_key"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" v-model="form.module_key">
                    <option value=null>
                        Seleccione una opción
                    </option>
                    <option v-for="item in modulos" v-bind:value="item.key" v-bind:key="item.id">
                        {{ item.nombre }}
                    </option>
                </select>
                <jet-input-error :message="form.errors.module_key" />
            </div>
            <div class="mb-6">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    <span class="text-red-600 mr-1">*</span>Nombre (nombre_modulo.index, nombre_modulo.store,
                    nombre_modulo.update, nombre_modulo.delete):
                </label>
                <jet-input id="name"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    v-model="form.name" required placeholder="" />
                <jet-input-error :message="form.errors.name" />
            </div>
            <div class="">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    <span class="text-red-600 mr-1">*</span>Descripción(index=Leer Registros, store=Crear Registros,
                    update=Actualizar Registros, delete=Eliminar Registros):
                </label>
                <jet-input id="description"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    v-model="form.description" required placeholder="" />
                <jet-input-error :message="form.errors.description" />
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton :href="route(`${routeName}index`)" color="" label="Cancelar" />
                    <BaseButton @click="guardar" :icon="mdiContentSave" type="submit" color="info" label="Guardar"/>
                    <BaseButton color="danger" :icon="mdiTrashCan" @click="eliminar" label="Eliminar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
