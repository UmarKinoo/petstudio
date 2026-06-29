<?php
/**
 * Smoke test — verify widget files and fixtures without WordPress.
 *
 * Usage: php bin/smoke-test.php
 */

$root = dirname( __DIR__ );

$widgets = array(
	'header'          => 'Header_Widget',
	'footer'          => 'Footer_Widget',
	'cookie-consent'  => 'Cookie_Consent_Widget',
	'hero-home'       => 'Hero_Home_Widget',
	'hero-inner'      => 'Hero_Inner_Widget',
	'services-cards'  => 'Services_Cards_Widget',
	'about-intro'     => 'About_Intro_Widget',
	'page-intro'      => 'Page_Intro_Widget',
	'content-split'   => 'Content_Split_Widget',
	'dog-divider'     => 'Dog_Divider_Widget',
	'courses-tabs'    => 'Courses_Tabs_Widget',
	'testimonials'    => 'Testimonials_Widget',
	'team-member'     => 'Team_Member_Widget',
	'est-banner'      => 'Est_Banner_Widget',
	'contact'         => 'Contact_Widget',
	'faq'             => 'Faq_Widget',
);

$errors = 0;

foreach ( $widgets as $slug => $class ) {
	$file = $root . '/widgets/class-' . $slug . '-widget.php';
	$fixture = $root . '/fixtures/widgets/' . $slug . '.json';
	$schema  = $root . '/schemas/' . $slug . '.json';

	if ( ! is_file( $file ) ) {
		echo "MISSING widget file: $file\n";
		++$errors;
		continue;
	}

	exec( 'php -l ' . escapeshellarg( $file ) . ' 2>&1', $out, $code );
	if ( 0 !== $code ) {
		echo "SYNTAX ERROR in $file: " . implode( ' ', $out ) . "\n";
		++$errors;
	}

	if ( ! is_file( $fixture ) ) {
		echo "MISSING fixture: $fixture\n";
		++$errors;
	}

	if ( ! is_file( $schema ) ) {
		echo "MISSING schema: $schema\n";
		++$errors;
	}

	$src = file_get_contents( $file );
	if ( false === strpos( $src, "class $class" ) ) {
		echo "CLASS mismatch in $file — expected $class\n";
		++$errors;
	}

	if ( false === strpos( $src, "return '$slug';" ) && false === strpos( $src, "return \"$slug\";" ) ) {
		// fixture slug may use get_fixture_slug return.
		if ( false === strpos( $src, $slug ) ) {
			echo "WARN: fixture slug '$slug' not found in $file\n";
		}
	}
}

if ( $errors > 0 ) {
	echo "\nFAILED: $errors error(s)\n";
	exit( 1 );
}

echo "OK: All " . count( $widgets ) . " widgets pass smoke test.\n";
exit( 0 );
