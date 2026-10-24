<?php
/**
 * Funciones auxiliares: valores del Personalizador, formato, imágenes y piezas de HTML.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valor de un campo del Personalizador (con su valor por defecto).
 *
 * @param string $key Clave sin prefijo.
 * @return mixed
 */
function akzara_opt( $key ) {
	$fields  = akzara_fields();
	$default = isset( $fields[ $key ][3] ) ? $fields[ $key ][3] : '';
	$value   = get_theme_mod( 'akzara_' . $key, null );
	if ( null === $value || '' === $value ) {
		return $default;
	}
	return $value;
}

/**
 * Escapa un texto y convierte *palabra* en <em>palabra</em>.
 *
 * @param string $text Texto con asteriscos.
 * @return string HTML seguro.
 */
function akzara_italic( $text ) {
	$parts = preg_split( '/(\*[^*]+\*)/u', (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$out   = '';
	foreach ( $parts as $p ) {
		if ( strlen( $p ) > 2 && '*' === $p[0] && '*' === substr( $p, -1 ) ) {
			$out .= '<em>' . esc_html( substr( $p, 1, -1 ) ) . '</em>';
		} else {
			$out .= esc_html( $p );
		}
	}
	return $out;
}

/**
 * Igual que akzara_italic, pero cada palabra sube sola al aparecer (título de la portada).
 *
 * @param string $text Texto con asteriscos.
 * @return string HTML seguro.
 */
function akzara_split_italic( $text ) {
	$n     = 0;
	$words = function ( $s ) use ( &$n ) {
		$out = '';
		foreach ( preg_split( '/(\s+)/u', $s, -1, PREG_SPLIT_DELIM_CAPTURE ) as $w ) {
			if ( '' === $w ) {
				continue;
			}
			if ( preg_match( '/^\s+$/u', $w ) ) {
				$out .= ' ';
			} else {
				$out .= '<span class="w" style="--i:' . (int) $n++ . '"><span>' . esc_html( $w ) . '</span></span>';
			}
		}
		return $out;
	};
	$parts = preg_split( '/(\*[^*]+\*)/u', (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$out   = '';
	foreach ( $parts as $p ) {
		if ( strlen( $p ) > 2 && '*' === $p[0] && '*' === substr( $p, -1 ) ) {
			$out .= '<em>' . $words( substr( $p, 1, -1 ) ) . '</em>';
		} else {
			$out .= $words( $p );
		}
	}
	return $out;
}

/**
 * Texto sin asteriscos.
 *
 * @param string $text Texto.
 * @return string
 */
function akzara_plain( $text ) {
	return str_replace( '*', '', (string) $text );
}

/**
 * Precio en pesos: $ 280.000
 *
 * @param float|int|string $n Valor.
 * @return string
 */
function akzara_cop( $n ) {
	return '$ ' . number_format( (float) $n, 0, ',', '.' );
}

/**
 * Duración: 80 → 1 h 20', 60 → 1 h, 35 → 35 min.
 *
 * @param int $min Minutos.
 * @return string
 */
function akzara_duration( $min ) {
	$min = (int) $min;
	if ( $min < 60 ) {
		return $min . ' min';
	}
	$h = intdiv( $min, 60 );
	$m = $min % 60;
	return $m ? $h . " h {$m}'" : $h . ' h';
}

/**
 * Enlace de WhatsApp.
 *
 * @param string $phone   Teléfono.
 * @param string $message Mensaje opcional.
 * @return string
 */
function akzara_wa_link( $phone = '', $message = '' ) {
	$phone = preg_replace( '/\D/', '', $phone ? $phone : (string) akzara_opt( 'whatsapp' ) );
	$url   = 'https://wa.me/' . $phone;
	if ( $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}
	return $url;
}

/**
 * Enlace interno: acepta rutas relativas («/reservar/») o URL completas.
 *
 * @param string $link Ruta o URL.
 * @return string
 */
function akzara_link( $link ) {
	$link = (string) $link;
	if ( '' === $link ) {
		return home_url( '/' );
	}
	if ( 0 === strpos( $link, '/' ) ) {
		return home_url( $link );
	}
	return esc_url_raw( $link );
}

/**
 * URL de un archivo del tema (assets/img/…).
 *
 * @param string $path Ruta dentro de assets/img.
 * @return string
 */
function akzara_img_uri( $path ) {
	return AKZARA_URI . '/assets/img/' . ltrim( $path, '/' );
}

/**
 * Horario del pie y del contacto: [[días, horas], …].
 *
 * @return array
 */
function akzara_hours() {
	$rows = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) akzara_opt( 'hours' ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$bits   = array_map( 'trim', explode( '|', $line, 2 ) );
		$rows[] = array( $bits[0], isset( $bits[1] ) ? $bits[1] : '' );
	}
	return $rows;
}

/**
 * Valor de un campo de imagen del Personalizador como ID de adjunto (o 0).
 *
 * @param string $key Clave.
 * @return int
 */
function akzara_opt_img( $key ) {
	return (int) akzara_opt( $key );
}

/**
 * Etiqueta <img> de un adjunto, con alt, carga diferida y clase de fundido.
 *
 * @param int    $id    ID del adjunto.
 * @param string $size  Tamaño registrado.
 * @param array  $args  priority (bool), sizes (string), class (string).
 * @return string HTML seguro ('' si no hay imagen).
 */
function akzara_img( $id, $size = 'akzara-wide', $args = array() ) {
	$id = (int) $id;
	if ( ! $id ) {
		return '';
	}
	$src = wp_get_attachment_image_src( $id, $size );
	if ( ! $src ) {
		return '';
	}
	$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	if ( '' === $alt ) {
		$alt = get_the_title( $id );
	}
	$attr = array(
		'class'    => trim( 'fade ' . ( isset( $args['class'] ) ? $args['class'] : '' ) ),
		'alt'      => $alt,
		'sizes'    => isset( $args['sizes'] ) ? $args['sizes'] : '(min-width: 960px) 33vw, 100vw',
		'decoding' => 'async',
	);
	if ( ! empty( $args['priority'] ) ) {
		$attr['loading']       = 'eager';
		$attr['fetchpriority'] = 'high';
	} else {
		$attr['loading'] = 'lazy';
	}
	$html = wp_get_attachment_image( $id, $size, false, $attr );
	return $html ? $html : '';
}

/**
 * Imagen provisional: degradado con ondas en el tono de marca.
 *
 * @param string $tone arena|humo|jardin|dark.
 * @return string
 */
function akzara_placeholder( $tone = 'arena' ) {
	$tone = in_array( $tone, array( 'arena', 'humo', 'jardin', 'dark' ), true ) ? $tone : 'arena';
	return '<div class="ph ph--' . esc_attr( $tone ) . '" aria-hidden="true"><svg viewBox="0 0 400 180" preserveAspectRatio="none"><path d="M0 70 C 80 40, 160 40, 230 62 S 340 92, 400 64 L400 180 L0 180 Z" fill="rgba(255,255,255,.10)"/><path d="M0 112 C 90 86, 170 94, 250 112 S 350 132, 400 108 L400 180 L0 180 Z" fill="rgba(255,255,255,.10)"/></svg></div>';
}

/**
 * Bloque .media con foto o con la imagen provisional.
 *
 * @param int    $id    Adjunto.
 * @param string $tone  Tono provisional.
 * @param string $ratio r-4x5, r-3x4, r-4x3, r-1x1, r-16x9 (o '').
 * @param array  $args  Ver akzara_img(), más class (string).
 * @return string HTML seguro.
 */
function akzara_media( $id, $tone = 'arena', $ratio = '', $args = array() ) {
	$size  = isset( $args['size'] ) ? $args['size'] : 'akzara-wide';
	$img   = akzara_img( $id, $size, $args );
	$class = trim( 'media ' . $ratio . ' ' . ( isset( $args['wrap_class'] ) ? $args['wrap_class'] : '' ) );
	return '<div class="' . esc_attr( $class ) . '">' . ( $img ? $img : akzara_placeholder( $tone ) ) . '</div>';
}

/**
 * Migas de pan + antetítulo + título + frase (cabecera de las páginas interiores).
 *
 * @param array  $crumbs  [[etiqueta, url|null], …].
 * @param string $title   Título.
 * @param string $eyebrow Antetítulo.
 * @param string $intro   Frase.
 */
function akzara_page_head( $crumbs, $title, $eyebrow = '', $intro = '' ) {
	echo '<header class="pagehead">';
	akzara_crumbs( $crumbs );
	if ( $eyebrow ) {
		echo '<span class="eyebrow eyebrow--bronze">' . esc_html( $eyebrow ) . '</span>';
	}
	echo '<h1 class="h-caps">' . esc_html( $title ) . '</h1>';
	if ( $intro ) {
		echo '<p>' . esc_html( $intro ) . '</p>';
	}
	echo '</header>';
}

/**
 * Migas de pan.
 *
 * @param array $items [[etiqueta, url|null], …].
 */
function akzara_crumbs( $items ) {
	echo '<nav class="crumbs" aria-label="' . esc_attr__( 'Migas de pan', 'akzara' ) . '">';
	foreach ( $items as $i => $c ) {
		if ( $i > 0 ) {
			echo '<span aria-hidden="true">/</span>';
		}
		if ( ! empty( $c[1] ) ) {
			echo '<a href="' . esc_url( $c[1] ) . '">' . esc_html( $c[0] ) . '</a>';
		} else {
			echo '<span aria-current="page">' . esc_html( $c[0] ) . '</span>';
		}
	}
	echo '</nav>';
}

/**
 * Icono de WhatsApp.
 *
 * @return string
 */
function akzara_whatsapp_icon() {
	return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.3c0-.1-.2-.2-.5-.3Z"/></svg>';
}

/**
 * Datos normalizados de un ritual (equivale al tipo Service del sitio Next.js).
 *
 * @param int|WP_Post $post Ritual.
 * @return array
 */
function akzara_ritual( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}
	$id    = $post->ID;
	$cats  = get_the_terms( $id, 'akzara_categoria' );
	$cat   = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0] : null;
	$occs  = get_the_terms( $id, 'akzara_ocasion' );
	$gal   = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $id, '_ak_gallery', true ) ) ) );
	$steps = json_decode( (string) get_post_meta( $id, '_ak_steps', true ), true );
	$thumb = (int) get_post_thumbnail_id( $id );
	if ( ! $thumb && $gal ) {
		$thumb = (int) reset( $gal );
	}
	$mode = get_post_meta( $id, '_ak_mode', true );

	return array(
		'id'            => $id,
		'slug'          => $post->post_name,
		'url'           => get_permalink( $id ),
		'name'          => get_the_title( $id ),
		'summary'       => $post->post_excerpt,
		'description'   => $post->post_content,
		'cat_name'      => $cat ? $cat->name : '',
		'cat_slug'      => $cat ? $cat->slug : '',
		'cat_url'       => $cat ? get_term_link( $cat ) : '',
		'occasions'     => ( $occs && ! is_wp_error( $occs ) ) ? wp_list_pluck( $occs, 'slug' ) : array(),
		'min'           => (int) get_post_meta( $id, '_ak_min', true ),
		'price'         => (float) get_post_meta( $id, '_ak_price', true ),
		'mode'          => 'grupo' === $mode ? 'grupo' : 'persona',
		'min_people'    => max( 1, (int) get_post_meta( $id, '_ak_minp', true ) ),
		'max_people'    => max( 1, (int) get_post_meta( $id, '_ak_maxp', true ) ),
		'badge'         => (string) get_post_meta( $id, '_ak_badge', true ),
		'featured'      => (bool) get_post_meta( $id, '_ak_featured', true ),
		'signature'     => (bool) get_post_meta( $id, '_ak_signature', true ),
		'giftable'      => '0' !== (string) get_post_meta( $id, '_ak_giftable', true ),
		'tone'          => (string) ( get_post_meta( $id, '_ak_tone', true ) ? get_post_meta( $id, '_ak_tone', true ) : 'arena' ),
		'thumb'         => $thumb,
		'gallery'       => array_values( $gal ),
		'steps'         => is_array( $steps ) ? $steps : array(),
		'includes'      => array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $id, '_ak_includes', true ) ) ) ) ),
		'contra'        => (string) get_post_meta( $id, '_ak_contra', true ),
		'related'       => array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $id, '_ak_related', true ) ) ) ),
		'order'         => (int) $post->menu_order,
	);
}

/**
 * Lista de rituales publicados, ya normalizados.
 *
 * @param array $args Argumentos extra de WP_Query.
 * @return array
 */
function akzara_rituals( $args = array() ) {
	$q = new WP_Query(
		wp_parse_args(
			$args,
			array(
				'post_type'      => 'akzara_ritual',
				'post_status'    => 'publish',
				'posts_per_page' => 200,
				'orderby'        => array(
					'meta_value_num' => 'ASC',
					'title'          => 'ASC',
				),
				'meta_key'       => '_ak_price', // phpcs:ignore WordPress.DB.SlowDBQuery
				'no_found_rows'  => true,
			)
		)
	);
	$out = array();
	foreach ( $q->posts as $p ) {
		$out[] = akzara_ritual( $p );
	}
	wp_reset_postdata();
	return $out;
}
