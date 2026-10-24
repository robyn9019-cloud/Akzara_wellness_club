import { $, $$, esc, cop, duration } from './util.js';

const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const PHONE = /^\+?[\d\s()-]{7,}$/;

// Textos de la parte trasera de la tarjeta, en el idioma de la tarjeta.
const CARD = {
  es: { messagePh: 'Aquí va su mensaje…', para: 'Para', deParte: 'De parte de', ritual: 'Ritual o servicio', dur: 'Duración', value: 'Valor', issued: 'Fecha de emisión', issuedV: 'al confirmar el pago', expires: 'Fecha de vencimiento', expiresV: 'un año después', code: 'Código del certificado', codeV: 'Se genera al confirmar el pago', one: 'persona', many: 'personas' },
  en: { messagePh: 'Your message goes here…', para: 'To', deParte: 'From', ritual: 'Ritual or treatment', dur: 'Duration', value: 'Value', issued: 'Issue date', issuedV: 'when payment is confirmed', expires: 'Expiry date', expiresV: 'one year later', code: 'Certificate code', codeV: 'Created when payment is confirmed', one: 'guest', many: 'guests' },
};
const STEPS = ['Elegir el regalo', 'Personalizar', 'Sus datos'];

/** Formulario de gift card en tres pasos (reemplaza al de Next.js; envía a la ruta REST del tema). */
export function initGift() {
  const root = $('[data-gift]');
  const cfg = window.akzaraData && window.akzaraData.gift;
  if (!root || !cfg) return;

  const rituals = cfg.rituals;
  const designs = cfg.designs; // [nombre, variante, frase ES, frase EN]
  const init = rituals.find((r) => r.slug === cfg.initial);
  const s = {
    step: init ? 1 : 0,
    max: init ? 1 : 0,
    mode: 'ritual',
    serviceId: init ? init.id : null,
    amount: 300000,
    custom: '',
    query: '',
    design: 0,
    lang: 'es',
    to: '',
    from: '',
    msg: '',
    name: '',
    email: '',
    phone: '',
    consent: false,
    errors: {},
    sending: false,
    sendError: '',
    done: null,
  };

  const svc = () => rituals.find((r) => String(r.id) === String(s.serviceId));
  const value = () => (s.mode === 'ritual' ? (svc() ? svc().price : 0) : s.custom ? Number(s.custom) : s.amount);
  const phrase = () => designs[s.design][s.lang === 'en' ? 3 : 2];
  const people = (n, L) => `${n} ${n === 1 ? L.one : L.many}`;

  const face = (variant, text) =>
    `<div class="gc gc--${esc(variant)}"><div class="gc__logo"><img src="${esc(cfg.iso)}" alt="" width="40" height="37"${variant === 'bronce' ? ' style="filter:brightness(.55) saturate(.7)"' : ''}><b>AKZARA</b><small>WELLNESS CLUB</small></div>${text ? `<div class="gc__phrase">${esc(text)}</div>` : ''}</div>`;

  const back = () => {
    const L = CARD[s.lang];
    const r = svc();
    return `<div class="back" aria-label="Por detrás de la tarjeta"><div class="msg ${s.msg ? '' : 'empty'}" data-back-msg>${esc(s.msg || L.messagePh)}</div>
      <p style="margin:0 0 8px" data-back-names>${backNames(L)}</p>
      ${
        s.mode === 'ritual' && r
          ? `<div><b>${L.ritual}:</b> ${esc(r.name)}</div><div><b>${L.dur}:</b> ${duration(r.min)} · ${people(r.people, L)}</div><p style="margin:8px 0">${esc(r.summary)}</p>`
          : `<div style="margin-bottom:8px"><b>${L.value}:</b> ${cop(value())}</div>`
      }
      <div style="font-size:10.5px"><div><b>${L.issued}:</b> ${L.issuedV}</div><div><b>${L.expires}:</b> ${L.expiresV}</div></div>
      <div class="code"><small>${L.code}</small>${L.codeV}</div></div>`;
  };
  const backNames = (L) => {
    const a = s.to ? `${L.para}: <b>${esc(s.to)}</b>` : '';
    const b = s.from ? `${L.deParte}: <b>${esc(s.from)}</b>` : '';
    return a + (a && b ? '&nbsp;&nbsp;&nbsp;' : '') + b;
  };

  const err = (k) => (s.errors[k] ? `<div class="err">${esc(s.errors[k])}</div>` : '');

  const pills = () =>
    `<div class="pills">${STEPS.map((t, i) => `<button type="button" class="pill" data-go="${i}" data-state="${i === s.step ? 'current' : i <= s.max ? 'done' : 'todo'}"${i > s.max ? ' disabled' : ''}><i>${i + 1}</i> ${t}</button>`).join('')}</div>`;

  const step0 = () => {
    const q = s.query.trim().toLowerCase();
    const list = rituals.filter((r) => !q || r.name.toLowerCase().includes(q));
    const can = s.mode === 'ritual' ? !!svc() : value() >= cfg.min;
    return `<div class="panel step-in" id="step-panel" tabindex="-1"><h2>¿Qué desea regalar?</h2>
      <div class="seg"><button type="button" data-mode="ritual" aria-pressed="${s.mode === 'ritual'}">Un ritual</button><button type="button" data-mode="monto" aria-pressed="${s.mode === 'monto'}">Por monto</button></div>
      ${
        s.mode === 'ritual'
          ? `<input class="input" data-query placeholder="Buscar un ritual…" value="${esc(s.query)}" aria-label="Buscar un ritual…">
             <div class="picklist" style="max-height:230px" role="listbox">${list
               .map((r) => `<button type="button" role="option" class="pick" data-pick="${r.id}" aria-selected="${String(s.serviceId) === String(r.id)}"><span><b>${esc(r.name)}</b><small>${duration(r.min)} · ${people(r.people, CARD.es)}</small></span><span class="p">${cop(r.price)}</span></button>`)
               .join('')}</div>`
          : `<div class="amounts">${cfg.amounts.map((a) => `<button type="button" class="slot" style="min-width:96px" data-amount="${a}" aria-pressed="${!s.custom && s.amount === a}">${cop(a)}</button>`).join('')}</div>
             <div class="field" style="max-width:260px"><label class="f-label" for="amt">Otro monto</label><input id="amt" class="input" data-custom inputmode="numeric" placeholder="$ 400.000" value="${s.custom ? cop(s.custom) : ''}"><p class="note">Monto mínimo ${cop(cfg.min)}</p></div>`
      }
      <div class="acts" style="justify-content:flex-start"><button type="button" class="btn" style="height:36px;padding:0 22px" data-go="1"${can ? '' : ' disabled'}>Continuar →</button></div></div>`;
  };

  const step1 = () => {
    const r = svc();
    const d = designs[s.design];
    return `<div class="panel step-in" id="step-panel" tabindex="-1"><h2>Personalice su regalo</h2><p class="sub">Así lo verá quien lo reciba. El código se genera solo cuando se confirma el pago.</p>
      <div class="gift-grid"><div>
        <div class="gc-wrap"><span data-face>${face(d[1], phrase())}</span><button type="button" class="gc-nav" style="left:12px" data-design="-1" aria-label="Diseño anterior">‹</button><button type="button" class="gc-nav" style="right:12px" data-design="1" aria-label="Diseño siguiente">›</button></div>
        <div class="gc-cap"><span>${esc(d[0])}</span><span>${s.design + 1} / ${designs.length}</span></div>
        <span class="f-label">Por detrás de la tarjeta</span><div data-back>${back()}</div>
      </div><div>
        <span class="f-label">Lo que regala</span>
        <div class="given">${s.mode === 'ritual' && r ? `<b>${esc(r.name)}</b><small>${duration(r.min)} · ${people(r.people, CARD.es)} · ${cop(r.price)}</small>` : `<b>Gift card</b><small>${cop(value())}</small>`}<button type="button" class="link-u" data-go="0">Cambiar el ritual</button></div>
        <span class="f-label">Idioma de la tarjeta</span>
        <div class="seg"><button type="button" data-lang="es" aria-pressed="${s.lang === 'es'}">Español</button><button type="button" data-lang="en" aria-pressed="${s.lang === 'en'}">English</button></div>
        <span class="f-label">Diseño</span>
        <div class="thumbs">${designs.map((x, i) => `<button type="button" class="thumb" data-thumb="${i}" aria-pressed="${i === s.design}" aria-label="${esc(x[0])}">${face(x[1], '')}</button>`).join('')}</div>
        <div class="field"><label class="f-label" for="gto">¿A quién se lo regala?</label><input id="gto" class="input" data-f="to" placeholder="Nombre de quien lo recibe" maxlength="60" value="${esc(s.to)}"${s.errors.to ? ' aria-invalid="true"' : ''}>${err('to')}</div>
        <div class="field"><label class="f-label" for="gfr">¿De parte de quién?</label><input id="gfr" class="input" data-f="from" placeholder="Su nombre, o como desee firmarlo" maxlength="60" value="${esc(s.from)}"${s.errors.from ? ' aria-invalid="true"' : ''}>${err('from')}</div>
        <div class="field"><label class="f-label" for="gms">Su mensaje <small>(máximo 300 caracteres)</small></label><textarea id="gms" class="textarea" data-f="msg" rows="4" maxlength="300" placeholder="Escriba unas líneas: por qué lo regala, qué le desea…">${esc(s.msg)}</textarea><div class="counter" data-counter>${s.msg.length} / 300</div></div>
      </div></div>
      <div class="acts"><button type="button" class="btn btn--ghost" data-go="0">← Volver</button><button type="button" class="btn btn--block" data-next2>Continuar →</button></div></div>`;
  };

  const step2 = () => {
    const r = svc();
    return `<div class="panel step-in" id="step-panel" tabindex="-1"><div class="gift-grid"><div>
      <h2>Sus datos</h2><p class="sub">Para enviarle el enlace de pago y la gift card. No se comparten con terceros.</p>
      <div class="field"><label class="f-label" for="bn">Nombre completo</label><input id="bn" class="input" data-f="name" autocomplete="name" value="${esc(s.name)}"${s.errors.name ? ' aria-invalid="true"' : ''}>${err('name')}</div>
      <div class="field"><label class="f-label" for="be">Correo electrónico</label><input id="be" type="email" class="input" data-f="email" autocomplete="email" value="${esc(s.email)}"${s.errors.email ? ' aria-invalid="true"' : ''}>${err('email')}</div>
      <div class="field"><label class="f-label" for="bp">Celular (con indicativo)</label><input id="bp" type="tel" class="input" data-f="phone" autocomplete="tel" placeholder="+57 300 000 0000" value="${esc(s.phone)}"${s.errors.phone ? ' aria-invalid="true"' : ''}>${err('phone')}</div>
      <div class="hp-field" aria-hidden="true"><label>No llenar<input type="text" name="website" tabindex="-1" autocomplete="off" data-hp></label></div>
      <label class="check"><input type="checkbox" data-consent${s.consent ? ' checked' : ''}>Autorizo el tratamiento de mis datos personales conforme a la Ley 1581 de 2012 y la política de tratamiento de datos.</label>${err('consent')}${s.sendError ? `<div class="err">${esc(s.sendError)}</div>` : ''}
      </div><div>
      <span class="f-label">Resumen</span>${face(designs[s.design][1], phrase())}
      <div class="kv"><div><span>${s.mode === 'ritual' ? 'Ritual' : 'Monto'}</span><b>${s.mode === 'ritual' && r ? esc(r.name) : 'Gift card'}</b></div><div><span>Valor</span><b>${cop(value())}</b></div><div><span>¿A quién se lo regala?</span><b>${esc(s.to)}</b></div><div><span>¿De parte de quién?</span><b>${esc(s.from)}</b></div></div>
      </div></div>
      <div class="acts"><button type="button" class="btn btn--ghost" data-go="1">← Volver</button><button type="button" class="btn btn--block" data-send${s.sending ? ' disabled' : ''}>${s.sending ? 'Enviando…' : 'Enviar solicitud'}</button></div></div>`;
  };

  const confirm = () => {
    const r = svc();
    const wa = `https://wa.me/${window.akzaraData.whatsapp}?text=${encodeURIComponent(`Hola, envié la solicitud de gift card ${s.done}.`)}`;
    const rows = [
      [s.mode === 'ritual' ? 'Ritual' : 'Monto', s.mode === 'ritual' && r ? r.name : 'Gift card'],
      ['Valor', cop(value())],
      ['¿A quién se lo regala?', s.to],
      ['¿De parte de quién?', s.from],
    ];
    return `<div class="panel confirm" id="flow-top" style="scroll-margin-top:90px"><div tabindex="-1" class="confirm__head" role="status" data-focus>
      <svg class="confirm__mark" viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="30" fill="none" stroke="currentColor" stroke-width="1"/><path d="M20 33.5 28.5 42 44.5 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
      <h2>Solicitud recibida.</h2><div class="confirm__ref">${esc(s.done)}</div>
      <p>Recibimos su solicitud (${esc(s.done)}). Le enviaremos el enlace de pago; cuando se confirme, generamos el código y la gift card.</p></div>
      <div class="confirm__visual"><div class="confirm__card">${face(designs[s.design][1], phrase())}</div></div>
      <dl class="confirm__rows">${rows.map((x) => `<div><dt>${esc(x[0])}</dt><dd>${esc(x[1])}</dd></div>`).join('')}</dl>
      <div class="confirm__acts"><a class="btn" href="${esc(wa)}" target="_blank" rel="noopener">Escribir al concierge</a><a class="btn btn--ghost" href="${esc(window.akzaraData.home)}">Inicio</a></div></div>`;
  };

  const render = (focus) => {
    root.innerHTML = s.done ? confirm() : `<div id="flow-top" style="scroll-margin-top:90px">${pills()}${[step0, step1, step2][s.step]()}</div>`;
    if (focus) {
      const top = $('#flow-top', root);
      if (top) top.scrollIntoView({ behavior: 'smooth', block: 'start' });
      const el = $('#step-panel', root) || $('[data-focus]', root);
      if (el) el.focus({ preventScroll: true });
    }
  };
  const go = (n) => {
    s.step = n;
    s.max = Math.max(s.max, n);
    render(true);
  };

  const validate2 = () => {
    s.errors = {};
    if (!s.to.trim()) s.errors.to = 'Este dato es obligatorio.';
    if (!s.from.trim()) s.errors.from = 'Este dato es obligatorio.';
    return !Object.keys(s.errors).length;
  };
  const validate3 = () => {
    s.errors = {};
    if (!s.name.trim()) s.errors.name = 'Este dato es obligatorio.';
    if (!EMAIL.test(s.email.trim())) s.errors.email = 'Revise el correo.';
    if (!PHONE.test(s.phone.trim())) s.errors.phone = 'Revise el celular.';
    if (!s.consent) s.errors.consent = 'Debe aceptar para continuar.';
    return !Object.keys(s.errors).length;
  };

  const send = async () => {
    if (!validate3()) return render(false);
    s.sending = true;
    s.sendError = '';
    render(false);
    try {
      const res = await fetch(window.akzaraData.ajax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.akzaraData.nonce },
        body: JSON.stringify({
          mode: s.mode,
          service: s.mode === 'ritual' ? s.serviceId : undefined,
          value: value(),
          design: s.design,
          cardLanguage: s.lang,
          recipient: s.to.trim(),
          from: s.from.trim(),
          message: s.msg.slice(0, 300),
          buyerName: s.name.trim(),
          buyerEmail: s.email.trim(),
          buyerPhone: s.phone.trim(),
          consent: s.consent,
          website: ($('[data-hp]', root) || {}).value || '',
        }),
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(json.message || 'error');
      s.done = json.reference || '—';
      s.sending = false;
      render(true);
    } catch (e) {
      s.sending = false;
      s.sendError = e.message && e.message !== 'error' ? e.message : 'No pudimos enviar la solicitud. Intente de nuevo o escríbanos por WhatsApp.';
      render(false);
    }
  };

  root.addEventListener('click', (e) => {
    const t = e.target.closest('button');
    if (!t || t.disabled) return;
    if (t.dataset.go !== undefined) return go(Number(t.dataset.go));
    if (t.dataset.mode) {
      s.mode = t.dataset.mode;
      return render(false);
    }
    if (t.dataset.pick) {
      s.serviceId = t.dataset.pick;
      return render(false);
    }
    if (t.dataset.amount) {
      s.amount = Number(t.dataset.amount);
      s.custom = '';
      return render(false);
    }
    if (t.dataset.design) {
      s.design = (s.design + Number(t.dataset.design) + designs.length) % designs.length;
      return render(false);
    }
    if (t.dataset.thumb) {
      s.design = Number(t.dataset.thumb);
      return render(false);
    }
    if (t.dataset.lang) {
      s.lang = t.dataset.lang;
      return render(false);
    }
    if (t.hasAttribute('data-next2')) return validate2() ? go(2) : render(false);
    if (t.hasAttribute('data-send')) return send();
  });

  // Los campos de texto no vuelven a dibujar todo (se perdería el cursor): solo actualizan lo que cambia.
  root.addEventListener('input', (e) => {
    const t = e.target;
    if (t.matches('[data-query]')) {
      s.query = t.value;
      const pos = t.selectionStart;
      render(false);
      const again = $('[data-query]', root);
      if (again) {
        again.focus();
        again.setSelectionRange(pos, pos);
      }
    } else if (t.matches('[data-custom]')) {
      s.custom = t.value.replace(/\D/g, '');
      t.value = s.custom ? cop(s.custom) : '';
      $$('[data-amount]', root).forEach((b) => b.setAttribute('aria-pressed', 'false'));
      const go0 = $('.acts .btn', root);
      if (go0) go0.disabled = value() < cfg.min;
    } else if (t.dataset.f) {
      s[t.dataset.f] = t.value;
      if (t.dataset.f === 'msg') {
        const c = $('[data-counter]', root);
        if (c) c.textContent = `${s.msg.length} / 300`;
        const m = $('[data-back-msg]', root);
        if (m) {
          m.textContent = s.msg || CARD[s.lang].messagePh;
          m.classList.toggle('empty', !s.msg);
        }
      }
      if (t.dataset.f === 'to' || t.dataset.f === 'from') {
        const n = $('[data-back-names]', root);
        if (n) n.innerHTML = backNames(CARD[s.lang]);
      }
    }
  });
  root.addEventListener('change', (e) => {
    if (e.target.matches('[data-consent]')) s.consent = e.target.checked;
  });

  render(Boolean(init));
}
