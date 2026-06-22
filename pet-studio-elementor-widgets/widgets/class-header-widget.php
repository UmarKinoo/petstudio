<?php
/**
 * Site header — mirror markup (mobile + desktop).
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_link_to_control;
use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\eager_media_attrs;
use function Pet_Studio_Elementor\lazy_load_exempt_class;
use function Pet_Studio_Elementor\media_url;
use function Pet_Studio_Elementor\print_link_attributes;
use function Pet_Studio_Elementor\social_icon_name;

defined( 'ABSPATH' ) || exit;

class Header_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_header';
	}

	public function get_title(): string {
		return esc_html__( 'Pet Studio Header', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-header';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'header', 'navigation', 'menu' );
	}

	protected function get_fixture_slug(): string {
		return 'header';
	}

	protected function register_controls(): void {
		$defaults = $this->get_fixture_defaults();

		$this->start_controls_section(
			'section_logo',
			array(
				'label' => esc_html__( 'Logo', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo_default',
			array(
				'label'   => esc_html__( 'Logo (default / dark)', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => api_media_to_control( $defaults['logo_default'] ?? null ),
			)
		);

		$this->add_control(
			'logo_inverse',
			array(
				'label'   => esc_html__( 'Logo (inverse / light)', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => api_media_to_control( $defaults['logo_inverse'] ?? null ),
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label'   => esc_html__( 'Logo link', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::URL,
				'default' => api_link_to_control( $defaults['logo_link'] ?? null ),
			)
		);

		$this->add_control(
			'logo_alt',
			array(
				'label'   => esc_html__( 'Logo alt text', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'The Pet Studio - Dog Grooming & Training Academy',
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'Logo width', 'pet-studio-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 400 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 240,
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => 200,
				),
				'mobile_default' => array(
					'unit' => 'px',
					'size' => 200,
				),
				'selectors'  => array(
					'{{WRAPPER}} .uk-logo img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			array(
				'label' => esc_html__( 'Navigation', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$nav_rep = new Repeater();

		$nav_rep->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Home',
			)
		);

		$nav_rep->add_control(
			'subtitle',
			array(
				'label'   => esc_html__( 'Subtitle', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$nav_rep->add_control(
			'link',
			array(
				'label'   => esc_html__( 'Link', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$nav_rep->add_control(
			'is_active',
			array(
				'label'        => esc_html__( 'Active item', 'pet-studio-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$nav_default = array();
		foreach ( $defaults['navigation'] ?? array() as $item ) {
			$nav_default[] = array(
				'label'     => $item['label'] ?? '',
				'subtitle'  => $item['subtitle'] ?? '',
				'link'      => api_link_to_control( $item['link'] ?? null ),
				'is_active' => ! empty( $item['is_active'] ) ? 'yes' : '',
			);
		}

		$this->add_control(
			'navigation',
			array(
				'label'   => esc_html__( 'Menu items', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $nav_rep->get_controls(),
				'default' => $nav_default,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social',
			array(
				'label' => esc_html__( 'Social', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_social',
			array(
				'label'        => esc_html__( 'Show social icons', 'pet-studio-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$social_rep = new Repeater();

		$social_rep->add_control(
			'network',
			array(
				'label'   => esc_html__( 'Network', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'instagram',
				'options' => array(
					'tiktok'    => 'TikTok',
					'instagram' => 'Instagram',
					'facebook'  => 'Facebook',
				),
			)
		);

		$social_rep->add_control(
			'link',
			array(
				'label'   => esc_html__( 'Link', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$social_default = array();
		foreach ( $defaults['social_items'] ?? array() as $item ) {
			$social_default[] = array(
				'network' => $item['network'] ?? 'instagram',
				'link'    => api_link_to_control( $item['link'] ?? null ),
			);
		}

		$this->add_control(
			'social_items',
			array(
				'label'     => esc_html__( 'Social links', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $social_rep->get_controls(),
				'default'   => $social_default,
				'condition' => array( 'show_social' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_behaviour',
			array(
				'label' => esc_html__( 'Behaviour', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'enable_sticky',
			array(
				'label'        => esc_html__( 'Sticky header', 'pet-studio-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => ! empty( $defaults['enable_sticky'] ) ? 'yes' : '',
			)
		);

		$this->add_control(
			'enable_transparent',
			array(
				'label'        => esc_html__( 'Transparent over hero', 'pet-studio-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => ! empty( $defaults['enable_transparent'] ) ? 'yes' : '',
			)
		);

		$this->add_control(
			'mobile_menu_label',
			array(
				'label'   => esc_html__( 'Mobile menu toggle label (aria)', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Open Menu', 'pet-studio-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_nav',
			array(
				'label' => esc_html__( 'Navigation style', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_typography',
				'label'    => esc_html__( 'Nav typography', 'pet-studio-elementor' ),
				'selector' => '{{WRAPPER}} .uk-navbar-nav > li > a, {{WRAPPER}} .uk-nav-default > li > a',
			)
		);

		$this->add_control(
			'nav_subtitle_color',
			array(
				'label'     => esc_html__( 'Subtitle colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uk-navbar-subtitle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uk-nav-subtitle'    => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_active_color',
			array(
				'label'     => esc_html__( 'Active item colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF90AA',
				'selectors' => array(
					'{{WRAPPER}} .uk-navbar-nav > li.uk-active > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uk-nav-default > li.uk-active > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$settings   = $this->get_render_settings();
		$dialog_id  = 'tm-dialog-mobile-' . $this->get_id();
		$logo_url   = media_url( $settings['logo_default'] ?? null );
		$logo_inv   = media_url( $settings['logo_inverse'] ?? null );
		$logo_alt   = $settings['logo_alt'] ?? '';
		$sticky     = ( $settings['enable_sticky'] ?? '' ) === 'yes';
		$transparent = ( $settings['enable_transparent'] ?? '' ) === 'yes';
		$inactive_cls = $transparent ? 'uk-navbar-transparent' : '';
		$sticky_attr  = $sticky
			? ' uk-sticky show-on-up animation="uk-animation-slide-top" cls-active="uk-navbar-sticky" sel-target=".uk-navbar-container" cls-inactive="' . esc_attr( $inactive_cls ) . '" tm-section-start'
			: '';
		$sticky_attr_desktop = $sticky
			? ' uk-sticky media="@m" show-on-up animation="uk-animation-slide-top" cls-active="uk-navbar-sticky" sel-target=".uk-navbar-container" cls-inactive="' . esc_attr( $inactive_cls ) . '" tm-section-start'
			: '';
		?>
		<header class="tm-header-mobile uk-hidden@m tm-header-overlay" uk-header uk-inverse="target: .uk-navbar-container; sel-active: .uk-navbar-transparent">
			<div<?php echo $sticky_attr ? ' ' . trim( $sticky_attr ) : ''; ?>>
				<div class="uk-navbar-container">
					<div class="uk-container uk-container-expand">
						<nav class="uk-navbar" uk-navbar='{"align":"left","container":".tm-header-mobile > [uk-sticky]","boundary":".tm-header-mobile .uk-navbar-container"}'>
							<div class="uk-navbar-center">
								<a aria-label="<?php esc_attr_e( 'Back to home', 'pet-studio-elementor' ); ?>" class="uk-logo uk-navbar-item"<?php print_link_attributes( $settings['logo_link'] ?? null ); ?>>
									<?php $this->render_logo_images( $logo_url, $logo_inv, $logo_alt, 200, 23 ); ?>
								</a>
							</div>
							<div class="uk-navbar-right">
								<a uk-toggle href="#<?php echo esc_attr( $dialog_id ); ?>" class="uk-navbar-toggle" aria-label="<?php echo esc_attr( $settings['mobile_menu_label'] ?? '' ); ?>">
									<div uk-navbar-toggle-icon></div>
								</a>
							</div>
						</nav>
					</div>
				</div>
			</div>
			<div id="<?php echo esc_attr( $dialog_id ); ?>" uk-offcanvas="container: true; overlay: true" mode="slide" flip>
				<div class="uk-offcanvas-bar uk-flex uk-flex-column">
					<button class="uk-offcanvas-close uk-close-large" type="button" uk-close uk-toggle="cls: uk-close-large; mode: media; media: @s"></button>
					<div class="uk-margin-auto-bottom">
						<div class="uk-panel" id="module-menu-dialog-mobile">
							<?php $this->render_mobile_nav( $settings['navigation'] ?? array() ); ?>
						</div>
					</div>
				</div>
			</div>
		</header>

		<header class="tm-header uk-visible@m tm-header-overlay" uk-header uk-inverse="target: .uk-navbar-container, .tm-headerbar; sel-active: .uk-navbar-transparent, .tm-headerbar">
			<div<?php echo $sticky_attr_desktop ? ' ' . trim( $sticky_attr_desktop ) : ''; ?>>
				<div class="uk-navbar-container">
					<div class="uk-container">
						<nav class="uk-navbar" uk-navbar='{"align":"left","container":".tm-header > [uk-sticky]","boundary":".tm-header .uk-navbar-container"}'>
							<div class="uk-navbar-left">
								<a aria-label="<?php esc_attr_e( 'Back to home', 'pet-studio-elementor' ); ?>" class="uk-logo uk-navbar-item"<?php print_link_attributes( $settings['logo_link'] ?? null ); ?>>
									<?php $this->render_logo_images( $logo_url, $logo_inv, $logo_alt, 240, 28 ); ?>
								</a>
							</div>
							<div class="uk-navbar-right">
								<?php $this->render_desktop_nav( $settings['navigation'] ?? array() ); ?>
								<?php if ( ( $settings['show_social'] ?? '' ) === 'yes' ) : ?>
									<div class="uk-navbar-item" id="module-tm-3">
										<?php $this->render_social( $settings['social_items'] ?? array() ); ?>
									</div>
								<?php endif; ?>
							</div>
						</nav>
					</div>
				</div>
			</div>
		</header>
		<?php
	}

	/**
	 * @param array<int, array<string, mixed>> $items Nav repeater rows.
	 */
	private function render_mobile_nav( array $items ): void {
		?>
		<ul class="uk-nav uk-nav-default">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$classes = array();
				if ( ( $item['is_active'] ?? '' ) === 'yes' ) {
					$classes[] = 'uk-active';
				}
				?>
				<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
					<a<?php print_link_attributes( $item['link'] ?? null ); ?>>
						<div>
							<?php echo esc_html( $item['label'] ?? '' ); ?>
							<div class="uk-nav-subtitle"><?php echo esc_html( $item['subtitle'] ?? '' ); ?></div>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * @param array<int, array<string, mixed>> $items Nav repeater rows.
	 */
	private function render_desktop_nav( array $items ): void {
		?>
		<ul class="uk-navbar-nav">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$classes = array();
				if ( ( $item['is_active'] ?? '' ) === 'yes' ) {
					$classes[] = 'uk-active';
				}
				?>
				<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
					<a<?php print_link_attributes( $item['link'] ?? null ); ?>>
						<div>
							<?php echo esc_html( $item['label'] ?? '' ); ?>
							<div class="uk-navbar-subtitle"><?php echo esc_html( $item['subtitle'] ?? '' ); ?></div>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * @param array<int, array<string, mixed>> $items Social repeater rows.
	 */
	private function render_social( array $items ): void {
		?>
		<ul class="uk-grid uk-flex-inline uk-flex-middle uk-flex-nowrap uk-grid-small">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$icon = social_icon_name( (string) ( $item['network'] ?? '' ) );
				$link = $item['link'] ?? null;
				if ( empty( $link['url'] ) ) {
					continue;
				}
				?>
				<li>
					<a class="uk-preserve-width uk-icon-link"<?php print_link_attributes( $link ); ?>>
						<span uk-icon="icon: <?php echo esc_attr( $icon ); ?>;"></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	private function render_logo_images( string $logo_url, string $logo_inv_url, string $alt, int $width = 240, int $height = 28 ): void {
		$attrs = eager_media_attrs( true );
		if ( $logo_url ) {
			echo '<img class="' . esc_attr( lazy_load_exempt_class() ) . '" src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $alt ) . '" width="' . (int) $width . '" height="' . (int) $height . '" ' . $attrs . '>';
		}
		if ( $logo_inv_url ) {
			echo '<img class="' . esc_attr( lazy_load_exempt_class( 'uk-logo-inverse' ) ) . '" src="' . esc_url( $logo_inv_url ) . '" alt="' . esc_attr( $alt ) . '" width="' . (int) $width . '" height="' . (int) $height . '" ' . $attrs . '>';
		}
	}
}
