<?php
/**
 * Secciones de la página de inicio (todo editable en Personalizador → Inicio).
 *
 * @package akzara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$akzara_all = akzara_rituals();

// Rituales destacados.
$akzara_feat = array_values( array_filter( $akzara_all, function ( $r ) { return $r['featured']; } ) );
usort( $akzara_feat, function ( $a, $b ) { return $a['order'] <=> $b['order']; } );
$akzara_feat = array_slice( $akzara_feat, 0, max( 1, (int) akzara_opt( 'featured_limit' ) ) );
if ( $akzara_feat ) :
	?>
	<section class="section" id="contenido">
		<div class="container">
			<div class="sec-head">
				<h2 class="h-section"><?php echo akzara_italic( akzara_opt( 'featured_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<a class="link" href="<?php echo esc_url( get_post_type_archive_link( 'akzara_ritual' ) ); ?>"><?php echo esc_html( akzara_opt( 'featured_link' ) ); ?></a>
			</div>
			<div class="grid grid-3" style="row-gap:48px">
				<?php foreach ( $akzara_feat as $akzara_r ) : ?>
					<?php akzara_ritual_card( $akzara_r ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/manifesto', null, array( 'eyebrow' => akzara_opt( 'manifesto_eyebrow' ), 'phrase' => akzara_opt( 'manifesto_phrase' ), 'text' => akzara_opt( 'manifesto_text' ), 'image' => akzara_opt_img( 'manifesto_image' ) ) ); ?>

<section class="section--tight" style="padding-top:0">
	<div class="container">
		<h2 class="serif" style="font-size:22px;margin:0 0 22px"><?php echo akzara_italic( akzara_opt( 'occ_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<div class="occ">
			<?php foreach ( array( 1, 2, 3, 4 ) as $akzara_n ) : ?>
				<a href="<?php echo esc_url( akzara_link( akzara_opt( 'occ' . $akzara_n . '_link' ) ) ); ?>" class="hover-zoom">
					<?php echo akzara_media( akzara_opt_img( 'occ' . $akzara_n . '_image' ), 3 === $akzara_n ? 'humo' : 'arena', '', array( 'size' => 'akzara-card', 'sizes' => '(max-width: 760px) 50vw, 25vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="cap"><?php echo esc_html( akzara_opt( 'occ' . $akzara_n . '_label' ) ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
// Ritual insignia.
$akzara_sig = null;
foreach ( $akzara_all as $akzara_r ) {
	if ( $akzara_r['signature'] ) {
		$akzara_sig = $akzara_r;
		break;
	}
}
if ( $akzara_sig ) :
	$akzara_sig_img = akzara_opt_img( 'sig_image' ) ? akzara_opt_img( 'sig_image' ) : ( $akzara_sig['gallery'] ? $akzara_sig['gallery'][0] : $akzara_sig['thumb'] );
	?>
	<section class="signature section" style="margin-top:clamp(48px,7vw,96px)">
		<?php echo akzara_media( $akzara_sig_img, 'dark', '', array( 'sizes' => '100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="container signature__in">
			<span class="eyebrow eyebrow--bronze"><?php echo esc_html( akzara_opt( 'sig_eyebrow' ) ); ?></span>
			<h2 class="serif"><?php echo esc_html( $akzara_sig['name'] ); ?></h2>
			<p><?php echo esc_html( akzara_opt( 'sig_text' ) ? akzara_opt( 'sig_text' ) : $akzara_sig['summary'] ); ?></p>
			<div class="price"><?php echo esc_html( akzara_duration( $akzara_sig['min'] ) ); ?> · desde <?php echo esc_html( akzara_cop( $akzara_sig['price'] ) ); ?></div>
			<div class="acts">
				<a class="btn btn--outline-light" href="<?php echo esc_url( add_query_arg( 'ritual', $akzara_sig['slug'], home_url( '/reservar/' ) ) ); ?>">Reservar</a>
				<a class="text" href="<?php echo esc_url( $akzara_sig['url'] ); ?>">Conocer el ritual</a>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
// El recorrido.
$akzara_steps = array();
foreach ( array( 1, 2, 3, 4 ) as $akzara_n ) {
	if ( akzara_opt( 'j' . $akzara_n . '_title' ) ) {
		$akzara_steps[] = array(
			'title' => akzara_opt( 'j' . $akzara_n . '_title' ),
			'text'  => akzara_opt( 'j' . $akzara_n . '_text' ),
			'image' => akzara_opt_img( 'j' . $akzara_n . '_image' ),
		);
	}
}
if ( $akzara_steps ) :
	?>
	<section class="section journey">
		<div class="container">
			<div class="journey__head reveal">
				<?php if ( akzara_opt( 'journey_eyebrow' ) ) : ?>
					<span class="eyebrow eyebrow--bronze"><?php echo esc_html( akzara_opt( 'journey_eyebrow' ) ); ?></span>
				<?php endif; ?>
				<h2 class="h-section"><?php echo akzara_italic( akzara_opt( 'journey_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			</div>
			<ol class="journey__list">
				<?php foreach ( $akzara_steps as $akzara_i => $akzara_s ) : ?>
					<li class="journey__item reveal">
						<div class="journey__media parallax">
							<?php echo akzara_media( $akzara_s['image'], 'arena', 'r-4x5', array( 'size' => 'akzara-card', 'sizes' => '(max-width: 860px) 100vw, 40vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<div class="journey__txt">
							<span class="journey__n" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $akzara_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<h3><?php echo esc_html( $akzara_s['title'] ); ?></h3>
							<?php if ( $akzara_s['text'] ) : ?>
								<p><?php echo esc_html( $akzara_s['text'] ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
<?php endif; ?>

<?php if ( akzara_memberships() ) : ?>
	<section class="section">
		<div class="container">
			<div class="sec-head">
				<h2 class="h-section"><?php echo akzara_italic( akzara_opt( 'plans_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<span class="muted" style="font-size:12.5px"><?php echo esc_html( akzara_opt( 'plans_subtitle' ) ); ?></span>
			</div>
			<?php akzara_plans(); ?>
		</div>
	</section>
<?php endif; ?>

<section class="section section--dark">
	<div class="container giftcta">
		<div class="card3d"><?php echo akzara_gift_face( 'lino', '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div>
			<h2 class="h-section" style="margin-bottom:26px"><?php echo akzara_italic( akzara_opt( 'gift_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<a class="btn btn--outline-light" href="<?php echo esc_url( home_url( '/gift-card/' ) ); ?>"><?php echo esc_html( akzara_opt( 'gift_button' ) ); ?></a>
		</div>
	</div>
</section>

<?php
$akzara_quotes = akzara_testimonials( 3 );
if ( $akzara_quotes ) :
	?>
	<section class="section section--soft">
		<div class="container">
			<h2 class="h-section reveal" style="margin-bottom:36px"><?php echo akzara_italic( akzara_opt( 'quotes_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<div class="quotes">
				<?php foreach ( $akzara_quotes as $akzara_q ) : ?>
					<figure class="quote reveal">
						<blockquote><?php echo esc_html( $akzara_q[1] ); ?></blockquote>
						<figcaption><?php echo esc_html( $akzara_q[0] ); ?><?php echo $akzara_q[2] ? '<small> · ' . esc_html( $akzara_q[2] ) . '</small>' : ''; ?></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
$akzara_faqs = akzara_faqs( max( 1, (int) akzara_opt( 'faq_limit' ) ) );
if ( $akzara_faqs ) :
	?>
	<section class="section">
		<div class="container container--narrow">
			<h2 class="h-section reveal" style="margin-bottom:28px"><?php echo akzara_italic( akzara_opt( 'faq_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php akzara_faq_list( $akzara_faqs ); ?>
		</div>
	</section>
<?php endif; ?>

<?php akzara_hscroll( akzara_opt( 'gallery_title' ), array_filter( array_map( 'absint', explode( ',', (string) akzara_opt( 'gallery_ids' ) ) ) ) ); ?>

<section class="section--tight" style="padding-bottom:clamp(64px,9vw,120px)">
	<div class="container">
		<h2 class="serif" style="font-size:22px;margin:0 0 24px"><?php echo akzara_italic( akzara_opt( 'visit_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php akzara_visit_info(); ?>
	</div>
</section>

<?php akzara_closing( akzara_opt( 'closing_title' ), akzara_opt( 'closing_button' ), akzara_opt( 'closing_link' ) ); ?>
