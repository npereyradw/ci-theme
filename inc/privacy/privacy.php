<?php
/**
 * Module: Google Analytics 4 and the cookie notice. Both need a measurement ID in the site
 * settings; without it nothing is printed or loaded.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-ci-theme-privacy.php';

CI_Theme_Privacy::init();
