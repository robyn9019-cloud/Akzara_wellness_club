<?php
/**
 * Pixel de Meta, Google Analytics 4 y Tag Manager. Los identificadores se configuran en el Personalizador.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Identificadores válidos (si el formato no coincide, no se imprime nada).
 *
 * @return array
 */
function akzara_tracking_ids() {
	$pixel = preg_replace( '/\D/', '', (string) akzara_opt( 'meta_pixel' ) );
	$ga4   = strtoupper( trim( (string) akzara_opt( 'ga4' ) ) );
	$gtm   = strtoupper( trim( (string) akzara_opt( 'gtm' ) ) );
	return array(
		'pixel' => strlen( $pixel ) >= 8 ? $pixel : '',
		'ga4'   => preg_match( '/^G-[A-Z0-9]{6,}$/', $ga4 ) ? $ga4 : '',
		'gtm'   => preg_match( '/^GTM-[A-Z0-9]{4,}$/', $gtm ) ? $gtm : '',
	);
}

/**
 * Scripts de seguimiento en <head>. No se cargan para quien edita el sitio.
 */
function akzara_tracking_head() {
	if ( is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
		return;
	}
	$ids = akzara_tracking_ids();

	if ( $ids['gtm'] ) {
		echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js( $ids['gtm'] ) . "');</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	if ( $ids['ga4'] && ! $ids['gtm'] ) {
		echo '<script async src="' . esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . $ids['ga4'] ) . '"></script>' . "\n";
		echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . esc_js( $ids['ga4'] ) . "');</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	if ( $ids['pixel'] ) {
		echo "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','" . esc_js( $ids['pixel'] ) . "');fbq('track','PageView');</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'wp_head', 'akzara_tracking_head', 3 );

/**
 * Versiones <noscript> (Tag Manager y Pixel).
 */
function akzara_tracking_body() {
	if ( is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
		return;
	}
	$ids = akzara_tracking_ids();
	if ( $ids['gtm'] ) {
		echo '<noscript><iframe src="' . esc_url( 'https://www.googletagmanager.com/ns.html?id=' . $ids['gtm'] ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
	}
	if ( $ids['pixel'] ) {
		echo '<noscript><img height="1" width="1" style="display:none" alt="" src="' . esc_url( 'https://www.facebook.com/tr?id=' . $ids['pixel'] . '&ev=PageView&noscript=1' ) . '"></noscript>' . "\n";
	}
}
add_action( 'wp_body_open', 'akzara_tracking_body' );
