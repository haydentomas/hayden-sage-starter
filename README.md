# Hayden Tomas – Custom Sage Starter Theme

A modern WordPress theme powered by **Sage (Roots)**, **Laravel Blade**, **Vite**, and **Tailwind CSS**, extended with a full **visual Customizer system**, configurable layout options, dynamic colour tokens, footer/widget systems, navigation controls, and block-ready templates.

This theme is now your **professional base theme** for all BBI and client builds. Clone it, run Composer/NPM, and you have a full modern workflow instantly.

---

## 🚀 Features

### 🧩 Modern Development Stack
- **Sage (Roots)** (dev-main)
- **Laravel Blade** templating
- **Vite 7** with instant hot reload
- **Tailwind CSS** fully configured
- Blade components, controllers, layouts
- Clean, scalable filesystem structure

### 🎨 Extensive Theme Customizer Controls  
All controls output CSS variables and update live in the preview.

---

## 🎨 Global Brand Colours
- Primary colour  
- Surface/background colour  
- Heading text colour  
- Body text colour  
- Muted body text colour  

---

## 🧭 Navigation Colours
- Top-level (parent) link colour  
- Hover/active link colour  
- Dropdown background  
- Dropdown link colour  
- Dropdown hover background  

---

## 📐 Layout Controls
- Site container max width (with live slider)
- Header layout selector:
  - Default (logo left, nav right)
  - Logo Top layout
  - Nav-center-CTA layout
- CTA button text + URL  
- Custom logo max height  

---

## 🦶 Footer Controls
- Footer background colour  
- Footer text colour  
- Footer widget column count (1–4)
- Clean footer design (no widget card backgrounds)

---

## 🧱 Widget Systems

### 🔹 Sidebar Widgets
Full styling controls:
- Sidebar widget background colour  
- Sidebar widget title colour  
- Sidebar widget text colour  
- Sidebar widget link colour  

Styled as “cards” using:
- `bg-surface-soft`
- rounded corners  
- border  
- variable-driven colours

### 🔹 Footer Widgets (Independent System)
Brand-new dedicated controls:
- Footer widget title colour  
- Footer widget text colour  
- Footer widget link colour  

Footer widgets use a **clean, non-card layout**:
- No widget background  
- No borders  
- No box styling  
- Fully independent from sidebar widget colours

---

## 🗂️ Content Card Controls  
A full colour system for blog/portfolio cards:

- Card background  
- Card heading colour  
- Card text colour  
- Card muted/excerpt colour  

---

## 🎛️ Dynamic CSS Variables

All customizer options output into:

```css
:root {
  --color-primary: ...;
  --color-surface: ...;
  --color-headings: ...;
  --color-body: ...;
  --color-body-muted: ...;

  --color-footer: ...;
  --color-footer-text: ...;

  --color-widget-bg: ...;
  --color-widget-heading: ...;
  --color-widget-text: ...;
  --color-widget-link: ...;

  --color-footer-widget-heading: ...;
  --color-footer-widget-text: ...;
  --color-footer-widget-link: ...;

  --card-bg: ...;
  --card-heading: ...;
  --card-text: ...;
  --card-text-muted: ...;

  --color-nav-link: ...;
  --color-nav-link-hover: ...;
  --color-nav-sub-bg: ...;
  --color-nav-sub-link: ...;
  --color-nav-sub-hover-bg: ...;

  --site-max-width: ...;
  --site-logo-max-height: ...;
}
```

This ensures Tailwind, components, and Blade templates all pull from a unified design system.

---

## 🧭 Navigation
- SmartMenus integration  
- Tailwind colour token overrides  
- Automatic contrast calculation for mobile toggle  
- Optional native mega-menu  
- Split-nav support depending on chosen header layout  

---

## 🧱 Block-Ready
Theme ships ready for:
- Core Gutenberg blocks  
- Widget blocks  
- Latest Comments, Latest Posts  
- Categories / Archives  
- ACF blocks (via Fancoolio or new block patterns)

---

## 🖥️ Requirements

- **PHP 8.2+ (NTS build)**  
- **Composer 2**  
- **Node.js 18+ / NPM 9+**  
- Local environment (LocalWP, Laragon, DDEV, XAMPP, etc.)

---

## 📦 Installation (New Machine)

```bash
cd wp-content/themes
git clone https://github.com/YOUR_USERNAME/hayden-sage-starter.git haydentomas
cd haydentomas
composer install
npm install
```

Add `.env`:

```
APP_URL=http://your-local-domain.test
```

Run dev mode:

```bash
npm run dev
```

---

## 🏭 Production Build

```bash
npm run build
composer install --no-dev --optimize-autoloader
```

---

## 🔄 Development Workflow

1. Pull or clone  
2. `composer install`  
3. `npm install`  
4. `npm run dev`  
5. Develop normally  
6. `npm run build` before deployment  

---

## 🚀 Deployment Guide

### 1. Build assets
```bash
npm run build
```

### 2. Install prod PHP deps
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Remove node_modules (optional)
```bash
rm -rf node_modules
```

### 4. ZIP the theme  
Include:  
- `app/`  
- `resources/`  
- `public/`  
- `vendor/`  
- PHP files  
- composer.json/lock  

Exclude:  
- node_modules  
- .git  
- .env  
- .vite  

### 5. Upload to server
Extract into:

```
wp-content/themes/
```

### 6. Final step
Go to **Permalinks → Save** to refresh rewrite rules.

---

## 📦 Optional: Full Site Migration (AIO WP Migration)

AIO Migration should be used only for:

- Database  
- Uploads  
- ACF fields  

Upload the theme manually to avoid conflicts.

---

## 🧹 .gitignore

```
vendor/
node_modules/
public/build/
.vite/
.env
```

---

## 👤 Author

**Hayden Tomas**

---

## 📝 License
MIT
