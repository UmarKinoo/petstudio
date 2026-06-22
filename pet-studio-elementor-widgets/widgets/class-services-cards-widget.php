<?php
/**
 * Services cards — sticky heading + parallax service tiles.
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
use function Pet_Studio_Elementor\media_url;
use function Pet_Studio_Elementor\print_link_attributes;

defined( 'ABSPATH' ) || exit;

class Services_Cards_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_services_cards';
	}

	public function get_title(): string {
		return esc_html__( 'Services Cards', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'services', 'cards' );
	}

	protected function get_fixture_slug(): string {
		return 'services-cards';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'heading', array( 'label' => esc_html__( 'Heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading'] ?? 'Our' ) );
		$this->add_control( 'heading_accent', array( 'label' => esc_html__( 'Heading accent', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading_accent'] ?? 'Services' ) );

		$card_rep = new Repeater();
		$card_rep->add_control( 'image', array( 'label' => esc_html__( 'Image', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => '' ) ) );
		$card_rep->add_control( 'title', array( 'label' => esc_html__( 'Title', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$card_rep->add_control( 'link', array( 'label' => esc_html__( 'Link', 'pet-studio-elementor' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ) ) );
		$card_rep->add_control( 'button_text', array( 'label' => esc_html__( 'Button text', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => 'See More' ) );
		$card_rep->add_control( 'parallax_start', array( 'label' => esc_html__( 'Parallax start', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '62vh' ) );
		$card_rep->add_control( 'image_width', array( 'label' => esc_html__( 'Image width', 'pet-studio-elementor' ), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0 ) );
		$card_rep->add_control( 'image_height', array( 'label' => esc_html__( 'Image height', 'pet-studio-elementor' ), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0 ) );

		$cards_default = array();
		foreach ( $d['cards'] ?? array() as $card ) {
			$cards_default[] = array(
				'image'          => api_media_to_control( $card['image'] ?? null ),
				'title'          => $card['title'] ?? '',
				'link'           => api_link_to_control( $card['link'] ?? null ),
				'button_text'    => $card['button_text'] ?? 'See More',
				'parallax_start' => $card['parallax_start'] ?? '62vh',
				'image_width'    => (int) ( $card['image_width'] ?? 0 ),
				'image_height'   => (int) ( $card['image_height'] ?? 0 ),
			);
		}

		$this->add_control( 'cards', array( 'label' => esc_html__( 'Service cards', 'pet-studio-elementor' ), 'type' => Controls_Manager::REPEATER, 'fields' => $card_rep->get_controls(), 'default' => $cards_default ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .el-title.uk-heading-large' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$eid = $this->get_id();
		$cards = $s['cards'] ?? array();
		?>
		<style class="uk-margin-remove-adjacent">
			@media (min-width: 960px) {
				.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-services-spacer { height: 100vh; }
				.elementor-element-<?php echo esc_attr( (string) $eid ); ?> .ps-services-cards { margin-top: -100vh; height: 100vh; }
			}
		</style>

		<div class="uk-section-default uk-section uk-section-xlarge-top uk-padding-remove-bottom">
			<div class="uk-container uk-container-expand">
				<div class="uk-grid tm-grid-expand uk-child-width-1-1 uk-margin-xlarge ps-services-spacer">
					<div class="js-sticky uk-width-1-1">
						<div class="uk-panel uk-position-z-index" uk-sticky="offset: 50vh - 50%; end: !.js-sticky; media: @s;">
							<div class="uk-panel uk-margin-remove-first-child uk-margin uk-width-large uk-margin-auto uk-text-center" uk-parallax="opacity: 1,0; blur: 50; easing: 0; media: @m; target: !.tm-grid-expand&gt;*; start: 55vh; end: 100vh">
								<h2 class="el-title uk-heading-large uk-margin-top uk-margin-remove-bottom">
									<?php echo esc_html( $s['heading'] ?? '' ); ?>
									<span class="uk-text-primary"><?php echo esc_html( $s['heading_accent'] ?? '' ); ?></span>
								</h2>
							</div>
						</div>
					</div>
				</div>

				<div class="uk-grid-margin uk-grid tm-grid-expand uk-grid-column-collapse ps-services-cards" uk-grid>
					<?php foreach ( $cards as $card ) : ?>
						<?php
						$img = media_url( $card['image'] ?? null );
						if ( ! $img ) {
							continue;
						}
						$start = esc_attr( $card['parallax_start'] ?? '62vh' );
						$img_w = (int) ( $card['image_width'] ?? 0 );
						$img_h = (int) ( $card['image_height'] ?? 0 );
						$img_size = ( $img_w && $img_h )
							? ' width="' . esc_attr( (string) $img_w ) . '" height="' . esc_attr( (string) $img_h ) . '"'
							: '';
						?>
						<div class="uk-width-1-3@m">
							<div class="uk-position-z-index uk-panel" uk-sticky="offset: 50vh - 50%; end: !.uk-section;">
								<div class="uk-light uk-margin uk-text-center" uk-parallax="x: 100vw,0,0,-100vw; y: 0 68%,-1200; opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @s; target: !.uk-section; start: <?php echo $start; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
									<a class="uk-transition-toggle uk-inline-clip uk-link-toggle"<?php print_link_attributes( $card['link'] ?? null ); ?>>
										<img class="el-image uk-transition-scale-up uk-transition-opaque" src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $card['title'] ?? '' ); ?>" loading="lazy"<?php echo $img_size; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
										<div class="uk-position-bottom">
											<div class="uk-panel uk-padding-large uk-margin-remove-first-child">
												<div class="el-title uk-heading-medium uk-margin-top uk-margin-remove-bottom"><?php echo esc_html( $card['title'] ?? '' ); ?></div>
												<div class="uk-margin-small-top">
													<div class="el-link uk-button uk-button-text uk-button-large"><?php echo esc_html( $card['button_text'] ?? 'See More' ); ?></div>
												</div>
											</div>
										</div>
									</a>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
