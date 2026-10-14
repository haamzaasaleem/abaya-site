/* Hayabella — collection page with filters and sorting.
   State lives in the URL, e.g. collection.html?fabric=Satin,Crepe&color=White&sort=price-asc,
   so menu links and shared links open the right view. Runs after main.js (uses productCard). */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const H = window.HAYABELLA;
  const { CATALOG, PRODUCTS, FAMILY_HEX } = H;

  const inList = (list, key) => (PRODUCTS[list] || []).some((p) => p.key === key);
  const products = Object.values(CATALOG).map((base, i) => {
    const p = { ...base, ...H.listing(base.key) };
    p.order = i;
    p.now = p.sale || p.price;
    p.edits = ['new', 'best', 'occasion'].filter((l) => inList(l, p.key));
    if (p.sale) p.edits.push('sale');
    p.priceBand = p.now < 150 ? 'under-150' : p.now < 200 ? '150-200' : '200-plus';
    return p;
  });
  const newOrder = (PRODUCTS.new || []).map((p) => p.key);

  /* ---------- Filter groups ---------- */
  const uniq = (fn) => [...new Set(products.flatMap(fn))];
  const GROUPS = [
    { id: 'edit', label: 'Collection', values: ['new', 'best', 'occasion', 'sale'], names: { new: 'New In', best: 'Best Sellers', occasion: 'Occasion Edit', sale: 'On Sale' }, test: (p, v) => p.edits.includes(v) },
    { id: 'color', label: 'Colour', values: Object.keys(FAMILY_HEX).filter((f) => products.some((p) => p.family === f)), swatch: true, test: (p, v) => p.family === v },
    { id: 'fabric', label: 'Fabric', values: uniq((p) => [p.fabric]).sort(), test: (p, v) => p.fabric === v },
    { id: 'style', label: 'Embellishment', values: uniq((p) => [p.style]).sort(), test: (p, v) => p.style === v },
    { id: 'cut', label: 'Style', values: ['Open', 'Closed', 'Butterfly', 'Kimono', 'Belted'], test: (p, v) => p.cut === v },
    { id: 'size', label: 'Size', values: ['XS', 'S', 'M', 'L', 'XL'], chips: true, test: (p, v) => p.sizes.includes(v) },
    { id: 'price', label: 'Price', values: ['under-150', '150-200', '200-plus'], names: { 'under-150': 'Under $150', '150-200': '$150 – $200', '200-plus': '$200 & above' }, test: (p, v) => p.priceBand === v }
  ];
  const nameOf = (g, v) => (g.names && g.names[v]) || v;

  /* ---------- State <-> URL ---------- */
  const state = { sort: 'featured' };
  GROUPS.forEach((g) => { state[g.id] = new Set(); });
  const params = new URLSearchParams(location.search);
  GROUPS.forEach((g) => {
    (params.get(g.id) || '').split(',').map((v) => v.trim()).filter(Boolean).forEach((v) => {
      const match = g.values.find((x) => x.toLowerCase() === v.toLowerCase());
      if (match) state[g.id].add(match);
    });
  });
  if (['featured', 'new', 'price-asc', 'price-desc', 'name'].includes(params.get('sort'))) state.sort = params.get('sort');

  function writeUrl() {
    const q = new URLSearchParams();
    GROUPS.forEach((g) => { if (state[g.id].size) q.set(g.id, [...state[g.id]].join(',')); });
    if (state.sort !== 'featured') q.set('sort', state.sort);
    const qs = q.toString().replace(/%2C/g, ',');
    history.replaceState(null, '', qs ? '?' + qs : location.pathname);
  }

  /* ---------- Matching (OR within a group, AND across groups) ---------- */
  const matches = (p, skip) => GROUPS.every((g) => g.id === skip || !state[g.id].size || [...state[g.id]].some((v) => g.test(p, v)));

  function sorted(list) {
    const by = {
      featured: (a, b) => a.order - b.order,
      new: (a, b) => (newOrder.includes(a.key) ? newOrder.indexOf(a.key) : 99) - (newOrder.includes(b.key) ? newOrder.indexOf(b.key) : 99) || a.order - b.order,
      'price-asc': (a, b) => a.now - b.now,
      'price-desc': (a, b) => b.now - a.now,
      name: (a, b) => a.name.localeCompare(b.name)
    };
    return [...list].sort(by[state.sort]);
  }

  /* ---------- Render filter groups ---------- */
  const groupsEl = $('[data-plp-groups]');
  groupsEl.innerHTML = GROUPS.map((g) => `
    <details class="fgroup" data-group="${g.id}" ${g.id === 'edit' || g.id === 'color' || g.id === 'fabric' || state[g.id].size ? 'open' : ''}>
      <summary>${g.label}<span class="fgroup__n" data-group-n></span><svg><use href="#i-chev"/></svg></summary>
      <div class="fgroup__opts${g.chips ? ' fgroup__opts--chips' : ''}">
        ${g.values.map((v) => g.chips
          ? `<button type="button" class="fchip" data-f="${g.id}" data-v="${v}" aria-pressed="false">${v}</button>`
          : `<label class="fopt"><input type="checkbox" data-f="${g.id}" data-v="${v}">${g.swatch ? `<span class="fopt__swatch" style="background:${FAMILY_HEX[v]}"></span>` : '<span class="fopt__box"></span>'}<span class="fopt__name">${nameOf(g, v)}</span><span class="fopt__count" data-count></span></label>`
        ).join('')}
      </div>
    </details>`).join('');

  groupsEl.addEventListener('change', (e) => {
    const box = e.target.closest('input[data-f]');
    if (!box) return;
    const set = state[box.dataset.f];
    box.checked ? set.add(box.dataset.v) : set.delete(box.dataset.v);
    update();
  });
  groupsEl.addEventListener('click', (e) => {
    const chip = e.target.closest('.fchip');
    if (!chip) return;
    const set = state[chip.dataset.f];
    set.has(chip.dataset.v) ? set.delete(chip.dataset.v) : set.add(chip.dataset.v);
    update();
  });

  /* ---------- Sort ---------- */
  const sortEl = $('[data-plp-sort]');
  sortEl.value = state.sort;
  sortEl.addEventListener('change', () => { state.sort = sortEl.value; update(); });

  /* ---------- Chips + clear ---------- */
  const chipsEl = $('[data-plp-chips]');
  chipsEl.addEventListener('click', (e) => {
    const b = e.target.closest('[data-remove]');
    if (!b) return;
    const [g, v] = b.dataset.remove.split('|');
    state[g].delete(v);
    update();
  });
  $$('[data-clear-all]').forEach((b) => b.addEventListener('click', () => { GROUPS.forEach((g) => state[g.id].clear()); update(); }));

  /* ---------- Mobile filter panel ---------- */
  const openPanel = (open) => { document.body.classList.toggle('filters-open', open); document.body.classList.toggle('no-scroll', open); };
  $$('[data-open-filters]').forEach((b) => b.addEventListener('click', () => openPanel(true)));
  $$('[data-close-filters]').forEach((b) => b.addEventListener('click', () => openPanel(false)));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') openPanel(false); });

  /* ---------- Page title from the main filter ---------- */
  const TITLES = {
    edit: { new: ['New In', 'The latest Hayabella pieces, just landed.'], best: ['Best Sellers', 'The styles our customers keep coming back for.'], occasion: ['The Occasion Edit', 'Considered pieces for Eid, weddings and evenings.'], sale: ['Sale', 'Selected styles, now reduced.'] }
  };
  function heading() {
    const one = (id) => (state[id].size === 1 ? [...state[id]][0] : null);
    const others = (id) => GROUPS.every((g) => g.id === id || !state[g.id].size);
    let title = 'Abayas';
    let intro = 'Satin, crepe, chiffon, linen and velvet abayas, from everyday essentials to occasion pieces.';
    if (one('edit') && others('edit')) [title, intro] = TITLES.edit[one('edit')];
    else if (one('fabric') && others('fabric')) [title, intro] = [`${one('fabric')} Abayas`, `Every Hayabella abaya cut in ${one('fabric').toLowerCase()}.`];
    else if (one('color') && others('color')) [title, intro] = [`${one('color')} Abayas`, `Our ${one('color').toLowerCase()} abayas, from everyday to occasion.`];
    else if (one('cut') && others('cut')) [title, intro] = [`${one('cut')} Abayas`, `Every ${one('cut').toLowerCase()} silhouette in the collection.`];
    $('[data-plp-title]').textContent = title;
    $('[data-plp-crumb]').textContent = title;
    $('[data-plp-intro]').textContent = intro;
    document.title = `${title} | Hayabella – USA`;
  }

  /* ---------- Update everything ---------- */
  const grid = $('[data-plp-grid]');
  const empty = $('[data-plp-empty]');
  function update() {
    const list = sorted(products.filter((p) => matches(p)));
    grid.innerHTML = list.map((p) => H.productCard(p)).join('');
    empty.hidden = list.length > 0;
    const n = list.length;
    $('[data-plp-count]').textContent = `${n} ${n === 1 ? 'product' : 'products'}`;
    $('[data-plp-show]').textContent = `Show ${n} ${n === 1 ? 'result' : 'results'}`;

    // checkbox state + faceted counts (what each option would give with the other filters applied)
    GROUPS.forEach((g) => {
      const pool = products.filter((p) => matches(p, g.id));
      $$(`[data-f="${g.id}"]`, groupsEl).forEach((el) => {
        const v = el.dataset.v;
        const on = state[g.id].has(v);
        const c = pool.filter((p) => g.test(p, v)).length;
        if (el.type === 'checkbox') {
          el.checked = on;
          const label = el.closest('.fopt');
          $('[data-count]', label).textContent = `(${c})`;
          label.classList.toggle('is-empty', !c && !on);
        } else {
          el.classList.toggle('is-active', on);
          el.setAttribute('aria-pressed', on);
          el.classList.toggle('is-empty', !c && !on);
        }
      });
      $(`[data-group="${g.id}"] [data-group-n]`, groupsEl).textContent = state[g.id].size ? ` (${state[g.id].size})` : '';
    });

    const active = GROUPS.flatMap((g) => [...state[g.id]].map((v) => ({ g, v })));
    chipsEl.innerHTML = active.length
      ? active.map(({ g, v }) => `<button type="button" class="plp-chip" data-remove="${g.id}|${v}" aria-label="Remove filter ${nameOf(g, v)}">${nameOf(g, v)} <svg><use href="#i-close"/></svg></button>`).join('') +
        '<button type="button" class="plp-chip plp-chip--clear" data-clear-all>Clear all</button>'
      : '';
    $$('[data-clear-all]', chipsEl).forEach((b) => b.addEventListener('click', () => { GROUPS.forEach((gr) => state[gr.id].clear()); update(); }));
    $('[data-plp-active-count]').textContent = active.length ? `(${active.length})` : '';

    heading();
    writeUrl();
  }

  update();
})();
