<script setup>
import { PanelRightOpen, PanelRightClose } from "lucide-vue-next";
import { cn } from "@/lib/utils";
import { Button } from '@/Components/ui/button';
import { useSidebar } from "./utils";
import { computed } from "vue";

const props = defineProps({
  class: { type: null, required: false },
});

const { open, toggleSidebar } = useSidebar();

const iconPanel = computed(() => {
  return open.value ? PanelRightOpen : PanelRightClose;
});

</script>

<template>
  <Button data-sidebar="trigger" data-slot="sidebar-trigger" variant="ghost" size="icon"
    :class="cn('h-7 w-7', props.class)" @click="toggleSidebar">
    <component :is="iconPanel" />
    <span class="sr-only">Toggle Sidebar</span>
  </Button>
</template>
