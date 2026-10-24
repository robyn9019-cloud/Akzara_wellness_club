import { $, $$, calm, finePointer } from './util.js';

/** Aparición suave de lo marcado con .reveal. */
export function initReveal() {
  const items = $$('.reveal');
  if (!items.length) return;
  if (!('IntersectionObserver' in window)) {
    items.forEach((e) => e.classList.add('is-in'));
    return;
  }
  const io = new IntersectionObserver(
    (entries) =>
      entries.forEach((en) => {
        if (en.isIntersecting) {
          en.target.classList.add('is-in');
          io.unobserve(en.target);
        }
      }),
    { rootMargin: '0px 0px -8% 0px' }
  );
  items.forEach((e) => io.observe(e));
}

/** Las fotos aparecen con un fundido cuando terminan de cargar. */
export function initFadeImages() {
  $$('img.fade').forEach((img) => {
    const ready = () => img.classList.add('is-ready');
    if (img.complete && img.naturalWidth) ready();
    else {
      img.addEventListener('load', ready, { once: true });
      img.addEventListener('error', ready, { once: true });
    }
  });
}

/** Halo del cursor y botones «magnéticos». No aparece en pantallas táctiles ni con «reducir movimiento». */
export function initCursor() {
  const el = $('.cursor');
  if (!el || !finePointer() || calm()) return;
  let x = -100;
  let y = -100;
  let cx = x;
  let cy = y;
  const loop = () => {
    cx += (x - cx) * 0.18;
    cy += (y - cy) * 0.18;
    el.style.transform = `translate3d(${cx}px, ${cy}px, 0) translate(-50%, -50%)`;
    requestAnimationFrame(loop);
  };
  let magnet = null;
  const pull = (b, e) => {
    if (magnet && magnet !== b) {
      magnet.style.setProperty('--mx', '0px');
      magnet.style.setProperty('--my', '0px');
    }
    magnet = b;
    if (b && e) {
      const r = b.getBoundingClientRect();
      b.style.setProperty('--mx', ((e.clientX - (r.left + r.width / 2)) * 0.14).toFixed(1) + 'px');
      b.style.setProperty('--my', ((e.clientY - (r.top + r.height / 2)) * 0.22).toFixed(1) + 'px');
    }
  };
  window.addEventListener(
    'mousemove',
    (e) => {
      x = e.clientX;
      y = e.clientY;
      el.dataset.on = '1';
      const t = e.target instanceof Element ? e.target : null;
      pull(t ? t.closest('.btn') : null, e);
      const hot = t && t.closest('a, button, [role="button"], summary, label, select, input, textarea');
      const pic = !hot && t && t.closest('.hover-zoom, .media');
      el.dataset.state = hot ? 'link' : pic ? 'image' : '';
    },
    { passive: true }
  );
  document.documentElement.addEventListener('mouseleave', () => (el.dataset.on = ''));
  requestAnimationFrame(loop);
}

/** Línea fina de bronce arriba: avanza al pulsar un enlace interno y se completa en la página siguiente. */
export function initNavProgress() {
  const bar = $('.navprog');
  if (!bar) return;
  const KEY = 'akz-nav';
  try {
    if (sessionStorage.getItem(KEY)) {
      sessionStorage.removeItem(KEY);
      bar.dataset.state = 'done';
      setTimeout(() => (bar.dataset.state = 'idle'), 700);
    }
  } catch (e) {
    /* almacenamiento bloqueado */
  }
  document.addEventListener('click', (e) => {
    const a = e.target instanceof Element ? e.target.closest('a') : null;
    if (!a || a.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey || !a.href) return;
    const u = new URL(a.href, location.href);
    if (u.origin !== location.origin || u.pathname === location.pathname || u.pathname.startsWith('/wp-admin')) return;
    bar.dataset.state = 'run';
    try {
      sessionStorage.setItem(KEY, '1');
    } catch (err) {
      /* nada */
    }
  });
  window.addEventListener('pageshow', (e) => {
    if (e.persisted) bar.dataset.state = 'idle';
  });
}

/** Video de portada: aparece con un fundido; con «reducir movimiento» queda la imagen. */
export function initHeroVideo() {
  const v = $('.hero__video');
  if (!v) return;
  if (calm()) {
    v.pause();
    v.removeAttribute('autoplay');
    return;
  }
  const ready = () => v.classList.add('is-ready');
  if (v.readyState >= 3) ready();
  v.addEventListener('canplay', ready, { once: true });
}
