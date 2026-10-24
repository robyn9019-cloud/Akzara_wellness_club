<?php
/**
 * Template Name: Instalaciones
 *
 * @package akzara
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="page-top">
		<div class="container">
			<?php akzara_page_head( array( array( 'Inicio', home_url( '/' ) ), array( get_the_title(), '' ) ), get_the_title(), '', has_excerpt() ? get_the_excerpt() : '' ); ?>
		</div>
	</div>
	<?php foreach ( array( 1, 2, 3 ) as $akzara_n ) : ?>
		<section class="section feature">
			<div class="container split<?php echo 2 === $akzara_n ? ' split--img-left' : ''; ?>">
				<div class="reveal">
					<h2 class="serif"><?php echo akzara_italic( akzara_opt( 'fac' . $akzara_n . '_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
					<p><?php echo esc_html( akzara_opt( 'fac' . $akzara_n . '_text' ) ); ?></p>
				</div>
				<div class="split__img">
					<?php echo akzara_media( akzara_opt_img( 'fac' . $akzara_n . '_image' ), 'jardin', 'r-4x5', array( 'size' => 'akzara-card', 'sizes' => '(max-width: 860px) 100vw, 45vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</section>
	<?php endforeach; ?>
	<?php
	akzara_hscroll( akzara_opt( 'fac_gallery_title' ), array_filter( array_map( 'absint', explode( ',', (string) akzara_opt( 'gallery_ids' ) ) ) ) );
	akzara_closing( akzara_opt( 'fac_closing_title' ), 'Reservar', '/reservar/' );
endwhile;

get_footer();
