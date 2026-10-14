/* Hayabella — bag page. Reads the bag kept by main.js (window.HAYABELLA.cart). */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const H = window.HAYABELLA;
  const { CATALOG, IMG, money, url, cart } = H;

  const FREE_SHIPPING = 150;
  const SHIPPING = 9.95;
  // Promo codes (placeholder until a real checkout exists). WELCOME10 matches the newsletter offer.
  const CODES = { WELCOME10: { type: 'percent', value: 10, label: '10% off' } };
  const PROMO_KEY = 'hb-promo';
  let promo = null;
  try { promo = CODES[localStorage.getItem(PROMO_KEY)] ? localStorage.getItem(PROMO_KEY) : null; } catch (e) { promo = null; }

  const linesEl = $('[data-bag-lines]');
  const bagEl = $('[data-bag]');
  const emptyEl = $('[data-bag-empty]');
  const undoEl = $('[data-bag-undo]');
  let lastRemoved = null;

  const priceOf = (key) => { const p = H.listing(key); return { now: p.sale || p.price, was: p.sale ? p.price : null }; };

  function lineHtml(l, i) {
    const p = CATALOG[l.key];
    const { now, was } = priceOf(l.key);
    return `
      <li class="line" data-i="${i}">
        <a href="${url(l.key)}" class="line__img"><img src="${IMG + p.img}.jpg" alt="${p.name}"></a>
        <div class="line__info">
          <p class="line__tag">${p.tag}</p>
          <h3 class="line__name"><a href="${url(l.key)}">${p.name}</a></h3>
          <p class="line__price">${was ? `<s>${money(was)}</s> <span class="now">${money(now)}</span>` : money(now)}</p>
          <p class="line__meta">Colour: ${p.colors[0][0]}</p>
          <label class="line__size">Size
            <select data-size aria-label="Size for ${p.name}">
              ${p.sizes.map((s) => `<option${s === l.size ? ' selected' : ''}>${s}</option>`).join('')}
            </select>
          </label>
          <button type="button" class="line__remove" data-remove><svg><use href="#i-trash"/></svg> Remove</button>
        </div>
        <div class="line__qty">
          <div class="qty" aria-label="Quantity for ${p.name}">
            <button type="button" aria-label="Decrease quantity" data-step="-1" ${l.qty <= 1 ? 'disabled' : ''}><svg><use href="#i-minus"/></svg></button>
            <input type="number" min="1" max="10" value="${l.qty}" aria-label="Quantity" data-qty>
            <button type="button" aria-label="Increase quantity" data-step="1" ${l.qty >= 10 ? 'disabled' : ''}><svg><use href="#i-plus"/></svg></button>
          </div>
        </div>
        <p class="line__total">${money(now * l.qty)}</p>
      </li>`;
  }

  function render() {
    const lines = cart.lines();
    const n = cart.count();
    $('[data-bag-count]').textContent = n ? `(${n})` : '';
    document.title = `Your Bag${n ? ` (${n})` : ''} | Hayabella – USA`;
    bagEl.hidden = !lines.length;
    emptyEl.hidden = !!lines.length;

    if (lastRemoved) {
      undoEl.hidden = false;
      undoEl.innerHTML = `<span>${CATALOG[lastRemoved.line.key].name} (${lastRemoved.line.size}) was removed.</span> <button type="button" data-undo>Undo</button>`;
      if (!lines.length) { emptyEl.hidden = true; bagEl.hidden = false; }
      bagEl.classList.toggle('is-emptying', !lines.length);
    } else {
      undoEl.hidden = true;
      bagEl.classList.remove('is-emptying');
    }
    if (!lines.length && !lastRemoved) return;

    linesEl.innerHTML = lines.map(lineHtml).join('');

    // totals
    const subtotal = lines.reduce((s, l) => s + priceOf(l.key).now * l.qty, 0);
    const discount = promo ? Math.round(subtotal * CODES[promo].value) / 100 : 0;
    const afterDiscount = subtotal - discount;
    const freeShip = afterDiscount >= FREE_SHIPPING || !lines.length;
    const shipping = freeShip ? 0 : SHIPPING;
    const total = afterDiscount + shipping;
    $('[data-sum-subtotal]').textContent = money(subtotal);
    $('[data-sum-discount-row]').hidden = !promo;
    if (promo) { $('[data-sum-code]').textContent = `(${promo})`; $('[data-sum-discount]').textContent = '−' + money(discount); }
    $('[data-sum-shipping]').textContent = shipping ? money(shipping) : 'Free';
    $('[data-sum-total]').textContent = money(total);
    $('[data-sum-klarna]').textContent = total ? `Or 4 interest-free payments of ${money(total / 4)} with Klarna` : '';

    // free-shipping meter
    const left = FREE_SHIPPING - afterDiscount;
    $('[data-ship-text]').innerHTML = left > 0
      ? `You're <strong>${money(left)}</strong> away from free US shipping.`
      : 'You\'ve unlocked <strong>free US shipping</strong>.';
    $('[data-ship-bar]').style.width = Math.min(100, (afterDiscount / FREE_SHIPPING) * 100) + '%';
    $('[data-ship-meter]').classList.toggle('is-done', left <= 0);
  }

  /* ---------- Line actions ---------- */
  linesEl.addEventListener('click', (e) => {
    const li = e.target.closest('.line');
    if (!li) return;
    const i = +li.dataset.i;
    const step = e.target.closest('[data-step]');
    if (step) { lastRemoved = null; cart.setQty(i, cart.lines()[i].qty + +step.dataset.step); return; }
    if (e.target.closest('[data-remove]')) {
      lastRemoved = { i, line: cart.lines()[i] };
      cart.remove(i);
    }
  });
  linesEl.addEventListener('change', (e) => {
    const li = e.target.closest('.line');
    if (!li) return;
    const i = +li.dataset.i;
    lastRemoved = null;
    if (e.target.matches('[data-qty]')) cart.setQty(i, e.target.value);
    if (e.target.matches('[data-size]')) cart.setSize(i, e.target.value);
  });
  undoEl.addEventListener('click', (e) => {
    if (!e.target.closest('[data-undo]') || !lastRemoved) return;
    cart.restore(lastRemoved.i, lastRemoved.line);
    lastRemoved = null;
    render();
  });

  /* ---------- Promo code ---------- */
  const form = $('[data-promo]');
  const input = $('[data-promo-input]');
  const msg = $('[data-promo-msg]');
  if (promo) { input.value = promo; msg.textContent = `${CODES[promo].label} applied.`; }
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const code = input.value.trim().toUpperCase();
    if (!code) { msg.textContent = 'Please enter a code.'; msg.className = 'promo__msg is-error'; return; }
    if (CODES[code]) {
      promo = code;
      try { localStorage.setItem(PROMO_KEY, code); } catch (err) { /* storage unavailable */ }
      msg.textContent = `${CODES[code].label} applied.`;
      msg.className = 'promo__msg is-ok';
    } else {
      msg.textContent = 'That code isn\'t valid.';
      msg.className = 'promo__msg is-error';
    }
    render();
  });

  /* ---------- Checkout (not built yet) ---------- */
  $('[data-checkout]').addEventListener('click', () => H.showToast('Checkout is coming soon.'));

  cart.onChange(() => { render(); });
  render();
})();
