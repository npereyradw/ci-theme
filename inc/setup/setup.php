<?php
/**
 * Module: theme setup (supports, base stylesheet, font preload).
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-ci-theme-setup.php';

CI_Theme_Setup::init();
