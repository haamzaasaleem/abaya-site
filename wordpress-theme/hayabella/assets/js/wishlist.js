/* Hayabella: wishlist page. Loads the saved product IDs from WooCommerce (Store API). */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const H = window.HAYABELLA || {};
  const T = H.i18n || {};
  const grid = $('[data-wl-grid]');
  const wrap = $('[data-wl]');
  const empty = $('[data-wl-empty]');
  const loading = $('[data-wl-loading]');
  if (!grid || !H.wishlist) return;

  const esc = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  let products = {};

  function card(p) {
    const img = p.images && p.images[0];
    const canAdd = p.type === 'simple' && p.is_purchasable && p.is_in_stock;
    const action = !p.is_in_stock
      ? `<span class="btn btn--outline is-disabled">${esc(T.outOfStock)}</span>`
      : canAdd
        ? `<button type="button" class="btn btn--dark" data-add="${p.id}" data-name="${esc(p.name)}">${esc(T.addToBag)}</button>`
        : `<a class="btn btn--dark" href="${esc(p.permalink)}">${esc(T.chooseOpts)}</a>`;
    return `
      <li class="wl-card" data-key="${p.id}">
        <div class="wl-card__media">
          <a href="${esc(p.permalink)}" aria-label="${esc(p.name)}">${img ? `<img src="${esc(img.src)}" ${img.srcset ? `srcset="${esc(img.srcset)}" sizes="(max-width: 767px) 50vw, 25vw"` : ''} alt="${esc(img.alt || p.name)}" loading="lazy">` : ''}</a>
          <button type="button" class="wl-card__remove" data-wl-remove="${p.id}" aria-label="${esc((T.remove || '%s').replace('%s', p.name))}"><svg aria-hidden="true"><use href="#i-close"/></svg></button>
        </div>
        <h3 class="product__name"><a href="${esc(p.permalink)}">${esc(p.name)}</a></h3>
        <div class="product__price">${p.price_html || ''}</div>
        <div class="wl-card__buy">${action}</div>
      </li>`;
  }

  function render() {
    const ids = H.wishlist.keys().filter((id) => products[id]);
    const count = $('[data-wl-count]');
    if (count) count.textContent = ids.length ? `(${ids.length})` : '';
    wrap.hidden = !ids.length;
    empty.hidden = !!ids.length;
    grid.innerHTML = ids.map((id) => card(products[id])).join('');
  }

  async function load() {
    const ids = H.wishlist.keys();
    if (!ids.length || !H.store) { products = {}; loading.hidden = true; render(); return; }
    try {
      const res = await fetch(`${H.store}products?include=${ids.join(',')}&per_page=100`, { credentials: 'same-origin' });
      const list = res.ok ? await res.json() : [];
      products = {};
      list.forEach((p) => { products[String(p.id)] = p; });
      // forget products that no longer exist
      ids.filter((id) => !products[id]).forEach((id) => H.wishlist.remove(id));
    } catch (e) { /* network error: show what we have */ }
    loading.hidden = true;
    render();
  }

  grid.addEventListener('click', (e) => {
    const rm = e.target.closest('[data-wl-remove]');
    if (rm) { H.wishlist.remove(rm.dataset.wlRemove); }
  });
  H.wishlist.onChange(render);
  load();
})();
