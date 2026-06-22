<?php
/**
 * Cookie consent banner.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_link_to_control;
use function Pet_Studio_Elementor\print_link_attributes;

defined( 'ABSPATH' ) || exit;

class Cookie_Consent_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_cookie_consent';
	}

	public function get_title(): string {
		return esc_html__( 'Cookie Consent', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-alert';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'cookie', 'consent', 'gdpr' );
	}

	protected function get_fixture_slug(): string {
		return 'cookie-consent';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'banner_text', array( 'label' => esc_html__( 'Banner text', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['banner_text'] ?? '', 'rows' => 3 ) );
		$this->add_control( 'accept_label', array( 'label' => esc_html__( 'Accept label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['accept_label'] ?? 'Accept' ) );
		$this->add_control( 'reject_label', array( 'label' => esc_html__( 'Reject label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['reject_label'] ?? 'Reject' ) );
		$this->add_control( 'manage_label', array( 'label' => esc_html__( 'Manage settings label', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['manage_label'] ?? 'Manage Settings' ) );
		$this->add_control( 'privacy_link', array( 'label' => esc_html__( 'Privacy policy link', 'pet-studio-elementor' ), 'type' => Controls_Manager::URL, 'default' => api_link_to_control( $d['privacy_link'] ?? null ) ) );
		$this->end_controls_section();
		$this->register_style_controls();
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$storage_key = 'pet_studio_cookie_consent_' . $this->get_id();
		?>
		<div class="tm-consent uk-section uk-section-xsmall uk-section-muted uk-position-bottom uk-position-fixed uk-position-z-index-high bottom ps-cookie-consent" data-storage-key="<?php echo esc_attr( $storage_key ); ?>" hidden>
			<div class="uk-container uk-container-expand">
				<?php if ( ! empty( $s['banner_text'] ) ) : ?>
					<p><?php echo esc_html( $s['banner_text'] ); ?>
						<?php if ( ! empty( $s['privacy_link']['url'] ) ) : ?>
							<a<?php print_link_attributes( $s['privacy_link'] ); ?>><?php esc_html_e( 'Privacy Policy', 'pet-studio-elementor' ); ?></a>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<div class="uk-child-width-1-1 uk-child-width-auto@s uk-grid-small" uk-grid>
					<div>
						<button type="button" class="uk-button uk-button-secondary uk-width-1-1 ps-cookie-accept"><?php echo esc_html( $s['accept_label'] ?? 'Accept' ); ?></button>
					</div>
					<div>
						<button type="button" class="uk-button uk-button-default uk-width-1-1 ps-cookie-reject"><?php echo esc_html( $s['reject_label'] ?? 'Reject' ); ?></button>
					</div>
					<?php if ( ! empty( $s['manage_label'] ) && ! empty( $s['privacy_link']['url'] ) ) : ?>
						<div>
							<a class="uk-button uk-button-default uk-width-1-1"<?php print_link_attributes( $s['privacy_link'] ); ?>><?php echo esc_html( $s['manage_label'] ); ?></a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<script>
		(function () {
			var banner = document.currentScript && document.currentScript.previousElementSibling;
			if (!banner || !banner.classList.contains('ps-cookie-consent')) {
				banner = document.querySelector('.elementor-element-<?php echo esc_js( (string) $this->get_id() ); ?> .ps-cookie-consent');
			}
			if (!banner) return;
			var key = banner.getAttribute('data-storage-key');
			if (localStorage.getItem(key)) return;
			banner.hidden = false;
			banner.querySelector('.ps-cookie-accept')?.addEventListener('click', function () {
				localStorage.setItem(key, 'accept');
				banner.remove();
			});
			banner.querySelector('.ps-cookie-reject')?.addEventListener('click', function () {
				localStorage.setItem(key, 'reject');
				banner.remove();
			});
		})();
		</script>
		<?php
	}
}
