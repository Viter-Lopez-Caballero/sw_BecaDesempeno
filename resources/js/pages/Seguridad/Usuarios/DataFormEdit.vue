<script>
import SectionMain from "@/Components/SectionMain.vue";
import CardBox from "@/Components/CardBox.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiBallotOutline, mdiPencil } from "@mdi/js";
import SectionTitle from "@/Components/SectionTitle.vue";

import { defineProps } from 'vue';
import { Link } from "@inertiajs/vue3";
import JetLabel from "@/Components/Label.vue";
import JetInput from "@/Components/Input.vue";
import JetButton from "@/Components/Button.vue";
import Button from "@/Components/PrimaryButton.vue";

import JetInputError from "@/Components/InputError.vue";
import Pagination from "@/Shared/Pagination.vue";
import JetDangerButton from "@/Components/DangerButton.vue";
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { useForm } from "@inertiajs/vue3";

import { computed, inject, reactive, ref, toRefs } from "vue";
import axios from "axios";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';

export default {
    name: "DataFormEdit",
    components: {
        SectionMain, LayoutAuthenticated, SectionTitleLineWithButton,
        Pagination,
        JetLabel,
        JetInput,
        JetInputError,
        JetButton,
        JetDangerButton,
        Button,
        Loading,
    },
    setup() {
        const form = inject("form");
        const profiles = inject("profiles");
        const isLoading = ref(false);

        const isChecked = (permission) =>
            form.permissions.some((permiso) => permission.id === permiso.id);

        // Associate profile and select all permissions
        const associateProfile = (profile) => {
            if (form.profiles.some((item) => profile.id === item.id)) {
                profile.permissions.forEach((item) =>
                    form.permissions.push({ id: item.id, name: item.name })
                );
            } else {
                profile.permissions.forEach((item) => {
                    const index = form.permissions.findIndex(
                        (formItem) => formItem.id === item.id
                    );
                    if (index > -1) {
                        form.permissions.splice(index, 1);
                    }
                });
            }
        };

        const isCheckedProfile = (profile) =>
            form.profiles.some((item) => profile.id === item.id);

        const getData = () => {
            isLoading.value = true;
            axios.get(route('usuarios.show', form.curp))
                .then((response) => {
                    isLoading.value = false;
                    const data = response.data[0][0];
                    form.name = data.nombres +' '+ data.apellidoPaterno +' '+ data.apellidoMaterno;
                }).catch(function (error) {
                    form.name = '';
                    if (error.response) {
                        if (error.response.status == 500) {
                            isLoading.value = false;
                            Swal.fire({
                                title: "Curp Incorrecta!",
                                text: "La curp ingresada no es valida, intente nuevamente",
                                icon: "warning",
                                confirmButtonColor: "#3085d6",
                                confirmButtonText: "Ok!",
                            });
                        }
                    }
                })
        };

        return {
            getData,
            isLoading,
            form,
            // actions
            associateProfile,
            isCheckedProfile,
            // injects
            profiles,
            isChecked,
        };
    },
};
</script>

<template>
    <div class="mb-5">
        <label for="curp" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            <span class="text-red-600 mr-1">*</span>CURP
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="search" id="curp"
                class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Ingresa tu CURP" v-model="form.curp" required>
            <button type="submit" @click="getData"
                class="hidden md:inline-block text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Buscar</button>
        </div>
        <jet-input-error :message="form.errors.curp" />
    </div>
    <div class="md:flex md:space-x-4 mb-5">
        <div class="md:w-1/2 mb-5">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <span class="text-red-600 mr-1">*</span>Nombre del usuario
            </label>
            <jet-input id="name"
                class="bg-gray-300 border border-gray-400 text-gray-900 text-base rounded-lg  block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                v-model="form.name" required placeholder="Nombre del usuario"
                autocomplete="off" readonly/>
            <jet-input-error :message="form.errors.name" />
        </div>
        <div class="md:w-1/2">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <span class="text-red-600 mr-1">*</span>Correo Electrónico
            </label>
            <jet-input id="email"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="form.email" required
                placeholder="Correo Electrónico" />
            <jet-input-error :message="form.errors.email" />
        </div>

    </div>

    <table class="min-w-full divide-y">
    <thead>
        <tr>
            <th></th>
            <th>Nombre de Rol</th>
            <th>Descripción</th>
            <th>Estatus</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="item in profiles" :key="item.id" class="flex flex-col md:table-row">
            <td class="px-6 py-4 md:table-cell">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-book-half" viewBox="0 0 16 16">
                    <path
                        d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z" />
                </svg>
            </td>
            <td class="px-6 py-4 md:table-cell" data-label="Nombre de Rol">{{ item.name }}</td>
            <td class="px-6 py-4 md:table-cell" data-label="Descripción">{{ item.description }}</td>
            <td class="px-6 py-4 md:table-cell" data-label="Estatus">
                <label class="relative inline-flex items-center cursor-pointer" :for="`chk${item.id}`">
                    <input type="checkbox" class="sr-only peer" v-model="form.profiles"
                        :value="{ id: item.id, name: item.name }" :id="`chk${item.id}`"
                        :checked="isCheckedProfile(item)" @change="associateProfile(item)">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                    </div>
                </label>
            </td>
        </tr>
    </tbody>
</table>
    <div class="vl-parent">
        <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
    </div>
</template>
<style>
    @media (max-width: 768px) {
        thead {
            display: none;
        }

        table tbody tr {
            display: block;
            border: 1px solid #ddd;
            margin-bottom: 1rem;
        }

        table tbody tr td {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr td:last-child {
            border-bottom: none;
        }

        table tbody tr td[data-label]:before {
            content: attr(data-label);
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 0.5rem;
        }
    }
</style>