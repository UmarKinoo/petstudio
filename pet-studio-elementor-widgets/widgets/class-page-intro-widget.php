<?php
/**
 * Page intro — heading, blockquote, image column.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\eager_media_attrs;
use function Pet_Studio_Elementor\lazy_load_exempt_class;
use function Pet_Studio_Elementor\media_url;
use function Pet_Studio_Elementor\render_inline_svg;
use function Pet_Studio_Elementor\render_rich_text;

defined( 'ABSPATH' ) || exit;

class Page_Intro_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_page_intro';
	}

	public function get_title(): string {
		return esc_html__( 'Page Intro', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-posts-ticker';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'intro', 'page' );
	}

	protected function get_fixture_slug(): string {
		return 'page-intro';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'heading', array( 'label' => esc_html__( 'Heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading'] ?? '' ) );
		$this->add_control( 'heading_accent', array( 'label' => esc_html__( 'Heading accent', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading_accent'] ?? '' ) );
		$this->add_control( 'blockquote', array( 'label' => esc_html__( 'Blockquote', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['blockquote'] ?? '', 'rows' => 3 ) );
		$this->add_control( 'body', array( 'label' => esc_html__( 'Body', 'pet-studio-elementor' ), 'type' => Controls_Manager::WYSIWYG, 'default' => $d['body'] ?? '' ) );
		$this->add_control( 'show_signature', array( 'label' => esc_html__( 'Show signature', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['show_signature'] ) ? 'yes' : '' ) );
		$this->add_control( 'signature_image', array( 'label' => esc_html__( 'Signature', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['signature_image'] ?? null ), 'condition' => array( 'show_signature' => 'yes' ) ) );
		$this->add_control( 'primary_image', array( 'label' => esc_html__( 'Primary image', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['primary_image'] ?? null ) ) );
		$this->add_control( 'secondary_image', array( 'label' => esc_html__( 'Secondary image (right column)', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['secondary_image'] ?? null ) ) );
		$this->add_control( 'badge_image', array( 'label' => esc_html__( 'Badge image (below text)', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['badge_image'] ?? null ) ) );
		$this->add_control( 'left_inset_image', array( 'label' => esc_html__( 'Inset image (below text, left column)', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['left_inset_image'] ?? null ) ) );
		$this->add_control( 'reverse_columns', array( 'label' => esc_html__( 'Reverse columns', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .el-title' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$text_col = ( $s['reverse_columns'] ?? '' ) === 'yes' ? 'uk-flex-first@m' : '';
		$img_col  = ( $s['reverse_columns'] ?? '' ) === 'yes' ? '' : '';
		$sig_url   = media_url( $s['signature_image'] ?? null );
		$img_url   = media_url( $s['primary_image'] ?? null );
		$img2_url  = media_url( $s['secondary_image'] ?? null );
		$badge_url = media_url( $s['badge_image'] ?? null );
		$inset_url = media_url( $s['left_inset_image'] ?? null );
		?>
		<div class="uk-section-default uk-section uk-section-small-top uk-padding-remove-bottom">
			<div class="uk-container uk-container-expand">
				<div class="uk-grid-margin uk-container uk-container-expand">
					<div class="uk-grid tm-grid-expand" uk-grid>
						<div class="uk-grid-item-match uk-flex-middle uk-width-1-2@m <?php echo esc_attr( $text_col ); ?>">
							<div class="uk-panel uk-width-1-1">
								<div class="uk-panel uk-margin-remove-first-child uk-margin uk-width-xlarge uk-margin-auto uk-text-left" uk-scrollspy="target: [uk-scrollspy-class];">
									<h2 class="el-title uk-heading-large uk-margin-top uk-margin-remove-bottom">
										<?php echo esc_html( $s['heading'] ?? '' ); ?>
										<?php if ( ! empty( $s['heading_accent'] ) ) : ?>
											<span class="uk-text-primary"><?php echo esc_html( $s['heading_accent'] ); ?></span>
										<?php endif; ?>
									</h2>
									<div class="el-content uk-panel uk-margin-large-top">
										<?php if ( ! empty( $s['blockquote'] ) ) : ?>
											<blockquote><p><strong><?php echo esc_html( $s['blockquote'] ); ?></strong></p></blockquote>
										<?php endif; ?>
										<?php if ( ! empty( $s['body'] ) ) : ?>
											<?php render_rich_text( $s['body'] ); ?>
										<?php endif; ?>
									</div>
									<?php if ( ( $s['show_signature'] ?? '' ) === 'yes' && $sig_url ) : ?>
										<div class="uk-margin-medium-top ps-signature-inline">
											<?php
											if ( ! render_inline_svg( $sig_url, 'uk-text-primary el-image ps-signature-svg', 300, 156 ) ) :
												?>
												<img class="<?php echo esc_attr( lazy_load_exempt_class( 'uk-text-primary el-image' ) ); ?>" src="<?php echo esc_url( $sig_url ); ?>" alt="" width="300" height="156"<?php echo eager_media_attrs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> uk-svg>
											<?php endif; ?>
										</div>
									<?php endif; ?>
									<?php if ( $badge_url ) : ?>
										<img class="el-image uk-margin-medium-top" src="<?php echo esc_url( $badge_url ); ?>" alt="" loading="lazy" width="180" height="130">
									<?php endif; ?>
								</div>
								<?php if ( $inset_url ) : ?>
									<div class="uk-margin-xlarge uk-text-center" uk-parallax="opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @l">
										<img class="el-image" src="<?php echo esc_url( $inset_url ); ?>" alt="" loading="lazy">
									</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="uk-width-1-2@m <?php echo esc_attr( $img_col ); ?>">
							<?php if ( $img_url ) : ?>
								<div class="uk-margin" uk-parallax="y: 100,-150; opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @l">
									<img class="el-image" src="<?php echo esc_url( $img_url ); ?>" alt="" loading="lazy" width="900" height="1200">
								</div>
							<?php endif; ?>
							<?php if ( $img2_url ) : ?>
								<div class="uk-margin-xlarge uk-text-center" uk-parallax="opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @l">
									<img class="el-image" src="<?php echo esc_url( $img2_url ); ?>" alt="" loading="lazy">
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
