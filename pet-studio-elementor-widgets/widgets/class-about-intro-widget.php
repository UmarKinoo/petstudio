<?php
/**
 * About intro — home team section.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_link_to_control;
use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\eager_media_attrs;
use function Pet_Studio_Elementor\lazy_load_exempt_class;
use function Pet_Studio_Elementor\media_url;
use function Pet_Studio_Elementor\print_link_attributes;
use function Pet_Studio_Elementor\render_rich_text;

defined( 'ABSPATH' ) || exit;

class About_Intro_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_about_intro';
	}

	public function get_title(): string {
		return esc_html__( 'About Intro', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-info-box';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'about', 'team' );
	}

	protected function get_fixture_slug(): string {
		return 'about-intro';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'heading', array( 'label' => esc_html__( 'Heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading'] ?? '' ) );
		$this->add_control( 'body', array( 'label' => esc_html__( 'Body', 'pet-studio-elementor' ), 'type' => Controls_Manager::WYSIWYG, 'default' => $d['body'] ?? '' ) );
		$this->add_control( 'cta_text', array( 'label' => esc_html__( 'CTA text', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['cta_text'] ?? '' ) );
		$this->add_control( 'cta_link', array( 'label' => esc_html__( 'CTA link', 'pet-studio-elementor' ), 'type' => Controls_Manager::URL, 'default' => api_link_to_control( $d['cta_link'] ?? null ) ) );
		$this->add_control( 'badge_image', array( 'label' => esc_html__( 'Badge image', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['badge_image'] ?? null ) ) );
		$this->add_control( 'main_image', array( 'label' => esc_html__( 'Main image', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['main_image'] ?? null ) ) );
		$this->add_control( 'show_signature', array( 'label' => esc_html__( 'Show signature overlay', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['show_signature'] ) ? 'yes' : '' ) );
		$this->add_control( 'signature_image', array( 'label' => esc_html__( 'Signature', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['signature_image'] ?? null ), 'condition' => array( 'show_signature' => 'yes' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .el-title' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$badge = media_url( $s['badge_image'] ?? null );
		$main  = media_url( $s['main_image'] ?? null );
		$sig   = media_url( $s['signature_image'] ?? null );
		?>
		<div class="uk-section-default uk-section uk-section-large">
			<div class="uk-container">
				<div class="uk-grid-margin uk-grid tm-grid-expand" uk-grid>
					<div class="uk-grid-item-match uk-flex-middle uk-width-1-2@s">
						<div class="uk-panel uk-width-1-1">
							<div class="uk-panel uk-margin-remove-first-child uk-position-relative uk-margin uk-width-large" style="z-index: 1;" uk-scrollspy="target: [uk-scrollspy-class];">
								<h2 class="el-title uk-heading-medium uk-margin-top uk-margin-remove-bottom"><?php echo esc_html( $s['heading'] ?? '' ); ?></h2>
								<?php if ( ! empty( $s['body'] ) ) : ?>
									<div class="el-content uk-panel uk-text-large uk-margin-medium-top"><?php render_rich_text( $s['body'] ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $s['cta_text'] ) ) : ?>
									<div class="uk-margin-large-top">
										<a<?php print_link_attributes( $s['cta_link'] ?? null ); ?> class="el-link uk-button uk-button-text"><?php echo esc_html( $s['cta_text'] ); ?></a>
									</div>
								<?php endif; ?>
							</div>
							<?php if ( $badge ) : ?>
								<div class="uk-margin">
									<img class="el-image" src="<?php echo esc_url( $badge ); ?>" alt="" loading="lazy" width="150" height="108">
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="uk-width-1-2@s">
						<?php if ( $main ) : ?>
							<div class="uk-margin" uk-parallax="y: 100,-100; opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @s; start: 50vh">
								<img class="el-image" src="<?php echo esc_url( $main ); ?>" alt="" loading="lazy" width="580" height="811">
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( ( $s['show_signature'] ?? '' ) === 'yes' && $sig ) : ?>
					<div class="uk-grid-margin uk-grid tm-grid-expand uk-child-width-1-1">
						<div class="uk-grid-item-match uk-width-1-1">
							<div class="uk-panel uk-width-1-1">
								<div class="uk-position-absolute uk-width-1-1 uk-text-center" uk-parallax="opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @s" style="right: -30px; bottom: -6vh;" uk-scrollspy="target: [uk-scrollspy-class];">
									<img class="<?php echo esc_attr( lazy_load_exempt_class( 'el-image uk-text-primary' ) ); ?>" src="<?php echo esc_url( $sig ); ?>" alt="" width="600" height="312"<?php echo eager_media_attrs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> uk-svg="stroke-animation: true; attributes: uk-scrollspy-class:uk-animation-stroke">
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
