<?php
/**
 * Early-Years Theme: header template
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
?>
	<!DOCTYPE html>
	<!--[if lt IE 7 ]>    <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
	<!--[if IE 7 ]>        <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
	<!--[if IE 8 ]>        <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
	<!--[if IE 9 ]>        <html <?php language_attributes(); ?> class="no-js ie9"> <![endif]-->
	<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<?php
infinity_get_template_part( 'templates/parts/header-head');
?>
<body <?php body_class() ?> id="infinity-base">
<?php
do_action( 'open_body' );
?>

<?php if (is_front_page()) {
	do_action( 'open_wrapper' );
	infinity_get_template_part( 'templates/parts/header-banner' );
//do_action('open_container');
}
else { ?>

	<div id="wrapper" class="hfeed">

	<?php
	do_action( 'open_wrapper' );
	?>

	<?php // the header-banner template contains all the markup for the header(logo) and menus. You can easily fork/modify this in your child theme without having to overwrite the entire header.php file.
	infinity_get_template_part( 'templates/parts/header-banner');
	?>
	<?php
	do_action( 'open_container' );
	?>

	<!-- start main wrap. the main-wrap div will be closed in the footer template -->
<div class="main-wrap row <?php do_action( 'main_wrap_class' ); ?>">
	<?php
	do_action( 'open_main_wrap' );
	?>


<?php } ?>