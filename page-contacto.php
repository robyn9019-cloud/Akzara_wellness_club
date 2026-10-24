<?php
/**
 * Template Name: Contacto
 *
 * @package akzara
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
		<div class="container">
			<?php akzara_page_head( array( array( 'Inicio', home_url( '/' ) ), array( get_the_title(), '' ) ), get_the_title() ); ?>
			<?php akzara_visit_info( true ); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
