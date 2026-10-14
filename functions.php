<?php
/**
 * CI Theme — loader.
 *
 * Only loads modules. Each module lives in inc/<module>/ with its entry file and classes.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

define( 'CI_THEME_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );
define( 'CI_THEME_DIR', get_template_directory() . '/' );
define( 'CI_THEME_URI', get_template_directory_uri() . '/' );

if ( ! function_exists( 'ci_theme_asset_version' ) ) {
	/**
	 * Version of a theme asset: its modification time, so a change is never served from cache.
	 *
	 * @param string $relative Path relative to the theme root.
	 * @return string
	 */
	function ci_theme_asset_version( $relative ) {
		$file = CI_THEME_DIR . $relative;
		return file_exists( $file ) ? (string) filemtime( $file ) : CI_THEME_VERSION;
	}
}

require_once CI_THEME_DIR . 'inc/setup/setup.php';
