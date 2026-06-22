<?php
/**
 * Dog icon divider with parallax.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\media_url;

defined( 'ABSPATH' ) || exit;

class Dog_Divider_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_dog_divider';
	}

	public function get_title(): string {
		return esc_html__( 'Dog Divider', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-divider';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'divider', 'dog', 'icon' );
	}

	protected function get_fixture_slug(): string {
		return 'dog-divider';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'icon_image', array( 'label' => esc_html__( 'Icon image', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['icon_image'] ?? null ) ) );
		$this->add_control( 'parallax_x', array( 'label' => esc_html__( 'Parallax expression', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['parallax_x'] ?? 'x: 50vw; easing: 0' ) );
		$this->add_control( 'offset_right', array( 'label' => esc_html__( 'Right offset (px)', 'pet-studio-elementor' ), 'type' => Controls_Manager::NUMBER, 'default' => 30 ) );
		$this->add_control( 'show_on_mobile', array( 'label' => esc_html__( 'Show on mobile', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['show_on_mobile'] ) ? 'yes' : '' ) );
		$this->end_controls_section();
		$this->register_style_controls();
	}

	protected function render(): void {
		$s    = $this->get_render_settings();
		$icon = media_url( $s['icon_image'] ?? null );
		if ( ! $icon ) {
			return;
		}
		$parallax = esc_attr( $s['parallax_x'] ?? 'x: 50vw; easing: 0' );
		$right    = (int) ( $s['offset_right'] ?? 30 );
		$mobile   = ( $s['show_on_mobile'] ?? '' ) === 'yes' ? '' : ' uk-visible@s';
		?>
		<div class="uk-grid tm-grid-expand uk-child-width-1-1 uk-margin-xlarge-top uk-margin-remove-bottom">
			<div class="uk-width-1-1<?php echo esc_attr( $mobile ); ?>">
				<div class="uk-position-relative uk-margin" uk-parallax="<?php echo $parallax; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" style="right: <?php echo esc_attr( (string) $right ); ?>px;" uk-scrollspy="target: [uk-scrollspy-class];">
					<img class="el-image" src="<?php echo esc_url( $icon ); ?>" alt="" loading="lazy" width="105" height="98">
				</div>
			</div>
		</div>
		<?php
	}
}
