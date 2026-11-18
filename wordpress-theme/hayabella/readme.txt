=== Hayabella ===
Contributors: Cybersia Labs
Requires at least: 6.2
Tested up to: 6.8
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce theme for Hayabella luxury abayas and modest wear.
Developed by Cybersia Labs - https://cybersialabs.com

== Installation ==

1. Install and activate WooCommerce (Plugins → Add New).
2. Appearance → Themes → Add New → Upload Theme, choose hayabella.zip, Install, Activate.
   (If the upload is refused as too large, upload the "hayabella" folder to wp-content/themes/
   with your host's File Manager or FTP, then activate it in Appearance → Themes.)
3. Settings → Permalinks: choose "Post name" and save.
4. WooCommerce → Settings → Products: set the Shop page if it is not set.

A "Wishlist" page is created when the theme is activated.

== Editing the site ==

Appearance → Customize:
* Site Identity: logo (otherwise the Hayabella wordmark is used), site icon.
* Hayabella: Brand colours: olive, blush cream, gold and text colours.
* Hayabella: Header & announcement bar: the three rotating messages, the line under the logo,
  popular searches, region label, wishlist page.
* Hayabella: Homepage: every homepage section, top to bottom. Each one can be switched off.
  - Hero slider: up to 10 slides. Type Video (background film), Photo (full-bleed photo with a
    caption bottom-left) or Banner (designed image with the text in it). Each slide has a desktop
    and an optional mobile image/video, a caption, link text and link.
  - Feature strip, three product sliders (newest, best sellers, on sale, featured or a category),
    split banners, scrolling marquee, campaign banner, occasion tiles (image or looping video),
    brand story, lookbook, newsletter, perks bar.
* Hayabella: Footer: footer logo, about text, social links, payment methods, copyright.
* Menus: Main menu (header, supports dropdowns), Mobile menu, Footer columns 1–3
  (the menu name is the column title). Add the CSS class "menu-sale" to a menu item
  (or call it "Sale") to show it in the sale colour.
* Widgets → Shop filters: add WooCommerce filter widgets (Filter Products by Attribute,
  Filter Products by Price, Product Categories) to show a filter sidebar on the shop.

Links accept a full URL, or {shop} for the shop page, e.g. {shop}?orderby=date.

== Products ==

Product cards use the product image, and the first gallery image on hover.
Each product has a "Hayabella card" box (right-hand column on the edit screen):
* Card video loop: a short silent MP4 (about 5 s, 3:4 portrait, under 1 MB) that plays on the
  card while it is on screen.
* Line above the name (e.g. "Satin | Crystal Border"); empty shows the category.
* Badge (e.g. New, Limited). Sale % and Sold out badges are automatic.

Quick Add on the cards shows the sizes of variable products whose only variation attribute is a
size (e.g. a "Size" attribute). Other variable products link to the product page.

To add the two demo products: Products → Import → choose sample-products.csv (in the repository's
wordpress-theme folder) → Run the importer. Images are downloaded from the live demo site.

== Newsletter ==

The built-in form only shows a thank-you message. To collect emails, install an email plugin
(Mailchimp for WordPress, MailPoet, Klaviyo...) and paste its form shortcode in
Customize → Hayabella: Homepage → Newsletter.

== Credits ==

Developed by Cybersia Labs (https://cybersialabs.com).
Fonts: Cormorant Garamond and Jost (Google Fonts, SIL Open Font License).
