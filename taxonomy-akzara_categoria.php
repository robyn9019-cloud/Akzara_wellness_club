<?php
/**
 * Rituales de una categoría (/rituales/{categoria}/).
 *
 * @package akzara
 */

$akzara_term = get_queried_object();
$akzara_eyebrow = (string) get_term_meta( $akzara_term->term_id, '_ak_eyebrow', true );
$akzara_intro   = (string) get_term_meta( $akzara_term->term_id, '_ak_intro', true );

get_header();
?>
<div class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
	<div class="container">
		<?php
		akzara_page_head(
			array( array( 'Inicio', home_url( '/' ) ), array( 'Rituales', get_post_type_archive_link( 'akzara_ritual' ) ), array( $akzara_term->name, '' ) ),
			$akzara_term->name,
			$akzara_eyebrow ? $akzara_eyebrow : 'Rituales',
			$akzara_intro
		);
		get_template_part( 'template-parts/rituals-browser', null, array( 'active' => $akzara_term->slug ) );
		?>
	</div>
</div>
<?php
get_footer();
