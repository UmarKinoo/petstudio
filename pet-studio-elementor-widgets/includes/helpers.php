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
 * Render link attributes from URL control or API link object.
 *
 * @param array|null $link Link settings.
 */
function link_attrs( ?array $link ): array {
	$url = $link['url'] ?? '#';

	return array(
		'href'   => esc_url( $url ),
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
