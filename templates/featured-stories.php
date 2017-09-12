<?php
/**
 * Early-Years Theme: Featured Stories
 *
 * Modified from original header template in cbox theme
 * @author Brad Payne
 * @package early-years
 * @since 0.9
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 or later
 */

global $post;
$args    = array(
	'posts_per_page' => 2,
	'category_name'  => 'Homepage',
	'post_status'    => 'publish',
	'order'          => 'DESC',
	'post__in'       => get_option( 'sticky_posts' ),
);
$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post );
	?>
	<article class="row">
		<div class="eight columns">
			<h4><a href="<?php the_permalink(); ?>" rel="bookmark"
				   title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			<p><?php the_excerpt(); ?>

		</div>
		<div class="eight columns">
			<p><a href="<?php the_permalink(); ?>" rel="bookmark"
				  title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( '150' ); ?></a></p>
		</div>
	</article>

<?php endforeach; ?>
<?php wp_reset_postdata(); ?>
