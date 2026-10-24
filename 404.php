<?php
/**
 * Página no encontrada.
 *
 * @package akzara
 */

get_header();
?>
<section class="nf">
	<div>
		<span class="nf__n" aria-hidden="true">404</span>
		<h1>Esta página guarda <em>silencio.</em></h1>
		<p>Es posible que el enlace haya cambiado. Estos caminos le llevan de vuelta a la calma.</p>
		<div class="nf__acts">
			<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Volver al inicio</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'akzara_ritual' ) ); ?>">Ver los rituales</a>
		</div>
	</div>
</section>
<?php
get_footer();
