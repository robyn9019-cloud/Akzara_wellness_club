<?php
/**
 * Plantilla de respaldo (listados de entradas, búsquedas, archivos).
 *
 * @package akzara
 */

get_header();
?>
<div class="page-top section" style="padding-top:calc(var(--header-h) + 54px)">
	<div class="container">
		<?php akzara_page_head( array( array( 'Inicio', home_url( '/' ) ), array( 'Diario', '' ) ), 'Diario', '', 'Notas sobre bienestar, agua y calma.' ); ?>
		<?php if ( have_posts() ) : ?>
			<div class="posts">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<a href="<?php the_permalink(); ?>" class="post-card hover-zoom">
						<?php echo akzara_media( (int) get_post_thumbnail_id(), 'arena', '', array( 'size' => 'akzara-card', 'sizes' => '(max-width: 760px) 100vw, 33vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
					</a>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p class="muted">Aún no hay entradas.</p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
