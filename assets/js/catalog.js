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

  const CATALOG = {
    white: { name: 'Pearl White Greek Key Abaya', tag: 'Satin | Crystal Border', img: 'white-greekkey-feature', alt: 'white-greekkey-standing', life: ['white-greekkey-studio'], price: 229, sizes: ['S', 'M', 'L', 'XL'], colors: [['Pearl White', '#F7F5F0']],
      desc: 'A luminous satin abaya with a relaxed batwing silhouette, framed by a Greek key border of hand-set crystals along the placket, shoulders and cuffs. Finished with a matching chiffon shawl.' },
    plum: { name: 'Plum Velvet Crystal Abaya', tag: 'Velvet | Crystal Trim', img: 'plum-velvet-full', alt: 'plum-velvet-close', video: 'plum-velvet-abaya', price: 189, sizes: ['S', 'M', 'L', 'XL'], colors: [['Plum', '#4E2A5A']],
      desc: 'Rich plum velvet with scalloped crystal trim tracing the placket, hem and flared sleeves. A tasseled waist tie lets you define the shape for evenings and winter occasions.' },
    champagne: { name: 'Champagne Crystal Wave Abaya', tag: 'Satin | Crystal Embellished', img: 'champagne-wave-front', alt: 'champagne-wave-side', life: ['champagne-wave-front'], price: 229, sizes: ['S', 'M', 'L', 'XL'], colors: [['Champagne', '#D8C8BC']],
      desc: 'Liquid champagne satin cut in a generous butterfly shape, with waves of crystals cascading over the shoulders and sleeves. Effortless to wear, made to catch the light.' },
    skyblue: { name: 'Sky Blue Greek Key Abaya', tag: 'Satin | Crystal Border', img: 'skyblue-greekkey-front', alt: 'skyblue-greekkey-side', life: ['skyblue-greekkey-front', 'skyblue-greekkey-side'], price: 219, sizes: ['S', 'M', 'L', 'XL'], colors: [['Sky Blue', '#8FB3C8']],
      desc: 'Soft sky blue satin with a crystal Greek key border along the front and across the sleeves. Comes with a matching chiffon shawl.' },
    sage: { name: 'Sage Branch Embroidered Abaya', tag: 'Linen | Embroidered', img: 'sage-embroidered-front', alt: 'sage-embroidered-close', video: 'sage-embroidered-abaya', price: 159, sizes: ['XS', 'S', 'M', 'L', 'XL'], colors: [['Sage', '#B9C3C4']],
      desc: 'A tailored open abaya in breathable sage linen, with a soft lapel and white branch embroidery climbing the front. Layer it over white for an easy day look.' },
    blush: { name: 'Blush Floral Belted Abaya', tag: 'Crepe | Printed', img: 'blush-floral-front', alt: 'blush-floral-garden', life: ['blush-floral-front', 'blush-floral-garden'], price: 145, sizes: ['XS', 'S', 'M', 'L', 'XL'], colors: [['Blush', '#F2C4C0']],
      desc: 'A painterly floral print on soft blush crepe, with chiffon-trimmed sleeves and a matching waist belt. Light enough for spring, pretty enough for celebrations.' },
    stripe: { name: 'Riviera Stripe Kimono Abaya', tag: 'Crepe | Printed', img: 'stripe-kimono-front', life: ['stripe-kimono-front'], price: 135, sizes: ['S', 'M', 'L', 'XL'], colors: [['Cobalt / White', '#2A3FB0']],
      desc: 'Bold cobalt and white stripes on a relaxed kimono abaya, worn over a white inner. Belted at the waist for shape.' },
    navy: { name: 'Midnight Embellished Open Abaya', tag: 'Crepe | Hand Embellished', img: 'navy-open-hooded', alt: 'navy-open-full', life: ['navy-open-hooded', 'navy-open-full'], price: 169, sizes: ['S', 'M', 'L', 'XL'], colors: [['Midnight', '#1F2740']],
      desc: 'An open abaya in deep midnight crepe, with a hand-embellished placket of pearls and sequins. Styled with a soft grey shawl.' },
    blackrose: { name: 'Black Rose Print Kimono Abaya', tag: 'Crepe | Printed', img: 'blackrose-print-front', life: ['blackrose-print-front'], price: 139, sizes: ['S', 'M', 'L', 'XL'], colors: [['Black Rose', '#1A1A1A']],
      desc: 'Vivid painted roses on black crepe, in an easy kimono cut with solid black cuffs and a tie belt.' },
    rose: { name: 'Dusty Rose Crystal Abaya', tag: 'Crepe | Crystal Embellished', img: 'rose-crystal-close', life: ['rose-crystal-close'], price: 199, sizes: ['XS', 'S', 'M', 'L', 'XL'], colors: [['Dusty Rose', '#B98A7C']],
      desc: 'Dusty rose crepe with lines of golden crystals and diamond motifs running from shoulder to hem, finished with a front zip.' },
    ivory: { name: 'Ivory Pearl Embroidered Abaya', tag: 'Crepe | Embroidered', img: 'ivory-pearl-front', alt: 'ivory-pearl-pose', life: ['ivory-pearl-front', 'ivory-pearl-pose'], price: 219, sizes: ['S', 'M', 'L', 'XL'], colors: [['Ivory', '#F1EAD8']],
      desc: 'Soft ivory crepe with blush thread-work and crystal detailing along the sleeves and side panels, paired with a blush chiffon shawl.' },
    mauve: { name: 'Mauve Ruffle Cuff Abaya', tag: 'Satin | Ruffle Cuff', img: 'mauve-ruffle', life: ['mauve-ruffle'], price: 139, sizes: ['XS', 'S', 'M', 'L', 'XL'], colors: [['Mauve', '#8E6F80']],
      desc: 'A fluid mauve satin abaya finished with black lace ruffle cuffs and a covered-button front.' },
    charcoal: { name: 'Charcoal Embroidered Bell Sleeve Abaya', tag: 'Nida | Embroidered', img: 'charcoal-cuff-wide', life: ['charcoal-cuff-wide'], price: 159, sizes: ['S', 'M', 'L'], colors: [['Charcoal', '#3A3A3F']],
      desc: 'Charcoal nida with wide bell sleeves, edged in tonal floral embroidery and beading.' },
    beige: { name: 'Sand Cutwork Belted Abaya', tag: 'Crepe | Embellished', img: 'beige-cutwork-front', life: ['beige-cutwork-front'], price: 209, sizes: ['S', 'M', 'L', 'XL'], colors: [['Sand', '#D4B49A']],
      desc: 'Sand crepe with copper cutwork embellishment along the front and cuffs, cinched with a braided tassel belt.' },
    burgundy: { name: 'Burgundy Petal Sleeve Abaya', tag: 'Satin | Petal Sleeve', img: 'burgundy-petal-full', alt: 'burgundy-petal-close', video: 'burgundy-petal-abaya', life: ['burgundy-petal-close'], price: 179, sizes: ['XS', 'S', 'M', 'L'], colors: [['Burgundy', '#5B1E2A']],
      desc: 'Deep burgundy satin with sculpted petal cuffs and ruffle detailing down the sides, layered with a matching chiffon shawl.' },
    violet: { name: 'Violet Tiered Satin Abaya', tag: 'Satin | Tiered', img: 'violet-tiered-full', alt: 'violet-tiered-close', video: 'violet-tiered-abaya', price: 165, sizes: ['S', 'M', 'L', 'XL'], colors: [['Violet', '#3E1A6B']],
      desc: 'Violet satin with a tiered skirt, crystal seam detailing and a self-tie belt, worn with a sheer chiffon shawl.' },
    lilac: { name: 'Lilac Lace Trim Abaya', tag: 'Chiffon | Lace Trim', img: 'lilac-lace-full', alt: 'lilac-lace-close', video: 'lilac-lace-abaya', price: 149, sizes: ['XS', 'S', 'M', 'L', 'XL'], colors: [['Lilac', '#B88AC4']],
      desc: 'Airy lilac chiffon with a bold black lace placket and flowing butterfly sleeves.' },
    noir: { name: 'Noir Crystal Chiffon Abaya', tag: 'Chiffon | Crystal Embellished', img: 'noir-crystal-front', alt: 'noir-crystal-back', life: ['noir-crystal-front', 'noir-crystal-back'], price: 189, sizes: ['XS', 'S', 'M', 'L', 'XL'], colors: [['Noir', '#141414']],
      desc: 'Classic black chiffon with a rhinestone placket and a geometric crystal panel across the back.' }
  };

  // Filter facets for the collection page: colour family, embellishment, cut
  const FACETS = {
    white: ['White', 'Crystal', 'Butterfly'], plum: ['Purple', 'Crystal', 'Belted'], champagne: ['Neutral', 'Crystal', 'Butterfly'],
    skyblue: ['Blue', 'Crystal', 'Butterfly'], sage: ['Green', 'Embroidered', 'Open'], blush: ['Pink', 'Printed', 'Belted'],
    stripe: ['Blue', 'Printed', 'Kimono'], navy: ['Blue', 'Embellished', 'Open'], blackrose: ['Black', 'Printed', 'Kimono'],
    rose: ['Pink', 'Crystal', 'Closed'], ivory: ['White', 'Embroidered', 'Closed'], mauve: ['Purple', 'Lace & Ruffle', 'Closed'],
    charcoal: ['Grey', 'Embroidered', 'Closed'], beige: ['Neutral', 'Embellished', 'Belted'], burgundy: ['Red', 'Lace & Ruffle', 'Closed'],
    violet: ['Purple', 'Crystal', 'Belted'], lilac: ['Purple', 'Lace & Ruffle', 'Butterfly'], noir: ['Black', 'Crystal', 'Open']
  };
  const FAMILY_HEX = { White: '#F7F5F0', Neutral: '#D8C8BC', Pink: '#E9B9B4', Red: '#6B1F2C', Purple: '#6B4A86', Blue: '#7FA6C2', Green: '#9DB0A4', Grey: '#8A8A8F', Black: '#141414' };

  Object.keys(CATALOG).forEach((key) => {
    const p = CATALOG[key];
    p.key = key;
    p.fabric = p.tag.split('|')[0].trim();
    p.care = CARE[p.fabric] || CARE.Crepe;
    [p.family, p.style, p.cut] = FACETS[key];
  });

  // One list per product slider (matched by data-products="<key>")
  const C = CATALOG;
  const PRODUCTS = {
    new: [
      { ...C.white, badge: 'New' }, { ...C.plum, badge: 'New' }, { ...C.champagne, badge: 'New' }, { ...C.skyblue, badge: 'New' },
      { ...C.sage, badge: 'New' }, { ...C.blush, badge: 'New' }, { ...C.stripe, badge: 'New' }, { ...C.navy, badge: 'New' }
    ],
    best: [
      { ...C.blackrose, badge: 'Bestseller' }, { ...C.rose, badge: 'Bestseller' }, { ...C.ivory, badge: 'Bestseller' }, { ...C.stripe, sale: 115 },
      { ...C.mauve, sale: 119 }, { ...C.navy }, { ...C.blush }, { ...C.charcoal, sale: 129 }
    ],
    occasion: [
      { ...C.white, badge: 'Limited' }, { ...C.plum }, { ...C.beige }, { ...C.champagne }, { ...C.skyblue },
      { ...C.burgundy }, { ...C.violet }, { ...C.noir }, { ...C.ivory, badge: 'Limited' }
    ]
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
