# Hayabella — Abaya Store (US)

Homepage for a US abaya / modest-wear e-commerce store. It is a static site with no build step and no dependencies: plain HTML, CSS and vanilla JS.

## Two homepage versions

There are two homepage designs for the client to choose between. Both use the same photos, videos and product data.

| | `index.html`: Version 1 | `v2.html`: Version 2 |
|---|---|---|
| Mood | Warm ivory, soft and editorial | Clean white retail, Khaadi-style |
| Fonts | Cormorant Garamond + Jost | Montserrat |
| Hero | 3-slide split banners (text + photos) | 3-slide full-width banners (wide photo, collage, sale) |
| Categories | Image tiles | Round category icons with a sale badge |
| Highlights | Split banners, video section, campaign banner, occasions | Promo banners, "Watch & Shop" videos with prices, occasions, benefits row |

A small **V1 / V2** switcher sits in the bottom-left corner of both pages. Once a version is chosen, delete the `version-switch` block from the HTML and its CSS (both are marked *remove before launch*). If V2 is chosen, rename `v2.html` to `index.html`.

## Run locally

Open `index.html` in a browser, or serve the folder:

```bash
python3 -m http.server 8000   # then visit http://localhost:8000
```

## Structure

```
index.html              Homepage Version 1
v2.html                 Homepage Version 2
assets/css/styles.css   Version 1 styles (design tokens at the top)
assets/css/v2.css       Version 2 styles (design tokens at the top)
assets/js/main.js       Shared by both versions: hero slider, announcement bar, mega menu, mobile drawer,
                        search overlay, product grid + tabs, wishlist/quick-add, newsletter
assets/images/photos/   Product & campaign photography (web-optimized JPGs)
assets/videos/          Collection videos (WebM + MP4) with poster frames
```

## Homepage sections

1. Rotating announcement bar (free US shipping, returns…)
2. Sticky header with centered logo, mega menu (desktop) and slide-out drawer (mobile)
3. Hero slider with 3 banners: autoplay, progress dots, arrows, swipe, pause on hover
4. Trust strip: free US shipping, 30-day returns, ships from the USA, secure checkout
5. Shop by Category (horizontal scroll on mobile)
6. Two split feature banners
7. New Arrivals / Best Sellers product grid with hover image swap, quick add, wishlist, sale pricing (USD)
8. "Made to Move" video section: two vertical videos that autoplay muted when scrolled into view, with pause/play buttons
9. Full-width campaign banner
10. Shop by Occasion
11. Brand story
12. Instagram / lookbook grid
13. Newsletter sign-up (10% off)
14. Footer (accordion on mobile)

## Customising

- **Brand name**: "Hayabella" appears in the logo, page titles, footer and copy of `index.html` and `v2.html`.
- **Colors / fonts**: edit the CSS variables in `:root` at the top of `styles.css`.
- **Products**: edit `CATALOG` / `PRODUCTS` at the top of `main.js`. Each item takes `img` (and an optional `alt` hover image) from `assets/images/photos/`.
- **Banners**: swap the `src` of the images in `index.html`. Hero slides take two portrait photos; the background tone of each slide is set with `style="--slide-bg:…"`.
- **Videos**: vertical clips work best. Keep each under about 2 MB with no audio track, and provide both `.webm` and `.mp4` plus a poster `.jpg`. Example with ffmpeg:
  ```bash
  ffmpeg -i in.mp4 -an -vf fps=30 -c:v libx264 -crf 24 -movflags +faststart out.mp4
  ffmpeg -i out.mp4 -an -c:v libvpx-vp9 -b:v 0 -crf 36 out.webm
  ffmpeg -ss 1 -i out.mp4 -frames:v 1 out-poster.jpg
  ```
