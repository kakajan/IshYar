# IshYar Frontend

Modern, visual-first frontend for the IshYar ERP & Task Management system.

## Tech Stack

- **Framework**: Nuxt 4 (SPA Mode)
- **UI Library**: Vue 3 (Composition API)
- **Styling**: Tailwind CSS 4.0
- **Components**: [Shadcn Vue](https://www.shadcn-vue.com/) (built on Radix Vue / Reka UI)
  - ⚠️ **Note**: We use **Shadcn Vue**, NOT Nuxt UI. All new UI implementations should use Shadcn Vue components.
- **Charts**: ApexCharts (vue3-apexcharts)
- **State Management**: Pinia
- **Icons**: Lucide Vue
- **Animations**: CSS Transitions + Vue Transitions

## Design Philosophy

- **Visual-First**: Card-based layouts, intuitive visual hierarchy
- **Simple & Clean**: Pure Vue + Tailwind components
- **Interactive**: Smooth transitions and hover states
- **Accessible**: WCAG 2.1 AA compliance
- **RTL Support**: Full right-to-left language support

## Setup

Make sure to install dependencies:

```bash
# npm
npm install

# pnpm
pnpm install

# yarn
yarn install

# bun
bun install
```

## Development Server

Start the development server on `http://localhost:3000`:

```bash
# npm
npm run dev

# pnpm
pnpm dev

# yarn
yarn dev

# bun
bun run dev
```

## Production

Build the application for production:

```bash
# npm
npm run build

# pnpm
pnpm build

# yarn
yarn build

# bun
bun run build
```

Locally preview production build:

```bash
# npm
npm run preview

# pnpm
pnpm preview

# yarn
yarn preview

# bun
bun run preview
```

Check out the [deployment documentation](https://nuxt.com/docs/getting-started/deployment) for more information.
