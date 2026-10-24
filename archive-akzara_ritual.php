<?php
/**
 * Todos los rituales (/rituales/).
 *
 * @package akzara
 */

get_header();
?>
<div class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
	<div class="container">
		<?php
		akzara_page_head(
			array( array( 'Inicio', home_url( '/' ) ), array( 'Rituales', '' ) ),
			'Rituales',
			'',
			'Todos los rituales, para cada forma de llegar.'
		);
		get_template_part( 'template-parts/rituals-browser', null, array( 'active' => '' ) );
		?>
	</div>
</div>
<?php
get_footer();
