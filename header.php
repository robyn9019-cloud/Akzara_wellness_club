<?php
/**
 * Cabecera: logo, menús desplegables, idioma, botón Reservar y menú móvil.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$akzara_over = is_front_page();
$akzara_rows = akzara_categories();
$akzara_logo = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' ) : akzara_img_uri( 'logo.webp' );
$akzara_cards = array();
foreach ( array( 1, 2 ) as $akzara_n ) {
	$akzara_cards[] = array(
		'label' => akzara_opt( 'exp' . $akzara_n . '_label' ),
		'text'  => akzara_opt( 'exp' . $akzara_n . '_text' ),
		'link'  => akzara_link( akzara_opt( 'exp' . $akzara_n . '_link' ) ),
		'image' => akzara_opt_img( 'exp' . $akzara_n . '_image' ),
		'tone'  => 1 === $akzara_n ? 'jardin' : 'arena',
	);
}
$akzara_club = akzara_club_links();
$akzara_book = home_url( '/reservar/' );
$akzara_lang = (bool) akzara_opt( 'lang_switch' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a href="#main" class="sr-only"><?php esc_html_e( 'Saltar al contenido', 'akzara' ); ?></a>

<header class="hdr<?php echo $akzara_over ? ' hdr--over' : ''; ?><?php echo $akzara_over ? '' : ' hdr--solid'; ?>" data-over="<?php echo $akzara_over ? '1' : '0'; ?>">
	<div class="hdr__in">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hdr__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — inicio">
			<img class="full" src="<?php echo esc_url( $akzara_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="1808" height="491">
			<img class="iso" src="<?php echo esc_url( akzara_img_uri( 'isotipo-white.webp' ) ); ?>" alt="Akzara" width="531" height="491">
		</a>

		<nav class="hdr__nav" aria-label="Principal">
			<div data-menu="rituals">
				<button class="hdr__trigger" type="button" aria-expanded="false" aria-controls="dd-rituals"><?php echo esc_html( akzara_opt( 'nav_rituals' ) ); ?> <?php echo akzara_chevron(); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<div id="dd-rituals" class="dd dd--rows" data-open="false">
					<ul>
						<?php foreach ( $akzara_rows as $akzara_i => $akzara_row ) : ?>
							<li data-row="<?php echo (int) $akzara_i; ?>">
								<a href="<?php echo esc_url( $akzara_row['url'] ); ?>">
									<b><?php echo esc_html( $akzara_row['name'] ); ?></b>
									<small><?php echo esc_html( $akzara_row['menu'] ); ?></small>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div class="dd__img">
						<?php foreach ( $akzara_rows as $akzara_i => $akzara_row ) : ?>
							<?php
							$akzara_m = akzara_media( $akzara_row['image'], 'arena', '', array( 'size' => 'akzara-card', 'sizes' => '320px' ) );
							echo $akzara_i ? str_replace( 'class="media', 'hidden class="media', $akzara_m ) : $akzara_m; // phpcs:ignore WordPress.Security.EscapeOutput
							?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div data-menu="exp">
				<button class="hdr__trigger" type="button" aria-expanded="false" aria-controls="dd-exp"><?php echo esc_html( akzara_opt( 'nav_experiences' ) ); ?> <?php echo akzara_chevron(); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<div id="dd-exp" class="dd dd--cards" data-open="false">
					<?php foreach ( $akzara_cards as $akzara_c ) : ?>
						<a href="<?php echo esc_url( $akzara_c['link'] ); ?>">
							<?php echo akzara_media( $akzara_c['image'], $akzara_c['tone'], '', array( 'size' => 'akzara-card', 'sizes' => '320px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="cap" style="position:absolute">
								<b><?php echo esc_html( $akzara_c['label'] ); ?></b>
								<small><?php echo esc_html( $akzara_c['text'] ); ?></small>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<div data-menu="club">
				<button class="hdr__trigger" type="button" aria-expanded="false" aria-controls="dd-club"><?php echo esc_html( akzara_opt( 'nav_club' ) ); ?> <?php echo akzara_chevron(); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<div id="dd-club" class="dd dd--links" data-open="false">
					<?php foreach ( $akzara_club as $akzara_l ) : ?>
						<a href="<?php echo esc_url( $akzara_l[1] ); ?>"><?php echo esc_html( $akzara_l[0] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>

			<a href="<?php echo esc_url( home_url( '/gift-card/' ) ); ?>" class="hdr__trigger"><?php echo esc_html( akzara_opt( 'nav_gift' ) ); ?></a>
		</nav>

		<div class="hdr__right">
			<?php if ( $akzara_lang ) : ?>
				<div class="lang" role="group" aria-label="Idioma / Language">
					<a href="<?php echo esc_url( apply_filters( 'akzara_lang_url', home_url( '/' ), 'es' ) ); ?>" aria-current="true" hreflang="es">ES</a>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( apply_filters( 'akzara_lang_url', home_url( '/en/' ), 'en' ) ); ?>" aria-current="false" hreflang="en">EN</a>
				</div>
			<?php endif; ?>
			<a class="btn" href="<?php echo esc_url( $akzara_book ); ?>"><?php echo esc_html( akzara_opt( 'nav_book' ) ); ?></a>
			<button class="burger" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="mnav">
				<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M2 6h16M2 14h16" stroke="currentColor" stroke-width="1.3"/></svg>
			</button>
		</div>
	</div>
</header>

<div class="mnav" id="mnav" data-open="false" role="dialog" aria-modal="true" aria-label="Menú">
	<div class="mnav__top">
		<img src="<?php echo esc_url( $akzara_logo ); ?>" alt="Akzara" width="103" height="28" style="height:28px;width:auto">
		<button class="burger" type="button" style="display:block" aria-label="Cerrar menú" data-close>
			<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 4l12 12M16 4 4 16" stroke="currentColor" stroke-width="1.3"/></svg>
		</button>
	</div>
	<h3 class="eyebrow"><?php echo esc_html( akzara_opt( 'nav_rituals' ) ); ?></h3>
	<?php foreach ( $akzara_rows as $akzara_row ) : ?>
		<a class="big" href="<?php echo esc_url( $akzara_row['url'] ); ?>"><?php echo esc_html( $akzara_row['name'] ); ?></a>
	<?php endforeach; ?>
	<h3 class="eyebrow"><?php echo esc_html( akzara_opt( 'nav_experiences' ) ); ?></h3>
	<?php foreach ( $akzara_cards as $akzara_c ) : ?>
		<a class="big" href="<?php echo esc_url( $akzara_c['link'] ); ?>"><?php echo esc_html( $akzara_c['label'] ); ?></a>
	<?php endforeach; ?>
	<h3 class="eyebrow"><?php echo esc_html( akzara_opt( 'nav_club' ) ); ?></h3>
	<?php foreach ( $akzara_club as $akzara_l ) : ?>
		<a class="small" href="<?php echo esc_url( $akzara_l[1] ); ?>"><?php echo esc_html( $akzara_l[0] ); ?></a>
	<?php endforeach; ?>
	<a class="small" href="<?php echo esc_url( home_url( '/gift-card/' ) ); ?>"><?php echo esc_html( akzara_opt( 'nav_gift' ) ); ?></a>
	<div style="display:flex;gap:10px;margin-top:28px;align-items:center">
		<a class="btn" href="<?php echo esc_url( $akzara_book ); ?>"><?php echo esc_html( akzara_opt( 'nav_book' ) ); ?></a>
	</div>
</div>

<main id="main"><div class="page-enter">
