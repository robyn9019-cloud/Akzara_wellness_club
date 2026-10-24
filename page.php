<?php
/**
 * Página genérica: cabecera interior y contenido del editor.
 *
 * @package akzara
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
		<div class="container" style="max-width:820px">
			<?php akzara_page_head( array( array( 'Inicio', home_url( '/' ) ), array( get_the_title(), '' ) ), get_the_title(), '', has_excerpt() ? get_the_excerpt() : '' ); ?>
			<div class="prose"><?php the_content(); ?></div>
		</div>
	</div>
	<?php
endwhile;

get_footer();
