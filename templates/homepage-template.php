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
	<img class="c-banner__logo" src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/eypd-logo-final.svg"
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
	<div class="six columns">

        <!-- tabs start -->
        <div id="tabs" class="ui-tabs ui-corner-all ui-widget ui-widget-content">

            <ul role="tablist" class="ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header">
                <li role="tab" tabindex="1"><a
                            href="#tabs-1"
                            role="presentation"
                            tabindex="-1"
                            class="ui-tabs-anchor"
                            id="ui-id-1">Upcoming Events</a>
                </li>
                <li role="tab" tabindex="2" aria-controls="tabs-2"><a href="#tabs-2" role="presentation" tabindex="-1"
                                                                      class="ui-tabs-anchor" id="ui-id-2">Recently
                        Posted</a></li>

            </ul>
            <div id="tabs-1">
				<?php
				$events_list = '[events_list scope="after-today" limit="4"]';
				echo do_shortcode( $events_list );
				?>
            </div>
            <div id="tabs-2">
				<?php
				$events_recent = '[events_list orderby="event_date_created" order="DESC" limit="4"]';
				echo do_shortcode( $events_recent );
				?>
            </div>
        </div>
        <!-- tabs end -->

	</div>
	<div class="ten columns">
		<?php
		infinity_load_template( 'templates/google-map.php' );
		?>
        <h2 class="text-center"><a class="text-gray" href="events"><?php eypd_display_count_events(); ?> Training Events Currently Posted</a></h2>
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
