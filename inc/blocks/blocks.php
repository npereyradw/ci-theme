<?php
/**
 * Module: block infrastructure (registry, categories, allowed blocks, shared editor library).
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-ci-theme-blocks.php';

CI_Theme_Blocks::init();
