// https://nuxt.com/docs/api/configuration/nuxt-config
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
    '@nuxt/ui',
    '@nuxtjs/i18n',
    '@pinia/nuxt',
  ],

  // i18n configuration
  i18n: {
    locales: [
      { code: 'en', name: 'English', dir: 'ltr', file: 'en.json' },
      { code: 'fa', name: 'فارسی', dir: 'rtl', file: 'fa.json' },
    ],
    defaultLocale: 'en',
    langDir: './locales',
    strategy: 'no_prefix',
    detectBrowserLanguage: {
      useCookie: true,
      cookieKey: 'i18n_redirected',
      redirectOn: 'root',
    },
  },

  // Runtime config
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api/v1',
      appName: 'IshYar',
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
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'manifest', href: '/manifest.json' },
      ],
      htmlAttrs: {
        lang: 'en',
        dir: 'ltr',
      },
    },
  },

  // CSS
  // In Nuxt 4, `~`/`@` point to `srcDir` (often `app/`). Use `~~` for project root.
  css: ['~~/assets/css/main.css'],

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