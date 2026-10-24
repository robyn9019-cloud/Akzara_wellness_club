<?php
/**
 * Campos nativos (sin ACF) para rituales, membresías, testimonios y categorías.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cuadros de datos.
 */
function akzara_add_meta_boxes() {
	add_meta_box( 'akzara_ritual_data', __( 'Datos del ritual', 'akzara' ), 'akzara_ritual_box', 'akzara_ritual', 'normal', 'high' );
	add_meta_box( 'akzara_membership_data', __( 'Datos de la membresía', 'akzara' ), 'akzara_membership_box', 'akzara_membresia', 'normal', 'high' );
	add_meta_box( 'akzara_testimonial_data', __( 'Fuente', 'akzara' ), 'akzara_testimonial_box', 'akzara_testimonio', 'side' );
}
add_action( 'add_meta_boxes', 'akzara_add_meta_boxes' );

/**
 * Un campo de texto / número del cuadro.
 *
 * @param string $key   Meta.
 * @param string $label Etiqueta.
 * @param mixed  $value Valor actual.
 * @param string $type  Tipo de input.
 * @param string $help  Ayuda.
 */
function akzara_field_row( $key, $label, $value, $type = 'text', $help = '' ) {
	echo '<p class="akz-row"><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
	echo '<input class="widefat" type="' . esc_attr( $type ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '"' . ( 'number' === $type ? ' min="0" step="1"' : '' ) . '>';
	if ( $help ) {
		echo '<span class="description">' . esc_html( $help ) . '</span>';
	}
	echo '</p>';
}

/**
 * Cuadro de un ritual.
 *
 * @param WP_Post $post Ritual.
 */
function akzara_ritual_box( $post ) {
	wp_nonce_field( 'akzara_ritual_save', 'akzara_ritual_nonce' );
	$g = function ( $k, $d = '' ) use ( $post ) {
		$v = get_post_meta( $post->ID, $k, true );
		return '' === $v ? $d : $v;
	};

	echo '<div class="akz-box">';
	echo '<h4>' . esc_html__( 'Precios y duración', 'akzara' ) . '</h4><div class="akz-grid">';
	akzara_field_row( '_ak_min', __( 'Duración (minutos)', 'akzara' ), $g( '_ak_min', 60 ), 'number', __( '90 = 1 h 30.', 'akzara' ) );
	akzara_field_row( '_ak_price', __( 'Precio desde (COP)', 'akzara' ), $g( '_ak_price', 0 ), 'number', __( 'En pesos, sin puntos. Ej.: 280000.', 'akzara' ) );
	akzara_field_row( '_ak_deposit', __( 'Anticipo (%)', 'akzara' ), $g( '_ak_deposit', 50 ), 'number', __( 'Lo que se pide para confirmar. Informativo: los cobros los gestiona Amelia.', 'akzara' ) );
	echo '</div>';
	akzara_field_row( '_ak_pricenote', __( 'Nota de precio (opcional)', 'akzara' ), $g( '_ak_pricenote' ), 'text', __( 'Se muestra bajo el precio. Ej.: Precio normal: $380.000 · Huéspedes: $249.500. Con precio 0 la ficha dice «Consultar».', 'akzara' ) );

	echo '<p class="akz-row"><label for="_ak_mode"><strong>' . esc_html__( 'El precio es', 'akzara' ) . '</strong></label><br><select id="_ak_mode" name="_ak_mode">';
	foreach ( array( 'persona' => __( 'Por persona', 'akzara' ), 'grupo' => __( 'Por pareja / grupo', 'akzara' ) ) as $v => $l ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $g( '_ak_mode', 'persona' ), $v, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select></p>';

	echo '<h4>' . esc_html__( 'Personas', 'akzara' ) . '</h4><div class="akz-grid">';
	akzara_field_row( '_ak_minp', __( 'Mínimo de personas', 'akzara' ), $g( '_ak_minp', 1 ), 'number' );
	akzara_field_row( '_ak_maxp', __( 'Máximo de personas', 'akzara' ), $g( '_ak_maxp', 1 ), 'number' );
	echo '</div>';

	echo '<h4>' . esc_html__( 'En el sitio', 'akzara' ) . '</h4>';
	akzara_field_row( '_ak_badge', __( 'Etiqueta sobre la foto', 'akzara' ), $g( '_ak_badge' ), 'text', __( 'Ej.: Insignia, Para dos, Cupos limitados. Vacía si no la necesita.', 'akzara' ) );
	echo '<p><label><input type="checkbox" name="_ak_featured" value="1"' . checked( (bool) $g( '_ak_featured' ), true, false ) . '> ' . esc_html__( 'Mostrar en «Rituales destacados» del inicio (el orden se define con «Orden» a la derecha: menor = antes).', 'akzara' ) . '</label></p>';
	echo '<p><label><input type="checkbox" name="_ak_signature" value="1"' . checked( (bool) $g( '_ak_signature' ), true, false ) . '> ' . esc_html__( 'Es el ritual insignia (se muestra grande en el inicio; marque solo uno).', 'akzara' ) . '</label></p>';
	echo '<p><label><input type="checkbox" name="_ak_giftable" value="1"' . checked( '0' !== (string) $g( '_ak_giftable', '1' ), true, false ) . '> ' . esc_html__( 'Se puede regalar (botón «Regalar» y gift card).', 'akzara' ) . '</label></p>';
	echo '<p class="akz-row"><label for="_ak_tone"><strong>' . esc_html__( 'Color provisional (mientras no haya foto)', 'akzara' ) . '</strong></label><br><select id="_ak_tone" name="_ak_tone">';
	foreach ( array( 'arena' => 'Arena', 'humo' => 'Humo', 'jardin' => 'Jardín' ) as $v => $l ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $g( '_ak_tone', 'arena' ), $v, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select></p>';

	// Galería.
	$gal = array_filter( array_map( 'absint', explode( ',', (string) $g( '_ak_gallery' ) ) ) );
	echo '<h4>' . esc_html__( 'Galería', 'akzara' ) . '</h4><p class="description">' . esc_html__( 'La primera foto es la de las tarjetas y la portada de la ficha. Arrastre para cambiar el orden.', 'akzara' ) . '</p>';
	echo '<div class="akz-gallery" data-input="_ak_gallery">';
	foreach ( $gal as $id ) {
		echo '<span class="akz-thumb" data-id="' . (int) $id . '">' . wp_get_attachment_image( $id, 'thumbnail' ) . '<button type="button" class="akz-x" aria-label="' . esc_attr__( 'Quitar', 'akzara' ) . '">×</button></span>';
	}
	echo '</div><input type="hidden" id="_ak_gallery" name="_ak_gallery" value="' . esc_attr( implode( ',', $gal ) ) . '">';
	echo '<p><button type="button" class="button akz-add-gallery">' . esc_html__( 'Añadir fotos', 'akzara' ) . '</button></p>';

	// Pasos.
	$steps = json_decode( (string) $g( '_ak_steps' ), true );
	$steps = is_array( $steps ) ? $steps : array();
	echo '<h4>' . esc_html__( 'Así transcurre su ritual (paso a paso)', 'akzara' ) . '</h4><p class="description">' . esc_html__( 'Un paso por fila: Llegada, Agua, Ritual, Pausa… Se muestran como una línea de tiempo.', 'akzara' ) . '</p>';
	echo '<div class="akz-steps">';
	foreach ( $steps as $i => $s ) {
		akzara_step_row( $i, $s );
	}
	echo '</div><p><button type="button" class="button akz-add-step">' . esc_html__( 'Añadir paso', 'akzara' ) . '</button></p>';
	echo '<template id="akz-step-tpl">';
	akzara_step_row( '__i__', array() );
	echo '</template>';

	echo '<h4>' . esc_html__( 'Incluye', 'akzara' ) . '</h4><p><textarea class="widefat" rows="4" name="_ak_includes" placeholder="' . esc_attr__( 'Una línea por elemento', 'akzara' ) . '">' . esc_textarea( (string) $g( '_ak_includes' ) ) . '</textarea></p>';
	echo '<h4>' . esc_html__( 'Contraindicaciones', 'akzara' ) . '</h4><p><textarea class="widefat" rows="3" name="_ak_contra">' . esc_textarea( (string) $g( '_ak_contra' ) ) . '</textarea></p>';

	// Relacionados.
	$rel  = array_filter( array_map( 'absint', explode( ',', (string) $g( '_ak_related' ) ) ) );
	$all  = get_posts(
		array(
			'post_type'      => 'akzara_ritual',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'exclude'        => array( $post->ID ),
		)
	);
	echo '<h4>' . esc_html__( 'También le puede interesar (hasta 3)', 'akzara' ) . '</h4><p class="description">' . esc_html__( 'Vacío = el sitio sugiere rituales de la misma categoría. Mantenga Ctrl pulsado para elegir varios.', 'akzara' ) . '</p>';
	echo '<select multiple size="6" class="widefat" name="_ak_related[]">';
	foreach ( $all as $r ) {
		echo '<option value="' . (int) $r->ID . '"' . selected( in_array( $r->ID, $rel, true ), true, false ) . '>' . esc_html( get_the_title( $r ) ) . '</option>';
	}
	echo '</select>';
	echo '</div>';
}

/**
 * Una fila de paso (o la plantilla).
 *
 * @param int|string $i Índice.
 * @param array      $s Datos.
 */
function akzara_step_row( $i, $s ) {
	$img = isset( $s['image'] ) ? absint( $s['image'] ) : 0;
	echo '<div class="akz-step"><input class="widefat" type="text" name="_ak_steps[' . esc_attr( $i ) . '][title]" placeholder="' . esc_attr__( 'Paso (ej.: Llegada)', 'akzara' ) . '" value="' . esc_attr( isset( $s['title'] ) ? $s['title'] : '' ) . '">';
	echo '<textarea class="widefat" rows="2" name="_ak_steps[' . esc_attr( $i ) . '][text]" placeholder="' . esc_attr__( 'Detalle', 'akzara' ) . '">' . esc_textarea( isset( $s['text'] ) ? $s['text'] : '' ) . '</textarea>';
	echo '<span class="akz-step-img">' . ( $img ? wp_get_attachment_image( $img, 'thumbnail' ) : '' ) . '</span>';
	echo '<input type="hidden" class="akz-step-id" name="_ak_steps[' . esc_attr( $i ) . '][image]" value="' . (int) $img . '">';
	echo '<button type="button" class="button akz-step-pick">' . esc_html__( 'Foto del paso', 'akzara' ) . '</button> <button type="button" class="button-link-delete akz-step-del">' . esc_html__( 'Quitar paso', 'akzara' ) . '</button></div>';
}

/**
 * Cuadro de una membresía.
 *
 * @param WP_Post $post Membresía.
 */
function akzara_membership_box( $post ) {
	wp_nonce_field( 'akzara_membership_save', 'akzara_membership_nonce' );
	$g = function ( $k, $d = '' ) use ( $post ) {
		$v = get_post_meta( $post->ID, $k, true );
		return '' === $v ? $d : $v;
	};
	akzara_field_row( '_ak_period', __( 'Duración (texto)', 'akzara' ), $g( '_ak_period' ), 'text', __( 'Ej.: 1 mes, 3 meses', 'akzara' ) );
	akzara_field_row( '_ak_price', __( 'Precio (COP)', 'akzara' ), $g( '_ak_price', 0 ), 'number' );
	akzara_field_row( '_ak_button', __( 'Texto del botón', 'akzara' ), $g( '_ak_button', 'Conocer' ) );
	echo '<p><strong>' . esc_html__( 'Beneficios', 'akzara' ) . '</strong><br><textarea class="widefat" rows="5" name="_ak_benefits" placeholder="' . esc_attr__( 'Una línea por beneficio', 'akzara' ) . '">' . esc_textarea( (string) $g( '_ak_benefits' ) ) . '</textarea></p>';
	echo '<p><label><input type="checkbox" name="_ak_highlight" value="1"' . checked( (bool) $g( '_ak_highlight' ), true, false ) . '> ' . esc_html__( 'Destacar (borde bronce)', 'akzara' ) . '</label></p>';
	echo '<p class="description">' . esc_html__( 'El orden se define con «Orden» a la derecha (menor = primero).', 'akzara' ) . '</p>';
}

/**
 * Cuadro de un testimonio.
 *
 * @param WP_Post $post Testimonio.
 */
function akzara_testimonial_box( $post ) {
	wp_nonce_field( 'akzara_testimonial_save', 'akzara_testimonial_nonce' );
	akzara_field_row( '_ak_source', __( 'Fuente (Google, Instagram…)', 'akzara' ), get_post_meta( $post->ID, '_ak_source', true ) );
	echo '<p class="description">' . esc_html__( 'Título = nombre de la persona; texto = su testimonio. Solo publique testimonios reales y con autorización de la persona.', 'akzara' ) . '</p>';
}

/**
 * Guarda los datos.
 *
 * @param int $post_id Entrada.
 */
function akzara_save_meta( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$type = get_post_type( $post_id );

	if ( 'akzara_ritual' === $type && isset( $_POST['akzara_ritual_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['akzara_ritual_nonce'] ) ), 'akzara_ritual_save' ) ) {
		$nums = array( '_ak_min', '_ak_price', '_ak_deposit', '_ak_minp', '_ak_maxp' );
		foreach ( $nums as $k ) {
			update_post_meta( $post_id, $k, isset( $_POST[ $k ] ) ? absint( wp_unslash( $_POST[ $k ] ) ) : 0 );
		}
		update_post_meta( $post_id, '_ak_pricenote', isset( $_POST['_ak_pricenote'] ) ? sanitize_text_field( wp_unslash( $_POST['_ak_pricenote'] ) ) : '' );
		$mode = isset( $_POST['_ak_mode'] ) ? sanitize_key( wp_unslash( $_POST['_ak_mode'] ) ) : 'persona';
		update_post_meta( $post_id, '_ak_mode', 'grupo' === $mode ? 'grupo' : 'persona' );
		$tone = isset( $_POST['_ak_tone'] ) ? sanitize_key( wp_unslash( $_POST['_ak_tone'] ) ) : 'arena';
		update_post_meta( $post_id, '_ak_tone', in_array( $tone, array( 'arena', 'humo', 'jardin' ), true ) ? $tone : 'arena' );
		update_post_meta( $post_id, '_ak_badge', isset( $_POST['_ak_badge'] ) ? sanitize_text_field( wp_unslash( $_POST['_ak_badge'] ) ) : '' );
		update_post_meta( $post_id, '_ak_featured', isset( $_POST['_ak_featured'] ) ? 1 : 0 );
		update_post_meta( $post_id, '_ak_signature', isset( $_POST['_ak_signature'] ) ? 1 : 0 );
		update_post_meta( $post_id, '_ak_giftable', isset( $_POST['_ak_giftable'] ) ? 1 : 0 );
		$gal = isset( $_POST['_ak_gallery'] ) ? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['_ak_gallery'] ) ) ) ) ) : array();
		update_post_meta( $post_id, '_ak_gallery', implode( ',', $gal ) );
		update_post_meta( $post_id, '_ak_includes', isset( $_POST['_ak_includes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_ak_includes'] ) ) : '' );
		update_post_meta( $post_id, '_ak_contra', isset( $_POST['_ak_contra'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_ak_contra'] ) ) : '' );
		$rel = isset( $_POST['_ak_related'] ) ? array_slice( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['_ak_related'] ) ) ), 0, 3 ) : array();
		update_post_meta( $post_id, '_ak_related', implode( ',', $rel ) );

		$steps = array();
		if ( isset( $_POST['_ak_steps'] ) && is_array( $_POST['_ak_steps'] ) ) {
			foreach ( wp_unslash( $_POST['_ak_steps'] ) as $s ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				$title = isset( $s['title'] ) ? sanitize_text_field( $s['title'] ) : '';
				if ( '' === $title ) {
					continue;
				}
				$steps[] = array(
					'title' => $title,
					'text'  => isset( $s['text'] ) ? sanitize_textarea_field( $s['text'] ) : '',
					'image' => isset( $s['image'] ) ? absint( $s['image'] ) : 0,
				);
			}
		}
		update_post_meta( $post_id, '_ak_steps', wp_slash( wp_json_encode( $steps ) ) );
	}

	if ( 'akzara_membresia' === $type && isset( $_POST['akzara_membership_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['akzara_membership_nonce'] ) ), 'akzara_membership_save' ) ) {
		update_post_meta( $post_id, '_ak_period', isset( $_POST['_ak_period'] ) ? sanitize_text_field( wp_unslash( $_POST['_ak_period'] ) ) : '' );
		update_post_meta( $post_id, '_ak_price', isset( $_POST['_ak_price'] ) ? absint( wp_unslash( $_POST['_ak_price'] ) ) : 0 );
		update_post_meta( $post_id, '_ak_button', isset( $_POST['_ak_button'] ) ? sanitize_text_field( wp_unslash( $_POST['_ak_button'] ) ) : 'Conocer' );
		update_post_meta( $post_id, '_ak_benefits', isset( $_POST['_ak_benefits'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_ak_benefits'] ) ) : '' );
		update_post_meta( $post_id, '_ak_highlight', isset( $_POST['_ak_highlight'] ) ? 1 : 0 );
	}

	if ( 'akzara_testimonio' === $type && isset( $_POST['akzara_testimonial_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['akzara_testimonial_nonce'] ) ), 'akzara_testimonial_save' ) ) {
		update_post_meta( $post_id, '_ak_source', isset( $_POST['_ak_source'] ) ? sanitize_text_field( wp_unslash( $_POST['_ak_source'] ) ) : '' );
	}
}
add_action( 'save_post', 'akzara_save_meta' );

/**
 * Campos de la categoría de rituales (antetítulo, frase, texto del menú, imagen, orden).
 *
 * @param WP_Term|string $term Término (o taxonomía al crear).
 */
function akzara_category_fields( $term ) {
	$is_term = $term instanceof WP_Term;
	$g       = function ( $k, $d = '' ) use ( $term, $is_term ) {
		if ( ! $is_term ) {
			return $d;
		}
		$v = get_term_meta( $term->term_id, $k, true );
		return '' === $v ? $d : $v;
	};
	wp_nonce_field( 'akzara_term_save', 'akzara_term_nonce' );
	$rows = array(
		'_ak_eyebrow' => array( __( 'Antetítulo', 'akzara' ), 'Rituales' ),
		'_ak_intro'   => array( __( 'Frase de la página', 'akzara' ), '' ),
		'_ak_menu'    => array( __( 'Texto corto del menú', 'akzara' ), '' ),
		'_ak_order'   => array( __( 'Orden en el menú (menor = primero)', 'akzara' ), '10' ),
	);
	foreach ( $rows as $k => $r ) {
		$html = '<input type="' . ( '_ak_order' === $k ? 'number' : 'text' ) . '" name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" value="' . esc_attr( $g( $k, $r[1] ) ) . '" class="widefat">';
		if ( $is_term ) {
			echo '<tr class="form-field"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_html( $r[0] ) . '</label></th><td>' . $html . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '<div class="form-field"><label for="' . esc_attr( $k ) . '">' . esc_html( $r[0] ) . '</label>' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
	$img  = (int) $g( '_ak_image', 0 );
	$pick = '<div class="akz-term-img"><span class="akz-term-thumb">' . ( $img ? wp_get_attachment_image( $img, 'thumbnail' ) : '' ) . '</span><input type="hidden" name="_ak_image" id="_ak_image" value="' . (int) $img . '"> <button type="button" class="button akz-term-pick">' . esc_html__( 'Elegir imagen', 'akzara' ) . '</button></div>';
	if ( $is_term ) {
		echo '<tr class="form-field"><th scope="row">' . esc_html__( 'Imagen', 'akzara' ) . '</th><td>' . $pick . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput
	} else {
		echo '<div class="form-field"><label>' . esc_html__( 'Imagen', 'akzara' ) . '</label>' . $pick . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'akzara_categoria_add_form_fields', 'akzara_category_fields' );
add_action( 'akzara_categoria_edit_form_fields', 'akzara_category_fields' );

/**
 * Guarda los campos de la categoría.
 *
 * @param int $term_id Término.
 */
function akzara_save_category_fields( $term_id ) {
	if ( ! isset( $_POST['akzara_term_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['akzara_term_nonce'] ) ), 'akzara_term_save' ) || ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	foreach ( array( '_ak_eyebrow', '_ak_menu' ) as $k ) {
		update_term_meta( $term_id, $k, isset( $_POST[ $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) : '' );
	}
	update_term_meta( $term_id, '_ak_intro', isset( $_POST['_ak_intro'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_ak_intro'] ) ) : '' );
	update_term_meta( $term_id, '_ak_order', isset( $_POST['_ak_order'] ) ? absint( wp_unslash( $_POST['_ak_order'] ) ) : 10 );
	update_term_meta( $term_id, '_ak_image', isset( $_POST['_ak_image'] ) ? absint( wp_unslash( $_POST['_ak_image'] ) ) : 0 );
}
add_action( 'created_akzara_categoria', 'akzara_save_category_fields' );
add_action( 'edited_akzara_categoria', 'akzara_save_category_fields' );

/**
 * Categorías de rituales ordenadas por el campo «Orden».
 *
 * @return array Lista de [term, eyebrow, intro, menu, image, order].
 */
function akzara_categories() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'akzara_categoria',
			'hide_empty' => false,
		)
	);
	$out   = array();
	if ( is_wp_error( $terms ) ) {
		return $out;
	}
	foreach ( $terms as $t ) {
		$out[] = array(
			'id'      => $t->term_id,
			'name'    => $t->name,
			'slug'    => $t->slug,
			'url'     => get_term_link( $t ),
			'eyebrow' => (string) get_term_meta( $t->term_id, '_ak_eyebrow', true ),
			'intro'   => (string) get_term_meta( $t->term_id, '_ak_intro', true ),
			'menu'    => (string) get_term_meta( $t->term_id, '_ak_menu', true ),
			'image'   => (int) get_term_meta( $t->term_id, '_ak_image', true ),
			'order'   => (int) get_term_meta( $t->term_id, '_ak_order', true ),
		);
	}
	usort(
		$out,
		function ( $a, $b ) {
			return $a['order'] <=> $b['order'];
		}
	);
	return $out;
}

/**
 * Carga el JS y el CSS del panel en las pantallas que lo usan.
 *
 * @param string $hook Pantalla.
 */
function akzara_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}
	$ok = in_array( $screen->post_type, array( 'akzara_ritual', 'akzara_membresia', 'akzara_testimonio' ), true ) || 'edit-akzara_categoria' === $screen->id;
	if ( ! $ok ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'akzara-admin', AKZARA_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), akzara_asset_version( 'assets/js/admin.js' ), true );
	wp_add_inline_style( 'wp-admin', '.akz-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.akz-box h4{margin:22px 0 6px;font-size:14px;border-top:1px solid #dcdcde;padding-top:14px}.akz-gallery{display:flex;flex-wrap:wrap;gap:8px;min-height:40px}.akz-thumb{position:relative;display:inline-block;cursor:move}.akz-thumb img{width:84px;height:84px;object-fit:cover;border-radius:4px;display:block}.akz-x{position:absolute;top:2px;right:2px;border:0;background:#1d2327;color:#fff;border-radius:50%;width:20px;height:20px;line-height:18px;cursor:pointer}.akz-step{display:grid;gap:6px;padding:10px;margin-bottom:8px;border:1px solid #dcdcde;border-radius:4px;background:#f6f7f7}.akz-step-img img{width:64px;height:64px;object-fit:cover;border-radius:4px}.akz-term-thumb img{width:80px;height:80px;object-fit:cover;border-radius:4px;display:block;margin-bottom:6px}' );
}
add_action( 'admin_enqueue_scripts', 'akzara_admin_assets' );
