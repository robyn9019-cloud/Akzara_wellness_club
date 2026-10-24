<?php
/**
 * Template Name: El Club
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
	<?php
	get_template_part(
		'template-parts/manifesto',
		null,
		array(
			'eyebrow' => 'Akzara',
			'phrase'  => akzara_opt( 'club_phrase' ),
			'text'    => akzara_opt( 'club_text' ),
			'image'   => akzara_opt_img( 'club_image' ),
		)
	);
	?>
	<section class="section section--soft">
		<div class="container">
			<h2 class="h-section" style="margin-bottom:40px"><?php echo akzara_italic( akzara_opt( 'pillars_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<div class="pillars">
				<?php foreach ( array( 1, 2, 3 ) as $akzara_n ) : ?>
					<div>
						<div class="num"><?php echo esc_html( str_pad( (string) $akzara_n, 2, '0', STR_PAD_LEFT ) ); ?></div>
						<?php echo akzara_media( akzara_opt_img( 'p' . $akzara_n . '_image' ), array( 1 => 'jardin', 2 => 'arena', 3 => 'humo' )[ $akzara_n ], '', array( 'size' => 'akzara-card', 'sizes' => '(max-width: 860px) 100vw, 33vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<h3><?php echo esc_html( akzara_opt( 'p' . $akzara_n . '_title' ) ); ?></h3>
						<p><?php echo esc_html( akzara_opt( 'p' . $akzara_n . '_text' ) ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	akzara_closing( akzara_opt( 'club_closing_title' ), 'Reservar', '/reservar/' );
endwhile;

get_footer();
