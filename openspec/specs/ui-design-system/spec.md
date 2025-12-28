# UI/UX Design System Specification

## Purpose

The UI/UX Design System defines the visual language, component library, animation guidelines, and responsive behavior for IshYar's Apple-inspired interface.

## Requirements

### Requirement: Design Tokens
The system SHALL use a consistent token-based design system.

#### Scenario: Color palette definition
- **WHEN** colors are used in the application
- **THEN** reference semantic color tokens only
- **AND** support light/dark/system themes
- **AND** ensure WCAG AA contrast ratios
- **AND** use HSL for theme customization

#### Scenario: Typography scale
- **WHEN** text is rendered
- **THEN** use Inter/SF Pro font stack
- **AND** follow 1.25 modular scale (major third)
- **AND** use semantic size tokens (xs, sm, base, lg, xl, 2xl, 3xl, 4xl)
- **AND** maintain consistent line heights

#### Scenario: Spacing system
- **WHEN** spacing is applied
- **THEN** use 4px base unit
- **AND** follow scale: 0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16, 20, 24, 32, 40, 48, 64
- **AND** use consistent padding/margin tokens

### Requirement: Glassmorphism Effects
The system SHALL implement Apple-inspired glassmorphism for premium feel.

#### Scenario: Frosted glass backgrounds
- **WHEN** glass effect is applied
- **THEN** use backdrop-blur (8px-20px based on intensity)
- **AND** apply semi-transparent background (white/black with opacity)
- **AND** add subtle border (1px white/black at 10% opacity)
- **AND** ensure readability of content above

#### Scenario: Performance optimization
- **WHEN** rendering glass effects
- **THEN** use will-change for GPU acceleration
- **AND** disable on low-performance devices
- **AND** provide solid fallback for reduced-motion preference

### Requirement: Micro-Interactions
The system SHALL provide delightful micro-interactions throughout.

#### Scenario: Button interactions
- **WHEN** user interacts with buttons
- **THEN** apply subtle scale on hover (1.02)
- **AND** depress slightly on active (0.98)
- **AND** show ripple effect on click
- **AND** use 150ms ease-out transitions

#### Scenario: Card hover effects
- **WHEN** user hovers on interactive cards
- **THEN** elevate shadow smoothly
- **AND** optionally reveal action buttons
- **AND** subtle background shift
- **AND** use 200ms transitions

#### Scenario: Loading states
- **WHEN** content is loading
- **THEN** use skeleton screens over spinners
- **AND** apply shimmer animation
- **AND** match layout of final content
- **AND** use subtle pulse for ongoing states

### Requirement: Animation Guidelines
The system SHALL use GSAP for complex animations with consistent timing.

#### Scenario: Page transitions
- **WHEN** navigating between pages
- **THEN** fade out current content (150ms)
- **AND** fade in new content (200ms)
- **AND** stagger child elements entrance
- **AND** respect reduced-motion preferences

#### Scenario: Modal/drawer animations
- **WHEN** modal or drawer opens
- **THEN** animate from trigger origin when possible
- **AND** use spring physics for natural feel
- **AND** backdrop fades in simultaneously
- **AND** content slides/scales in

#### Scenario: Data visualization animations
- **WHEN** charts/graphs render
- **THEN** animate data points entrance
- **AND** stagger bars/segments
- **AND** draw lines progressively
- **AND** use 400-600ms duration with easing

### Requirement: Responsive Design
The system SHALL be mobile-first with fluid responsiveness.

#### Scenario: Breakpoint system
- **WHEN** layout adapts to viewport
- **THEN** use breakpoints: sm (640px), md (768px), lg (1024px), xl (1280px), 2xl (1536px)
- **AND** design mobile layout first
- **AND** use container queries where beneficial

#### Scenario: Navigation adaptation
- **WHEN** viewport width changes
- **THEN** collapse sidebar to bottom nav on mobile
- **AND** convert horizontal nav to hamburger menu
- **AND** maintain consistent spacing proportions

#### Scenario: Touch interactions
- **WHEN** on touch devices
- **THEN** use minimum 44x44px touch targets
- **AND** implement swipe gestures for common actions
- **AND** provide haptic feedback where supported
- **AND** avoid hover-only interactions

### Requirement: Accessibility
The system SHALL meet WCAG 2.1 AA compliance.

#### Scenario: Keyboard navigation
- **WHEN** using keyboard
- **THEN** all interactive elements are focusable
- **AND** focus order is logical
- **AND** focus indicators are visible
- **AND** skip links are provided

#### Scenario: Screen reader support
- **WHEN** using screen reader
- **THEN** semantic HTML is used throughout
- **AND** ARIA labels are provided where needed
- **AND** dynamic content changes are announced
- **AND** form errors are associated with fields

#### Scenario: Color contrast
- **WHEN** content is displayed
- **THEN** text meets 4.5:1 contrast ratio
- **AND** large text meets 3:1 ratio
- **AND** interactive elements are distinguishable
- **AND** color is not sole indicator of meaning

### Requirement: RTL (Right-to-Left) Support
The system SHALL provide complete RTL support for languages like Persian, Arabic, and Hebrew.

#### Scenario: Automatic direction detection
- **WHEN** user selects an RTL language
- **THEN** set `dir="rtl"` on document root
- **AND** switch `lang` attribute accordingly
- **AND** mirror entire layout horizontally
- **AND** persist direction preference

#### Scenario: Text alignment and flow
- **WHEN** rendering RTL content
- **THEN** text aligns to the right by default
- **AND** text flows right-to-left
- **AND** lists start from right
- **AND** form labels align right

#### Scenario: Layout mirroring
- **WHEN** RTL mode is active
- **THEN** sidebar moves to right side
- **AND** navigation icons flip horizontally
- **AND** breadcrumbs reverse order
- **AND** progress bars fill from right
- **AND** sliders operate in reverse
- **AND** pagination reverses order

#### Scenario: Spacing and margins
- **WHEN** applying directional spacing in RTL
- **THEN** margin-left becomes margin-right
- **AND** padding-left becomes padding-right
- **AND** use logical properties (margin-inline-start/end)
- **AND** border-radius corners swap appropriately

#### Scenario: Icons and arrows
- **WHEN** displaying directional icons in RTL
- **THEN** mirror navigation arrows (← ↔ →)
- **AND** flip chevrons and carets
- **AND** reverse "back" and "forward" icons
- **AND** keep non-directional icons unchanged (save, delete, etc.)

#### Scenario: Tables and data grids
- **WHEN** displaying tables in RTL
- **THEN** columns render right-to-left
- **AND** sorting icons flip
- **AND** row actions appear on left
- **AND** horizontal scrolling starts from right

#### Scenario: Forms and inputs
- **WHEN** rendering forms in RTL
- **THEN** labels align right
- **AND** input text aligns right
- **AND** placeholder text aligns right
- **AND** validation messages appear on left
- **AND** submit buttons align left (action side)
- **AND** number inputs remain LTR for digits

#### Scenario: Modals and drawers
- **WHEN** opening overlays in RTL
- **THEN** drawers slide from left side
- **AND** close buttons position on left
- **AND** modal actions reverse order
- **AND** toast notifications appear on left

#### Scenario: Charts and visualizations
- **WHEN** rendering charts in RTL
- **THEN** Y-axis moves to right
- **AND** X-axis reads right-to-left
- **AND** legends align right
- **AND** bar charts grow leftward
- **AND** line charts plot right-to-left

#### Scenario: Animations in RTL
- **WHEN** animating in RTL mode
- **THEN** slide animations mirror direction
- **AND** entrance animations come from right
- **AND** exit animations go to left
- **AND** progress indicators animate rightward

#### Scenario: Mixed content handling
- **WHEN** content contains both LTR and RTL text
- **THEN** use Unicode bidirectional algorithm
- **AND** wrap LTR content in `dir="ltr"` spans
- **AND** preserve number formatting (Western digits)
- **AND** handle URLs and code snippets as LTR

#### Scenario: Print styles in RTL
- **WHEN** printing in RTL mode
- **THEN** maintain RTL layout
- **AND** mirror headers/footers
- **AND** ensure proper text flow

### Requirement: Component Library
The system SHALL use Vue Shadcn (Radix Vue) with custom styling.

#### Scenario: Button component
- **WHEN** button is rendered
- **THEN** support variants: primary, secondary, outline, ghost, destructive
- **AND** support sizes: sm, md, lg
- **AND** support loading state
- **AND** support icon-only mode

#### Scenario: Form components
- **WHEN** form inputs are rendered
- **THEN** provide consistent styling across types
- **AND** show validation states visually
- **AND** support labels, descriptions, errors
- **AND** integrate with form libraries

#### Scenario: Data display components
- **WHEN** displaying data
- **THEN** provide Card, Table, List components
- **AND** support sorting, filtering, pagination
- **AND** include empty and loading states
- **AND** support custom cell renderers

## Design Tokens

### Colors
```css
:root {
  /* Primary */
  --color-primary-50: hsl(221, 83%, 97%);
  --color-primary-100: hsl(221, 83%, 94%);
  --color-primary-500: hsl(221, 83%, 53%);
  --color-primary-600: hsl(221, 83%, 43%);
  --color-primary-700: hsl(221, 83%, 33%);
  
  /* Neutral */
  --color-gray-50: hsl(210, 20%, 98%);
  --color-gray-100: hsl(210, 20%, 96%);
  --color-gray-200: hsl(210, 16%, 93%);
  --color-gray-300: hsl(210, 14%, 83%);
  --color-gray-400: hsl(210, 12%, 63%);
  --color-gray-500: hsl(210, 10%, 43%);
  --color-gray-600: hsl(210, 12%, 33%);
  --color-gray-700: hsl(210, 14%, 23%);
  --color-gray-800: hsl(210, 16%, 13%);
  --color-gray-900: hsl(210, 20%, 8%);
  
  /* Semantic */
  --color-success: hsl(142, 76%, 36%);
  --color-warning: hsl(38, 92%, 50%);
  --color-error: hsl(0, 84%, 60%);
  --color-info: hsl(199, 89%, 48%);
  
  /* Glassmorphism */
  --glass-bg-light: hsla(0, 0%, 100%, 0.7);
  --glass-bg-dark: hsla(0, 0%, 0%, 0.5);
  --glass-border: hsla(0, 0%, 100%, 0.1);
  --glass-blur: 16px;
}
```

### Typography
```css
:root {
  /* Font Family */
  --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
  --font-mono: 'JetBrains Mono', 'SF Mono', 'Fira Code', monospace;
  
  /* Font Sizes (1.25 scale) */
  --text-xs: 0.75rem;     /* 12px */
  --text-sm: 0.875rem;    /* 14px */
  --text-base: 1rem;      /* 16px */
  --text-lg: 1.125rem;    /* 18px */
  --text-xl: 1.25rem;     /* 20px */
  --text-2xl: 1.5rem;     /* 24px */
  --text-3xl: 1.875rem;   /* 30px */
  --text-4xl: 2.25rem;    /* 36px */
  --text-5xl: 3rem;       /* 48px */
  
  /* Line Heights */
  --leading-tight: 1.25;
  --leading-snug: 1.375;
  --leading-normal: 1.5;
  --leading-relaxed: 1.625;
  
  /* Font Weights */
  --font-light: 300;
  --font-normal: 400;
  --font-medium: 500;
  --font-semibold: 600;
  --font-bold: 700;
}
```

### Spacing
```css
:root {
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
}
```

### Shadows
```css
:root {
  --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
}
```

### Animation
```css
:root {
  /* Durations */
  --duration-75: 75ms;
  --duration-100: 100ms;
  --duration-150: 150ms;
  --duration-200: 200ms;
  --duration-300: 300ms;
  --duration-500: 500ms;
  --duration-700: 700ms;
  --duration-1000: 1000ms;
  
  /* Easings */
  --ease-linear: linear;
  --ease-in: cubic-bezier(0.4, 0, 1, 1);
  --ease-out: cubic-bezier(0, 0, 0.2, 1);
  --ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-spring: cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
```

### RTL Support Tokens
```css
/* Logical Properties for RTL/LTR compatibility */
:root {
  /* Use logical properties instead of physical */
  --inline-start: left;
  --inline-end: right;
  --block-start: top;
  --block-end: bottom;
}

[dir="rtl"] {
  --inline-start: right;
  --inline-end: left;
}

/* RTL-aware spacing utilities */
.ms-auto { margin-inline-start: auto; }
.me-auto { margin-inline-end: auto; }
.ps-4 { padding-inline-start: var(--space-4); }
.pe-4 { padding-inline-end: var(--space-4); }

/* RTL-aware text alignment */
.text-start { text-align: start; }
.text-end { text-align: end; }

/* RTL-aware flexbox */
.flex-row { flex-direction: row; }
[dir="rtl"] .flex-row { flex-direction: row-reverse; }

/* RTL-aware positioning */
.start-0 { inset-inline-start: 0; }
.end-0 { inset-inline-end: 0; }

/* RTL-aware borders */
.border-s { border-inline-start-width: 1px; }
.border-e { border-inline-end-width: 1px; }
.rounded-s { border-start-start-radius: var(--radius-md); border-end-start-radius: var(--radius-md); }
.rounded-e { border-start-end-radius: var(--radius-md); border-end-end-radius: var(--radius-md); }

/* RTL-aware transforms */
[dir="rtl"] .flip-rtl { transform: scaleX(-1); }

/* RTL-aware animations */
@keyframes slide-in-start {
  from { transform: translateX(calc(-100% * var(--direction, 1))); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

[dir="ltr"] { --direction: 1; }
[dir="rtl"] { --direction: -1; }
```

### Persian/Farsi Typography
```css
/* Persian-specific font stack */
:root[lang="fa"], 
:root[dir="rtl"][lang="fa"] {
  --font-sans: 'Vazirmatn', 'IRANSans', 'Tahoma', 'Arial', sans-serif;
  --font-mono: 'Vazir Code', 'Fira Code', monospace;
  
  /* Slightly larger base for Persian readability */
  --text-base: 1.0625rem; /* 17px */
  
  /* Adjusted line heights for Persian script */
  --leading-normal: 1.7;
  --leading-relaxed: 1.8;
}

/* Persian number display (use Persian digits) */
.fa-digits {
  font-feature-settings: "ss01";
  font-variant-numeric: tabular-nums;
}

/* Force Western digits in specific contexts */
.western-digits {
  font-feature-settings: "ss01" off;
  direction: ltr;
  unicode-bidi: embed;
}
```

## Component Patterns

### Progress Ring
```vue
<template>
  <svg class="progress-ring" :width="size" :height="size">
    <circle
      class="progress-ring__background"
      :stroke-width="strokeWidth"
      fill="transparent"
      :r="radius"
      :cx="center"
      :cy="center"
    />
    <circle
      class="progress-ring__progress"
      :stroke-width="strokeWidth"
      fill="transparent"
      :r="radius"
      :cx="center"
      :cy="center"
      :stroke-dasharray="circumference"
      :stroke-dashoffset="offset"
    />
    <text 
      :x="center" 
      :y="center" 
      text-anchor="middle" 
      dominant-baseline="central"
      class="progress-ring__text"
    >
      {{ progress }}%
    </text>
  </svg>
</template>
```

### Glass Card
```vue
<template>
  <div class="glass-card">
    <slot />
  </div>
</template>

<style>
.glass-card {
  background: var(--glass-bg-light);
  backdrop-filter: blur(var(--glass-blur));
  -webkit-backdrop-filter: blur(var(--glass-blur));
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-lg);
}

@media (prefers-color-scheme: dark) {
  .glass-card {
    background: var(--glass-bg-dark);
  }
}
</style>
```

### Status Badge
```vue
<template>
  <span :class="['status-badge', `status-badge--${status}`]">
    <span class="status-badge__dot" />
    <span class="status-badge__text"><slot /></span>
  </span>
</template>
```
