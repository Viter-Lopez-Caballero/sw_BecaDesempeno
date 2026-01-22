<template>
    <HeadLogo :title="title" />
    <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" loader="dots" color="#283C2A" />
    <LayoutAuthenticated>
        <SectionTitleLineWithButton :icon="mdiAccount" :title="title" main />

        <SearchBar @apply-filters="applyFilters" @clear-filters="clearFilters" v-model:search="filters.search"
            v-model:rows="filters.rows" v-model:withTrashed="filters.withTrashed" :routeName="routeName"
            :total="users.meta.total" />

        <CardBox v-if="users.data.length > 0">
            <table>
                <thead>
                    <tr>
                        <th />
                        <SortableHeader @sort="sortByColumn" :filters="filters" column="name" label="Nombre" />
                        <SortableHeader @sort="sortByColumn" :filters="filters" column="email" label="Correo electrónico" />
                        <SortableHeader @sort="sortByColumn" :filters="filters" column="roles" label="Rol" />
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in users.data" :key="item.id">
                        <td>
                            <div class="relative">
                                <img class="w-10 h-10 rounded-sm" :src="IMAGES.user.src" :alt="IMAGES.user.src"
                                    loading="lazy">
                                <span
                                    class="absolute bottom-0 left-8 transform translate-y-1/4 w-3.5 h-3.5 border-2 border-white dark:border-gray-800 rounded-full"
                                    :class="item.deleted_at ? 'bg-error-300' : 'bg-success-300'" />
                            </div>
                        </td>
                        <td data-label="Nombre">
                            {{ item.name }}
                        </td>
                        <td data-label="Correo electrónico">
                            {{ item.email }}
                        </td>
                        <td data-label="Rol">
                            <ul class="list-disc list-inside">
                                <li v-for="(role, index) in item.roles" :key="index" class="text-xs">
                                    {{ role.name }}
                                </li>
                            </ul>
                        </td>
                        <td data-label="Acciones">
                            <BaseButtons>
                                <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                    :parameter="item.id" title="Editar usuario" />
                            </BaseButtons>
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardBox>
        <CardBoxComponentEmpty v-else />
        <pagination :links="users.meta.links" :total="users.meta.total" :to="users.meta.to" :from="users.meta.from" />
    </LayoutAuthenticated>
</template>
<script setup>
import CardBox from "@/Components/CardBox.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import {
    mdiAccount,
    mdiPencil,
} from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import Pagination from "@/Components/Pagination.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import { useFilters } from "@/Hooks/useFilters";
import SearchBar from "@/Components/SearchBar.vue";
import { IMAGES } from "@/Utils/Image";
import SortableHeader from "@/Components/Table/SortableHeader.vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    users: {
        type: Object,
        default: () => ({}),
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    filters: {
        type: Object,
        required: true
    },
});

const { filters, clearFilters, applyFilters, isLoading, sortByColumn } = useFilters(props.filters, props.routeName);
</script>
