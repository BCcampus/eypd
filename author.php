<?php
/**
 * Infinity Theme: author template
 *
 * @author Bowe Frankema <bowe@presscrew.com>
 * @link http://infinity.presscrew.com/
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package Infinity
 * @subpackage templates
 * @since 1.0
 */

	infinity_get_header();
?>
	<div id="content" role="main" class="<?php do_action( 'content_class' ); ?>">
		<p><b>Member profile information is not available.<b></p>
		<p>Back to <a href="<?php echo home_url(); ?>">Homepage</a></p>
	</div>
<?php
	infinity_get_sidebar();
	infinity_get_footer();
?>
