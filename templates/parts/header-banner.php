<?php
/**
 * Early Years Theme: Header Content
 *
 * Modified from original header template in cbox theme
 * @author Brad Payne
 * @package early-years
 * @since 0.9.5
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 or later
 */
?>
<div class="top-wrap row <?php do_action( 'top_wrap_class' ); ?>">
	<?php
	// Load Top Menu only if it's enabled
	if ( current_theme_supports( 'infinity-top-menu-setup' ) ) :
		infinity_get_template_part( 'templates/parts/top-menu', 'header' );
	endif;
	?>
	<!-- header -->
	<header id="header" role="banner">
		<div id="logo-menu-wrap">
			<?php
			do_action( 'open_header' );
			?>
			<?php
			$heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div';
			?>
			<<?php echo $heading_tag; ?> id="icext" class="icext-feature icext-header-logo">
			<a href="<?php echo home_url( '/' ); ?>" title="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/bcid-logo.png" width="101" height="92" alt="BC Provincial Government">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/eypd-logomark.png" width="135" height="92" alt="Early Years Professional Development">
			</a>
		</<?php echo $heading_tag; ?>>
		<?php
		// Load Main Menu only if it's enabled
		if ( current_theme_supports( 'infinity-main-menu-setup' ) ) :
			infinity_get_template_part( 'templates/parts/main-menu', 'header' );
		endif;
		do_action( 'close_header' );
		?>
</div>
</header><!-- end header -->
<?php
// Load Sub Menu only if it's enabled
if ( current_theme_supports( 'infinity-sub-menu-setup' ) ) :
	infinity_get_template_part( 'templates/parts/sub-menu', 'header' );
endif;
?>
</div><!-- end top wrap -->
