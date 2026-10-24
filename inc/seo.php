<?php
/**
 * SEO básico: títulos, descripciones, Open Graph, datos estructurados y robots.
 * Si hay un plugin de SEO activo (Yoast, Rank Math, SEOPress) el tema no duplica etiquetas.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ¿Hay un plugin de SEO activo?
 *
 * @return bool
 */
function akzara_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Título del documento: «Rituales · Akzara Wellness Club»; en el inicio, «Akzara Wellness Club · Spa en Medellín».
 *
 * @param array $parts Partes.
 * @return array
 */
function akzara_title_parts( $parts ) {
	if ( is_front_page() ) {
		return array(
			'title'   => 'Akzara Wellness Club',
			'tagline' => 'Spa en Medellín',
		);
	}
	unset( $parts['tagline'] );
	$parts['site'] = 'Akzara Wellness Club';
	return $parts;
}
add_filter( 'document_title_parts', 'akzara_title_parts' );
add_filter(
	'document_title_separator',
	function () {
		return '·';
	}
);

/**
 * Descripción de la página actual (140–160 caracteres, ideal).
 *
 * @return string
 */
function akzara_meta_description() {
	$by_slug = array(
		'instalaciones' => 'Jardín, piscina exterior, jacuzzis, sauna y baño turco en la Milla de Oro.',
		'membresias'    => 'Aqua, Plata y Dorada: el bienestar como hábito, no como excepción.',
		'contacto'      => 'Dirección, horario y WhatsApp del concierge de Akzara Wellness Club.',
		'el-club'       => 'Filosofía de Akzara: el lujo no hace ruido.',
		'gift-card'     => 'Regale calma: personalice una gift card de Akzara Wellness Club.',
		'reservar'      => 'Solicite su ritual en Akzara Wellness Club, El Poblado, Medellín.',
	);

	if ( is_front_page() ) {
		return 'Spa en Medellín: el único jardín de la Milla de Oro. Rituales, masajes y membresías en El Poblado.';
	}
	if ( is_singular( 'akzara_ritual' ) ) {
		$r = akzara_ritual( get_queried_object() );
		return trim( ( $r['summary'] ? $r['summary'] . ' ' : '' ) . akzara_duration( $r['min'] ) . ' · desde ' . akzara_cop( $r['price'] ) . '.' );
	}
	if ( is_post_type_archive( 'akzara_ritual' ) ) {
		return 'Rituales, masajes y tratamientos de Akzara Wellness Club en El Poblado, Medellín.';
	}
	if ( is_tax( 'akzara_categoria' ) ) {
		$intro = (string) get_term_meta( get_queried_object_id(), '_ak_intro', true );
		return $intro ? $intro : 'Rituales de Akzara Wellness Club en El Poblado, Medellín.';
	}
	if ( is_home() ) {
		return 'Notas sobre bienestar, agua y calma.';
	}
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && $post->post_excerpt ) {
			return wp_strip_all_tags( $post->post_excerpt );
		}
		if ( $post && isset( $by_slug[ $post->post_name ] ) ) {
			return $by_slug[ $post->post_name ];
		}
		if ( $post && $post->post_content ) {
			return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 26, '…' );
		}
	}
	return (string) akzara_opt( 'tagline' );
}

/**
 * Imagen para compartir.
 *
 * @return string URL.
 */
function akzara_share_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'akzara-wide' );
		if ( $src ) {
			return $src[0];
		}
	}
	if ( is_singular( 'akzara_ritual' ) ) {
		$r = akzara_ritual( get_queried_object() );
		if ( $r['thumb'] ) {
			$src = wp_get_attachment_image_src( $r['thumb'], 'akzara-wide' );
			if ( $src ) {
				return $src[0];
			}
		}
	}
	return akzara_img_uri( 'og.jpg' );
}

/**
 * Meta descripción, Open Graph y Twitter.
 */
function akzara_head_meta() {
	if ( akzara_seo_plugin_active() ) {
		return;
	}
	$desc  = akzara_meta_description();
	$title = wp_get_document_title();
	$url   = home_url( '/' );
	if ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_tax() ) {
		$link = get_term_link( get_queried_object() );
		$url  = is_wp_error( $link ) ? $url : $link;
	} elseif ( is_post_type_archive() ) {
		$link = get_post_type_archive_link( get_query_var( 'post_type' ) );
		$url  = $link ? $link : $url;
	} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
		$url = get_permalink( (int) get_option( 'page_for_posts' ) );
	}
	$type = is_singular( 'post' ) ? 'article' : 'website';
	$img  = akzara_share_image();

	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:locale" content="es_CO">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:site_name" content="Akzara Wellness Club">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="theme-color" content="' . esc_attr( akzara_opt( 'c_ink' ) ) . '">' . "\n";
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" href="' . esc_url( akzara_img_uri( 'favicon.png' ) ) . '" type="image/png">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( akzara_img_uri( 'favicon.png' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'akzara_head_meta', 2 );

/**
 * Imprime un bloque JSON-LD.
 *
 * @param array $data Datos.
 */
function akzara_jsonld( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Datos estructurados: el spa (todas las páginas) y, según la página, ritual, artículo o preguntas.
 */
function akzara_structured_data() {
	if ( akzara_seo_plugin_active() ) {
		return;
	}
	$site = home_url( '/' );
	$logo = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' ) : akzara_img_uri( 'logo.webp' );

	$spa = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'DaySpa',
		'name'            => 'Akzara Wellness Club',
		'url'             => $site,
		'telephone'       => (string) akzara_opt( 'whatsapp_display' ),
		'email'           => (string) akzara_opt( 'email' ),
		'image'           => akzara_img_uri( 'og.jpg' ),
		'logo'            => $logo,
		'description'     => (string) akzara_opt( 'tagline' ),
		'priceRange'      => '$$$',
		'address'         => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => (string) akzara_opt( 'address' ),
			'addressLocality' => 'Medellín',
			'addressRegion'   => 'Antioquia',
			'addressCountry'  => 'CO',
		),
		'potentialAction' => array(
			'@type'  => 'ReserveAction',
			'target' => home_url( '/reservar/' ),
			'name'   => 'Reservar',
		),
	);
	$maps = (string) akzara_opt( 'maps_url' );
	if ( $maps && 'https://maps.app.goo.gl/' !== $maps ) {
		$spa['hasMap'] = $maps;
	}
	$ig = (string) akzara_opt( 'instagram' );
	if ( $ig && 'https://instagram.com/' !== $ig ) {
		$spa['sameAs'] = array( $ig );
	}
	akzara_jsonld( $spa );

	if ( is_singular( 'akzara_ritual' ) ) {
		$r = akzara_ritual( get_queried_object() );
		akzara_jsonld(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => array(
					array(
						'@type'       => 'Service',
						'name'        => $r['name'],
						'description' => $r['summary'],
						'serviceType' => $r['cat_name'],
						'provider'    => array(
							'@type' => 'DaySpa',
							'name'  => 'Akzara Wellness Club',
							'url'   => $site,
						),
						'areaServed'  => 'Medellín',
						'image'       => akzara_share_image(),
						'offers'      => array(
							'@type'         => 'Offer',
							'price'         => $r['price'],
							'priceCurrency' => 'COP',
							'availability'  => 'https://schema.org/InStock',
							'url'           => $r['url'],
						),
					),
					array(
						'@type'           => 'BreadcrumbList',
						'itemListElement' => array(
							array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Rituales', 'item' => get_post_type_archive_link( 'akzara_ritual' ) ),
							array( '@type' => 'ListItem', 'position' => 2, 'name' => $r['cat_name'], 'item' => is_wp_error( $r['cat_url'] ) ? '' : $r['cat_url'] ),
							array( '@type' => 'ListItem', 'position' => 3, 'name' => $r['name'], 'item' => $r['url'] ),
						),
					),
				),
			)
		);
	}

	if ( is_singular( 'post' ) ) {
		$p = get_queried_object();
		akzara_jsonld(
			array(
				'@context'         => 'https://schema.org',
				'@type'            => 'Article',
				'headline'         => get_the_title( $p ),
				'datePublished'    => get_the_date( 'c', $p ),
				'dateModified'     => get_the_modified_date( 'c', $p ),
				'image'            => akzara_share_image(),
				'author'           => array( '@type' => 'Organization', 'name' => 'Akzara Wellness Club' ),
				'publisher'        => array( '@type' => 'Organization', 'name' => 'Akzara Wellness Club', 'logo' => array( '@type' => 'ImageObject', 'url' => $logo ) ),
				'mainEntityOfPage' => get_permalink( $p ),
			)
		);
	}
}
add_action( 'wp_head', 'akzara_structured_data', 20 );

/**
 * Datos estructurados de preguntas frecuentes (se llama desde las plantillas que las muestran).
 *
 * @param array $items [[pregunta, respuesta], …].
 */
function akzara_faq_jsonld( $items ) {
	if ( ! $items || akzara_seo_plugin_active() ) {
		return;
	}
	$entities = array();
	foreach ( $items as $i ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $i[0],
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $i[1] ),
		);
	}
	akzara_jsonld( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities ) );
}

/**
 * La página de reservas no se indexa; las búsquedas tampoco.
 *
 * @param array $robots Directivas.
 * @return array
 */
function akzara_robots( $robots ) {
	if ( akzara_is_template( 'page-reservas.php' ) || is_search() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	}
	return $robots;
}
add_filter( 'wp_robots', 'akzara_robots' );

/**
 * robots.txt: sin zonas privadas.
 *
 * @param string $output Contenido.
 * @return string
 */
function akzara_robots_txt( $output ) {
	$output .= "Disallow: /reservar/\n";
	return $output;
}
add_filter( 'robots_txt', 'akzara_robots_txt' );

/**
 * Todas las imágenes salen con atributo alt (texto alternativo o, si falta, el título).
 *
 * @param array   $attr       Atributos.
 * @param WP_Post $attachment Adjunto.
 * @return array
 */
function akzara_image_alt( $attr, $attachment ) {
	if ( empty( $attr['alt'] ) ) {
		$attr['alt'] = get_the_title( $attachment );
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'akzara_image_alt', 10, 2 );
