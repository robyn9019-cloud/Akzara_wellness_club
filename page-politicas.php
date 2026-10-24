<?php
/**
 * Template Name: Política
 *
 * Texto legal con accesos a las demás políticas (páginas hijas de «Políticas»).
 *
 * @package akzara
 */

get_header();

while ( have_posts() ) :
	the_post();
	$akzara_parent = wp_get_post_parent_id( get_the_ID() );
	$akzara_root   = $akzara_parent ? $akzara_parent : get_the_ID();
	$akzara_sibs   = get_pages(
		array(
			'child_of'    => $akzara_root,
			'sort_column' => 'menu_order',
		)
	);
	?>
	<div class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
		<div class="container" style="max-width:820px">
			<?php akzara_page_head( array( array( 'Inicio', home_url( '/' ) ), array( get_the_title(), '' ) ), get_the_title() ); ?>
			<?php if ( $akzara_sibs ) : ?>
				<div class="filters__row" style="margin-bottom:32px">
					<?php foreach ( $akzara_sibs as $akzara_p ) : ?>
						<a class="chip" href="<?php echo esc_url( get_permalink( $akzara_p ) ); ?>"<?php echo get_the_ID() === $akzara_p->ID ? ' aria-current="page"' : ''; ?>><?php echo esc_html( get_the_title( $akzara_p ) ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="prose"><?php the_content(); ?></div>
		</div>
	</div>
	<?php
endwhile;

get_footer();
