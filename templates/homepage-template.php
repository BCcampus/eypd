<?php
/**
 * Early-Years Theme: homepage template
 *
 * Modified from original header template in cbox theme
 * @author Brad Payne
 * @package early-years
 * @since 0.9
 * @license https://www.gnu.org/licenses/gpl.html GPLv3 or later
 *
 * Original:
 * @author Bowe Frankema <bowe@presscrew.com>
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 */

infinity_get_header();

?>
<div class="c-banner">
    <img class="c-banner__logo" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-map.png"
         alt="EYPD logo">
</div>

<div class="row">
    <div class="c-search">
        <h2 class="text-blue text-center">Search for training events</h2>
        <p class="text-center">Fill in one or more of the fields below</p>
		<?php echo do_shortcode( '[events_search]' ); ?>
    </div>
</div>

<div class="c-map row">
    <h2 class="text-blue text-center">Find training events near you</h2>
    <div class="four columns">
        <h3>Upcoming Events</h3>
		<?php
		$events_list = '[events_list scope="future" limit="3"]';
		echo do_shortcode( $events_list );
		?>
    </div>
    <div class="twelve columns">
		<?php
		infinity_load_template( 'templates/google-map.php' );
		?>
    </div>
</div>
<div id="content" role="main" class="column sixteen">
	<?php
	do_action( 'open_content' );
	do_action( 'open_home' );
	?>

    <h2 class="text-blue text-center">Explore the professional learning showcase</h2>
	<?php infinity_load_template( 'templates/featured-stories.php' ); ?>

	<?php
	do_action( 'close_home' );
	do_action( 'close_content' );

	?>
</div>
<?php
infinity_get_footer();
?>
