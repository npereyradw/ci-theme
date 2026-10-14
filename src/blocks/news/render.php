<?php
/**
 * Render: ci/news — blog posts.
 *
 * - cards: the latest posts; hidden while there are none.
 * - listing: the posts page. It uses the main WordPress query, so pagination and categories are
 *   real URLs that work without JavaScript. The first post of each page is featured.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$text = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};

/* ── Listing (posts page) ─────────────────────────────────────── */

if ( isset( $attributes['layout'] ) && 'listing' === $attributes['layout'] ) :
	global $wp_query;
	$main = ( is_home() || is_category() ) && $wp_query instanceof WP_Query;
	if ( $main ) {
		$news  = array_map( array( 'CI_Theme_Content', 'news_item' ), $wp_query->posts );
		$page  = max( 1, (int) get_query_var( 'paged' ) );
		$pages = max( 1, (int) $wp_query->max_num_pages );
	} else {
		// Editor preview: the first page.
		$news  = CI_Theme_Content::news( array( 'limit' => max( 1, (int) get_option( 'posts_per_page' ) ) ) );
		$page  = 1;
		$pages = 1;
	}
	$current = is_category() ? get_queried_object() : null;
	$all_url = (int) get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );
	$cats    = get_categories(
		array(
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	$featured = array_shift( $news );
	?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-news-listing' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?> aria-label="<?php esc_attr_e( 'Posts', 'ci-theme' ); ?>">
	<div class="ci-container">
		<?php if ( ! $featured ) : ?>
			<div class="ci-empty">
				<p class="ci-empty-title"><?php echo esc_html( $text( 'emptyTitle' ) ); ?></p>
				<?php if ( '' !== $text( 'emptyButtonLabel' ) ) : ?>
					<a href="<?php echo CI_Theme_Section::url( $text( 'emptyButtonUrl' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-btn ci-btn--outline ci-btn--sm"><?php echo esc_html( $text( 'emptyButtonLabel' ) ); ?></a>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="ci-news-listing-stack">
				<?php if ( count( $cats ) > 1 ) : ?>
					<nav class="ci-chips" aria-label="<?php echo esc_attr( $text( 'filterLabel' ) ); ?>">
						<a href="<?php echo esc_url( $all_url ); ?>" class="ci-chip"<?php echo $current ? '' : ' aria-current="page"'; ?>><?php echo esc_html( $text( 'allLabel' ) ); ?></a>
						<?php foreach ( $cats as $cat ) : ?>
							<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="ci-chip"<?php echo $current && $current->term_id === $cat->term_id ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $cat->name ); ?></a>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>

				<a href="<?php echo esc_url( $featured['url'] ); ?>" class="ci-news-listing-featured">
					<?php
					echo CI_Theme_Cover::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cover.
						array(
							'seed'  => $featured['cover'],
							'ratio' => '16 / 10',
							'image' => $featured['image'],
							'sizes' => '(max-width: 1023px) 100vw, 600px',
						)
					);
					?>
					<div class="ci-news-listing-featured-body">
						<?php if ( 1 === $page && '' !== $text( 'featuredBadge' ) ) : ?>
							<span class="ci-news-listing-badge"><?php echo esc_html( $text( 'featuredBadge' ) ); ?></span>
						<?php endif; ?>
						<div class="ci-news-listing-meta">
							<span class="ci-news-listing-category"><?php echo esc_html( $featured['category'] ); ?></span>
							<time datetime="<?php echo esc_attr( $featured['date_iso'] ); ?>"><?php echo esc_html( $featured['date'] ); ?></time>
							<span><?php echo esc_html( $featured['reading'] ); ?></span>
						</div>
						<h2 class="ci-news-listing-title"><?php echo esc_html( $featured['title'] ); ?></h2>
						<?php if ( '' !== $featured['excerpt'] ) : ?>
							<p class="ci-news-listing-text"><?php echo esc_html( $featured['excerpt'] ); ?></p>
						<?php endif; ?>
						<span class="ci-news-listing-more"><?php echo esc_html( $text( 'readMore' ) ); ?> <span aria-hidden="true">→</span></span>
					</div>
				</a>

				<?php if ( $news ) : ?>
					<div class="ci-cards ci-news-listing-grid">
						<?php
						foreach ( $news as $item ) {
							echo CI_Theme_Cards::news( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cards.
						}
						?>
					</div>
				<?php endif; ?>

				<?php if ( $pages > 1 ) : ?>
					<nav class="ci-news-listing-pager" aria-label="<?php esc_attr_e( 'Pagination', 'ci-theme' ); ?>">
						<?php for ( $n = 1; $n <= $pages; $n++ ) : ?>
							<a href="<?php echo esc_url( get_pagenum_link( $n ) ); ?>" class="ci-news-listing-page"<?php echo $n === $page ? ' aria-current="page"' : ''; ?> aria-label="<?php /* translators: %d: page number. */ echo esc_attr( sprintf( __( 'Page %d', 'ci-theme' ), $n ) ); ?>"><?php echo (int) $n; ?></a>
						<?php endfor; ?>
						<?php if ( $page < $pages && '' !== $text( 'nextLabel' ) ) : ?>
							<a href="<?php echo esc_url( get_pagenum_link( $page + 1 ) ); ?>" class="ci-news-listing-next" rel="next"><?php echo esc_html( $text( 'nextLabel' ) ); ?></a>
						<?php endif; ?>
					</nav>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
	<?php
	return;
endif;

/* ── Latest posts ─────────────────────────────────────────────── */

// On a single post, the post being viewed is not repeated.
$current = get_post();
$news    = CI_Theme_Content::news(
	array(
		'limit'    => max( 1, (int) $attributes['limit'] ),
		'category' => isset( $attributes['category'] ) ? sanitize_title( $attributes['category'] ) : '',
		'exclude'  => $current && 'post' === $current->post_type ? array( $current->ID ) : array(),
	)
);
if ( ! $news ) {
	return;
}

$id = wp_unique_id( 'ci-news-title-' );
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-news' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-stack ci-stack--md">
		<?php echo CI_Theme_Section::intro( $attributes, array( 'id' => $id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		<div data-anim-stagger class="ci-cards ci-cards--tall">
			<?php
			foreach ( $news as $item ) {
				echo CI_Theme_Cards::news( $item, empty( $attributes['title'] ) ? 'h2' : 'h3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cards.
			}
			?>
		</div>
	</div>
</section>
