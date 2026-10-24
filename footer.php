<?php
/**
 * Pie de página, botón de WhatsApp, aviso promocional y adornos de movimiento.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</div></main>

<footer class="ftr">
	<div class="container">
		<div class="ftr__grid">
			<div class="ftr__brand">
				<img src="<?php echo esc_url( akzara_img_uri( 'logo-white.webp' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="1808" height="491" loading="lazy">
				<p><?php echo esc_html( akzara_opt( 'tagline' ) ); ?></p>
			</div>
			<div>
				<h4>Dirección</h4>
				<p><?php echo esc_html( akzara_opt( 'address' ) ); ?></p>
				<a href="<?php echo esc_url( akzara_opt( 'maps_url' ) ); ?>" target="_blank" rel="noopener" style="margin-top:10px">Google Maps</a>
			</div>
			<div>
				<h4>Horario</h4>
				<div class="hours">
					<?php foreach ( akzara_hours() as $akzara_h ) : ?>
						<span><?php echo esc_html( $akzara_h[0] ); ?></span><span><?php echo esc_html( $akzara_h[1] ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
			<div>
				<h4>Contacto</h4>
				<a href="<?php echo esc_url( akzara_wa_link( '', akzara_opt( 'whatsapp_message' ) ) ); ?>" target="_blank" rel="noopener">WhatsApp <?php echo esc_html( akzara_opt( 'whatsapp_display' ) ); ?></a>
				<a href="mailto:<?php echo esc_attr( antispambot( akzara_opt( 'email' ) ) ); ?>"><?php echo esc_html( akzara_opt( 'email' ) ); ?></a>
				<a href="<?php echo esc_url( akzara_opt( 'instagram' ) ); ?>" target="_blank" rel="noopener" style="margin-top:10px">Instagram</a>
			</div>
		</div>
		<div class="ftr__bottom">
			<span><?php echo esc_html( akzara_opt( 'payment_note' ) ); ?></span>
			<nav aria-label="Legal">
				<?php foreach ( akzara_legal_links() as $akzara_l ) : ?>
					<a href="<?php echo esc_url( $akzara_l[1] ); ?>"><?php echo esc_html( $akzara_l[0] ); ?></a>
				<?php endforeach; ?>
			</nav>
			<span><?php echo esc_html( akzara_opt( 'copyright' ) ); ?></span>
		</div>
	</div>
</footer>

<a class="wa" href="<?php echo esc_url( akzara_wa_link( '', akzara_opt( 'whatsapp_message' ) ) ); ?>" target="_blank" rel="noopener" aria-label="Escribir por WhatsApp"><?php echo akzara_whatsapp_icon(); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>

<?php if ( akzara_opt( 'promo_on' ) && akzara_opt( 'promo_title' ) && ! akzara_is_template( array( 'page-reservas.php', 'page-gift-card.php' ) ) ) : ?>
	<?php
	$akzara_promo_link = akzara_opt( 'promo_link' ) ? akzara_link( akzara_opt( 'promo_link' ) ) : akzara_wa_link( '', akzara_opt( 'promo_message' ) );
	?>
	<aside class="promo<?php echo is_front_page() ? '' : ' promo--left'; ?>" aria-label="<?php echo esc_attr( akzara_opt( 'promo_title' ) ); ?>" hidden>
		<button class="promo__x" type="button" aria-label="Cerrar">×</button>
		<div style="position:relative">
			<?php echo akzara_media( akzara_opt_img( 'promo_image' ), 'arena', '', array( 'sizes' => '360px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( akzara_opt( 'promo_overlay' ) ) : ?>
				<span class="ov"><?php echo esc_html( akzara_opt( 'promo_overlay' ) ); ?></span>
			<?php endif; ?>
		</div>
		<div class="promo__body">
			<b><?php echo esc_html( akzara_opt( 'promo_title' ) ); ?></b>
			<?php if ( akzara_opt( 'promo_text' ) ) : ?>
				<p><?php echo esc_html( akzara_opt( 'promo_text' ) ); ?></p>
			<?php endif; ?>
			<a class="btn" href="<?php echo esc_url( $akzara_promo_link ); ?>"<?php echo 0 === strpos( $akzara_promo_link, 'http' ) && false === strpos( $akzara_promo_link, home_url() ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( akzara_opt( 'promo_button' ) ); ?></a>
		</div>
	</aside>
<?php endif; ?>

<div class="cursor" aria-hidden="true"></div>
<div class="navprog" data-state="idle" aria-hidden="true"></div>

<?php wp_footer(); ?>
</body>
</html>
