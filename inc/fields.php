<?php
/**
 * Registro de los campos editables del Personalizador (textos, imágenes y ajustes).
 * Es la única fuente de verdad: de aquí salen los controles y los valores por defecto.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Secciones del Personalizador.
 *
 * @return array
 */
function akzara_sections() {
	return array(
		'marca'         => array( __( 'Marca y contacto', 'akzara' ), __( 'Datos que se repiten en el pie, el contacto y los botones de WhatsApp.', 'akzara' ) ),
		'colores'       => array( __( 'Colores', 'akzara' ), __( 'Colores de marca (Brand Book). Cambie con cuidado: el contraste debe permitir leer bien.', 'akzara' ) ),
		'menu'          => array( __( 'Menú y botones', 'akzara' ), __( 'Textos del menú superior y de las dos tarjetas del menú «Experiencias». Los enlaces de «El Club» se editan en Apariencia → Menús.', 'akzara' ) ),
		'portada'       => array( __( 'Inicio · Portada', 'akzara' ), __( 'Pantalla principal. Una palabra entre *asteriscos* sale en cursiva.', 'akzara' ) ),
		'inicio'        => array( __( 'Inicio · Secciones', 'akzara' ), __( 'Textos e imágenes de cada sección de la página de inicio.', 'akzara' ) ),
		'instalaciones' => array( __( 'Página Instalaciones', 'akzara' ), '' ),
		'club'          => array( __( 'Página El Club', 'akzara' ), '' ),
		'membresias'    => array( __( 'Página Membresías', 'akzara' ), '' ),
		'reservas'      => array( __( 'Página Reservar', 'akzara' ), __( 'La página «Reservar» imprime lo que escriba en su contenido: ahí va el shortcode de Amelia.', 'akzara' ) ),
		'gift'          => array( __( 'Gift card', 'akzara' ), __( 'Las solicitudes llegan a «Gift cards» en el escritorio y por correo.', 'akzara' ) ),
		'aviso'         => array( __( 'Aviso emergente', 'akzara' ), __( 'Ventana pequeña con una promoción.', 'akzara' ) ),
		'seguimiento'   => array( __( 'Seguimiento (Pixel y Analytics)', 'akzara' ), __( 'Pegue solo el identificador. Vacío = no se carga nada.', 'akzara' ) ),
		'idioma'        => array( __( 'Idioma', 'akzara' ), '' ),
	);
}

/**
 * Campos: clave => [sección, etiqueta, tipo, valor por defecto, ayuda].
 * Tipos: text, textarea, url, checkbox, color, number, image, video.
 *
 * @return array
 */
function akzara_fields() {
	static $f = null;
	if ( null !== $f ) {
		return $f;
	}

	$f = array(
		// Marca y contacto.
		'tagline'            => array( 'marca', __( 'Frase del pie', 'akzara' ), 'text', 'El lujo no hace ruido.' ),
		'address'            => array( 'marca', __( 'Dirección', 'akzara' ), 'textarea', 'Carrera 36 # 1 Sur - 75, Poblado Alejandría, Milla de Oro, El Poblado, Medellín' ),
		'maps_url'           => array( 'marca', __( 'Enlace de Google Maps', 'akzara' ), 'url', 'https://maps.app.goo.gl/' ),
		'hours'              => array( 'marca', __( 'Horario', 'akzara' ), 'textarea', "Lun–Vie|10:00–19:00\nSáb|10:00–18:00\nDom|10:00–16:00", __( 'Una línea por fila: «Días|Horas».', 'akzara' ) ),
		'whatsapp'           => array( 'marca', __( 'WhatsApp del concierge (solo números con indicativo)', 'akzara' ), 'text', '573052929212' ),
		'whatsapp_display'   => array( 'marca', __( 'WhatsApp (como se muestra)', 'akzara' ), 'text', '+57 305 292 9212' ),
		'whatsapp_message'   => array( 'marca', __( 'Mensaje de WhatsApp por defecto', 'akzara' ), 'text', 'Hola, quisiera información sobre Akzara.' ),
		'email'              => array( 'marca', __( 'Correo', 'akzara' ), 'text', 'info@akzaraspa.co' ),
		'instagram'          => array( 'marca', __( 'Instagram (URL)', 'akzara' ), 'url', 'https://instagram.com/' ),
		'arrival_note'       => array( 'marca', __( 'Nota de llegada', 'akzara' ), 'text', 'Le recomendamos llegar 15 minutos antes de su ritual.' ),
		'payment_note'       => array( 'marca', __( 'Medios de pago', 'akzara' ), 'text', 'Medios de pago: tarjetas, PSE, Nequi y transferencia Bancolombia.' ),
		'cancellation_note'  => array( 'marca', __( 'Nota de cancelación', 'akzara' ), 'textarea', 'Cancelación: lunes a viernes con 24 h hábiles; fines de semana y festivos con 48 h. Consulte las políticas de reserva.' ),
		'copyright'          => array( 'marca', __( 'Copyright', 'akzara' ), 'text', '© 2026 Grupo Akzara S.A.S. Todos los derechos reservados.' ),
		'slot_from'          => array( 'marca', __( 'Primera hora de inicio', 'akzara' ), 'text', '10:00' ),
		'slot_to'            => array( 'marca', __( 'Última hora de inicio', 'akzara' ), 'text', '17:30' ),
		'slot_step'          => array( 'marca', __( 'Intervalo entre horas (min)', 'akzara' ), 'number', 30 ),
		'max_people'         => array( 'marca', __( 'Máximo de personas en la barra de reserva', 'akzara' ), 'number', 8 ),

		// Colores.
		'c_bg'               => array( 'colores', __( 'Fondo principal (Gris Travertino)', 'akzara' ), 'color', '#EAE6DF' ),
		'c_ink'              => array( 'colores', __( 'Texto y fondo oscuro (Negro Humo)', 'akzara' ), 'color', '#2B2B2A' ),
		'c_bronze_text'      => array( 'colores', __( 'Bronce para texto', 'akzara' ), 'color', '#9A7A4F' ),
		'c_bronze'           => array( 'colores', __( 'Bronce del logo (Oro Envejecido)', 'akzara' ), 'color', '#C5A073' ),
		'radius'             => array( 'colores', __( 'Radio de esquinas (px)', 'akzara' ), 'number', 2 ),

		// Menú y botones.
		'nav_rituals'        => array( 'menu', __( 'Texto del menú «Rituales»', 'akzara' ), 'text', 'Rituales' ),
		'nav_experiences'    => array( 'menu', __( 'Texto del menú «Experiencias»', 'akzara' ), 'text', 'Experiencias' ),
		'nav_club'           => array( 'menu', __( 'Texto del menú «El Club»', 'akzara' ), 'text', 'El Club' ),
		'nav_gift'           => array( 'menu', __( 'Texto del enlace «Regalar»', 'akzara' ), 'text', 'Regalar' ),
		'nav_book'           => array( 'menu', __( 'Texto del botón «Reservar»', 'akzara' ), 'text', 'Reservar' ),
		'exp1_label'         => array( 'menu', __( 'Tarjeta 1 · título', 'akzara' ), 'text', 'Masaje en el jardín' ),
		'exp1_text'          => array( 'menu', __( 'Tarjeta 1 · texto corto', 'akzara' ), 'text', 'Al aire libre, entre bambú y agua.' ),
		'exp1_link'          => array( 'menu', __( 'Tarjeta 1 · enlace', 'akzara' ), 'text', '/ritual/natural-en-el-jardin/' ),
		'exp1_image'         => array( 'menu', __( 'Tarjeta 1 · imagen', 'akzara' ), 'image', 0 ),
		'exp2_label'         => array( 'menu', __( 'Tarjeta 2 · título', 'akzara' ), 'text', 'Membresías del Club' ),
		'exp2_text'          => array( 'menu', __( 'Tarjeta 2 · texto corto', 'akzara' ), 'text', 'El bienestar como hábito, no como excepción.' ),
		'exp2_link'          => array( 'menu', __( 'Tarjeta 2 · enlace', 'akzara' ), 'text', '/membresias/' ),
		'exp2_image'         => array( 'menu', __( 'Tarjeta 2 · imagen', 'akzara' ), 'image', 0 ),

		// Portada.
		'hero_video'         => array( 'portada', __( 'Video de fondo (MP4 o WebM, sin audio)', 'akzara' ), 'video', 0, __( 'Opcional. Si no hay video se usa la imagen.', 'akzara' ) ),
		'hero_poster'        => array( 'portada', __( 'Imagen de portada', 'akzara' ), 'image', 0 ),
		'hero_video_alt'     => array( 'portada', __( 'Descripción accesible del video', 'akzara' ), 'text', '' ),
		'hero_title'         => array( 'portada', __( 'Frase principal', 'akzara' ), 'text', 'Spa en Medellín: el único *jardín* de la Milla de Oro.' ),
		'hero_h1'            => array( 'portada', __( 'Título discreto para Google (H1)', 'akzara' ), 'text', 'Spa en Medellín — Akzara Wellness Club' ),
		'hero_quick'         => array( 'portada', __( 'Mostrar la barra de reserva rápida', 'akzara' ), 'checkbox', true ),

		// Inicio.
		'featured_title'     => array( 'inicio', __( 'Rituales destacados · título', 'akzara' ), 'text', 'Rituales *destacados*' ),
		'featured_limit'     => array( 'inicio', __( 'Rituales destacados · cuántos mostrar', 'akzara' ), 'number', 6 ),
		'featured_link'      => array( 'inicio', __( 'Rituales destacados · texto del enlace', 'akzara' ), 'text', 'Ver todos los rituales' ),
		'manifesto_eyebrow'  => array( 'inicio', __( 'Manifiesto · antetítulo', 'akzara' ), 'text', 'Akzara' ),
		'manifesto_phrase'   => array( 'inicio', __( 'Manifiesto · frase', 'akzara' ), 'text', 'El lujo no *hace ruido.*' ),
		'manifesto_text'     => array( 'inicio', __( 'Manifiesto · texto', 'akzara' ), 'textarea', 'El verdadero lujo no se anuncia, se respira. No deslumbra con excesos; reconforta con el silencio.' ),
		'manifesto_image'    => array( 'inicio', __( 'Manifiesto · imagen', 'akzara' ), 'image', 0 ),
		'occ_title'          => array( 'inicio', __( 'Ocasiones · título', 'akzara' ), 'text', 'Explorar por *ocasión*' ),
		'occ1_label'         => array( 'inicio', __( 'Ocasión 1 · texto', 'akzara' ), 'text', 'En pareja' ),
		'occ1_link'          => array( 'inicio', __( 'Ocasión 1 · enlace', 'akzara' ), 'text', '/rituales/?para=pareja' ),
		'occ1_image'         => array( 'inicio', __( 'Ocasión 1 · imagen', 'akzara' ), 'image', 0 ),
		'occ2_label'         => array( 'inicio', __( 'Ocasión 2 · texto', 'akzara' ), 'text', 'Para mí' ),
		'occ2_link'          => array( 'inicio', __( 'Ocasión 2 · enlace', 'akzara' ), 'text', '/rituales/?para=para-mi' ),
		'occ2_image'         => array( 'inicio', __( 'Ocasión 2 · imagen', 'akzara' ), 'image', 0 ),
		'occ3_label'         => array( 'inicio', __( 'Ocasión 3 · texto', 'akzara' ), 'text', 'Celebración' ),
		'occ3_link'          => array( 'inicio', __( 'Ocasión 3 · enlace', 'akzara' ), 'text', '/rituales/?para=celebracion' ),
		'occ3_image'         => array( 'inicio', __( 'Ocasión 3 · imagen', 'akzara' ), 'image', 0 ),
		'occ4_label'         => array( 'inicio', __( 'Ocasión 4 · texto', 'akzara' ), 'text', 'Regalo' ),
		'occ4_link'          => array( 'inicio', __( 'Ocasión 4 · enlace', 'akzara' ), 'text', '/gift-card/' ),
		'occ4_image'         => array( 'inicio', __( 'Ocasión 4 · imagen', 'akzara' ), 'image', 0 ),
		'sig_eyebrow'        => array( 'inicio', __( 'Ritual insignia · antetítulo', 'akzara' ), 'text', 'Insignia' ),
		'sig_text'           => array( 'inicio', __( 'Ritual insignia · texto (vacío = el resumen del ritual)', 'akzara' ), 'textarea', 'El ritual insignia del club: cinco horas y media para desconectar por completo.', __( 'El ritual que sale aquí es el que tenga marcada la casilla «Ritual insignia».', 'akzara' ) ),
		'sig_image'          => array( 'inicio', __( 'Ritual insignia · imagen de fondo (vacío = su primera foto)', 'akzara' ), 'image', 0 ),
		'journey_eyebrow'    => array( 'inicio', __( 'Recorrido · antetítulo', 'akzara' ), 'text', 'El recorrido' ),
		'journey_title'      => array( 'inicio', __( 'Recorrido · título', 'akzara' ), 'text', 'Así transcurre *su visita*' ),
		'j1_title'           => array( 'inicio', __( 'Recorrido 1 · título', 'akzara' ), 'text', 'Llegada' ),
		'j1_text'            => array( 'inicio', __( 'Recorrido 1 · texto', 'akzara' ), 'textarea', 'Té de bienvenida y tiempo en el jardín.' ),
		'j1_image'           => array( 'inicio', __( 'Recorrido 1 · foto', 'akzara' ), 'image', 0 ),
		'j2_title'           => array( 'inicio', __( 'Recorrido 2 · título', 'akzara' ), 'text', 'Agua' ),
		'j2_text'            => array( 'inicio', __( 'Recorrido 2 · texto', 'akzara' ), 'textarea', 'Jacuzzi al aire libre, sauna y baño turco con eucalipto.' ),
		'j2_image'           => array( 'inicio', __( 'Recorrido 2 · foto', 'akzara' ), 'image', 0 ),
		'j3_title'           => array( 'inicio', __( 'Recorrido 3 · título', 'akzara' ), 'text', 'Ritual' ),
		'j3_text'            => array( 'inicio', __( 'Recorrido 3 · texto', 'akzara' ), 'textarea', 'Exfoliación, envoltura, masaje y facial.' ),
		'j3_image'           => array( 'inicio', __( 'Recorrido 3 · foto', 'akzara' ), 'image', 0 ),
		'j4_title'           => array( 'inicio', __( 'Recorrido 4 · título', 'akzara' ), 'text', 'Pausa' ),
		'j4_text'            => array( 'inicio', __( 'Recorrido 4 · texto', 'akzara' ), 'textarea', 'Lounge frente al jardín, sin reloj.' ),
		'j4_image'           => array( 'inicio', __( 'Recorrido 4 · foto', 'akzara' ), 'image', 0 ),
		'plans_title'        => array( 'inicio', __( 'Membresías · título', 'akzara' ), 'text', 'El bienestar como *hábito*' ),
		'plans_subtitle'     => array( 'inicio', __( 'Membresías · subtítulo', 'akzara' ), 'text', 'Aqua, Plata y Dorada: tres formas de volver.' ),
		'quotes_title'       => array( 'inicio', __( 'Testimonios · título', 'akzara' ), 'text', 'Lo que dicen de *Akzara*', __( 'Los testimonios se cargan en «Testimonios». Si no hay ninguno publicado, la sección no se ve.', 'akzara' ) ),
		'faq_title'          => array( 'inicio', __( 'Preguntas frecuentes · título', 'akzara' ), 'text', 'Preguntas *frecuentes*' ),
		'faq_limit'          => array( 'inicio', __( 'Preguntas frecuentes · cuántas mostrar', 'akzara' ), 'number', 5 ),
		'gift_title'         => array( 'inicio', __( 'Regalar · título', 'akzara' ), 'text', 'Regale *calma*' ),
		'gift_button'        => array( 'inicio', __( 'Regalar · botón', 'akzara' ), 'text', 'Regalar' ),
		'gallery_title'      => array( 'inicio', __( 'Galería · título', 'akzara' ), 'text', 'Instalaciones' ),
		'gallery_ids'        => array( 'inicio', __( 'Galería · fotos (números de la Mediateca, separados por coma)', 'akzara' ), 'text', '', __( 'Abra una foto en Medios y copie el número que aparece en la barra de direcciones (post=123). Vacío = imágenes provisionales.', 'akzara' ) ),
		'visit_title'        => array( 'inicio', __( 'Visítenos · título', 'akzara' ), 'text', 'Visítenos' ),
		'closing_title'      => array( 'inicio', __( 'Cierre · frase', 'akzara' ), 'text', 'Bienvenido a la *calma.*' ),
		'closing_button'     => array( 'inicio', __( 'Cierre · botón', 'akzara' ), 'text', 'Reservar' ),
		'closing_link'       => array( 'inicio', __( 'Cierre · enlace', 'akzara' ), 'text', '/reservar/' ),

		// Instalaciones.
		'fac1_title'         => array( 'instalaciones', __( 'Sección 1 · título', 'akzara' ), 'text', 'Un jardín real en la *Milla de Oro*' ),
		'fac1_text'          => array( 'instalaciones', __( 'Sección 1 · texto', 'akzara' ), 'textarea', 'Mil metros cuadrados de bambú y follaje alrededor de una piscina exterior. Un respiro verde en pleno El Poblado.' ),
		'fac1_image'         => array( 'instalaciones', __( 'Sección 1 · imagen', 'akzara' ), 'image', 0 ),
		'fac2_title'         => array( 'instalaciones', __( 'Sección 2 · título', 'akzara' ), 'text', 'El *agua*, en todas sus formas' ),
		'fac2_text'          => array( 'instalaciones', __( 'Sección 2 · texto', 'akzara' ), 'textarea', 'Jacuzzis al aire libre, sauna y baño turco con eucalipto. Cada ritual incluye el tiempo para disfrutarlos sin prisa.' ),
		'fac2_image'         => array( 'instalaciones', __( 'Sección 2 · imagen', 'akzara' ), 'image', 0 ),
		'fac3_title'         => array( 'instalaciones', __( 'Sección 3 · título', 'akzara' ), 'text', 'Salas que *miran al jardín*' ),
		'fac3_text'          => array( 'instalaciones', __( 'Sección 3 · texto', 'akzara' ), 'textarea', 'Salas con cortinas que se abren hacia el jardín y un lounge para el antes y el después.' ),
		'fac3_image'         => array( 'instalaciones', __( 'Sección 3 · imagen', 'akzara' ), 'image', 0 ),
		'fac_gallery_title'  => array( 'instalaciones', __( 'Galería · título', 'akzara' ), 'text', 'Galería' ),
		'fac_closing_title'  => array( 'instalaciones', __( 'Cierre · frase', 'akzara' ), 'text', 'Solo hay que *llegar.*' ),

		// El Club.
		'club_phrase'        => array( 'club', __( 'Manifiesto · frase', 'akzara' ), 'text', 'El lujo no *hace ruido.*' ),
		'club_text'          => array( 'club', __( 'Manifiesto · texto', 'akzara' ), 'textarea', 'No está aquí para huir del mundo. Está aquí para recordar quién es cuando el mundo guarda silencio. Bienvenido a la calma. Bienvenido a Akzara.' ),
		'club_image'         => array( 'club', __( 'Manifiesto · imagen', 'akzara' ), 'image', 0 ),
		'pillars_title'      => array( 'club', __( 'Pilares · título', 'akzara' ), 'text', 'Nuestros *pilares*' ),
		'p1_title'           => array( 'club', __( 'Pilar 1 · título', 'akzara' ), 'text', 'Sutileza' ),
		'p1_text'            => array( 'club', __( 'Pilar 1 · texto', 'akzara' ), 'textarea', 'Menos es más. Espacios amplios, líneas limpias.' ),
		'p1_image'           => array( 'club', __( 'Pilar 1 · imagen', 'akzara' ), 'image', 0 ),
		'p2_title'           => array( 'club', __( 'Pilar 2 · título', 'akzara' ), 'text', 'Misticismo orgánico' ),
		'p2_text'            => array( 'club', __( 'Pilar 2 · texto', 'akzara' ), 'textarea', 'Conexión profunda con el agua, la luz sutil y las texturas de piedra.' ),
		'p2_image'           => array( 'club', __( 'Pilar 2 · imagen', 'akzara' ), 'image', 0 ),
		'p3_title'           => array( 'club', __( 'Pilar 3 · título', 'akzara' ), 'text', 'Exclusividad' ),
		'p3_text'            => array( 'club', __( 'Pilar 3 · texto', 'akzara' ), 'textarea', 'Un club privado donde la privacidad y la personalización son lo primero.' ),
		'p3_image'           => array( 'club', __( 'Pilar 3 · imagen', 'akzara' ), 'image', 0 ),
		'club_closing_title' => array( 'club', __( 'Cierre · frase', 'akzara' ), 'text', 'Solo hay que *llegar.*' ),

		// Membresías.
		'plans_lead'         => array( 'membresias', __( 'Frase principal', 'akzara' ), 'text', 'El bienestar como hábito, no como excepción.' ),
		'plans_soon'         => array( 'membresias', __( 'Nota bajo la frase', 'akzara' ), 'text', 'Disponible muy pronto: la compra en línea llegará en la siguiente fase.' ),

		// Reservas.
		'book_note'          => array( 'reservas', __( 'Texto sobre el formulario de reservas', 'akzara' ), 'textarea', 'Es una solicitud: nuestro concierge confirmará la disponibilidad y le enviará el enlace para el anticipo. Llegue 15 minutos antes.' ),

		// Gift card.
		'gift_notify'        => array( 'gift', __( 'Correo que recibe las solicitudes', 'akzara' ), 'text', '', __( 'Vacío = el correo de «Marca y contacto».', 'akzara' ) ),
		'gift_lead'          => array( 'gift', __( 'Frase bajo el título', 'akzara' ), 'text', 'Regale calma: personalícela y véala antes de enviarla.' ),

		// Aviso emergente.
		'promo_on'           => array( 'aviso', __( 'Mostrar el aviso', 'akzara' ), 'checkbox', true ),
		'promo_image'        => array( 'aviso', __( 'Imagen (proporción 2:1)', 'akzara' ), 'image', 0 ),
		'promo_overlay'      => array( 'aviso', __( 'Texto sobre la imagen (opcional)', 'akzara' ), 'text', '2×1 Ritual Energizante · Solo $515 mil · 3 horas' ),
		'promo_title'        => array( 'aviso', __( 'Título', 'akzara' ), 'text', '2 x 1 Ritual Energizante' ),
		'promo_text'         => array( 'aviso', __( 'Texto corto', 'akzara' ), 'textarea', '¡Disfruta en compañía a precio de una sola persona!' ),
		'promo_button'       => array( 'aviso', __( 'Texto del botón', 'akzara' ), 'text', 'Preguntar por WhatsApp →' ),
		'promo_link'         => array( 'aviso', __( 'Enlace (vacío = WhatsApp del concierge)', 'akzara' ), 'text', '' ),
		'promo_message'      => array( 'aviso', __( 'Mensaje de WhatsApp prellenado', 'akzara' ), 'text', 'Hola, quisiera información sobre la promoción 2 x 1 Ritual Energizante.' ),
		'promo_delay'        => array( 'aviso', __( 'Aparece a los (segundos)', 'akzara' ), 'number', 4 ),
		'promo_days'         => array( 'aviso', __( 'Vuelve a salir tras cerrarla (días, 0 = en cada visita)', 'akzara' ), 'number', 0 ),

		// Seguimiento.
		'meta_pixel'         => array( 'seguimiento', __( 'ID del Pixel de Meta', 'akzara' ), 'text', '', __( 'Solo números. Ej.: 123456789012345', 'akzara' ) ),
		'ga4'                => array( 'seguimiento', __( 'ID de Google Analytics 4', 'akzara' ), 'text', '', __( 'Ej.: G-XXXXXXXXXX', 'akzara' ) ),
		'gtm'                => array( 'seguimiento', __( 'ID de Google Tag Manager (opcional)', 'akzara' ), 'text', '', __( 'Ej.: GTM-XXXXXXX. Si usa Tag Manager no ponga también Analytics.', 'akzara' ) ),

		// Idioma.
		'lang_switch'        => array( 'idioma', __( 'Mostrar el selector ES / EN', 'akzara' ), 'checkbox', false, __( 'Actívelo solo cuando tenga las páginas en inglés (por ejemplo con Polylang). Los enlaces se editan con el filtro akzara_lang_url.', 'akzara' ) ),
	);

	return $f;
}
