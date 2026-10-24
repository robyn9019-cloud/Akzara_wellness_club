import { $, $$, localISO } from './util.js';

const cap = (s) => s.charAt(0).toUpperCase() + s.slice(1);
const parse = (iso) => {
  const [y, m, d] = iso.split('-').map(Number);
  return new Date(y, (m || 1) - 1, d || 1, 12);
};
const loc = 'es-CO';

/** Selector de fecha propio (calendario en español, navegable con teclado) para la barra de reserva rápida. */
export function initDatePickers() {
  $$('[data-datepicker]').forEach(mount);
}

function mount(wrap) {
  const name = wrap.dataset.name;
  const placeholder = wrap.dataset.placeholder || 'Elegir fecha';
  const placement = wrap.dataset.placement === 'top' ? 'top' : 'bottom';
  const minISO = localISO();
  const uid = 'dp' + Math.random().toString(36).slice(2, 8);
  let value = '';
  let open = false;
  let view = { y: 0, m: 0 };
  let focusISO = minISO;
  let pop = null;

  const hidden = document.createElement('input');
  hidden.type = 'hidden';
  if (name) hidden.name = name;
  const btn = document.createElement('button');
  btn.type = 'button';
  btn.className = 'dp__btn';
  btn.setAttribute('aria-haspopup', 'dialog');
  btn.setAttribute('aria-expanded', 'false');
  btn.setAttribute('aria-controls', uid + '-cal');
  btn.setAttribute('aria-labelledby', 'qb-date-l');
  btn.innerHTML =
    '<span class="dp__ph"></span><svg viewBox="0 0 16 16" aria-hidden="true"><rect x="2" y="3.5" width="12" height="10.5" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.1"/><path d="M2 6.8h12M5.5 2v3M10.5 2v3" stroke="currentColor" stroke-width="1.1" fill="none"/></svg>';
  wrap.append(hidden, btn);

  const label = $('span', btn);
  const paintLabel = () => {
    if (value) {
      label.className = '';
      label.textContent = cap(new Intl.DateTimeFormat(loc, { weekday: 'long', day: 'numeric', month: 'long' }).format(parse(value)));
    } else {
      label.className = 'dp__ph';
      label.textContent = placeholder;
    }
    hidden.value = value;
  };
  paintLabel();

  const show = (iso) => {
    const d = parse(iso);
    view = { y: d.getFullYear(), m: d.getMonth() };
    focusISO = iso;
    render();
  };

  const render = () => {
    if (!open) return;
    if (!pop) {
      pop = document.createElement('div');
      pop.id = uid + '-cal';
      pop.className = 'dp__pop dp__pop--' + placement;
      pop.setAttribute('role', 'dialog');
      pop.setAttribute('aria-label', 'Elegir fecha');
      pop.addEventListener('keydown', onKey);
      wrap.append(pop);
    }
    const monthName = cap(new Intl.DateTimeFormat(loc, { month: 'long' }).format(new Date(view.y, view.m, 1)));
    const weekdays = Array.from({ length: 7 }, (_, i) => cap(new Intl.DateTimeFormat(loc, { weekday: 'short' }).format(new Date(2024, 0, 1 + i)).replace('.', '')).slice(0, 2));
    const first = new Date(view.y, view.m, 1, 12);
    const lead = (first.getDay() + 6) % 7;
    const count = new Date(view.y, view.m + 1, 0).getDate();
    const canPrev = new Date(view.y, view.m, 0, 12) >= parse(minISO);
    let days = '<span aria-hidden="true"></span>'.repeat(lead);
    for (let n = 1; n <= count; n++) {
      const iso = localISO(new Date(view.y, view.m, n, 12));
      const aria = new Intl.DateTimeFormat(loc, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(parse(iso));
      days += `<button type="button" role="gridcell" class="dp__day" data-iso="${iso}" tabindex="${iso === focusISO ? 0 : -1}"${iso < minISO ? ' disabled' : ''} aria-selected="${iso === value}"${iso === minISO ? ' data-today' : ''} aria-label="${aria}">${n}</button>`;
    }
    pop.innerHTML = `<div class="dp__head"><button type="button" class="dp__nav" data-nav="-1" aria-label="Mes anterior"${canPrev ? '' : ' disabled'}>‹</button><div class="dp__title" aria-live="polite"><span>${monthName}</span> <i>${view.y}</i></div><button type="button" class="dp__nav" data-nav="1" aria-label="Mes siguiente">›</button></div><div class="dp__week" aria-hidden="true">${weekdays.map((w) => `<span>${w}</span>`).join('')}</div><div class="dp__grid" role="grid">${days}</div>`;
    const f = $('button[tabindex="0"]', pop);
    if (f) f.focus();
  };

  const close = (refocus = true) => {
    open = false;
    btn.setAttribute('aria-expanded', 'false');
    if (pop) {
      pop.remove();
      pop = null;
    }
    if (refocus) btn.focus();
  };
  const move = (n) => {
    const d = parse(focusISO);
    d.setDate(d.getDate() + n);
    const iso = localISO(d);
    if (iso >= minISO) show(iso);
  };
  const moveMonth = (n) => {
    const d = parse(focusISO);
    const want = d.getDate();
    d.setDate(1);
    d.setMonth(d.getMonth() + n);
    d.setDate(Math.min(want, new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate()));
    let iso = localISO(d);
    if (iso < minISO) iso = minISO;
    show(iso);
  };
  function onKey(e) {
    const map = { ArrowLeft: () => move(-1), ArrowRight: () => move(1), ArrowUp: () => move(-7), ArrowDown: () => move(7), PageUp: () => moveMonth(-1), PageDown: () => moveMonth(1), Escape: () => close() };
    if (map[e.key]) {
      e.preventDefault();
      e.stopPropagation();
      map[e.key]();
    }
  }

  btn.addEventListener('click', () => {
    if (open) return close();
    open = true;
    btn.setAttribute('aria-expanded', 'true');
    show(value || minISO);
  });
  wrap.addEventListener('click', (e) => {
    const day = e.target.closest('.dp__day');
    const nav = e.target.closest('.dp__nav');
    if (day && !day.disabled) {
      value = day.dataset.iso;
      paintLabel();
      close();
    } else if (nav && !nav.disabled) {
      moveMonth(Number(nav.dataset.nav));
    }
  });
  document.addEventListener('mousedown', (e) => {
    if (open && !wrap.contains(e.target)) close(false);
  });
}
