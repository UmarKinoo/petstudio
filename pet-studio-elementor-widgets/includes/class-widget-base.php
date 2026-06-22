<?php
/**
 * Base class for Pet Studio Elementor widgets.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor;

use Elementor\Widget_Base as Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

abstract class Widget_Base extends Elementor_Widget_Base {

	public function get_categories(): array {
		return array( 'pet-studio' );
	}

	public function get_style_depends(): array {
		return Assets::widget_style_handles();
	}

	public function get_script_depends(): array {
		return Assets::widget_script_handles();
	}

	/**
	 * Fixture slug matching fixtures/widgets/{slug}.json and schemas/{slug}.json.
	 */
	abstract protected function get_fixture_slug(): string;

	/**
	 * Load API-shaped defaults from fixtures.
	 */
	protected function get_fixture_defaults(): array {
		return Content_Normalizer::get_control_defaults( $this->get_fixture_slug() );
	}

	/**
	 * Settings normalized for render (Elementor + future API).
	 * Empty panel values fall back to fixture defaults from the mirror export.
	 */
	protected function get_render_settings(): array {
		$settings = $this->get_settings_for_display();
		$defaults = $this->get_fixture_defaults();
		$merged   = Content_Normalizer::merge_settings( $defaults, $settings );

		return Content_Normalizer::normalize(
			$this->get_name(),
			$merged
		);
	}

	/**
	 * Pet Studio widgets read fixture defaults at render time — skip Elementor HTML cache.
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_style_controls( string $section_id = 'section_style' ): void {
		$this->start_controls_section(
			$section_id,
			array(
				'label' => esc_html__( 'Style', 'pet-studio-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'Accent colour', 'pet-studio-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FF90AA',
				'selectors' => array(
					'{{WRAPPER}} .uk-text-primary' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}
}
