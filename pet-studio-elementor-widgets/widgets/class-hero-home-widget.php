<?php
/**
 * Hero — Home (video + parallax logo + headline words).
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\eager_media_attrs;
use function Pet_Studio_Elementor\format_multiline_text;
use function Pet_Studio_Elementor\lazy_load_exempt_class;
use function Pet_Studio_Elementor\media_url;

defined( 'ABSPATH' ) || exit;

class Hero_Home_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_hero_home';
	}

	public function get_title(): string {
		return esc_html__( 'Hero — Home', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-video-camera';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'hero', 'home', 'video' );
	}

	protected function get_fixture_slug(): string {
		return 'hero-home';
	}

	protected function register_controls(): void {
		$defaults = $this->get_fixture_defaults();

		$this->start_controls_section(
			'section_video',
			array(
				'label' => esc_html__( 'Video', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'video_desktop',
			array(
				'label'   => esc_html__( 'Background video (desktop)', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'default' => api_media_to_control( $defaults['video_desktop'] ?? null ),
			)
		);

		$this->add_control(
			'video_mobile',
			array(
				'label'   => esc_html__( 'Background video (mobile)', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'default' => api_media_to_control( $defaults['video_mobile'] ?? null ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logo',
			array(
				'label' => esc_html__( 'Logo', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo_desktop',
			array(
				'label'   => esc_html__( 'Logo (desktop)', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => api_media_to_control( $defaults['logo_desktop'] ?? null ),
			)
		);

		$this->add_control(
			'logo_mobile',
			array(
				'label'   => esc_html__( 'Logo (mobile)', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => api_media_to_control( $defaults['logo_mobile'] ?? null ),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_headlines',
			array(
				'label' => esc_html__( 'Headlines', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$words_rep = new Repeater();
		$words_rep->add_control(
			'word',
			array(
				'label'   => esc_html__( 'Word', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Experienced.',
			)
		);

		$words_default = array();
		foreach ( $defaults['headline_words'] ?? array() as $word ) {
			$words_default[] = is_array( $word ) ? $word : array( 'word' => (string) $word );
		}

		$this->add_control(
			'headline_words',
			array(
				'label'   => esc_html__( 'Parallax words', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $words_rep->get_controls(),
				'default' => $words_default,
			)
		);

		$this->add_control(
			'hours_title',
			array(
				'label'   => esc_html__( 'Opening hours title', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['hours_title'] ?? 'Opening Hours',
			)
		);

		$this->add_control(
			'hours_text',
			array(
				'label'   => esc_html__( 'Opening hours', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => $defaults['hours_text'] ?? '',
				'rows'    => 3,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'headline_typography',
				'label'    => esc_html__( 'Headline typography', 'pet-studio-elementor' ),
				'selector' => '{{WRAPPER}} .uk-heading-large',
			)
		);

		$this->add_control(
			'headline_color',
			array(
				'label'     => esc_html__( 'Headline colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uk-heading-large' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s           = $this->get_render_settings();
		$eid         = $this->get_id();
		$video_desk  = media_url( $s['video_desktop'] ?? null );
		$video_mob   = media_url( $s['video_mobile'] ?? null ) ?: $video_desk;
		$split_video = $video_desk && $video_mob && $video_mob !== $video_desk;
		$logo_desk   = media_url( $s['logo_desktop'] ?? null );
		$logo_mob    = media_url( $s['logo_mobile'] ?? null ) ?: $logo_desk;
		$logo_alt    = $s['logo_alt'] ?? '';
		$words       = $s['headline_words'] ?? array();
		?>
		<style class="uk-margin-remove-adjacent">
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-logo-desktop .el-image { transform: translateY(-50%); max-width: 60vw; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-logo-mobile .el-image { transform: translateY(-50%); max-width: 60vw; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-overlay { position: relative; z-index: 1; margin-top: -100vh; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-word-last { margin-bottom: 15vh; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-hours-text { margin-bottom: 30vh; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-copy > * { position: relative; z-index: 1; }
		</style>

		<div class="uk-section-default uk-inverse-light uk-section uk-padding-remove-vertical pet-studio-hero-home" tm-header-transparent-noplaceholder>
			<div class="uk-grid-margin uk-grid tm-grid-expand uk-child-width-1-1">
				<div class="uk-width-1-1">
					<div class="uk-position-z-index uk-tile uk-padding-remove ps-hero-video-tile" uk-height-viewport="offset-top: !*;" uk-sticky="end: !.uk-section;">
						<?php if ( $video_desk ) : ?>
							<?php if ( $split_video ) : ?>
								<video class="uk-object-center-left uk-visible@s" src="<?php echo esc_url( $video_desk ); ?>" playsinline loop muted preload="none" width="2560" uk-cover></video>
								<video class="uk-object-center-left uk-hidden@s" src="<?php echo esc_url( $video_mob ); ?>" playsinline loop muted preload="none" width="2560" uk-cover></video>
							<?php else : ?>
								<video class="uk-object-center-left" src="<?php echo esc_url( $video_desk ); ?>" playsinline loop muted preload="none" width="2560" uk-cover></video>
							<?php endif; ?>
						<?php endif; ?>
						<div class="uk-panel uk-width-1-1"></div>
					</div>
				</div>
			</div>

			<div class="uk-grid-margin uk-grid tm-grid-expand uk-child-width-1-1 ps-hero-overlay">
				<div class="uk-light uk-width-1-1">
					<div class="uk-height-viewport uk-panel uk-flex uk-flex-middle">
						<div class="uk-panel uk-width-1-1">
							<?php if ( $logo_desk ) : ?>
								<div class="uk-position-absolute uk-width-1-1 uk-text-center uk-visible@s ps-hero-logo-desktop" uk-parallax="y: -80; scale: 0.5; rotate: -30; opacity: 1,0,0; blur: 50; easing: 0; start: 50vh + 50%" style="top: 50%; z-index: 0;" uk-scrollspy="target: [uk-scrollspy-class];">
									<img class="<?php echo esc_attr( lazy_load_exempt_class( 'el-image uk-text-primary' ) ); ?>" src="<?php echo esc_url( $logo_desk ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" width="650" height="138"<?php echo eager_media_attrs( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> uk-svg>
								</div>
							<?php endif; ?>
							<?php if ( $logo_mob ) : ?>
								<div class="uk-position-relative uk-margin uk-text-right uk-hidden@s ps-hero-logo-mobile" uk-parallax="y: -80; scale: 0.5; rotate: -30; opacity: 1,0,0; blur: 50; easing: 0; start: 50vh + 50%" style="top: 220px; z-index: 0;" uk-scrollspy="target: [uk-scrollspy-class];">
									<img class="<?php echo esc_attr( lazy_load_exempt_class( 'el-image uk-text-primary' ) ); ?>" src="<?php echo esc_url( $logo_mob ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" width="400" height="270"<?php echo eager_media_attrs( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> uk-svg>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div class="uk-container uk-container-expand uk-margin-remove-vertical ps-hero-copy">
				<div class="uk-grid tm-grid-expand uk-child-width-1-1">
					<div class="uk-light uk-width-1-1">
						<?php
						$word_count = count( $words );
						foreach ( $words as $i => $row ) :
							$is_last = ( $i === $word_count - 1 );
							?>
							<div class="uk-heading-large uk-text-center<?php echo $is_last ? ' ps-hero-word-last' : ''; ?>" uk-parallax="scale: 0.5; opacity: 1,0; blur: 50; easing: 0; start: 55vh + 50%">
								<?php echo esc_html( is_array( $row ) ? ( $row['word'] ?? '' ) : (string) $row ); ?>
							</div>
						<?php endforeach; ?>

						<?php if ( ! empty( $s['hours_title'] ) ) : ?>
							<div class="uk-h1 uk-margin-xlarge-top uk-margin-remove-bottom uk-text-center" uk-parallax="scale: 0.5; opacity: 1,0; blur: 50; easing: 0; start: 55vh + 50%">
								<?php echo esc_html( $s['hours_title'] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $s['hours_text'] ) ) : ?>
							<div class="uk-text-lead uk-margin-small uk-text-center ps-hero-hours-text" uk-parallax="scale: 0.5; opacity: 1,0; blur: 50; easing: 0; start: 55vh + 50%">
								<?php echo format_multiline_text( $s['hours_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
