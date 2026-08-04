<?php
/**
 * Contact page section — booking info + academy enquiry form + map.
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
use function Pet_Studio_Elementor\render_rich_text;

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

		$this->start_controls_section( 'section_booking', array( 'label' => esc_html__( 'Booking', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'heading', array( 'label' => esc_html__( 'Page heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading'] ?? 'Contact Us', 'label_block' => true ) );
		$this->add_control( 'subheading', array( 'label' => esc_html__( 'Lead copy', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['subheading'] ?? '', 'rows' => 3 ) );
		$this->add_control( 'grooming_heading', array( 'label' => esc_html__( 'Grooming section heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['grooming_heading'] ?? '', 'label_block' => true ) );
		$this->add_control( 'intro_text', array( 'label' => esc_html__( 'Grooming intro', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['intro_text'] ?? '', 'rows' => 6 ) );
		$this->add_control( 'phone', array( 'label' => esc_html__( 'Phone', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['phone'] ?? '' ) );
		$this->add_control( 'phone_cta_text', array( 'label' => esc_html__( 'Phone CTA text', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['phone_cta_text'] ?? '' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_academy_form', array( 'label' => esc_html__( 'Academy enquiry form', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'academy_section_heading', array( 'label' => esc_html__( 'Academy section heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['academy_section_heading'] ?? '', 'label_block' => true ) );
		$this->add_control( 'academy_section_intro', array( 'label' => esc_html__( 'Academy section intro', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['academy_section_intro'] ?? '', 'rows' => 5 ) );
		$this->add_control( 'academy_heading', array( 'label' => esc_html__( 'Form heading (pink)', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['academy_heading'] ?? '', 'label_block' => true ) );
		$this->add_control( 'academy_intro', array( 'label' => esc_html__( 'Form introduction', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['academy_intro'] ?? '', 'rows' => 4 ) );
		$this->add_control( 'form_shortcode', array( 'label' => esc_html__( 'Form shortcode override', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['form_shortcode'] ?? '', 'description' => esc_html__( 'Optional. Paste a shortcode to replace the built-in form.', 'pet-studio-elementor' ) ) );
		$this->add_control( 'recipient_email', array( 'label' => esc_html__( 'Send enquiries to', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'input_type' => 'email', 'placeholder' => get_option( 'admin_email' ), 'label_block' => true, 'default' => $d['recipient_email'] ?? '' ) );
		$this->add_control( 'email_subject', array( 'label' => esc_html__( 'Email subject', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'label_block' => true, 'default' => $d['email_subject'] ?? '' ) );
		$this->add_control( 'first_name_label', array( 'label' => esc_html__( 'First name label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['first_name_label'] ?? 'First Name' ) );
		$this->add_control( 'last_name_label', array( 'label' => esc_html__( 'Last name label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['last_name_label'] ?? 'Last Name' ) );
		$this->add_control( 'email_label', array( 'label' => esc_html__( 'Email label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['email_label'] ?? 'Email Address' ) );
		$this->add_control( 'phone_label', array( 'label' => esc_html__( 'Telephone label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['phone_label'] ?? 'Telephone Number' ) );
		$this->add_control( 'message_label', array( 'label' => esc_html__( 'Enquiry field label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['message_label'] ?? '', 'label_block' => true ) );
		$this->add_control( 'message_placeholder', array( 'label' => esc_html__( 'Enquiry field placeholder', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['message_placeholder'] ?? '', 'rows' => 2 ) );
		$this->add_control( 'button_text', array( 'label' => esc_html__( 'Button text', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['button_text'] ?? 'Send Enquiry' ) );
		$this->add_control( 'success_message', array( 'label' => esc_html__( 'Success message', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => $d['success_message'] ?? '' ) );
		$this->add_control( 'privacy_text', array( 'label' => esc_html__( 'Privacy line', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['privacy_text'] ?? '', 'rows' => 2, 'description' => esc_html__( 'Shown under the submit button. “Privacy Policy” becomes a link when a privacy URL is set.', 'pet-studio-elementor' ) ) );
		$this->add_control( 'privacy_link', array( 'label' => esc_html__( 'Privacy policy link', 'pet-studio-elementor' ), 'type' => Controls_Manager::URL, 'default' => api_link_to_control( $d['privacy_link'] ?? null ) ) );
		$this->add_control( 'show_enquiry_type', array( 'label' => esc_html__( 'Show "Type of enquiry" field', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['show_enquiry_type'] ) ? 'yes' : '' ) );
		$this->add_control( 'message_required', array( 'label' => esc_html__( 'Require enquiry message', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['message_required'] ) ? 'yes' : '' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_location', array( 'label' => esc_html__( 'Location', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'sticky_image', array( 'label' => esc_html__( 'Sticky image (desktop)', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['sticky_image'] ?? null ) ) );
		$this->add_control( 'mobile_image', array( 'label' => esc_html__( 'Image (mobile)', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => api_media_to_control( $d['mobile_image'] ?? null ) ) );
		$this->add_control( 'visit_heading', array( 'label' => esc_html__( 'Visit heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['visit_heading'] ?? 'Find Us', 'label_block' => true ) );
		$this->add_control( 'address', array( 'label' => esc_html__( 'Address', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['address'] ?? '', 'rows' => 4 ) );
		$this->add_control( 'opening_hours', array( 'label' => esc_html__( 'Opening hours', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['opening_hours'] ?? '', 'rows' => 2 ) );
		$this->add_control( 'visit_body', array( 'label' => esc_html__( 'Visit body copy', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['visit_body'] ?? '', 'rows' => 4 ) );
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
		$s      = $this->get_render_settings();
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
		<div class="uk-section-default uk-section uk-section-small-top uk-padding-remove-bottom ps-contact-section">
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
							<div class="ps-contact-booking" tabindex="-1">
								<h1 class="uk-heading-medium uk-heading-line uk-margin-medium uk-width-xlarge uk-margin-auto uk-text-left">
									<span><?php echo esc_html( $s['heading'] ?? '' ); ?></span>
								</h1>
								<?php if ( $mobile ) : ?>
									<div class="uk-margin uk-hidden@l">
										<img class="el-image" src="<?php echo esc_url( $mobile ); ?>" alt="" loading="lazy" width="1240" height="1860">
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $s['subheading'] ) ) : ?>
									<p class="uk-h4 uk-width-xlarge uk-margin-auto uk-text-left uk-margin-remove-top"><?php echo esc_html( $s['subheading'] ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $s['grooming_heading'] ) ) : ?>
									<h2 class="uk-h3 uk-margin-medium-top uk-margin-small-bottom uk-width-xlarge uk-margin-auto uk-text-left"><?php echo esc_html( $s['grooming_heading'] ); ?></h2>
								<?php endif; ?>
								<?php if ( ! empty( $s['intro_text'] ) ) : ?>
									<div class="ps-contact-intro uk-width-xlarge uk-margin-auto uk-text-left uk-margin-medium-top">
										<?php echo format_multiline_text( $s['intro_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endif; ?>
								<?php if ( $phone ) : ?>
									<div class="ps-contact-phone uk-h1 uk-margin uk-width-xlarge uk-margin-auto uk-text-left uk-visible@s">
										<a class="el-link uk-link-reset" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html( $phone ); ?></a>
									</div>
									<div class="ps-contact-phone uk-h2 uk-margin uk-width-xlarge uk-margin-auto uk-text-left uk-hidden@s">
										<a class="el-link uk-link-reset" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html( $phone ); ?></a>
									</div>
									<?php if ( ! empty( $s['phone_cta_text'] ) ) : ?>
										<div class="uk-margin uk-width-xlarge uk-margin-auto">
											<a class="uk-button uk-button-primary" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html( $s['phone_cta_text'] ); ?></a>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>

							<div class="ps-contact-academy uk-margin-xlarge uk-width-xlarge uk-margin-auto">
								<?php if ( ! empty( $s['academy_section_heading'] ) ) : ?>
									<h2 class="uk-h3 uk-margin-medium-bottom"><?php echo esc_html( $s['academy_section_heading'] ); ?></h2>
								<?php endif; ?>
								<?php if ( ! empty( $s['academy_section_intro'] ) ) : ?>
									<div class="ps-contact-academy-section-intro uk-margin-medium-bottom">
										<?php echo format_multiline_text( $s['academy_section_intro'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $s['academy_heading'] ) ) : ?>
									<h2 class="uk-h3 uk-text-primary uk-margin-medium-bottom"><?php echo esc_html( $s['academy_heading'] ); ?></h2>
								<?php endif; ?>
								<?php if ( ! empty( $s['academy_intro'] ) ) : ?>
									<div class="ps-contact-academy-intro uk-margin-medium-bottom">
										<?php echo format_multiline_text( $s['academy_intro'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endif; ?>
								<div class="uk-panel uk-margin">
									<?php
									$shortcode = trim( (string) ( $s['form_shortcode'] ?? '' ) );
									if ( $shortcode ) {
										echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									} else {
										echo \Pet_Studio_Elementor\Contact_Form::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											array(
												'page_id'             => (int) get_the_ID(),
												'widget_id'           => (string) $this->get_id(),
												'button_text'         => $s['button_text'] ?? 'Send Enquiry',
												'show_enquiry_type'   => ! empty( $s['show_enquiry_type'] ) && 'yes' === $s['show_enquiry_type'],
												'message_required'    => ! isset( $s['message_required'] ) || 'yes' === $s['message_required'],
												'success_message'     => $s['success_message'] ?? '',
												'first_name_label'    => $s['first_name_label'] ?? 'First Name',
												'last_name_label'     => $s['last_name_label'] ?? 'Last Name',
												'email_label'         => $s['email_label'] ?? 'Email Address',
												'phone_label'         => $s['phone_label'] ?? 'Telephone Number',
												'message_label'       => $s['message_label'] ?? 'Enquiry',
												'message_placeholder' => $s['message_placeholder'] ?? '',
												'privacy_text'        => $s['privacy_text'] ?? '',
												'privacy_link'        => $s['privacy_link'] ?? null,
											)
										);
									}
									?>
								</div>
							</div>

							<?php if ( ! empty( $s['address'] ) || ! empty( $s['visit_heading'] ) || ! empty( $s['visit_body'] ) ) : ?>
								<?php if ( ! empty( $s['visit_heading'] ) ) : ?>
									<div class="uk-h4 uk-text-primary uk-margin-large-top uk-margin-remove-bottom uk-width-xlarge uk-margin-auto uk-text-left"><?php echo esc_html( $s['visit_heading'] ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $s['address'] ) ) : ?>
									<div class="uk-h3 uk-margin uk-width-xlarge uk-margin-auto uk-text-left">
										<?php echo format_multiline_text( $s['address'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $s['opening_hours'] ) ) : ?>
									<div class="uk-margin uk-width-xlarge uk-margin-auto uk-text-left">
										<?php echo format_multiline_text( $s['opening_hours'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $s['visit_body'] ) ) : ?>
									<div class="uk-margin uk-width-xlarge uk-margin-auto uk-text-left">
										<?php echo format_multiline_text( $s['visit_body'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endif; ?>
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
