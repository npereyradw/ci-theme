<?php
/**
 * Module: forms (own markup, sent to Contact Form 7 through its REST endpoint).
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-ci-theme-forms.php';

CI_Theme_Forms::init();
