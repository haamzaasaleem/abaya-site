/* Hayabella — Version 2 extras (shared behaviour lives in main.js) */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  /* ---------- Category index: swap preview image on hover ---------- */
  const preview = $('[data-index-preview]');
  if (preview) {
    $$('[data-index] .index__item').forEach((item) => {
      const show = () => {
        if (preview.getAttribute('src') === item.dataset.img) return;
        preview.classList.add('is-swapping');
        setTimeout(() => {
          preview.src = item.dataset.img;
          preview.classList.remove('is-swapping');
        }, 180);
      };
      item.addEventListener('mouseenter', show);
      item.addEventListener('focus', show);
    });
  }

  /* ---------- Feature product: size selection feeds Add to Bag ---------- */
  const sizes = $('[data-feature-sizes]');
  const add = $('[data-feature-add]');
  if (sizes && add) {
    sizes.addEventListener('click', (e) => {
      const chip = e.target.closest('.chip');
      if (!chip) return;
      $$('.chip', sizes).forEach((c) => c.classList.toggle('is-active', c === chip));
      add.dataset.size = chip.textContent.trim();
    });
  }

  /* ---------- Reveal on scroll for v2 sections ---------- */
  const els = $$('.statement, .index__item, .feature__info, .film__text, .mosaic__item, .lookbook__head');
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); }
      });
    }, { rootMargin: '0px 0px -60px 0px' });
    els.forEach((el, i) => {
      el.classList.add('reveal');
      el.style.transitionDelay = (i % 6) * 60 + 'ms';
      io.observe(el);
    });
  }
})();
