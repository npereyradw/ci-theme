<?php
/**
 * Title: Page · Services
 * Slug: ci-theme/page-services
 * Categories: ci-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1440
 * Description: The list of services with a filter by area.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:ci/page-hero <?php echo serialize_block_attributes( array( 'title' => __( 'Services for every stage of your project.', 'ci-theme' ), 'lead' => __( 'Hire a single service, such as a structural calculation, or the complete management of your project.', 'ci-theme' ), 'areaFilter' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/services {"layout":"listing"} /-->

<!-- wp:ci/audiences /-->

<!-- wp:ci/cta /-->
