<?php
/**
 * Title: Template · News
 * Slug: ci-theme/template-news
 * Inserter: no
 * Description: Content of the posts page and category templates.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$hero = array(
	'title'   => __( 'News', 'ci-theme' ),
	'lead'    => __( 'Company news, ongoing works and technical guides to plan your project.', 'ci-theme' ),
	'divider' => true,
);
?>
<!-- wp:ci/page-hero <?php echo serialize_block_attributes( $hero ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/news {"layout":"listing","scheme":"light"} /-->

<!-- wp:ci/cta /-->
