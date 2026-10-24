<?php
/**
 * Akzara Wellness Club: funciones del tema.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AKZARA_VERSION', '1.0.0' );
define( 'AKZARA_DIR', get_template_directory() );
define( 'AKZARA_URI', get_template_directory_uri() );

$akzara_files = array(
	'setup',
	'helpers',
	'template-helpers',
	'fields',
	'cpt',
	'meta-boxes',
	'customizer',
	'enqueue',
	'seo',
	'tracking',
	'gift-card',
	'seed',
);
foreach ( $akzara_files as $akzara_file ) {
	require_once AKZARA_DIR . '/inc/' . $akzara_file . '.php';
}
unset( $akzara_files, $akzara_file );
