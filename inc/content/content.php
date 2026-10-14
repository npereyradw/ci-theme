<?php
/**
 * Module: content readers (services, projects, news, team…) and the markup shared by several
 * sections (cards, institutions list).
 *
 * The post types are registered by a companion plugin; every reader returns an empty list when
 * they are missing, so the theme never breaks without it.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-ci-theme-content.php';
require_once __DIR__ . '/includes/class-ci-theme-institutions.php';
require_once __DIR__ . '/includes/class-ci-theme-cards.php';

add_action(
	'init',
	static function () {
		wp_register_style(
			'ci-theme-cards',
			CI_THEME_URI . 'inc/content/assets/css/cards.css',
			array(),
			ci_theme_asset_version( 'inc/content/assets/css/cards.css' )
		);
		wp_register_style(
			'ci-theme-institutions',
			CI_THEME_URI . 'inc/content/assets/css/institutions.css',
			array(),
			ci_theme_asset_version( 'inc/content/assets/css/institutions.css' )
		);
	},
	5
);
