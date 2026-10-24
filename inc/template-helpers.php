<?php
/**
 * Datos y piezas de HTML compartidos por las plantillas (menús, bloques, tarjetas).
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ¿La página actual usa esta plantilla (asignada o por el slug)?
 *
 * @param string|array $files Archivo(s), p. ej. 'page-gift-card.php'.
 * @return bool
 */
function akzara_is_template( $files ) {
	if ( ! is_page() ) {
		return false;
	}
	return in_array( basename( (string) get_page_template() ), (array) $files, true );
}

/**
 * Flecha pequeña de los menús desplegables.
 *
 * @return string
 */
function akzara_chevron() {
	return '<svg viewBox="0 0 10 10" aria-hidden="true"><path d="M2 3.5 5 6.5 8 3.5" fill="none" stroke="currentColor" stroke-width="1.2"/></svg>';
}

/**
 * Enlaces del menú «El Club»: usa el menú de Apariencia → Menús y, si no existe, los de siempre.
 *
 * @return array [[etiqueta, url], …]
 */
function akzara_club_links() {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['akzara_club'] ) ) {
		$items = wp_get_nav_menu_items( $locations['akzara_club'] );
		if ( $items ) {
			$out = array();
			foreach ( $items as $it ) {
				$out[] = array( $it->title, $it->url );
			}
			return $out;
		}
	}
	return array(
		array( 'Instalaciones', home_url( '/instalaciones/' ) ),
		array( 'Filosofía', home_url( '/el-club/' ) ),
		array( 'Diario', home_url( '/diario/' ) ),
		array( 'Contacto', home_url( '/contacto/' ) ),
	);
}

/**
 * Enlaces legales del pie (menú «Pie · legal» o las cuatro políticas).
 *
 * @return array [[etiqueta, url], …]
 */
function akzara_legal_links() {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['akzara_legal'] ) ) {
		$items = wp_get_nav_menu_items( $locations['akzara_legal'] );
		if ( $items ) {
			$out = array();
			foreach ( $items as $it ) {
				$out[] = array( $it->title, $it->url );
			}
			return $out;
		}
	}
	return array(
		array( 'Políticas de reserva', home_url( '/politicas/reserva/' ) ),
		array( 'Privacidad', home_url( '/politicas/privacidad/' ) ),
		array( 'Términos', home_url( '/politicas/terminos/' ) ),
		array( 'Tratamiento de datos', home_url( '/politicas/tratamiento-de-datos/' ) ),
	);
}

/**
 * Tarjeta de ritual (listados, destacados y relacionados).
 *
 * @param array  $r     Ritual normalizado (akzara_ritual).
 * @param string $ratio Proporción de la foto.
 */
function akzara_ritual_card( $r, $ratio = 'r-4x5' ) {
	$occ = implode( ' ', array_map( 'sanitize_html_class', $r['occasions'] ) );
	?>
	<article class="rcard hover-zoom reveal" data-occ="<?php echo esc_attr( $occ ); ?>" data-min="<?php echo (int) $r['min']; ?>">
		<a href="<?php echo esc_url( $r['url'] ); ?>" aria-label="<?php echo esc_attr( $r['name'] ); ?>" tabindex="-1" class="rcard__img">
			<?php if ( $r['badge'] ) : ?>
				<span class="badge"><?php echo esc_html( $r['badge'] ); ?></span>
			<?php endif; ?>
			<?php
			echo akzara_media( $r['thumb'], $r['tone'], $ratio, array( 'size' => 'akzara-card', 'sizes' => '(max-width: 760px) 100vw, (max-width: 1200px) 50vw, 33vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			if ( $r['summary'] ) :
				?>
				<span class="rcard__peek"><?php echo esc_html( $r['summary'] ); ?></span>
			<?php endif; ?>
		</a>
		<span class="eyebrow"><?php echo esc_html( $r['cat_name'] ); ?></span>
		<h3 class="h-card"><a href="<?php echo esc_url( $r['url'] ); ?>"><?php echo esc_html( $r['name'] ); ?></a></h3>
		<div class="meta">
			<span><?php echo esc_html( akzara_duration( $r['min'] ) ); ?></span>
			<span class="meta__price">desde <?php echo esc_html( akzara_cop( $r['price'] ) ); ?></span>
		</div>
		<div class="actions">
			<a class="btn" href="<?php echo esc_url( add_query_arg( 'ritual', $r['slug'], home_url( '/reservar/' ) ) ); ?>">Reservar</a>
			<?php if ( $r['giftable'] ) : ?>
				<a class="btn btn--ghost" href="<?php echo esc_url( add_query_arg( 'ritual', $r['slug'], home_url( '/gift-card/' ) ) ); ?>">Regalar</a>
			<?php endif; ?>
		</div>
	</article>
	<?php
}

/**
 * Lista de preguntas frecuentes (<details>).
 *
 * @param array $items [[pregunta, respuesta], …].
 */
function akzara_faq_list( $items ) {
	echo '<div class="faq">';
	foreach ( $items as $f ) {
		echo '<details class="faq__item"><summary><span>' . esc_html( $f[0] ) . '</span><i aria-hidden="true"></i></summary><p>' . esc_html( $f[1] ) . '</p></details>';
	}
	echo '</div>';
	akzara_faq_jsonld( $items );
}

/**
 * Planes de membresía.
 */
function akzara_plans() {
	$plans = akzara_memberships();
	echo '<div class="plans">';
	foreach ( $plans as $m ) {
		echo '<article class="plan' . ( $m['highlight'] ? ' plan--hl' : '' ) . '">';
		echo '<span class="eyebrow">' . esc_html( $m['period'] ) . '</span>';
		echo '<h3>' . esc_html( $m['name'] ) . '</h3>';
		echo '<div class="price">' . esc_html( akzara_cop( $m['price'] ) ) . '</div><ul>';
		foreach ( $m['benefits'] as $b ) {
			echo '<li>' . esc_html( $b ) . '</li>';
		}
		echo '</ul><a class="btn btn--block' . ( $m['highlight'] ? '' : ' btn--ghost' ) . '" href="' . esc_url( akzara_wa_link( '', 'Hola, quisiera conocer la membresía ' . $m['name'] . '.' ) ) . '" target="_blank" rel="noopener">' . esc_html( $m['button'] ) . '</a></article>';
	}
	echo '</div>';
}

/**
 * Bloque «Visítenos» (mapa, dirección, horario y, opcionalmente, contacto).
 *
 * @param bool $extra Mostrar contacto y nota de llegada.
 */
function akzara_visit_info( $extra = false ) {
	?>
	<div class="visit">
		<a class="mapbox" href="<?php echo esc_url( akzara_opt( 'maps_url' ) ); ?>" target="_blank" rel="noopener">
			<span><img src="<?php echo esc_url( akzara_img_uri( 'isotipo.webp' ) ); ?>" alt="" width="40" height="37" loading="lazy">Abrir en Google Maps</span>
		</a>
		<dl class="info">
			<dt>Dirección</dt>
			<dd><?php echo esc_html( akzara_opt( 'address' ) ); ?></dd>
			<dt>Horario</dt>
			<dd class="hours">
				<?php foreach ( akzara_hours() as $h ) : ?>
					<span><?php echo esc_html( $h[0] ); ?></span><span><?php echo esc_html( $h[1] ); ?></span>
				<?php endforeach; ?>
			</dd>
			<?php if ( $extra ) : ?>
				<dt>Contacto</dt>
				<dd>
					<a href="<?php echo esc_url( akzara_wa_link( '', akzara_opt( 'whatsapp_message' ) ) ); ?>" target="_blank" rel="noopener">WhatsApp concierge <?php echo esc_html( akzara_opt( 'whatsapp_display' ) ); ?></a><br>
					<a href="mailto:<?php echo esc_attr( antispambot( akzara_opt( 'email' ) ) ); ?>"><?php echo esc_html( akzara_opt( 'email' ) ); ?></a>
				</dd>
				<dd class="muted" style="font-size:11px;margin-top:24px"><?php echo esc_html( akzara_opt( 'arrival_note' ) ); ?></dd>
			<?php endif; ?>
		</dl>
	</div>
	<?php
}

/**
 * Frente de una gift card (vista previa y miniaturas).
 *
 * @param string $variant lino|jardin|grafito|bronce.
 * @param string $phrase  Frase.
 * @return string
 */
function akzara_gift_face( $variant = 'lino', $phrase = '' ) {
	$style = 'bronce' === $variant ? ' style="filter:brightness(.55) saturate(.7)"' : '';
	return '<div class="gc gc--' . esc_attr( $variant ) . '"><div class="gc__logo"><img src="' . esc_url( akzara_img_uri( 'isotipo.webp' ) ) . '" alt="" width="40" height="37"' . $style . '><b>AKZARA</b><small>WELLNESS CLUB</small></div>' . ( $phrase ? '<div class="gc__phrase">' . esc_html( $phrase ) . '</div>' : '' ) . '</div>';
}

/**
 * Bloques reutilizables de las páginas (se llaman desde las plantillas).
 */

/**
 * Cierre oscuro con un botón.
 *
 * @param string $title  Título con *cursiva*.
 * @param string $button Texto del botón.
 * @param string $link   Ruta o URL.
 */
function akzara_closing( $title, $button, $link ) {
	?>
	<section class="section--dark closing">
		<div class="container">
			<h2 class="h-section"><?php echo akzara_italic( $title ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<a class="btn btn--outline-light" href="<?php echo esc_url( akzara_link( $link ) ); ?>"><?php echo esc_html( $button ); ?></a>
		</div>
	</section>
	<?php
}

/**
 * Galería horizontal.
 *
 * @param string $title Título.
 * @param array  $ids   IDs de adjuntos (vacío = muestras provisionales).
 */
function akzara_hscroll( $title, $ids = array() ) {
	$tones = array( 'jardin', 'arena', 'humo', 'jardin', 'arena' );
	?>
	<section class="section--tight" style="padding-bottom:clamp(56px,8vw,100px)">
		<div class="container">
			<h2 class="serif" style="font-size:22px;margin:0 0 20px"><?php echo akzara_italic( $title ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		</div>
		<div class="hscroll">
			<?php
			if ( $ids ) {
				foreach ( $ids as $id ) {
					echo akzara_media( $id, 'arena', '', array( 'sizes' => '(max-width: 760px) 80vw, 33vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
			} else {
				foreach ( $tones as $t ) {
					echo akzara_media( 0, $t ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
			}
			?>
		</div>
	</section>
	<?php
}
