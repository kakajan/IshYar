// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from '@tailwindcss/vite'

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  // Keep folders in root (Nuxt 3 style)
  srcDir: '.',
  dir: {
    app: 'app',
  },

  modules: [
    '@nuxt/eslint',
    '@nuxt/hints',
    '@nuxt/image',
    '@nuxt/scripts',
    '@nuxt/test-utils',
    '@nuxtjs/i18n',
    '@pinia/nuxt',
    '@nuxt/icon',
  ],

  // Disable hydration hints (false positives in SPA mode with ssr: false)
  hints: {
    hydration: false,
  },

  // Vite configuration for Tailwind CSS v4
  vite: {
    plugins: [tailwindcss()],
  },

  // i18n configuration
  i18n: {
    locales: [
      { code: 'en', name: 'English', dir: 'ltr', file: 'en.json' },
      { code: 'fa', name: 'فارسی', dir: 'rtl', file: 'fa.json' },
    ],
    defaultLocale: (process.env.NUXT_PUBLIC_DEFAULT_LOCALE || 'fa') as 'fa' | 'en',
    langDir: 'locales',
    strategy: 'no_prefix',
    vueI18n: './i18n.config.ts',
    detectBrowserLanguage: false,
  },

  // Runtime config
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api/v1',
      appName: 'IshYar',
      defaultLocale: process.env.NUXT_PUBLIC_DEFAULT_LOCALE || 'fa',
      reverbAppKey: process.env.NUXT_PUBLIC_REVERB_APP_KEY || 'local',
      reverbHost: process.env.NUXT_PUBLIC_REVERB_HOST || '127.0.0.1',
      reverbPort: process.env.NUXT_PUBLIC_REVERB_PORT || '8080',
      reverbScheme: process.env.NUXT_PUBLIC_REVERB_SCHEME || 'http',
    }
  },

  // App configuration
  app: {
    head: {
      title: 'IshYar - Enterprise WorkSuite',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Next-generation Enterprise Resource Planning and Task Management Platform' },
        { name: 'theme-color', content: '#3b82f6' },
        { name: 'google', content: 'notranslate' },
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'manifest', href: '/manifest.json' },
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap' },
        { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap' },
      ],
      htmlAttrs: {
        lang: process.env.NUXT_PUBLIC_DEFAULT_LOCALE || 'fa',
        dir: (process.env.NUXT_PUBLIC_DEFAULT_LOCALE || 'fa') === 'fa' ? 'rtl' : 'ltr',
        translate: 'no',
      },
    },
  },

  // CSS
  // In Nuxt 4, `~`/`@` point to `srcDir` (often `app/`). Use `~~` for project root.
  css: ['~~/assets/css/main.css'],

  // Components configuration
  // Exclude index.ts barrel files from auto-import to avoid duplicate component warnings
  components: {
    dirs: [
      {
        path: '~/components',
        extensions: ['.vue'],
        pathPrefix: false,
      },
    ],
  },

  // TypeScript
  typescript: {
    strict: true,
    typeCheck: true,
  },

  // SSR disabled for SPA mode
  ssr: false,

  // Nitro configuration
  nitro: {
    preset: 'static',
  },

  // PWA-like configuration
  experimental: {
    payloadExtraction: false,
  },
})