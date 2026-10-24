<?php
/**
 * Listado de rituales con filtros (categoría = enlaces; «para quién» y duración = botones sin recargar).
 * Args: active (slug de la categoría o '').
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$akzara_a      = wp_parse_args( $args, array( 'active' => '' ) );
$akzara_active = $akzara_a['active'];
$akzara_list   = akzara_rituals(
	$akzara_active ? array(
		'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'taxonomy' => 'akzara_categoria',
				'field'    => 'slug',
				'terms'    => $akzara_active,
			),
		),
	) : array()
);
$akzara_occ = array(
	'pareja'      => 'En pareja',
	'para-mi'     => 'Para mí',
	'celebracion' => 'Celebración',
	'regalo'      => 'Regalo',
);
?>
<div class="filters" data-browser>
	<div class="filters__row" role="navigation" aria-label="Categorías">
		<a class="chip" href="<?php echo esc_url( get_post_type_archive_link( 'akzara_ritual' ) ); ?>"<?php echo $akzara_active ? '' : ' aria-current="page"'; ?>>Todos</a>
		<?php foreach ( akzara_categories() as $akzara_c ) : ?>
			<a class="chip" href="<?php echo esc_url( $akzara_c['url'] ); ?>"<?php echo $akzara_active === $akzara_c['slug'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $akzara_c['name'] ); ?></a>
		<?php endforeach; ?>
	</div>
	<div class="filters__row" role="group" aria-label="Para quién" data-filter="occ">
		<span class="eyebrow">Para quién</span>
		<button class="chip" type="button" aria-pressed="true" data-value="">Todos</button>
		<?php foreach ( $akzara_occ as $akzara_slug => $akzara_label ) : ?>
			<button class="chip" type="button" aria-pressed="false" data-value="<?php echo esc_attr( $akzara_slug ); ?>"><?php echo esc_html( $akzara_label ); ?></button>
		<?php endforeach; ?>
	</div>
	<div class="filters__row" role="group" aria-label="Duración" data-filter="dur">
		<span class="eyebrow">Duración</span>
		<button class="chip" type="button" aria-pressed="true" data-value="0">Todos</button>
		<?php foreach ( array( 1, 2, 3, 4 ) as $akzara_h ) : ?>
			<button class="chip" type="button" aria-pressed="false" data-value="<?php echo (int) $akzara_h; ?>">Hasta <?php echo (int) $akzara_h; ?> h</button>
		<?php endforeach; ?>
	</div>
</div>
<?php if ( $akzara_list ) : ?>
	<div class="grid grid-4" data-grid>
		<?php foreach ( $akzara_list as $akzara_r ) : ?>
			<?php akzara_ritual_card( $akzara_r ); ?>
		<?php endforeach; ?>
	</div>
	<p class="muted" data-empty hidden>No hay rituales con estos filtros.</p>
<?php else : ?>
	<p class="muted">No hay rituales con estos filtros.</p>
<?php endif; ?>
