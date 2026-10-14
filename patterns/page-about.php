<?php
/**
 * Title: Page · About
 * Slug: ci-theme/page-about
 * Categories: ci-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1440
 * Description: An about page: story, approach, team and reach.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:ci/page-hero <?php echo serialize_block_attributes( array( 'variant' => 'dark', 'title' => __( 'Engineering applied to real projects.', 'ci-theme' ), 'lead' => __( 'We design, calculate and supervise projects of every scale, with one team from the first sketch to the handover.', 'ci-theme' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/text-media /-->

<!-- wp:pattern {"slug":"ci-theme/section-approach"} /-->

<!-- wp:ci/team /-->

<!-- wp:ci/reach /-->

<!-- wp:ci/institutions {"variant":"label"} /-->

<!-- wp:pattern {"slug":"ci-theme/section-cta-custom"} /-->
