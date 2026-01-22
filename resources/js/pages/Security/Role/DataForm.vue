<template>
    <CardBox>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <FormField label="Nombre del rol:" required :error="form.errors.name">
                <FormControl v-model="form.name" placeholder="Nombre de rol" />
            </FormField>
            <FormField label="Descripción:" required :error="form.errors.description">
                <FormControl v-model="form.description" placeholder="Descripción" />
            </FormField>
        </div>
    </CardBox>
    <div class="flex flex-col md:flex-row gap-4 my-4">
        <CardBox class="w-full md:w-5/12 p-2 max-md:mb-4 max-h-[550px] md:h-[550px]">
            <SectionTitleLineWithButton title="Módulos" :hisBreadCrumb="false"
                :description="`${modules.length} módulos disponibles`" />

            <div class="flex flex-col space-y-2 overflow-y-auto max-h-96">
                <a v-for="item in modules" :key="item.key" @click="key = item.key" :class="[
                    'p-4 rounded-lg border-2 cursor-pointer transition-all duration-200',
                    item.key === key
                        ? 'border-forest-100 bg-forest-50 text-forest-900'
                        : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'
                ]">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">{{ item.name }}</span>
                        <div v-if="item.key === key" class="w-2 h-2 bg-forest-400 rounded-full" />
                    </div>
                </a>
            </div>
        </CardBox>

        <CardBox class="w-full md:w-7/12 p-2 max-h-[550px] md:h-[550px]">
            <SectionTitleLineWithButton title="Permisos" :his-bread-crumb="false"
                :description="`${permissionsFilter?.[key]?.length ?? 0} permisos disponibles`">
                <Badge color="forest" size="md">
                    {{ form.permissions.length }} seleccionados
                </Badge>
            </SectionTitleLineWithButton>

            <BaseButtons type="justify-start" no-wrap>
                <BaseButton color="lightDark" :icon="mdiCheckAll" small label="TODOS" @click="addAllPermissions()" />
                <BaseButton color="lightDark" :icon="mdiReplyAll" small label="NINGUNO"
                    @click="removeAllPermissions()" />
            </BaseButtons>

            <div class="w-full my-5">
                <FormControl type="search" :icon="mdiMagnify" v-model="inputSearchPermission"
                    placeholder="Ingresa un parámetro de búsqueda" @change="searchPermission"
                    @keyup="searchPermission" />
            </div>

            <div v-if="permissionsFilter?.[key]?.length > 0" class="overflow-y-auto relative max-h-72">
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-2">
                    <li v-for="(item, index) in permissionsFilter[key]" :key="item.id" class="flex items-start">
                        <label :for="'permission_' + item.id" class="flex items-start space-x-3 cursor-pointer w-full">
                            <input type="checkbox" class="mt-1.5 checked:bg-forest-400 ring-forest-100 rounded-sm p-2"
                                :id="'permission_' + item.id" :value="{ id: item.id }"
                                :checked="checkedPermission(item)" @change="togglePermission(item)" />
                            <div class="text-sm text-gray-800 dark:text-gray-200 flex-1 min-w-0">
                                <div class="font-semibold truncate">
                                    {{ index + 1 }} {{ item.name }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs truncate">
                                    {{ item.description }}
                                </div>
                            </div>
                        </label>
                    </li>
                </ul>
            </div>
            <CardBoxComponentEmpty v-else />
        </CardBox>
    </div>
    <CardBox class="mt-6">
        <div class="flex flex-col gap-3 md:flex-row md:justify-start md:gap-4">
            <slot name="actions" />
        </div>
    </CardBox>
</template>
<script setup>
import CardBox from "@/Components/CardBox.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiCheckAll, mdiReplyAll, mdiMagnify, mdiViewModule } from "@mdi/js";
import { defineProps } from 'vue';
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import usePermissionSelection from "./Composables/usePermissionSelection";
import Badge from "@/Components/Badge.vue";

const { permissions, form } = defineProps({
    permissions: {
        type: Object,
        required: true
    },
    modules: {
        type: Object,
        required: true
    },
    form: {
        type: Object,
        required: true
    }
});

const {
    key,
    inputSearchPermission,
    permissionsFilter,
    checkedPermission,
    togglePermission,
    searchPermission,
    addAllPermissions,
    removeAllPermissions,
} = usePermissionSelection(form, permissions);
</script>
