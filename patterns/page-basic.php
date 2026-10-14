<?php
/**
 * Title: Page · Basic
 * Slug: ci-theme/page-basic
 * Categories: ci-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1440
 * Description: A starting point for a new page: heading, text with media and call to action.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:ci/page-hero <?php echo serialize_block_attributes( array( 'title' => __( 'Page title', 'ci-theme' ), 'lead' => __( 'One or two sentences about what this page is about.', 'ci-theme' ), 'divider' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/text-media /-->

<!-- wp:ci/cta /-->
