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
            <div id="footer-info" class="column ten">
				<?php
				// Load Footer Menu only if it's enabled
				if ( current_theme_supports( 'infinity-footer-menu-setup' ) ) :
					infinity_get_template_part( 'templates/parts/footer-menu', 'footer' );
				endif;
				?>
            </div>
            <div id="copyright-info" class="column six">
				<?php echo infinity_option_get( 'infinity-core-options.footer-text' ); ?>
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
<script>
	<?php include( get_stylesheet_directory_uri() . '/dist/scripts/pwa/nomodule-safari.js' ); ?>
</script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/system.js" nomodule></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/custom-elements.js" defer></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/import-polyfill.js" defer></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/ric-polyfill.js" defer></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/pubsubhub.js" defer></script>
<?php
$modules = array( 'pwp-view.js', 'lazyload.js' );
foreach ( $modules as $module ):
	?>
    <script type="module"
            src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/<?php echo $module; ?>"></script>
	<?php
endforeach;
?>
<script nomodule>
	<?php echo json_encode( $modules );?>.reduce(
        async (chain, module) => {
            await chain;
            return SystemJS.import(`<?php echo get_stylesheet_directory_uri();?>/dist/scripts/pwa/systemjs/${module}`);
        },
        Promise.resolve()
    )
</script>
<script type="module" src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/router.js"></script>
<script type="module" src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/lazyload.js"></script>
<template class="lazyload">
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/idb.js" defer></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/bg-sync-manager.js" defer></script>
	<?php
	$modules = array( 'install-sw.js', 'resource-updates.js', 'pwp-lazy-image.js', 'offline-articles.js' );
	foreach ( $modules as $module ):
		?>
        <script type="module"
                src="<?php echo get_stylesheet_directory_uri(); ?>/dist/scripts/pwa/<?php echo $module; ?>"></script>
		<?php
	endforeach;
	?>
    <script nomodule>
		<?php echo json_encode( $modules );?>.reduce(
            async (chain, module) => {
                await chain;
                return SystemJS.import(`<?php echo get_stylesheet_directory_uri();?>/dist/scripts/pwa/systemjs/${module}`);
            },
            Promise.resolve()
        )
    </script>

</template>
</body>
</html>
