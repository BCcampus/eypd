<?php
/**
 * Early-Years Theme: footer template
 *
 * Modified from original header template in cbox theme
 * @author Alex Paredes
 * @package early-years
 * @since 0.9
 * @license https://www.gnu.org/licenses/gpl.html GPLv3 or later
 *
 * Original:
 * @author Bowe Frankema <bowe@presscrew.com>
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 */
?>

<?php
do_action( 'close_main_wrap' );
?>
</div>
<div class="footer-wrap row <?php do_action( 'footer_wrap_class' ); ?>">
	<?php
	do_action( 'open_footer_wrap' );
	?>
    <!-- begin footer -->
    <footer id="footer" role="contentinfo">
		<?php
		do_action( 'open_footer' );
		infinity_get_template_part( 'templates/parts/footer-widgets' );
		?>
        <div id="powered-by">
            <div id="copyright-info" class="column ten">
				<?php echo infinity_option_get( 'infinity-core-options.footer-text' ); ?>
            </div>
            <div id="footer-info" class="column six">
		        <?php
		        // Load Footer Menu only if it's enabled
		        if ( current_theme_supports( 'infinity-footer-menu-setup' ) ) :
			        infinity_get_template_part( 'templates/parts/footer-menu', 'footer' );
		        endif;
		        ?>
            </div>
        </div>
		<?php
		do_action( 'close_footer' );
		?>
    </footer>
	<?php
	do_action( 'close_footer_wrap' );
	?>
</div><!-- close container -->
</div>

<?php
do_action( 'close_body' );
wp_footer();
?>
<?php if ( is_page( 'Sign Up' ) ) {
	get_template_part( 'templates/terms-modal' );
	get_template_part( 'templates/roles-modal' );
} ?>
<script>
 window._wordpressConfig = {
       templateUrl: new URL('<?php echo get_stylesheet_directory_uri();?>').toString(),
      baseUrl: new URL('<?php echo site_url();?>').toString(),
   };
</script>

<script src="<?php echo get_stylesheet_directory_uri();?>/register-sw.js"></script>

</body>
</html>
