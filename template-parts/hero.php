<?php
/**
 * Portada: imagen o video de fondo, título palabra por palabra y barra de reserva rápida.
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$akzara_poster = akzara_opt_img( 'hero_poster' );
$akzara_video  = akzara_opt_img( 'hero_video' ) ? wp_get_attachment_url( akzara_opt_img( 'hero_video' ) ) : '';
$akzara_ptitle = akzara_plain( akzara_opt( 'hero_title' ) );
?>
<section class="hero" aria-label="<?php echo esc_attr( $akzara_ptitle ); ?>">
	<?php if ( $akzara_video ) : ?>
		<div class="media">
			<?php echo akzara_img( $akzara_poster, 'akzara-wide', array( 'priority' => true, 'sizes' => '100vw' ) ) ?: akzara_placeholder( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<video class="hero__video" autoplay muted loop playsinline preload="metadata"<?php echo $akzara_poster ? ' poster="' . esc_url( wp_get_attachment_image_url( $akzara_poster, 'akzara-wide' ) ) . '"' : ''; ?> aria-label="<?php echo esc_attr( akzara_opt( 'hero_video_alt' ) ); ?>">
				<source src="<?php echo esc_url( $akzara_video ); ?>">
			</video>
		</div>
	<?php else : ?>
		<?php echo akzara_media( $akzara_poster, 'dark', '', array( 'priority' => true, 'sizes' => '100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php endif; ?>
	<div class="hero__shade"></div>
	<?php if ( akzara_opt( 'hero_h1' ) ) : ?>
		<h1 class="sr-only"><?php echo esc_html( akzara_opt( 'hero_h1' ) ); ?></h1>
	<?php endif; ?>
	<a href="#contenido" class="hero__cue"><span aria-hidden="true">(</span><em>descubra</em><span aria-hidden="true">)</span></a>
	<div class="hero__bottom">
		<p class="hero__title"><?php echo akzara_split_italic( akzara_opt( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<?php if ( akzara_opt( 'hero_quick' ) ) : ?>
			<?php get_template_part( 'template-parts/quick-booking' ); ?>
		<?php endif; ?>
	</div>
</section>
