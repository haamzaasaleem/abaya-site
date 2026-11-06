/* Hayabella — product catalog shared by the homepage and the product page.
   Replace with an API / CMS later. Images:
   - img / alt   : studio cut-outs in assets/images/products/ (cards + gallery)
   - life        : lifestyle photos in assets/images/photos/ (gallery only)
   - video       : file name in assets/videos/ (gallery only)            */
(function () {
  'use strict';

  const CARE = {
    Satin: 'Dry clean recommended. Cool iron on the reverse side. Store hanging, away from direct sunlight.',
    Chiffon: 'Hand wash cold or dry clean. Do not wring. Steam to release creases.',
    Crepe: 'Gentle machine wash cold inside out. Line dry. Warm iron on the reverse.',
    Velvet: 'Dry clean only. Steam lightly from the reverse; do not iron the pile.',
    Linen: 'Gentle machine wash cold. Line dry. Medium iron while slightly damp.',
    Nida: 'Gentle machine wash cold or hand wash. Line dry. Low iron.'
  };

  // Products go here, keyed by a short id used in links (product.html?p=<key>). Example:
  //
  //   white: {
  //     name: 'Pearl White Greek Key Abaya', tag: 'Satin | Crystal Border',
  //     img: 'white-greekkey-front', alt: 'white-greekkey-back',      // assets/images/products/*.jpg
  //     life: ['white-greekkey-studio'], video: 'white-greekkey',    // optional: photos/*.jpg, videos/*.mp4|webm
  //     price: 229, sizes: ['S', 'M', 'L', 'XL'], colors: [['Pearl White', '#F7F5F0']],
  //     desc: 'Short description shown on the product page.'
  //   },
  //
  // and add the same key to FACETS below for the collection filters.
  const CATALOG = {
    white: { name: 'Pearl White Greek Key Abaya', tag: 'Satin | Crystal Border', img: 'white-greekkey-feature', alt: 'white-greekkey-standing', life: ['white-greekkey-studio'], price: 229, sizes: ['S', 'M', 'L', 'XL'], colors: [['Pearl White', '#F7F5F0']],
      desc: 'A luminous satin abaya with a relaxed batwing silhouette, framed by a Greek key border of hand-set crystals along the placket, shoulders and cuffs. Finished with a matching chiffon shawl.' },
    plum: { name: 'Plum Velvet Crystal Abaya', tag: 'Velvet | Crystal Trim', img: 'plum-velvet-full', alt: 'plum-velvet-close', video: 'plum-velvet-abaya', price: 189, sizes: ['S', 'M', 'L', 'XL'], colors: [['Plum', '#4E2A5A']],
      desc: 'Rich plum velvet with scalloped crystal trim tracing the placket, hem and flared sleeves. A tasseled waist tie lets you define the shape for evenings and winter occasions.' }
  };

  // Filter facets for the collection page: colour family, embellishment, cut
  // key: [colour family, embellishment, style], e.g. white: ['White', 'Crystal', 'Butterfly']
  const FACETS = {
    white: ['White', 'Crystal', 'Butterfly'],
    plum: ['Purple', 'Crystal', 'Belted']
  };
  const FAMILY_HEX = { White: '#F7F5F0', Neutral: '#D8C8BC', Pink: '#E9B9B4', Red: '#6B1F2C', Purple: '#6B4A86', Blue: '#7FA6C2', Green: '#9DB0A4', Grey: '#8A8A8F', Black: '#141414' };

  Object.keys(CATALOG).forEach((key) => {
    const p = CATALOG[key];
    p.key = key;
    p.fabric = p.tag.split('|')[0].trim();
    p.care = CARE[p.fabric] || CARE.Crepe;
    [p.family, p.style, p.cut] = FACETS[key] || ['', '', ''];
  });

  // One list per product slider (matched by data-products="<key>")
  const C = CATALOG;
  // e.g. new: [{ ...C.white, badge: 'New' }], best: [{ ...C.white, sale: 199 }]
  const PRODUCTS = {
    new: [{ ...C.white, badge: 'New' }, { ...C.plum, sale: 159 }],
    best: [{ ...C.plum, sale: 159 }, { ...C.white, badge: 'Bestseller' }],
    occasion: [{ ...C.white, badge: 'Limited' }, { ...C.plum, sale: 159 }]
  };

  // Sale price / badge as shown in the sliders, so the product page matches the card
  function listing(key) {
    const all = Object.values(PRODUCTS).flat().filter((p) => p.key === key);
    return all.find((p) => p.sale) || all.find((p) => p.badge) || CATALOG[key];
  }

  window.HAYABELLA = {
    CATALOG, PRODUCTS, listing, FAMILY_HEX,
    IMG: 'assets/images/products/',
    LIFE: 'assets/images/photos/',
    VIDEO: 'assets/videos/',
    money: (n) => 'USD ' + n.toFixed(2),
    url: (key) => 'product.html?p=' + encodeURIComponent(key)
  };
})();
