# IshYar UI/UX Design Bible

> **Version**: 1.0.0  
> **Design Philosophy**: Apple-inspired minimalism meets enterprise functionality  
> **Approach**: Mobile-First, Performance-Obsessed, Accessibility-Native

---

## Table of Contents

1. [Design Principles](#design-principles)
2. [Design Tokens](#design-tokens)
3. [Color System](#color-system)
4. [Typography](#typography)
5. [Spacing & Layout](#spacing--layout)
6. [Component Library](#component-library)
7. [Animation System](#animation-system)
8. [Responsive Patterns](#responsive-patterns)
9. [RTL Support](#rtl-support)
10. [Accessibility](#accessibility)
11. [Dark Mode](#dark-mode)
12. [Iconography](#iconography)
13. [Page Templates](#page-templates)

---

## 1. Design Principles

### The IshYar Design DNA

```
┌─────────────────────────────────────────────────────────────┐
│                                                              │
│   "Complexity is the enemy of execution.                    │
│    We design for clarity, not decoration."                  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Core Principles

#### 1. Generous Whitespace
- Breathing room between elements
- Content should feel uncrowded
- Minimum 16px between related elements
- Minimum 32px between sections

#### 2. Visual Hierarchy
- One primary action per view
- Clear information architecture
- Progressive disclosure of complexity
- F-pattern and Z-pattern reading flows

#### 3. Purposeful Animation
- Every animation serves a purpose
- Micro-interactions provide feedback
- Never animate for decoration
- Respect reduced-motion preferences

#### 4. Mobile-First Reality
- Design for thumb zones
- Touch targets minimum 44x44px
- Content prioritization for small screens
- Desktop adds, never mobile removes

#### 5. Accessibility by Default
- WCAG 2.1 AA compliance minimum
- Semantic HTML first
- Keyboard navigation complete
- Screen reader optimized

---

## 2. Design Tokens

### Token Architecture

```css
/* Design tokens using CSS custom properties */
/* Located in: assets/css/tokens.css */

:root {
  /* === SPACING === */
  --space-0: 0;
  --space-1: 0.25rem;   /* 4px */
  --space-2: 0.5rem;    /* 8px */
  --space-3: 0.75rem;   /* 12px */
  --space-4: 1rem;      /* 16px */
  --space-5: 1.25rem;   /* 20px */
  --space-6: 1.5rem;    /* 24px */
  --space-8: 2rem;      /* 32px */
  --space-10: 2.5rem;   /* 40px */
  --space-12: 3rem;     /* 48px */
  --space-16: 4rem;     /* 64px */
  --space-20: 5rem;     /* 80px */
  --space-24: 6rem;     /* 96px */

  /* === RADIUS === */
  --radius-none: 0;
  --radius-sm: 0.25rem;   /* 4px */
  --radius-md: 0.5rem;    /* 8px */
  --radius-lg: 0.75rem;   /* 12px */
  --radius-xl: 1rem;      /* 16px */
  --radius-2xl: 1.5rem;   /* 24px */
  --radius-full: 9999px;

  /* === SHADOWS === */
  --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
  
  /* Glass shadow for elevated elements */
  --shadow-glass: 0 8px 32px rgb(0 0 0 / 0.08), 0 0 0 1px rgb(255 255 255 / 0.1);

  /* === TRANSITIONS === */
  --duration-fast: 100ms;
  --duration-normal: 200ms;
  --duration-slow: 300ms;
  --duration-slower: 500ms;
  
  --ease-default: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-in: cubic-bezier(0.4, 0, 1, 1);
  --ease-out: cubic-bezier(0, 0, 0.2, 1);
  --ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
  --ease-spring: cubic-bezier(0.175, 0.885, 0.32, 1.275);

  /* === Z-INDEX === */
  --z-dropdown: 50;
  --z-sticky: 100;
  --z-fixed: 200;
  --z-modal-backdrop: 300;
  --z-modal: 400;
  --z-popover: 500;
  --z-tooltip: 600;
  --z-toast: 700;
}
```

---

## 3. Color System

### Semantic Color Palette

```css
:root {
  /* === PRIMARY (Blue - Trust & Professionalism) === */
  --color-primary-50: oklch(97% 0.015 250);
  --color-primary-100: oklch(94% 0.035 250);
  --color-primary-200: oklch(88% 0.07 250);
  --color-primary-300: oklch(80% 0.12 250);
  --color-primary-400: oklch(70% 0.16 250);
  --color-primary-500: oklch(60% 0.19 250);  /* Primary */
  --color-primary-600: oklch(52% 0.19 250);
  --color-primary-700: oklch(45% 0.17 250);
  --color-primary-800: oklch(38% 0.14 250);
  --color-primary-900: oklch(32% 0.10 250);
  --color-primary-950: oklch(24% 0.07 250);

  /* === NEUTRAL (Gray Scale) === */
  --color-neutral-50: oklch(98% 0.002 250);
  --color-neutral-100: oklch(96% 0.004 250);
  --color-neutral-200: oklch(92% 0.006 250);
  --color-neutral-300: oklch(87% 0.008 250);
  --color-neutral-400: oklch(70% 0.010 250);
  --color-neutral-500: oklch(55% 0.012 250);
  --color-neutral-600: oklch(45% 0.010 250);
  --color-neutral-700: oklch(37% 0.008 250);
  --color-neutral-800: oklch(27% 0.006 250);
  --color-neutral-900: oklch(20% 0.004 250);
  --color-neutral-950: oklch(14% 0.003 250);

  /* === SUCCESS (Green) === */
  --color-success-50: oklch(97% 0.025 150);
  --color-success-500: oklch(65% 0.20 150);
  --color-success-700: oklch(45% 0.16 150);

  /* === WARNING (Amber) === */
  --color-warning-50: oklch(97% 0.03 85);
  --color-warning-500: oklch(75% 0.18 75);
  --color-warning-700: oklch(55% 0.16 60);

  /* === ERROR (Red) === */
  --color-error-50: oklch(97% 0.015 25);
  --color-error-500: oklch(60% 0.22 25);
  --color-error-700: oklch(45% 0.20 25);

  /* === INFO (Cyan) === */
  --color-info-50: oklch(97% 0.02 200);
  --color-info-500: oklch(65% 0.15 200);
  --color-info-700: oklch(50% 0.13 200);
}

/* Semantic mappings */
:root {
  /* Backgrounds */
  --bg-primary: var(--color-neutral-50);
  --bg-secondary: var(--color-neutral-100);
  --bg-tertiary: var(--color-neutral-200);
  --bg-inverse: var(--color-neutral-900);
  
  /* Surfaces (cards, dialogs) */
  --surface-default: white;
  --surface-raised: white;
  --surface-overlay: white;
  
  /* Text */
  --text-primary: var(--color-neutral-900);
  --text-secondary: var(--color-neutral-600);
  --text-tertiary: var(--color-neutral-500);
  --text-inverse: white;
  --text-link: var(--color-primary-600);
  
  /* Borders */
  --border-default: var(--color-neutral-200);
  --border-strong: var(--color-neutral-300);
  --border-focus: var(--color-primary-500);
  
  /* Interactive */
  --interactive-default: var(--color-primary-500);
  --interactive-hover: var(--color-primary-600);
  --interactive-active: var(--color-primary-700);
}
```

### Dark Mode Colors

```css
.dark {
  /* Backgrounds */
  --bg-primary: var(--color-neutral-950);
  --bg-secondary: var(--color-neutral-900);
  --bg-tertiary: var(--color-neutral-800);
  --bg-inverse: var(--color-neutral-50);
  
  /* Surfaces */
  --surface-default: var(--color-neutral-900);
  --surface-raised: var(--color-neutral-800);
  --surface-overlay: var(--color-neutral-800);
  
  /* Text */
  --text-primary: var(--color-neutral-50);
  --text-secondary: var(--color-neutral-400);
  --text-tertiary: var(--color-neutral-500);
  --text-inverse: var(--color-neutral-900);
  
  /* Borders */
  --border-default: var(--color-neutral-800);
  --border-strong: var(--color-neutral-700);
  
  /* Shadows - softer in dark mode */
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.3), 0 2px 4px -2px rgb(0 0 0 / 0.2);
  --shadow-glass: 0 8px 32px rgb(0 0 0 / 0.3), 0 0 0 1px rgb(255 255 255 / 0.05);
}
```

### Status Colors for Tasks

```css
:root {
  /* Task Priority */
  --priority-critical: oklch(60% 0.22 25);   /* Red */
  --priority-high: oklch(70% 0.18 40);       /* Orange */
  --priority-medium: oklch(75% 0.15 85);     /* Amber */
  --priority-low: oklch(70% 0.12 200);       /* Cyan */
  
  /* Task Status */
  --status-not-started: var(--color-neutral-400);
  --status-in-progress: var(--color-primary-500);
  --status-pending-review: var(--color-warning-500);
  --status-completed: var(--color-success-500);
  --status-blocked: var(--color-error-500);
}
```

---

## 4. Typography

### Font Families

```css
:root {
  /* Primary font for Latin text */
  --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 
               Roboto, Oxygen, Ubuntu, sans-serif;
  
  /* Persian/Arabic font */
  --font-persian: 'Vazirmatn', 'Inter', sans-serif;
  
  /* Monospace for code */
  --font-mono: 'JetBrains Mono', 'Fira Code', Consolas, monospace;
}

/* RTL font switching */
:root[dir="rtl"] {
  --font-sans: var(--font-persian);
}
```

### Type Scale (Major Third - 1.25)

```css
:root {
  /* Font sizes */
  --text-xs: 0.75rem;     /* 12px */
  --text-sm: 0.875rem;    /* 14px */
  --text-base: 1rem;      /* 16px */
  --text-lg: 1.125rem;    /* 18px */
  --text-xl: 1.25rem;     /* 20px */
  --text-2xl: 1.5rem;     /* 24px */
  --text-3xl: 1.875rem;   /* 30px */
  --text-4xl: 2.25rem;    /* 36px */
  --text-5xl: 3rem;       /* 48px */
  
  /* Line heights */
  --leading-none: 1;
  --leading-tight: 1.25;
  --leading-snug: 1.375;
  --leading-normal: 1.5;
  --leading-relaxed: 1.625;
  --leading-loose: 2;
  
  /* Font weights */
  --weight-normal: 400;
  --weight-medium: 500;
  --weight-semibold: 600;
  --weight-bold: 700;
  
  /* Letter spacing */
  --tracking-tighter: -0.05em;
  --tracking-tight: -0.025em;
  --tracking-normal: 0;
  --tracking-wide: 0.025em;
}
```

### Typography Utilities

```css
/* Heading styles */
.heading-1 {
  font-size: var(--text-4xl);
  font-weight: var(--weight-bold);
  line-height: var(--leading-tight);
  letter-spacing: var(--tracking-tight);
}

.heading-2 {
  font-size: var(--text-3xl);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-tight);
}

.heading-3 {
  font-size: var(--text-2xl);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-snug);
}

.heading-4 {
  font-size: var(--text-xl);
  font-weight: var(--weight-medium);
  line-height: var(--leading-snug);
}

/* Body text */
.body-lg {
  font-size: var(--text-lg);
  line-height: var(--leading-relaxed);
}

.body-base {
  font-size: var(--text-base);
  line-height: var(--leading-normal);
}

.body-sm {
  font-size: var(--text-sm);
  line-height: var(--leading-normal);
}

/* Caption/Label */
.caption {
  font-size: var(--text-xs);
  font-weight: var(--weight-medium);
  line-height: var(--leading-normal);
  letter-spacing: var(--tracking-wide);
  text-transform: uppercase;
}
```

---

## 5. Spacing & Layout

### Container System

```css
/* Container widths */
.container {
  width: 100%;
  margin-inline: auto;
  padding-inline: var(--space-4);
}

@media (min-width: 640px) {
  .container { max-width: 640px; }
}
@media (min-width: 768px) {
  .container { max-width: 768px; padding-inline: var(--space-6); }
}
@media (min-width: 1024px) {
  .container { max-width: 1024px; }
}
@media (min-width: 1280px) {
  .container { max-width: 1280px; padding-inline: var(--space-8); }
}
```

### Layout Grid

```css
/* Standard layout grid */
.layout-grid {
  display: grid;
  gap: var(--space-4);
  grid-template-columns: repeat(4, 1fr);
}

@media (min-width: 640px) {
  .layout-grid { grid-template-columns: repeat(6, 1fr); }
}
@media (min-width: 768px) {
  .layout-grid { grid-template-columns: repeat(8, 1fr); gap: var(--space-6); }
}
@media (min-width: 1024px) {
  .layout-grid { grid-template-columns: repeat(12, 1fr); }
}
```

### Page Layout Patterns

```
┌──────────────────────────────────────────────────────────────┐
│                        Page Layout                            │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ HEADER (64px desktop / 56px mobile)                      │ │
│  │ Logo | Search | Notifications | Profile                 │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  ┌───────────┐  ┌────────────────────────────────────────┐  │
│  │           │  │                                         │  │
│  │  SIDEBAR  │  │           MAIN CONTENT                  │  │
│  │  (256px)  │  │                                         │  │
│  │           │  │  ┌─────────────────────────────────┐   │  │
│  │  • Home   │  │  │ Page Header                     │   │  │
│  │  • Tasks  │  │  │ Title + Actions                 │   │  │
│  │  • Kanban │  │  └─────────────────────────────────┘   │  │
│  │  • Team   │  │                                         │  │
│  │  • Reports│  │  ┌─────────────────────────────────┐   │  │
│  │           │  │  │                                  │   │  │
│  │           │  │  │     Content Area                │   │  │
│  │           │  │  │                                  │   │  │
│  │           │  │  └─────────────────────────────────┘   │  │
│  │           │  │                                         │  │
│  └───────────┘  └────────────────────────────────────────┘  │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ MOBILE BOTTOM NAV (shown < 768px, 64px height)          │ │
│  └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

---

## 6. Component Library

### 6.1 Buttons

```vue
<!-- Button Component Variants -->

<!-- Primary Button -->
<Button variant="default" size="default">
  Create Task
</Button>

<!-- Secondary Button -->
<Button variant="outline" size="default">
  Cancel
</Button>

<!-- Ghost Button -->
<Button variant="ghost" size="sm">
  <Icon name="edit" class="w-4 h-4" />
</Button>

<!-- Destructive Button -->
<Button variant="destructive" size="default">
  Delete
</Button>

<!-- Button with Loading -->
<Button :disabled="loading">
  <Loader2 v-if="loading" class="w-4 h-4 mr-2 animate-spin" />
  {{ loading ? 'Saving...' : 'Save' }}
</Button>
```

**Button Specifications:**

| Property | Default | sm | lg |
|----------|---------|----|----|
| Height | 40px | 32px | 48px |
| Padding X | 16px | 12px | 24px |
| Font Size | 14px | 13px | 16px |
| Border Radius | 8px | 6px | 10px |
| Min Touch Target | 44px | 44px | 48px |

### 6.2 Cards

```vue
<!-- Standard Card -->
<Card class="hover:shadow-lg transition-shadow duration-200">
  <CardHeader>
    <CardTitle>Task Title</CardTitle>
    <CardDescription>Due in 3 days</CardDescription>
  </CardHeader>
  <CardContent>
    <!-- Content -->
  </CardContent>
  <CardFooter>
    <!-- Actions -->
  </CardFooter>
</Card>

<!-- Glass Card (Premium feel) -->
<Card class="bg-white/80 dark:bg-neutral-900/80 
             backdrop-blur-xl border-white/20
             shadow-glass">
  <!-- Content -->
</Card>
```

**Card Specifications:**

| Property | Value |
|----------|-------|
| Background | white / neutral-900 |
| Border | 1px neutral-200 |
| Border Radius | 12px (lg) |
| Padding | 24px (default) |
| Shadow | shadow-sm (default), shadow-lg (hover) |
| Transition | 200ms ease-out |

### 6.3 Task Card (Custom Component)

```vue
<template>
  <Card 
    :class="[
      'group cursor-pointer transition-all duration-200',
      'hover:shadow-lg hover:-translate-y-0.5',
      'active:scale-[0.98]',
      { 'opacity-60': task.status === 'completed' }
    ]"
    @click="$emit('click', task)"
  >
    <!-- Priority Indicator -->
    <div 
      :class="[
        'absolute top-0 left-0 w-1 h-full rounded-l-lg',
        priorityColors[task.priority]
      ]" 
    />
    
    <CardHeader class="pb-3">
      <div class="flex items-start justify-between gap-3">
        <CardTitle class="text-base font-medium line-clamp-2">
          {{ task.title }}
        </CardTitle>
        <StatusBadge :status="task.status" />
      </div>
    </CardHeader>
    
    <CardContent class="pb-3">
      <!-- Progress Ring -->
      <ProgressRing :value="task.progress" :size="48" />
      
      <!-- Labels -->
      <div class="flex flex-wrap gap-1.5 mt-3">
        <Badge 
          v-for="label in task.labels" 
          :key="label.id"
          :style="{ backgroundColor: label.color }"
          variant="secondary"
          class="text-xs"
        >
          {{ label.name }}
        </Badge>
      </div>
    </CardContent>
    
    <CardFooter class="pt-3 border-t">
      <div class="flex items-center justify-between w-full">
        <!-- Assignees -->
        <AvatarStack :users="task.assignees" :max="3" />
        
        <!-- Due Date -->
        <span :class="dueDateClasses">
          <Icon name="calendar" class="w-3.5 h-3.5 mr-1" />
          {{ formatDueDate(task.due_date) }}
        </span>
      </div>
    </CardFooter>
    
    <!-- Hover Actions -->
    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 
                transition-opacity duration-200">
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button variant="ghost" size="icon" class="h-8 w-8">
            <Icon name="more-vertical" class="w-4 h-4" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuItem @click="$emit('edit', task)">
            <Icon name="edit" class="w-4 h-4 mr-2" /> Edit
          </DropdownMenuItem>
          <DropdownMenuItem @click="$emit('duplicate', task)">
            <Icon name="copy" class="w-4 h-4 mr-2" /> Duplicate
          </DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuItem 
            class="text-error-500" 
            @click="$emit('delete', task)"
          >
            <Icon name="trash" class="w-4 h-4 mr-2" /> Delete
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </Card>
</template>
```

### 6.4 Forms

```vue
<!-- Form Field Pattern -->
<FormField v-slot="{ componentField }" name="title">
  <FormItem>
    <FormLabel>Task Title</FormLabel>
    <FormControl>
      <Input 
        v-bind="componentField" 
        placeholder="Enter task title..."
        class="h-11"
      />
    </FormControl>
    <FormDescription>
      Brief description of what needs to be done.
    </FormDescription>
    <FormMessage />
  </FormItem>
</FormField>

<!-- Select with Search -->
<FormField v-slot="{ componentField }" name="assignee">
  <FormItem>
    <FormLabel>Assign to</FormLabel>
    <FormControl>
      <Combobox v-bind="componentField">
        <ComboboxAnchor>
          <ComboboxInput placeholder="Search users..." />
          <ComboboxTrigger />
        </ComboboxAnchor>
        <ComboboxContent>
          <ComboboxEmpty>No users found.</ComboboxEmpty>
          <ComboboxItem 
            v-for="user in users" 
            :key="user.id" 
            :value="user.id"
          >
            <Avatar :src="user.avatar" :name="user.name" size="sm" />
            <span class="ml-2">{{ user.name }}</span>
          </ComboboxItem>
        </ComboboxContent>
      </Combobox>
    </FormControl>
    <FormMessage />
  </FormItem>
</FormField>
```

**Input Specifications:**

| Property | Value |
|----------|-------|
| Height | 44px (touch-friendly) |
| Padding X | 12px |
| Border Radius | 8px |
| Border | 1px neutral-300 |
| Focus Ring | 2px offset, primary-500 |
| Transition | 150ms border-color |

### 6.5 Data Table

```vue
<template>
  <div class="rounded-xl border overflow-hidden">
    <Table>
      <TableHeader>
        <TableRow class="bg-neutral-50 dark:bg-neutral-900">
          <TableHead class="w-[50px]">
            <Checkbox 
              :checked="allSelected" 
              @update:checked="toggleAll" 
            />
          </TableHead>
          <TableHead 
            v-for="column in columns" 
            :key="column.key"
            :class="{ 'cursor-pointer hover:bg-neutral-100': column.sortable }"
            @click="column.sortable && sort(column.key)"
          >
            <div class="flex items-center gap-2">
              {{ column.label }}
              <SortIcon 
                v-if="column.sortable" 
                :direction="sortBy === column.key ? sortDir : null" 
              />
            </div>
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow 
          v-for="row in data" 
          :key="row.id"
          class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50
                 transition-colors duration-100"
        >
          <!-- Row content -->
        </TableRow>
      </TableBody>
    </Table>
    
    <!-- Pagination -->
    <div class="flex items-center justify-between px-4 py-3 border-t">
      <span class="text-sm text-neutral-500">
        Showing {{ from }}-{{ to }} of {{ total }}
      </span>
      <Pagination 
        :current="page" 
        :total="totalPages" 
        @change="setPage" 
      />
    </div>
  </div>
</template>
```

---

## 7. Animation System

### Animation Tokens

```css
/* Base animation classes */
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fade-out {
  from { opacity: 1; }
  to { opacity: 0; }
}

@keyframes slide-in-from-top {
  from { transform: translateY(-10px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@keyframes slide-in-from-bottom {
  from { transform: translateY(10px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@keyframes slide-in-from-left {
  from { transform: translateX(-10px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes slide-in-from-right {
  from { transform: translateX(10px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes scale-in {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

@keyframes scale-out {
  from { transform: scale(1); opacity: 1; }
  to { transform: scale(0.95); opacity: 0; }
}

/* Shimmer for loading skeletons */
@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.skeleton {
  background: linear-gradient(
    90deg,
    var(--color-neutral-200) 0%,
    var(--color-neutral-100) 50%,
    var(--color-neutral-200) 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s ease-in-out infinite;
}
```

### Vue Transition Classes

```vue
<!-- Page Transitions -->
<template>
  <Transition name="page" mode="out-in">
    <slot />
  </Transition>
</template>

<style>
.page-enter-active,
.page-leave-active {
  transition: opacity 200ms ease, transform 200ms ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
```

```vue
<!-- List Stagger Animation -->
<template>
  <TransitionGroup 
    name="list" 
    tag="div"
    class="grid gap-4"
  >
    <TaskCard 
      v-for="(task, index) in tasks" 
      :key="task.id"
      :style="{ '--delay': `${index * 50}ms` }"
    />
  </TransitionGroup>
</template>

<style>
.list-enter-active {
  transition: opacity 300ms ease, transform 300ms ease;
  transition-delay: var(--delay, 0ms);
}

.list-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.list-leave-active {
  transition: opacity 200ms ease, transform 200ms ease;
  position: absolute;
}

.list-leave-to {
  opacity: 0;
  transform: scale(0.9);
}

.list-move {
  transition: transform 300ms ease;
}
</style>
```

### Micro-Interaction Guidelines

| Element | Trigger | Animation | Duration |
|---------|---------|-----------|----------|
| Button hover | mouseenter | scale(1.02), shadow | 150ms |
| Button press | mousedown | scale(0.98) | 100ms |
| Card hover | mouseenter | translateY(-2px), shadow-lg | 200ms |
| Checkbox | change | scale bounce | 200ms + spring |
| Toggle | change | slide + color | 200ms |
| Dropdown open | click | fade + slide down | 150ms |
| Modal open | trigger | fade + scale | 200ms |
| Toast enter | show | slide from right | 300ms |
| Skeleton | loading | shimmer | 1.5s infinite |

---

## 8. Responsive Patterns

### Breakpoint Behavior

| Breakpoint | Navigation | Sidebar | Columns | Cards |
|------------|------------|---------|---------|-------|
| xs (<640px) | Bottom nav | Hidden | 1 | Full width |
| sm (640px) | Bottom nav | Hidden | 2 | 2-up |
| md (768px) | Header | Overlay | 2-3 | 2-up |
| lg (1024px) | Header | Visible (collapsible) | 3-4 | 3-up |
| xl (1280px) | Header | Visible (expanded) | 4-6 | 4-up |
| 2xl (1536px) | Header | Visible | 4-6 | 4-up |

### Component Responsive Examples

```vue
<!-- Responsive Grid -->
<div class="grid gap-4
            grid-cols-1 
            sm:grid-cols-2 
            lg:grid-cols-3 
            xl:grid-cols-4">
  <TaskCard v-for="task in tasks" :key="task.id" :task="task" />
</div>

<!-- Responsive Table → Cards -->
<div class="hidden md:block">
  <DataTable :data="data" :columns="columns" />
</div>
<div class="md:hidden space-y-3">
  <MobileCard v-for="item in data" :key="item.id" :item="item" />
</div>

<!-- Responsive Form Layout -->
<div class="grid gap-4 grid-cols-1 md:grid-cols-2">
  <FormField name="firstName" />
  <FormField name="lastName" />
</div>
<div class="grid gap-4 grid-cols-1">
  <FormField name="email" />
</div>
```

### Touch Zones

```
┌─────────────────────────────────────────────┐
│                SAFE ZONE                     │  < Header (reachable)
├─────────────────────────────────────────────┤
│                                              │
│           SECONDARY ACTIONS                  │  < Requires stretch
│                                              │
├─────────────────────────────────────────────┤
│                                              │
│                                              │
│            NATURAL ZONE                      │  < One-handed comfort
│         (Primary Actions Here)               │
│                                              │
│                                              │
├─────────────────────────────────────────────┤
│          BOTTOM NAVIGATION                   │  < Most accessible
└─────────────────────────────────────────────┘
```

---

## 9. RTL Support

### RTL Switching

```typescript
// composables/useDirection.ts
export function useDirection() {
  const { locale } = useI18n()
  
  const isRtl = computed(() => ['fa', 'ar', 'he'].includes(locale.value))
  const dir = computed(() => isRtl.value ? 'rtl' : 'ltr')
  
  // Apply to document
  watch(dir, (newDir) => {
    document.documentElement.dir = newDir
    document.documentElement.lang = locale.value
  }, { immediate: true })
  
  return { isRtl, dir }
}
```

### RTL Tailwind Utilities

```css
/* Use logical properties */
.margin-start { margin-inline-start: var(--space-4); }
.margin-end { margin-inline-end: var(--space-4); }
.padding-start { padding-inline-start: var(--space-4); }
.padding-end { padding-inline-end: var(--space-4); }

/* RTL-aware flexbox */
[dir="rtl"] .flex-row { flex-direction: row-reverse; }

/* RTL icon flipping */
[dir="rtl"] .icon-directional {
  transform: scaleX(-1);
}
```

### RTL Component Considerations

| Component | RTL Behavior |
|-----------|-------------|
| Sidebar | Moves to right side |
| Progress bars | Fill from right |
| Breadcrumbs | Reverse order |
| Icons (arrows) | Flip horizontally |
| Tables | Columns right-to-left |
| Carousel | Swipe direction reversed |
| Pagination | Arrows flipped |

---

## 10. Accessibility

### WCAG 2.1 AA Checklist

#### Perceivable
- [ ] Color contrast 4.5:1 (text), 3:1 (large text)
- [ ] Color not sole indicator
- [ ] Text resizable to 200%
- [ ] Images have alt text
- [ ] Videos have captions

#### Operable
- [ ] All functionality keyboard accessible
- [ ] Focus visible on all elements
- [ ] Skip links provided
- [ ] No keyboard traps
- [ ] Sufficient time for interactions

#### Understandable
- [ ] Language declared
- [ ] Consistent navigation
- [ ] Error identification
- [ ] Labels for inputs
- [ ] Error prevention for important actions

#### Robust
- [ ] Valid HTML
- [ ] ARIA used correctly
- [ ] Compatible with assistive tech

### Focus States

```css
/* Custom focus ring */
.focus-ring {
  outline: none;
}

.focus-ring:focus-visible {
  outline: 2px solid var(--color-primary-500);
  outline-offset: 2px;
  border-radius: var(--radius-md);
}

/* Ensure visible focus in high contrast */
@media (prefers-contrast: high) {
  .focus-ring:focus-visible {
    outline: 3px solid currentColor;
    outline-offset: 3px;
  }
}
```

### Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  
  .skeleton {
    animation: none;
    background: var(--color-neutral-200);
  }
}
```

---

## 11. Dark Mode

### Implementation

```typescript
// composables/useTheme.ts
export function useTheme() {
  const theme = useState<'light' | 'dark' | 'system'>('theme', () => 'system')
  
  const isDark = computed(() => {
    if (theme.value === 'system') {
      return window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    return theme.value === 'dark'
  })
  
  // Apply theme class
  watch(isDark, (dark) => {
    document.documentElement.classList.toggle('dark', dark)
  }, { immediate: true })
  
  // Persist preference
  const setTheme = (value: 'light' | 'dark' | 'system') => {
    theme.value = value
    localStorage.setItem('theme', value)
  }
  
  return { theme, isDark, setTheme }
}
```

### Dark Mode Component Patterns

```vue
<!-- Example: Dark mode aware card -->
<Card class="bg-white dark:bg-neutral-900 
             border-neutral-200 dark:border-neutral-800
             shadow-sm dark:shadow-neutral-950/50">
  <CardContent class="text-neutral-900 dark:text-neutral-100">
    <!-- Content automatically adapts -->
  </CardContent>
</Card>
```

---

## 12. Iconography

### Icon System: Lucide Icons

```vue
<!-- Icon usage -->
<script setup>
import { Calendar, CheckCircle, AlertTriangle, User } from 'lucide-vue-next'
</script>

<template>
  <Calendar class="w-5 h-5 text-neutral-500" />
  <CheckCircle class="w-5 h-5 text-success-500" />
  <AlertTriangle class="w-5 h-5 text-warning-500" />
</template>

<!-- Or via Nuxt Icon module -->
<Icon name="lucide:calendar" class="w-5 h-5" />
```

### Icon Size Guidelines

| Context | Size | Class |
|---------|------|-------|
| Inline (text) | 16px | w-4 h-4 |
| Button icon | 16-18px | w-4 h-4 |
| Nav item | 20px | w-5 h-5 |
| Feature icon | 24px | w-6 h-6 |
| Hero/empty state | 48-64px | w-12 h-12 |

---

## 13. Page Templates

### Dashboard Template

```vue
<template>
  <div class="space-y-6 p-4 md:p-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold">Dashboard</h1>
        <p class="text-neutral-500">Welcome back, {{ user.name }}</p>
      </div>
      <div class="flex gap-2">
        <Button variant="outline">Export</Button>
        <Button>New Task</Button>
      </div>
    </div>
    
    <!-- KPI Cards -->
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
      <KpiCard
        v-for="kpi in kpis"
        :key="kpi.id"
        :title="kpi.title"
        :value="kpi.value"
        :change="kpi.change"
        :icon="kpi.icon"
      />
    </div>
    
    <!-- Charts Row -->
    <div class="grid gap-6 grid-cols-1 lg:grid-cols-2">
      <Card>
        <CardHeader>
          <CardTitle>Task Completion</CardTitle>
        </CardHeader>
        <CardContent>
          <ApexChart type="area" :options="chartOptions" :series="chartSeries" />
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader>
          <CardTitle>Department Workload</CardTitle>
        </CardHeader>
        <CardContent>
          <ApexChart type="bar" :options="barOptions" :series="barSeries" />
        </CardContent>
      </Card>
    </div>
    
    <!-- Recent Activity -->
    <Card>
      <CardHeader>
        <CardTitle>Recent Tasks</CardTitle>
        <Button variant="ghost" size="sm">View All</Button>
      </CardHeader>
      <CardContent>
        <TaskList :tasks="recentTasks" compact />
      </CardContent>
    </Card>
  </div>
</template>
```

### List Page Template

```vue
<template>
  <div class="space-y-4 p-4 md:p-6">
    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <h1 class="text-2xl font-semibold">Tasks</h1>
      <Button @click="createTask">
        <Plus class="w-4 h-4 mr-2" />
        New Task
      </Button>
    </div>
    
    <!-- Filters Bar -->
    <div class="flex flex-wrap gap-2 p-3 bg-neutral-50 dark:bg-neutral-900 rounded-lg">
      <Input 
        v-model="search" 
        placeholder="Search tasks..." 
        class="max-w-xs"
      >
        <template #prefix>
          <Search class="w-4 h-4 text-neutral-400" />
        </template>
      </Input>
      
      <Select v-model="status" placeholder="Status">
        <SelectItem value="all">All</SelectItem>
        <SelectItem value="in_progress">In Progress</SelectItem>
        <SelectItem value="completed">Completed</SelectItem>
      </Select>
      
      <Select v-model="priority" placeholder="Priority">
        <SelectItem value="all">All</SelectItem>
        <SelectItem value="critical">Critical</SelectItem>
        <SelectItem value="high">High</SelectItem>
      </Select>
      
      <Button variant="ghost" @click="clearFilters">Clear</Button>
    </div>
    
    <!-- View Toggle -->
    <div class="flex justify-end">
      <ToggleGroup v-model="viewMode" type="single">
        <ToggleGroupItem value="grid">
          <LayoutGrid class="w-4 h-4" />
        </ToggleGroupItem>
        <ToggleGroupItem value="list">
          <List class="w-4 h-4" />
        </ToggleGroupItem>
      </ToggleGroup>
    </div>
    
    <!-- Content -->
    <div v-if="loading">
      <LoadingSkeleton />
    </div>
    
    <EmptyState v-else-if="!tasks.length" title="No tasks found">
      <template #action>
        <Button @click="createTask">Create your first task</Button>
      </template>
    </EmptyState>
    
    <div v-else-if="viewMode === 'grid'" class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
      <TaskCard v-for="task in tasks" :key="task.id" :task="task" />
    </div>
    
    <DataTable v-else :data="tasks" :columns="columns" />
    
    <!-- Pagination -->
    <Pagination 
      v-model:page="page" 
      :total="totalPages" 
      :sibling-count="1"
    />
  </div>
</template>
```

---

## Summary Checklist

### Before Implementing Any Component:

- [ ] Mobile layout designed first
- [ ] Touch targets ≥ 44px
- [ ] Keyboard navigation planned
- [ ] Focus states defined
- [ ] Loading states designed
- [ ] Empty states designed
- [ ] Error states designed
- [ ] RTL behavior defined
- [ ] Dark mode colors set
- [ ] Animations respect reduced-motion

### Performance Checklist:

- [ ] Images optimized (WebP/AVIF)
- [ ] Icons from Lucide (tree-shakeable)
- [ ] Heavy components lazy-loaded
- [ ] Virtual scrolling for long lists
- [ ] Debounced search inputs
- [ ] Skeleton loaders, not spinners

---

*This design system is a living document. Update as patterns evolve.*
