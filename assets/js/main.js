/* Hayabella — shared site interactions (no dependencies). Needs catalog.js loaded first. */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  const { PRODUCTS, IMG, money, url } = window.HAYABELLA;

  function productCard(p) {
    const pill = p.sale
      ? `<span class="pill pill--sale">${Math.round((1 - p.sale / p.price) * 100)}% Off</span>`
      : p.badge ? `<span class="pill">${p.badge}</span>` : '';
    const price = p.sale
      ? `<s>${money(p.price)}</s><span class="now">${money(p.sale)}</span>`
      : `<span>${money(p.price)}</span>`;
    return `
      <article class="product">
        <div class="product__media">
          <a href="${url(p.key)}" class="product__link" aria-label="${p.name}">
            <img src="${IMG + p.img}.jpg" alt="${p.name}" loading="lazy">
            ${p.alt ? `<img src="${IMG + p.alt}.jpg" alt="" class="img-alt" loading="lazy">` : ''}
          </a>
          <img src="assets/images/brand/hb-logo-full.svg" alt="" class="product__mark" aria-hidden="true">
          <button class="wish" aria-label="Add ${p.name} to wishlist" aria-pressed="false"><svg><use href="#i-heart"/></svg></button>
          <div class="quick-add">
            <span class="quick-add__label">Quick Add</span>
            <div class="sizes">${p.sizes.map((s) => `<button class="size" data-add="${p.key}" data-size="${s}">${s}</button>`).join('')}</div>
          </div>
        </div>
        <p class="product__tag">${p.tag}</p>
        <h3 class="product__name"><a href="${url(p.key)}">${p.name}</a></h3>
        <div class="product__price">${price}</div>
        ${pill}
      </article>`;
  }

  window.HAYABELLA.productCard = productCard;

  /* ---------- Product sliders ---------- */
  function setupRail(rail) {
    const track = $('[data-products]', rail);
    const prev = $('[data-rail-prev]', rail);
    const next = $('[data-rail-next]', rail);
    const dotsWrap = $('[data-rail-dots]', rail);
    track.innerHTML = (PRODUCTS[track.dataset.products] || []).map(productCard).join('');
    const count = track.children.length;
    let dots = [];

    // card width + gap, and how many cards fit in one view
    const metrics = () => {
      const card = track.firstElementChild;
      const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
      const cardStep = card ? card.offsetWidth + gap : track.clientWidth;
      const perView = Math.max(1, Math.round((track.clientWidth + gap) / cardStep));
      return { cardStep, perView, pages: Math.max(1, Math.ceil(count / perView)) };
    };
    const goTo = (page) => {
      const { cardStep, perView } = metrics();
      track.scrollTo({ left: page * perView * cardStep, behavior: 'smooth' });
    };
    const buildDots = () => {
      const { pages } = metrics();
      if (dots.length === pages) return;
      dotsWrap.innerHTML = '';
      dots = Array.from({ length: pages }, (_, i) => {
        const d = document.createElement('button');
        d.className = 'rail__dot';
        d.setAttribute('aria-label', `Show products page ${i + 1} of ${pages}`);
        d.addEventListener('click', () => goTo(i));
        dotsWrap.appendChild(d);
        return d;
      });
      dotsWrap.hidden = pages < 2;
    };
    const update = () => {
      const { cardStep, perView, pages } = metrics();
      const max = track.scrollWidth - track.clientWidth;
      const atEnd = track.scrollLeft >= max - 2;
      const page = atEnd ? pages - 1 : Math.round(track.scrollLeft / (perView * cardStep));
      dots.forEach((d, i) => d.classList.toggle('is-active', i === page));
      if (prev) prev.disabled = track.scrollLeft <= 2;
      if (next) next.disabled = atEnd;
    };
    const current = () => dots.findIndex((d) => d.classList.contains('is-active'));
    if (prev) prev.addEventListener('click', () => goTo(Math.max(0, current() - 1)));
    if (next) next.addEventListener('click', () => goTo(Math.min(dots.length - 1, current() + 1)));
    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', () => { buildDots(); update(); });
    buildDots();
    update();
  }

  /* ---------- Toast ---------- */
  const toast = $('[data-toast]');
  let toastTimer;
  function showToast(msg, link) {
    toast.textContent = msg;
    if (link) {
      const a = document.createElement('a');
      a.href = link.href; a.textContent = link.text; a.className = 'toast__link';
      toast.appendChild(a);
    }
    toast.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('is-visible'), link ? 4200 : 2600);
  }

  /* ---------- Bag (kept in this visitor's browser; no checkout yet) ----------
     Lines are { key, size, qty }; product details come from catalog.js. */
  const BAG_KEY = 'hb-cart';
  const cartCount = $('[data-cart-count]');
  const listeners = [];
  const readBag = () => {
    try {
      const raw = JSON.parse(localStorage.getItem(BAG_KEY) || '[]');
      return Array.isArray(raw) ? raw.filter((l) => window.HAYABELLA.CATALOG[l.key] && l.qty > 0) : [];
    } catch (e) { return []; }
  };
  let bag = readBag();
  const count = () => bag.reduce((n, l) => n + l.qty, 0);
  function saveBag() {
    try { localStorage.setItem(BAG_KEY, JSON.stringify(bag)); } catch (e) { /* storage unavailable */ }
    cartCount.textContent = count();
    listeners.forEach((fn) => fn(bag));
  }
  const clampQty = (n) => Math.min(10, Math.max(1, parseInt(n, 10) || 1));
  const cart = {
    lines: () => bag.map((l) => ({ ...l })),
    count,
    add(key, size, qty = 1) {
      const line = bag.find((l) => l.key === key && l.size === size);
      if (line) line.qty = clampQty(line.qty + qty); else bag.push({ key, size, qty: clampQty(qty) });
      saveBag();
    },
    setQty(i, qty) { if (bag[i]) { bag[i].qty = clampQty(qty); saveBag(); } },
    setSize(i, size) {
      const line = bag[i];
      if (!line || line.size === size) return;
      const twin = bag.findIndex((l, j) => j !== i && l.key === line.key && l.size === size);
      if (twin > -1) { bag[twin].qty = clampQty(bag[twin].qty + line.qty); bag.splice(i, 1); } else line.size = size;
      saveBag();
    },
    remove(i) { bag.splice(i, 1); saveBag(); },
    restore(i, line) { bag.splice(i, 0, line); saveBag(); },
    onChange(fn) { listeners.push(fn); }
  };
  cartCount.textContent = count();
  // keep the count in sync if the bag changes in another tab
  window.addEventListener('storage', (e) => { if (e.key === BAG_KEY) { bag = readBag(); cartCount.textContent = count(); listeners.forEach((fn) => fn(bag)); } });

  function addToBag(key, size, qty = 1) {
    const p = window.HAYABELLA.CATALOG[key];
    if (!p) return;
    cart.add(key, size, qty);
    cartCount.classList.remove('bump');
    void cartCount.offsetWidth;
    cartCount.classList.add('bump');
    showToast(`${qty > 1 ? qty + ' × ' : ''}${p.name} (${size}) added to bag`, { href: 'cart.html', text: 'View bag' });
  }
  window.HAYABELLA.cart = cart;
  window.HAYABELLA.addToBag = addToBag;
  window.HAYABELLA.showToast = showToast;

  document.addEventListener('click', (e) => {
    const add = e.target.closest('[data-add]');
    if (add) {
      addToBag(add.dataset.add, add.dataset.size);
      return;
    }
    const wish = e.target.closest('.wish');
    if (wish) {
      const on = wish.classList.toggle('is-active');
      wish.setAttribute('aria-pressed', on);
      showToast(on ? 'Added to wishlist' : 'Removed from wishlist');
    }
  });

  /* ---------- Announcement bar ---------- */
  const notes = $$('[data-announcement] p');
  let noteIdx = 0;
  if (notes.length > 1) {
    setInterval(() => {
      const cur = notes[noteIdx];
      cur.classList.remove('is-active');
      cur.classList.add('is-leaving');
      setTimeout(() => cur.classList.remove('is-leaving'), 600);
      noteIdx = (noteIdx + 1) % notes.length;
      notes[noteIdx].classList.add('is-active');
    }, 4000);
  }

  /* ---------- Sticky header shadow ---------- */
  const header = $('[data-header]');
  const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 40);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Drawer & search ---------- */
  function toggleLayer(el, open) {
    el.classList.toggle('is-open', open);
    el.setAttribute('aria-hidden', !open);
    document.body.classList.toggle('no-scroll', open && el.matches('[data-drawer]'));
  }
  const drawer = $('[data-drawer]');
  const search = $('[data-search]');
  $$('[data-open-drawer]').forEach((b) => b.addEventListener('click', () => toggleLayer(drawer, true)));
  $$('[data-close-drawer]').forEach((b) => b.addEventListener('click', () => toggleLayer(drawer, false)));
  $$('[data-open-search]').forEach((b) => b.addEventListener('click', () => {
    toggleLayer(search, true);
    setTimeout(() => $('[data-search-input]').focus(), 300);
  }));
  $$('[data-close-search]').forEach((b) => b.addEventListener('click', () => toggleLayer(search, false)));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') { toggleLayer(drawer, false); toggleLayer(search, false); }
  });

  /* ---------- Hero slider ---------- */
  const hero = $('[data-slider]');
  if (hero) {
    const slides = $$('.hero__slide', hero);
    const dotsWrap = $('[data-dots]', hero);
    const DURATION = 6000;
    let idx = 0;
    let timer;
    hero.style.setProperty('--slide-duration', DURATION + 'ms');

    const dots = slides.map((_, i) => {
      const d = document.createElement('button');
      d.className = 'hero__dot';
      d.setAttribute('role', 'tab');
      d.setAttribute('aria-label', `Go to slide ${i + 1}`);
      d.addEventListener('click', () => go(i));
      dotsWrap.appendChild(d);
      return d;
    });

    function go(n) {
      slides[idx].classList.remove('is-active');
      dots[idx].classList.remove('is-active');
      idx = (n + slides.length) % slides.length;
      slides[idx].classList.add('is-active');
      // restart progress animation
      void dots[idx].offsetWidth;
      dots[idx].classList.add('is-active');
      dots.forEach((d, i) => d.setAttribute('aria-selected', i === idx));
      hero.classList.toggle('hero--light-ui', slides[idx].classList.contains('hero__slide--light'));
      restart();
    }
    function restart() {
      clearInterval(timer);
      if (!hero.classList.contains('is-paused')) timer = setInterval(() => go(idx + 1), DURATION);
    }

    $('[data-next]', hero).addEventListener('click', () => go(idx + 1));
    $('[data-prev]', hero).addEventListener('click', () => go(idx - 1));
    hero.addEventListener('mouseenter', () => { hero.classList.add('is-paused'); clearInterval(timer); });
    hero.addEventListener('mouseleave', () => { hero.classList.remove('is-paused'); restart(); });
    hero.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') go(idx + 1);
      if (e.key === 'ArrowLeft') go(idx - 1);
    });

    // touch swipe
    let startX = null;
    hero.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', (e) => {
      if (startX === null) return;
      const dx = e.changedTouches[0].clientX - startX;
      if (Math.abs(dx) > 40) go(dx < 0 ? idx + 1 : idx - 1);
      startX = null;
    });

    // images after the first are lazy; warm them up once the page has loaded
    window.addEventListener('load', () => $$('img[loading="lazy"]', hero).forEach((img) => { img.loading = 'eager'; }));

    go(0);
  }

  /* ---------- Video reels ---------- */
  const reels = $$('[data-reel]');
  if (reels.length) {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const setPaused = (video, paused) => {
      const fig = video.closest('.reel');
      fig.classList.toggle('is-paused', paused);
      $('[data-reel-toggle]', fig).setAttribute('aria-label', paused ? 'Play video' : 'Pause video');
    };
    const play = (video) => {
      const p = video.play();
      if (p && p.catch) p.catch(() => {}); // autoplay blocked: poster stays, button offers play
    };

    reels.forEach((video) => {
      // user-paused videos stay paused while scrolling
      video.dataset.userPaused = reduceMotion ? 'true' : 'false';
      setPaused(video, true);
      video.addEventListener('play', () => setPaused(video, false));
      video.addEventListener('pause', () => setPaused(video, true));
      const toggle = () => {
        video.dataset.userPaused = video.paused ? 'false' : 'true';
        video.paused ? play(video) : video.pause();
      };
      $('[data-reel-toggle]', video.closest('.reel')).addEventListener('click', toggle);
      video.addEventListener('click', toggle);
    });

    // only load and play while on screen
    if ('IntersectionObserver' in window) {
      const vio = new IntersectionObserver((entries) => {
        entries.forEach(({ target: video, isIntersecting }) => {
          if (isIntersecting && video.dataset.userPaused !== 'true') play(video);
          else if (!isIntersecting && !video.paused) video.pause();
        });
      }, { threshold: 0.25 });
      reels.forEach((v) => vio.observe(v));
    } else if (!reduceMotion) {
      reels.forEach(play);
    }
  }

  /* ---------- Newsletter ---------- */
  const form = $('[data-newsletter]');
  if (form) {
    const input = $('input', form);
    const msg = $('[data-newsletter-msg]');
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim());
      input.classList.toggle('is-invalid', !ok);
      msg.textContent = ok
        ? 'Welcome to Hayabella! Check your inbox for your 10% off code.'
        : 'Please enter a valid email address.';
      if (ok) form.reset();
    });
  }

  /* ---------- Reveal on scroll ---------- */
  const io = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); }
      });
    }, { rootMargin: '0px 0px -60px 0px' })
    : null;
  function observeReveals(ctx = document) {
    $$('.reveal', ctx).forEach((el, i) => {
      el.style.transitionDelay = (i % 4) * 80 + 'ms';
      io ? io.observe(el) : el.classList.add('is-visible');
    });
  }
  $$('.section__head, .cat, .occasion, .story__inner, .newsletter__inner').forEach((el) => el.classList.add('reveal'));

  /* ---------- Footer accordions closed on mobile ---------- */
  if (window.matchMedia('(max-width: 767px)').matches) {
    $$('.footer__col').forEach((d) => d.removeAttribute('open'));
  } else {
    // keep desktop columns always expanded
    $$('.footer__col summary').forEach((s) => s.addEventListener('click', (e) => e.preventDefault()));
  }

  $$('[data-year]').forEach((el) => { el.textContent = new Date().getFullYear(); });

  $$('[data-rail]').forEach(setupRail);
  observeReveals();
})();
