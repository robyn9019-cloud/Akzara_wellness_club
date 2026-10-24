<?php
/**
 * Barra de reserva rápida de la portada → /reservar/?fecha&hora&personas
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$akzara_slots = array();
$akzara_step  = max( 5, (int) akzara_opt( 'slot_step' ) );
$akzara_from  = explode( ':', (string) akzara_opt( 'slot_from' ) );
$akzara_to    = explode( ':', (string) akzara_opt( 'slot_to' ) );
$akzara_t     = ( (int) $akzara_from[0] * 60 ) + ( isset( $akzara_from[1] ) ? (int) $akzara_from[1] : 0 );
$akzara_end   = ( (int) $akzara_to[0] * 60 ) + ( isset( $akzara_to[1] ) ? (int) $akzara_to[1] : 0 );
for ( ; $akzara_t <= $akzara_end; $akzara_t += $akzara_step ) {
	$akzara_slots[] = sprintf( '%02d:%02d', intdiv( $akzara_t, 60 ), $akzara_t % 60 );
}
$akzara_max = max( 1, (int) akzara_opt( 'max_people' ) );
?>
<form class="qb" action="<?php echo esc_url( home_url( '/reservar/' ) ); ?>" method="get" aria-label="Reservar" data-quick>
	<div class="qb__f qb__f--date">
		<span class="qb__l" id="qb-date-l">Fecha</span>
		<div class="dp dp--dark" data-datepicker data-name="fecha" data-placement="top" data-placeholder="Elegir"></div>
	</div>
	<label class="qb__f">
		<span class="qb__l">Hora</span>
		<select name="hora">
			<?php foreach ( $akzara_slots as $akzara_s ) : ?>
				<option<?php selected( $akzara_s, '16:00' ); ?>><?php echo esc_html( $akzara_s ); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
	<label class="qb__f">
		<span class="qb__l">Personas</span>
		<select name="personas">
			<?php for ( $akzara_n = 1; $akzara_n <= $akzara_max; $akzara_n++ ) : ?>
				<option value="<?php echo (int) $akzara_n; ?>"<?php selected( $akzara_n, 2 ); ?>><?php echo (int) $akzara_n; ?> <?php echo 1 === $akzara_n ? 'persona' : 'personas'; ?></option>
			<?php endfor; ?>
		</select>
	</label>
	<button class="btn btn--light" type="submit">Reservar</button>
</form>
