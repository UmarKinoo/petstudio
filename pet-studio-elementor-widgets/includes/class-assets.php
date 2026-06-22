<?php
/**
 * Register and enqueue UIkit + mirror theme assets.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor;

defined( 'ABSPATH' ) || exit;

final class Assets {

	public static function register(): void {
		wp_register_style(
			'pet-studio-uikit',
			PET_STUDIO_EW_URL . 'assets/css/uikit.min.css',
			array(),
			'3.21.6'
		);

		wp_register_style(
			'pet-studio-yootheme-theme',
			PET_STUDIO_EW_URL . 'assets/css/yootheme-theme.css',
			array( 'pet-studio-uikit' ),
			PET_STUDIO_EW_VERSION
		);

		wp_register_script(
			'pet-studio-uikit',
			PET_STUDIO_EW_URL . 'assets/js/uikit.min.js',
			array(),
			PET_STUDIO_EW_VERSION,
			true
		);

		wp_register_script(
			'pet-studio-uikit-icons-kojiro',
			PET_STUDIO_EW_URL . 'assets/js/uikit-icons-kojiro.min.js',
			array( 'pet-studio-uikit' ),
			PET_STUDIO_EW_VERSION,
			true
		);

		wp_register_script(
			'pet-studio-yootheme-theme',
			PET_STUDIO_EW_URL . 'assets/js/yootheme-theme.js',
			array( 'pet-studio-uikit' ),
			PET_STUDIO_EW_VERSION,
			true
		);

		wp_register_script(
			'pet-studio-elementor-init',
			PET_STUDIO_EW_URL . 'assets/js/elementor-init.js',
			array( 'jquery', 'pet-studio-uikit-icons-kojiro', 'pet-studio-yootheme-theme' ),
			PET_STUDIO_EW_VERSION,
			true
		);
	}

	/**
	 * Style handles loaded by every Pet Studio widget.
	 */
	public static function widget_style_handles(): array {
		return array(
			'pet-studio-uikit',
			'pet-studio-yootheme-theme',
			'pet-studio-elementor-frontend',
		);
	}

	/**
	 * Script handles loaded by every Pet Studio widget.
	 */
	public static function widget_script_handles(): array {
		return array(
			'pet-studio-uikit',
			'pet-studio-uikit-icons-kojiro',
			'pet-studio-yootheme-theme',
			'pet-studio-elementor-init',
		);
	}

	public static function enqueue_editor(): void {
		foreach ( self::widget_style_handles() as $handle ) {
			wp_enqueue_style( $handle );
		}
		foreach ( self::widget_script_handles() as $handle ) {
			wp_enqueue_script( $handle );
		}
	}
}
