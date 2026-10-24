<?php
/**
 * Ficha de un ritual: galería, datos, tarjeta de reserva, línea de tiempo, incluye y relacionados.
 *
 * @package akzara
 */

get_header();

while ( have_posts() ) :
	the_post();
	$akzara_s    = akzara_ritual( get_the_ID() );
	$akzara_imgs = $akzara_s['gallery'] ? $akzara_s['gallery'] : ( $akzara_s['thumb'] ? array( $akzara_s['thumb'] ) : array() );
	$akzara_book = add_query_arg( 'ritual', $akzara_s['slug'], home_url( '/reservar/' ) );
	$akzara_gift = add_query_arg( 'ritual', $akzara_s['slug'], home_url( '/gift-card/' ) );
	?>
	<div class="page-top">
		<div class="container">
			<?php
			akzara_crumbs(
				array(
					array( 'Inicio', home_url( '/' ) ),
					array( 'Rituales', get_post_type_archive_link( 'akzara_ritual' ) ),
					array( $akzara_s['cat_name'], $akzara_s['cat_url'] ),
					array( $akzara_s['name'], '' ),
				)
			);
			?>
			<span class="eyebrow eyebrow--bronze" style="display:block;margin-bottom:14px"><?php echo esc_html( $akzara_s['cat_name'] ); ?></span>
			<h1 class="h-caps" style="padding-bottom:30px;border-bottom:1px solid var(--line);margin-bottom:40px"><?php echo esc_html( $akzara_s['name'] ); ?></h1>
			<div class="ritual">
				<div class="ritual__main">
					<div class="gal" role="group" aria-roledescription="carrusel" aria-label="Fotos del ritual" data-gallery>
						<div class="gal__main">
							<?php if ( $akzara_imgs ) : ?>
								<?php foreach ( $akzara_imgs as $akzara_k => $akzara_id ) : ?>
									<div class="gal__slide"<?php echo $akzara_k ? ' hidden' : ''; ?>>
										<?php echo akzara_media( $akzara_id, $akzara_s['tone'], 'r-4x3', array( 'priority' => 0 === $akzara_k, 'sizes' => '(min-width: 960px) 56vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									</div>
								<?php endforeach; ?>
							<?php else : ?>
								<div class="gal__slide"><?php echo akzara_media( 0, $akzara_s['tone'], 'r-4x3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
							<?php endif; ?>
							<?php if ( count( $akzara_imgs ) > 1 ) : ?>
								<button class="gal__nav gal__nav--prev" type="button" data-dir="-1" aria-label="Foto anterior">‹</button>
								<button class="gal__nav gal__nav--next" type="button" data-dir="1" aria-label="Foto siguiente">›</button>
								<span class="gal__count" aria-live="polite"><span data-n>1</span> / <?php echo count( $akzara_imgs ); ?></span>
							<?php endif; ?>
						</div>
						<?php if ( count( $akzara_imgs ) > 1 ) : ?>
							<div class="gal__thumbs">
								<?php foreach ( $akzara_imgs as $akzara_k => $akzara_id ) : ?>
									<button class="gal__thumb" type="button" aria-pressed="<?php echo $akzara_k ? 'false' : 'true'; ?>" aria-label="Foto <?php echo (int) $akzara_k + 1; ?>" data-i="<?php echo (int) $akzara_k; ?>">
										<?php echo akzara_media( $akzara_id, $akzara_s['tone'], 'r-1x1', array( 'size' => 'akzara-thumb', 'sizes' => '96px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									</button>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( $akzara_s['description'] ) : ?>
						<div class="prose ritual__desc"><?php echo wp_kses_post( wpautop( $akzara_s['description'] ) ); ?></div>
					<?php endif; ?>
				</div>
				<aside class="ritual__side" aria-label="<?php echo esc_attr( $akzara_s['name'] ); ?>">
					<div class="book-card">
						<?php if ( $akzara_s['summary'] ) : ?>
							<p class="lead"><?php echo esc_html( $akzara_s['summary'] ); ?></p>
						<?php endif; ?>
						<dl class="facts">
							<div><dt>Duración</dt><dd><?php echo esc_html( akzara_duration( $akzara_s['min'] ) ); ?></dd></div>
							<div><dt>Personas</dt><dd><?php echo esc_html( $akzara_s['min_people'] === $akzara_s['max_people'] ? $akzara_s['min_people'] : $akzara_s['min_people'] . '–' . $akzara_s['max_people'] ); ?></dd></div>
							<div>
								<dt>Desde</dt>
								<dd><span class="big"><?php echo esc_html( akzara_cop( $akzara_s['price'] ) ); ?></span><small><?php echo 'grupo' === $akzara_s['mode'] ? 'por pareja' : 'por persona'; ?></small></dd>
							</div>
						</dl>
						<div class="book-card__acts">
							<a class="btn btn--block" href="<?php echo esc_url( $akzara_book ); ?>">Reservar</a>
							<?php if ( $akzara_s['giftable'] ) : ?>
								<a class="btn btn--ghost btn--block" href="<?php echo esc_url( $akzara_gift ); ?>">Regalar</a>
							<?php endif; ?>
						</div>
						<p class="note">Es una solicitud: nuestro concierge confirmará la disponibilidad y le enviará el enlace para el anticipo.</p>
					</div>
				</aside>
			</div>
		</div>
	</div>

	<?php if ( $akzara_s['steps'] ) : ?>
		<section class="section--tight timeline-sec">
			<div class="container">
				<h2 class="h-section reveal" style="font-size:clamp(28px,3.2vw,38px);margin-bottom:36px">Así transcurre su ritual</h2>
				<ol class="timeline">
					<?php foreach ( $akzara_s['steps'] as $akzara_i => $akzara_x ) : ?>
						<li class="reveal">
							<span class="timeline__dot" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $akzara_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<div class="timeline__body">
								<b class="serif"><?php echo esc_html( isset( $akzara_x['title'] ) ? $akzara_x['title'] : '' ); ?></b>
								<?php if ( ! empty( $akzara_x['text'] ) ) : ?>
									<p><?php echo esc_html( $akzara_x['text'] ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $akzara_x['image'] ) ) : ?>
									<div class="timeline__img parallax">
										<?php echo akzara_media( (int) $akzara_x['image'], $akzara_s['tone'], 'r-16x9', array( 'sizes' => '(max-width: 860px) 100vw, 520px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									</div>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $akzara_s['includes'] || $akzara_s['contra'] ) : ?>
		<section class="section--tight">
			<div class="container split" style="align-items:start">
				<?php if ( $akzara_s['includes'] ) : ?>
					<div>
						<h2 class="serif" style="font-size:24px">Incluye</h2>
						<ul class="muted" style="font-size:13.5px;line-height:1.9">
							<?php foreach ( $akzara_s['includes'] as $akzara_x ) : ?>
								<li><?php echo esc_html( $akzara_x ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<?php if ( $akzara_s['contra'] ) : ?>
					<div>
						<h2 class="serif" style="font-size:24px">Contraindicaciones</h2>
						<p class="muted" style="font-size:13.5px"><?php echo esc_html( $akzara_s['contra'] ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$akzara_faqs = akzara_faqs( 4 );
	if ( $akzara_faqs ) :
		?>
		<section class="section--tight">
			<div class="container container--narrow">
				<h2 class="h-section reveal" style="font-size:clamp(26px,3vw,34px);margin-bottom:22px">Antes de venir</h2>
				<?php akzara_faq_list( $akzara_faqs ); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	// Relacionados: los elegidos a mano; si no hay, los de la misma categoría.
	$akzara_rel = array();
	if ( $akzara_s['related'] ) {
		$akzara_rel = akzara_rituals( array( 'post__in' => array_values( $akzara_s['related'] ), 'posts_per_page' => 3 ) );
	}
	if ( ! $akzara_rel && $akzara_s['cat_slug'] ) {
		$akzara_rel = akzara_rituals(
			array(
				'post__not_in'   => array( $akzara_s['id'] ),
				'posts_per_page' => 3,
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
					array(
						'taxonomy' => 'akzara_categoria',
						'field'    => 'slug',
						'terms'    => $akzara_s['cat_slug'],
					),
				),
			)
		);
	}
	// Si la categoría tiene pocos rituales, se completa con otros hasta tener tres.
	if ( count( $akzara_rel ) < 3 ) {
		$akzara_have = array_merge( array( $akzara_s['id'] ), wp_list_pluck( $akzara_rel, 'id' ) );
		$akzara_more = akzara_rituals(
			array(
				'post__not_in'   => $akzara_have,
				'posts_per_page' => 3 - count( $akzara_rel ),
				'meta_key'       => '_ak_featured', // phpcs:ignore WordPress.DB.SlowDBQuery
				'orderby'        => array( 'meta_value_num' => 'DESC', 'menu_order' => 'ASC' ),
			)
		);
		$akzara_rel = array_merge( $akzara_rel, $akzara_more );
	}
	if ( $akzara_rel ) :
		?>
		<section class="section">
			<div class="container">
				<h2 class="h-section" style="margin-bottom:32px">También le puede interesar</h2>
				<div class="grid grid-3" style="row-gap:40px">
					<?php foreach ( array_slice( $akzara_rel, 0, 3 ) as $akzara_r ) : ?>
						<?php akzara_ritual_card( $akzara_r ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
