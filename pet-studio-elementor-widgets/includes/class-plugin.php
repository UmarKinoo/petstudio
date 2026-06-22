<?php
/**
 * Plugin bootstrap.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	private static ?Plugin $instance = null;

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'bootstrap' ), 20 );
		add_action( 'init', array( $this, 'maybe_bust_caches' ), 30 );
		add_filter( 'litespeed_media_lazy_img_cls_excludes', array( $this, 'litespeed_lazy_exclude_classes' ) );
		add_filter( 'litespeed_optimize_css_excludes', array( $this, 'litespeed_css_excludes' ) );
	}

	/**
	 * Clear Elementor + LiteSpeed caches after plugin updates so render fixes reach production.
	 */
	public function maybe_bust_caches(): void {
		$stored = get_option( 'pet_studio_ew_version', '' );
		if ( $stored === PET_STUDIO_EW_VERSION ) {
			return;
		}

		update_option( 'pet_studio_ew_version', PET_STUDIO_EW_VERSION, false );
		self::purge_elementor_caches();
	}

	/**
	 * Clear Elementor file cache + LiteSpeed after plugin update or demo import.
	 */
	public static function purge_elementor_caches(): void {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$elementor = \Elementor\Plugin::$instance;
			if ( isset( $elementor->files_manager ) ) {
				$elementor->files_manager->clear_cache();
			}
		}

		do_action( 'litespeed_purge_all' );
	}

	/**
	 * @param string[] $excludes Class names excluded from LiteSpeed lazy load.
	 * @return string[]
	 */
	public function litespeed_lazy_exclude_classes( array $excludes ): array {
		return array_merge( $excludes, array( 'ps-no-lazy', 'skip-lazy', 'litespeed-no-lazy' ) );
	}

	/**
	 * Keep plugin frontend.css out of LiteSpeed combined bundles (stale CSS cache on Hostinger).
	 *
	 * @param string[] $excludes CSS handle or URL fragments to skip.
	 * @return string[]
	 */
	public function litespeed_css_excludes( array $excludes ): array {
		return array_merge(
			$excludes,
			array(
				'pet-studio-elementor-frontend',
				'pet-studio-elementor-widgets/assets/css/frontend.css',
			)
		);
	}

	/**
	 * Elementor fires elementor/loaded before Widget_Base exists.
	 * Widget_Base loads during elementor/init (WordPress init hook).
	 */
	public function bootstrap(): void {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'elementor_missing_notice' ) );
			return;
		}

		add_action( 'elementor/init', array( $this, 'on_elementor_loaded' ) );
	}

	public function on_elementor_loaded(): void {
		if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}

		require_once PET_STUDIO_EW_PATH . 'includes/class-widget-base.php';
		require_once PET_STUDIO_EW_PATH . 'includes/class-demo-importer.php';

		Demo_Importer::register_admin();
		Demo_Importer::maybe_repair_theme_builder();

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_assets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_assets' ) );
	}

	public function elementor_missing_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>';
		echo esc_html__( 'Pet Studio Elementor Widgets requires Elementor to be installed and active.', 'pet-studio-elementor' );
		echo '</p></div>';
	}

	public function register_category( $elements_manager ): void {
		$elements_manager->add_category(
			'pet-studio',
			array(
				'title' => esc_html__( 'Pet Studio', 'pet-studio-elementor' ),
				'icon'  => 'fa fa-paw',
			)
		);
	}

	public function register_widgets( $widgets_manager ): void {
		$widgets = array(
			'Header'         => 'Header_Widget',
			'Footer'         => 'Footer_Widget',
			'Cookie_Consent' => 'Cookie_Consent_Widget',
			'Hero_Home'      => 'Hero_Home_Widget',
			'Hero_Inner'     => 'Hero_Inner_Widget',
			'Services_Cards' => 'Services_Cards_Widget',
			'About_Intro'    => 'About_Intro_Widget',
			'Page_Intro'     => 'Page_Intro_Widget',
			'Content_Split'  => 'Content_Split_Widget',
			'Dog_Divider'    => 'Dog_Divider_Widget',
			'Courses_Tabs'   => 'Courses_Tabs_Widget',
			'Testimonials'   => 'Testimonials_Widget',
			'Team_Member'    => 'Team_Member_Widget',
			'Est_Banner'     => 'Est_Banner_Widget',
			'Contact'        => 'Contact_Widget',
		);

		foreach ( $widgets as $file => $class ) {
			require_once PET_STUDIO_EW_PATH . 'widgets/class-' . strtolower( str_replace( '_', '-', $file ) ) . '-widget.php';
			$fqcn = '\\Pet_Studio_Elementor\\Widgets\\' . $class;
			if ( class_exists( $fqcn ) ) {
				$widgets_manager->register( new $fqcn() );
			}
		}
	}

	public function register_assets(): void {
		wp_register_style(
			'pet-studio-elementor-frontend',
			PET_STUDIO_EW_URL . 'assets/css/frontend.css',
			array( 'pet-studio-yootheme-theme' ),
			PET_STUDIO_EW_VERSION
		);

		wp_register_style(
			'pet-studio-elementor-editor',
			PET_STUDIO_EW_URL . 'assets/css/editor.css',
			array( 'pet-studio-elementor-frontend' ),
			PET_STUDIO_EW_VERSION
		);

		Assets::register();
	}

	public function enqueue_editor_assets(): void {
		wp_enqueue_style( 'pet-studio-elementor-editor' );
		Assets::enqueue_editor();
	}
}
