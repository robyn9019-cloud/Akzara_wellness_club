/* Utilidades pequeñas compartidas por los módulos. */
export const $ = (sel, root = document) => root.querySelector(sel);
export const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
export const calm = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
export const finePointer = () => window.matchMedia('(hover: hover) and (pointer: fine)').matches;

/** Escapa texto para meterlo en HTML. */
export const esc = (s) =>
  String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

/** $ 280.000 */
export const cop = (n) => '$ ' + Math.round(Number(n) || 0).toLocaleString('es-CO').replace(/,/g, '.');

/** 80 → 1 h 20', 60 → 1 h, 35 → 35 min */
export const duration = (min) => {
  min = Number(min) || 0;
  if (!min) return 'duración a confirmar';
  if (min < 60) return min + ' min';
  const h = Math.floor(min / 60);
  const m = min % 60;
  return m ? `${h} h ${m}'` : `${h} h`;
};

/** Fecha local (no UTC) en formato YYYY-MM-DD. */
export const localISO = (d = new Date()) =>
  `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
