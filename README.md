<div align="center">

<!-- Logo Placeholder -->
<img src="./assets/logo.svg" alt="IshYar Logo" width="120" height="120" />

# ✨ IshYar

### *Where Vision Meets Execution*

**A modern, visual-first ERP & Task Management system inspired by Apple's design philosophy.**

[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen.svg)]()
[![Version](https://img.shields.io/badge/Version-1.0.0--beta-orange.svg)]()
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20.svg?logo=laravel)](https://laravel.com)
[![Nuxt](https://img.shields.io/badge/Nuxt-4-00DC82.svg?logo=nuxt.js)](https://nuxt.com)
[![Vue](https://img.shields.io/badge/Vue-3-4FC08D.svg?logo=vue.js)](https://vuejs.org)
[![PRs Welcome](https://img.shields.io/badge/PRs-Welcome-brightgreen.svg)]()

<br />

[🚀 Live Demo](#) · [📖 Documentation](#) · [🐛 Report Bug](../../issues) · [💡 Request Feature](../../issues)

</div>

---

<br />

## 🎯 The Problem We Solve

> *"Traditional ERPs are built for databases, not for humans."*

Enterprise Resource Planning systems have become synonymous with complexity. Endless spreadsheets, confusing navigation, and interfaces that require weeks of training. Meanwhile, your team loses hours every day just trying to figure out *what to do next*.

**IshYar takes a radically different approach.**

We believe that managing a company should feel as intuitive as scrolling through your favorite app. By combining a **Visual Org-Chart** approach with intelligent task automation, IshYar bridges the gap between high-level project management and daily employee execution.

### How We Solve It

| Traditional ERPs | IshYar |
|-----------------|--------|
| ❌ Complex navigation trees | ✅ **Visual hierarchy** – See your entire organization at a glance |
| ❌ Information overload | ✅ **Role-based focus** – Everyone sees exactly what they need |
| ❌ Manual task assignment | ✅ **Smart automation** – Routine tasks manage themselves |
| ❌ Desktop-only experience | ✅ **PWA excellence** – Full functionality on any device |
| ❌ Steep learning curve | ✅ **Intuitive UX** – Onboard in minutes, not months |

<br />

---

## ⭐ Key Features

<table>
<tr>
<td width="50%">

### 🌳 Interactive Org-Tree

Navigate your organization like never before. Our **D3.js-powered** animated hierarchy lets you:

- Zoom, pan, and explore departments intuitively
- Click any node to drill down into teams & individuals
- Visualize workload distribution in real-time
- Drag-and-drop organizational restructuring

</td>
<td width="50%">

### ⚡ Smart Task Engine

Not all tasks are created equal. IshYar distinguishes between:

- **🔄 Routine Tasks** – Recurring workflows that auto-generate and self-assign
- **🎯 Situational Tasks** – One-time projects with intelligent priority scoring
- **📊 Progress Tracking** – Visual completion rates at every level

</td>
</tr>
<tr>
<td width="50%">

### 📢 Multi-Channel Notifications

Stay connected your way:

- **🌐 Web Push** – Instant browser notifications
- **✈️ Telegram** – Real-time bot integration
- **📱 SMS** – Critical alerts never missed
- **📧 Email** – Detailed digests and summaries

</td>
<td width="50%">

### 📲 PWA Excellence

Desktop-class experience on mobile:

- **⚡ Offline Mode** – Work without internet, sync when connected
- **🏠 Home Screen Install** – Native app feel, zero app store friction
- **🔔 Background Sync** – Updates happen automatically
- **💾 Local Caching** – Lightning-fast load times

</td>
</tr>
</table>

<br />

### 🎭 Role-Based Dashboards

| Role | Experience |
|------|-----------|
| **👑 Owner/CEO** | Bird's-eye view of all operations, KPI dashboards, organizational health metrics |
| **📋 Project Manager** | Workspace with Kanban boards, team capacity planning, deadline tracking |
| **👤 Employee** | Focus Mode – A distraction-free view of today's tasks and priorities |

<br />

---

## 🏗️ Architecture & Tech Stack

IshYar is built on a **Modular Monolith** architecture – combining the simplicity of a monolith with the flexibility of microservices. This approach allows for:

- Clean separation of concerns
- Easy extraction of modules as the system scales
- Simplified deployment and maintenance
- Consistent developer experience

<br />

### Backend: Laravel 12

```
┌─────────────────────────────────────────────────────────────┐
│                      Laravel 12 API                         │
├─────────────┬─────────────┬─────────────┬──────────────────┤
│   Auth      │   HR        │   Tasks     │   Notifications  │
│   Module    │   Module    │   Module    │   Module         │
├─────────────┴─────────────┴─────────────┴──────────────────┤
│                    Shared Domain Layer                      │
├─────────────────────────────────────────────────────────────┤
│              PostgreSQL / MySQL Database                    │
└─────────────────────────────────────────────────────────────┘
```

**Why Laravel 12?**
- 🚀 Best-in-class developer experience
- 🔐 Built-in authentication with Sanctum
- 📡 Native queue system for background jobs
- 🧪 First-class testing support

<br />

### Frontend: Nuxt 4 + Vue 3

```
┌─────────────────────────────────────────────────────────────┐
│                       Nuxt 4 (SSR/SPA)                      │
├─────────────────────────────────────────────────────────────┤
│                         Vue 3 + Pinia                       │
├─────────────┬─────────────┬─────────────┬──────────────────┤
│  Shadcn     │   D3.js     │   PWA       │   Tailwind       │
│  Vue UI     │   Charts    │   Module    │   CSS 4          │
└─────────────┴─────────────┴─────────────┴──────────────────┘
```

**Why Nuxt 4?**
- ⚡ Hybrid rendering (SSR + SPA + Static)
- 📁 File-based routing with layouts
- 🔄 Auto-imports for a cleaner codebase
- 🎯 Optimal SEO and performance out of the box

<br />

### Full Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend Framework** | Laravel 12 |
| **Frontend Framework** | Nuxt 4 / Vue 3 |
| **UI Components** | Shadcn Vue |
| **Styling** | Tailwind CSS 4 |
| **Data Visualization** | D3.js |
| **Progressive Web App** | Vite PWA Plugin |
| **Authentication** | Laravel Sanctum |
| **Database** | PostgreSQL / MySQL |
| **Caching** | Redis |
| **Queue** | Laravel Horizon |

<br />

---

## 🚀 Installation Guide

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 20+
- PNPM (recommended) or NPM
- PostgreSQL or MySQL
- Redis (optional, for caching/queues)

<br />

### 🔧 Backend Setup

```bash
# Clone the repository
git clone https://github.com/kakajan/IshYar.git
cd IshYar/backend

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure your database in .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=ishyar
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations and seeders
php artisan migrate --seed

# Start the development server
php artisan serve
```

<br />

### 🎨 Frontend Setup

```bash
# Navigate to frontend directory
cd ../frontend

# Install dependencies (PNPM recommended)
pnpm install
# or: npm install

# Environment configuration
cp .env.example .env

# Configure API endpoint
# NUXT_PUBLIC_API_BASE=http://localhost:8000/api

# Start development server
pnpm dev
# or: npm run dev
```

<br />

### 🐳 Docker Setup (Coming Soon)

```bash
# One-command setup
docker-compose up -d
```

<br />

---

## 📸 Screenshots & Gallery

<div align="center">

### 👑 Owner Dashboard
*Bird's-eye view of your entire organization*

<!-- Screenshot Placeholder -->
<img src="./assets/screenshots/owner-dashboard.png" alt="Owner Dashboard" width="800" />

<br /><br />

### 📋 Project Manager Workspace
*Kanban boards, team capacity, and deadline tracking*

<!-- Screenshot Placeholder -->
<img src="./assets/screenshots/pm-workspace.png" alt="PM Workspace" width="800" />

<br /><br />

### 🎯 Employee Focus Mode
*Distraction-free productivity*

<!-- Screenshot Placeholder -->
<img src="./assets/screenshots/employee-focus.png" alt="Employee Focus Mode" width="800" />

<br /><br />

### 🌳 Interactive Org-Tree
*Navigate your organization visually*

<!-- Screenshot Placeholder -->
<img src="./assets/screenshots/org-tree.png" alt="Org Tree" width="800" />

</div>

<br />

---

## 🎨 Design Language

IshYar embraces an **Apple-inspired aesthetic** that prioritizes clarity and elegance:

<table>
<tr>
<td width="33%" align="center">

### 🪟 Glassmorphism

Frosted glass effects with depth and transparency

</td>
<td width="33%" align="center">

### ✨ Minimalist UI

Every pixel serves a purpose

</td>
<td width="33%" align="center">

### 🌙 Dark Mode First

Easy on the eyes, stunning in any light

</td>
</tr>
</table>

**Design Principles:**
- **Whitespace is a feature** – Generous padding for visual breathing room
- **Micro-interactions** – Subtle animations that delight
- **Typography hierarchy** – Clear visual order in every view
- **Consistent iconography** – Lucide icons throughout

<br />

---

## 🗺️ Roadmap

We're just getting started. Here's what's coming:

### 🔮 Near Future (Q1 2025)
- [ ] 🤖 **AI-Powered Productivity Insights** – Smart suggestions based on work patterns
- [ ] 📊 **Advanced Analytics Dashboard** – Deep dive into team performance
- [ ] 🔗 **Webhook Integrations** – Connect to your favorite tools

### 🚀 Medium Term (Q2-Q3 2025)
- [ ] 📅 **Calendar Integration** – Sync with Google Calendar, Outlook
- [ ] 💬 **Built-in Team Chat** – Real-time collaboration
- [ ] 📈 **Time Tracking** – Automatic and manual time logging
- [ ] 🌍 **Multi-language Support** – Full i18n implementation

### 🌟 Long Term Vision
- [ ] 🏢 **Multi-tenant Architecture** – SaaS-ready deployment
- [ ] 🔌 **Plugin Ecosystem** – Community-driven extensions
- [ ] 📱 **Native Mobile Apps** – iOS and Android companions
- [ ] 🤝 **ERP Integrations** – SAP, Oracle, Microsoft Dynamics connectors

<br />

---

## 🤝 Contributing

We believe in the power of community. IshYar is open-source, and we warmly welcome contributions of all kinds!

### How to Contribute

```bash
# 1. Fork the repository
# Click the 'Fork' button at the top right of this page

# 2. Clone your fork
git clone https://github.com/YOUR_USERNAME/IshYar.git

# 3. Create a feature branch
git checkout -b feature/amazing-feature

# 4. Make your changes
# ... code, code, code ...

# 5. Commit with a descriptive message
git commit -m "feat: add amazing feature"

# 6. Push to your branch
git push origin feature/amazing-feature

# 7. Open a Pull Request
# Go to the original repository and click 'New Pull Request'
```

### Contribution Guidelines

| Type | Description |
|------|-------------|
| 🐛 **Bug Reports** | Found a bug? Open an issue with steps to reproduce |
| 💡 **Feature Requests** | Great ideas are always welcome! |
| 📖 **Documentation** | Help us improve our docs |
| 🌍 **Translations** | Help make IshYar accessible worldwide |
| 🧪 **Testing** | More test coverage = more stability |

<br />

> 💡 **First time contributing?** Look for issues labeled `good first issue` – they're perfect for getting started!

<br />

---

## 📜 License

IshYar is open-source software licensed under the [MIT License](LICENSE).

```
MIT License

Copyright (c) 2024 IshYar Contributors

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software...
```

<br />

---

<div align="center">

### 🌟 Star Us on GitHub!

If IshYar helps your organization, consider giving us a ⭐ — it helps others discover this project!

<br />

**Built with ❤️ by the IshYar Community**

[Website](#) · [Documentation](#) · [Discord](#) · [Twitter](#)

<br />

<sub>*"Simplicity is the ultimate sophistication." — Leonardo da Vinci*</sub>

</div>
