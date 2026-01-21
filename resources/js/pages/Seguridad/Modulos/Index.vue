<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiBallotOutline, mdiInformation, mdiPlus, mdiPencil, mdiTrashCan, mdiContentSave, mdiChartBox, mdiBroom, mdiRefresh } from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import { defineProps } from 'vue';
import { Link, Head, router } from "@inertiajs/vue3";
import JetInput from "@/Components/Input.vue";
import Pagination from "@/Shared/Pagination.vue";
import RecordsHelper from "@/Shared/RecordsHelper.vue";
import { computed, onMounted, reactive, toRefs, ref, watch } from "vue";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';

const props = defineProps({
    name: 'Index',
    titulo: {
        type: String,
        required: true
    },
    modulos: {
      type: Object,
      required: true
    },
    isDarkMode: {
      type: Boolean,
      default: false
    },
    routeName: {
        type: String,
        required: true
    },
    loadingResults: { type: Boolean, required: true, default: true },
    search: { type: String, required: true },
    status: { type: Boolean, required: true, default: true },
});

const isLoading = ref(false)
const state = reactive({
    filters: {
        page: ref(props.modulos.current_page),
        search: ref(props.search),
        status: ref(props.status ?? 1),
    },
});
const search = () => {
    
    isLoading.value = true
    router.get(route(`${props.routeName}index`, state.filters));
};
const cleanFilters = () => {
    isLoading.value = true
    router.get(route(`${props.routeName}index`));
}

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
        <form class="w-full mb-5">
            <div class="flex flex-col md:flex-row">
                <select
                    @change="search"
                    class="md:h-11 max-xl:mb-4 bg-gray-50 border rounded-l-lg rounded-r-lg md:rounded-r-none border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    v-model="state.filters.status"
                >
                    <option :value="true">Activos</option>
                    <option :value="false">Eliminados</option>
                </select>

                <div class="relative w-full md:w-4/5 mr-1">
                    <input
                        type="search"
                        id="search-dropdown"
                        class="block p-2.5 md:h-11 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-l-lg md:rounded-l-none rounded-r-lg md:border-l-gray-50 border-l-gray-300 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-l-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                        placeholder="Ingresa un parametro de busqueda"
                        v-model="state.filters.search"
                        @change="search"
                    />
                    <button
                        type="submit"
                        class="absolute top-0 right-0 p-2.5 text-sm font-medium h-full md:h-11 xl:h-full text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click.prevent="search"
                    >
                        <svg
                            class="w-4 h-4"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 20"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                            />
                        </svg>
                        <span class="sr-only">Search</span>
                    </button>
                </div>
                
                <BaseButton
                    class="md:w-1/5 md:mt-0 md:h-11 max-xl:mt-4 mr-1"
                    @click="cleanFilters"
                    :icon="mdiBroom"
                    color="danger"
                    label="Limpiar"
                />
            </div>
        </form>

        <CardBox v-if="modulos.data.length > 0" class="overflow-x-auto">
<table class="min-w-full divide-y">
    <thead class="hidden md:table-header-group">
        <tr class="flex flex-col md:table-row">
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}"></th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">Módulo</th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">Descripción</th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">Clave</th>
            <th class="px-6 py-3 text-left text-xs font-medium" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode, 'uppercase tracking-wider': true}">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="item in modulos.data" :key="item.id" class="flex flex-col md:table-row">
            <td class="px-6 py-4 text-sm" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book-half" viewBox="0 0 16 16">
                    <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                </svg>
            </td>
            <td class="px-6 py-4 text-sm md:table-cell" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Modulo">{{ item.nombre }}</td>
            <td class="px-6 py-4 text-sm md:table-cell" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Descripcion">{{ item.descripcion }}</td>
            <td class="px-6 py-4 text-sm md:table-cell" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Clave">{{ item.key }}</td>
            <td class="px-6 py-4 text-sm" :class="{'text-white': isDarkMode, 'text-gray-900': isDarkMode}" data-label="Acciones">
                <BaseButtons>
                    <BaseButton v-if="status == true" color="success" :icon="mdiPencil" small :href="route(`${routeName}edit`, item.id)" />
                    <BaseButton v-if="status == false" color="success" :icon="mdiRefresh" small :href="route('modulo.recover', item.id)" label="Recuperar" />
                </BaseButtons>
            </td>
        </tr>
    </tbody>
</table>
        </CardBox>
        <CardBoxComponentEmpty v-else />
        <Pagination :links="modulos.links" :total="modulos.total" />
        <div class="vl-parent">
            <Loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
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
