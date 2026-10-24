<?php
/**
 * Configuración base del tema: soportes, menús y tamaños de imagen.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Soportes del tema.
 */
function akzara_setup() {
	load_theme_textdomain( 'akzara', AKZARA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 440,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'akzara_club'  => __( 'Menú «El Club» (desplegable)', 'akzara' ),
			'akzara_legal' => __( 'Pie de página: textos legales', 'akzara' ),
		)
	);

	// Tamaños: tarjeta vertical (4:5), ancho y miniatura.
	add_image_size( 'akzara-card', 900, 1125, true );
	add_image_size( 'akzara-wide', 1920, 1920, false );
	add_image_size( 'akzara-thumb', 480, 480, true );
}
add_action( 'after_setup_theme', 'akzara_setup' );

/**
 * El ancho de contenido para los embeds.
 */
function akzara_content_width() {
	$GLOBALS['content_width'] = 1180;
}
add_action( 'after_setup_theme', 'akzara_content_width', 0 );

/**
 * Quita el JavaScript de emojis (no se usa y pesa).
 */
function akzara_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'akzara_disable_emojis' );

/**
 * Con direcciones tipo /diario/%postname%/, WordPress genera una regla por cada página y una
 * dirección inexistente acabaría mostrando el inicio. Esta regla final la manda a «no encontrada».
 */
function akzara_catch_all_rule() {
	add_rewrite_rule( '^(.+?)/?$', 'index.php?error=404', 'bottom' );
}
add_action( 'init', 'akzara_catch_all_rule', 20 );

/**
 * Actualiza las direcciones una sola vez cada vez que cambia la versión del tema.
 */
function akzara_maybe_flush_rules() {
	$stamp = AKZARA_VERSION . '-3';
	if ( get_option( 'akzara_rules_v' ) !== $stamp ) {
		flush_rewrite_rules( false );
		update_option( 'akzara_rules_v', $stamp );
	}
}
add_action( 'init', 'akzara_maybe_flush_rules', 99 );

/**
 * Redirecciones de las direcciones de la web anterior.
 */
function akzara_legacy_redirects() {
	if ( is_admin() ) {
		return;
	}
	$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	$map  = array(
		'/politicas.html'     => '/politicas/reserva/',
		'/blog.html'          => '/diario/',
		'/instalaciones.html' => '/instalaciones/',
		'/catalogo.html'      => '/rituales/',
		'/reservas'           => '/reservar/',
		'/reservas/'          => '/reservar/',
	);
	if ( $path && isset( $map[ $path ] ) ) {
		wp_safe_redirect( home_url( $map[ $path ] ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'akzara_legacy_redirects', 1 );

/**
 * Cabeceras de seguridad básicas.
 */
function akzara_security_headers() {
	if ( headers_sent() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );
}
add_action( 'send_headers', 'akzara_security_headers' );

/**
 * Oculta la versión de WordPress.
 */
remove_action( 'wp_head', 'wp_generator' );
