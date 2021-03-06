<?php
/**
 * Modified from Original in c-box theme version: 1.0.16
 *
 * @author Brad Payne
 * @package early-years
 * @since 0.9.6
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Original:
 * @author Bowe Frankema <bowe@presscrew.com>
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 */
?>
<?php if ( is_active_sidebar( 'Footer Left' ) || is_active_sidebar( 'Footer Middle' ) || is_active_sidebar( 'Footer Right' ) ) : ?>
	<div class="footer-widgets  d-flex flex-row flex-wrap">
		<?php if ( is_active_sidebar( 'Footer Left' ) ) : ?>
			<!-- footer widgets -->
			<div class="col-md-2 col-sm-offset-1" id="footer-widget-left">
				<?php
				dynamic_sidebar( 'Footer Left' );
				?>
			</div>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'Footer Middle' ) ) : ?>
			<div class="col-md-2" id="footer-widget-middle">
				<?php
				dynamic_sidebar( 'Footer Middle' );
				?>
			</div>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'Footer Right' ) ) : ?>
			<div class="col-md-2" id="footer-widget-right">
				<?php
				dynamic_sidebar( 'Footer Right' );
				?>
			</div>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'sidebar-footer-last' ) ) : ?>
			<div class="col-md-3" id="footer-widget-last">
				<?php
				dynamic_sidebar( 'sidebar-footer-last' );
				?>
			</div>
		<?php endif; ?>
	<div class="col-md-3">
		<h4 class="funded">Funded by</h4>
		<picture>
			<source srcset="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/bc-ministry-logo.webp"
					type="image/webp">
			<source srcset="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/bc-ministry-logo.png">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/bc-ministry-logo.png" width="329"
				 height="78" alt="BC Provincial Office for the Early Years">
		</picture>

	</div>
	</div>
<?php endif; ?>
<div style="clear:both;"></div>
