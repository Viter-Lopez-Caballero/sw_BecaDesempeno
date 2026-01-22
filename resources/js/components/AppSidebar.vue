<template>
  <Sidebar class="bg-cover bg-center border-slate-100 dark:border-gray-800" v-bind="props">
    <AsideHeader :logo="IMAGES.logo.src" />

    <SidebarContent>
      <SidebarGroup v-for="{ labelGroup, items } in asideMenu" :key="labelGroup">
        <SidebarGroupLabel>{{ labelGroup }}</SidebarGroupLabel>
        <AsideMenuList v-for="item in items" :key="item.label" :item="item" />
      </SidebarGroup>
    </SidebarContent>

    <SidebarFooter>
      <AsideFooter />
    </SidebarFooter>
  </Sidebar>
</template>

<script setup>
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupLabel,
} from '@/Components/ui/sidebar';
import AsideMenuList from "@/Layouts/Components/AsideMenuList.vue";
import { useAside } from "@/Layouts/Composables/asideMenu.js";
import AsideHeader from '@/Layouts/Components/AsideHeader.vue';
import AsideFooter from '@/Layouts/Components/AsideFooter.vue';
import { IMAGES } from '@/Utils/Image';

const props = defineProps({
  side: { type: String, required: false },
  variant: { type: String, required: false },
  collapsible: { type: String, required: false, default: "icon" },
  class: { type: null, required: false },
});

const { asideMenu } = useAside();
</script>
