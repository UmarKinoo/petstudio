<?php
/**
 * Contact form — markup renderer + secure submission handler.
 *
 * Mirrors the fields of the original Joomla (ConvertForms) contact form:
 * First name, Last name, Email, Phone, Type of enquiry (radio), Enquiry (textarea).
 *
 * The form posts back to its own page. handle_submission() runs on
 * template_redirect (before output): it validates + sanitises, emails the
 * recipient, then PRG-redirects on success. On validation error it stashes the
 * errors + old input in a per-request static so the widget can re-render them.
 *
 * The recipient/subject are read from the submitting widget's STORED Elementor
 * settings (looked up by page + element id) — never from the posted payload —
 * so the form can't be abused as an open mail relay.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor;

defined( 'ABSPATH' ) || exit;

class Contact_Form {

	/**
	 * Per-request submission state after a failed validation.
	 *
	 * @var array{errors: array<string,string>, old: array<string,string>}|null
	 */
	private static $state = null;

	/**
	 * Default "Type of enquiry" radio options (from the original form).
	 *
	 * @return string[]
	 */
	public static function default_enquiry_options(): array {
		return array( 'Dog Grooming', 'Training', 'Other' );
	}

	/**
	 * Render the form markup.
	 *
	 * @param array $opts {
	 *     @type int      $page_id          Post ID the form lives on.
	 *     @type string   $widget_id        Elementor element id (for settings lookup).
	 *     @type string   $button_text      Submit button label.
	 *     @type string[] $enquiry_options  Radio options.
	 *     @type bool     $enquiry_required Whether the radio is required.
	 *     @type string   $success_message  Shown after a successful submit.
	 * }
	 */
	public static function render( array $opts = array() ): string {
		$button_text     = (string) ( $opts['button_text'] ?? __( 'Send Enquiry', 'pet-studio-elementor' ) );
		$options         = ! empty( $opts['enquiry_options'] ) ? (array) $opts['enquiry_options'] : self::default_enquiry_options();
		$enquiry_req     = ! empty( $opts['enquiry_required'] );
		$success_message = (string) ( $opts['success_message'] ?? __( 'Thanks for your enquiry — we’ll be in touch soon.', 'pet-studio-elementor' ) );
		$page_id         = (int) ( $opts['page_id'] ?? (int) get_the_ID() );
		$widget_id       = (string) ( $opts['widget_id'] ?? '' );

		$sent   = isset( $_GET['ps_cf'] ) && 'sent' === sanitize_key( wp_unslash( $_GET['ps_cf'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$errors = self::$state['errors'] ?? array();
		$old    = self::$state['old'] ?? array();

		$val = static function ( string $key ) use ( $old ): string {
			return isset( $old[ $key ] ) ? esc_attr( (string) $old[ $key ] ) : '';
		};
		$err = static function ( string $key ) use ( $errors ): string {
			return isset( $errors[ $key ] )
				? '<span class="ps-cf-error">' . esc_html( $errors[ $key ] ) . '</span>'
				: '';
		};
		$req = '<span class="ps-cf-req" aria-hidden="true">*</span>';

		ob_start();
		?>
		<form class="ps-cf-form" id="ps-cf-form" method="post" action="<?php echo esc_url( get_permalink( $page_id ) ); ?>#ps-cf-form" novalidate>
			<?php if ( $sent ) : ?>
				<div class="ps-cf-notice ps-cf-notice--success" role="status"><?php echo esc_html( $success_message ); ?></div>
			<?php elseif ( ! empty( $errors['_global'] ) ) : ?>
				<div class="ps-cf-notice ps-cf-notice--error" role="alert"><?php echo esc_html( $errors['_global'] ); ?></div>
			<?php endif; ?>

			<div class="ps-cf-row">
				<div class="ps-cf-field ps-cf-field--half">
					<label class="ps-cf-label" for="ps-cf-first"><?php esc_html_e( 'First name', 'pet-studio-elementor' ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
					<input class="uk-input ps-cf-input" type="text" name="ps_cf[first_name]" id="ps-cf-first" value="<?php echo $val( 'first_name' ); ?>" required aria-required="true">
					<?php echo $err( 'first_name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="ps-cf-field ps-cf-field--half">
					<label class="ps-cf-label" for="ps-cf-last"><?php esc_html_e( 'Last name', 'pet-studio-elementor' ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
					<input class="uk-input ps-cf-input" type="text" name="ps_cf[last_name]" id="ps-cf-last" value="<?php echo $val( 'last_name' ); ?>" required aria-required="true">
					<?php echo $err( 'last_name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>

			<div class="ps-cf-row">
				<div class="ps-cf-field ps-cf-field--half">
					<label class="ps-cf-label" for="ps-cf-email"><?php esc_html_e( 'Email', 'pet-studio-elementor' ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
					<input class="uk-input ps-cf-input" type="email" name="ps_cf[email]" id="ps-cf-email" value="<?php echo $val( 'email' ); ?>" required aria-required="true">
					<?php echo $err( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="ps-cf-field ps-cf-field--half">
					<label class="ps-cf-label" for="ps-cf-phone"><?php esc_html_e( 'Phone', 'pet-studio-elementor' ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
					<input class="uk-input ps-cf-input" type="tel" name="ps_cf[phone]" id="ps-cf-phone" value="<?php echo $val( 'phone' ); ?>" required aria-required="true">
					<?php echo $err( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>

			<fieldset class="ps-cf-field ps-cf-fieldset">
				<legend class="ps-cf-label"><?php esc_html_e( 'Type of enquiry', 'pet-studio-elementor' ); ?><?php echo $enquiry_req ? ' ' . $req : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></legend>
				<div class="ps-cf-radios">
					<?php foreach ( $options as $option ) : ?>
						<?php $option = (string) $option; ?>
						<label class="ps-cf-radio">
							<input class="uk-radio" type="radio" name="ps_cf[enquiry_type]" value="<?php echo esc_attr( $option ); ?>" <?php checked( $old['enquiry_type'] ?? '', $option ); ?> <?php echo $enquiry_req ? 'required aria-required="true"' : ''; ?>>
							<span><?php echo esc_html( $option ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<?php echo $err( 'enquiry_type' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</fieldset>

			<div class="ps-cf-field">
				<label class="ps-cf-label" for="ps-cf-enquiry"><?php esc_html_e( 'Enquiry', 'pet-studio-elementor' ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
				<textarea class="uk-textarea ps-cf-input" name="ps_cf[enquiry]" id="ps-cf-enquiry" rows="4" placeholder="<?php esc_attr_e( 'Enter any questions you may have...', 'pet-studio-elementor' ); ?>" required aria-required="true"><?php echo esc_textarea( $old['enquiry'] ?? '' ); ?></textarea>
				<?php echo $err( 'enquiry' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php // Honeypot — hidden from humans, bots tend to fill it. ?>
			<div class="ps-cf-hp" aria-hidden="true">
				<label><?php esc_html_e( 'Leave this field empty', 'pet-studio-elementor' ); ?>
					<input type="text" name="ps_cf_hp" tabindex="-1" autocomplete="off" value="">
				</label>
			</div>

			<input type="hidden" name="ps_cf_page" value="<?php echo esc_attr( (string) $page_id ); ?>">
			<input type="hidden" name="ps_cf_widget" value="<?php echo esc_attr( $widget_id ); ?>">
			<?php wp_nonce_field( 'ps_cf_submit', 'ps_cf_nonce' ); ?>

			<div class="ps-cf-field ps-cf-submit">
				<button type="submit" class="uk-button uk-button-primary ps-cf-btn"><?php echo esc_html( $button_text ); ?></button>
			</div>
		</form>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * template_redirect handler — process a posted contact form.
	 */
	public static function handle_submission(): void {
		if ( empty( $_POST['ps_cf'] ) || ! is_array( $_POST['ps_cf'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		// Honeypot tripped → silently accept (bot), send nothing.
		if ( ! empty( $_POST['ps_cf_hp'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			self::redirect_sent();
		}

		$errors = array();

		$nonce = isset( $_POST['ps_cf_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_cf_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'ps_cf_submit' ) ) {
			$errors['_global'] = __( 'Your session expired. Please try again.', 'pet-studio-elementor' );
		}

		$raw  = map_deep( wp_unslash( $_POST['ps_cf'] ), 'trim' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data = array(
			'first_name'   => sanitize_text_field( $raw['first_name'] ?? '' ),
			'last_name'    => sanitize_text_field( $raw['last_name'] ?? '' ),
			'email'        => sanitize_email( $raw['email'] ?? '' ),
			'phone'        => sanitize_text_field( $raw['phone'] ?? '' ),
			'enquiry_type' => sanitize_text_field( $raw['enquiry_type'] ?? '' ),
			'enquiry'      => sanitize_textarea_field( $raw['enquiry'] ?? '' ),
		);

		$page_id   = isset( $_POST['ps_cf_page'] ) ? absint( $_POST['ps_cf_page'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$widget_id = isset( $_POST['ps_cf_widget'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_cf_widget'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$settings  = self::lookup_widget_settings( $page_id, $widget_id );

		$enquiry_required = isset( $settings['enquiry_required'] ) && 'yes' === $settings['enquiry_required'];

		if ( ! $errors ) {
			if ( '' === $data['first_name'] ) {
				$errors['first_name'] = __( 'Please enter your first name.', 'pet-studio-elementor' );
			}
			if ( '' === $data['last_name'] ) {
				$errors['last_name'] = __( 'Please enter your last name.', 'pet-studio-elementor' );
			}
			if ( '' === $data['email'] || ! is_email( $data['email'] ) ) {
				$errors['email'] = __( 'Please enter a valid email address.', 'pet-studio-elementor' );
			}
			if ( '' === $data['phone'] ) {
				$errors['phone'] = __( 'Please enter your phone number.', 'pet-studio-elementor' );
			}
			if ( $enquiry_required && '' === $data['enquiry_type'] ) {
				$errors['enquiry_type'] = __( 'Please choose a type of enquiry.', 'pet-studio-elementor' );
			}
			if ( '' === $data['enquiry'] ) {
				$errors['enquiry'] = __( 'Please enter your enquiry.', 'pet-studio-elementor' );
			}
		}

		if ( $errors ) {
			self::$state = array(
				'errors' => $errors,
				'old'    => $data,
			);
			return; // Let the page render with errors + repopulated values.
		}

		self::send( $data, $settings );
		self::redirect_sent();
	}

	/**
	 * Build and send the notification email.
	 *
	 * @param array<string,string> $data     Sanitised field values.
	 * @param array<string,mixed>  $settings Stored widget settings.
	 */
	private static function send( array $data, array $settings ): void {
		$recipient = '';
		if ( ! empty( $settings['recipient_email'] ) && is_email( (string) $settings['recipient_email'] ) ) {
			$recipient = (string) $settings['recipient_email'];
		}
		if ( '' === $recipient ) {
			$recipient = (string) get_option( 'admin_email' );
		}

		/** Allow a site to override where enquiries are delivered. */
		$recipient = (string) apply_filters( 'pet_studio_contact_recipient', $recipient, $data, $settings );

		$subject = ! empty( $settings['email_subject'] )
			? (string) $settings['email_subject']
			/* translators: %s: site name. */
			: sprintf( __( 'New enquiry from %s', 'pet-studio-elementor' ), get_bloginfo( 'name' ) );

		$body = implode(
			"\n",
			array(
				sprintf( '%s: %s %s', __( 'Name', 'pet-studio-elementor' ), $data['first_name'], $data['last_name'] ),
				sprintf( '%s: %s', __( 'Email', 'pet-studio-elementor' ), $data['email'] ),
				sprintf( '%s: %s', __( 'Phone', 'pet-studio-elementor' ), $data['phone'] ),
				sprintf( '%s: %s', __( 'Type of enquiry', 'pet-studio-elementor' ), '' !== $data['enquiry_type'] ? $data['enquiry_type'] : '—' ),
				'',
				__( 'Enquiry:', 'pet-studio-elementor' ),
				$data['enquiry'],
			)
		);

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			sprintf( 'Reply-To: %s %s <%s>', $data['first_name'], $data['last_name'], $data['email'] ),
		);

		wp_mail( $recipient, $subject, $body, $headers );
	}

	/**
	 * Read a widget's STORED settings from the page's Elementor data.
	 *
	 * @return array<string,mixed>
	 */
	private static function lookup_widget_settings( int $page_id, string $widget_id ): array {
		if ( ! $page_id || '' === $widget_id ) {
			return array();
		}
		$data = get_post_meta( $page_id, '_elementor_data', true );
		if ( empty( $data ) ) {
			return array();
		}
		$tree = is_string( $data ) ? json_decode( $data, true ) : $data;
		if ( ! is_array( $tree ) ) {
			return array();
		}
		$node = self::find_element( $tree, $widget_id );
		return ( $node && isset( $node['settings'] ) && is_array( $node['settings'] ) ) ? $node['settings'] : array();
	}

	/**
	 * Depth-first search of an Elementor element tree by id.
	 *
	 * @param array<int,array<string,mixed>> $nodes Elementor elements.
	 * @return array<string,mixed>|null
	 */
	private static function find_element( array $nodes, string $id ): ?array {
		foreach ( $nodes as $node ) {
			if ( ! is_array( $node ) ) {
				continue;
			}
			if ( isset( $node['id'] ) && (string) $node['id'] === $id ) {
				return $node;
			}
			if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
				$found = self::find_element( $node['elements'], $id );
				if ( $found ) {
					return $found;
				}
			}
		}
		return null;
	}

	/**
	 * Post/Redirect/Get back to the form with a success flag.
	 */
	private static function redirect_sent(): void {
		$base = wp_get_referer();
		if ( ! $base ) {
			$base = home_url( '/' );
		}
		$url = add_query_arg( 'ps_cf', 'sent', remove_query_arg( 'ps_cf', $base ) );
		wp_safe_redirect( $url . '#ps-cf-form' );
		exit;
	}
}
