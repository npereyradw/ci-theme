<?php
/**
 * Module: site animations (GSAP). Loaded only on pages with a block that declares the
 * "ci-theme-motion" view script.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-ci-theme-motion.php';

CI_Theme_Motion::init();
