<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import EvaluadorLayout from '@/layouts/EvaluadorLayout.vue'
import DocenteLayout from '@/layouts/DocenteLayout.vue'

const page = usePage()

const layouts = {
  LayoutAuthenticated,
  AdminLayout,
  EvaluadorLayout,
  DocenteLayout,
}

const currentLayout = computed(() => {
  const layoutName = page.props.auth.layoutName
  return layouts[layoutName] || LayoutAuthenticated
})
</script>

<template>
  <component :is="currentLayout">
    <slot />
  </component>
</template>
