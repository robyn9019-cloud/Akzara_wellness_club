<?php
/**
 * Contenido de muestra: al activar el tema crea categorías, rituales, membresías, preguntas,
 * entradas, páginas y menús, y sube las fotos de referencia a la Mediateca.
 * Solo crea lo que falta: se puede volver a ejecutar desde Herramientas → Contenido de Akzara.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Al activar el tema se deja una marca; el contenido se crea en la siguiente pantalla del escritorio
 * (para entonces los tipos de contenido del tema ya están registrados).
 */
function akzara_seed_flag() {
	if ( ! get_option( 'akzara_seeded' ) ) {
		update_option( 'akzara_seed_pending', 1 );
	}
}
add_action( 'after_switch_theme', 'akzara_seed_flag' );

/**
 * Ejecuta la carga pendiente.
 */
function akzara_seed_maybe_run() {
	if ( get_option( 'akzara_seed_pending' ) && current_user_can( 'manage_options' ) ) {
		delete_option( 'akzara_seed_pending' );
		akzara_seed_run();
	}
}
add_action( 'admin_init', 'akzara_seed_maybe_run' );

/**
 * Texto con barras (wp_insert_post y los metadatos esperan datos «con barras»).
 *
 * @param mixed $v Valor.
 * @return mixed
 */
function akzara_seed_s( $v ) {
	return is_string( $v ) ? wp_slash( $v ) : $v;
}

/**
 * Busca una entrada por tipo y slug.
 *
 * @param string $type Tipo.
 * @param string $slug Slug.
 * @return int ID o 0.
 */
function akzara_seed_find( $type, $slug ) {
	$found = get_posts(
		array(
			'post_type'      => $type,
			'name'           => $slug,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * Sube las fotos de assets/img/demo a la Mediateca (una sola vez).
 *
 * @return array clave => ID del adjunto.
 */
function akzara_seed_media() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$alts = array(
		'jardin'    => 'Jardín de bambú de Akzara Wellness Club',
		'piscina'   => 'Piscina exterior rodeada de vegetación',
		'arco'      => 'Arco de piedra junto al agua',
		'velas'     => 'Velas encendidas en una sala de masaje',
		'calientes' => 'Piedras calientes para el ritual',
		'eucalipto' => 'Ramas de eucalipto del baño turco',
		'ventana'   => 'Ventanal con vista al jardín',
		'arboles'   => 'Árboles del jardín de Akzara',
	);
	$out  = array();
	$dir  = AKZARA_DIR . '/assets/img/demo/';
	foreach ( $alts as $key => $alt ) {
		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'meta_key'       => '_akzara_demo', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $existing ) {
			$out[ $key ] = (int) $existing[0];
			continue;
		}
		$src = $dir . $key . '.webp';
		if ( ! file_exists( $src ) ) {
			continue;
		}
		$bits = wp_upload_bits( 'akzara-' . $key . '.webp', null, file_get_contents( $src ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( ! empty( $bits['error'] ) ) {
			continue;
		}
		$id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/webp',
				'post_title'     => 'Akzara · ' . $key . ' (reemplazar con foto real)',
				'post_status'    => 'inherit',
			),
			$bits['file']
		);
		if ( is_wp_error( $id ) || ! $id ) {
			continue;
		}
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $bits['file'] ) );
		update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $alt ) );
		update_post_meta( $id, '_akzara_demo', $key );
		$out[ $key ] = (int) $id;
	}
	return $out;
}

/**
 * Crea una página (o devuelve la que ya existe).
 *
 * @param string $slug     Slug.
 * @param string $title    Título.
 * @param array  $extra    parent, template, content, excerpt, order.
 * @return int
 */
function akzara_seed_page( $slug, $title, $extra = array() ) {
	$id = akzara_seed_find( 'page', $slug );
	if ( $id ) {
		return $id;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => wp_slash( $title ),
			'post_name'    => $slug,
			'post_parent'  => isset( $extra['parent'] ) ? (int) $extra['parent'] : 0,
			'post_content' => isset( $extra['content'] ) ? wp_slash( $extra['content'] ) : '',
			'post_excerpt' => isset( $extra['excerpt'] ) ? wp_slash( $extra['excerpt'] ) : '',
			'menu_order'   => isset( $extra['order'] ) ? (int) $extra['order'] : 0,
		)
	);
	if ( $id && ! is_wp_error( $id ) && ! empty( $extra['template'] ) ) {
		update_post_meta( $id, '_wp_page_template', $extra['template'] );
	}
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Carga todo el contenido de muestra.
 */
function akzara_seed_run() {
	@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	$file = AKZARA_DIR . '/data/demo.json';
	if ( ! file_exists( $file ) ) {
		return;
	}
	$d = json_decode( file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! is_array( $d ) ) {
		return;
	}
	// Los tipos de contenido deben existir en esta petición.
	if ( ! post_type_exists( 'akzara_ritual' ) ) {
		return;
	}

	$img = akzara_seed_media();
	$id  = function ( $key ) use ( $img ) {
		return isset( $img[ $key ] ) ? $img[ $key ] : 0;
	};

	// Ocasiones.
	foreach ( array( 'pareja' => 'En pareja', 'para-mi' => 'Para mí', 'celebracion' => 'Celebración', 'regalo' => 'Regalo' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'akzara_ocasion' ) ) {
			wp_insert_term( $name, 'akzara_ocasion', array( 'slug' => $slug ) );
		}
	}

	// Categorías.
	$cats = array();
	foreach ( $d['categories'] as $c ) {
		$t = term_exists( $c['slug'], 'akzara_categoria' );
		if ( ! $t ) {
			$t = wp_insert_term( $c['name'], 'akzara_categoria', array( 'slug' => $c['slug'] ) );
		}
		if ( is_wp_error( $t ) ) {
			continue;
		}
		$tid = (int) ( is_array( $t ) ? $t['term_id'] : $t );
		update_term_meta( $tid, '_ak_eyebrow', akzara_seed_s( $c['eyebrow'] ) );
		update_term_meta( $tid, '_ak_intro', akzara_seed_s( $c['intro'] ) );
		update_term_meta( $tid, '_ak_menu', akzara_seed_s( $c['menuText'] ) );
		update_term_meta( $tid, '_ak_order', (int) $c['order'] );
		if ( ! empty( $c['photo'] ) && $id( $c['photo'] ) ) {
			update_term_meta( $tid, '_ak_image', $id( $c['photo'] ) );
		}
		$cats[ $c['slug'] ] = $c['slug'];
	}

	// Rituales.
	foreach ( $d['services'] as $s ) {
		if ( akzara_seed_find( 'akzara_ritual', $s['slug'] ) ) {
			continue;
		}
		$pid = wp_insert_post(
			array(
				'post_type'    => 'akzara_ritual',
				'post_status'  => 'publish',
				'post_title'   => wp_slash( $s['name'] ),
				'post_name'    => $s['slug'],
				'post_excerpt' => wp_slash( $s['summary'] ),
				'menu_order'   => (int) $s['order'],
			)
		);
		if ( ! $pid || is_wp_error( $pid ) ) {
			continue;
		}
		$people = max( 1, (int) $s['people'] );
		$gal    = array();
		foreach ( (array) $s['photos'] as $ph ) {
			if ( $id( $ph ) ) {
				$gal[] = $id( $ph );
			}
		}
		$steps = array();
		foreach ( (array) $s['steps'] as $st ) {
			$steps[] = array(
				'title' => $st['title'],
				'text'  => isset( $st['text'] ) ? $st['text'] : '',
				'image' => ! empty( $st['photo'] ) ? $id( $st['photo'] ) : 0,
			);
		}
		$meta = array(
			'_ak_min'       => (int) $s['min'],
			'_ak_price'     => (int) $s['price'],
			'_ak_deposit'   => 50,
			'_ak_mode'      => $people > 1 ? 'grupo' : 'persona',
			'_ak_minp'      => $people,
			'_ak_maxp'      => $people,
			'_ak_badge'     => (string) $s['badge'],
			'_ak_featured'  => (int) $s['featured'] > 0 ? 1 : 0,
			'_ak_signature' => ! empty( $s['signature'] ) ? 1 : 0,
			'_ak_giftable'  => 1,
			'_ak_tone'      => (string) $s['tone'],
			'_ak_gallery'   => implode( ',', $gal ),
			'_ak_steps'     => wp_json_encode( $steps ),
		);
		foreach ( $meta as $k => $v ) {
			update_post_meta( $pid, $k, akzara_seed_s( $v ) );
		}
		wp_set_object_terms( $pid, array( $s['cat'] ), 'akzara_categoria' );
		wp_set_object_terms( $pid, (array) $s['occ'], 'akzara_ocasion' );
		if ( $gal ) {
			set_post_thumbnail( $pid, $gal[0] );
		}
	}

	// Membresías.
	foreach ( $d['memberships'] as $m ) {
		$slug = sanitize_title( $m['name'] );
		if ( akzara_seed_find( 'akzara_membresia', $slug ) ) {
			continue;
		}
		$pid = wp_insert_post(
			array(
				'post_type'   => 'akzara_membresia',
				'post_status' => 'publish',
				'post_title'  => wp_slash( $m['name'] ),
				'post_name'   => $slug,
				'menu_order'  => (int) $m['order'],
			)
		);
		if ( $pid && ! is_wp_error( $pid ) ) {
			update_post_meta( $pid, '_ak_period', akzara_seed_s( $m['period'] ) );
			update_post_meta( $pid, '_ak_price', (int) $m['price'] );
			update_post_meta( $pid, '_ak_button', 'Conocer' );
			update_post_meta( $pid, '_ak_benefits', akzara_seed_s( implode( "\n", $m['benefits'] ) ) );
			update_post_meta( $pid, '_ak_highlight', ! empty( $m['highlight'] ) ? 1 : 0 );
		}
	}

	// Preguntas frecuentes.
	foreach ( $d['faqs'] as $f ) {
		$slug = sanitize_title( $f['q'] );
		if ( akzara_seed_find( 'akzara_faq', $slug ) ) {
			continue;
		}
		wp_insert_post(
			array(
				'post_type'    => 'akzara_faq',
				'post_status'  => 'publish',
				'post_title'   => wp_slash( $f['q'] ),
				'post_name'    => $slug,
				'post_content' => wp_slash( $f['a'] ),
				'menu_order'   => (int) $f['order'],
			)
		);
	}

	// Páginas.
	$home   = akzara_seed_page( 'inicio', 'Inicio' );
	$diario = akzara_seed_page( 'diario', 'Diario', array( 'excerpt' => 'Notas sobre bienestar, agua y calma.' ) );
	akzara_seed_page( 'membresias', 'Membresías del Club', array( 'template' => 'page-membresias.php' ) );
	akzara_seed_page( 'instalaciones', 'Instalaciones', array( 'template' => 'page-instalaciones.php' ) );
	akzara_seed_page( 'el-club', 'El Club', array( 'template' => 'page-el-club.php' ) );
	akzara_seed_page( 'contacto', 'Contacto', array( 'template' => 'page-contacto.php' ) );
	akzara_seed_page( 'reservar', 'Reservar', array( 'template' => 'page-reservas.php' ) );
	akzara_seed_page( 'gift-card', 'Gift card', array( 'template' => 'page-gift-card.php' ) );
	$pol = akzara_seed_page( 'politicas', 'Políticas', array( 'template' => 'page-politicas.php', 'content' => '<p>Elija la política que desea consultar.</p>' ) );
	foreach ( $d['policies'] as $i => $p ) {
		akzara_seed_page(
			$p['slug'],
			$p['title'],
			array(
				'parent'   => $pol,
				'template' => 'page-politicas.php',
				'content'  => wpautop( $p['body'] ),
				'order'    => $i + 1,
			)
		);
	}

	// Entradas del Diario.
	foreach ( $d['posts'] as $p ) {
		if ( akzara_seed_find( 'post', $p['slug'] ) ) {
			continue;
		}
		$pid = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => wp_slash( $p['title'] ),
				'post_name'    => $p['slug'],
				'post_excerpt' => wp_slash( $p['excerpt'] ),
				'post_content' => wp_slash( $p['content'] ),
				'post_date'    => $p['date'] . ' 09:00:00',
			)
		);
		if ( $pid && ! is_wp_error( $pid ) && ! empty( $p['photo'] ) && $id( $p['photo'] ) ) {
			set_post_thumbnail( $pid, $id( $p['photo'] ) );
		}
	}

	// Lectura y enlaces permanentes.
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}
	if ( $diario ) {
		update_option( 'page_for_posts', $diario );
	}
	update_option( 'blogname', 'Akzara Wellness Club' );
	update_option( 'blogdescription', 'Spa en Medellín: el único jardín de la Milla de Oro' );
	update_option( 'permalink_structure', '/diario/%postname%/' );

	// Imágenes del Personalizador.
	$mods = array(
		'hero_poster'    => 'jardin',
		'manifesto_image' => 'velas',
		'occ1_image'     => 'arco',
		'occ2_image'     => 'eucalipto',
		'occ3_image'     => 'calientes',
		'occ4_image'     => 'ventana',
		'j1_image'       => 'ventana',
		'j2_image'       => 'piscina',
		'j3_image'       => 'calientes',
		'j4_image'       => 'jardin',
		'exp1_image'     => 'arboles',
		'exp2_image'     => 'ventana',
		'fac1_image'     => 'jardin',
		'fac2_image'     => 'piscina',
		'fac3_image'     => 'ventana',
		'club_image'     => 'arboles',
		'p1_image'       => 'arboles',
		'p2_image'       => 'piscina',
		'p3_image'       => 'velas',
		'promo_image'    => 'eucalipto',
	);
	foreach ( $mods as $k => $photo ) {
		if ( $id( $photo ) && ! get_theme_mod( 'akzara_' . $k ) ) {
			set_theme_mod( 'akzara_' . $k, $id( $photo ) );
		}
	}
	if ( ! get_theme_mod( 'akzara_gallery_ids' ) ) {
		set_theme_mod( 'akzara_gallery_ids', implode( ',', array_filter( array( $id( 'jardin' ), $id( 'piscina' ), $id( 'arco' ), $id( 'velas' ), $id( 'arboles' ) ) ) ) );
	}

	// Menús (El Club y Legal).
	akzara_seed_menu(
		'Akzara · El Club',
		'akzara_club',
		array(
			array( 'Instalaciones', 'instalaciones' ),
			array( 'Filosofía', 'el-club' ),
			array( 'Diario', 'diario' ),
			array( 'Contacto', 'contacto' ),
		)
	);
	$legal = array();
	foreach ( $d['policies'] as $p ) {
		$legal[] = array( $p['title'], 'politicas/' . $p['slug'] );
	}
	akzara_seed_menu( 'Akzara · Legal', 'akzara_legal', $legal );

	update_option( 'akzara_seeded', 1 );
	flush_rewrite_rules( false );
}

/**
 * Crea un menú con enlaces a páginas y lo asigna a una ubicación (si no tiene menú).
 *
 * @param string $name     Nombre.
 * @param string $location Ubicación.
 * @param array  $items    [[etiqueta, ruta de la página], …].
 */
function akzara_seed_menu( $name, $location, $items ) {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations[ $location ] ) ) {
		return;
	}
	$menu = wp_get_nav_menu_object( $name );
	$mid  = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $name );
	if ( ! $mid || is_wp_error( $mid ) ) {
		return;
	}
	if ( ! wp_get_nav_menu_items( $mid ) ) {
		foreach ( $items as $it ) {
			$page = get_page_by_path( $it[1] );
			if ( ! $page ) {
				continue;
			}
			wp_update_nav_menu_item(
				$mid,
				0,
				array(
					'menu-item-title'     => $it[0],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}
	}
	$locations[ $location ] = $mid;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Herramientas → Contenido de Akzara (volver a cargar lo que falte).
 */
function akzara_seed_admin_menu() {
	add_management_page( 'Contenido de Akzara', 'Contenido de Akzara', 'manage_options', 'akzara-contenido', 'akzara_seed_admin_page' );
}
add_action( 'admin_menu', 'akzara_seed_admin_menu' );

/**
 * Pantalla de la herramienta.
 */
function akzara_seed_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['akzara_seed_go'] ) && check_admin_referer( 'akzara_seed' ) ) {
		akzara_seed_run();
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Listo: se creó el contenido que faltaba. No se modificó nada de lo que ya existía.', 'akzara' ) . '</p></div>';
	}
	?>
	<div class="wrap">
		<h1>Contenido de Akzara</h1>
		<p>Crea los rituales, categorías, membresías, preguntas, entradas, páginas y menús de muestra, y sube las fotos de referencia. Solo agrega lo que falte.</p>
		<form method="post">
			<?php wp_nonce_field( 'akzara_seed' ); ?>
			<p><button class="button button-primary" name="akzara_seed_go" value="1">Cargar contenido de muestra</button></p>
		</form>
	</div>
	<?php
}
