<?php
/**
 * Template Name: Reservas (Amelia)
 *
 * Contenedor con estilo de la marca: imprime el contenido de la página (ahí va el shortcode de Amelia).
 *
 * @package akzara
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
		<div class="container">
			<?php akzara_page_head( array( array( 'Inicio', home_url( '/' ) ), array( 'Reservar', '' ) ), get_the_title() ); ?>
			<?php if ( akzara_opt( 'book_note' ) ) : ?>
				<p class="booking-note"><?php echo esc_html( akzara_opt( 'book_note' ) ); ?></p>
			<?php endif; ?>
			<div class="booking-embed">
				<?php
				$akzara_content = get_the_content();
				// Si llega ?ritual={slug} y el ritual tiene su servicio de Amelia, se abre con ese servicio elegido.
				$akzara_servicio = 0;
				$akzara_slug     = isset( $_GET['ritual'] ) ? sanitize_title( wp_unslash( $_GET['ritual'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( '' !== $akzara_slug ) {
					$akzara_ritual = get_page_by_path( $akzara_slug, OBJECT, 'akzara_ritual' );
					if ( $akzara_ritual && 'publish' === $akzara_ritual->post_status ) {
						$akzara_servicio = (int) get_post_meta( $akzara_ritual->ID, '_ak_amelia', true );
					}
				}
				if ( '' === trim( wp_strip_all_tags( $akzara_content ) ) ) {
					echo '<p class="muted" style="text-align:center">' . esc_html__( 'Aquí aparecerá el motor de reservas. Pegue el shortcode de Amelia en el contenido de esta página.', 'akzara' ) . '</p>';
				} elseif ( $akzara_servicio > 0 ) {
					$akzara_tag = false !== strpos( $akzara_content, '[ameliabooking' ) ? 'ameliabooking' : 'ameliastepbooking';
					echo do_shortcode( '[' . $akzara_tag . ' service=' . $akzara_servicio . ']' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					the_content();
				}
				?>
			</div>
			<p class="booking-note" style="margin:22px auto 0">
				¿Prefiere escribirnos? <a href="<?php echo esc_url( akzara_wa_link( '', akzara_opt( 'whatsapp_message' ) ) ); ?>" target="_blank" rel="noopener" style="text-decoration:underline;text-underline-offset:3px">WhatsApp concierge</a>.
			</p>
		</div>
	</div>
	<?php
endwhile;

get_footer();
