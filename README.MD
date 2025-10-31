# BJA NSVSP WordPress Theme

A **custom WordPress theme** built for the **National Survey of Victim Service Providers (NSVSP)** website — designed and developed by **Jameel Evans**.  
This theme is optimized for accessibility, modular development, and modern performance using **Gulp**, **Webpack**, and **SASS**.

---

## 🧩 Overview

The **BJA NSVSP theme** powers a hybrid single-page + dynamic content experience for the NSVSP website.  
It combines static front-page sections (like Resources, Dictionary, FAQs, and Contact) with WordPress-driven custom post types and dynamic templates.

Built with flexibility in mind, it’s ideal for government or research-oriented sites that need both **clean UX** and **structured data-driven content**.

---

## ⚙️ Features

### 🧱 General

- Modern **custom WordPress theme** (no parent dependency)
- Fully compatible with **WordPress 6+**
- Uses **ACF Pro** for dynamic content fields
- Built-in **Custom Post Types (CPTs)**:
  - **Resources** – replaces the default “Posts”
  - **Dictionary** – alphabetically grouped definitions
  - **FAQs** – accessible, collapsible accordions
- Native **Category taxonomy** integration for all post types
- Fully responsive layout (mobile-first, grid + flex based)

### 🎨 Front-End Features

- **Dynamic Anchored Navigation**:
  - Smooth scroll & active link highlighting
  - Sticky sidebar navigation
- **Animated Accordions** for FAQs with full ARIA accessibility
- **Resources Pagination** (AJAX-style, no full page reload)
- **Dynamic Downloads Section**:
  - Displays up to four resource files via ACF
  - Automatically suggests the newest downloads when none exist
- **Contact Section Highlight Animation** on navigation click
- **Sticky Back to Top Button** with smooth visibility animation
- **Search Integration** across all post types with pagination
- Built-in **SVG icon system** for lightweight vector rendering

### 💻 Developer Features

- **Gulp 5 + Webpack** build system with:
  - Automatic SASS → CSS compilation
  - Autoprefixer + minification
  - ES6+ modular JavaScript support
  - BrowserSync for live reloading
- **SCSS Architecture** based on components and utilities:
  assets/css/
  ├── abstracts/ # variables, mixins, functions
  ├── base/ # typography, reset
  ├── components/ # buttons, pagination, forms, etc.
  ├── layout/ # header, footer, grid, navigation
  ├── pages/ # unique page styles
  ├── themes/ # color palettes
  └── main.scss

- **Modular JS Structure**:
  Each component (Pagination, Accordion, ScrollSpy, etc.) has its own ES module in `assets/js/modules/` and is imported into `scripts.js`.

---

## 🚀 Installation

1. Clone or download this repository into your WordPress themes directory:

```bash
cd wp-content/themes
git clone https://github.com/jameelevans/bjansvsp.git
```
