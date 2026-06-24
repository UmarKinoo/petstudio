<?php
/**
 * Courses tabs — uk-switcher course panels.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\media_url;
use function Pet_Studio_Elementor\render_rich_text;

defined( 'ABSPATH' ) || exit;

class Courses_Tabs_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_courses_tabs';
	}

	public function get_title(): string {
		return esc_html__( 'Courses Tabs', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-tabs';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'courses', 'tabs', 'academy' );
	}

	protected function get_fixture_slug(): string {
		return 'courses-tabs';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'section_heading', array( 'label' => esc_html__( 'Section heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => 'Courses Available' ) );

		$tab_rep = new Repeater();
		$tab_rep->add_control( 'tab_label', array( 'label' => esc_html__( 'Tab label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$tab_rep->add_control( 'badge_image', array( 'label' => esc_html__( 'Badge', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => '' ) ) );
		$tab_rep->add_control( 'title', array( 'label' => esc_html__( 'Title', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$tab_rep->add_control( 'duration_meta', array( 'label' => esc_html__( 'Duration', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$tab_rep->add_control( 'content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'type' => Controls_Manager::WYSIWYG, 'default' => '' ) );
		$tab_rep->add_control(
			'features_list',
			array(
				'label'       => esc_html__( 'Features (one per line)', 'pet-studio-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'rows'        => 6,
			)
		);

		$tabs_default = array();
		foreach ( $d['tabs'] ?? array() as $tab ) {
			$lines = array();
			foreach ( $tab['features'] ?? array() as $feat ) {
				$line = is_string( $feat ) ? $feat : ( $feat['item'] ?? '' );
				if ( '' !== $line ) {
					$lines[] = $line;
				}
			}
			$tabs_default[] = array(
				'tab_label'     => $tab['tab_label'] ?? '',
				'badge_image'   => api_media_to_control( $tab['badge_image'] ?? null ),
				'title'         => $tab['title'] ?? '',
				'duration_meta' => $tab['duration_meta'] ?? '',
				'content'       => $tab['content'] ?? '',
				'features_list' => implode( "\n", $lines ),
			);
		}

		$this->add_control( 'tabs', array( 'label' => esc_html__( 'Tabs', 'pet-studio-elementor' ), 'type' => Controls_Manager::REPEATER, 'fields' => $tab_rep->get_controls(), 'default' => $tabs_default ) );
		$this->add_control( 'default_active_tab', array( 'label' => esc_html__( 'Default active tab (0-based)', 'pet-studio-elementor' ), 'type' => Controls_Manager::NUMBER, 'default' => (int) ( $d['default_active_tab'] ?? 0 ), 'min' => 0 ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .el-title' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$tabs = $s['tabs'] ?? array();
		if ( empty( $tabs ) ) {
			return;
		}
		$switcher_id = 'ps-courses-' . $this->get_id();
		$active      = (int) ( $s['default_active_tab'] ?? 0 );
		?>
		<div id="ps-courses-available" class="uk-section-secondary uk-section uk-section-large">
			<div class="uk-container">
				<div class="uk-grid-margin uk-container uk-container-small">
					<div class="uk-grid tm-grid-expand uk-child-width-1-1">
						<div class="uk-width-1-1">
							<?php if ( ! empty( $s['section_heading'] ) ) : ?>
								<h1 class="uk-heading-medium uk-text-success uk-margin-large"><?php echo esc_html( $s['section_heading'] ); ?></h1>
							<?php endif; ?>
							<div class="uk-margin">
								<ul class="el-nav uk-margin-medium uk-subnav uk-subnav-pill" uk-switcher="connect: #<?php echo esc_attr( $switcher_id ); ?>; animation: uk-animation-scale-up;">
									<?php foreach ( $tabs as $i => $tab ) : ?>
										<li<?php echo $i === $active ? ' class="uk-active"' : ''; ?>>
											<a href="#"><?php echo esc_html( $tab['tab_label'] ?? '' ); ?></a>
										</li>
									<?php endforeach; ?>
								</ul>
								<div id="<?php echo esc_attr( $switcher_id ); ?>" class="uk-switcher">
									<?php foreach ( $tabs as $tab ) : ?>
										<?php $badge = media_url( $tab['badge_image'] ?? null ); ?>
										<div class="el-item uk-margin-remove-first-child">
											<?php if ( $badge ) : ?>
												<img class="el-image" src="<?php echo esc_url( $badge ); ?>" alt="" loading="lazy" width="150">
											<?php endif; ?>
											<?php if ( ! empty( $tab['title'] ) ) : ?>
												<h3 class="el-title uk-heading-small uk-heading-divider uk-margin-top uk-margin-remove-bottom">
													<span class="uk-text-background"><?php echo esc_html( $tab['title'] ); ?></span>
												</h3>
											<?php endif; ?>
											<?php if ( ! empty( $tab['duration_meta'] ) ) : ?>
												<div class="el-meta uk-h2 uk-margin-top uk-margin-remove-bottom"><?php echo esc_html( $tab['duration_meta'] ); ?></div>
											<?php endif; ?>
											<div class="el-content uk-panel uk-column-1-2@m uk-column-divider uk-margin-top">
												<?php if ( ! empty( $tab['content'] ) ) : ?>
													<?php render_rich_text( $tab['content'] ); ?>
												<?php endif; ?>
												<?php
												$feature_lines = self::parse_feature_lines( $tab );
												if ( ! empty( $feature_lines ) ) :
													?>
													<p><strong><?php esc_html_e( 'Our course content includes inputs on:', 'pet-studio-elementor' ); ?></strong></p>
													<ul class="uk-column-span">
														<?php foreach ( $feature_lines as $feat ) : ?>
															<li><?php echo esc_html( $feat ); ?></li>
														<?php endforeach; ?>
													</ul>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @param array<string, mixed> $tab Tab settings.
	 * @return array<int, string>
	 */
	private static function parse_feature_lines( array $tab ): array {
		if ( ! empty( $tab['features_list'] ) && is_string( $tab['features_list'] ) ) {
			$lines = preg_split( '/\r\n|\r|\n/', $tab['features_list'] ) ?: array();
			return array_values(
				array_filter(
					array_map( 'trim', $lines ),
					static function ( string $line ): bool {
						return '' !== $line;
					}
				)
			);
		}

		if ( empty( $tab['features'] ) || ! is_array( $tab['features'] ) ) {
			return array();
		}

		$lines = array();
		foreach ( $tab['features'] as $feat ) {
			$line = is_string( $feat ) ? $feat : ( $feat['item'] ?? '' );
			if ( '' !== $line ) {
				$lines[] = $line;
			}
		}

		return $lines;
	}
}
