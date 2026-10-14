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
collection.html         Collection page with filters (collection.html?fabric=Satin&sort=price-asc)
cart.html               Bag page
product.html            Single product page (product.html?p=<key>, e.g. ?p=white)
assets/css/styles.css   Design tokens (colors, fonts) at the top, then styles per section
assets/js/catalog.js    Product catalog shared by both pages (names, prices, sizes, images, descriptions)
assets/js/collection.js Collection page: filters, faceted counts, sorting, URL state
assets/js/cart.js       Bag page: line items, quantities, sizes, promo code, totals
assets/js/product.js    Product page: gallery, size/quantity, add to bag, size guide, related products
assets/js/main.js       Shared: hero slider, announcement bar, mega menu, mobile drawer,
                        search overlay, product grid + tabs, wishlist/quick-add, newsletter
assets/images/photos/   Lifestyle & campaign photography (web-optimized JPGs)
assets/images/brand/    Official logos (SVG): wordmark, HB monogram, full logo, cream footer version
assets/images/products/ Product card images: model cut out onto a plain #EEEAE5 studio background
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
12. "Made to Move" film banner: five vertical videos in a staggered row (swipeable on mobile) over a blurred backdrop, autoplaying muted when scrolled into view, with pause/play buttons
13. Brand story
14. Instagram / lookbook grid
15. Newsletter sign-up (10% off)
16. Peach benefits bar: shipping, crafted with care, customer care
17. Footer (accordion on mobile)

Each product slider has a centered title and intro line, then the products: 4 per view on desktop, 3 on tablet, 2 on mobile, with side arrows (desktop), swipe, dot pagination and a "View All" link. Product cards show a fabric line, name, USD price and a "New" / "% Off" pill, plus hover image swap, quick add and wishlist.

## Customising

- **Brand name**: "Hayabella" appears in the logo, page title, footer and copy of `index.html`.
- **Colors / fonts**: the accent is olive (`--olive`, taken from the HB logo), used for small labels, the announcement bar, New pills, the cart count, slider dots and hover states; the Our Story band uses `--sage-tint`. Edit edit the CSS variables in `:root` at the top of `styles.css`.
- **Products**: edit `CATALOG` (every product) and `PRODUCTS` (which products appear in each slider) in `assets/js/catalog.js`. Every card links to `product.html?p=<key>`. Each slider in `index.html` picks its list with `data-products="new"`, `"best"` or `"occasion"`. To add another slider, copy a slider section and add a new list to `PRODUCTS`. The fabric line under each photo comes from each product's `tag`.
- **Banners**: swap the `src` of the images in `index.html`. Hero slides take two portrait photos; the background tone of each slide is set with `style="--slide-bg:…"`.
- **Videos**: vertical clips work best. Keep each under about 2 MB with no audio track, and provide both `.webm` and `.mp4` plus a poster `.jpg`. Example with ffmpeg:
  ```bash
  ffmpeg -i in.mp4 -an -vf fps=30 -c:v libx264 -crf 24 -movflags +faststart out.mp4
  ffmpeg -i out.mp4 -an -c:v libvpx-vp9 -b:v 0 -crf 36 out.webm
  ffmpeg -ss 1 -i out.mp4 -frames:v 1 out-poster.jpg
  ```
- **Product images**: cards use `assets/images/products/`, where each photo's background was removed (rembg, BiRefNet portrait model) and the model placed on a plain `#EEEAE5` background at a consistent size. For a new product, add a studio shot on a plain background with the same name, or run the same cut-out step.
- **Brand**: colours come from the brand guide, olive `#45451B` (`--olive`) and blush cream `#F4E9E2` (`--cream` / `--sand`). Header uses `hayabella-wordmark.svg` with the tagline "Modesty – The New Bold"; footer uses the cream full logo; the HB monogram is the favicon and the watermark shown on every product image (`.product__mark`, an overlay so it stays sharp and consistent).

## Product page

`product.html?p=<key>` renders any product from `catalog.js`: breadcrumb, gallery (studio images, the product video if it has one, and lifestyle photos; thumbnails on desktop, swipe + dots on mobile), price with sale/badge, Klarna instalment line, colour, size picker (required before adding), quantity, Add to Bag, wishlist, Description / Fabric & Care / Size & Fit / Shipping & Returns accordions, a size guide pop-up, "You May Also Like" (same fabric first) and a sticky Add to Bag bar on mobile. The bag is kept in the visitor's browser across pages (see Bag page below).

## Collection page

`collection.html` lists every abaya with a filter sidebar (a slide-in panel on mobile): Collection (New In, Best Sellers, Occasion Edit, On Sale), Colour, Fabric, Embellishment, Style (open, closed, butterfly, kimono, belted), Size and Price. Options in one group combine with OR, groups combine with AND, and each option shows how many products it would return. Sort by featured, newest, price or name. Active filters appear as removable chips, and the page title follows the main filter (e.g. "Satin Abayas", "Sale").

Filters live in the URL, so any view can be linked or shared. Values are comma-separated:

| Param | Values |
|---|---|
| `edit` | `new`, `best`, `occasion`, `sale` |
| `color` | `White`, `Neutral`, `Pink`, `Red`, `Purple`, `Blue`, `Green`, `Grey`, `Black` |
| `fabric` | `Satin`, `Crepe`, `Chiffon`, `Linen`, `Velvet`, `Nida` |
| `style` | `Crystal`, `Embroidered`, `Embellished`, `Printed`, `Lace & Ruffle` |
| `cut` | `Open`, `Closed`, `Butterfly`, `Kimono`, `Belted` |
| `size` | `XS`, `S`, `M`, `L`, `XL` |
| `price` | `under-150`, `150-200`, `200-plus` |
| `sort` | `featured`, `new`, `price-asc`, `price-desc`, `name` |

The header menu, mega menu, footer, category tiles, hero buttons and "View All" links all point at these views. A product's colour family, embellishment and style are set in `FACETS` in `catalog.js`.

## Bag page

`cart.html` shows everything added from product pages and quick add. The bag is stored in the visitor's browser (`localStorage`, key `hb-cart`, lines of `{ key, size, qty }`) and the header count stays in sync on every page, even across tabs. "Add to bag" toasts include a "View bag" link.

- Free-shipping meter: "You're USD X away from free US shipping" (free at $150, otherwise $9.95 standard)
- Each line: image, name, fabric line, price (with sale), colour, size dropdown (merges with an existing line of the same size), quantity stepper (1–10), remove with Undo, line total
- Order summary: subtotal, discount, shipping, "tax calculated at checkout", promo code, total, Klarna instalments, Checkout button, accepted payments
- Promo codes live in `CODES` in `cart.js`; `WELCOME10` gives 10% off (matches the newsletter offer)
- Empty state with links to New In and all abayas, and a Best Sellers slider

There is no payment step yet: Checkout shows "Checkout is coming soon." It is ready to hand the lines to Shopify, Stripe Checkout or another platform.
