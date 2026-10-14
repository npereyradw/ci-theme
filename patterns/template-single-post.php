<?php
/**
 * Title: Template · Single post
 * Slug: ci-theme/template-single-post
 * Inserter: no
 * Description: Content of the single post template.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$more = array(
	'eyebrow'   => '',
	'title'     => __( 'Keep reading', 'ci-theme' ),
	'linkLabel' => '',
	'limit'     => 3,
);
?>
<!-- wp:group {"tagName":"article","className":"ci-article","layout":{"type":"default"}} -->
<article class="wp-block-group ci-article"><!-- wp:ci/post-header /-->

<!-- wp:ci/post-body /--></article>
<!-- /wp:group -->

<!-- wp:ci/news <?php echo serialize_block_attributes( $more ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/cta /-->
