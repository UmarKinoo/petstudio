<?php
/**
 * Contact page section — form shortcode + map.
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

class Contact_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_contact';
	}

	public function get_title(): string {
		return esc_html__( 'Contact', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-form-horizontal';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'contact', 'form', 'map' );
	}

	protected function get_fixture_slug(): string {
		return 'contact';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'heading', array( 'label' => esc_html__( 'Heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading'] ?? 'Contact Us' ) );
		$this->add_control( 'phone', array( 'label' => esc_html__( 'Phone', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['phone'] ?? '' ) );
		$this->add_control( 'form_shortcode', array( 'label' => esc_html__( 'Form shortcode', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['form_shortcode'] ?? '', 'description' => esc_html__( 'Optional. Paste a shortcode to override the built-in enquiry form, e.g. [elementor-template id="123"]', 'pet-studio-elementor' ) ) );
		$this->add_control( 'recipient_email', array( 'label' => esc_html__( 'Send enquiries to', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'input_type' => 'email', 'placeholder' => get_option( 'admin_email' ), 'description' => esc_html__( 'Built-in form only. Leave blank to use the site admin email.', 'pet-studio-elementor' ), 'label_block' => true, 'default' => $d['recipient_email'] ?? '' ) );
		$this->add_control( 'email_subject', array( 'label' => esc_html__( 'Email subject', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'label_block' => true, 'default' => $d['email_subject'] ?? '' ) );
		$this->add_control( 'button_text', array( 'label' => esc_html__( 'Button text', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['button_text'] ?? 'Send Enquiry' ) );
		$this->add_control( 'success_message', array( 'label' => esc_html__( 'Success message', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => $d['success_message'] ?? 'Thanks for your enquiry — we’ll be in touch soon.' ) );
		$this->add_control( 'enquiry_required', array( 'label' => esc_html__( 'Make "Type of enquiry" required', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ) );
		$this->add_control( 'sticky_image', array( 'label' => esc_html__( 'Sticky image (desktop)', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['sticky_image'] ?? null ) ) );
		$this->add_control( 'mobile_image', array( 'label' => esc_html__( 'Image (mobile)', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['mobile_image'] ?? null ) ) );
		$this->add_control( 'address', array( 'label' => esc_html__( 'Address', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['address'] ?? '', 'rows' => 4 ) );
		$this->add_control( 'maps_button_text', array( 'label' => esc_html__( 'Maps button text', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['maps_button_text'] ?? 'Open in Google Maps' ) );
		$this->add_control( 'maps_link', array( 'label' => esc_html__( 'Maps link', 'pet-studio-elementor' ), 'type' => Controls_Manager::URL, 'default' => api_link_to_control( $d['maps_link'] ?? null ) ) );
		$this->add_control( 'map_lat', array( 'label' => esc_html__( 'Map latitude', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['map_lat'] ?? '51.3703' ) );
		$this->add_control( 'map_lng', array( 'label' => esc_html__( 'Map longitude', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['map_lng'] ?? '-2.8091' ) );
		$this->add_control( 'map_zoom', array( 'label' => esc_html__( 'Map zoom', 'pet-studio-elementor' ), 'type' => Controls_Manager::NUMBER, 'default' => (int) ( $d['map_zoom'] ?? 15 ), 'min' => 1, 'max' => 20 ) );
		$this->add_control( 'map_marker_title', array( 'label' => esc_html__( 'Map marker title', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['map_marker_title'] ?? '' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .uk-heading-medium' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$sticky = media_url( $s['sticky_image'] ?? null );
		$mobile = media_url( $s['mobile_image'] ?? null ) ?: $sticky;
		$phone  = $s['phone'] ?? '';
		$tel    = $phone ? phone_tel_href( $phone ) : '#';
		$lat    = (float) ( $s['map_lat'] ?? 51.3703 );
		$lng    = (float) ( $s['map_lng'] ?? -2.8091 );
		$zoom   = (int) ( $s['map_zoom'] ?? 15 );
		$map_src = sprintf(
			'https://maps.google.com/maps?q=%s,%s&z=%d&output=embed',
			rawurlencode( (string) $lat ),
			rawurlencode( (string) $lng ),
			$zoom
		);
		?>
		<div class="uk-section-default uk-section uk-section-small-top uk-padding-remove-bottom">
			<div class="uk-container uk-container-expand">
				<div class="uk-grid-margin uk-container uk-container-expand">
					<div class="uk-grid tm-grid-expand uk-grid-column-medium" uk-grid>
						<div class="js-sticky uk-width-1-2@l uk-visible@l">
							<div class="uk-panel uk-position-z-index" uk-sticky="end: !.js-sticky; media: @m;">
								<?php if ( $sticky ) : ?>
									<div class="uk-margin uk-visible@l">
										<img class="el-image" style="height: 100vh; object-fit: cover;" src="<?php echo esc_url( $sticky ); ?>" alt="" width="1240" height="1860">
									</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="uk-width-1-2@l">
							<h1 class="uk-heading-medium uk-heading-line uk-margin-large uk-width-xlarge uk-margin-auto uk-text-left">
								<span><?php echo esc_html( $s['heading'] ?? '' ); ?></span>
							</h1>
							<?php if ( $mobile ) : ?>
								<div class="uk-margin uk-hidden@m">
									<img class="el-image" src="<?php echo esc_url( $mobile ); ?>" alt="" loading="lazy" width="1240" height="1860">
								</div>
							<?php endif; ?>
							<div class="uk-h4 uk-text-primary uk-margin-large-top uk-margin-remove-bottom uk-width-xlarge uk-margin-auto uk-text-left">Get in Touch</div>
							<?php if ( $phone ) : ?>
								<div class="uk-h1 uk-margin uk-width-xlarge uk-margin-auto uk-text-left uk-visible@s">
									<a class="el-link uk-link-reset" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html( $phone ); ?></a>
								</div>
								<div class="uk-h2 uk-margin uk-width-xlarge uk-margin-auto uk-text-left uk-hidden@s">
									<a class="el-link uk-link-reset" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html( $phone ); ?></a>
								</div>
							<?php endif; ?>
							<div class="uk-h4 uk-text-primary uk-margin-large-top uk-margin-remove-bottom uk-width-xlarge uk-margin-auto uk-text-left">Enquiry Form</div>
							<div class="uk-panel uk-margin uk-width-xlarge uk-margin-auto">
								<?php
								$shortcode = trim( (string) ( $s['form_shortcode'] ?? '' ) );
								if ( $shortcode ) {
									echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									echo \Pet_Studio_Elementor\Contact_Form::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											array(
												'page_id'          => (int) get_the_ID(),
												'widget_id'        => (string) $this->get_id(),
												'button_text'      => $s['button_text'] ?? 'Send Enquiry',
												'enquiry_required' => ! empty( $s['enquiry_required'] ) && 'yes' === $s['enquiry_required'],
												'success_message'  => $s['success_message'] ?? '',
											)
										);
								}
								?>
							</div>
							<?php if ( ! empty( $s['address'] ) ) : ?>
								<div class="uk-h4 uk-text-primary uk-margin-large-top uk-margin-remove-bottom uk-width-xlarge uk-margin-auto uk-text-left">Find Us</div>
								<div class="uk-h3 uk-margin uk-width-xlarge uk-margin-auto uk-text-left">
									<?php echo format_multiline_text( $s['address'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $s['maps_button_text'] ) ) : ?>
								<div class="uk-margin uk-width-xlarge uk-margin-auto">
									<a class="el-content uk-button uk-button-text"<?php print_link_attributes( $s['maps_link'] ?? null ); ?>><?php echo esc_html( $s['maps_button_text'] ); ?></a>
								</div>
							<?php endif; ?>
							<div class="uk-margin-xlarge">
								<iframe class="uk-width-1-1" style="height: 450px; border: 0;" loading="lazy" allowfullscreen title="<?php echo esc_attr( $s['map_marker_title'] ?? 'Map' ); ?>" src="<?php echo esc_url( $map_src ); ?>"></iframe>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
