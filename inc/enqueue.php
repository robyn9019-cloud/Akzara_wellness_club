<?php
/**
 * Estilos y scripts del tema (con versión para romper la caché).
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Versión de un archivo según su fecha de modificación.
 *
 * @param string $rel Ruta relativa al tema.
 * @return string
 */
function akzara_asset_version( $rel ) {
	$file = AKZARA_DIR . '/' . ltrim( $rel, '/' );
	return file_exists( $file ) ? (string) filemtime( $file ) : AKZARA_VERSION;
}

/**
 * Carga CSS y JS en el sitio.
 */
function akzara_enqueue() {
	wp_enqueue_style( 'akzara-main', AKZARA_URI . '/assets/css/main.css', array(), akzara_asset_version( 'assets/css/main.css' ) );

	wp_enqueue_script(
		'akzara-main',
		AKZARA_URI . '/assets/js/main.js',
		array(),
		akzara_asset_version( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	$data = array(
		'ajax'      => esc_url_raw( rest_url( 'akzara/v1/gift-card' ) ),
		'nonce'     => wp_create_nonce( 'wp_rest' ),
		'home'      => home_url( '/' ),
		'book'      => home_url( '/reservar/' ),
		'whatsapp'  => preg_replace( '/\D/', '', (string) akzara_opt( 'whatsapp' ) ),
		'slots'     => array(
			'from' => (string) akzara_opt( 'slot_from' ),
			'to'   => (string) akzara_opt( 'slot_to' ),
			'step' => (int) akzara_opt( 'slot_step' ),
		),
		'locale'    => 'es',
		'pixel'     => (bool) akzara_opt( 'meta_pixel' ),
		'promo'     => array(
			'on'    => (bool) akzara_opt( 'promo_on' ),
			'delay' => (int) akzara_opt( 'promo_delay' ),
			'days'  => (int) akzara_opt( 'promo_days' ),
		),
	);

	if ( akzara_is_template( 'page-gift-card.php' ) ) {
		$data['gift'] = akzara_gift_script_data();
	}

	wp_add_inline_script( 'akzara-main', 'window.akzaraData=' . wp_json_encode( $data ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'akzara_enqueue' );

/**
 * Precarga de las fuentes principales (evita el parpadeo de tipografía).
 */
function akzara_preload_fonts() {
	foreach ( array( 'inter-latin-400-normal', 'cormorant-garamond-latin-400-normal', 'cormorant-garamond-latin-400-italic' ) as $f ) {
		echo '<link rel="preload" href="' . esc_url( AKZARA_URI . '/assets/fonts/' . $f . '.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	}
}
add_action( 'wp_head', 'akzara_preload_fonts', 1 );

/**
 * El tema usa su propio CSS: se quita lo que el editor de bloques añade por defecto.
 */
function akzara_dequeue_block_css() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'akzara_dequeue_block_css', 100 );

/**
 * Tipos de archivo permitidos en la Mediateca para el video de portada y las fotos.
 *
 * @param array $mimes Tipos.
 * @return array
 */
function akzara_upload_mimes( $mimes ) {
	$mimes['webp'] = 'image/webp';
	$mimes['webm'] = 'video/webm';
	return $mimes;
}
add_filter( 'upload_mimes', 'akzara_upload_mimes' );
