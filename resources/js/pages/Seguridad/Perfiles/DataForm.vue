<script>
import SectionMain from "@/Components/SectionMain.vue";
import CardBox from "@/Components/CardBox.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiBallotOutline, mdiPencil, mdiCheckAll, mdiReplyAll } from "@mdi/js";
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

import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { computed, inject, reactive, ref, toRefs } from "vue";

export default {
    components: {
        SectionMain, LayoutAuthenticated, SectionTitleLineWithButton,
        Pagination,
        JetLabel,
        JetInput,
        JetInputError,
        JetButton,
        BaseButton,
        BaseButtons,
        JetDangerButton,
        Button,
    },
    setup() {
        const permisos = inject("permisos", {});
        const modules = inject("modulos", {});
        const form = inject("form");

        const isChecked = (permission) =>
            form.permisos.some((permiso) => permission.id === permiso.id);

        const state = reactive({
            modulesFilter: [...modules],
            permissionsFilter: { ...permisos },
        });

        // filters
        const active = ref("cat");
        const getActive = computed(() => active);
        const inputSearchPermission = ref("");
        const inputSearchModule = ref("");

        const searchModule = () => {
            if (inputSearchModule.value.trim() !== "") {
                state.modulesFilter = modules.filter((module) => {
                    const filter = module.nombre
                        .toLowerCase()
                        .includes(inputSearchModule.value.toLowerCase());
                    if (filter) return module;
                });
                if (state.modulesFilter.length > 0) {
                    active.value = state.modulesFilter[0].key;
                }
            } else {
                state.modulesFilter = modules;
            }
        };

        const changeModule = (key) => {
            active.value = key;
            inputSearchPermission.value = "";
            searchPermission();
        };

        const searchPermission = () => {
            const key = active.value;
            if (inputSearchPermission.value.trim() !== "") {
                state.permissionsFilter[key] = permisos[key].filter(
                    (item) =>
                        item.description
                            .toLowerCase()
                            .includes(inputSearchPermission.value.toLowerCase()) ||
                        item.name
                            .toLowerCase()
                            .includes(inputSearchPermission.value.toLowerCase())
                );
            } else {
                state.permissionsFilter[key] = permisos[key];
            }
        };

        const addAllPermissions = () => {
            const key = active.value;
            if (state.permissionsFilter[key]) {
                removeAllPermissions();
                state.permissionsFilter[key].forEach((permission) =>
                    form.permisos.push({ id: permission.id, name: permission.name })
                );
            }
        };

        const removeAllPermissions = () => {
            const key = active.value;
            if (state.permissionsFilter[key]) {
                state.permissionsFilter[key].forEach((permission) => {
                    const index = form.permisos.findIndex(
                        (item) => item.id === permission.id
                    );
                    if (index > -1) form.permisos.splice(index, 1);
                });
            }
        };

        return {
            mdiCheckAll, mdiReplyAll,
            form,
            isChecked,
            ...toRefs(state),
            inputSearchPermission,
            searchPermission,
            addAllPermissions,
            removeAllPermissions,
            //
            changeModule,
            modules,
            searchModule,
            inputSearchModule,
            //
            active,
            getActive,
        };
    },
};
</script>

<template>
    <div class="md:flex md:space-x-4 mb-5">
        <div class="md:w-1/2">
            <!-- Contenido de la primera columna de la primer "fila" -->
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <span class="text-red-600 mr-1">*</span>Nombre:
            </label>
            <jet-input id="name"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="form.name" required placeholder="Nombre de rol" />
            <jet-input-error :message="form.errors.name" />
        </div>

        <div class="md:w-1/2">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <span class="text-red-600 mr-1">*</span>Descripción:
            </label>
            <jet-input id="description"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="form.description" required
                placeholder="Descripción" />
            <jet-input-error :message="form.errors.description" />
        </div>
    </div>
    <div class="md:flex md:space-x-4">
        <div class="md:w-5/12 p-2 mb-5">
            <div class="justify-center flex">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                Módulos disponibles
            </div>
            <div class="mt-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" id="default-search"
                        class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        v-model="inputSearchModule" placeholder="Ingrese un parámetro de búsqueda" @change="searchModule">

                    <button type="submit"
                        class="hidden md:inline-block text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click.prevent="searchModule">
                        Buscar
                    </button>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex flex-col space-y-2">
                    <a v-for="(module, key) in modulesFilter" :key="key" :href="`#${module.key}`" :id="`pill-${module.key}`"
                        @click="changeModule(module.key)"
                        class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                        :class="{ 'active': module.key === active }" role="tab" aria-controls="module.key"
                        aria-selected="true" v-text="module.nombre">
                    </a>

                </div>
            </div>
        </div>
        <div class="md:w-7/12 p-2">
            <div class="justify-center flex">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                Permisos disponibles
            </div>
            <div class="mt-2">
                <div class="mb-5">
                    <BaseButtons type="justify-start" no-wrap>
                        <BaseButton color="info" :icon="mdiCheckAll" small label="TODOS" @click="addAllPermissions()"/>
                        <BaseButton color="danger" :icon="mdiReplyAll" small label="NINGUNO" @click="removeAllPermissions()"/>
                    </BaseButtons>

                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search"
                        class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        id="search-module" v-model="inputSearchPermission" placeholder="Buscar permisos"
                        @keyup="searchPermission">
                    <button type="submit"
                        class="hidden md:inline-block text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click.prevent="searchPermission">
                        Buscar</button>
                </div>
            </div>
            <div class="mt-5">
                <div class="ml-5" :id="key" v-for="(module, key) in permissionsFilter" :key="key">
                    <div class="" v-if="getActive.value === key" style="max-height: 25rem; overflow-y: auto">
                        <div class="" style="word-break: break-all" v-for="permiso in module" :key="permiso.id">
                            <div class="">
                                <label class="relative inline-flex items-center mb-5 cursor-pointer" :for="permiso.name">
                                    <input type="checkbox" class="sr-only peer" v-model="form.permisos"
                                        :value="{ id: permiso.id, name: permiso.name }" :id="permiso.name"
                                        :checked="isChecked(permiso)">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300 truncate">
                                        {{ permiso.name }} - {{ permiso.description }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.active {
    background-color: #1A56DB;
    color: white;
}
</style>