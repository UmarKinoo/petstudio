<?php
/**
 * Generate the COMPUTED page-block fixtures only.
 *
 * This script regenerates the fixtures that are derived from rules/inventory:
 *   - dog-divider-01 … dog-divider-08  (icon + dimensions table)
 *   - testimonials-academy             (a fixed subset of fixtures/widgets/testimonials.json)
 *
 * Everything else under fixtures/page-blocks/ (page-intros, content-splits, team
 * members, etc.) is HAND-AUTHORED content. This script deliberately does NOT touch
 * those files, so running it can never overwrite edited content. Edit those JSON
 * files directly.
 *
 * Note: the demo importer reads the committed fixtures directly — it does NOT run
 * this script. This is a developer convenience for the two computed block sets only.
 *
 * Usage: php bin/generate-page-blocks.php
 */

$root  = dirname( __DIR__ );
$out   = $root . '/fixtures/page-blocks';
$media = 'https://thepetstudio.local/wp-content/uploads/pet-studio/media';

if ( ! is_dir( $out ) ) {
	mkdir( $out, 0755, true );
}

$m = static function ( string $path ) use ( $media ): array {
	return array( 'id' => 0, 'url' => $media . '/' . ltrim( $path, '/' ) );
};

$blocks = array();

// Dog dividers — icon + per-icon dimensions.
foreach ( range( 1, 8 ) as $i ) {
	$num       = str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
	$icon_file = 'icons/icon_dog_' . $num . '.png';
	$icon_dims = array(
		'01' => array( 105, 98 ),
		'02' => array( 115, 101 ),
		'03' => array( 101, 54 ),
		'04' => array( 111, 111 ),
		'05' => array( 136, 101 ),
		'06' => array( 65, 47 ),
		'07' => array( 156, 100 ),
		'08' => array( 127, 109 ),
	);
	$blocks[ 'dog-divider-' . $num ] = array(
		'icon_image'     => $m( $icon_file ),
		'parallax_x'     => 'x: 50vw',
		'show_on_mobile' => true,
		'icon_width'     => $icon_dims[ $num ][0],
		'icon_height'    => $icon_dims[ $num ][1],
	);
}

// Academy testimonials — a fixed subset of the master testimonials list.
$all_testimonials = json_decode( file_get_contents( $root . '/fixtures/widgets/testimonials.json' ), true );
$academy_indices  = array( 4, 5, 8, 9, 13, 11, 12 );
$academy_reviews  = array();
foreach ( $academy_indices as $i ) {
	if ( isset( $all_testimonials['reviews'][ $i ] ) ) {
		$academy_reviews[] = $all_testimonials['reviews'][ $i ];
	}
}
$blocks['testimonials-academy'] = array(
	'reviews'   => $academy_reviews,
	'autoplay'  => true,
	'show_dots' => true,
);

foreach ( $blocks as $name => $data ) {
	$path = $out . '/' . $name . '.json';
	file_put_contents( $path, json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
	echo "Wrote $name.json\n";
}
