<template>
    <div class="flex flex-col md:flex-row items-center justify-between mt-6">
        <div class="md:pr-2">
            <ul class="pagination flex flex-wrap md:flex-nowrap">
                <li v-for="(link, key) in links" :class="estatus(link)" :key="key">
                    <Link :class="{
                        'inline-block border border-slate-500 dark:border-slate-100 text-black px-3 py-1 mr-1 dark:text-white rounded hover:bg-slate-200 dark:hover:bg-slate-600':
                            !link.active,
                        'inline-block border border-blue-500 text-white px-3 py-1 mr-1 bg-blue-500 rounded':
                            link.active,
                    }" :href="link.url ? link.url : '#'" @click="isLoading = true">
                    <span v-html="link.label"></span>
                    </Link>
                </li>
            </ul>
        </div>
        <div class="mt-4 md:mt-0">
            <span v-if="total > 0" class="text-black-50 font-light">Usted tiene {{ useNFmt(total, 0) }} registros</span>
        </div>
    </div>
    <div class="vl-parent">
        <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
    </div>
</template>

<script>
import { useNFmt } from '@/composables/useFormato';
import { Link } from '@inertiajs/vue3';
import Loading from 'vue-loading-overlay';
import { ref } from "vue";

export default {
    props: { links: Array, total: Number },
    components: {
        Link,
        Loading,
    },
    setup() {
        const isLoading = ref(false)

        const estatus = (link) => {
            if (link.url === null) return "disabled";
            if (link.active) return "active";
        };
        return { useNFmt, estatus, isLoading };
    },
};
</script>
