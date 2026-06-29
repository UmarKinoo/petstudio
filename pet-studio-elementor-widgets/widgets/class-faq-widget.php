<?php
/**
 * FAQ accordion section.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Pet_Studio_Elementor\Widget_Base;

use function Pet_Studio_Elementor\render_rich_text;

defined( 'ABSPATH' ) || exit;

class Faq_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_faq';
	}

	public function get_title(): string {
		return esc_html__( 'FAQ', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-help-o';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'faq', 'accordion', 'questions' );
	}

	protected function get_fixture_slug(): string {
		return 'faq';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'heading', array( 'label' => esc_html__( 'Heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading'] ?? 'Frequently Asked Questions' ) );
		$this->add_control( 'heading_accent', array( 'label' => esc_html__( 'Accent word', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading_accent'] ?? '' ) );

		$rep = new Repeater();
		$rep->add_control( 'question', array( 'label' => esc_html__( 'Question', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true ) );
		$rep->add_control( 'answer', array( 'label' => esc_html__( 'Answer', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => '', 'rows' => 4 ) );

		$items_default = array();
		foreach ( $d['items'] ?? array() as $item ) {
			$items_default[] = array(
				'question' => $item['question'] ?? '',
				'answer'   => $item['answer'] ?? '',
			);
		}

		$this->add_control( 'items', array( 'label' => esc_html__( 'FAQ items', 'pet-studio-elementor' ), 'type' => Controls_Manager::REPEATER, 'fields' => $rep->get_controls(), 'default' => $items_default, 'title_field' => '{{{ question }}}' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .ps-faq-heading' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'question_typography', 'selector' => '{{WRAPPER}} .uk-accordion-title' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'answer_typography', 'selector' => '{{WRAPPER}} .uk-accordion-content' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s     = $this->get_render_settings();
		$items = array_filter(
			(array) ( $s['items'] ?? array() ),
			static function ( $item ): bool {
				return is_array( $item ) && '' !== trim( (string) ( $item['question'] ?? '' ) );
			}
		);

		if ( ! $items ) {
			return;
		}
		?>
		<div class="uk-section-default uk-section uk-section-large ps-faq-section">
			<div class="uk-container">
				<?php if ( ! empty( $s['heading'] ) ) : ?>
					<h2 class="ps-faq-heading uk-heading-medium uk-margin-large-bottom">
						<?php echo esc_html( $s['heading'] ); ?>
						<?php if ( ! empty( $s['heading_accent'] ) ) : ?>
							<span class="uk-text-primary"><?php echo esc_html( $s['heading_accent'] ); ?></span>
						<?php endif; ?>
					</h2>
				<?php endif; ?>
				<ul class="ps-faq-accordion uk-accordion" uk-accordion="multiple: true">
					<?php foreach ( $items as $item ) : ?>
						<li>
							<a class="uk-accordion-title" href="#"><?php echo esc_html( $item['question'] ?? '' ); ?></a>
							<div class="uk-accordion-content">
								<?php echo render_rich_text( '<p>' . esc_html( $item['answer'] ?? '' ) . '</p>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php
	}
}
