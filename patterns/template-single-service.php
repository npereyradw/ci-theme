<?php
/**
 * Title: Template · Single service
 * Slug: ci-theme/template-single-service
 * Inserter: no
 * Description: Content of the single service template.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$process  = array(
	'layout'  => 'cover',
	'eyebrow' => __( 'How we work', 'ci-theme' ),
	'steps'   => array(
		array(
			'title' => __( 'Planning', 'ci-theme' ),
			'text'  => __( 'We survey the need and the site, and send you the scope, a fixed quote and a schedule.', 'ci-theme' ),
		),
		array(
			'title' => __( 'Design', 'ci-theme' ),
			'text'  => __( 'We develop the calculations, drawings and technical documents to approve and build.', 'ci-theme' ),
		),
		array(
			'title' => __( 'Construction', 'ci-theme' ),
			'text'  => __( 'We follow the works with supervision and inspections until the handover.', 'ci-theme' ),
		),
	),
);
$projects = array(
	'source'    => 'current-service',
	'eyebrow'   => '',
	'title'     => __( 'Projects of this service', 'ci-theme' ),
	'linkLabel' => __( 'View all →', 'ci-theme' ),
	'linkUrl'   => '',
	'divider'   => false,
	'scheme'    => 'muted',
);
$others   = array(
	'eyebrow'     => '',
	'title'       => __( 'Other services', 'ci-theme' ),
	'linkLabel'   => '',
	'hideSoon'    => true,
	'limit'       => 3,
	'divider'     => true,
	'spaceBottom' => 'none',
);
?>
<!-- wp:ci/service-hero /-->

<!-- wp:ci/process <?php echo serialize_block_attributes( $process ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/projects <?php echo serialize_block_attributes( $projects ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/services <?php echo serialize_block_attributes( $others ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->

<!-- wp:ci/cta /-->
