<?php
/**
 * Module: brand graphics drawn in SVG (generative covers, area icons, logo).
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-ci-theme-cover.php';
require_once __DIR__ . '/includes/class-ci-theme-area-icon.php';
require_once __DIR__ . '/includes/class-ci-theme-logo.php';

add_action(
	'init',
	static function () {
		// Loaded by the blocks that show covers (block.json → "style").
		wp_register_style(
			'ci-theme-cover',
			CI_THEME_URI . 'inc/graphics/assets/css/cover.css',
			array(),
			ci_theme_asset_version( 'inc/graphics/assets/css/cover.css' )
		);
	},
	5
);
