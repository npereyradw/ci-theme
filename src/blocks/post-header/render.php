<?php
/**
 * Render: ci/post-header — header of a single post and its cover. The breadcrumbs end with the
 * category.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$post = CI_Theme_Content::context_post( 'post' );
if ( ! $post ) {
	return;
}
$n    = CI_Theme_Content::news_item( $post );
$text = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};
$lead = $n['excerpt'];
// Author: the name set in the block, or the site title when the block has none.
$author = isset( $attributes['authorName'] ) ? trim( (string) $attributes['authorName'] ) : get_bloginfo( 'name' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'ci-post-header' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<header class="ci-post-header-head">
		<?php
		echo CI_Theme_Breadcrumbs::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Breadcrumbs.
			array(
				'label' => $n['category'],
				'post'  => $post,
			)
		);
		?>
		<div data-anim="rise" class="ci-post-header-meta">
			<span class="ci-post-header-category"><?php echo esc_html( $n['category'] ); ?></span>
			<time datetime="<?php echo esc_attr( $n['date_iso'] ); ?>"><?php echo esc_html( $n['date'] ); ?></time>
			<span><?php echo esc_html( trim( $n['reading'] . ' ' . $text( 'readingSuffix' ) ) ); ?></span>
		</div>
		<h1 data-anim-css="mask" data-anim-step="1" class="ci-post-header-title"><span><?php echo esc_html( $n['title'] ); ?></span></h1>
		<?php if ( '' !== $lead ) : ?>
			<p data-anim="rise" class="ci-post-header-lead"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $author ) : ?>
			<div data-anim="rise" class="ci-post-header-author">
				<span aria-hidden="true"><?php echo CI_Theme_Logo::icon( 36 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Logo. ?></span>
				<span class="ci-post-header-author-text">
					<span class="ci-post-header-author-name"><?php echo esc_html( $author ); ?></span>
					<?php if ( '' !== $text( 'authorRole' ) ) : ?>
						<span class="ci-post-header-author-role"><?php echo esc_html( $text( 'authorRole' ) ); ?></span>
					<?php endif; ?>
				</span>
			</div>
		<?php endif; ?>
	</header>
	<div class="ci-container">
		<?php
		echo CI_Theme_Cover::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cover.
			array(
				'seed'     => $n['cover'],
				'ratio'    => '16 / 7',
				'image'    => $n['image'],
				'priority' => true,
				'sizes'    => '(max-width: 1279px) 100vw, 1240px',
			)
		);
		?>
	</div>
</div>
