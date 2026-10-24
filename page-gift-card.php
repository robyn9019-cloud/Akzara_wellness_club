<?php
/**
 * Template Name: Gift card
 *
 * El formulario de tres pasos lo dibuja assets/js/main.js; sin JavaScript queda el enlace a WhatsApp.
 *
 * @package akzara
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
		<div class="container">
			<?php akzara_page_head( array( array( 'Inicio', home_url( '/' ) ), array( 'Gift card', '' ) ), get_the_title(), 'Regalar', akzara_opt( 'gift_lead' ) ); ?>
			<div id="gift-app" data-gift>
				<noscript>
					<p class="muted">Para personalizar su gift card necesita activar JavaScript. También puede escribirnos por <a href="<?php echo esc_url( akzara_wa_link( '', 'Hola, quisiera una gift card.' ) ); ?>" style="text-decoration:underline">WhatsApp</a>.</p>
				</noscript>
			</div>
		</div>
	</div>
	<?php
endwhile;

get_footer();
