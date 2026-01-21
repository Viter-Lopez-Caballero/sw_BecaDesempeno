<script>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiBallotOutline, mdiAccount, mdiMail, mdiInformation, mdiPencil, mdiBroom } from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import { defineProps } from 'vue';
import { Link, Head, useForm } from "@inertiajs/vue3";
import JetButton from "@/Components/Button.vue";
import JetInput from "@/Components/Input.vue";

import Pagination from "@/Shared/Pagination.vue";
import RecordsHelper from "@/Shared/RecordsHelper.vue";
import { computed, onMounted, reactive, ref, watch } from "vue";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';

export default {
    name: "Index",
    props: {
        titulo: { type: String, required: true },
        usuarios: {
            type: Object,
            default: {},
            required: true,
        },
        profiles: {
            type: Object,
            default: {},
            required: true,
        },
         usuarios: {
      type: Object,
      required: true
    },
    isDarkMode: {
      type: Boolean,
      default: false  // Puedes cambiar esto dependiendo de cómo gestionas el modo oscuro
    },
        filtro: {
            type: Object,
            default: {},
            required: true,
        },
        routeName: { type: String, required: true },
        loadingResults: { type: Boolean, required: true, default: true }
    },

    components: {
        LayoutMain, Loading, CardBox, SectionTitleLineWithButton, NotificationBar, BaseButton, CardBoxComponentEmpty,
        Link,
        Pagination,
        RecordsHelper,
        JetInput,
        Head,
        JetButton,
    },
    setup(props) {
        const isLoading = ref(false);
        const form = useForm({ ...props.filtro });
        const thereAreResults = computed(() => props.usuarios.total > 0);
        const filtrar = () => {
            isLoading.value = true;
            form.get(route('usuarios.index'));
        };

        const search = () => {
            isLoading.value = true;
            
            router.get(route(`${props.routeName}index`, state.filters));
        };

        return {
            isLoading,
            mdiBroom,
            mdiBallotOutline,
            mdiPencil,
            mdiInformation,
            form,
            search,
            thereAreResults,
            thereAreFilter: () => computed(() => form.profile).value,
            filtrar,
            limpiarFiltro: () => {
                isLoading.value = true;
                form.profile = null;
                form.search = null;
                filtrar();
            },
        };
    },
};
</script>

<template>
    <Head :title="titulo">
        <link rel="shortcut icon" type="image/png" href="/img/TecnmBlanco.png">
    </Head>
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="titulo" main>
            <BaseButton :href="route(`${routeName}create`)" color="info" label="+ Agregar" />
        </SectionTitleLineWithButton>

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.error }}
        </NotificationBar>
        <div class="mb-5 md:flex md:justify-between">
            <div class="w-full md:w-3/4">
                <form @submit.prevent="filtrar" class="flex flex-col md:flex-row items-center">
                    <select
                        class="w-full md:w-1/3 text-base text-gray-900 border border-gray-300 rounded-l-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        v-model="form.profile" @change="filtrar">
                        <option value="null">Seleccione una opción</option>
                        <option v-for="profile in profiles" :value="profile.id" :key="profile.id" v-text="profile.name">
                        </option>
                    </select>
                    <div class="relative w-full md:w-2/3">
                        <input type="search" id="search-dropdown"
                            class="p-3 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-r-lg border-l-gray-50 border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-l-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                            placeholder="Búsqueda por nombre o correo electrónico" v-model="form.search" />
                        <button type="submit"
                            class="absolute top-0 right-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            :class="{ 'hidden': !thereAreFilter() }">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                            <span class="sr-only">Buscar</span>
                        </button>
                    </div>
                </form>
            </div>
            <BaseButton @click="limpiarFiltro()" color="danger" label="Limpiar" :icon="mdiBroom" class="mt-2 md:mt-0" />
        </div>
        <CardBox v-if="usuarios.data.length > 0">
            <table class="min-w-full divide-y">
    <thead>
        <tr class="flex flex-col md:table-row">
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}"></th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">CURP</th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">Rol</th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="item in usuarios.data" :key="item.id" class="flex flex-col md:table-row">
            <td class="px-6 py-4 text-sm" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book-half" viewBox="0 0 16 16">
                    <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                </svg>
            </td>
            <td class="px-6 py-4 text-sm md:table-cell" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Curp">{{ item.curp }}</td>
            <td class="px-6 py-4 text-sm md:table-cell" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Nombre">{{ item.name }}</td>
            <td class="px-6 py-4 text-sm md:table-cell" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Correo">{{ item.email }}</td>
            <td class="px-6 py-4 text-sm md:table-cell" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Rol">{{ item.roles.map(r => r.name).join(', ') }}</td>
            <td class="px-6 py-4 text-sm" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Acciones">
                <BaseButton color="success" :icon="mdiPencil" small :href="route(`${routeName}edit`, item.id)" />
            </td>
        </tr>
    </tbody>
</table>

        </CardBox>
        <CardBoxComponentEmpty v-else />
        <pagination :links="usuarios.links" :total="usuarios.total" />
        <div class="vl-parent">
            <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
        </div>
    </LayoutMain>
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
