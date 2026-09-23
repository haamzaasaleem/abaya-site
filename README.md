# Maison Noor — Abaya Store (US)

Homepage for a US abaya / modest-wear e-commerce store. It is a static site with no build step and no dependencies: plain HTML, CSS and vanilla JS.

## Run locally

Open `index.html` in a browser, or serve the folder:

```bash
python3 -m http.server 8000   # then visit http://localhost:8000
```

## Structure

```
index.html              Homepage markup (all sections)
assets/css/styles.css   Design tokens (colors, fonts) at the top, then styles per section
assets/js/main.js       Hero slider, announcement bar, mega menu, mobile drawer,
                        search overlay, product grid + tabs, wishlist/quick-add, newsletter
assets/images/photos/   Product & campaign photography (web-optimized JPGs)
```

## Homepage sections

1. Rotating announcement bar (free US shipping, returns…)
2. Sticky header with centered logo, mega menu (desktop) and slide-out drawer (mobile)
3. Hero slider with 3 banners: autoplay, progress dots, arrows, swipe, pause on hover
4. Trust strip: free US shipping, 30-day returns, ships from the USA, secure checkout
5. Shop by Category (horizontal scroll on mobile)
6. Two split feature banners
7. New Arrivals / Best Sellers product grid with hover image swap, quick add, wishlist, sale pricing (USD)
8. Full-width campaign banner
9. Shop by Occasion
10. Brand story
11. Instagram / lookbook grid
12. Newsletter sign-up (10% off)
13. Footer (accordion on mobile)

## Customising

- **Brand name**: "Maison Noor" is a placeholder. Search and replace it in `index.html`.
- **Colors / fonts**: edit the CSS variables in `:root` at the top of `styles.css`.
- **Products**: edit `CATALOG` / `PRODUCTS` at the top of `main.js`. Each item takes `img` (and an optional `alt` hover image) from `assets/images/photos/`.
- **Banners**: swap the `src` of the images in `index.html`. Hero slides take two portrait photos; the background tone of each slide is set with `style="--slide-bg:…"`.
