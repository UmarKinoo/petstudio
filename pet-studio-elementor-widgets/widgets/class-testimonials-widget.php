<?php
/**
 * Testimonials slider.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\api_media_to_control;
use function Pet_Studio_Elementor\media_url;
use function Pet_Studio_Elementor\render_rich_text;

defined( 'ABSPATH' ) || exit;

class Testimonials_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_testimonials';
	}

	public function get_title(): string {
		return esc_html__( 'Testimonials', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-testimonial-carousel';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'testimonials', 'reviews', 'slider' );
	}

	protected function get_fixture_slug(): string {
		return 'testimonials';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );

		$rep = new Repeater();
		$rep->add_control( 'icon', array( 'label' => esc_html__( 'Icon', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => '' ) ) );
		$rep->add_control( 'title', array( 'label' => esc_html__( 'Title', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$rep->add_control( 'quote', array( 'label' => esc_html__( 'Quote', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => '', 'rows' => 4 ) );
		$rep->add_control( 'author', array( 'label' => esc_html__( 'Author', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );

		$reviews_default = array();
		foreach ( $d['reviews'] ?? array() as $review ) {
			$reviews_default[] = array(
				'icon'   => api_media_to_control( $review['icon'] ?? null ),
				'title'  => $review['title'] ?? '',
				'quote'  => $review['quote'] ?? '',
				'author' => $review['author'] ?? '',
			);
		}

		$this->add_control( 'reviews', array( 'label' => esc_html__( 'Reviews', 'pet-studio-elementor' ), 'type' => Controls_Manager::REPEATER, 'fields' => $rep->get_controls(), 'default' => $reviews_default ) );
		$this->add_control( 'autoplay', array( 'label' => esc_html__( 'Autoplay', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['autoplay'] ) ? 'yes' : '' ) );
		$this->add_control( 'interval_ms', array( 'label' => esc_html__( 'Interval (ms)', 'pet-studio-elementor' ), 'type' => Controls_Manager::NUMBER, 'default' => (int) ( $d['interval_ms'] ?? 7000 ) ) );
		$this->add_control( 'show_dots', array( 'label' => esc_html__( 'Show dots', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => ! empty( $d['show_dots'] ) ? 'yes' : '' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Review title typography', 'pet-studio-elementor' ),
				'selector'       => '{{WRAPPER}} .ps-testimonial-title',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Noto Sans' ),
					'font_size'   => array( 'default' => array( 'size' => 20, 'unit' => 'px' ) ),
					'font_weight' => array( 'default' => '600' ),
					'line_height' => array( 'default' => array( 'size' => 1.4, 'unit' => 'em' ) ),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'quote_typography',
				'label'          => esc_html__( 'Review text typography', 'pet-studio-elementor' ),
				'selector'       => '{{WRAPPER}} .ps-testimonial-quote, {{WRAPPER}} .ps-testimonial-quote p',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Noto Sans' ),
					'font_size'   => array( 'default' => array( 'size' => 16, 'unit' => 'px' ) ),
					'font_weight' => array( 'default' => '400' ),
					'line_height' => array( 'default' => array( 'size' => 1.5, 'unit' => 'em' ) ),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'author_typography',
				'label'          => esc_html__( 'Author typography', 'pet-studio-elementor' ),
				'selector'       => '{{WRAPPER}} .ps-testimonial-author',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Noto Sans' ),
					'font_size'   => array( 'default' => array( 'size' => 16, 'unit' => 'px' ) ),
					'font_weight' => array( 'default' => '400' ),
					'line_height' => array( 'default' => array( 'size' => 1.4, 'unit' => 'em' ) ),
				),
			)
		);
		$this->add_control(
			'quote_color',
			array(
				'label'     => esc_html__( 'Review text colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .ps-testimonial-quote'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .ps-testimonial-quote p'   => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Review title colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .ps-testimonial-title' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'author_color',
			array(
				'label'     => esc_html__( 'Author colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#B4ADA7',
				'selectors' => array(
					'{{WRAPPER}} .ps-testimonial-author' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'divider_color',
			array(
				'label'     => esc_html__( 'Title divider colour', 'pet-studio-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF90AA',
				'selectors' => array(
					'{{WRAPPER}} .ps-testimonial-title.uk-heading-divider' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$reviews = $s['reviews'] ?? array();
		if ( empty( $reviews ) ) {
			return;
		}

		$slider_opts = array( 'center: 1' );
		if ( ( $s['autoplay'] ?? '' ) === 'yes' ) {
			$slider_opts[] = 'autoplay: 1';
			$interval = (int) ( $s['interval_ms'] ?? 7000 );
			if ( $interval > 0 ) {
				$slider_opts[] = 'autoplay-interval: ' . $interval;
			}
		}
		$slider_attr = implode( '; ', $slider_opts );
		?>
		<div class="uk-section-muted uk-section" uk-scrollspy="target: [uk-scrollspy-class]; cls: uk-animation-scale-up; delay: false;">
			<div class="uk-container">
				<div class="uk-grid-margin uk-grid tm-grid-expand uk-child-width-1-1">
					<div class="uk-width-1-1">
						<div class="uk-slider-container uk-margin uk-text-left" uk-slider="<?php echo esc_attr( $slider_attr ); ?>" uk-scrollspy-class>
							<div class="uk-position-relative">
								<div class="uk-slider-items uk-grid">
									<?php foreach ( $reviews as $review ) : ?>
										<?php $icon = media_url( $review['icon'] ?? null ); ?>
										<div class="uk-width-1-1 uk-width-1-3@m uk-flex">
											<div class="el-item uk-width-1-1 uk-panel uk-margin-remove-first-child">
												<?php if ( $icon ) : ?>
													<img class="el-image" src="<?php echo esc_url( $icon ); ?>" alt="" width="60" height="52">
												<?php endif; ?>
												<h3 class="el-title uk-h4 uk-heading-divider uk-font-primary uk-margin-top uk-margin-remove-bottom ps-testimonial-title"><?php echo esc_html( $review['title'] ?? '' ); ?></h3>
												<?php if ( ! empty( $review['quote'] ) ) : ?>
													<div class="el-content uk-panel uk-margin-top ps-testimonial-quote"><?php render_rich_text( wpautop( esc_html( $review['quote'] ) ) ); ?></div>
												<?php endif; ?>
												<?php if ( ! empty( $review['author'] ) ) : ?>
													<div class="el-meta uk-text-meta uk-margin-top ps-testimonial-author"><?php echo esc_html( $review['author'] ); ?></div>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							<?php if ( ( $s['show_dots'] ?? '' ) === 'yes' ) : ?>
								<div class="uk-margin-top uk-visible@s" uk-inverse>
									<ul class="el-nav uk-slider-nav uk-dotnav uk-flex-right" uk-margin></ul>
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
