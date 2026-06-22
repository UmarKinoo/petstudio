#!/usr/bin/env php
<?php
/**
 * CLI demo import — requires Local site running + wp-config-local.php socket.
 *
 * Usage: php bin/run-demo-import.php
 */

$wp_root = '/Users/kinooumarkhayyamhassam/Local Sites/the-pet-studio/app/public';

if ( ! is_readable( $wp_root . '/wp-load.php' ) ) {
	fwrite( STDERR, "WordPress not found at {$wp_root}\n" );
	exit( 1 );
}

$_SERVER['HTTP_HOST'] = 'the-pet-studio.local';
$_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );

require $wp_root . '/wp-load.php';

if ( ! class_exists( '\Pet_Studio_Elementor\Demo_Importer' ) ) {
	fwrite( STDERR, "Pet Studio Elementor plugin not active.\n" );
	exit( 1 );
}

$result = ( new \Pet_Studio_Elementor\Demo_Importer() )->import();

echo ( $result['success'] ? 'SUCCESS: ' : 'FAILED: ' ) . $result['message'] . PHP_EOL;
exit( $result['success'] ? 0 : 1 );
