/* Maison Noor — homepage interactions (no dependencies) */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const money = (n) => '$' + n.toFixed(2).replace(/\.00$/, '');

  /* ---------- Product data (replace with API / CMS later) ---------- */
  const IMG = 'assets/images/photos/';
  const CATALOG = {
    noir: { name: 'Noir Crystal Chiffon Abaya', img: 'noir-crystal-front', alt: 'noir-crystal-back', price: 189, sizes: ['XS', 'S', 'M', 'L', 'XL'], colors: ['#141414'] },
    navy: { name: 'Midnight Embellished Open Abaya', img: 'navy-open-hooded', alt: 'navy-open-full', price: 169, sizes: ['S', 'M', 'L', 'XL'], colors: ['#1F2740', '#9AA5B5'] },
    burgundy: { name: 'Burgundy Petal Sleeve Abaya', img: 'burgundy-petal-full', alt: 'burgundy-petal-close', price: 179, sizes: ['XS', 'S', 'M', 'L'], colors: ['#5B1E2A'] },
    ivory: { name: 'Ivory Pearl Embroidered Abaya', img: 'ivory-pearl-front', alt: 'ivory-pearl-pose', price: 219, sizes: ['S', 'M', 'L', 'XL'], colors: ['#F1EAD8', '#F3D5C8'] },
    mauve: { name: 'Mauve Ruffle Cuff Abaya', img: 'mauve-ruffle', price: 139, sizes: ['XS', 'S', 'M', 'L', 'XL'], colors: ['#8E6F80', '#141414'] },
    charcoal: { name: 'Charcoal Embroidered Bell Sleeve Abaya', img: 'charcoal-cuff-wide', price: 159, sizes: ['S', 'M', 'L'], colors: ['#3A3A3F'] }
  };
  const PRODUCTS = {
    new: [
      { ...CATALOG.noir, badge: 'New' },
      { ...CATALOG.burgundy, badge: 'New' },
      { ...CATALOG.ivory, badge: 'Limited' },
      { ...CATALOG.navy, badge: 'New' }
    ],
    best: [
      { ...CATALOG.ivory, badge: 'Bestseller' },
      { ...CATALOG.mauve, sale: 119 },
      { ...CATALOG.charcoal, sale: 129 },
      { ...CATALOG.noir, badge: 'Bestseller' }
    ]
  };

  function productCard(p) {
    const badge = p.sale
      ? `<span class="badge badge--sale">-${Math.round((1 - p.sale / p.price) * 100)}%</span>`
      : p.badge ? `<span class="badge">${p.badge}</span>` : '';
    const price = p.sale
      ? `<s>${money(p.price)}</s><span class="now">${money(p.sale)}</span>`
      : `<span>${money(p.price)}</span>`;
    return `
      <article class="product reveal">
        <div class="product__media">
          <a href="#" class="product__link" aria-label="${p.name}">
            <img src="${IMG + p.img}.jpg" alt="${p.name}" loading="lazy">
            ${p.alt ? `<img src="${IMG + p.alt}.jpg" alt="" class="img-alt" loading="lazy">` : ''}
          </a>
          ${badge}
          <button class="wish" aria-label="Add ${p.name} to wishlist" aria-pressed="false"><svg><use href="#i-heart"/></svg></button>
          <div class="quick-add">
            <span class="quick-add__label">Quick Add</span>
            <div class="sizes">${p.sizes.map((s) => `<button class="size" data-add="${p.name}" data-size="${s}">${s}</button>`).join('')}</div>
          </div>
        </div>
        <h3 class="product__name"><a href="#">${p.name}</a></h3>
        <div class="product__price">${price}</div>
        <div class="swatches">${p.colors.map((c) => `<span class="swatch" style="background:${c}"></span>`).join('')}</div>
      </article>`;
  }

  const grid = $('[data-products]');
  function renderProducts(key) {
    grid.innerHTML = PRODUCTS[key].map(productCard).join('');
    observeReveals(grid);
  }

  /* ---------- Toast & cart ---------- */
  const toast = $('[data-toast]');
  let toastTimer;
  function showToast(msg) {
    toast.textContent = msg;
    toast.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 2600);
  }

  const cartCount = $('[data-cart-count]');
  let cart = 0;
  document.addEventListener('click', (e) => {
    const add = e.target.closest('[data-add]');
    if (add) {
      cart += 1;
      cartCount.textContent = cart;
      cartCount.classList.remove('bump');
      void cartCount.offsetWidth;
      cartCount.classList.add('bump');
      showToast(`${add.dataset.add} (${add.dataset.size}) added to bag`);
      return;
    }
    const wish = e.target.closest('.wish');
    if (wish) {
      const on = wish.classList.toggle('is-active');
      wish.setAttribute('aria-pressed', on);
      showToast(on ? 'Added to wishlist' : 'Removed from wishlist');
    }
  });

  /* ---------- Tabs ---------- */
  $$('[data-tab]').forEach((tab) => {
    tab.addEventListener('click', () => {
      if (tab.classList.contains('is-active')) return;
      $$('[data-tab]').forEach((t) => {
        t.classList.toggle('is-active', t === tab);
        t.setAttribute('aria-selected', t === tab);
      });
      grid.classList.add('is-loading');
      setTimeout(() => {
        renderProducts(tab.dataset.tab);
        grid.classList.remove('is-loading');
      }, 250);
    });
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
      $('[data-reel-toggle]', video.closest('.reel')).addEventListener('click', () => {
        video.dataset.userPaused = video.paused ? 'false' : 'true';
        video.paused ? play(video) : video.pause();
      });
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
        ? 'Welcome to the Maison! Check your inbox for your 10% off code.'
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

  renderProducts('new');
  observeReveals();
})();
