<?php
/**
 * Contact enquiry form — renders the bespoke Pet Studio form and emails it.
 *
 * Fields mirror the original site: First/Last name, Email, Phone,
 * Type of enquiry (radio), Enquiry (textarea). Submission is handled by
 * \Pet_Studio_Elementor\Contact_Form (nonce, honeypot, validation, wp_mail).
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Pet_Studio_Elementor\Contact_Form;
use Pet_Studio_Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

class Contact_Form_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_contact_form';
	}

	public function get_title(): string {
		return esc_html__( 'Contact Form', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-form-horizontal';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'contact', 'form', 'enquiry' );
	}

	protected function get_fixture_slug(): string {
		return 'contact-form';
	}

	protected function register_controls(): void {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Form', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'recipient_email',
			array(
				'label'       => esc_html__( 'Send enquiries to', 'pet-studio-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'input_type'  => 'email',
				'placeholder' => get_option( 'admin_email' ),
				'description' => esc_html__( 'Leave blank to use the site admin email.', 'pet-studio-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'email_subject',
			array(
				'label'       => esc_html__( 'Email subject', 'pet-studio-elementor' ),
				'type'        => Controls_Manager::TEXT,
				/* translators: %s: site name. */
				'placeholder' => sprintf( esc_html__( 'New enquiry from %s', 'pet-studio-elementor' ), get_bloginfo( 'name' ) ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button text', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Send Enquiry', 'pet-studio-elementor' ),
			)
		);

		$this->add_control(
			'success_message',
			array(
				'label'   => esc_html__( 'Success message', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => esc_html__( 'Thanks for your enquiry — we’ll be in touch soon.', 'pet-studio-elementor' ),
			)
		);

		$this->add_control(
			'enquiry_required',
			array(
				'label'        => esc_html__( 'Make "Type of enquiry" required', 'pet-studio-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'label',
			array(
				'label' => esc_html__( 'Option', 'pet-studio-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'enquiry_options',
			array(
				'label'       => esc_html__( 'Type of enquiry options', 'pet-studio-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'label' => esc_html__( 'Dog Grooming', 'pet-studio-elementor' ) ),
					array( 'label' => esc_html__( 'Training', 'pet-studio-elementor' ) ),
					array( 'label' => esc_html__( 'Other', 'pet-studio-elementor' ) ),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_render_settings();

		$options = array();
		foreach ( (array) ( $s['enquiry_options'] ?? array() ) as $row ) {
			$label = is_array( $row ) ? trim( (string) ( $row['label'] ?? '' ) ) : trim( (string) $row );
			if ( '' !== $label ) {
				$options[] = $label;
			}
		}
		if ( ! $options ) {
			$options = Contact_Form::default_enquiry_options();
		}

		echo Contact_Form::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'page_id'          => (int) get_the_ID(),
				'widget_id'        => (string) $this->get_id(),
				'button_text'      => $s['button_text'] ?? 'Send Enquiry',
				'enquiry_options'  => $options,
				'enquiry_required' => ! empty( $s['enquiry_required'] ) && 'yes' === $s['enquiry_required'],
				'success_message'  => $s['success_message'] ?? '',
			)
		);
	}
}
