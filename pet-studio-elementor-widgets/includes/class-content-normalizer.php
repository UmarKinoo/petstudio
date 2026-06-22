<?php
/**
 * Maps Elementor settings ↔ API payloads using schemas/fixtures.
 *
 * @package Pet_Studio_Elementor
 */

namespace Pet_Studio_Elementor;

defined( 'ABSPATH' ) || exit;

class Content_Normalizer {

	/** @var array<string, array>|null */
	private static $fixture_cache = array();

	/**
	 * Widget slug (file basename) to Elementor widget name.
	 *
	 * @var array<string, string>
	 */
	public const WIDGET_MAP = array(
		'header'          => 'pet_studio_header',
		'footer'          => 'pet_studio_footer',
		'cookie-consent'  => 'pet_studio_cookie_consent',
		'hero-home'       => 'pet_studio_hero_home',
		'hero-inner'      => 'pet_studio_hero_inner',
		'services-cards'  => 'pet_studio_services_cards',
		'about-intro'     => 'pet_studio_about_intro',
		'page-intro'      => 'pet_studio_page_intro',
		'content-split'   => 'pet_studio_content_split',
		'dog-divider'     => 'pet_studio_dog_divider',
		'courses-tabs'    => 'pet_studio_courses_tabs',
		'testimonials'    => 'pet_studio_testimonials',
		'team-member'     => 'pet_studio_team_member',
		'est-banner'      => 'pet_studio_est_banner',
		'contact'         => 'pet_studio_contact',
	);

	/**
	 * Load fixture JSON for a widget (API-shaped defaults).
	 *
	 * @param string $fixture_slug e.g. header, hero-home.
	 */
	public static function get_fixture( string $fixture_slug ): array {
		if ( isset( self::$fixture_cache[ $fixture_slug ] ) ) {
			return self::$fixture_cache[ $fixture_slug ];
		}

		$path = PET_STUDIO_EW_PATH . 'fixtures/widgets/' . $fixture_slug . '.json';

		if ( ! is_readable( $path ) ) {
			self::$fixture_cache[ $fixture_slug ] = array();
			return array();
		}

		$data = json_decode( (string) file_get_contents( $path ), true );
		self::$fixture_cache[ $fixture_slug ] = is_array( $data ) ? $data : array();

		return self::$fixture_cache[ $fixture_slug ];
	}

	/**
	 * Defaults for Elementor controls — flattened from fixture where needed.
	 *
	 * @param string $fixture_slug Fixture file slug.
	 */
	public static function get_control_defaults( string $fixture_slug ): array {
		$fixture = self::get_fixture( $fixture_slug );
		$defaults = apply_filters( 'pet_studio_fixture_defaults', $fixture, $fixture_slug );
		if ( ! is_array( $defaults ) ) {
			return array();
		}

		return self::to_elementor_settings( $fixture_slug, $defaults );
	}

	/**
	 * Page-specific block override (fixtures/page-blocks/{variant}.json).
	 */
	public static function get_page_block( string $variant ): array {
		$path = PET_STUDIO_EW_PATH . 'fixtures/page-blocks/' . $variant . '.json';
		if ( ! is_readable( $path ) ) {
			return array();
		}
		$data = json_decode( (string) file_get_contents( $path ), true );
		return is_array( $data ) ? $data : array();
	}

	/**
	 * Convert API/fixture JSON shape to Elementor panel settings (repeaters, switchers, links).
	 *
	 * @param string               $fixture_slug Fixture slug.
	 * @param array<string, mixed> $fixture      Optional fixture payload (defaults loaded when empty).
	 */
	public static function to_elementor_settings( string $fixture_slug, array $fixture = array() ): array {
		if ( empty( $fixture ) ) {
			$fixture = self::get_fixture( $fixture_slug );
		}

		switch ( $fixture_slug ) {
			case 'hero-home':
				$fixture['headline_words']  = self::list_to_repeater( $fixture['headline_words'] ?? array(), 'word' );
				$fixture['video_desktop']   = self::media_to_elementor( $fixture['video_desktop'] ?? null );
				$fixture['video_mobile']    = self::media_to_elementor( $fixture['video_mobile'] ?? null );
				$fixture['logo_desktop']    = self::media_to_elementor( $fixture['logo_desktop'] ?? null );
				$fixture['logo_mobile']     = self::media_to_elementor( $fixture['logo_mobile'] ?? null );
				break;

			case 'header':
				$book_label = (string) ( $fixture['book_now_label'] ?? 'Book Now' );
				$fixture['navigation'] = self::map_list(
					self::strip_book_now_nav_items( $fixture['navigation'] ?? array(), $book_label ),
					function ( array $item ): array {
						return array(
							'label'     => $item['label'] ?? '',
							'subtitle'  => $item['subtitle'] ?? '',
							'link'      => self::link_to_elementor( $item['link'] ?? null ),
							'is_active' => self::bool_to_switcher( $item['is_active'] ?? false ),
						);
					}
				);
				$fixture['social_items'] = self::map_list(
					$fixture['social_items'] ?? array(),
					function ( array $item ): array {
						return array(
							'network' => $item['network'] ?? 'instagram',
							'link'    => self::link_to_elementor( $item['link'] ?? null ),
						);
					}
				);
				$fixture['logo_default']       = self::media_to_elementor( $fixture['logo_default'] ?? null );
				$fixture['logo_inverse']       = self::media_to_elementor( $fixture['logo_inverse'] ?? null );
				$fixture['logo_link']          = self::link_to_elementor( $fixture['logo_link'] ?? null );
				$fixture['book_now_link']      = self::link_to_elementor( $fixture['book_now_link'] ?? null );
				$fixture['show_book_now']      = self::bool_to_switcher( $fixture['show_book_now'] ?? true );
				$fixture['show_social']        = self::bool_to_switcher( $fixture['show_social'] ?? true );
				$fixture['enable_sticky']      = self::bool_to_switcher( $fixture['enable_sticky'] ?? true );
				$fixture['enable_transparent'] = self::bool_to_switcher( $fixture['enable_transparent'] ?? true );
				$fixture['nav_typography_typography']      = 'custom';
				$fixture['nav_typography_text_transform']    = 'none';
				break;

			case 'footer':
				$fixture['logo']         = self::media_to_elementor( $fixture['logo'] ?? null );
				$fixture['contact_link'] = self::link_to_elementor( $fixture['contact_link'] ?? null );
				$fixture['privacy_link'] = self::link_to_elementor( $fixture['privacy_link'] ?? null );
				break;

			case 'hero-inner':
				$fixture['video'] = self::media_to_elementor( $fixture['video'] ?? null );
				break;

			case 'services-cards':
				$fixture['cards'] = self::map_list(
					$fixture['cards'] ?? array(),
					function ( array $card ): array {
						return array(
							'image'          => self::media_to_elementor( $card['image'] ?? null ),
							'title'          => $card['title'] ?? '',
							'link'           => self::link_to_elementor( $card['link'] ?? null ),
							'button_text'    => $card['button_text'] ?? 'See More',
							'parallax_start' => $card['parallax_start'] ?? '62vh',
							'image_width'    => (int) ( $card['image_width'] ?? 0 ),
							'image_height'   => (int) ( $card['image_height'] ?? 0 ),
						);
					}
				);
				break;

			case 'about-intro':
				$fixture['body']            = $fixture['body'] ?? '';
				$fixture['cta_link']        = self::link_to_elementor( $fixture['cta_link'] ?? null );
				$fixture['badge_image']     = self::media_to_elementor( $fixture['badge_image'] ?? null );
				$fixture['main_image']      = self::media_to_elementor( $fixture['main_image'] ?? null );
				$fixture['signature_image'] = self::media_to_elementor( $fixture['signature_image'] ?? null );
				$fixture['show_signature']  = self::bool_to_switcher( $fixture['show_signature'] ?? false );
				break;

			case 'page-intro':
				$fixture['signature_image'] = self::media_to_elementor( $fixture['signature_image'] ?? null );
				$fixture['primary_image']   = self::media_to_elementor( $fixture['primary_image'] ?? null );
				$fixture['secondary_image'] = self::media_to_elementor( $fixture['secondary_image'] ?? null );
				$fixture['badge_image']     = self::media_to_elementor( $fixture['badge_image'] ?? null );
				$fixture['left_inset_image'] = self::media_to_elementor( $fixture['left_inset_image'] ?? null );
				$fixture['show_signature']  = self::bool_to_switcher( $fixture['show_signature'] ?? false );
				$fixture['reverse_columns'] = self::bool_to_switcher( $fixture['reverse_columns'] ?? false );
				break;

			case 'content-split':
				$fixture['bullet_list'] = self::list_to_repeater( $fixture['bullet_list'] ?? array(), 'item' );
				$fixture['images']      = self::map_list(
					$fixture['images'] ?? array(),
					function ( array $img ): array {
						$media = isset( $img['url'] ) ? $img : ( $img['image'] ?? $img );
						return array( 'image' => self::media_to_elementor( $media ) );
					}
				);
				$fixture['reverse_columns'] = self::bool_to_switcher( $fixture['reverse_columns'] ?? false );
				break;

			case 'dog-divider':
				$fixture['icon_image']     = self::media_to_elementor( $fixture['icon_image'] ?? null );
				$fixture['icon_width']     = (int) ( $fixture['icon_width'] ?? 0 );
				$fixture['icon_height']    = (int) ( $fixture['icon_height'] ?? 0 );
				$fixture['show_on_mobile'] = self::bool_to_switcher( $fixture['show_on_mobile'] ?? true );
				break;

			case 'courses-tabs':
				$fixture['tabs'] = self::map_list(
					$fixture['tabs'] ?? array(),
					function ( array $tab ): array {
						$tab['badge_image'] = self::media_to_elementor( $tab['badge_image'] ?? null );
						$lines              = array();
						foreach ( $tab['features'] ?? array() as $feat ) {
							$line = is_string( $feat ) ? $feat : ( $feat['item'] ?? '' );
							if ( '' !== $line ) {
								$lines[] = $line;
							}
						}
						$tab['features_list'] = implode( "\n", $lines );
						unset( $tab['features'] );
						return $tab;
					}
				);
				break;

			case 'testimonials':
				$fixture['reviews'] = self::map_list(
					$fixture['reviews'] ?? array(),
					function ( array $review ): array {
						return array(
							'icon'   => self::media_to_elementor( $review['icon'] ?? null ),
							'title'  => $review['title'] ?? '',
							'quote'  => $review['quote'] ?? '',
							'author' => $review['author'] ?? '',
						);
					}
				);
				$fixture['autoplay']  = self::bool_to_switcher( $fixture['autoplay'] ?? true );
				$fixture['show_dots'] = self::bool_to_switcher( $fixture['show_dots'] ?? true );
				break;

			case 'team-member':
				$fixture['portrait']        = self::media_to_elementor( $fixture['portrait'] ?? null );
				$fixture['signature_image'] = self::media_to_elementor( $fixture['signature_image'] ?? null );
				$fixture['show_signature']  = self::bool_to_switcher( $fixture['show_signature'] ?? false );
				$fixture['reverse_columns'] = self::bool_to_switcher( $fixture['reverse_columns'] ?? false );
				break;

			case 'est-banner':
				$fixture['hide_on_mobile'] = self::bool_to_switcher( $fixture['hide_on_mobile'] ?? false );
				break;

			case 'contact':
				$fixture['sticky_image'] = self::media_to_elementor( $fixture['sticky_image'] ?? null );
				$fixture['mobile_image'] = self::media_to_elementor( $fixture['mobile_image'] ?? null );
				$fixture['maps_link']    = self::link_to_elementor( $fixture['maps_link'] ?? null );
				break;

			case 'cookie-consent':
				$fixture['privacy_link'] = self::link_to_elementor( $fixture['privacy_link'] ?? null );
				break;
		}

		return self::sanitize_for_elementor( $fixture );
	}

	/**
	 * Strip nulls and normalize list shapes before saving to _elementor_data.
	 *
	 * @param array<string, mixed> $settings Settings tree.
	 * @return array<string, mixed>
	 */
	public static function sanitize_for_elementor( array $settings ): array {
		foreach ( $settings as $key => $value ) {
			if ( null === $value ) {
				unset( $settings[ $key ] );
				continue;
			}

			if ( is_array( $value ) ) {
				$settings[ $key ] = self::is_list_array( $value )
					? array_values(
						array_map(
							static function ( $item ) {
								return is_array( $item ) ? self::sanitize_for_elementor( $item ) : $item;
							},
							$value
						)
					)
					: self::sanitize_for_elementor( $value );
			}
		}

		return $settings;
	}

	/**
	 * Remove Book Now rows from nav repeaters — CTA is a separate control.
	 *
	 * @param array<int, array<string, mixed>> $items Nav rows.
	 * @param string                           $book_label Book Now label to strip.
	 * @return array<int, array<string, mixed>>
	 */
	public static function strip_book_now_nav_items( array $items, string $book_label = 'Book Now' ): array {
		$blocked = array(
			strtolower( trim( $book_label ) ),
			'book now',
		);

		return array_values(
			array_filter(
				$items,
				static function ( array $item ) use ( $blocked ): bool {
					$label = strtolower( trim( (string) ( $item['label'] ?? '' ) ) );
					return ! in_array( $label, $blocked, true );
				}
			)
		);
	}

	/**
	 * @param array<int, mixed> $list List values.
	 */
	private static function list_to_repeater( array $list, string $field ): array {
		$out = array();
		foreach ( $list as $item ) {
			if ( is_array( $item ) && array_key_exists( $field, $item ) ) {
				$out[] = $item;
				continue;
			}
			if ( is_string( $item ) || is_numeric( $item ) ) {
				$out[] = array( $field => (string) $item );
			}
		}
		return $out;
	}

	/**
	 * @param array<int, mixed>        $list    List.
	 * @param callable(array): array  $mapper  Row mapper.
	 */
	private static function map_list( array $list, callable $mapper ): array {
		$out = array();
		foreach ( $list as $item ) {
			if ( is_array( $item ) ) {
				$out[] = $mapper( $item );
			}
		}
		return $out;
	}

	/**
	 * @param array<string, mixed>|null $link API link.
	 * @return array<string, mixed>
	 */
	public static function link_to_elementor( ?array $link ): array {
		if ( empty( $link['url'] ) ) {
			return array(
				'url'               => '#',
				'is_external'       => '',
				'nofollow'          => '',
				'custom_attributes' => '',
			);
		}
		return array(
			'url'               => (string) $link['url'],
			'is_external'       => ! empty( $link['is_external'] ) ? 'yes' : '',
			'nofollow'          => ! empty( $link['nofollow'] ) ? 'yes' : '',
			'custom_attributes' => '',
		);
	}

	/**
	 * @param array<string, mixed>|null $media API media.
	 * @return array<string, string>
	 */
	public static function media_to_elementor( ?array $media ): array {
		if ( empty( $media['url'] ) ) {
			return array( 'url' => '', 'id' => '' );
		}
		return array(
			'url' => (string) $media['url'],
			'id'  => isset( $media['id'] ) ? (string) $media['id'] : '',
		);
	}

	/**
	 * @param mixed $value Bool or switcher string.
	 */
	public static function bool_to_switcher( $value ): string {
		if ( 'yes' === $value || true === $value || 1 === $value || '1' === $value ) {
			return 'yes';
		}
		return '';
	}

	/**
	 * Merge fixture defaults into Elementor settings (empty values use mirror content).
	 *
	 * @param array<string, mixed> $defaults Fixture defaults.
	 * @param array<string, mixed> $settings Elementor panel settings.
	 */
	public static function merge_settings( array $defaults, array $settings ): array {
		foreach ( $defaults as $key => $default_val ) {
			if ( ! array_key_exists( $key, $settings ) || self::is_empty_setting( $settings[ $key ] ) ) {
				$settings[ $key ] = $default_val;
				continue;
			}

			if ( is_array( $default_val ) && is_array( $settings[ $key ] ) ) {
				if ( self::is_list_array( $default_val ) ) {
					if ( self::is_empty_setting( $settings[ $key ] ) ) {
						$settings[ $key ] = $default_val;
					}
				} else {
					$settings[ $key ] = self::merge_settings( $default_val, $settings[ $key ] );
				}
			}
		}

		return $settings;
	}

	/**
	 * @param mixed $value Setting value.
	 */
	private static function is_empty_setting( $value ): bool {
		if ( null === $value || false === $value || '' === $value ) {
			return true;
		}

		if ( is_array( $value ) ) {
			if ( array() === $value ) {
				return true;
			}
			// Elementor media control empty shape.
			if ( isset( $value['url'] ) && '' === $value['url'] && empty( $value['id'] ) ) {
				return true;
			}
			// Elementor URL control empty shape.
			if ( isset( $value['url'] ) && '#' === $value['url'] && empty( $value['is_external'] ) && empty( $value['nofollow'] ) ) {
				return false;
			}
		}

		return false;
	}

	/**
	 * @param array<mixed> $array Array to inspect.
	 */
	private static function is_list_array( array $array ): bool {
		if ( array() === $array ) {
			return true;
		}
		return array_keys( $array ) === range( 0, count( $array ) - 1 );
	}

	/**
	 * Normalize settings before render (Elementor panel or API payload).
	 *
	 * @param string $widget_name Elementor widget name e.g. pet_studio_header.
	 * @param array  $settings    Raw settings.
	 */
	public static function normalize( string $widget_name, array $settings ): array {
		$normalized = array_merge(
			array(
				'_widget' => $widget_name,
				'_source' => 'elementor',
			),
			$settings
		);

		/**
		 * Filter widget settings before render.
		 *
		 * @param array  $normalized  Settings passed to render templates.
		 * @param string $widget_name Elementor widget name.
		 */
		return apply_filters( 'pet_studio_widget_settings', $normalized, $widget_name );
	}

	/**
	 * Resolve fixture slug from Elementor widget name.
	 */
	public static function fixture_slug_from_widget( string $widget_name ): string {
		$flip = array_flip( self::WIDGET_MAP );
		return $flip[ $widget_name ] ?? '';
	}

	/**
	 * Load JSON schema for a widget (for API validation / docs).
	 */
	public static function get_schema( string $fixture_slug ): array {
		$path = PET_STUDIO_EW_PATH . 'schemas/' . $fixture_slug . '.json';

		if ( ! is_readable( $path ) ) {
			return array();
		}

		$data = json_decode( (string) file_get_contents( $path ), true );
		return is_array( $data ) ? $data : array();
	}
}
