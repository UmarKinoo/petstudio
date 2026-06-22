<?php
/**
 * Import mirror media + build Elementor demo pages from fixtures.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor;

defined( 'ABSPATH' ) || exit;

class Demo_Importer {

	private const OPTION_MEDIA_MAP = 'pet_studio_ew_media_map';
	private const OPTION_IMPORTED  = 'pet_studio_ew_demo_imported';

	/** @var array<string, array{id:int,url:string}> */
	private array $media_map = array();

	/** @var string */
	private string $workspace_root;

	public function __construct() {
		$this->workspace_root = rtrim( PET_STUDIO_EW_PATH, '/' );
	}

	public static function register_admin(): void {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_post_pet_studio_import_demo', array( __CLASS__, 'handle_import_request' ) );
	}

	public static function add_menu(): void {
		add_submenu_page(
			'tools.php',
			esc_html__( 'Pet Studio Demo Import', 'pet-studio-elementor' ),
			esc_html__( 'Pet Studio Demo', 'pet-studio-elementor' ),
			'manage_options',
			'pet-studio-demo-import',
			array( __CLASS__, 'render_admin_page' )
		);
	}

	public static function render_admin_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$imported = get_option( self::OPTION_IMPORTED, false );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Pet Studio Demo Import', 'pet-studio-elementor' ); ?></h1>
			<p><?php esc_html_e( 'Imports mirror media, creates Theme Builder header/footer, and builds all 6 pages with widget stacks pre-filled from fixtures.', 'pet-studio-elementor' ); ?></p>
			<?php if ( $imported ) : ?>
				<div class="notice notice-success"><p><?php esc_html_e( 'Demo content was imported. You can re-run to refresh pages (existing Pet Studio pages will be updated).', 'pet-studio-elementor' ); ?></p></div>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'pet_studio_import_demo' ); ?>
				<input type="hidden" name="action" value="pet_studio_import_demo">
				<?php submit_button( esc_html__( 'Import demo content', 'pet-studio-elementor' ), 'primary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	}

	public static function handle_import_request(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'pet-studio-elementor' ) );
		}
		check_admin_referer( 'pet_studio_import_demo' );

		$result = ( new self() )->import();

		$redirect = add_query_arg(
			array(
				'page'             => 'pet-studio-demo-import',
				'pet_studio_import' => $result['success'] ? '1' : '0',
				'message'          => rawurlencode( $result['message'] ),
			),
			admin_url( 'tools.php' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * @return array{success:bool,message:string}
	 */
	public function import(): array {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return array(
				'success' => false,
				'message' => 'Elementor is not active.',
			);
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$this->media_map = get_option( self::OPTION_MEDIA_MAP, array() );
		if ( ! is_array( $this->media_map ) ) {
			$this->media_map = array();
		}

		$media_result = $this->import_media();
		if ( ! $media_result['success'] ) {
			return $media_result;
		}

		update_option( self::OPTION_MEDIA_MAP, $this->media_map, false );

		$this->import_theme_templates();
		$this->import_pages();

		update_option( 'show_on_front', 'page', false );
		$home = get_page_by_path( 'home' );
		if ( $home ) {
			update_option( 'page_on_front', $home->ID, false );
		}

		update_option( self::OPTION_IMPORTED, time(), false );

		return array(
			'success' => true,
			'message' => sprintf(
				'Imported %d media files, theme templates, and 6 pages.',
				$media_result['count']
			),
		);
	}

	/**
	 * @return array{success:bool,message:string,count:int}
	 */
	private function import_media(): array {
		$manifest_path = PET_STUDIO_EW_PATH . 'fixtures/media-manifest.json';
		if ( ! is_readable( $manifest_path ) ) {
			return array(
				'success' => false,
				'message' => 'Media manifest not found.',
				'count'   => 0,
			);
		}

		$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );
		if ( empty( $manifest['files'] ) || ! is_array( $manifest['files'] ) ) {
			return array(
				'success' => false,
				'message' => 'Invalid media manifest.',
				'count'   => 0,
			);
		}

		$uploads  = wp_upload_dir();
		$base_dir = trailingslashit( $uploads['basedir'] ) . ( $manifest['base_upload_path'] ?? 'pet-studio/media' );
		$base_url = trailingslashit( $uploads['baseurl'] ) . ( $manifest['base_upload_path'] ?? 'pet-studio/media' );
		$count    = 0;

		foreach ( $manifest['files'] as $logical => $relative_source ) {
			$source = trailingslashit( $this->workspace_root ) . ltrim( $relative_source, '/' );
			if ( ! is_readable( $source ) ) {
				continue;
			}

			$target = trailingslashit( $base_dir ) . $logical;
			wp_mkdir_p( dirname( $target ) );

			if ( ! copy( $source, $target ) ) {
				continue;
			}

			$filetype = wp_check_filetype( basename( $target ), null );
			$attach_id = $this->find_existing_attachment( $target );

			if ( ! $attach_id ) {
				$attach_id = wp_insert_attachment(
					array(
						'post_mime_type' => $filetype['type'] ?: 'application/octet-stream',
						'post_title'     => sanitize_file_name( pathinfo( $target, PATHINFO_FILENAME ) ),
						'post_content'   => '',
						'post_status'    => 'inherit',
					),
					$target
				);
				if ( is_wp_error( $attach_id ) || ! $attach_id ) {
					continue;
				}
				if ( 0 === strpos( $filetype['type'] ?? '', 'image/' ) ) {
					wp_generate_attachment_metadata( $attach_id, $target );
				}
			}

			$this->media_map[ $logical ] = array(
				'id'  => (int) $attach_id,
				'url' => trailingslashit( $base_url ) . str_replace( '\\', '/', $logical ),
			);
			++$count;
		}

		return array(
			'success' => true,
			'message' => 'Media imported.',
			'count'   => $count,
		);
	}

	private function find_existing_attachment( string $file_path ): int {
		global $wpdb;
		$id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s LIMIT 1",
				'%' . $wpdb->esc_like( basename( $file_path ) )
			)
		);
		return $id;
	}

	private function import_theme_templates(): void {
		$config_path = PET_STUDIO_EW_PATH . 'fixtures/pages/theme-templates.json';
		if ( ! is_readable( $config_path ) ) {
			return;
		}
		$config = json_decode( (string) file_get_contents( $config_path ), true );
		foreach ( array( 'header', 'footer' ) as $type ) {
			if ( empty( $config[ $type ] ) ) {
				continue;
			}
			$this->create_theme_template( $type, $config[ $type ] );
		}
	}

	/**
	 * @param array<string, mixed> $config Template config.
	 */
	private function create_theme_template( string $type, array $config ): void {
		$title   = $config['title'] ?? ucfirst( $type );
		$widgets = $config['widgets'] ?? array();
		$existing = get_page_by_title( $title, OBJECT, 'elementor_library' );

		$post_id = $existing ? $existing->ID : 0;
		if ( ! $post_id ) {
			$post_id = wp_insert_post(
				array(
					'post_title'  => $title,
					'post_type'   => 'elementor_library',
					'post_status' => 'publish',
				)
			);
		}

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			return;
		}

		$data = $this->build_document_data( $widgets );

		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_template_type', $type );
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );

		$this->apply_theme_builder_assignment( $post_id, $type );
	}

	/**
	 * Register Theme Builder location + conditions via Elementor Pro API.
	 */
	private function apply_theme_builder_assignment( int $post_id, string $type ): void {
		update_post_meta( $post_id, '_elementor_location', $type );

		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) || ! class_exists( '\ElementorPro\Plugin' ) ) {
			update_post_meta( $post_id, '_elementor_conditions', array( 'include/general' ) );
			return;
		}

		$theme_builder = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
		if ( ! $theme_builder ) {
			update_post_meta( $post_id, '_elementor_conditions', array( 'include/general' ) );
			return;
		}

		$theme_builder->get_conditions_manager()->save_conditions(
			$post_id,
			array(
				array(
					'type' => 'include',
					'name' => 'general',
				),
			)
		);
	}

	/**
	 * Rebuild Theme Builder cache when templates exist but conditions were not registered.
	 */
	public static function maybe_repair_theme_builder(): void {
		if ( ! get_option( self::OPTION_IMPORTED, false ) ) {
			return;
		}

		$cache = get_option( 'elementor_pro_theme_builder_conditions', array() );
		if ( is_array( $cache ) && ! empty( $cache['header'] ) && ! empty( $cache['footer'] ) ) {
			return;
		}

		$importer = new self();
		foreach ( array( 'Pet Studio Header' => 'header', 'Pet Studio Footer' => 'footer' ) as $title => $type ) {
			$post = get_page_by_title( $title, OBJECT, 'elementor_library' );
			if ( $post ) {
				$importer->apply_theme_builder_assignment( (int) $post->ID, $type );
			}
		}
	}

	private function import_pages(): void {
		$page_files = glob( PET_STUDIO_EW_PATH . 'fixtures/pages/*.json' ) ?: array();
		foreach ( $page_files as $file ) {
			if ( false !== strpos( $file, 'theme-templates.json' ) ) {
				continue;
			}
			$config = json_decode( (string) file_get_contents( $file ), true );
			if ( empty( $config['slug'] ) ) {
				continue;
			}
			$this->create_page( $config );
		}
	}

	/**
	 * @param array<string, mixed> $config Page config.
	 */
	private function create_page( array $config ): void {
		$slug    = $config['slug'];
		$title   = $config['title'] ?? ucfirst( str_replace( '-', ' ', $slug ) );
		$widgets = $config['widgets'] ?? array();
		$page    = get_page_by_path( $slug );

		$post_id = $page ? $page->ID : 0;
		if ( ! $post_id ) {
			$post_id = wp_insert_post(
				array(
					'post_title'  => $title,
					'post_name'   => $slug,
					'post_type'   => 'page',
					'post_status' => 'publish',
				)
			);
		}

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			return;
		}

		$data = $this->build_document_data( $widgets );

		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $post_id, '_wp_page_template', 'elementor_header_footer' );
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
	}

	/**
	 * @param array<int, string> $widget_slugs Fixture slugs.
	 * @return array<int, array<string, mixed>>
	 */
	private function build_document_data( array $widget_entries ): array {
		$sections = array();
		foreach ( $widget_entries as $entry ) {
			$parsed = $this->parse_widget_entry( $entry );
			if ( empty( $parsed['slug'] ) ) {
				continue;
			}
			$sections[] = $this->build_section(
				array( $this->build_widget_element( $parsed['slug'], $parsed['variant'] ) )
			);
		}

		return $sections;
	}

	/**
	 * @param string|array<string, string> $entry Widget slug or { slug, variant }.
	 * @return array{slug:string,variant:string}
	 */
	private function parse_widget_entry( $entry ): array {
		if ( is_string( $entry ) ) {
			return array(
				'slug'    => $entry,
				'variant' => '',
			);
		}

		if ( ! is_array( $entry ) ) {
			return array( 'slug' => '', 'variant' => '' );
		}

		return array(
			'slug'    => (string) ( $entry['slug'] ?? '' ),
			'variant' => (string) ( $entry['variant'] ?? '' ),
		);
	}

	/**
	 * @param array<int, array<string, mixed>> $widgets Widget elements.
	 */
	private function build_section( array $widgets ): array {
		$zero_spacing = array(
			'unit'     => 'px',
			'top'      => '0',
			'right'    => '0',
			'bottom'   => '0',
			'left'     => '0',
			'isLinked' => true,
		);

		return array(
			'id'       => $this->gen_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'  => 'full_width',
				'gap'     => 'no',
				'padding' => $zero_spacing,
				'margin'  => $zero_spacing,
			),
			'elements' => array(
				array(
					'id'       => $this->gen_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 100,
						'padding'      => $zero_spacing,
						'margin'       => $zero_spacing,
					),
					'elements' => $widgets,
				),
			),
		);
	}

	private function build_widget_element( string $fixture_slug, string $variant = '' ): array {
		$widget_name = Content_Normalizer::WIDGET_MAP[ $fixture_slug ] ?? '';
		$settings    = $this->fixture_settings_for_elementor( $fixture_slug, $variant );

		return array(
			'id'         => $this->gen_id(),
			'elType'     => 'widget',
			'widgetType' => $widget_name,
			'settings'   => $settings,
			'elements'   => array(),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private function fixture_settings_for_elementor( string $fixture_slug, string $variant = '' ): array {
		$fixture  = $variant ? Content_Normalizer::get_page_block( $variant ) : array();
		$settings = Content_Normalizer::to_elementor_settings( $fixture_slug, $fixture );
		$settings = $this->resolve_media_in_settings( $settings );
		$settings = $this->add_repeater_ids( $settings );
		return $settings;
	}

	/**
	 * @param array<string, mixed> $settings Settings tree.
	 * @return array<string, mixed>
	 */
	private function resolve_media_in_settings( array $settings ): array {
		foreach ( $settings as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( $this->looks_like_media( $value ) ) {
					$settings[ $key ] = $this->resolve_media_control( $value );
				} else {
					$settings[ $key ] = $this->resolve_media_in_settings( $value );
				}
			}
		}
		return $settings;
	}

	/**
	 * @param array<string, mixed> $value Media-like array.
	 */
	private function looks_like_media( array $value ): bool {
		return array_key_exists( 'url', $value ) && ( array_key_exists( 'id', $value ) || 1 === count( $value ) );
	}

	/**
	 * @param array<string, mixed> $media Media control.
	 * @return array<string, mixed>
	 */
	private function resolve_media_control( array $media ): array {
		$logical = $this->url_to_logical_path( (string) ( $media['url'] ?? '' ) );
		if ( $logical && isset( $this->media_map[ $logical ] ) ) {
			return array(
				'id'  => (string) $this->media_map[ $logical ]['id'],
				'url' => $this->media_map[ $logical ]['url'],
			);
		}
		return array(
			'id'  => isset( $media['id'] ) ? (string) $media['id'] : '',
			'url' => (string) ( $media['url'] ?? '' ),
		);
	}

	private function url_to_logical_path( string $url ): string {
		$needle = '/pet-studio/media/';
		$pos    = strpos( $url, $needle );
		if ( false === $pos ) {
			return '';
		}
		return ltrim( substr( $url, $pos + strlen( $needle ) ), '/' );
	}

	/**
	 * @param array<string, mixed> $settings Settings.
	 * @return array<string, mixed>
	 */
	private function add_repeater_ids( array $settings ): array {
		foreach ( $settings as $key => $value ) {
			if ( ! is_array( $value ) ) {
				continue;
			}
			if ( $this->is_list_array( $value ) ) {
				$settings[ $key ] = array_map(
					function ( $item ) {
						if ( ! is_array( $item ) ) {
							return $item;
						}
						$item['_id'] = $this->gen_id();
						return $this->add_repeater_ids( $item );
					},
					$value
				);
			} else {
				$settings[ $key ] = $this->add_repeater_ids( $value );
			}
		}
		return $settings;
	}

	/**
	 * @param array<mixed> $array Array.
	 */
	private function is_list_array( array $array ): bool {
		if ( array() === $array ) {
			return true;
		}
		return array_keys( $array ) === range( 0, count( $array ) - 1 );
	}

	private function gen_id(): string {
		return substr( uniqid(), -7 );
	}
}
