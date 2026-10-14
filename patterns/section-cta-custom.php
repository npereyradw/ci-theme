<?php
/**
 * Title: Section · Call to action with its own text
 * Slug: ci-theme/section-cta-custom
 * Categories: ci-sections
 * Keywords: cta, quote, contact
 * Viewport Width: 1440
 * Description: A call to action with its own eyebrow, title and text instead of the site-wide ones.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$attributes = array(
	'useGlobal' => false,
	'eyebrow'   => __( 'Free quote', 'ci-theme' ),
	'title'     => __( 'Is your project in another region? We can help too.', 'ci-theme' ),
	'text'      => __( 'We reply on business days and work with industries, studios, builders and private clients across the country.', 'ci-theme' ),
);
?>
<!-- wp:ci/cta <?php echo serialize_block_attributes( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block attributes. ?> /-->
