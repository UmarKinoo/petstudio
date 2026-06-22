<?php
/**
 * Plugin Name:       Pet Studio Elementor Widgets
 * Description:       Custom Elementor widgets for The Pet Studio — faithful mirror HTML/CSS with full builder controls and API-ready schemas.
 * Version:           0.5.21
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            The Pet Studio
 * Text Domain:       pet-studio-elementor
 *
 * @package Pet_Studio_Elementor
 */

defined( 'ABSPATH' ) || exit;

define( 'PET_STUDIO_EW_VERSION', '0.5.21' );
define( 'PET_STUDIO_EW_FILE', __FILE__ );
define( 'PET_STUDIO_EW_PATH', plugin_dir_path( __FILE__ ) );
define( 'PET_STUDIO_EW_URL', plugin_dir_url( __FILE__ ) );

require_once PET_STUDIO_EW_PATH . 'includes/helpers.php';
require_once PET_STUDIO_EW_PATH . 'includes/class-content-normalizer.php';
require_once PET_STUDIO_EW_PATH . 'includes/class-assets.php';
require_once PET_STUDIO_EW_PATH . 'includes/class-plugin.php';

Pet_Studio_Elementor\Plugin::instance();
