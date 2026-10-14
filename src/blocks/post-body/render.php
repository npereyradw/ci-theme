<?php
/**
 * Render: ci/post-body — post text and share buttons.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$post = CI_Theme_Content::context_post( 'post' );
if ( ! $post ) {
	return;
}

$url   = rawurlencode( get_permalink( $post ) );
$title = rawurlencode( get_the_title( $post ) . ' ' );
$all   = array(
	'linkedin' => array( 'LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url ),
	'facebook' => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . $url ),
	'whatsapp' => array( 'WhatsApp', 'https://wa.me/?text=' . $title . $url ),
	'x'        => array( 'X', 'https://x.com/intent/post?url=' . $url . '&text=' . rawurlencode( get_the_title( $post ) ) ),
);
$share = array();
foreach ( isset( $attributes['networks'] ) ? (array) $attributes['networks'] : array() as $key ) {
	if ( isset( $all[ $key ] ) ) {
		$share[] = $all[ $key ];
	}
}
$label = isset( $attributes['shareLabel'] ) ? trim( (string) $attributes['shareLabel'] ) : '';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'ci-post-body' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<div class="ci-prose">
		<?php echo CI_Theme_Content::content_html( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- post content filtered by the_content. ?>
	</div>
	<?php if ( $share ) : ?>
		<div class="ci-post-body-share">
			<?php if ( '' !== $label ) : ?>
				<span class="ci-post-body-share-label"><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
			<?php foreach ( $share as $item ) : ?>
				<a href="<?php echo esc_url( $item[1] ); ?>" class="ci-post-body-share-link" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $item[0] ); ?><?php echo CI_Theme_Section::new_tab_note(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
