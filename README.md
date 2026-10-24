# Akzara Wellness Club · tema de WordPress

Tema clásico (PHP, sin constructores de páginas ni plugins de pago) que replica el sitio de Akzara hecho en Next.js.
La **raíz de este repositorio es la carpeta del tema**: en el servidor va en `wp-content/themes/akzara`.

## Instalar

1. Copie (o conecte por Git) esta carpeta a `public_html/wp-content/themes/akzara`.
2. En **Apariencia → Temas**, active «Akzara Wellness Club».
3. Al activarlo se crea solo el contenido de muestra: categorías, 42 rituales, membresías, preguntas, entradas, páginas, menús y 8 fotos de referencia.
   Si falta algo, **Herramientas → Contenido de Akzara** lo vuelve a crear (no toca lo que ya existe).
4. **Ajustes → Enlaces permanentes**: no hace falta cambiarlos; el tema deja `/diario/%postname%/`.
5. Plugins recomendados: **Amelia** (reservas) y uno de SEO (Yoast, Rank Math, SEOPress o AIOSEO). Si hay un plugin de SEO activo, el tema no repite las etiquetas.

> Las fotos de muestra están marcadas «reemplazar con foto real» en la Mediateca. Súbalas del club y cámbielas en el Personalizador y en cada ritual.

## Dónde se edita cada cosa

| Qué | Dónde |
| --- | --- |
| Textos, imágenes, colores, horario, WhatsApp, aviso emergente, Pixel/Analytics | **Apariencia → Personalizar → Akzara · Contenido del sitio** |
| Rituales (precio, duración, fotos, pasos, incluye) | **Rituales** |
| Categorías de rituales (texto del menú, foto, orden) | **Rituales → Categorías** |
| Membresías, preguntas frecuentes, testimonios | Sus menús propios (un testimonio publicado = autorizado) |
| Menú «El Club» y enlaces legales del pie | **Apariencia → Menús** |
| Logo | Personalizar → Identidad del sitio |
| Solicitudes de gift card | **Gift cards** (al pasar a «Pagada» se crea el código y se avisa al comprador) |
| Reservas | Página **Reservar**: pegue el shortcode de Amelia en su contenido |

## Seguimiento (Meta Pixel, Analytics, Tag Manager)

Personalizar → **Seguimiento**. Solo se escribe el identificador; vacío = no se carga nada. No se cargan para quien edita el sitio.

## Idioma

El sitio sale solo en español. El selector ES / EN existe pero está apagado (Personalizar → Idioma). Para el inglés se recomienda Polylang; los enlaces se ajustan con el filtro `akzara_lang_url`.

## Desarrollo

El CSS y el JavaScript compilados están en el repositorio (el servidor no ejecuta npm). Para cambiarlos:

```bash
npm install
npm run build
```

- `assets/css/src/` → `assets/css/main.css` (minificado)
- `assets/js/src/` → `assets/js/main.js` y `assets/js/admin.js`
- Fuentes propias (Cormorant Garamond e Inter) en `assets/fonts/`; imágenes en `assets/img/` (WebP).

## Estructura

```
style.css  functions.php  header.php  footer.php
front-page.php  page.php  index.php  404.php  home.php  single.php
archive-akzara_ritual.php  taxonomy-akzara_categoria.php  single-akzara_ritual.php
page-reservas.php  page-gift-card.php  page-membresias.php  page-instalaciones.php
page-el-club.php  page-contacto.php  page-politicas.php
template-parts/   inc/ (cpt, campos, Personalizador, SEO, seguimiento, gift card, contenido de muestra)
```
