# Hayabella — Abaya Store (US)

Homepage for a US abaya / modest-wear e-commerce store. It is a static site with no build step and no dependencies: plain HTML, CSS and vanilla JS.

## Run locally

Open `index.html` in a browser, or serve the folder:

```bash
python3 -m http.server 8000   # then visit http://localhost:8000
```

## Publishing updates

`index.html` loads `styles.css?v=…` and `main.js?v=…`. After changing CSS or JS, bump that version string (both links) so browsers and hosts like GitHub Pages don't serve an old cached copy alongside the new page.

## Structure

```
index.html              Homepage markup (all sections)
assets/css/styles.css   Design tokens (colors, fonts) at the top, then styles per section
assets/js/main.js       Hero slider, announcement bar, mega menu, mobile drawer,
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
6. **Product slider: New Arrivals**
7. Two split feature banners
8. **Product slider: Best Sellers**
9. Full-width campaign banner
10. Shop by Occasion
11. **Product slider: The Occasion Edit**
12. "Made to Move" film banner: two vertical videos over a blurred backdrop, autoplaying muted when scrolled into view, with pause/play buttons
13. Brand story
14. Instagram / lookbook grid
15. Newsletter sign-up (10% off)
16. Footer (accordion on mobile)

Each product slider shows 4 products per view on desktop (3 on tablet) with arrows, a progress bar and a "View All" link. On mobile it is a swipeable row with the next card peeking in. Product cards have hover image swap, quick add, wishlist and sale pricing (USD).

## Customising

- **Brand name**: "Hayabella" appears in the logo, page title, footer and copy of `index.html`.
- **Colors / fonts**: edit the CSS variables in `:root` at the top of `styles.css`.
- **Products**: edit `CATALOG` (every product) and `PRODUCTS` (which products appear in each slider) at the top of `main.js`. Each slider in `index.html` picks its list with `data-products="new"`, `"best"` or `"occasion"`. To add another slider, copy a slider section and add a new list to `PRODUCTS`.
- **Banners**: swap the `src` of the images in `index.html`. Hero slides take two portrait photos; the background tone of each slide is set with `style="--slide-bg:…"`.
- **Videos**: vertical clips work best. Keep each under about 2 MB with no audio track, and provide both `.webm` and `.mp4` plus a poster `.jpg`. Example with ffmpeg:
  ```bash
  ffmpeg -i in.mp4 -an -vf fps=30 -c:v libx264 -crf 24 -movflags +faststart out.mp4
  ffmpeg -i out.mp4 -an -c:v libvpx-vp9 -b:v 0 -crf 36 out.webm
  ffmpeg -ss 1 -i out.mp4 -frames:v 1 out-poster.jpg
  ```
