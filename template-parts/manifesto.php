<?php
/**
 * Manifiesto: antetítulo, frase grande, texto e imagen.
 * Args: eyebrow, phrase, text, image (ID).
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$akzara_a = wp_parse_args( $args, array( 'eyebrow' => '', 'phrase' => '', 'text' => '', 'image' => 0 ) );
?>
<section class="section manifesto">
	<div class="container split">
		<div class="reveal">
			<span class="eyebrow"><?php echo esc_html( $akzara_a['eyebrow'] ); ?></span>
			<h2 class="h-display"><?php echo akzara_italic( $akzara_a['phrase'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<p class="lead"><?php echo esc_html( $akzara_a['text'] ); ?></p>
		</div>
		<div class="split__img">
			<?php echo akzara_media( $akzara_a['image'], 'humo', 'r-4x5', array( 'size' => 'akzara-card', 'sizes' => '(max-width: 860px) 100vw, 45vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</section>
