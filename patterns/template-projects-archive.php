<?php
/**
 * Title: Template · Projects archive
 * Slug: ci-theme/template-projects-archive
 * Inserter: no
 * Description: Content of the projects archive template.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$hero = array(
	'title' => __( 'Projects', 'ci-theme' ),
	'lead'  => __( 'Industrial works, structural calculations, architecture and telecommunication sites in different regions.', 'ci-theme' ),
);
$cta  = array(
	'useGlobal' => false,
	'title'     => __( 'Have a similar project? Request your quote.', 'ci-theme' ),
);
?>
<!-- wp:ci/page-hero <?php echo serialize_block_attributes( $hero ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/projects {"layout":"listing"} /-->

<!-- wp:ci/cta <?php echo serialize_block_attributes( $cta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->
