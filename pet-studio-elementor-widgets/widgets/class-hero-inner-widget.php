<?php
/**
 * Hero — Inner page (full-viewport video).
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\media_url;

defined( 'ABSPATH' ) || exit;

class Hero_Inner_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_hero_inner';
	}

	public function get_title(): string {
		return esc_html__( 'Hero — Inner', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-video-camera';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'hero', 'inner', 'video' );
	}

	protected function get_fixture_slug(): string {
		return 'hero-inner';
	}

	protected function register_controls(): void {
		$defaults = $this->get_fixture_defaults();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'video',
			array(
				'label'   => esc_html__( 'Background video', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'default' => api_media_to_control( $defaults['video'] ?? null ),
			)
		);

		$this->add_control(
			'viewport_offset',
			array(
				'label'   => esc_html__( 'Viewport bottom offset (px)', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (int) ( $defaults['viewport_offset'] ?? 83 ),
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_opacity',
			array(
				'label'   => esc_html__( 'Overlay opacity', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'default' => array( 'size' => 0 ),
				'selectors' => array(
					'{{WRAPPER}} .ps-hero-inner-overlay' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => esc_html__( 'Overlay colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .ps-hero-inner-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s       = $this->get_render_settings();
		$video   = media_url( $s['video'] ?? null );
		$offset  = (int) ( $s['viewport_offset'] ?? 83 );
		?>
		<div class="uk-section-primary uk-inverse-light uk-section uk-padding-remove-vertical" tm-header-transparent-noplaceholder>
			<div class="uk-grid tm-grid-expand uk-child-width-1-1">
				<div class="uk-width-1-1">
					<div class="uk-position-z-index uk-tile uk-padding-remove" uk-height-viewport="offset-top: !*; offset-bottom: <?php echo esc_attr( (string) $offset ); ?>;" uk-sticky="end: !.uk-section;">
						<?php if ( $video ) : ?>
							<video src="<?php echo esc_url( $video ); ?>" playsinline loop muted preload="none" width="2560" class="uk-object-center-left" uk-cover></video>
						<?php endif; ?>
						<div class="uk-position-cover ps-hero-inner-overlay"></div>
						<div class="uk-panel uk-width-1-1"></div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
