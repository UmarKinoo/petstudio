<?php
/**
 * Est. banner — parallax heading line.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Pet_Studio_Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

class Est_Banner_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_est_banner';
	}

	public function get_title(): string {
		return esc_html__( 'Est. Banner', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-heading';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'established', 'banner' );
	}

	protected function get_fixture_slug(): string {
		return 'est-banner';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'text', array( 'label' => esc_html__( 'Text', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['text'] ?? 'Est. 2000' ) );
		$this->add_control( 'parallax_expression', array( 'label' => esc_html__( 'Parallax', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['parallax_expression'] ?? 'x: -20vw,20vw; easing: 0' ) );
		$this->add_control( 'offset_right', array( 'label' => esc_html__( 'Right offset (px)', 'pet-studio-elementor' ), 'type' => Controls_Manager::NUMBER, 'default' => 50 ) );
		$this->add_control( 'hide_on_mobile', array( 'label' => esc_html__( 'Hide on mobile', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['hide_on_mobile'] ) ? 'yes' : '' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'text_typography', 'selector' => '{{WRAPPER}} .uk-heading-small' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$mobile_cls = ( $s['hide_on_mobile'] ?? '' ) === 'yes' ? ' uk-visible@s' : '';
		?>
		<div class="uk-section-default uk-section">
			<div class="uk-container uk-container-expand">
				<div class="uk-grid tm-grid-expand uk-child-width-1-1 uk-margin-remove-bottom">
					<div class="uk-width-1-1<?php echo esc_attr( $mobile_cls ); ?>">
						<div class="uk-heading-small uk-heading-line uk-text-primary uk-position-relative uk-text-right" uk-parallax="<?php echo esc_attr( $s['parallax_expression'] ?? '' ); ?>" style="right: <?php echo esc_attr( (string) (int) ( $s['offset_right'] ?? 50 ) ); ?>px;">
							<span><?php echo esc_html( $s['text'] ?? '' ); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
