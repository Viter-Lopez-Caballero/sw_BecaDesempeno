// playwright.config.js
import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/playwright',
  use: {
    baseURL: 'http://becaslaravel_ad.test:8080', // Cambiado para entorno correcto
    headless: true,
      video: 'on',
  },
  reporter: [['html', { open: 'never' }]],
});