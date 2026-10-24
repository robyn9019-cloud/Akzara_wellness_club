<?php
/**
 * Entrada del Diario.
 *
 * @package akzara
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
		<div class="container" style="max-width:820px">
			<?php akzara_crumbs( array( array( 'Inicio', home_url( '/' ) ), array( 'Diario', get_permalink( (int) get_option( 'page_for_posts' ) ) ), array( get_the_title(), '' ) ) ); ?>
			<span class="eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
			<h1 class="h-section" style="margin:12px 0 18px"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="lead" style="max-width:none;font-size:15px"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<?php echo akzara_media( (int) get_post_thumbnail_id(), 'arena', 'r-16x9', array( 'priority' => true, 'sizes' => '(max-width: 860px) 100vw, 820px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="prose" style="margin-top:32px"><?php the_content(); ?></div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
