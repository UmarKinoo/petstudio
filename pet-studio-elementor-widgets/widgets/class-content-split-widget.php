<?php
/**
 * Content split — two-column text + images.
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
use function Pet_Studio_Elementor\section_tone_class;

defined( 'ABSPATH' ) || exit;

class Content_Split_Widget extends Widget_Base {

	public function get_name(): string {
		return 'pet_studio_content_split';
	}

	public function get_title(): string {
		return esc_html__( 'Content Split', 'pet-studio-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-columns';
	}

	public function get_keywords(): array {
		return array( 'pet studio', 'content', 'split', 'columns' );
	}

	protected function get_fixture_slug(): string {
		return 'content-split';
	}

	protected function register_controls(): void {
		$d = $this->get_fixture_defaults();

		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Content', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'section_tone', array(
			'label' => esc_html__( 'Section tone', 'pet-studio-elementor' ),
			'type' => Controls_Manager::SELECT,
			'default' => $d['section_tone'] ?? 'default',
			'options' => array( 'default' => 'Default', 'muted' => 'Muted', 'secondary' => 'Secondary' ),
		) );

		$this->add_control( 'heading', array( 'label' => esc_html__( 'Heading', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => $d['heading'] ?? '' ) );
		$this->add_control( 'blockquote', array( 'label' => esc_html__( 'Blockquote', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $d['blockquote'] ?? '', 'rows' => 3 ) );
		$this->add_control( 'body', array( 'label' => esc_html__( 'Body', 'pet-studio-elementor' ), 'type' => Controls_Manager::WYSIWYG, 'default' => $d['body'] ?? '' ) );

		$bullet_rep = new Repeater();
		$bullet_rep->add_control( 'item', array( 'label' => esc_html__( 'Item', 'pet-studio-elementor' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$bullet_default = array();
		foreach ( $d['bullet_list'] ?? array() as $item ) {
			$bullet_default[] = array( 'item' => is_string( $item ) ? $item : ( $item['item'] ?? '' ) );
		}
		$this->add_control( 'bullet_list', array( 'label' => esc_html__( 'Bullet list', 'pet-studio-elementor' ), 'type' => Controls_Manager::REPEATER, 'fields' => $bullet_rep->get_controls(), 'default' => $bullet_default ) );

		$this->add_control( 'image_layout', array(
			'label' => esc_html__( 'Image layout', 'pet-studio-elementor' ),
			'type' => Controls_Manager::SELECT,
			'default' => $d['image_layout'] ?? '2-stacked',
			'options' => array( '1-right' => 'Single image', '2-stacked' => 'Two stacked', '1+1' => 'Large + small' ),
		) );

		$img_rep = new Repeater();
		$img_rep->add_control( 'image', array( 'label' => esc_html__( 'Image', 'pet-studio-elementor' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => '' ) ) );
		$img_default = array();
		foreach ( $d['images'] ?? array() as $img ) {
			$img_default[] = array( 'image' => api_media_to_control( $img ) );
		}
		$this->add_control( 'images', array( 'label' => esc_html__( 'Images', 'pet-studio-elementor' ), 'type' => Controls_Manager::REPEATER, 'fields' => $img_rep->get_controls(), 'default' => $img_default ) );
		$this->add_control( 'reverse_columns', array( 'label' => esc_html__( 'Reverse columns', 'pet-studio-elementor' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ) );

		$this->end_controls_section();
		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'pet-studio-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .el-title' ) );
		$this->end_controls_section();
		$this->register_style_controls( 'section_style_accent' );
	}

	protected function render(): void {
		$s = $this->get_render_settings();
		$tone = section_tone_class( (string) ( $s['section_tone'] ?? 'default' ) );
		$images = $s['images'] ?? array();
		$layout = $s['image_layout'] ?? '2-stacked';
		$text_first = ( $s['reverse_columns'] ?? '' ) !== 'yes';
		?>
		<div class="<?php echo esc_attr( $tone ); ?> uk-section">
			<div class="uk-container uk-container-expand uk-margin-xlarge-top uk-margin-remove-bottom">
				<div class="uk-grid tm-grid-expand uk-grid-medium" uk-grid>
					<?php if ( $text_first ) : ?>
						<?php $this->render_text_column( $s ); ?>
						<?php $this->render_image_column( $images, $layout ); ?>
					<?php else : ?>
						<?php $this->render_image_column( $images, $layout ); ?>
						<?php $this->render_text_column( $s ); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @param array<string, mixed> $s Settings.
	 */
	private function render_text_column( array $s ): void {
		?>
		<div class="uk-width-1-2@m">
			<div class="uk-panel uk-margin-remove-first-child uk-margin uk-width-xlarge uk-margin-auto uk-text-left">
				<?php if ( ! empty( $s['heading'] ) ) : ?>
					<h2 class="el-title uk-heading-large uk-margin-top uk-margin-remove-bottom"><?php echo esc_html( $s['heading'] ); ?></h2>
				<?php endif; ?>
				<div class="el-content uk-panel uk-margin-large-top">
					<?php if ( ! empty( $s['blockquote'] ) ) : ?>
						<blockquote><p><strong><?php echo esc_html( $s['blockquote'] ); ?></strong></p></blockquote>
					<?php endif; ?>
					<?php if ( ! empty( $s['body'] ) ) : ?>
						<?php render_rich_text( $s['body'] ); ?>
					<?php endif; ?>
					<?php if ( ! empty( $s['bullet_list'] ) ) : ?>
						<ul>
							<?php foreach ( $s['bullet_list'] as $row ) : ?>
								<?php if ( ! empty( $row['item'] ) ) : ?>
									<li><?php echo esc_html( $row['item'] ); ?></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
			<?php
			// Large hero image below text (1+1 layout).
			if ( ( $s['image_layout'] ?? '' ) === '1+1' && ! empty( $s['images'][0]['image'] ) ) :
				$url = media_url( $s['images'][0]['image'] );
				if ( $url ) :
					?>
					<div class="uk-margin-xlarge uk-text-center" uk-parallax="opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @l">
						<img class="el-image" src="<?php echo esc_url( $url ); ?>" alt="" loading="lazy">
					</div>
					<?php
				endif;
			endif;
			?>
		</div>
		<?php
	}

	/**
	 * @param array<int, array<string, mixed>> $images Image repeater.
	 * @param string                          $layout Layout key.
	 */
	private function render_image_column( array $images, string $layout ): void {
		$start = ( '1+1' === $layout ) ? 1 : 0;
		?>
		<div class="uk-width-1-2@m">
			<?php for ( $i = $start; $i < count( $images ); $i++ ) : ?>
				<?php
				$url = media_url( $images[ $i ]['image'] ?? null );
				if ( ! $url ) {
					continue;
				}
				?>
				<div class="uk-margin uk-text-center" uk-parallax="y: 100,-150; opacity: 1 70%,0; blur: 0 70%,100; easing: 0; media: @l">
					<img class="el-image" src="<?php echo esc_url( $url ); ?>" alt="" loading="lazy">
				</div>
			<?php endfor; ?>
		</div>
		<?php
	}
}
