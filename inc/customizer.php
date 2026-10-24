<?php
/**
 * Personalizador: crea los controles a partir del registro de campos (inc/fields.php).
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Limpia un campo según su tipo.
 *
 * @param mixed  $value Valor.
 * @param string $type  Tipo del campo.
 * @return mixed
 */
function akzara_sanitize_field( $value, $type ) {
	switch ( $type ) {
		case 'textarea':
			return sanitize_textarea_field( $value );
		case 'url':
			return esc_url_raw( $value );
		case 'checkbox':
			return (bool) $value;
		case 'number':
		case 'image':
		case 'video':
			return absint( $value );
		case 'color':
			$c = sanitize_hex_color( $value );
			return $c ? $c : '';
		default:
			return sanitize_text_field( $value );
	}
}

/**
 * Registra secciones, ajustes y controles.
 *
 * @param WP_Customize_Manager $wp_customize Gestor.
 */
function akzara_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'akzara',
		array(
			'title'    => __( 'Akzara · Contenido del sitio', 'akzara' ),
			'priority' => 20,
		)
	);

	foreach ( akzara_sections() as $id => $s ) {
		$wp_customize->add_section(
			'akzara_' . $id,
			array(
				'title'       => $s[0],
				'description' => $s[1],
				'panel'       => 'akzara',
			)
		);
	}

	foreach ( akzara_fields() as $key => $f ) {
		list( $section, $label, $type, $default ) = $f;
		$desc                                      = isset( $f[4] ) ? $f[4] : '';
		$id                                        = 'akzara_' . $key;

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $default,
				'type'              => 'theme_mod',
				'sanitize_callback' => function ( $v ) use ( $type ) {
					return akzara_sanitize_field( $v, $type );
				},
				'transport'         => 'refresh',
			)
		);

		$args = array(
			'label'       => $label,
			'description' => $desc,
			'section'     => 'akzara_' . $section,
			'settings'    => $id,
		);

		if ( 'image' === $type || 'video' === $type ) {
			$args['mime_type'] = 'image' === $type ? 'image' : 'video';
			$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, $args ) );
		} elseif ( 'color' === $type ) {
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, $args ) );
		} else {
			$map          = array(
				'text'     => 'text',
				'textarea' => 'textarea',
				'url'      => 'url',
				'checkbox' => 'checkbox',
				'number'   => 'number',
			);
			$args['type'] = isset( $map[ $type ] ) ? $map[ $type ] : 'text';
			$wp_customize->add_control( $id, $args );
		}
	}
}
add_action( 'customize_register', 'akzara_customize_register' );

/**
 * Colores de marca como variables CSS (el CSS ya las usa).
 */
function akzara_print_theme_vars() {
	$vars = array(
		'--bg'          => akzara_opt( 'c_bg' ),
		'--ink'         => akzara_opt( 'c_ink' ),
		'--bronze-text' => akzara_opt( 'c_bronze_text' ),
		'--bronze'      => akzara_opt( 'c_bronze' ),
		'--radius'      => (int) akzara_opt( 'radius' ) . 'px',
	);
	$css  = '';
	foreach ( $vars as $k => $v ) {
		if ( $v ) {
			$css .= $k . ':' . $v . ';';
		}
	}
	echo '<style id="akzara-vars">:root{' . esc_html( $css ) . '}</style>' . "\n";
}
add_action( 'wp_head', 'akzara_print_theme_vars', 5 );
