<template>
    <SidebarProvider :defaultOpen="defaultOpen">
        <AppSidebar />

        <SidebarInset class="grid grid-rows-[auto_1fr] min-h-screen">
            <header
                class="sticky top-0 z-10 flex h-12 shrink-0 items-center gap-2 bg-white dark:bg-slate-800 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                <SidebarTrigger class="ml-1 hover:cursor-pointer" />
            </header>

            <div class="bg-white dark:bg-slate-800 dark:text-white font-light">
                <SectionMain>
                    <Banner />

                    <slot />
                </SectionMain>
            </div>

            <FooterBar />
        </SidebarInset>
    </SidebarProvider>
</template>

<script setup>
import AppSidebar from '@/Components/AppSidebar.vue'
import Banner from '@/Components/Banner.vue';
import FooterBar from '@/Components/FooterBar.vue';
import SectionMain from '@/Components/SectionMain.vue';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/Components/ui/sidebar';

import { useCookies } from '@vueuse/integrations/useCookies';

const cookies = useCookies(['sidebar_state'])
const defaultOpen = cookies.get('sidebar_state', true);
</script>
