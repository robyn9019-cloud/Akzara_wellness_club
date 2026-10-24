<?php
/**
 * Template Name: Membresías
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
			<h2 class="h-section" style="margin:40px 0 18px"><?php echo esc_html( akzara_opt( 'plans_lead' ) ); ?></h2>
			<?php if ( akzara_opt( 'plans_soon' ) ) : ?>
				<p class="muted" style="font-size:13px;margin-bottom:40px"><?php echo esc_html( akzara_opt( 'plans_soon' ) ); ?></p>
			<?php endif; ?>
			<?php akzara_plans(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
