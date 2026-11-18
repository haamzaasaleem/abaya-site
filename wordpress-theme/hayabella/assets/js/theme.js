/* Hayabella theme: site interactions (no dependencies).
   Settings come from window.HAYABELLA (printed by functions.php). */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const CFG = window.HAYABELLA || {};
  const T = CFG.i18n || {};
  const fmt = (s, v) => String(s || '').replace('%s', v);
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Toast ---------- */
  const toast = $('[data-toast]');
  let toastTimer;
  function showToast(msg, link) {
    if (!toast) return;
    toast.textContent = msg;
    if (link && link.href) {
      const a = document.createElement('a');
      a.href = link.href; a.textContent = link.text; a.className = 'toast__link';
      toast.appendChild(a);
    }
    toast.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('is-visible'), link ? 4200 : 2600);
  }

  /* ---------- Bag count ---------- */
  function setCartCount(n) {
    $$('[data-cart-count]').forEach((el) => {
      el.textContent = n;
      el.classList.remove('bump'); void el.offsetWidth; el.classList.add('bump');
    });
  }
  // WooCommerce's own AJAX add-to-cart (and cart fragments) replace the count; just bump it.
  if (window.jQuery) {
    window.jQuery(document.body).on('added_to_cart wc_fragments_refreshed', () => {
      $$('[data-cart-count]').forEach((el) => { el.classList.remove('bump'); void el.offsetWidth; el.classList.add('bump'); });
    });
  }

  /* ---------- Quick add (WooCommerce Store API) ---------- */
  async function addToBag(id, name, btn) {
    if (!CFG.store) return;
    if (btn) btn.disabled = true;
    try {
      const res = await fetch(CFG.store + 'cart/add-item', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', Nonce: CFG.nonce },
        body: JSON.stringify({ id: parseInt(id, 10), quantity: 1 })
      });
      const nonce = res.headers.get('Nonce');
      if (nonce) CFG.nonce = nonce;
      const data = await res.json();
      if (!res.ok) throw new Error(data && data.message ? data.message.replace(/<[^>]+>/g, '') : T.addFailed);
      setCartCount(data.items_count);
      showToast(fmt(T.added, name), { href: CFG.cartUrl, text: T.viewBag });
      // keep any WooCommerce mini-carts in sync
      if (window.jQuery) window.jQuery(document.body).trigger('wc_fragment_refresh');
    } catch (err) {
      showToast(err.message || T.addFailed);
    } finally {
      if (btn) btn.disabled = false;
    }
  }
  window.HAYABELLA = Object.assign(CFG, { showToast, addToBag });

  /* ---------- Wishlist (product IDs kept in this visitor's browser) ---------- */
  const WISH_KEY = 'hb-wishlist';
  const readWish = () => {
    try {
      const raw = JSON.parse(localStorage.getItem(WISH_KEY) || '[]');
      return Array.isArray(raw) ? raw.map(String).filter((k) => /^\d+$/.test(k)) : [];
    } catch (e) { return []; }
  };
  let wish = readWish();
  const wishListeners = [];
  function syncHearts() {
    $$('[data-wish]').forEach((b) => {
      const on = wish.includes(String(b.dataset.wish));
      b.classList.toggle('is-active', on);
      b.setAttribute('aria-pressed', on);
    });
    $$('[data-wish-count]').forEach((c) => { c.textContent = wish.length; c.hidden = !wish.length; });
  }
  function saveWish() {
    try { localStorage.setItem(WISH_KEY, JSON.stringify(wish)); } catch (e) { /* storage unavailable */ }
    syncHearts();
    wishListeners.forEach((fn) => fn(wish));
  }
  const wishlist = {
    keys: () => [...wish],
    toggle(id) {
      id = String(id);
      const on = !wish.includes(id);
      wish = on ? [id, ...wish] : wish.filter((k) => k !== id);
      saveWish();
      return on;
    },
    remove(id) { wish = wish.filter((k) => k !== String(id)); saveWish(); },
    onChange(fn) { wishListeners.push(fn); },
    sync: syncHearts
  };
  CFG.wishlist = wishlist;
  window.addEventListener('storage', (e) => { if (e.key === WISH_KEY) { wish = readWish(); syncHearts(); wishListeners.forEach((fn) => fn(wish)); } });

  document.addEventListener('click', (e) => {
    const add = e.target.closest('[data-add]');
    if (add) {
      e.preventDefault();
      addToBag(add.dataset.add, add.dataset.name || '', add);
      return;
    }
    const heart = e.target.closest('[data-wish]');
    if (heart) {
      e.preventDefault();
      const name = heart.dataset.name || '';
      const on = wishlist.toggle(heart.dataset.wish);
      showToast(on ? fmt(T.saved, name) : fmt(T.removed, name), on && CFG.wishlistUrl ? { href: CFG.wishlistUrl, text: T.viewWish } : null);
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
  if (header) {
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------- Drawer, search and shop filters ---------- */
  function toggleLayer(el, open) {
    if (!el) return;
    el.classList.toggle('is-open', open);
    el.setAttribute('aria-hidden', !open);
    document.body.classList.toggle('no-scroll', open && (el.matches('[data-drawer]') || el.matches('[data-filters]')));
  }
  const drawer = $('[data-drawer]');
  const search = $('[data-search]');
  const filters = $('[data-filters]');
  $$('[data-open-drawer]').forEach((b) => b.addEventListener('click', () => toggleLayer(drawer, true)));
  $$('[data-close-drawer]').forEach((b) => b.addEventListener('click', () => toggleLayer(drawer, false)));
  $$('[data-open-search]').forEach((b) => b.addEventListener('click', () => {
    toggleLayer(search, true);
    setTimeout(() => { const i = $('[data-search-input]'); if (i) i.focus(); }, 300);
  }));
  $$('[data-close-search]').forEach((b) => b.addEventListener('click', () => toggleLayer(search, false)));
  $$('[data-open-filters]').forEach((b) => b.addEventListener('click', () => toggleLayer(filters, true)));
  $$('[data-close-filters]').forEach((b) => b.addEventListener('click', () => toggleLayer(filters, false)));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') { toggleLayer(drawer, false); toggleLayer(search, false); toggleLayer(filters, false); }
  });
  // drawer sub-menus open on tap
  $$('.drawer__list .menu-item-has-children > a').forEach((a) => {
    const btn = document.createElement('button');
    btn.type = 'button'; btn.className = 'drawer__toggle'; btn.setAttribute('aria-expanded', 'false');
    btn.setAttribute('aria-label', a.textContent.trim());
    btn.addEventListener('click', () => {
      const li = a.parentElement; const open = !li.classList.contains('is-open');
      li.classList.toggle('is-open', open); btn.setAttribute('aria-expanded', open);
    });
    a.after(btn);
  });

  /* ---------- Live media: card clips play only while on screen ---------- */
  (function liveMedia() {
    if (reduceMotion || !('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => entries.forEach((e) => {
      const el = e.target;
      if (e.isIntersecting) { const pr = el.play(); if (pr && pr.catch) pr.catch(() => {}); } else el.pause();
    }), { threshold: 0.25 });
    const seen = new WeakSet();
    const scan = (root) => {
      const els = $$('video[data-live]', root);
      if (root.matches && root.matches('video[data-live]')) els.push(root);
      els.forEach((el) => { if (seen.has(el)) return; seen.add(el); io.observe(el); });
    };
    scan(document);
    // cards added later (wishlist page, infinite-scroll plugins)
    new MutationObserver((muts) => muts.forEach((m) => m.addedNodes.forEach((n) => { if (n.nodeType === 1) scan(n); })))
      .observe(document.body, { childList: true, subtree: true });
  })();

  /* ---------- Hero slider ----------
     On each change the current slide drops to black (.15 s), black holds (.25 s), then the next
     fades up while settling from 120% to 100% (.8 s) and stays still (see theme.css). */
  const hero = $('[data-slider]');
  if (hero) {
    const slides = $$('.hero__slide', hero);
    const dotsWrap = $('[data-dots]', hero);
    const CHANGE = 400;                                   // time for the drop to black + hold
    const DURATION = (CFG.heroMs || 5250) + CHANGE;       // photo / banner slides
    let idx = 0;
    let timer;
    let shownAt = 0;

    const setHeroTop = () => document.documentElement.style.setProperty('--hero-top', Math.round(hero.getBoundingClientRect().top + window.scrollY) + 'px');
    setHeroTop();
    window.addEventListener('resize', setHeroTop);
    window.addEventListener('load', setHeroTop);

    slides.forEach((sl, i) => sl.setAttribute('aria-label', `${i + 1} / ${slides.length}: ${sl.getAttribute('aria-label') || ''}`));

    const dots = dotsWrap ? slides.map((_, i) => {
      const d = document.createElement('button');
      d.type = 'button';
      d.className = 'hero__dot';
      d.setAttribute('role', 'tab');
      d.setAttribute('aria-label', `${i + 1} / ${slides.length}`);
      d.addEventListener('click', () => go(i));
      dotsWrap.appendChild(d);
      return d;
    }) : [];

    // video slides: play only the visible video, only while its slide shows, from the start each time
    let vUserPaused = reduceMotion;
    const visibleVideo = (sl) => $$('video', sl).find((v) => v.offsetParent !== null);
    function syncVideos() {
      $$('[data-video-slide]', hero).forEach((sl) => {
        const showing = slides[idx] === sl;
        $$('video', sl).forEach((v) => {
          const visible = v.offsetParent !== null;
          if (showing && visible && !vUserPaused) {
            if (!sl.dataset.playing) v.currentTime = 0;
            const pr = v.play(); if (pr && pr.catch) pr.catch(() => {});
          } else v.pause();
        });
        if (showing) sl.dataset.playing = '1'; else delete sl.dataset.playing;
        const t = $('[data-hero-vtoggle]', sl);
        if (t) { t.classList.toggle('is-paused', vUserPaused); t.setAttribute('aria-label', vUserPaused ? 'Play video' : 'Pause video'); }
      });
    }
    $$('[data-hero-vtoggle]', hero).forEach((t) => t.addEventListener('click', (e) => {
      e.preventDefault(); e.stopPropagation(); vUserPaused = !vUserPaused; syncVideos();
    }));
    window.addEventListener('resize', syncVideos);

    // how long the current slide stays: a video slide plays its film once
    function slideMs() {
      const sl = slides[idx];
      if (sl.matches('[data-video-slide]')) {
        const v = visibleVideo(sl);
        if (v && v.duration && isFinite(v.duration)) return v.duration * 1000 + CHANGE;
        return 16000;
      }
      return DURATION;
    }
    function schedule() {
      clearTimeout(timer);
      if (slides.length < 2 || hero.classList.contains('is-paused')) return;
      timer = setTimeout(() => go(idx + 1), Math.max(800, slideMs() - (Date.now() - shownAt)));
    }
    // once a film's length is known, re-time its slide
    $$('[data-video-slide] video', hero).forEach((v) => v.addEventListener('loadedmetadata', () => {
      if (slides[idx].contains(v)) schedule();
    }));

    function go(n) {
      slides[idx].classList.remove('is-active');
      if (dots[idx]) dots[idx].classList.remove('is-active');
      idx = (n + slides.length) % slides.length;
      slides[idx].classList.add('is-active');
      if (dots[idx]) dots[idx].classList.add('is-active');
      dots.forEach((d, i) => d.setAttribute('aria-selected', i === idx));
      slides.forEach((sl, i) => { sl.setAttribute('aria-hidden', i !== idx); sl.tabIndex = i === idx ? 0 : -1; });
      shownAt = Date.now();
      syncVideos();
      schedule();
    }

    const next = $('[data-next]', hero);
    const prev = $('[data-prev]', hero);
    if (next) next.addEventListener('click', () => go(idx + 1));
    if (prev) prev.addEventListener('click', () => go(idx - 1));
    hero.addEventListener('mouseenter', () => { hero.classList.add('is-paused'); clearTimeout(timer); });
    hero.addEventListener('mouseleave', () => { hero.classList.remove('is-paused'); shownAt = Date.now(); schedule(); });
    hero.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') go(idx + 1);
      if (e.key === 'ArrowLeft') go(idx - 1);
    });
    let startX = null;
    hero.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', (e) => {
      if (startX === null) return;
      const dx = e.changedTouches[0].clientX - startX;
      if (Math.abs(dx) > 40) go(dx < 0 ? idx + 1 : idx - 1);
      startX = null;
    });
    window.addEventListener('load', () => $$('img[loading="lazy"]', hero).forEach((img) => { img.loading = 'eager'; }));

    go(0);
  }

  /* ---------- Product sliders ---------- */
  function setupRail(rail) {
    const track = $('.rail__track', rail);
    if (!track || !track.children.length) { rail.hidden = true; return; }
    const prev = $('[data-rail-prev]', rail);
    const next = $('[data-rail-next]', rail);
    const dotsWrap = $('[data-rail-dots]', rail);
    const count = track.children.length;
    const centre = () => track.classList.toggle('is-few', track.scrollWidth <= track.clientWidth + 2);
    let dots = [];
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
      if (!dotsWrap || dots.length === pages) return;
      dotsWrap.innerHTML = '';
      dots = Array.from({ length: pages }, (_, i) => {
        const d = document.createElement('button');
        d.type = 'button';
        d.className = 'rail__dot';
        d.setAttribute('aria-label', `${i + 1} / ${pages}`);
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
    window.addEventListener('resize', () => { centre(); buildDots(); update(); });
    centre(); buildDots(); update();
  }
  $$('[data-rail]').forEach(setupRail);

  /* ---------- Newsletter (built-in form: shows the thank-you message only) ---------- */
  const form = $('[data-newsletter]');
  if (form) {
    const input = $('input', form);
    const msg = $('[data-newsletter-msg]');
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim());
      input.classList.toggle('is-invalid', !ok);
      msg.textContent = ok ? CFG.newsThanks : T.badEmail;
      if (ok) form.reset();
    });
  }

  /* ---------- Reveal on scroll ---------- */
  const io = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => entries.forEach((en) => {
      if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); }
    }), { rootMargin: '0px 0px -60px 0px' })
    : null;
  $$('.section__head, .occasion, .story__inner, .newsletter__inner').forEach((el, i) => {
    el.classList.add('reveal');
    el.style.transitionDelay = (i % 4) * 80 + 'ms';
    if (io) io.observe(el); else el.classList.add('is-visible');
  });

  /* ---------- Footer accordions closed on mobile ---------- */
  if (window.matchMedia('(max-width: 767px)').matches) {
    $$('.footer__col').forEach((d) => d.removeAttribute('open'));
  } else {
    $$('.footer__col summary').forEach((s) => s.addEventListener('click', (e) => e.preventDefault()));
  }

  syncHearts();
})();
