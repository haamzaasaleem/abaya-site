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
wishlist.html           Wishlist page
product.html            Single product page (product.html?p=<key>, e.g. ?p=white)
assets/css/styles.css   Design tokens (colors, fonts) at the top, then styles per section
assets/js/catalog.js    Product catalog shared by both pages (names, prices, sizes, images, descriptions)
assets/js/collection.js Collection page: filters, faceted counts, sorting, URL state
assets/js/wishlist.js   Wishlist page: saved pieces, add to bag with size, remove
assets/js/cart.js       Bag page: line items, quantities, sizes, promo code, totals
assets/js/product.js    Product page: gallery, size/quantity, add to bag, size guide, related products
assets/js/main.js       Shared: hero slider, announcement bar, mega menu, mobile drawer,
                        search overlay, product grid + tabs, wishlist/quick-add, newsletter
assets/images/banners/  Hero slider banners (desktop + mobile per slide)
assets/images/photos/   Lifestyle & campaign photography (web-optimized JPGs)
assets/images/brand/    Official logos (SVG): wordmark, HB monogram, full logo, cream footer version
assets/images/products/ Product card images: model cut out onto a plain #EEEAE5 studio background
assets/videos/          Collection videos (WebM + MP4) with poster frames
```

> **Catalog is currently empty.** There are no products on the site: the homepage product sliders hide themselves, the collection page shows a "coming soon" message, product links show "no longer available", and the bag empties. Add products to `CATALOG` in `assets/js/catalog.js` (there is a commented example) and everything appears again.

## Homepage sections

1. Rotating announcement bar (free US shipping, returns…)
2. Sticky header with centered logo, mega menu (desktop) and slide-out drawer (mobile)
3. Hero slider of banner images (desktop + mobile version per slide): autoplay, progress dots, arrows, swipe, pause on hover; each banner links to its collection
4. Trust strip: free US shipping, 30-day returns, ships from the USA, secure checkout
5. **Product slider: New Arrivals**
6. Two split feature banners
7. **Product slider: Best Sellers**
8. Full-width campaign banner
9. Shop by Occasion
10. **Product slider: The Occasion Edit**
11. Brand story
12. Instagram / lookbook grid
13. Newsletter sign-up (10% off)
14. Peach benefits bar: shipping, crafted with care, customer care
15. Footer (accordion on mobile)

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

## Hero banners

The homepage slider shows designed banner images, like most fashion stores: any text, buttons or offers are part of the image itself. Each slide needs two JPGs in `assets/images/banners/`:

| Version | Size | Shown on |
|---|---|---|
| `<name>-desktop.jpg` | 1920 × 800 px | tablets and desktops (wider than 767 px) |
| `<name>-mobile.jpg` | 1080 × 1350 px | phones |

Keep important text and faces away from the very bottom centre (the slide dots sit there) and inside the middle 1440 px of the desktop version, since very wide or short screens trim the edges slightly. Export as JPG at about 80–85% quality, ideally under 300 KB each.

To swap a banner, replace its two files with the same names. To add, remove or reorder slides, edit the `<a class="hero__slide">` blocks in `index.html`: each has the link (`href`), the two image paths and an `aria-label` describing the banner for screen readers. The three current banners (The Noir Edit, Berry Luxe, The Pearl Collection) are starter images made from the previous slides.

**Hero slider.** The hero fills the window below the header on desktop (4:5 on phones). On each change the current slide drops to black (0.15 s), black holds briefly (0.25 s), then the next slide fades up while settling from 120% to 100% size (0.8 s) and stays still; its caption (spaced uppercase title and an underlined "Shop now") fades in at the bottom-left; small dots sit bottom-right. Slides in order: the campaign film, four full-bleed photo slides (`hero__slide--photo`, images in `assets/images/slides/`: desktop 1920x800, mobile 864x1080, taken from the campaign film), then the three designed banners (`hero__slide--banner`, text in the image). To add a photo slide, copy one of the `hero__slide--photo` blocks in `index.html` and change its two images, caption and link; landscape photos of at least 1920 px wide work best on desktop.

**Video slide.** The first slide (`data-video-slide`) is a video background with live text ("Made to Move", Shop New In): one 16.6 s campaign film merged from 8 of the photoshoot clips (each shot cut and cropped around the model's face, so faces stay in frame on both cuts; the slide's `data-duration` matches the film length), colour-graded per clip (white balance, levels, brighter faces, light sharpening), with a wide cut for desktop (`hero-film-desktop`, 1920x800, about 6 MB) and a portrait cut for phones (`hero-film-mobile`, 864x1080, about 3.5 MB), in MP4 and WebM with a poster JPG in `assets/videos/`. Only the version for the screen size plays; it plays muted only while that slide is showing, has a pause button, and does not autoplay for visitors who prefer reduced motion.

**Live cards.** Product cards play a short silent loop when the product has a `clip` in `assets/js/catalog.js` (files in `assets/videos/cards/`: `<name>.mp4`, `<name>.webm`, `<name>-poster.jpg`; 3:4, about 5 s, ideally under 500 KB). Clips play only while the card is on screen and pause when scrolled away. The three "Dressed for Every Occasion" tiles are loops from the campaign footage. Visitors who prefer reduced motion see still images.

**Hover zoom.** Every image card (product cards, split banners, campaign, occasion tiles, lookbook, wishlist) zooms in slowly on hover and back out slowly on leave; the amount and speed are the `--zoom` and `--zoom-in` variables at the top of `styles.css`.

**Marquee.** The scrolling olive-on-cream line under the split banners ("Modesty – The New Bold | Hayabella") is the `marquee` section in `index.html`; change the text in both groups (the second is the copy that makes the loop seamless). It pauses on hover.

**Rebuilding the film from the original clips.** `tools/make-hero-film.py` cuts the right 2.6 s from each of the 8 clips, crops them around the model's face for desktop and mobile, colour-grades each clip, merges them with cross-fades and writes all six files into `assets/videos/`. It needs only Python 3 and ffmpeg (`sudo pacman -S --needed python ffmpeg` on Manjaro/Arch). Put the original clips in one folder (they can keep their names; each is found by its number, e.g. `1000035254`) and run from the repository folder:

```bash
python3 tools/make-hero-film.py ~/Videos/hayabella-clips          # final files, about 3 minutes
python3 tools/make-hero-film.py ~/Videos/hayabella-clips --fast   # quick MP4-only test
```

The shot timings, crops and grade for each clip are in the `SHOTS` table at the top of the script.

## Wishlist

Every heart (product cards, product page) saves the piece to a wishlist kept in the visitor's browser (`localStorage`, key `hb-wishlist`, a list of product keys). Hearts stay filled on every page, the header heart shows a count and opens `wishlist.html`, and the toast offers "View wishlist". On the wishlist page each piece has a size dropdown with Add to Bag and a remove button; an empty wishlist links to New In and all abayas. It is per device until customer accounts exist.

## WordPress + WooCommerce theme

`wordpress-theme/hayabella/` is the same site as a WordPress theme for WooCommerce, developed by Cybersia Labs (https://cybersialabs.com).

- Install: zip the `hayabella` folder (or use the provided `hayabella.zip`), then Appearance → Themes → Add New → Upload Theme. WooCommerce must be active.
- Everything is edited in Appearance → Customize: brand colours, announcement bar, every hero slide (video / photo / banner, desktop and mobile), feature strip, three product sliders, split banners, marquee, campaign, occasion tiles, story, lookbook, newsletter, perks and footer. Each section can be switched off.
- Menus (header with dropdowns, mobile, three footer columns) are set in Appearance → Menus; the shop filter sidebar in Widgets → Shop filters.
- Products come from WooCommerce. Each product has a "Hayabella card" box for a card video loop, the line above the name and a badge.
- `wordpress-theme/sample-products.csv` imports the two demo abayas (Products → Import).
- Full instructions: `wordpress-theme/hayabella/readme.txt`.
