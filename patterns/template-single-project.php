<?php
/**
 * Title: Template · Single project
 * Slug: ci-theme/template-single-project
 * Inserter: no
 * Description: Content of the single project template.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$archive = get_post_type_archive_link( CI_Theme_Content::PROJECT );
$related = array(
	'source'    => 'related',
	'eyebrow'   => '',
	'title'     => __( 'Related projects', 'ci-theme' ),
	'linkLabel' => __( 'View all →', 'ci-theme' ),
	'linkUrl'   => $archive ? wp_make_link_relative( $archive ) : '',
	'divider'   => false,
	'scheme'    => 'muted',
);
?>
<!-- wp:ci/project-header /-->

<!-- wp:ci/project-body /-->

<!-- wp:ci/projects <?php echo serialize_block_attributes( $related ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->
