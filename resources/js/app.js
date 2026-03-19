import '../css/app.css';



import { createPinia } from "pinia";
import { useStyleStore } from "@/stores/style.js";
import { darkModeKey, styleKey } from "@/config.js";
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName =
  window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

const pinia = createPinia();

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => {
    const pages = import.meta.glob<Record<string, any>>('./pages/**/*.vue', { eager: true });
    const path = `./pages/${name}.vue`;
    
    if (path in pages) {
      return pages[path];
    }
    
    // Fallback: try case-insensitive lookup for Linux
    const lowerName = name.toLowerCase();
    for (const [key, value] of Object.entries(pages)) {
      if (key.toLowerCase().endsWith(lowerName.toLowerCase() + '.vue')) {
        return value;
      }
    }
    
    console.error(`Page not found: ${path}`);
    throw new Error(`Page not found: ${path}`);
  },
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .use(ZiggyVue)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

const styleStore = useStyleStore(pinia);

/* App style */
styleStore.setStyle(localStorage[styleKey] ?? "basic");

/* Dark mode */
if (
  (!localStorage[darkModeKey] &&
    window.matchMedia("(prefers-color-scheme: dark)").matches) ||
  localStorage[darkModeKey] === "1"
) {
  styleStore.setDarkMode(true);
}
