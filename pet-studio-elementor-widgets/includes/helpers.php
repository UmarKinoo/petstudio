<?php
/**
 * Shared render helpers.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor;

defined( 'ABSPATH' ) || exit;

/**
 * Render a media array as img or video src.
 *
 * @param array|null $media   { id, url }.
 * @param string     $fallback Fallback URL.
 */
/**
 * Extra classes so cache plugins (LiteSpeed etc.) do not lazy-load critical images.
 */
function lazy_load_exempt_class( string $classes = '' ): string {
	return trim( 'ps-no-lazy skip-lazy litespeed-no-lazy ' . $classes );
}

/**
 * Attributes for above-fold images and uk-svg (lazy optimizers break uk-svg).
 */
function eager_media_attrs( bool $high_priority = false ): string {
	$attrs = ' loading="eager" data-no-lazy="1"';
	if ( $high_priority ) {
		$attrs .= ' fetchpriority="high"';
	}
	return $attrs;
}

/**
 * Resolve a readable SVG path from a media URL (uploads or bundled demo asset).
 */
function resolve_svg_file_path( string $url ): string {
	$url = strtok( $url, '?' ) ?: $url;

	static $bundled = array(
		'Liza_signature_pink_v06.svg' => 'assets/demo-media/logos/Liza_signature_pink_v06.svg',
	);

	foreach ( $bundled as $needle => $relative ) {
		if ( false !== strpos( $url, $needle ) ) {
			$path = PET_STUDIO_EW_PATH . $relative;
			if ( is_readable( $path ) ) {
				return $path;
			}
		}
	}

	$upload = wp_upload_dir();
	if ( ! empty( $upload['baseurl'] ) && str_contains( $url, $upload['baseurl'] ) ) {
		$path = str_replace( $upload['baseurl'], $upload['basedir'], $url );
		if ( is_readable( $path ) ) {
			return $path;
		}
	}

	return '';
}

/**
 * Echo inline SVG markup (immune to LiteSpeed/img lazy optimizers).
 *
 * @return bool True when SVG was rendered.
 */
function render_inline_svg( string $url, string $class = '', int $width = 0, int $height = 0 ): bool {
	$path = resolve_svg_file_path( $url );
	if ( '' === $path ) {
		return false;
	}

	$svg = (string) file_get_contents( $path );
	if ( '' === $svg || ! preg_match( '/<svg\b/i', $svg ) ) {
		return false;
	}

	$attrs = '';
	if ( '' !== $class ) {
		$attrs .= ' class="' . esc_attr( $class ) . '"';
	}
	if ( $width > 0 ) {
		$attrs .= ' width="' . (int) $width . '"';
	}
	if ( $height > 0 ) {
		$attrs .= ' height="' . (int) $height . '"';
	}
	$attrs .= ' aria-hidden="true" focusable="false"';

	$svg = preg_replace( '/<svg\b/i', '<svg' . $attrs, $svg, 1 );
	if ( ! is_string( $svg ) ) {
		return false;
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin/upload SVG source.
	echo $svg;

	return true;
}

function media_url( ?array $media, string $fallback = '' ): string {
	if ( empty( $media['url'] ) ) {
		return $fallback;
	}

	if ( ! empty( $media['id'] ) ) {
		$attached = wp_get_attachment_url( (int) $media['id'] );
		if ( $attached ) {
			return $attached;
		}
	}

	return (string) $media['url'];
}

/**
 * Footer should use the compact wordmark — not the tall hero/mobile tagline SVG.
 */
function footer_logo_url( string $url ): string {
	if ( $url === '' ) {
		return '';
	}

	if ( preg_match( '/tagline_MOBILE_600|tagline\+box|tagline_%2B/i', $url ) ) {
		$uploads = wp_upload_dir();
		$base    = trailingslashit( $uploads['baseurl'] ) . 'pet-studio/media/logos/the_pet_studio_logo_400px.svg';

		return $base;
	}

	return $url;
}

/**
 * Book Now / booking CTAs should open the contact page — not scroll to the footer.
 *
 * @param array|null $link Elementor URL control shape.
 * @return array<string, mixed>
 */
function normalize_booking_link( ?array $link ): array {
	$url = trim( (string) ( $link['url'] ?? '' ) );

	if ( $url === '#ps-contact' || $url === '#contact' ) {
		return array(
			'url'         => '/contact/',
			'is_external' => false,
			'nofollow'    => false,
		);
	}

	return is_array( $link ) ? $link : array( 'url' => $url );
}

/**
 * Render link attributes from URL control or API link object.
 *
 * @param array|null $link Link settings.
 */
function link_attrs( ?array $link ): array {
	$link = Content_Normalizer::normalize_behaviour_link( $link );
	$url = (string) ( $link['url'] ?? '#' );

	// esc_url() strips bare fragment links (#anchor) — keep in-page scroll targets intact.
	if ( str_starts_with( $url, '#' ) && strlen( $url ) > 1 ) {
		$href = esc_attr( $url );
	} else {
		$href = esc_url( $url );
	}

	return array(
		'href'   => $href,
		'target' => ! empty( $link['is_external'] ) ? '_blank' : '',
		'rel'    => ! empty( $link['nofollow'] ) ? 'nofollow noopener noreferrer' : ( ! empty( $link['is_external'] ) ? 'noopener noreferrer' : '' ),
	);
}

/**
 * Convert API media object to Elementor MEDIA control default shape.
 *
 * @param array|null $api_media API media.
 */
function api_media_to_control( ?array $api_media ): array {
	if ( empty( $api_media['url'] ) ) {
		return array( 'url' => '', 'id' => '' );
	}

	return array(
		'url' => (string) $api_media['url'],
		'id'  => isset( $api_media['id'] ) ? (string) $api_media['id'] : '',
	);
}

/**
 * Print href/target/rel for anchor tags.
 *
 * @param array|null $link Link settings.
 */
function print_link_attributes( ?array $link ): void {
	$attrs = link_attrs( $link );
	echo ' href="' . $attrs['href'] . '"';
	if ( $attrs['target'] ) {
		echo ' target="' . esc_attr( $attrs['target'] ) . '"';
	}
	if ( $attrs['rel'] ) {
		echo ' rel="' . esc_attr( $attrs['rel'] ) . '"';
	}
}

/**
 * Render a group of section CTA buttons.
 *
 * Each CTA: text (label), link (Elementor URL control shape), style ('pill'|'text').
 * Empty-text CTAs are skipped. In-page anchor links (href starting "#") get
 * UIkit smooth-scroll with a sticky-header offset.
 *
 * @param array<int, array<string, mixed>> $ctas CTA definitions.
 */
function render_cta_group( array $ctas ): void {
	$items = array();
	foreach ( $ctas as $cta ) {
		if ( ! empty( $cta['text'] ) ) {
			$items[] = $cta;
		}
	}
	if ( empty( $items ) ) {
		return;
	}
	echo '<div class="ps-cta-group">';
	foreach ( $items as $cta ) {
		$is_text = ( $cta['style'] ?? 'pill' ) === 'text';
		$class   = $is_text ? 'el-link uk-button uk-button-text' : 'el-link uk-button ps-book-now-btn';
		$link    = normalize_booking_link( is_array( $cta['link'] ?? null ) ? $cta['link'] : null );
		$url     = (string) ( $link['url'] ?? '' );
		echo '<a class="' . esc_attr( $class ) . '"';
		print_link_attributes( $link );
		if ( '' !== $url && '#' === $url[0] ) {
			echo ' uk-scroll="offset: 100"';
		}
		echo '>' . esc_html( (string) $cta['text'] ) . '</a>';
	}
	echo '</div>';
}

/**
 * Map social network key to UIkit icon name (Kojiro set).
 */
function social_icon_name( string $network ): string {
	$map = array(
		'tiktok'    => 'tiktok',
		'instagram' => 'instagram',
		'facebook'  => 'facebook',
	);
	return $map[ $network ] ?? $network;
}

/**
 * Elementor URL control default from API link array.
 *
 * @param array|null $link API link.
 */
function api_link_to_control( ?array $link ): array {
	if ( empty( $link['url'] ) ) {
		return array( 'url' => '#', 'is_external' => false, 'nofollow' => false );
	}
	return array(
		'url'         => (string) $link['url'],
		'is_external' => ! empty( $link['is_external'] ),
		'nofollow'    => ! empty( $link['nofollow'] ),
	);
}

/**
 * Build tel: href from display phone number.
 */
function phone_tel_href( string $phone ): string {
	$digits = preg_replace( '/\D+/', '', $phone );
	if ( '0' === substr( $digits, 0, 1 ) ) {
		$digits = '44' . substr( $digits, 1 );
	}
	return 'tel:+' . $digits;
}

/**
 * Escape and preserve line breaks for plain text blocks.
 */
function format_multiline_text( string $text ): string {
	return nl2br( esc_html( $text ) );
}

/**
 * UIkit section tone class from preset key.
 */
function section_tone_class( string $tone ): string {
	$map = array(
		'default'   => 'uk-section-default',
		'muted'     => 'uk-section-muted',
		'secondary' => 'uk-section-secondary',
		'primary'   => 'uk-section-primary',
	);
	return $map[ $tone ] ?? 'uk-section-default';
}

/**
 * Output Elementor WYSIWYG / API HTML safely.
 */
function render_rich_text( string $html ): void {
	echo wp_kses_post( $html );
}

/**
 * Whether an Elementor switcher (or fixture bool) is on.
 *
 * @param mixed $value Panel value.
 */
function switcher_enabled( $value, bool $default = true ): bool {
	if ( 'no' === $value || false === $value || 0 === $value || '0' === $value ) {
		return false;
	}
	if ( 'yes' === $value || true === $value || 1 === $value || '1' === $value ) {
		return true;
	}

	return $default;
}

/**
 * Mirror width/height for decorative dog divider PNGs (from original YOOtheme markup).
 *
 * @return array{width:int,height:int}|null
 */
function dog_icon_dimensions( string $url ): ?array {
	static $map = array(
		'icon_dog_01.png' => array( 105, 98 ),
		'icon_dog_02.png' => array( 115, 101 ),
		'icon_dog_03.png' => array( 101, 54 ),
		'icon_dog_04.png' => array( 111, 111 ),
		'icon_dog_05.png' => array( 136, 101 ),
		'icon_dog_06.png' => array( 65, 47 ),
		'icon_dog_07.png' => array( 156, 100 ),
		'icon_dog_08.png' => array( 127, 109 ),
	);

	foreach ( $map as $file => $size ) {
		if ( false !== strpos( $url, $file ) ) {
			return array(
				'width'  => $size[0],
				'height' => $size[1],
			);
		}
	}

	return null;
}
