# Static Prototype Folder

Purpose: design and approve page layouts **before** they are converted into Laravel Blade views/components. This folder is throwaway — nothing here ships to production. It exists so UI/UX (spacing, color, typography, component structure, dark mode) can be iterated on quickly with zero build step, then ported 1:1 into `resources/views`.

## How it works

- Plain HTML files, styled with the **Tailwind CDN build** (`https://cdn.tailwindcss.com`) and **Alpine.js CDN** — no npm/vite needed, just open a file in a browser.
- The Tailwind config block in each page's `<head>` mirrors `tailwind.config.js` in the real app (same color tokens, same font) so the look transfers directly.
- Dark mode is implemented with Tailwind's `class` strategy (`<html class="dark">`) to match the real app's dark-mode toggle.
- Because static HTML has no partials, shared chrome (sidebar, topbar, footer) is duplicated per page for now. Treat `components/` as the source of truth for that markup — copy from there, don't hand-edit divergent copies. When a component changes, update `components/` first, then re-sync the pages that embed it.

## Folder layout

```
static_prototype_folder/
  index.html              gallery/index linking to every prototype page
  components/             canonical copies of shared chrome (sidebar, topbar, cards, buttons, empty states...)
  pages/
    auth/                 login, register, forgot-password, verify-email
    dashboard/            admin, alumni, student, faculty dashboards
    alumni/               profile, directory
    events/, jobs/, mentorship/, notices/, stories/, donations/, gallery/, documents/, feedback/
  assets/
    css/style.css         any custom CSS not expressible via Tailwind utility classes
    js/app.js             shared Alpine data/behavior (sidebar toggle, dark mode toggle, dropdowns)
    img/                  placeholder images/logos used only in prototypes
```

## Workflow per milestone

1. Sketch the page(s) for the module here as static HTML first.
2. Get it looking right (responsive, dark+light, empty/loading/error states).
3. Convert into Blade: layout → `resources/views/layouts/`, repeated chrome → `resources/views/components/`, page → `resources/views/{module}/*.blade.php`.
4. Once a page is ported to Blade and confirmed working, its static prototype is dead weight — you can leave it (as a design reference) or delete it. It is never imported by the Laravel app.

## Design tokens (kept in sync with `tailwind.config.js`)

- Primary color: `indigo` (600 light-mode accent, 500 dark-mode accent)
- Neutral surface: `slate` (50/white light background, 900/950 dark background)
- Font: Instrument Sans / system-ui fallback (Breeze default)
- Radius: `rounded-xl` for cards, `rounded-lg` for inputs/buttons
- Shadow: `shadow-sm` default, `shadow-lg` for popovers/modals
