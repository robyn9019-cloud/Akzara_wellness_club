import { $, $$ } from './util.js';

/** Filtros del listado de rituales («para quién» y duración) sin recargar la página. */
export function initRitualFilters() {
  const root = $('[data-browser]');
  const grid = $('[data-grid]');
  if (!root || !grid) return;
  const cards = $$('.rcard', grid);
  const empty = $('[data-empty]');
  const valid = ['pareja', 'para-mi', 'celebracion', 'regalo'];
  const q = new URLSearchParams(location.search).get('para');
  const state = { occ: valid.includes(q) ? q : '', dur: 0 };

  const apply = () => {
    let shown = 0;
    cards.forEach((c) => {
      const occs = (c.dataset.occ || '').split(' ');
      const ok = (!state.occ || occs.includes(state.occ)) && (!state.dur || Number(c.dataset.min) <= state.dur * 60);
      c.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    if (empty) empty.hidden = shown > 0;
    $$('[data-filter]', root).forEach((row) => {
      const cur = String(state[row.dataset.filter]);
      $$('button', row).forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.value === cur)));
    });
  };
  $$('[data-filter]', root).forEach((row) =>
    row.addEventListener('click', (e) => {
      const b = e.target.closest('button');
      if (!b) return;
      state[row.dataset.filter] = row.dataset.filter === 'dur' ? Number(b.dataset.value) : b.dataset.value;
      apply();
    })
  );
  apply();
}

/** Galería de la ficha del ritual: foto principal y miniaturas, también con las flechas del teclado. */
export function initGallery() {
  $$('[data-gallery]').forEach((g) => {
    const slides = $$('.gal__slide', g);
    if (slides.length < 2) return;
    const thumbs = $$('.gal__thumb', g);
    const count = $('[data-n]', g);
    let i = 0;
    const go = (n) => {
      i = (n + slides.length) % slides.length;
      slides.forEach((s, k) => (s.hidden = k !== i));
      thumbs.forEach((t, k) => t.setAttribute('aria-pressed', String(k === i)));
      if (count) count.textContent = String(i + 1);
    };
    $$('.gal__nav', g).forEach((b) => b.addEventListener('click', () => go(i + Number(b.dataset.dir))));
    thumbs.forEach((t) => t.addEventListener('click', () => go(Number(t.dataset.i))));
    g.tabIndex = 0;
    g.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') go(i - 1);
      if (e.key === 'ArrowRight') go(i + 1);
    });
  });
}

/** Aviso promocional: aparece tras unos segundos y recuerda que se cerró. */
export function initPromo() {
  const box = $('.promo');
  const cfg = (window.akzaraData && window.akzaraData.promo) || {};
  if (!box || !cfg.on) return;
  const KEY = 'akz-promo-closed';
  const days = Number(cfg.days) || 0;
  let closedAt = 0;
  let session = false;
  try {
    closedAt = Number(localStorage.getItem(KEY) || 0);
    session = sessionStorage.getItem(KEY) === '1';
  } catch (e) {
    /* se muestra igual */
  }
  if (closedAt && days > 0 && Date.now() - closedAt < days * 86400000) return;
  if (closedAt && days === 0 && session) return;
  setTimeout(() => (box.hidden = false), Math.max(0, Number(cfg.delay) || 0) * 1000);
  $('.promo__x', box).addEventListener('click', () => {
    box.hidden = true;
    try {
      localStorage.setItem(KEY, String(Date.now()));
      sessionStorage.setItem(KEY, '1');
    } catch (e) {
      /* nada */
    }
  });
}
