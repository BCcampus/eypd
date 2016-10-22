<?php
/**
 * Template Name: Homepage Template
 *
 * @author Bowe Frankema <bowe@presscrew.com>
 * @link http://shop.presscrew.com/
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @since 1.0
 */
infinity_get_header();

?>
<div class="grid_24" role="main">

	<?php
	do_action( 'open_content' );
	do_action( 'open_page' );

	infinity_load_template( 'templates/fullwidth-map.php' );
	//infinity_load_template( 'templates/google-map.php' );

	?>

	<div id="filter-bar">
		<div class="container">
			<?php echo do_shortcode( '[events_search]' ); ?>
		</div>
	</div>
	<?php
	do_action( 'close_page' );
	do_action( 'close_content' );
	?>
</div>
<?php
infinity_get_footer();
?>
