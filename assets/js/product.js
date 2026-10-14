/* Hayabella — single product page. Reads ?p=<key> and renders from catalog.js.
   Runs before main.js so the "You may also like" slider list exists. */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const H = window.HAYABELLA;
  const { CATALOG, PRODUCTS, IMG, LIFE, VIDEO, money } = H;

  const requested = new URLSearchParams(location.search).get('p');
  const key = CATALOG[requested] ? requested : Object.keys(CATALOG)[0];
  const base = CATALOG[key];
  const p = { ...base, ...H.listing(key) };
  const price = p.sale || p.price;

  document.title = `${p.name} | Hayabella – USA`;
  const meta = $('meta[name="description"]');
  if (meta) meta.setAttribute('content', `${p.name}. ${p.desc}`);

  /* ---------- Related products: same fabric first, then the rest ---------- */
  PRODUCTS.related = Object.values(CATALOG)
    .filter((x) => x.key !== key)
    .sort((a, b) => (b.fabric === p.fabric) - (a.fabric === p.fabric))
    .slice(0, 8)
    .map((x) => H.listing(x.key));

  /* ---------- Text ---------- */
  $('[data-pdp-crumb]').textContent = p.name;
  $('[data-pdp-tag]').textContent = p.tag;
  $('[data-pdp-title]').textContent = p.name;
  $('[data-pdp-price]').innerHTML = p.sale
    ? `<s>${money(p.price)}</s><span class="now">${money(p.sale)}</span><span class="pill pill--sale">${Math.round((1 - p.sale / p.price) * 100)}% Off</span>`
    : `<span>${money(p.price)}</span>${p.badge ? `<span class="pill">${p.badge}</span>` : ''}`;
  $('[data-pdp-klarna]').textContent = `Or 4 interest-free payments of ${money(price / 4)} with Klarna`;
  $('[data-pdp-desc]').innerHTML = `<p>${p.desc}</p>`;
  $('[data-pdp-fabric]').textContent = p.fabric;
  $('[data-pdp-care]').textContent = p.care;
  $('[data-pdp-bar-name]').textContent = p.name;
  $('[data-pdp-bar-price]').textContent = money(price);

  /* ---------- Colour ---------- */
  $('[data-pdp-colour]').textContent = p.colors[0][0];
  $('[data-pdp-swatches]').innerHTML = p.colors
    .map(([n, hex], i) => `<span class="pdp__swatch${i === 0 ? ' is-active' : ''}" style="background:${hex}" title="${n}"></span>`)
    .join('');

  /* ---------- Gallery: studio shots, video, lifestyle photos ---------- */
  const media = [{ type: 'img', src: IMG + p.img + '.jpg' }];
  if (p.alt) media.push({ type: 'img', src: IMG + p.alt + '.jpg' });
  if (p.video) media.push({ type: 'video', src: VIDEO + p.video, poster: VIDEO + p.video + '-poster.jpg' });
  // lifestyle photos keep their original backgrounds (same file names as the studio cut-outs, different folder)
  (p.life || []).forEach((l) => media.push({ type: 'img', src: LIFE + l + '.jpg' }));
  const slides = media;

  const stage = $('[data-pdp-stage]');
  const thumbs = $('[data-pdp-thumbs]');
  const dots = $('[data-pdp-dots]');
  stage.innerHTML = slides.map((m, i) => m.type === 'video'
    ? `<div class="pdp__slide pdp__slide--video"><video poster="${m.poster}" muted loop playsinline preload="none" aria-label="${p.name} video">
         <source src="${m.src}.mp4" type="video/mp4"><source src="${m.src}.webm" type="video/webm"></video></div>`
    : `<div class="pdp__slide"><img src="${m.src}" alt="${p.name}, image ${i + 1}" ${i ? 'loading="lazy"' : ''}>${i === 0 ? '<img src="assets/images/brand/hb-logo-full.svg" alt="" class="pdp__mark" aria-hidden="true">' : ''}</div>`
  ).join('');
  thumbs.innerHTML = slides.map((m, i) => `<button type="button" class="pdp__thumb${i === 0 ? ' is-active' : ''}${m.type === 'video' ? ' pdp__thumb--video' : ''}" aria-label="Show ${m.type === 'video' ? 'video' : 'image ' + (i + 1)}">
      <img src="${m.type === 'video' ? m.poster : m.src}" alt="" loading="lazy"></button>`).join('');
  dots.innerHTML = slides.map((_, i) => `<span class="${i === 0 ? 'is-active' : ''}"></span>`).join('');

  const thumbBtns = $$('.pdp__thumb', thumbs);
  const dotEls = $$('span', dots);
  let current = 0;
  const goTo = (i) => { stage.scrollTo({ left: i * stage.clientWidth, behavior: 'smooth' }); };
  thumbBtns.forEach((b, i) => b.addEventListener('click', () => goTo(i)));
  $('[data-pdp-prev]').addEventListener('click', () => goTo(Math.max(0, current - 1)));
  $('[data-pdp-next]').addEventListener('click', () => goTo(Math.min(slides.length - 1, current + 1)));
  stage.addEventListener('scroll', () => {
    const i = Math.round(stage.scrollLeft / stage.clientWidth);
    if (i === current) return;
    current = i;
    thumbBtns.forEach((b, j) => b.classList.toggle('is-active', j === i));
    dotEls.forEach((d, j) => d.classList.toggle('is-active', j === i));
    // play the video only while its slide is showing
    $$('video', stage).forEach((v) => {
      const onSlide = v.closest('.pdp__slide') === stage.children[i];
      if (onSlide) { const pr = v.play(); if (pr && pr.catch) pr.catch(() => {}); } else v.pause();
    });
  }, { passive: true });
  if (slides.length < 2) $('.pdp__gallery').classList.add('is-single');

  /* ---------- Size ---------- */
  let size = null;
  const sizesEl = $('[data-pdp-sizes]');
  const sizeLabel = $('[data-pdp-size]');
  const error = $('[data-pdp-error]');
  sizesEl.innerHTML = p.sizes.map((s) => `<button type="button" class="pdp__size" role="radio" aria-checked="false">${s}</button>`).join('');
  sizesEl.addEventListener('click', (e) => {
    const b = e.target.closest('.pdp__size');
    if (!b) return;
    size = b.textContent;
    $$('.pdp__size', sizesEl).forEach((x) => { const on = x === b; x.classList.toggle('is-active', on); x.setAttribute('aria-checked', on); });
    sizeLabel.textContent = size;
    error.textContent = '';
  });

  /* ---------- Quantity ---------- */
  const qtyInput = $('[data-qty-input]');
  const clampQty = (n) => Math.min(10, Math.max(1, parseInt(n, 10) || 1));
  $$('[data-qty]').forEach((b) => b.addEventListener('click', () => { qtyInput.value = clampQty(+qtyInput.value + +b.dataset.qty); }));
  qtyInput.addEventListener('change', () => { qtyInput.value = clampQty(qtyInput.value); });

  /* ---------- Add to bag (sticky bar on mobile uses the same action) ---------- */
  function add() {
    if (!size) {
      error.textContent = 'Please select a size.';
      sizesEl.classList.remove('shake'); void sizesEl.offsetWidth; sizesEl.classList.add('shake');
      sizesEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }
    H.addToBag(key, size, clampQty(qtyInput.value));
  }
  $('[data-pdp-add]').addEventListener('click', add);
  $('[data-pdp-bar-add]').addEventListener('click', add);

  const bar = $('[data-pdp-bar]');
  const buyBtn = $('[data-pdp-add]');
  // show the sticky bar once the main Add to Bag button has scrolled above the screen
  let ticking = false;
  const syncBar = () => { bar.classList.toggle('is-visible', buyBtn.getBoundingClientRect().bottom < 0); ticking = false; };
  window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(syncBar); } }, { passive: true });
  syncBar();

  /* ---------- Wishlist ---------- */
  const wish = $('[data-pdp-wish]');
  wish.addEventListener('click', () => {
    const on = wish.classList.toggle('is-active');
    wish.setAttribute('aria-pressed', on);
    H.showToast && H.showToast(on ? 'Added to wishlist' : 'Removed from wishlist');
  });

  /* ---------- Size guide ---------- */
  const guide = $('[data-guide]');
  $$('[data-open-guide]').forEach((b) => b.addEventListener('click', () => {
    if (guide.showModal) guide.showModal(); else guide.setAttribute('open', '');
  }));
  $('[data-close-guide]').addEventListener('click', () => guide.close ? guide.close() : guide.removeAttribute('open'));
  guide.addEventListener('click', (e) => { if (e.target === guide) guide.close(); });
})();
