<template>
    <HeadLogo :title="title" />
    <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" loader="dots" color="#283C2A" />
    <LayoutAuthenticated>
        <SectionTitleLineWithButton :icon="mdiAccountSupervisor" :title="title" main />

        <SearchBar @apply-filters="applyFilters" @clear-filters="clearFilters" v-model:search="filters.search"
            v-model:rows="filters.rows" :routeName="routeName" :total="roles.meta.total" />

        <CardBox v-if="roles.data.length > 0">
            <table>
                <thead>
                    <tr>
                        <SortableHeader @sort="sortByColumn" :filters="filters" column="name" label="Nombre" />
                        <SortableHeader @sort="sortByColumn" :filters="filters" column="description" label="Descripción" />
                        <SortableHeader @sort="sortByColumn" :filters="filters" column="created_at" label="Fecha" />
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in roles.data" :key="item.id">
                        <td data-label="Nombre">
                            {{ item.name }}
                        </td>
                        <td data-label="Descripción">
                            {{ item.description }}
                        </td>
                        <td data-label="Fecha">
                            {{ item.created_at.human }}
                        </td>
                        <td data-label="Acciones">
                            <BaseButtons>
                                <BaseButton title="Editar rol" color="info" :icon="mdiPencil" small
                                    :routeName="`${routeName}edit`" :parameter="item.id" />
                            </BaseButtons>
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardBox>
        <CardBoxComponentEmpty v-else />
        <pagination :links="roles.meta.links" :total="roles.meta.total" :to="roles.meta.to" :from="roles.meta.from" />
    </LayoutAuthenticated>
</template>
<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import {
    mdiAccountSupervisor,
    mdiPencil,
} from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import Pagination from "@/Components/Pagination.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import { useFilters } from "@/Hooks/useFilters";
import SearchBar from "@/Components/SearchBar.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    roles: {
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
