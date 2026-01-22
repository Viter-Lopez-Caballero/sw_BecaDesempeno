<template>
    <CardBox>
        <div class="md:flex md:space-x-4 mb-5">
            <div class="md:w-1/2 max-lg:mb-5">
                <FormField label="Nombre del usuario:" required :error="form.errors.name" label-for="name">
                    <FormControl v-model="form.name" placeholder="Nombre del usuario" id="name" />
                </FormField>
            </div>
            <div class="md:w-1/2">
                <FormField label="Correo Electrónico:" required :error="form.errors.email" label-for="email">
                    <FormControl v-model="form.email" type="email" placeholder="Correo Electrónico" id="email" />
                </FormField>
            </div>
        </div>
        <FormField :label="isEdit ? 'Nueva contraseña:' : 'Contraseña:'" required :error="form.errors.password"
            label-for="password">
            <FormControl v-model="form.password" placeholder="Contraseña" type="password" id="password" />
        </FormField>
        <FormField label="Selecciona un rol:" required help="Selecciona el rol que tendrá el usuario" :error="form.errors.roles">
            <table>
                <thead>
                    <tr>
                        <th class="w-10"></th>
                        <th>Nombre de Rol</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in roles" :key="item.id" @click="selectRole(item.id)"
                        class="cursor-pointer hover:bg-gray-50">
                        <td>
                            <input type="radio" :checked="form.roles.includes(item.id)" name="role_selection"
                                class="h-4 w-4 text-forest-600 focus:ring-forest-600 border-forest-600 disabled:opacity-50" :disabled="isEdit" />
                        </td>
                        <td data-label="Nombre">
                            {{ item.name }}
                        </td>
                        <td data-label="Descripción">
                            {{ item.description }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </FormField>

        <template #footer>
            <BaseButtons>
                <slot name="actions" />
            </BaseButtons>
        </template>
    </CardBox>
</template>
<script setup>
import { defineProps } from 'vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import CardBox from '@/Components/CardBox.vue';
import BaseButtons from '@/Components/BaseButtons.vue';

const { form, roles, isEdit } = defineProps({
    form: Object,
    roles: Object,
    isEdit: Boolean
});

const selectRole = (id) => {
    if (isEdit) return;
    form.roles = [id];
};
</script>
