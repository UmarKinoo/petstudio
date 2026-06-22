<?php
/**
 * Generate fixtures/page-blocks/*.json from mirror content inventory.
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

$blocks = array(
	'page-intro-grooming' => array(
		'heading'         => 'Dog',
		'heading_accent'  => 'Grooming',
		'blockquote'      => 'Come to us for our highly rated dog grooming services in Congresbury, North Somerset.',
		'body'            => file_get_contents( $root . '/fixtures/widgets/page-intro.json' ) ? json_decode( file_get_contents( $root . '/fixtures/widgets/page-intro.json' ), true )['body'] ?? '' : '',
		'show_signature'  => true,
		'signature_image' => $m( 'logos/Liza_signature_pink_v06.svg' ),
		'primary_image'   => $m( 'photos/grooming/BK1_8643_1.jpg' ),
	),
	'page-intro-academy' => array(
		'heading'         => 'Training',
		'heading_accent'  => 'Academy',
		'blockquote'      => '',
		'body'            => '<p>Whether you want to become a dog groomer or learn to groom your own dog at home, our Training Academy can help you get there. With City &amp; Guilds qualifications and our own dog grooming courses, as well as a fully equipped teaching space chock full with dogs of all shapes and sizes to practise on, there has never been a better time to get started.</p>',
		'show_signature'  => false,
		'primary_image'   => $m( 'photos/academy/service-card-grooming-academy.jpg' ),
	),
	'page-intro-training' => array(
		'heading'         => 'Train',
		'heading_accent'  => 'Your Dog',
		'blockquote'      => 'Subtitle.',
		'body'            => '<p>Main text.</p>',
		'show_signature'  => false,
		'primary_image'   => $m( 'photos/general/service-card-dog-training.jpg' ),
	),
);

foreach ( range( 1, 8 ) as $i ) {
	$num = str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
	$blocks[ 'dog-divider-' . $num ] = array(
		'icon_image'     => $m( 'icons/icon_dog_' . $num . '.png' ),
		'parallax_x'     => 'x: 50vw',
		'show_on_mobile' => true,
	);
}

$grooming = $m( 'photos/grooming/BK1_8822_1.jpg' );
$grooming2 = $m( 'photos/grooming/BK1_8643_1.jpg' );

$blocks['content-split-our-salon'] = json_decode( file_get_contents( $root . '/fixtures/widgets/content-split.json' ), true );
$blocks['content-split-calm-relaxed'] = array(
	'heading'    => 'Calm & Relaxed',
	'blockquote' => 'We always ensure that our grooming salon provide a relaxed and calm environment for your dog.',
	'body'       => '<p>All dogs that come to us are treated gently and kindly by our fully qualified groomers and they are given plenty of breaks throughout the grooming process to make sure they remain stress-free and comfortable.</p><p>We also use tasty healthy treats as positive reinforcement during our grooms so that your dog comes to associate a trip to us as a happy and yummy experience.</p>',
	'images'     => array( array( 'image' => $grooming ), array( 'image' => $grooming2 ) ),
);
$blocks['content-split-puppy-grooming'] = array(
	'heading'    => 'Puppy Grooming',
	'blockquote' => 'We want all dogs to feel welcome and enjoy their grooming process with us and not be fearful or scared!',
	'body'       => '<p>Owners are encouraged to bring their puppy’s to us for desensitisation from as early as 4 month’s old. We give puppies a positive and fun experience whilst introducing them to the sights and sounds within the salon.</p><p>We like to work closely with owners to help them continue their puppy’s grooming at home.</p>',
	'images'     => array( array( 'image' => $grooming2 ) ),
);
$blocks['content-split-difficult-dog'] = array(
	'heading'    => 'Difficult Dog?',
	'blockquote' => 'To us, there’s no such thing as a “difficult” dog.',
	'body'       => '<p>Do you have a nervous dog? Has your dog bitten a groomer in the past? The Pet Studio has you covered.</p><p>We specialise in dogs that are scared and nervous of the grooming environment. Even dogs deemed as aggressive or difficult are welcome with us.</p><p>With over 20 years’ of grooming experience in-house, there really isn’t anything that we haven’t dealt with before!</p>',
	'images'     => array( array( 'image' => $grooming ), array( 'image' => $grooming2 ) ),
);
$blocks['content-split-hand-stripping'] = array(
	'heading'    => 'Hand Stripping',
	'blockquote' => 'Hand stripping involves removing the dull, older hairs in your dog’s coat by hand.',
	'body'       => '<p>If you have a wire-coated or silk-coated dog, not all groomers can accommodate the techniques required to keep them looking their best.</p><p>With over 20 years’ experience, we pride ourselves on offering this specialist service to our clients.</p>',
	'images'     => array( array( 'image' => $grooming ), array( 'image' => $grooming2 ) ),
);
$blocks['content-split-teeth-cleaning'] = array(
	'heading'    => 'Teeth Cleaning',
	'blockquote' => 'Looking after your dog’s oral health with ultrasonic teeth cleaning',
	'body'       => '<p>Protect your pet’s dental health with a regularly scheduled oral hygiene routine with The Pet Studio.</p><p>The equipment we use has no scary movement or vibration, and causes no pain or discomfort.</p>',
	'bullet_list' => array(
		array( 'item' => 'Deep cleansing and tartar removal' ),
		array( 'item' => 'Reduces gum inflammation' ),
		array( 'item' => 'Cleans without movement, vibration or noise' ),
	),
	'images' => array( array( 'image' => $grooming ), array( 'image' => $grooming2 ) ),
);
$blocks['content-split-training-salon'] = array(
	'heading'    => 'North Somerset Training Salon',
	'blockquote' => '',
	'body'       => '<p>Our fully equipped training salon in Congresbury gives you hands-on experience with real dogs under expert guidance from Liza and the team.</p>',
	'images'     => array( array( 'image' => $m( 'photos/academy/service-card-grooming-academy.jpg' ) ) ),
);
$blocks['content-split-career-change'] = array(
	'heading'    => 'Career Change?',
	'blockquote' => 'Ready for a new career working with dogs every day?',
	'body'       => '<p>Many of our students come to us looking for a career change. Our City &amp; Guilds courses and refresher programmes are designed to get you salon-ready with confidence.</p>',
	'images'     => array( array( 'image' => $m( 'photos/academy/service-card-grooming-academy.jpg' ) ) ),
);

$team_base = json_decode( file_get_contents( $root . '/fixtures/widgets/team-member.json' ), true );
$team_members = array(
	'liza'    => array( 'name_line_1' => 'Liza', 'name_accent' => 'Smith', 'role' => 'Teacher, Assessor & Dog Groomer', 'portrait' => 'photos/team/liza-portrait.jpg', 'show_signature' => true, 'reverse_columns' => false ),
	'skye'    => array( 'name_line_1' => 'Skye', 'name_accent' => 'Braham', 'role' => 'Dog Groomer', 'portrait' => 'photos/team/skye.jpg', 'bio' => '<p>Skye is Liza’s daughter who completed her City &amp; Guilds Level 2 qualification in 2020. She specialises in nervous dogs, young puppies and ultrasonic teeth cleaning.</p>', 'reverse_columns' => true ),
	'jemma'   => array( 'name_line_1' => 'Jemma', 'name_accent' => '', 'role' => 'Dog Groomer', 'portrait' => 'photos/team/jemma.jpg', 'bio' => '<p>Jemma has years of experience grooming nervous, fearful dogs using gentle patience. Her specialty lies in handstripping.</p>', 'reverse_columns' => false ),
	'georgia' => array( 'name_line_1' => 'Georgia', 'name_accent' => '', 'role' => 'Dog Groomer', 'portrait' => 'photos/team/georgia.jpg', 'bio' => '<p>Georgia trained under Liza in 2021 and loves the creative and technical challenge that dog grooming offers across many breeds.</p>', 'reverse_columns' => true ),
	'rita'    => array( 'name_line_1' => 'Rita', 'name_accent' => '', 'role' => 'Receptionist', 'portrait' => 'photos/team/rita.jpg', 'bio' => '<p>Rita works part-time as receptionist and enjoys meeting customers as they drop off their canine companions for a pampering session.</p>', 'reverse_columns' => false ),
	'liz'     => array( 'name_line_1' => 'Liz', 'name_accent' => '', 'role' => 'Receptionist', 'portrait' => 'photos/team/liz.jpg', 'bio' => '<p>Liz works part-time as a receptionist at The Pet Studio and enjoys meeting and greeting customers and booking appointments.</p>', 'reverse_columns' => true ),
	'bea'     => array( 'name_line_1' => 'Bea', 'name_accent' => '', 'role' => 'Dog Groomer', 'portrait' => 'photos/team/bea.jpg', 'bio' => '<p>Bea is a skilled groomer at The Pet Studio bringing care and attention to every dog in the salon.</p>', 'reverse_columns' => false ),
	'kianna'  => array( 'name_line_1' => 'Kianna', 'name_accent' => '', 'role' => 'Apprentice', 'portrait' => 'photos/team/kianna.jpg', 'bio' => '<p>Kianna is one of the apprentices with a lifelong love for animals, learning everything she can about grooming while working with dogs every day.</p>', 'reverse_columns' => true ),
);

foreach ( $team_members as $slug => $member ) {
	$data = array_merge( $team_base, $member );
	$data['portrait'] = $m( $member['portrait'] );
	if ( empty( $data['show_signature'] ) ) {
		$data['show_signature'] = false;
		unset( $data['signature_image'] );
	}
	$blocks[ 'team-' . $slug ] = $data;
}

$all_testimonials = json_decode( file_get_contents( $root . '/fixtures/widgets/testimonials.json' ), true );
$academy_indices  = array( 4, 5, 8, 9, 13, 11, 12 );
$academy_reviews  = array();
foreach ( $academy_indices as $i ) {
	if ( isset( $all_testimonials['reviews'][ $i ] ) ) {
		$academy_reviews[] = $all_testimonials['reviews'][ $i ];
	}
}
$blocks['testimonials-academy'] = array(
	'reviews'    => $academy_reviews,
	'autoplay'   => true,
	'show_dots'  => true,
);

foreach ( $blocks as $name => $data ) {
	$path = $out . '/' . $name . '.json';
	file_put_contents( $path, json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
	echo "Wrote $name.json\n";
}
