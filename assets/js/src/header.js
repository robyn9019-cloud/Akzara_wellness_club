import { $, $$ } from './util.js';

/** Cabecera: transparente sobre la portada, se esconde al bajar, menús desplegables y menú móvil. */
export function initHeader() {
  const hdr = $('.hdr');
  if (!hdr) return;
  const over = hdr.dataset.over === '1';
  const mnav = $('#mnav');
  const burger = $('.hdr__right .burger');
  let openMenu = null;
  let mobile = false;
  let hidden = false;
  let closeTimer = 0;

  const paint = () => {
    $$('[data-menu]', hdr).forEach((wrap) => {
      const on = wrap.dataset.menu === openMenu;
      $('.hdr__trigger', wrap).setAttribute('aria-expanded', String(on));
      $('.dd', wrap).dataset.open = String(on);
    });
    hdr.classList.toggle('hdr--hide', hidden && !openMenu && !mobile);
  };

  // Scroll: sólido tras pasar la portada y escondida al bajar.
  let lastY = window.scrollY;
  const onScroll = () => {
    const y = window.scrollY;
    if (over) hdr.classList.toggle('hdr--solid', y > window.innerHeight * 0.6);
    hidden = y > 160 && y > lastY + 4 ? true : y < lastY - 4 || y <= 160 ? false : hidden;
    lastY = y;
    paint();
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  // Menús desplegables (al pasar el cursor y al pulsar).
  $$('[data-menu]', hdr).forEach((wrap) => {
    const name = wrap.dataset.menu;
    wrap.addEventListener('mouseenter', () => {
      clearTimeout(closeTimer);
      openMenu = name;
      paint();
    });
    wrap.addEventListener('mouseleave', () => {
      closeTimer = setTimeout(() => {
        openMenu = null;
        paint();
      }, 160);
    });
    $('.hdr__trigger', wrap).addEventListener('click', () => {
      openMenu = openMenu === name ? null : name;
      paint();
    });
  });

  // La foto del menú de rituales cambia con la fila.
  const imgs = $$('.dd--rows .dd__img > .media', hdr);
  $$('.dd--rows li', hdr).forEach((li) => {
    const show = () => imgs.forEach((m, i) => (m.hidden = i !== Number(li.dataset.row)));
    li.addEventListener('mouseenter', show);
    li.addEventListener('focusin', show);
  });

  // Menú móvil.
  const setMobile = (on) => {
    mobile = on;
    mnav.dataset.open = String(on);
    burger.setAttribute('aria-expanded', String(on));
    document.body.style.overflow = on ? 'hidden' : '';
    if (on) $('[data-close]', mnav).focus();
    else burger.focus();
    paint();
  };
  burger.addEventListener('click', () => setMobile(true));
  $('[data-close]', mnav).addEventListener('click', () => setMobile(false));
  $$('a', mnav).forEach((a) => a.addEventListener('click', () => setMobile(false)));

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    openMenu = null;
    if (mobile) setMobile(false);
    paint();
  });
  document.addEventListener('click', (e) => {
    if (openMenu && !hdr.contains(e.target)) {
      openMenu = null;
      paint();
    }
  });
}
