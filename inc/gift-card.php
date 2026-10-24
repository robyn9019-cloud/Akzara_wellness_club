<?php
/**
 * Gift card: formulario del tema (reemplaza al flujo de Next.js), solicitudes en el escritorio y correos.
 * Amelia no vende gift cards: aquí se recibe la solicitud y el pago se confirma a mano.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AKZARA_GIFT_MIN', 100000 );
define( 'AKZARA_GIFT_MAX', 5000000 );

/**
 * Diseños de la tarjeta: [nombre, variante, frase ES, frase EN].
 *
 * @return array
 */
function akzara_gift_designs() {
	return apply_filters(
		'akzara_gift_designs',
		array(
			array( 'Calma', 'lino', 'El lujo no hace ruido.', 'True luxury makes no noise.' ),
			array( 'Jardín', 'jardin', 'Un respiro verde.', 'A green pause.' ),
			array( 'Agua', 'grafito', 'Bienvenido a la calma.', 'Welcome to calm.' ),
			array( 'Bronce', 'bronce', 'Solo hay que llegar.', 'All you have to do is arrive.' ),
			array( 'Lino', 'lino', 'Para volver a usted.', 'To come back to yourself.' ),
			array( 'Noche', 'grafito', 'El silencio también es un regalo.', 'Silence is a gift too.' ),
			array( 'Ritual', 'bronce', 'Un ritual, sin prisa.', 'A ritual, unhurried.' ),
			array( 'Bambú', 'jardin', 'Entre bambú y agua.', 'Between bamboo and water.' ),
		)
	);
}

/**
 * Datos que usa el JavaScript de la página de gift card.
 *
 * @return array
 */
function akzara_gift_script_data() {
	$list = array();
	foreach ( akzara_rituals() as $r ) {
		if ( ! $r['giftable'] ) {
			continue;
		}
		$list[] = array(
			'id'      => $r['id'],
			'slug'    => $r['slug'],
			'name'    => $r['name'],
			'min'     => $r['min'],
			'people'  => $r['min_people'],
			'price'   => $r['price'],
			'summary' => $r['summary'],
		);
	}
	return array(
		'rituals' => $list,
		'designs' => akzara_gift_designs(),
		'amounts' => array( 200000, 300000, 500000, 1000000 ),
		'min'     => AKZARA_GIFT_MIN,
		'max'     => AKZARA_GIFT_MAX,
		'iso'     => akzara_img_uri( 'isotipo.webp' ),
		'initial' => isset( $_GET['ritual'] ) ? sanitize_title( wp_unslash( $_GET['ritual'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification
	);
}

/**
 * Solicitudes de gift card (solo en el escritorio).
 */
function akzara_register_gift_type() {
	register_post_type(
		'akzara_gift',
		array(
			'labels'        => array(
				'name'          => __( 'Gift cards', 'akzara' ),
				'singular_name' => __( 'Gift card', 'akzara' ),
				'edit_item'     => __( 'Gift card', 'akzara' ),
				'search_items'  => __( 'Buscar gift cards', 'akzara' ),
				'not_found'     => __( 'Aún no hay solicitudes.', 'akzara' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'menu_icon'     => 'dashicons-tickets-alt',
			'menu_position' => 9,
			'supports'      => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'  => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'  => true,
		)
	);
}
add_action( 'init', 'akzara_register_gift_type' );

/**
 * Cuadro con los datos y el estado de la gift card.
 */
function akzara_gift_meta_box() {
	add_meta_box(
		'akzara_gift_data',
		__( 'Detalle de la solicitud', 'akzara' ),
		function ( $post ) {
			wp_nonce_field( 'akzara_gift_save', 'akzara_gift_nonce' );
			$m = function ( $k ) use ( $post ) {
				return (string) get_post_meta( $post->ID, $k, true );
			};
			$status = $m( '_ak_gstatus' ) ? $m( '_ak_gstatus' ) : 'pendiente';
			echo '<p><strong>' . esc_html__( 'Estado', 'akzara' ) . '</strong><br><select name="_ak_gstatus">';
			foreach ( array( 'pendiente' => 'Pendiente de pago', 'pagada' => 'Pagada', 'canjeada' => 'Canjeada', 'vencida' => 'Vencida', 'anulada' => 'Anulada' ) as $v => $l ) {
				echo '<option value="' . esc_attr( $v ) . '"' . selected( $status, $v, false ) . '>' . esc_html( $l ) . '</option>';
			}
			echo '</select><br><span class="description">' . esc_html__( 'Al pasar a «Pagada» se crean el código y las fechas de emisión y vencimiento, y se avisa al comprador por correo.', 'akzara' ) . '</span></p>';
			echo '<table class="widefat striped"><tbody>';
			$rows = array(
				__( 'Código', 'akzara' )       => $m( '_ak_code' ) ? $m( '_ak_code' ) : '—',
				__( 'Valor', 'akzara' )        => akzara_cop( $m( '_ak_value' ) ),
				__( 'Tipo', 'akzara' )         => 'ritual' === $m( '_ak_mode' ) ? __( 'Un ritual', 'akzara' ) . ': ' . $m( '_ak_ritual_name' ) : __( 'Por monto', 'akzara' ),
				__( 'Diseño', 'akzara' )       => $m( '_ak_design' ),
				__( 'Idioma de la tarjeta', 'akzara' ) => strtoupper( $m( '_ak_lang' ) ),
				__( 'Para', 'akzara' )         => $m( '_ak_recipient' ),
				__( 'De parte de', 'akzara' )  => $m( '_ak_from' ),
				__( 'Mensaje', 'akzara' )      => $m( '_ak_message' ),
				__( 'Comprador', 'akzara' )    => $m( '_ak_buyer' ),
				__( 'Correo', 'akzara' )       => $m( '_ak_email' ),
				__( 'Celular', 'akzara' )      => $m( '_ak_phone' ),
				__( 'Emitida', 'akzara' )      => $m( '_ak_issued' ) ? $m( '_ak_issued' ) : '—',
				__( 'Vence', 'akzara' )        => $m( '_ak_expires' ) ? $m( '_ak_expires' ) : '—',
			);
			foreach ( $rows as $k => $v ) {
				echo '<tr><th style="width:190px">' . esc_html( $k ) . '</th><td>' . esc_html( $v ) . '</td></tr>';
			}
			echo '</tbody></table>';
		},
		'akzara_gift',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'akzara_gift_meta_box' );

/**
 * Código único: AKZ-XXXX-XXXX.
 *
 * @return string
 */
function akzara_gift_code() {
	$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
	$part     = function () use ( $alphabet ) {
		$s = '';
		for ( $i = 0; $i < 4; $i++ ) {
			$s .= $alphabet[ random_int( 0, strlen( $alphabet ) - 1 ) ];
		}
		return $s;
	};
	return 'AKZ-' . $part() . '-' . $part();
}

/**
 * Al guardar: cambia el estado y, si pasa a «Pagada», crea el código y las fechas.
 *
 * @param int $post_id Gift card.
 */
function akzara_gift_save( $post_id ) {
	if ( ! isset( $_POST['akzara_gift_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['akzara_gift_nonce'] ) ), 'akzara_gift_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}
	$allowed = array( 'pendiente', 'pagada', 'canjeada', 'vencida', 'anulada' );
	$new     = isset( $_POST['_ak_gstatus'] ) ? sanitize_key( wp_unslash( $_POST['_ak_gstatus'] ) ) : 'pendiente';
	$new     = in_array( $new, $allowed, true ) ? $new : 'pendiente';
	$old     = (string) get_post_meta( $post_id, '_ak_gstatus', true );
	update_post_meta( $post_id, '_ak_gstatus', $new );

	if ( 'pagada' === $new && 'pagada' !== $old && ! get_post_meta( $post_id, '_ak_code', true ) ) {
		update_post_meta( $post_id, '_ak_code', akzara_gift_code() );
		update_post_meta( $post_id, '_ak_issued', wp_date( 'Y-m-d' ) );
		update_post_meta( $post_id, '_ak_expires', wp_date( 'Y-m-d', strtotime( '+1 year' ) ) );
		akzara_gift_mail_paid( $post_id );
	}
}
add_action( 'save_post_akzara_gift', 'akzara_gift_save' );

/**
 * Columnas de la lista.
 *
 * @param array $cols Columnas.
 * @return array
 */
function akzara_gift_columns( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => __( 'Referencia', 'akzara' ),
		'akz_buyer' => __( 'Comprador', 'akzara' ),
		'akz_to'    => __( 'Para', 'akzara' ),
		'akz_value' => __( 'Valor', 'akzara' ),
		'akz_state' => __( 'Estado', 'akzara' ),
		'akz_code'  => __( 'Código', 'akzara' ),
		'date'      => $cols['date'],
	);
}
add_filter( 'manage_akzara_gift_posts_columns', 'akzara_gift_columns' );

/**
 * Contenido de las columnas.
 *
 * @param string $col Columna.
 * @param int    $id  Gift card.
 */
function akzara_gift_column_content( $col, $id ) {
	$map = array(
		'akz_buyer' => '_ak_buyer',
		'akz_to'    => '_ak_recipient',
		'akz_code'  => '_ak_code',
	);
	if ( isset( $map[ $col ] ) ) {
		echo esc_html( (string) get_post_meta( $id, $map[ $col ], true ) );
	} elseif ( 'akz_value' === $col ) {
		echo esc_html( akzara_cop( get_post_meta( $id, '_ak_value', true ) ) );
	} elseif ( 'akz_state' === $col ) {
		$s = (string) get_post_meta( $id, '_ak_gstatus', true );
		echo esc_html( $s ? ucfirst( $s ) : 'Pendiente' );
	}
}
add_action( 'manage_akzara_gift_posts_custom_column', 'akzara_gift_column_content', 10, 2 );

/**
 * Correo HTML con la marca (estilos en línea).
 *
 * @param string $title Título.
 * @param string $intro Introducción.
 * @param array  $rows  [[etiqueta, valor], …].
 * @param string $note  Nota final.
 * @return string
 */
function akzara_email_html( $title, $intro, $rows = array(), $note = '' ) {
	$tr = '';
	foreach ( $rows as $r ) {
		$tr .= '<tr><td style="padding:11px 0;border-bottom:1px solid #d9d3c9;font:12px Arial,sans-serif;letter-spacing:.12em;text-transform:uppercase;color:#686459">' . esc_html( $r[0] ) . '</td><td style="padding:11px 0;border-bottom:1px solid #d9d3c9;font:15px Georgia,serif;color:#2b2b2a;text-align:right">' . esc_html( $r[1] ) . '</td></tr>';
	}
	return '<!doctype html><html><body style="margin:0;background:#eae6df"><table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eae6df"><tr><td align="center" style="padding:32px 14px"><table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%"><tr><td align="center" style="padding:6px 0 26px"><img src="' . esc_url( akzara_img_uri( 'logo.webp' ) ) . '" width="170" alt="Akzara Wellness Club" style="display:block;height:auto;border:0"></td></tr><tr><td style="background:#f4f1ec;border:1px solid #e3ddd3;padding:38px 34px"><h1 style="margin:0 0 14px;font:400 30px/1.15 Georgia,serif;color:#2b2b2a">' . esc_html( $title ) . '</h1><p style="margin:0 0 24px;font:15px/1.7 Arial,sans-serif;color:#55534f">' . esc_html( $intro ) . '</p>' . ( $tr ? '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #d9d3c9;margin:0 0 24px">' . $tr . '</table>' : '' ) . ( $note ? '<p style="margin:0;font:12.5px/1.6 Arial,sans-serif;color:#686459">' . esc_html( $note ) . '</p>' : '' ) . '</td></tr><tr><td align="center" style="padding:22px 0 0;font:italic 17px Georgia,serif;color:#7b5f3c">El lujo no hace ruido.</td></tr></table></td></tr></table></body></html>';
}

/**
 * Envía un correo HTML.
 *
 * @param string $to      Destino.
 * @param string $subject Asunto.
 * @param string $html    Contenido.
 */
function akzara_send_html( $to, $subject, $html ) {
	if ( ! is_email( $to ) ) {
		return;
	}
	wp_mail( $to, $subject, $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
}

/**
 * Correo al equipo.
 *
 * @return string
 */
function akzara_team_email() {
	$to = (string) akzara_opt( 'gift_notify' );
	if ( ! is_email( $to ) ) {
		$to = (string) akzara_opt( 'email' );
	}
	return is_email( $to ) ? $to : get_option( 'admin_email' );
}

/**
 * Aviso al comprador cuando la gift card queda pagada.
 *
 * @param int $id Gift card.
 */
function akzara_gift_mail_paid( $id ) {
	$m  = function ( $k ) use ( $id ) {
		return (string) get_post_meta( $id, $k, true );
	};
	$en = 'en' === $m( '_ak_lang' );
	akzara_send_html(
		$m( '_ak_email' ),
		$en ? 'Your gift card is ready · ' . $m( '_ak_code' ) : 'Su gift card está lista · ' . $m( '_ak_code' ),
		akzara_email_html(
			$en ? 'Your gift card is ready.' : 'Su gift card está lista.',
			$en ? 'Payment confirmed. Here is the code for ' . $m( '_ak_recipient' ) . '.' : 'Pago confirmado. Este es el código para ' . $m( '_ak_recipient' ) . '.',
			array(
				array( $en ? 'Code' : 'Código', $m( '_ak_code' ) ),
				array( $en ? 'Value' : 'Valor', akzara_cop( $m( '_ak_value' ) ) ),
				array( $en ? 'Valid until' : 'Vigente hasta', $m( '_ak_expires' ) ),
			),
			$en ? 'Present the code at reception or when booking.' : 'Presente el código en recepción o al reservar.'
		)
	);
}

/**
 * Ruta REST pública que recibe la solicitud del formulario.
 */
function akzara_register_rest() {
	register_rest_route(
		'akzara/v1',
		'/gift-card',
		array(
			'methods'             => 'POST',
			'callback'            => 'akzara_gift_submit',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'akzara_register_rest' );

/**
 * IP del visitante (para el límite de envíos).
 *
 * @return string
 */
function akzara_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	return $ip;
}

/**
 * Recibe y valida la solicitud de gift card. Todo se vuelve a comprobar aquí: no se confía en el navegador.
 *
 * @param WP_REST_Request $req Petición.
 * @return WP_REST_Response|WP_Error
 */
function akzara_gift_submit( WP_REST_Request $req ) {
	$err = function ( $msg, $code = 400 ) {
		return new WP_Error( 'akzara_gift', $msg, array( 'status' => $code ) );
	};

	// Límite: 5 solicitudes cada 10 minutos por IP.
	$key   = 'akz_gift_' . md5( akzara_client_ip() );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return $err( 'Demasiados intentos. Por favor, espere unos minutos e intente de nuevo.', 429 );
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$p = $req->get_json_params();
	$p = is_array( $p ) ? $p : array();

	// Trampa para robots: un campo oculto que una persona nunca llena.
	if ( ! empty( $p['website'] ) ) {
		return rest_ensure_response( array( 'ok' => true, 'reference' => 'GC-OK' ) );
	}

	$mode = isset( $p['mode'] ) && 'monto' === $p['mode'] ? 'monto' : 'ritual';
	$name = '';
	$val  = 0;
	if ( 'ritual' === $mode ) {
		$rid = isset( $p['service'] ) ? absint( $p['service'] ) : 0;
		$r   = $rid ? get_post( $rid ) : null;
		if ( ! $r || 'akzara_ritual' !== $r->post_type || 'publish' !== $r->post_status || '0' === (string) get_post_meta( $rid, '_ak_giftable', true ) ) {
			return $err( 'Elija el ritual que desea regalar.' );
		}
		$name = get_the_title( $r );
		$val  = (int) get_post_meta( $rid, '_ak_price', true );
	} else {
		$val = isset( $p['value'] ) ? (int) round( (float) $p['value'] ) : 0;
		if ( $val < AKZARA_GIFT_MIN || $val > AKZARA_GIFT_MAX ) {
			return $err( 'El monto debe estar entre $ 100.000 y $ 5.000.000.' );
		}
	}

	$recipient = isset( $p['recipient'] ) ? mb_substr( sanitize_text_field( $p['recipient'] ), 0, 120 ) : '';
	$from      = isset( $p['from'] ) ? mb_substr( sanitize_text_field( $p['from'] ), 0, 120 ) : '';
	$buyer     = isset( $p['buyerName'] ) ? mb_substr( sanitize_text_field( $p['buyerName'] ), 0, 120 ) : '';
	$email     = isset( $p['buyerEmail'] ) ? sanitize_email( $p['buyerEmail'] ) : '';
	$phone     = isset( $p['buyerPhone'] ) ? mb_substr( sanitize_text_field( $p['buyerPhone'] ), 0, 30 ) : '';
	$message   = isset( $p['message'] ) ? mb_substr( sanitize_textarea_field( $p['message'] ), 0, 300 ) : '';
	$lang      = isset( $p['cardLanguage'] ) && 'en' === $p['cardLanguage'] ? 'en' : 'es';
	$design    = isset( $p['design'] ) ? absint( $p['design'] ) : 0;
	$designs   = akzara_gift_designs();
	$design    = isset( $designs[ $design ] ) ? $designs[ $design ][0] : $designs[0][0];

	if ( '' === $recipient || '' === $from || '' === $buyer || ! is_email( $email ) || ! preg_match( '/^\+?[\d\s()-]{7,}$/', $phone ) ) {
		return $err( 'Revise los datos del formulario.' );
	}
	if ( empty( $p['consent'] ) ) {
		return $err( 'Debe autorizar el tratamiento de datos.' );
	}

	$ref = 'GC-' . strtoupper( base_convert( (string) time(), 10, 36 ) ) . strtoupper( wp_generate_password( 2, false, false ) );
	$id  = wp_insert_post(
		array(
			'post_type'   => 'akzara_gift',
			'post_status' => 'publish',
			'post_title'  => $ref,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $err( 'No pudimos enviar la solicitud. Intente de nuevo o escríbanos por WhatsApp.', 500 );
	}
	$meta = array(
		'_ak_gstatus'     => 'pendiente',
		'_ak_mode'        => $mode,
		'_ak_ritual_name' => $name,
		'_ak_value'       => $val,
		'_ak_design'      => $design,
		'_ak_lang'        => $lang,
		'_ak_recipient'   => $recipient,
		'_ak_from'        => $from,
		'_ak_message'     => $message,
		'_ak_buyer'       => $buyer,
		'_ak_email'       => $email,
		'_ak_phone'       => $phone,
	);
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}

	$en   = 'en' === $lang;
	$rows = array(
		array( $en ? 'Reference' : 'Referencia', $ref ),
		array( $en ? 'Value' : 'Valor', akzara_cop( $val ) ),
		array( $en ? 'For' : 'Para', $recipient ),
		array( $en ? 'From' : 'De parte de', $from ),
	);
	akzara_send_html(
		$email,
		$en ? 'We received your gift card request · ' . $ref : 'Recibimos su solicitud de gift card · ' . $ref,
		akzara_email_html(
			$en ? 'Request received.' : 'Solicitud recibida.',
			$en ? 'We will send you the payment link; once it is confirmed we create the code and the gift card.' : 'Le enviaremos el enlace de pago; cuando se confirme, generamos el código y la gift card.',
			$rows
		)
	);
	akzara_send_html(
		akzara_team_email(),
		'Nueva gift card · ' . $ref . ' · ' . $buyer,
		akzara_email_html( 'Nueva solicitud de gift card', $buyer . ' · ' . $phone . ' · ' . $email, $rows, admin_url( 'post.php?post=' . $id . '&action=edit' ) )
	);

	return rest_ensure_response( array( 'ok' => true, 'reference' => $ref ) );
}
