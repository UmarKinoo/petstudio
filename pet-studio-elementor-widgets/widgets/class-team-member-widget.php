<?php
/**
 * Team member — bio + portrait column.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Pet_Studio_Elementor\Widget_Base;
use Pet_Studio_Elementor\Content_Normalizer;

use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\eager_media_attrs;
use function Pet_Studio_Elementor\lazy_load_exempt_class;
use function Pet_Studio_Elementor\media_url;
use function Pet_Studio_Elementor\render_inline_svg;
use function Pet_Studio_Elementor\render_rich_text;

defined( 'ABSPATH' ) || exit;

class Team_Member_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_team_member';
	}

	public function get_title(): string {
		return esc_html__( 'Team Member', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-person';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'team', 'member', 'bio' );
	}

	protected function get_fixture_slug(): string {
		return 'team-member';
	}

	/**
	 * Each team profile is a distinct instance — do not backfill empty fields from Liza defaults.
	 */
	protected function get_render_settings(): array {
		$settings = $this->get_settings_for_display();

		return Content_Normalizer::normalize( $this->get_name(), $settings );
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'name_line_1', array( 'label' => esc_html__( 'Name line 1', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['name_line_1'] ?? '' ) );
		$this->add_control( 'name_accent', array( 'label' => esc_html__( 'Name accent', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['name_accent'] ?? '' ) );
		$this->add_control( 'role', array( 'label' => esc_html__( 'Role', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['role'] ?? '' ) );
		$this->add_control( 'bio', array( 'label' => esc_html__( 'Bio', 'pet-studio-elementor' ), 'type' => Controls_Manager::WYSIWYG, 'default' => $d['bio'] ?? '' ) );
		$this->add_control( 'portrait', array( 'label' => esc_html__( 'Portrait', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['portrait'] ?? null ) ) );
		$this->add_control( 'show_signature', array( 'label' => esc_html__( 'Show signature', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['show_signature'] ) ? 'yes' : '' ) );
		$this->add_control( 'signature_image', array( 'label' => esc_html__( 'Signature', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['signature_image'] ?? null ), 'condition' => array( 'show_signature' => 'yes' ) ) );
		$this->add_control( 'reverse_columns', array( 'label' => esc_html__( 'Reverse columns', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['reverse_columns'] ) ? 'yes' : '' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'name_typography', 'selector' => '{{WRAPPER}} .el-title' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$portrait = media_url( $s['portrait'] ?? null );
		$sig      = media_url( $s['signature_image'] ?? null );
		$reverse  = ( $s['reverse_columns'] ?? '' ) === 'yes';
		?>
		<div class="uk-section-default uk-section">
			<div class="uk-container uk-container-expand">
				<div class="uk-grid-margin uk-container uk-container-expand">
					<div class="uk-grid tm-grid-expand" uk-grid>
						<?php if ( ! $reverse ) : ?>
							<?php $this->render_bio( $s, $sig ); ?>
							<?php $this->render_portrait( $portrait ); ?>
						<?php else : ?>
							<?php $this->render_portrait( $portrait ); ?>
							<?php $this->render_bio( $s, $sig ); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @param array<string, mixed> $s Settings.
	 */
	private function render_bio( array $s, string $sig ): void {
		?>
		<div class="uk-grid-item-match uk-flex-middle uk-width-1-2@m">
			<div class="uk-panel uk-width-1-1">
				<div class="uk-panel uk-margin-remove-first-child uk-margin uk-width-xlarge uk-margin-auto uk-text-left" uk-scrollspy="target: [uk-scrollspy-class];">
					<h2 class="el-title uk-heading-large uk-margin-top uk-margin-remove-bottom">
						<?php echo esc_html( $s['name_line_1'] ?? '' ); ?>
						<?php if ( ! empty( $s['name_accent'] ) ) : ?>
							<span class="uk-text-primary"><br><?php echo esc_html( $s['name_accent'] ); ?></span>
						<?php endif; ?>
					</h2>
					<?php if ( ! empty( $s['role'] ) ) : ?>
						<div class="el-meta uk-text-meta uk-margin-top"><?php echo esc_html( $s['role'] ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $s['bio'] ) ) : ?>
						<div class="el-content uk-panel uk-margin-large-top"><?php render_rich_text( $s['bio'] ); ?></div>
					<?php endif; ?>
					<?php if ( ( $s['show_signature'] ?? '' ) === 'yes' && $sig ) : ?>
						<div class="uk-margin-medium-top ps-signature-inline">
							<?php
							if ( ! render_inline_svg( $sig, 'uk-text-primary el-image ps-signature-svg', 350, 182 ) ) :
								?>
								<img class="<?php echo esc_attr( lazy_load_exempt_class( 'uk-text-primary el-image' ) ); ?>" src="<?php echo esc_url( $sig ); ?>" alt="" width="350" height="182"<?php echo eager_media_attrs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> uk-svg>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	private function render_portrait( string $portrait ): void {
		if ( ! $portrait ) {
			return;
		}
		?>
		<div class="uk-width-1-2@m">
			<div class="uk-margin" uk-parallax="y: 100,-150; opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @l">
				<img class="el-image" src="<?php echo esc_url( $portrait ); ?>" alt="" loading="lazy" width="600" height="900">
			</div>
		</div>
		<?php
	}
}
