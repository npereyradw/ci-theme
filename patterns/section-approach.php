<?php
/**
 * Title: Section · Approach
 * Slug: ci-theme/section-approach
 * Categories: ci-sections
 * Keywords: approach, values, features
 * Viewport Width: 1440
 * Description: Three cards that explain how the firm works.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$attributes = array(
	'eyebrow' => __( 'Approach', 'ci-theme' ),
	'title'   => __( 'Comprehensive advice, with clear rules from the start.', 'ci-theme' ),
	'variant' => 'cards',
	'items'   => array(
		array(
			'title' => __( 'Comprehensive', 'ci-theme' ),
			'text'  => __( 'We cover the whole cycle: assessment, design, permits and supervision.', 'ci-theme' ),
		),
		array(
			'title' => __( 'Transparent', 'ci-theme' ),
			'text'  => __( 'A detailed quote and schedule before any work begins.', 'ci-theme' ),
		),
		array(
			'title' => __( 'Accountable', 'ci-theme' ),
			'text'  => __( 'One lead for each project, available from start to finish.', 'ci-theme' ),
		),
	),
);
?>
<!-- wp:ci/features <?php echo serialize_block_attributes( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->
