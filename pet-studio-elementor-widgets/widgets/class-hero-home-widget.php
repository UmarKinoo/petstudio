<?php
/**
 * Hero — Home (video + text headline + parallax highlight words).
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
use function Pet_Studio_Elementor\render_cta_group;
use function Pet_Studio_Elementor\format_multiline_text;
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
				'label'       => esc_html__( 'Background video (desktop)', 'pet-studio-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'default'     => api_media_to_control( $defaults['video_desktop'] ?? null ),
			)
		);

		$this->add_control(
			'video_mobile',
			array(
				'label'       => esc_html__( 'Background video (mobile)', 'pet-studio-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'default'     => api_media_to_control( $defaults['video_mobile'] ?? null ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hero_copy',
			array(
				'label' => esc_html__( 'Hero heading', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'headline',
			array(
				'label'       => esc_html__( 'Heading (white)', 'pet-studio-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $defaults['headline'] ?? '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'headline_accent',
			array(
				'label'       => esc_html__( 'Heading accent (pink)', 'pet-studio-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $defaults['headline_accent'] ?? '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'supporting_copy',
			array(
				'label'   => esc_html__( 'Subheading', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => $defaults['supporting_copy'] ?? '',
				'rows'    => 4,
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'   => esc_html__( 'Primary CTA text', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['cta_text'] ?? 'Book Grooming',
			)
		);
		$this->add_control(
			'cta_link',
			array(
				'label'     => esc_html__( 'Primary CTA link', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::URL,
				'default'   => api_link_to_control( $defaults['cta_link'] ?? array( 'url' => '/contact/' ) ),
				'condition' => array( 'cta_text!' => '' ),
			)
		);
		$this->add_control(
			'cta2_text',
			array(
				'label'   => esc_html__( 'Secondary CTA text', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['cta2_text'] ?? '',
			)
		);
		$this->add_control(
			'cta2_link',
			array(
				'label'     => esc_html__( 'Secondary CTA link', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::URL,
				'default'   => api_link_to_control( $defaults['cta2_link'] ?? null ),
				'condition' => array( 'cta2_text!' => '' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_headlines',
			array(
				'label' => esc_html__( 'Highlights & hours', 'pet-studio-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$words_rep = new Repeater();
		$words_rep->add_control(
			'word',
			array(
				'label'   => esc_html__( 'Highlight word', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Experienced.',
			)
		);
		$words_rep->add_control(
			'subtitle',
			array(
				'label'   => esc_html__( 'Highlight subtitle', 'pet-studio-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$words_default = array();
		foreach ( $defaults['headline_words'] ?? array() as $word ) {
			if ( is_array( $word ) ) {
				$words_default[] = array(
					'word'     => (string) ( $word['word'] ?? '' ),
					'subtitle' => (string) ( $word['subtitle'] ?? '' ),
				);
			} else {
				$words_default[] = array(
					'word'     => (string) $word,
					'subtitle' => '',
				);
			}
		}

		$this->add_control(
			'headline_words',
			array(
				'label'   => esc_html__( 'Three highlights', 'pet-studio-elementor' ),
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
				'label'    => esc_html__( 'Hero heading typography', 'pet-studio-elementor' ),
				'selector' => '{{WRAPPER}} .ps-hero-h1',
			)
		);

		$this->add_control(
			'headline_color',
			array(
				'label'     => esc_html__( 'Heading colour (white)', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ps-hero-h1' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'headline_accent_color',
			array(
				'label'     => esc_html__( 'Heading accent colour (pink)', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff90aa',
				'selectors' => array(
					'{{WRAPPER}} .ps-hero-h1 .uk-text-primary' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'support_typography',
				'label'    => esc_html__( 'Subheading typography', 'pet-studio-elementor' ),
				'selector' => '{{WRAPPER}} .ps-hero-support',
			)
		);

		$this->add_control(
			'support_color',
			array(
				'label'     => esc_html__( 'Subheading colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ps-hero-support' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'highlight_typography',
				'label'    => esc_html__( 'Highlight word typography', 'pet-studio-elementor' ),
				'selector' => '{{WRAPPER}} .uk-heading-large',
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
		$cta_text    = trim( (string) ( $s['cta_text'] ?? '' ) );
		$cta_link    = is_array( $s['cta_link'] ?? null ) ? $s['cta_link'] : null;
		$cta2_text   = trim( (string) ( $s['cta2_text'] ?? '' ) );
		$cta2_link   = is_array( $s['cta2_link'] ?? null ) ? $s['cta2_link'] : null;
		$words       = $s['headline_words'] ?? array();
		$headline    = trim( (string) ( $s['headline'] ?? '' ) );
		$accent      = trim( (string) ( $s['headline_accent'] ?? '' ) );
		$support     = trim( (string) ( $s['supporting_copy'] ?? '' ) );
		$ctas        = array();
		if ( $cta_text !== '' ) {
			$ctas[] = array( 'text' => $cta_text, 'link' => $cta_link, 'style' => 'pill' );
		}
		if ( $cta2_text !== '' ) {
			$ctas[] = array( 'text' => $cta2_text, 'link' => $cta2_link, 'style' => 'text' );
		}
		$has_title = ( $headline !== '' || $accent !== '' || $support !== '' || ! empty( $ctas ) );
		?>
		<style class="uk-margin-remove-adjacent">
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-title-desktop {
				left: 0;
				right: 0;
				top: 50%;
				text-align: center;
			}
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-title-desktop .ps-hero-brand {
				display: inline-flex;
				flex-direction: column;
				align-items: center;
				transform: translateY(-50%);
				text-align: center;
			}
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-title-mobile .ps-hero-brand {
				transform: none;
			}
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-overlay { position: relative; z-index: 1; margin-top: -100vh; overflow-x: clip; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-word-last { margin-bottom: 15vh; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-hours-text { margin-bottom: 30vh; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-copy > * { position: relative; z-index: 1; }
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-intro {
				max-width: min(92vw, 52rem);
				margin-left: auto;
				margin-right: auto;
				padding: 0 1rem;
			}
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-h1 {
				margin: 0;
				color: #ffffff;
			}
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-h1 .uk-text-primary {
				color: #ff90aa;
			}
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-support {
				margin: 1rem auto 0;
				max-width: 42rem;
				color: #ffffff;
				font-size: 1.25rem;
				line-height: 1.5;
				font-weight: 400;
			}
			.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-highlight-sub {
				display: block;
				font-size: clamp(1rem, 2vw, 1.35rem);
				font-weight: 400;
				line-height: 1.4;
				margin-top: 0.35rem;
				opacity: 0.9;
			}
			@media (min-width: 1200px) {
				.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-hero-support {
					font-size: 1.35rem;
				}
			}
		</style>

		<div class="uk-section-default uk-inverse-light uk-section uk-padding-remove-vertical pet-studio-hero-home" tm-header-transparent-noplaceholder>
			<div class="uk-grid-margin uk-grid tm-grid-expand uk-child-width-1-1">
				<div class="uk-width-1-1">
					<div class="uk-position-z-index uk-tile uk-padding-remove ps-hero-video-tile" uk-height-viewport="offset-top: !*;" uk-sticky="end: !.uk-section;">
						<?php if ( $video_desk ) : ?>
							<?php if ( $split_video ) : ?>
								<video class="uk-object-center-left uk-visible@s" src="<?php echo esc_url( $video_desk ); ?>" playsinline muted preload="auto" width="2560" uk-cover></video>
								<video class="uk-object-center-left uk-hidden@s" src="<?php echo esc_url( $video_mob ); ?>" playsinline muted preload="auto" width="2560" uk-cover></video>
							<?php else : ?>
								<video class="uk-object-center-left" src="<?php echo esc_url( $video_desk ); ?>" playsinline muted preload="auto" width="2560" uk-cover></video>
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
							<?php if ( $has_title ) : ?>
								<div class="uk-position-absolute uk-width-1-1 uk-text-center uk-visible@s ps-hero-title-desktop" uk-parallax="y: -80; scale: 0.5; rotate: -30; opacity: 1,0,0; blur: 50; easing: 0; start: 50vh + 50%" style="top: 50%; z-index: 0;" uk-scrollspy="target: [uk-scrollspy-class];">
									<div class="ps-hero-brand">
										<?php $this->render_hero_title_block( $headline, $accent, $support, $ctas ); ?>
									</div>
								</div>
								<div class="uk-position-relative uk-margin uk-text-center uk-hidden@s ps-hero-title-mobile" uk-parallax="y: -80; scale: 0.5; rotate: -30; opacity: 1,0,0; blur: 50; easing: 0; start: 50vh + 50%" style="z-index: 0;" uk-scrollspy="target: [uk-scrollspy-class];">
									<div class="ps-hero-brand">
										<?php $this->render_hero_title_block( $headline, $accent, $support, $ctas ); ?>
									</div>
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
							$is_last  = ( $i === $word_count - 1 );
							$word     = is_array( $row ) ? (string) ( $row['word'] ?? '' ) : (string) $row;
							$subtitle = is_array( $row ) ? trim( (string) ( $row['subtitle'] ?? '' ) ) : '';
							?>
							<div class="uk-heading-large uk-text-center<?php echo $is_last ? ' ps-hero-word-last' : ''; ?>" uk-parallax="scale: 0.5; opacity: 1,0; blur: 50; easing: 0; start: 55vh + 50%">
								<?php echo esc_html( $word ); ?>
								<?php if ( $subtitle !== '' ) : ?>
									<span class="ps-hero-highlight-sub"><?php echo esc_html( $subtitle ); ?></span>
								<?php endif; ?>
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

	/**
	 * @param array<int, array<string, mixed>> $ctas CTA list.
	 */
	private function render_hero_title_block( string $headline, string $accent, string $support, array $ctas ): void {
		?>
		<div class="ps-hero-intro">
			<?php if ( $headline !== '' || $accent !== '' ) : ?>
				<h1 class="ps-hero-h1 uk-heading-large uk-margin-remove-bottom">
					<?php echo esc_html( $headline ); ?>
					<?php if ( $accent !== '' ) : ?>
						<span class="uk-text-primary"><?php echo ( $headline !== '' ? ' ' : '' ) . esc_html( $accent ); ?></span>
					<?php endif; ?>
				</h1>
			<?php endif; ?>
			<?php if ( $support !== '' ) : ?>
				<p class="ps-hero-support uk-text-lead uk-margin-small-top"><?php echo esc_html( $support ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $ctas ) ) : ?>
				<div class="ps-hero-cta">
					<?php render_cta_group( $ctas ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
