/* Escritorio de WordPress: galería, pasos del ritual e imagen de la categoría (selector de la Mediateca). */
(function ($) {
  if (!$ || !window.wp || !wp.media) return;

  const pick = (opts, cb) => {
    const frame = wp.media({ title: opts.title, button: { text: opts.button }, library: { type: 'image' }, multiple: !!opts.multiple });
    frame.on('select', () => cb(frame.state().get('selection').toJSON()));
    frame.open();
  };
  const thumbUrl = (a) => (a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url);

  // Galería del ritual.
  const gallery = $('.akz-gallery');
  if (gallery.length) {
    const input = $('#' + gallery.data('input'));
    const sync = () => input.val(gallery.find('.akz-thumb').map((i, el) => $(el).data('id')).get().join(','));
    gallery.sortable({ update: sync });
    gallery.on('click', '.akz-x', function () {
      $(this).closest('.akz-thumb').remove();
      sync();
    });
    $('.akz-add-gallery').on('click', () =>
      pick({ title: 'Fotos del ritual', button: 'Añadir', multiple: true }, (items) => {
        items.forEach((a) => {
          if (gallery.find('[data-id="' + a.id + '"]').length) return;
          gallery.append($('<span class="akz-thumb"/>').attr('data-id', a.id).data('id', a.id).append($('<img/>').attr('src', thumbUrl(a)), '<button type="button" class="akz-x" aria-label="Quitar">×</button>'));
        });
        sync();
      })
    );
  }

  // Pasos del ritual.
  const steps = $('.akz-steps');
  if (steps.length) {
    let n = steps.children().length + 1000;
    $('.akz-add-step').on('click', () => {
      const tpl = $('#akz-step-tpl').html().replace(/__i__/g, String(n++));
      steps.append(tpl);
    });
    steps.sortable({ items: '.akz-step', handle: 'input[type=text]', cancel: '' });
    steps.on('click', '.akz-step-del', function () {
      $(this).closest('.akz-step').remove();
    });
    steps.on('click', '.akz-step-pick', function () {
      const row = $(this).closest('.akz-step');
      pick({ title: 'Foto del paso', button: 'Usar esta foto' }, (items) => {
        const a = items[0];
        row.find('.akz-step-id').val(a.id);
        row.find('.akz-step-img').empty().append($('<img/>').attr('src', thumbUrl(a)));
      });
    });
  }

  // Imagen de la categoría.
  $(document).on('click', '.akz-term-pick', function () {
    const box = $(this).closest('.akz-term-img');
    pick({ title: 'Imagen de la categoría', button: 'Usar esta imagen' }, (items) => {
      const a = items[0];
      box.find('input[type=hidden]').val(a.id);
      box.find('.akz-term-thumb').empty().append($('<img/>').attr('src', thumbUrl(a)));
    });
  });
})(window.jQuery);
