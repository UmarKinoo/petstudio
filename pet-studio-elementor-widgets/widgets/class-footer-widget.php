<?php
/**
 * Site footer — mirror markup.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_link_to_control;
use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\format_multiline_text;
use function Pet_Studio_Elementor\media_url;
use function Pet_Studio_Elementor\phone_tel_href;
use function Pet_Studio_Elementor\print_link_attributes;

defined( 'ABSPATH' ) || exit;

class Footer_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_footer';
	}

	public function get_title(): string {
		return esc_html__( 'Pet Studio Footer', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-footer';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'footer', 'contact' );
	}

	protected function get_fixture_slug(): string {
		return 'footer';
	}

	protected function register_controls(): void {
		$defaults = $this->get_fixture_defaults();

		$this->start_controls_section(
			'section_contact',
			array(
				'label' => esc_html__( 'Contact strip', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'contact_heading',
			array(
				'label'   => esc_html__( 'Contact heading', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['contact_heading'] ?? 'Contact Us',
			)
		);

		$this->add_control(
			'contact_link',
			array(
				'label'   => esc_html__( 'Contact link', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::URL,
				'default' => api_link_to_control( $defaults['contact_link'] ?? null ),
			)
		);

		$this->add_control(
			'phone',
			array(
				'label'   => esc_html__( 'Phone', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['phone'] ?? '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_brand',
			array(
				'label' => esc_html__( 'Brand', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo',
			array(
				'label'   => esc_html__( 'Logo', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => api_media_to_control( $defaults['logo'] ?? null ),
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label'   => esc_html__( 'Logo link', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::URL,
				'default' => api_link_to_control( $defaults['logo_link'] ?? array( 'url' => '/' ) ),
			)
		);

		$this->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => $defaults['tagline'] ?? '',
				'rows'    => 2,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_details',
			array(
				'label' => esc_html__( 'Address & hours', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'address_heading',
			array(
				'label'   => esc_html__( 'Address heading', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Find Us',
			)
		);

		$this->add_control(
			'address',
			array(
				'label'   => esc_html__( 'Address', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => $defaults['address'] ?? '',
				'rows'    => 3,
			)
		);

		$this->add_control(
			'hours_heading',
			array(
				'label'   => esc_html__( 'Hours heading', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['hours_heading'] ?? 'Opening Hours',
			)
		);

		$this->add_control(
			'hours_text',
			array(
				'label'   => esc_html__( 'Hours', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => $defaults['hours_text'] ?? '',
				'rows'    => 2,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_legal',
			array(
				'label' => esc_html__( 'Legal', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'privacy_label',
			array(
				'label'   => esc_html__( 'Privacy label', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['privacy_label'] ?? 'Privacy Policy',
			)
		);

		$this->add_control(
			'privacy_link',
			array(
				'label'   => esc_html__( 'Privacy link', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::URL,
				'default' => api_link_to_control( $defaults['privacy_link'] ?? null ),
			)
		);

		$this->add_control(
			'copyright',
			array(
				'label'   => esc_html__( 'Copyright', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['copyright'] ?? '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_typography',
			array(
				'label' => esc_html__( 'Typography', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'label'    => esc_html__( 'Contact headings', 'pet-studio-elementor' ),
				'selector' => '{{WRAPPER}} .ps-footer-contact-title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'detail_heading_typography',
				'label'    => esc_html__( 'Detail headings', 'pet-studio-elementor' ),
				'selector' => '{{WRAPPER}} .el-title.uk-h5',
			)
		);

		$this->end_controls_section();

		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s         = $this->get_render_settings();
		$phone     = $s['phone'] ?? '';
		$tel_href  = $phone ? phone_tel_href( $phone ) : '#';
		$logo_url  = media_url( $s['logo'] ?? null );
		$logo_link = $s['logo_link'] ?? array( 'url' => '/' );
		?>
		<footer class="ps-footer">
			<div class="uk-section-secondary uk-section uk-section-small">
				<div class="uk-container uk-container-xlarge">
					<div id="ps-contact" class="ps-footer-contact uk-margin-medium-bottom" tabindex="-1">
						<div class="uk-grid uk-child-width-1-1 uk-child-width-auto@s uk-grid-medium uk-flex-middle" uk-grid>
							<div>
								<div class="ps-footer-contact-title uk-h3 uk-link-heading uk-margin-remove">
									<a<?php print_link_attributes( $s['contact_link'] ?? null ); ?>><?php echo esc_html( $s['contact_heading'] ?? '' ); ?></a>
								</div>
							</div>
							<?php if ( $phone ) : ?>
								<div>
									<div class="ps-footer-contact-title uk-h3 uk-link-heading uk-margin-remove">
										<a href="<?php echo esc_url( $tel_href ); ?>"><?php echo esc_html( $phone ); ?></a>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<hr class="uk-margin-medium">

					<div class="uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-2@m" uk-grid>
						<div>
							<div class="uk-panel uk-margin-remove-first-child">
								<?php if ( $logo_url ) : ?>
									<a<?php print_link_attributes( $logo_link ); ?> uk-scroll>
										<img class="el-image ps-footer-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="" loading="lazy" width="200">
									</a>
								<?php endif; ?>
								<?php if ( ! empty( $s['tagline'] ) ) : ?>
									<p class="ps-footer-tagline uk-text-large uk-margin-small-top uk-margin-remove-bottom"><?php echo esc_html( $s['tagline'] ); ?></p>
								<?php endif; ?>
							</div>
						</div>
						<div>
							<div class="uk-grid uk-child-width-1-2@s uk-grid-small" uk-grid>
								<?php if ( ! empty( $s['address'] ) ) : ?>
									<div>
										<div class="el-title uk-h5 uk-text-muted uk-margin-remove"><?php echo esc_html( $s['address_heading'] ?? 'Find Us' ); ?></div>
										<div class="el-content uk-panel uk-text-small uk-margin-small-top">
											<?php echo format_multiline_text( $s['address'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $s['hours_text'] ) ) : ?>
									<div>
										<div class="el-title uk-h5 uk-text-muted uk-margin-remove"><?php echo esc_html( $s['hours_heading'] ?? '' ); ?></div>
										<div class="el-content uk-panel uk-text-small uk-margin-small-top">
											<?php echo format_multiline_text( $s['hours_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<hr class="uk-margin-medium">

					<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap ps-footer-legal">
						<ul class="uk-margin-remove-bottom uk-subnav uk-subnav-divider">
							<li class="el-item">
								<a class="el-link uk-text-small"<?php print_link_attributes( $s['privacy_link'] ?? null ); ?> uk-scroll>
									<?php echo esc_html( $s['privacy_label'] ?? '' ); ?>
								</a>
							</li>
						</ul>
						<p class="uk-text-small uk-text-muted uk-margin-remove"><?php echo esc_html( $s['copyright'] ?? '' ); ?></p>
					</div>
				</div>
			</div>
		</footer>
		<?php
	}
}
