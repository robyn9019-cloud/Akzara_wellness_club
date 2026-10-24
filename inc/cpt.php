<?php
/**
 * Tipos de contenido propios: rituales, membresías, preguntas, testimonios y solicitudes de gift card.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra tipos de contenido y taxonomías.
 */
function akzara_register_content_types() {
	// Rituales: /ritual/{slug}/ y archivo en /rituales/.
	register_post_type(
		'akzara_ritual',
		array(
			'labels'       => array(
				'name'               => __( 'Rituales', 'akzara' ),
				'singular_name'      => __( 'Ritual', 'akzara' ),
				'add_new'            => __( 'Añadir ritual', 'akzara' ),
				'add_new_item'       => __( 'Añadir ritual', 'akzara' ),
				'edit_item'          => __( 'Editar ritual', 'akzara' ),
				'all_items'          => __( 'Todos los rituales', 'akzara' ),
				'search_items'       => __( 'Buscar rituales', 'akzara' ),
				'not_found'          => __( 'No hay rituales.', 'akzara' ),
				'featured_image'     => __( 'Foto principal', 'akzara' ),
				'set_featured_image' => __( 'Elegir la foto principal', 'akzara' ),
			),
			'public'       => true,
			'has_archive'  => 'rituales',
			'rewrite'      => array(
				'slug'       => 'ritual',
				'with_front' => false,
			),
			'menu_icon'    => 'dashicons-palmtree',
			'menu_position' => 5,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'akzara_categoria',
		'akzara_ritual',
		array(
			'labels'       => array(
				'name'          => __( 'Categorías de rituales', 'akzara' ),
				'singular_name' => __( 'Categoría', 'akzara' ),
				'add_new_item'  => __( 'Añadir categoría', 'akzara' ),
				'edit_item'     => __( 'Editar categoría', 'akzara' ),
			),
			'hierarchical' => true,
			'public'       => true,
			'rewrite'      => array(
				'slug'       => 'rituales',
				'with_front' => false,
			),
			'show_in_rest' => true,
			'show_admin_column' => true,
		)
	);

	register_taxonomy(
		'akzara_ocasion',
		'akzara_ritual',
		array(
			'labels'            => array(
				'name'          => __( 'Ideal para', 'akzara' ),
				'singular_name' => __( 'Ocasión', 'akzara' ),
			),
			'hierarchical'      => false,
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);

	// Membresías.
	register_post_type(
		'akzara_membresia',
		array(
			'labels'        => array(
				'name'          => __( 'Membresías', 'akzara' ),
				'singular_name' => __( 'Membresía', 'akzara' ),
				'add_new_item'  => __( 'Añadir membresía', 'akzara' ),
				'edit_item'     => __( 'Editar membresía', 'akzara' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'menu_icon'     => 'dashicons-star-filled',
			'menu_position' => 6,
			'supports'      => array( 'title', 'page-attributes' ),
		)
	);

	// Preguntas frecuentes.
	register_post_type(
		'akzara_faq',
		array(
			'labels'        => array(
				'name'          => __( 'Preguntas frecuentes', 'akzara' ),
				'singular_name' => __( 'Pregunta', 'akzara' ),
				'add_new_item'  => __( 'Añadir pregunta', 'akzara' ),
				'edit_item'     => __( 'Editar pregunta', 'akzara' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'menu_icon'     => 'dashicons-editor-help',
			'menu_position' => 7,
			'supports'      => array( 'title', 'editor', 'page-attributes' ),
		)
	);

	// Testimonios: solo se muestran los publicados (publicar = la persona lo autorizó).
	register_post_type(
		'akzara_testimonio',
		array(
			'labels'        => array(
				'name'          => __( 'Testimonios', 'akzara' ),
				'singular_name' => __( 'Testimonio', 'akzara' ),
				'add_new_item'  => __( 'Añadir testimonio', 'akzara' ),
				'edit_item'     => __( 'Editar testimonio', 'akzara' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'menu_icon'     => 'dashicons-format-quote',
			'menu_position' => 8,
			'supports'      => array( 'title', 'editor', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'akzara_register_content_types' );

/**
 * Orden de los rituales: por precio en el archivo y los filtros.
 *
 * @param WP_Query $q Consulta.
 */
function akzara_ritual_archive_query( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_post_type_archive( 'akzara_ritual' ) || $q->is_tax( 'akzara_categoria' ) ) {
		$q->set( 'posts_per_page', 200 );
		$q->set( 'meta_key', '_ak_price' );
		$q->set( 'orderby', array( 'meta_value_num' => 'ASC', 'title' => 'ASC' ) );
	}
}
add_action( 'pre_get_posts', 'akzara_ritual_archive_query' );

/**
 * Columnas útiles en la lista de rituales.
 *
 * @param array $cols Columnas.
 * @return array
 */
function akzara_ritual_columns( $cols ) {
	$new = array();
	foreach ( $cols as $k => $v ) {
		$new[ $k ] = $v;
		if ( 'title' === $k ) {
			$new['akz_price'] = __( 'Precio desde', 'akzara' );
			$new['akz_min']   = __( 'Duración', 'akzara' );
			$new['akz_flags'] = __( 'Marcas', 'akzara' );
		}
	}
	return $new;
}
add_filter( 'manage_akzara_ritual_posts_columns', 'akzara_ritual_columns' );

/**
 * Contenido de las columnas.
 *
 * @param string $col     Columna.
 * @param int    $post_id Ritual.
 */
function akzara_ritual_column_content( $col, $post_id ) {
	if ( 'akz_price' === $col ) {
		echo esc_html( akzara_cop( get_post_meta( $post_id, '_ak_price', true ) ) );
	} elseif ( 'akz_min' === $col ) {
		echo esc_html( akzara_duration( (int) get_post_meta( $post_id, '_ak_min', true ) ) );
	} elseif ( 'akz_flags' === $col ) {
		$flags = array();
		if ( get_post_meta( $post_id, '_ak_featured', true ) ) {
			$flags[] = __( 'Destacado', 'akzara' );
		}
		if ( get_post_meta( $post_id, '_ak_signature', true ) ) {
			$flags[] = __( 'Insignia', 'akzara' );
		}
		echo esc_html( implode( ' · ', $flags ) );
	}
}
add_action( 'manage_akzara_ritual_posts_custom_column', 'akzara_ritual_column_content', 10, 2 );

/**
 * Lista de membresías normalizadas.
 *
 * @return array
 */
function akzara_memberships() {
	$q   = new WP_Query(
		array(
			'post_type'      => 'akzara_membresia',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	$out = array();
	foreach ( $q->posts as $p ) {
		$out[] = array(
			'id'        => $p->ID,
			'name'      => get_the_title( $p ),
			'period'    => (string) get_post_meta( $p->ID, '_ak_period', true ),
			'price'     => (float) get_post_meta( $p->ID, '_ak_price', true ),
			'benefits'  => array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $p->ID, '_ak_benefits', true ) ) ) ) ),
			'highlight' => (bool) get_post_meta( $p->ID, '_ak_highlight', true ),
			'button'    => (string) ( get_post_meta( $p->ID, '_ak_button', true ) ? get_post_meta( $p->ID, '_ak_button', true ) : 'Conocer' ),
		);
	}
	wp_reset_postdata();
	return $out;
}

/**
 * Lista de preguntas frecuentes.
 *
 * @param int $limit Máximo.
 * @return array [[pregunta, respuesta], …]
 */
function akzara_faqs( $limit = 20 ) {
	$q   = new WP_Query(
		array(
			'post_type'      => 'akzara_faq',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $limit,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	$out = array();
	foreach ( $q->posts as $p ) {
		$out[] = array( get_the_title( $p ), wp_strip_all_tags( $p->post_content ) );
	}
	wp_reset_postdata();
	return $out;
}

/**
 * Lista de testimonios publicados.
 *
 * @param int $limit Máximo.
 * @return array [[nombre, texto, fuente], …]
 */
function akzara_testimonials( $limit = 3 ) {
	$q   = new WP_Query(
		array(
			'post_type'      => 'akzara_testimonio',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $limit,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	$out = array();
	foreach ( $q->posts as $p ) {
		$out[] = array( get_the_title( $p ), wp_strip_all_tags( $p->post_content ), (string) get_post_meta( $p->ID, '_ak_source', true ) );
	}
	wp_reset_postdata();
	return $out;
}
