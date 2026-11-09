/* Hayabella — wishlist page. Reads the wishlist kept by main.js (window.HAYABELLA.wishlist). */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const H = window.HAYABELLA;
  const { CATALOG, IMG, money, url, wishlist } = H;

  const grid = $('[data-wl-grid]');
  const wrap = $('[data-wl]');
  const empty = $('[data-wl-empty]');

  function card(key) {
    const p = { ...CATALOG[key], ...H.listing(key) };
    const price = p.sale
      ? `<s>${money(p.price)}</s><span class="now">${money(p.sale)}</span>`
      : `<span>${money(p.price)}</span>`;
    return `
      <li class="wl-card" data-key="${key}">
        <div class="wl-card__media">
          <a href="${url(key)}" aria-label="${p.name}"><img src="${IMG + p.img}.jpg" alt="${p.name}" loading="lazy"></a>
          <button type="button" class="wl-card__remove" data-wl-remove aria-label="Remove ${p.name} from wishlist"><svg><use href="#i-close"/></svg></button>
        </div>
        <p class="product__tag">${p.tag}</p>
        <h3 class="product__name"><a href="${url(key)}">${p.name}</a></h3>
        <div class="product__price">${price}</div>
        <div class="wl-card__buy">
          <select aria-label="Size for ${p.name}" data-wl-size>
            <option value="">Select size</option>
            ${p.sizes.map((s) => `<option>${s}</option>`).join('')}
          </select>
          <button type="button" class="btn btn--dark" data-wl-add>Add to Bag</button>
        </div>
        <p class="wl-card__error" role="alert" data-wl-error></p>
      </li>`;
  }

  function render() {
    const keys = wishlist.keys();
    $('[data-wl-count]').textContent = keys.length ? `(${keys.length})` : '';
    document.title = `Wishlist${keys.length ? ` (${keys.length})` : ''} | Hayabella – USA`;
    wrap.hidden = !keys.length;
    empty.hidden = !!keys.length;
    grid.innerHTML = keys.map(card).join('');
  }

  grid.addEventListener('click', (e) => {
    const li = e.target.closest('.wl-card');
    if (!li) return;
    const key = li.dataset.key;
    if (e.target.closest('[data-wl-remove]')) {
      wishlist.remove(key);
      H.showToast(`${CATALOG[key].name} removed from wishlist`);
      return;
    }
    if (e.target.closest('[data-wl-add]')) {
      const size = $('[data-wl-size]', li).value;
      if (!size) { $('[data-wl-error]', li).textContent = 'Please select a size.'; return; }
      H.addToBag(key, size, 1);
    }
  });
  grid.addEventListener('change', (e) => {
    const li = e.target.closest('.wl-card');
    if (li && e.target.matches('[data-wl-size]')) $('[data-wl-error]', li).textContent = '';
  });

  wishlist.onChange(render);
  render();
})();
